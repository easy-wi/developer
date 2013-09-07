<?php
/**
 * File: login.php.
 * Author: Ulrich Block
 * Contact: <ulrich.block@easy-wi.com>
 *
 * This file is part of Easy-WI.
 *
 * Easy-WI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Easy-WI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy-WI.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Diese Datei ist Teil von Easy-WI.
 *
 * Easy-WI ist Freie Software: Sie koennen es unter den Bedingungen
 * der GNU General Public License, wie von der Free Software Foundation,
 * Version 3 der Lizenz oder (nach Ihrer Wahl) jeder spaeteren
 * veroeffentlichten Version, weiterverbreiten und/oder modifizieren.
 *
 * Easy-WI wird in der Hoffnung, dass es nuetzlich sein wird, aber
 * OHNE JEDE GEWAEHELEISTUNG, bereitgestellt; sogar ohne die implizite
 * Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
 * Siehe die GNU General Public License fuer weitere Details.
 *
 * Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 * Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 */

$s=preg_split('/\//',$_SERVER['SCRIPT_NAME'],-1,PREG_SPLIT_NO_EMPTY);
$ewInstallPath='';
if (count($s)>1) {
    unset($s[(count($s)-1)]);
    $ewInstallPath=implode('/',$s).'/';
}
define('EASYWIDIR',dirname(__FILE__));
if (is_dir(EASYWIDIR.'/install')) die('Please remove the "install" folder');
if ((!isset($ui->get['w']) and isset($ui->post['username'])) or (isset($ui->get['w']) and $ui->get['w']!='pr')) $logininclude=true;
include(EASYWIDIR.'/stuff/vorlage.php');
include(EASYWIDIR.'/stuff/class_validator.php');
include(EASYWIDIR.'/stuff/functions.php');
include(EASYWIDIR.'/stuff/settings.php');
if ($ui->ismail('email','post')) {
	$fullday=date('Y-m-d H:i:s',strtotime("+1 day"));
    $query=$sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? LIMIT 1");
    $query->execute(array($loguserip));
	$rowcount=$query->rowcount();
    $query=($rowcount==0) ? $sql->prepare("INSERT INTO `badips` (`bantime`,`failcount`,`reason`,`badip`) VALUES (?,'1','bot',?)") : $sql->prepare("UPDATE `badips` SET `bantime`=?, `failcount`=failcount+1, `reason`='bot' WHERE `badip`=? LIMIT 1");
    $query->execute(array($fullday,$loguserip));
}
$query=$sql->prepare("SELECT language FROM settings LIMIT 1");
$query->execute();
$default_language=$query->fetchColumn();

if (is_file(EASYWIDIR."/languages/$template_to_use/$default_language/login.xml")) {
	$sprache=simplexml_load_file(EASYWIDIR."/languages/$template_to_use/$default_language/login.xml");
} else if (is_file(EASYWIDIR."/languages/default/$default_language/login.xml")) {
	$sprache=simplexml_load_file(EASYWIDIR."/languages/default/$default_language/login.xml");
} else {
	$sprache=simplexml_load_file(EASYWIDIR."/languages/$default_language/login.xml");
}
if ($w=='lo') {
	if (isset($ui->server['HTTP_REFERER'])) {
		$refstring=explode('/',substr(str_replace(array('http://'.$ui->domain('HTTP_HOST','server'),'https://'.$ui->domain('HTTP_HOST','server'),'//'),array('','','/'),strtolower($ui->server['HTTP_REFERER'])),strlen($ewInstallPath)));
		$referrer=(isset($refstring[1])) ? explode('?',$refstring[1]) : '';
	} else {
		$referrer[0]="login.php";
	}
	if (isset($_SESSION['resellerid']) and isset($_SESSION['adminid']) and isset($_SESSION['oldid']) and isset($_SESSION['oldresellerid']) and !isset($_SESSION['userid']) and $_SESSION['resellerid']!=0 and $referrer[0]=='admin.php') {
		$_SESSION['adminid']=$_SESSION['oldid'];
		$_SESSION['resellerid']=$_SESSION['oldresellerid'];
		if ($_SESSION['oldresellerid']!=0 and $_SESSION['oldid']==$_SESSION['oldresellerid']) {
			$_SESSION['oldresellerid']=0;
			$_SESSION['oldid']=$_SESSION['oldadminid'];
			unset($_SESSION['oldadminid']);
		}
		redirect('admin.php');
	} else if (isset($_SESSION['adminid']) and isset($_SESSION['userid']) and $referrer[0]=="userpanel.php") {
		unset($_SESSION['userid']);
		redirect('admin.php');
	} else {
		session_unset();
		session_destroy();
		redirect($page_url.'/'.$ewInstallPath);
	}
} else if ($w=='ba') {
	$sus=$sprache->banned;
	$include='login.tpl';
} else if ($w=='up') {
    $sus=($ui->escaped('error','get')) ? 'External Auth failed: '.htmlentities(base64_decode(urldecode($ui->escaped('error','get')))) : $sprache->bad_up;
	$include='login.tpl';
} else if ($w=='pr') {
    $token='';
	if (($ui->ismail('um','post') or $ui->username('um',50,'post')) and !$ui->w('gamestring',32,'get')) {
        # https://github.com/easy-wi/developer/issues/43
        $send=true;
        $text=$sprache->send;
		$query=$sql->prepare("SELECT `id`,`cname`,`logintime`,`lastlogin` FROM `userdata` WHERE `cname`=? OR `mail`=? ORDER BY `lastlogin` DESC LIMIT 1");
        $query->execute(array($ui->username('um',50,'post'),$ui->ismail('um','post')));
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$userid=$row['id'];
			$md5=md5($userid.$row['logintime'].$row['cname'].$row['lastlogin'].mt_rand());
            $query2=$sql->prepare("UPDATE `userdata` SET `token`=? WHERE `id`=? LIMIT 1");
            $query2->execute(array($md5,$userid));
			$folders=explode("/",$ui->server['SCRIPT_NAME']);
			$amount=count($folders)-1;
			$i=0;
			$path="";
			while ($i<$amount) {
				$path .=$folders["$i"]."/";
				$i++;
			}
            $webhostdomain=(isset($ui->server['HTTPS'])) ? "https://".$ui->server['HTTP_HOST'].$path : "http://".$ui->server['HTTP_HOST'].$path;
			$link=$webhostdomain.'login.php?w=pr&amp;gamestring='.$md5;
			$htmllink='<a href="'.$link.'">'.$link.'</a>';
			sendmail('emailpwrecovery',$userid,$htmllink,'');
		}
    } else if ($ui->password('password1',255,'post') and $ui->password('password2',255,'post') and $ui->w('token',32,'get')) {
        if ($ui->password('password1',255,'post')==$ui->password('password2',255,'post')) {
            $query=$sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `token`=? LIMIT 1");
            $query->execute(array($ui->w('token',32,'get')));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $username=$row['cname'];
                $aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
                $aeskey=$aesfilecvar['aeskey'];
                $salt=md5(mt_rand().date('Y-m-d H:i:s:u'));
                $password=createHash($username,$ui->password('password1',255,'post'),$salt,$aeskey);
                $query=$sql->prepare("UPDATE `userdata` SET `token`='',`security`=?,`salt`=? WHERE `id`=? LIMIT 1");
                $query->execute(array($password,$salt,$row['id']));
                $text=$sprache->passwordreseted;
            }
        } else if ($ui->password('password1',255,'post')!=$ui->password('password2',255,'post'))  {
            # https://github.com/easy-wi/developer/issues/43
            $token='&amp;gamestring='.$ui->w('token',32,'get');
            $text=$sprache->pwnomatch;
        }
    } else if ($ui->w('gamestring',32,'get')) {
        $token='&amp;token='.$ui->w('gamestring',32,'get');
        $recover=false;
        $randompass=passwordgenerate(10);
        $query=$sql->prepare("SELECT `id` FROM `userdata` WHERE `token`=? LIMIT 1");
        $query->execute(array($ui->w('gamestring',32,'get')));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $recover=true;
        }
    }
	if ($d=='vo') {
		if ($ui->ips('ip','post') and $ui->port('port','post') and $ui->ismail('mail','post')) {
			$checkmail=$sql->prepare("SELECT `id` FROM `userdata` WHERE `mail`=? LIMIT 1");
			$checkmail->execute(array($ui->ismail('mail','post')));
			if ($checkmail->rowcount()>0) {
				$text='Error: E-Mail exists';
			} else {
                $query=$sql->prepare("SELECT `userid` FROM `voice_server` WHERE `ip`=? AND `port`=? LIMIT 1");
                $query->execute(array($ui->ips('ip','post'),$ui->port('port','post')));
				foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $query=$sql->prepare("SELECT `security`,`cname`,`logintime`,`lastlogin` FROM `userdata` WHERE `id`=? AND `mail`='ts3@import.mail' LIMIT 1");
                    $query->execute(array($row['userid']));
					$ts3userlist=array();
					foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
						$md5=md5($row['userid'].$row['logintime'].$row['cname'].$row['lastlogin'].mt_rand());
						foreach (explode('|', $row['security']) as $user) {
							$ex=explode(':',str_replace(array("\r","\n"),"",$user));
							if (isset($ex['1'])) $ts3userlist[$ex['0']]=$ex['1'];
						}
					}
					$text=$sprache->nouser;
					$dbid=$ui->id('dbid',255,'post');
					if (isset($ts3userlist[$dbid]) and isset($md5) and $ts3userlist[$dbid]==$ui->id('dbid',255,'post')) {
						$text=$sprache->send;
                        $query=$sql->prepare("UPDATE `userdata` SET `token`=?,`mail`=? WHERE `id`=? LIMIT 1");
                        $query->execute(array($md5,$ui->ismail('mail','post'),$row['userid']));
						$folders=explode("/",$ui->server['SCRIPT_NAME']);
						$amount=count($folders)-1;
						$i=0;
						$path="";
						while ($i<$amount) {
							$path .=$folders["$i"]."/";
							$i++;
						}
                        $webhostdomain=(isset($ui->server['HTTPS'])) ? "https://".$ui->server['HTTP_HOST'].$path : "http://".$ui->server['HTTP_HOST'].$path;
						$link=$webhostdomain.'login.php?w=pr&amp;gamestring='.$md5;
						$htmllink='<a href="'.$link.'">'.$link.'</a>';
						sendmail('emailpwrecovery',$row['userid'],$htmllink,'');
					} else if (!isset($ts3userlist[$dbid])) {
						$text='Error: '.$sprache->nouser;
					}
				}
				if (!isset($htmllink)) {
					$text='Error: IP/Port';
				}
			}
		} else if (!$ui->ismail('mail','post')) {
            $text='Error: E-Mail';
        }
		if (is_file(EASYWIDIR."/languages/$template_to_use/$default_language/voice.xml")) {
			$vosprache=simplexml_load_file(EASYWIDIR."/languages/$template_to_use/$default_language/voice.xml");
		} else if (is_file(EASYWIDIR."/languages/default/$default_language/voice.xml")) {
			$vosprache=simplexml_load_file(EASYWIDIR."/languages/default/$default_language/voice.xml");
		} else {
			$vosprache=simplexml_load_file(EASYWIDIR."/languages/$default_language/voice.xml");
		}
		$include='passwordrecovery_vo.tpl';
	} else {
		$include='passwordrecovery.tpl';
	}
} else {
    include(EASYWIDIR.'/stuff/keyphrasefile.php');
	if (!$ui->username('username',255,'post') and !$ui->ismail('username',255,'post') and !$ui->password('password',255,'post') and !isset($_SESSION['sessionid'])) {
		$include='login.tpl';
	} else if (($ui->username('username',255,'post') or $ui->ismail('username','post')) and $ui->password('password',255,'post') and !isset($_SESSION['sessionid'])) {
		$password=$ui->password('password',255,'post');
		if ($ewCfg['captcha']==1) {
			if (md5($ui->w('captcha',4,'post'))!=$_SESSION['captcha']) {
				$halfhour=date('Y-m-d H:i:s',strtotime("+30 minutes"));
				$query=$sql->prepare("SELECT id FROM badips WHERE badip=? LIMIT 1");
                $query->execute(array($loguserip));
				$rowcount=$query->rowcount();
                $query=($rowcount==0) ? $sql->prepare("INSERT INTO `badips` (`bantime`,`failcount`,`reason`,`badip`) VALUES (?,'1','password',?)") : $sql->prepare("UPDATE `badips` SET `bantime`=?, `failcount`=`failcount`+1, `reason`='password' WHERE `badip`=? LIMIT 1");
                $query->execute(array($halfhour,$loguserip));
				redirect('login.php?w=ca&r=lo');
			}
		}
        $salt='';
        $query=$sql->prepare("SELECT `id`,`cname`,`active`,`security`,`resellerid`,`mail`,`salt`,`externalID` FROM `userdata` WHERE `cname`=? OR `mail`=? ORDER BY `lastlogin` DESC LIMIT 1");
        $query->execute(array($ui->username('username',255,'post'),$ui->ismail('username','post')));
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $username=$row['cname'];
			$id=$row['id'];
			$active=$row['active'];
            $mail=$row['mail'];
            $salt=$row['salt'];
            $externalID=$row['externalID'];
			$security=$row['security'];
			$resellerid=$row['resellerid'];
            $userpassNew=createHash($username,$password,$salt,$aeskey);
            if (isset($security) and $security!=$userpassNew) {
                $userpassOld=passwordhash($username,$password);
                if (isset($id) and $userpassOld==$security) {
                    $salt=md5(mt_rand().date('Y-m-d H:i:s:u'));
                    $query=$sql->prepare("UPDATE `userdata` SET `security`=?,`salt`=? WHERE `id`=? LIMIT 1");
                    $query->execute(array(createHash($username,$password,$salt,$aeskey),$salt,$id));
                    $userpass=$userpassOld;
                } else {
                    $userpass=$userpassNew;
                }
            } else {
                $userpass=$userpassNew;
            }
        }
        # https://github.com/easy-wi/developer/issues/2
        if (!isset($active)) {
            $query=$sql->prepare("SELECT * FROM `userdata_substitutes` WHERE `loginName`=? LIMIT 1");
            $query->execute(array($ui->username('username',255,'post')));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $sID=$row['sID'];
                $id=$row['userID'];
                $username=$row['loginName'];
                $active=$row['active'];
                $mail='';
                $salt=$row['salt'];
                $externalID=0;
                $security=$row['passwordHashed'];
                $resellerid=$row['resellerID'];
                $userpass=createHash($username,$password,$salt,$aeskey);
            }
        }
        if (isset($active) and $active=='Y' and $security!=$userpass) {
            $authLookupID=($resellerid==$id) ? 0 : $resellerid;
            $query=$sql->prepare("SELECT `active`,`ssl`,`user`,`domain`,AES_DECRYPT(`pwd`,?) AS `decryptedPWD`,`file` FROM `api_external_auth` WHERE `resellerID`=? LIMIT 1");
            $query->execute(array($aeskey,$authLookupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $activeAuth=$row['active'];
                $portAuth=($row['ssl']=='Y') ? 433 : 80;
                $userAuth=urlencode($row['user']);
                $pwdAuth=urlencode($row['decryptedPWD']);
                $domainAuth=$row['domain'];
                $fileAuth=$row['file'];
                $XML=<<<XML
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE user>
<user>
	<username>$username</username>
	<pwd>$password</pwd>
	<mail>$mail</mail>
	<externalID>$externalID</externalID>
</user>
XML;
                $postXML=urlencode(base64_encode($XML));
            }
            if (isset($activeAuth) and $activeAuth=='Y') {
                $reply=webhostRequest($domainAuth,$ui->escaped('HTTP_HOST','server'),$fileAuth,array('authPWD'=>$pwdAuth,'userAuth'=>$userAuth,'postXML'=>$postXML),$portAuth);
                $xmlReply= @simplexml_load_string($reply);
                if ($xmlReply and isset($xmlReply->success) and $xmlReply->success==1 and $xmlReply->user==$username) {
                    $externalOK=1;
                    $salt=md5(mt_rand().date('Y-m-d H:i:s:u'));
                    $query=$sql->prepare("UPDATE `userdata` SET `security`=?,`salt`=? WHERE `id`=? LIMIT 1");
                    $query->execute(array(createHash($username,$password,$salt,$aeskey),$salt,$id));
                } else if ($xmlReply and isset($xmlReply->error)) {
                    $externalAuthError=$xmlReply->error;
                } else if ($reply!=null and $reply!=false) {
                    $externalAuthError=$reply;
                }
            }
        }
		if (isset($active) and $active=='Y' and ($security==$userpass or (isset($externalOK) and $externalOK==1))) {
			session_unset();
			session_destroy();
			session_start();

            # https://github.com/easy-wi/developer/issues/2
            if(isset($sID)) {
                $query=$sql->prepare("SELECT `logintime`,`language` FROM `userdata_substitutes` WHERE `sID`=? LIMIT 1");
                $query->execute(array($sID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $logintime=$row['logintime'];
                    $_SESSION['language']=$row['language'];
                }
                $query=$sql->prepare("UPDATE `userdata_substitutes` SET `lastlogin`=?,`logintime`=? WHERE `sID`=? LIMIT 1");
                $query->execute(array($logintime,$logdate,$sID));
            } else {
                $query=$sql->prepare("SELECT `logintime`,`language` FROM `userdata` WHERE `id`=? LIMIT 1");
                $query->execute(array($id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $logintime=$row['logintime'];
                    $_SESSION['language']=$row['language'];
                }
                $query=$sql->prepare("UPDATE `userdata` SET `lastlogin`=?,`logintime`=? WHERE `id`=? LIMIT 1");
                $query->execute(array($logintime,$logdate,$id));
            }
            $_SESSION['resellerid']=$resellerid;
            $query=$sql->prepare("DELETE FROM `badips` WHERE `badip`=?");
            $query->execute(array($loguserip));
			if(isanyadmin($id) or rsellerpermisions($id)) {
				$_SESSION['adminid']=$id;
				if(isset($_SESSION['adminid']) and is_numeric($_SESSION['adminid'])) $admin_id=$_SESSION['adminid'];
			} else if (isanyuser($id)) {
				$_SESSION['userid']=$id;
				if(isset($_SESSION['userid']) and is_numeric($_SESSION['userid'])) $user_id=$_SESSION['userid'];
                if(isset($sID)) $_SESSION['sID']=$sID;
			}
            $ref='';
			if ($ui->url('HTTP_REFERER','server')) {
				$ref=$ui->url('HTTP_REFERER','server');
			} else if ($ui->domain('HTTP_REFERER','server')) {
				$ref=$ui->domain('HTTP_REFERER','server');
			}
			$referrer=explode('/', str_replace(array('http://', 'https://'),'',strtolower($ref)));
			if (isset($referrer['1']) and $referrer['1']=='login.php') $topanel=true;
			if (!isset($user_id) and !isset($admin_id)) {
				header('Location: login.php&r=lo');
			} else if(isset($user_id)) {
				redirect('userpanel.php');
			} else if(isset($admin_id)) {
				$folders=explode("/",$ui->server['SCRIPT_NAME']);
				$amount=count($folders)-1;
				$i=0;
				$path="";
				while ($i<$amount) {
					$path .=$folders["$i"]."/";
					$i++;
				}
                $webhostdomain=(isset($ui->server['HTTPS'])) ? "https://".$ui->server['HTTP_HOST'].$path : "http://".$ui->server['HTTP_HOST'].$path;
                $query=$sql->prepare("UPDATE `settings` SET `paneldomain`=? WHERE `resellerid`=0 LIMIT 1");
                $query->execute(array($webhostdomain));
                $params=@json_decode(licenceRequest(true));
				if(isanyadmin($admin_id) or rsellerpermisions($admin_id)) {
					redirect('admin.php');
				} else {
                    redirect('login.php&r=lo');
				}
			}
		} else if (!isset($security) or $security!=$userpass) {
			$halfhour=date('Y-m-d H:i:s',strtotime("+30 minutes"));
            $query=$sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? LIMIT 1");
            $query->execute(array($loguserip));
			$rowcount=$query->rowCount();
            $query=($rowcount==0) ? $sql->prepare("INSERT INTO `badips` (bantime,failcount,reason,badip) VALUES (?,'1','password',?)") : $sql->prepare("UPDATE `badips` SET `bantime`=?,`failcount`=`failcount`+1, `reason`='password' WHERE `badip`=? LIMIT 1");
            $query->execute(array($halfhour,$loguserip));
            if (isset($externalAuthError)) redirect('login.php?w=up&error='.urlencode(base64_encode($externalAuthError)).'&r=lo');
            else redirect('login.php?w=up&r=lo');
		} else if (isset($active) and $active=='N') {
            redirect('login.php?w=su&r=lo');
		} else {
            redirect('login.php?w=up&r=lo');
		}
    } else if ($ui->escaped('username','post') and $ui->escaped('password','post')) {
        redirect('login.php?w=up&r=lo');
	} else {
        redirect('login.php?w=lo');
	}
}
if (isset($include)) {
	if (is_file(EASYWIDIR."/template/$template_to_use/$include")) {
		include(EASYWIDIR."/template/$template_to_use/$include");
	} else if (is_file(EASYWIDIR."/template/default/$include")) {
		include(EASYWIDIR."/template/default/$include");
	} else {
		include(EASYWIDIR."/template/$include");
	}
}
$sql=null;