<?php
/**
 * File: user.php.
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
if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['user'] and !$pa['user_users'])) {
	header('Location: admin.php');
    die();
}
include(EASYWIDIR . '/stuff/keyphrasefile.php');
$sprache = getlanguagefile('user',$user_language,$reseller_id);
$rsprache = getlanguagefile('reseller',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id==0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller = 0;
}
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d', 'get') == 'ad') {
	if ($ui->smallletters('action',2, 'post') == 'ad') {
		$error = array();
		if (!$ui->ismail('mail', 'post')){
            $error[] = $sprache->error_mail;
		} else {
            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE `mail`=? LIMIT 1");
            $query->execute(array($ui->ismail('mail', 'post')));
            if ($query->fetchColumn()>0) $error[] = $sprache->error_mail_exists;
        }
		if (!$ui->password('security',20, 'post'))$error[] = $sprache->error_pass;
		if (!$ui->smallletters('accounttype',1, 'post')){
            $error[] = '';
		} else {
			$accounttype = $ui->smallletters('accounttype',1, 'post');
			$query = $sql->prepare("SELECT `accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($admin_id));
            $user_accounttype = $query->fetchColumn();
            $fdlpath = $ui->url('fdlpath', 'post');
		}
		if (count($error)>0) {
            $template_file = 'Error: '.implode('<br/>',$error);
        } else {
			$query = $sql->prepare("SELECT `prefix1`,`prefix2` FROM `settings` WHERE `resellerid`=? LIMIT 1");
			$query->execute(array($reseller_id));
			foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$prefix1 = $row['prefix1'];
				$prefix2 = $row['prefix2'];
			}
			if ($prefix1== 'Y' and $accounttype!='a') {
				$cname = $prefix2;
				$bogus = $cname.$ui->ismail('mail', 'post');
			} else {
                if ($accounttype == 'a' and $ui->username('acname',255, 'post')) {
                    $cname = $ui->username('acname',255, 'post');
                } else if ($accounttype == 'r' and $ui->username('rcname',255, 'post')) {
                    $cname = $ui->username('rcname',255, 'post');
                } else if ($accounttype == 'u' and $ui->username('cname',255, 'post')) {
                    $cname = $ui->username('cname',255, 'post');
                } else {
                    $error[] = 'Entered Username not valid!';
                }
                if ($cname!='' and $cname != null and $cname != false) {
                    $bogus = $cname;
                    $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? LIMIT 1");
                    $query->execute(array($cname));
                    if ($query->rowCount()>0) unset($cname,$bogus);
                    else {
                        # https://github.com/easy-wi/developer/issues/2 "Substitutes"
                        $query = $sql->prepare("SELECT 1 FROM `userdata_substitutes` WHERE `loginName`=? LIMIT 1");
                        $query->execute(array($cname));
                        if ($query->rowCount()>0) unset($cname,$bogus);
                    }
                } else {
                    $error[] = 'Username transmitted empty!';
                }
			}
			if (isset($cname) and isset($bogus)) {
				$active = $ui->active('active', 'post');
				$security="bogus";
				$name = $ui->names('name',255, 'post');
				$vname = $ui->names('vname',255, 'post');
				$mail = $ui->ismail('mail', 'post');
				$phone = $ui->phone('phone',50, 'post');
				$handy = $ui->phone('handy',50, 'post');
				$city = $ui->names('city',50, 'post');
				$cityn = $ui->id('cityn',6, 'post');
				$street = $ui->names('street',50, 'post');
				$streetn = $ui->streetNumber('streetn', 'post');
				$password = $ui->password('security',255, 'post');
                $salutation = $ui->id('salutation',1, 'post');
                $birthday=date('Y-m-d',strtotime($ui->isDate('birthday', 'post')));
                $country = $ui->st('country', 'post');
                $fax = $ui->phone('fax',50, 'post');
                $mail_backup=yesNo('mail_backup');
                $mail_gsupdate=yesNo('mail_gsupdate');
                $mail_securitybreach=yesNo('mail_securitybreach');
                $mail_serverdown=yesNo('mail_serverdown');
                $mail_ticket=yesNo('mail_ticket');
                $mail_vserver=yesNo('mail_vserver');
                if ($accounttype == 'r') {
                    $usergroup = $ui->id('groups_r',19, 'post');
                    $mail_backup=yesNo('mail_backup');
                    $mail_gsupdate=yesNo('rmail_gsupdate');
                    $mail_securitybreach=yesNo('rmail_securitybreach');
                    $mail_vserver=yesNo('rmail_vserver');
                    $useractive=yesNo('useractive');
				} else if ($accounttype == 'a') {
                    $usergroup = $ui->id('groups_a',19, 'post');
                } else {
                    $usergroup = $ui->id('groups_u',19, 'post');
                }
                $query = $sql->prepare("INSERT INTO `userdata` (`creationTime`,`updateTime`,`active`,`salutation`,`birthday`,`country`,`fax`,`cname`,`security`,`name`,`vname`,`mail`,`phone`,`handy`,`city`,`cityn`,`street`,`streetn`,`fdlpath`,`accounttype`,`mail_backup`,`mail_gsupdate`,`mail_securitybreach`,`mail_serverdown`,`mail_ticket`,`mail_vserver`) VALUES (NOW(),NOW(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $query->execute(array($active,$salutation,$birthday,$country,$fax,$bogus,$security,$name,$vname,$mail,$phone,$handy,$city,$cityn,$street,$streetn,$fdlpath,$accounttype,$mail_backup,$mail_gsupdate,$mail_securitybreach,$mail_serverdown,$mail_ticket,$mail_vserver));
                $id = $sql->lastInsertId();
                $query=($accounttype == 'r' and $reseller_id==0) ? $sql->prepare("SELECT `id` FROM `usergroups` WHERE `id`=? AND `grouptype`=? AND `resellerid`=0 LIMIT 1") : $sql->prepare("SELECT `id` FROM `usergroups` WHERE `id`=? AND `grouptype`=? AND `resellerid`=? LIMIT 1");
                $query2 = $sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`,`resellerID`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `groupID`=VALUES(`groupID`)");
                foreach ($usergroup as $gid) {
                    if ($accounttype == 'r' and $reseller_id==0) $query->execute(array($gid,$accounttype));
                    else $query->execute(array($gid,$accounttype,$reseller_id));
                    if(isid($query->fetchColumn(),10)) $query2->execute(array($id,$gid,$reseller_id));
                }
                customColumns('U',$id,'save');
                $cnamenew = $ui->username('cname',255, 'post');
				if($prefix1== 'Y' and $accounttype!='a') {
					$cnamenew = $cname.$id;
				} else if ($accounttype!='a') {
					$cnamenew = $cname;
				} else if ($accounttype == 'a' and $ui->username('acname',255, 'post')) {
                    $cnamenew = $ui->username('acname',255, 'post');
                } else {
                    die('Fatal Error 2: Username transmitted empty!');
                }
				if ($accounttype == 'a') {
					$resellerid = $reseller_id;
					$ips = '';
				} else if ($accounttype == 'u') {
					$resellerid = $reseller_id;
					$ips = '';
				} else if ($accounttype == 'r') {
					$resellerid = $id;
                    if (!$ui->id('maxuser',10, 'post')) {
                        $maxuser = 0;
					} else {
                        $maxuser = $ui->id('maxuser',10, 'post');
					}
					if (!$ui->id('maxgserver',10, 'post')) {
                        $maxgserver = 0;
					} else {
                        $maxgserver = $ui->id('maxgserver',10, 'post');
                    }
                    if (!$ui->id('maxvoiceserver',10, 'post')) {
                        $maxvoserver = 0;
					} else {
                        $maxvoserver = $ui->id('maxvoiceserver',10, 'post');
					}
                    if($vserver_module or $dediserver_module) {
                        if (!$ui->id('maxgserver',10, 'post')) {
                            $maxvserver = 0;
                        } else {
                            $maxvserver = $ui->id('maxgserver',10, 'post');
                        }
                        if (!$ui->id('maxdedis',10, 'post')) {
                            $maxdedis = 0;
                        } else {
                            $maxdedis = $ui->id('maxdedis',10, 'post');
                        }
						$post_ips=array_unique((array)$ui->ips('ips', 'post'));
						$maxuserram = $ui->id('maxuserram',255, 'post');
						$maxusermhz = $ui->id('maxusermhz',255, 'post');
						if ($reseller_id==0 or $reseller_id==$admin_id) {
							$availableips=freeips($reseller_id);
						} else {
							$availableips=freeips($admin_id);
						}
						foreach ($post_ips as $ip) {
							if (in_array($ip, $availableips) and isset($ips)) {
								$ips .="\r\n".$ip;
							} else if (in_array($ip, $availableips)) {
								$ips = $ip;
							}
						}
						if (!isset($ips)) {
							$ips = '';
						}
					} else {
						$ips = '';
						$maxvdedis = 0;
                        $maxvserver = 0;
						$maxuserram = 0;
						$maxusermhz = 0;
					}
                    function CopyAdminTable ($tablename,$id,$reseller_id,$limit,$sql,$where='') {
                        $query = $sql->prepare("SELECT * FROM `$tablename` WHERE `resellerid`=? $where $limit");
                        $query->execute(array($reseller_id));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            $keys = array();
                            $questionmarks = array();
                            $intos = array();
                            foreach ($row as $key=>$value) {
                                if ($key != 'id' and $key != 'resellerid'){
                                    $keys[]="`".$key."`";
                                    $questionmarks[] = '?';
                                    $intos[] = $value;
                                }
                            }
                            $keys[]="`resellerid`";
                            $intos[] = $id;
                            $questionmarks[] = '?';
                            $into='INSERT INTO `'.$tablename.'` ('.implode(',',$keys).') VALUES ('.implode(',',$questionmarks).')';
                            $query = $sql->prepare("$into");
                            $query->execute($intos);
                        }
                    }
                    $query = $sql->prepare("SELECT * FROM `addons` WHERE `resellerid`=?");
                    $query2 = $sql->prepare("INSERT INTO `addons` (`active`,`shorten`,`addon`,`type`,`folder`,`menudescription`,`configs`,`cmd`,`paddon`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?)");
                    $query3 = $sql->prepare("SELECT `lang`,`text` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `resellerID`=? LIMIT 1");
                    $query4 = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`text`,`transID`,`resellerID`) VALUES ('ad',?,?,?,?)");
                    $query->execute(array($reseller_id));
					foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $query2->execute(array($row['active'], $row['shorten'], $row['addon'], $row['type'], $row['folder'], $row['menudescription'], $row['configs'], $row['cmd'], $row['paddon'],$id));
                        $newID = $sql->lastInsertId();
                        $query3->execute(array($row['id'],$reseller_id));
						foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) $query4->execute(array($row3['lang'], $row3['text'],$newID,$id));
					}
                    CopyAdminTable('servertypes',$id,$reseller_id,'',$sql);
                    CopyAdminTable('settings',$id,$reseller_id,'LIMIT 1',$sql);
                    CopyAdminTable('voice_stats_settings',$id,$reseller_id,'LIMIT 1',$sql);
                    CopyAdminTable('usergroups',$id,$reseller_id,'',$sql,"AND `active`='Y' AND `name` IS NOT NULL AND `grouptype`='u'");
                    $query = $sql->prepare("SELECT * FROM `lendsettings` WHERE `resellerid`=? LIMIT 1");
                    $query2 = $sql->prepare("INSERT INTO `lendsettings` (`active`,`mintime`,`maxtime`,`timesteps`,`minplayer`,`maxplayer`,`playersteps`,`vomintime`,`vomaxtime`,`votimesteps`,`vominplayer`,`vomaxplayer`,`voplayersteps`,`shutdownempty`,`shutdownemptytime`,`ftpupload`,`ftpuploadpath`,`lendaccess`,`lastcheck`,`oldcheck`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'0xe4bca9cd69b8488c9c5ee5b7d32c12f3a3cdae349a54edbe6659fc2817ccc86489b12864ebbb43eff607be85611da6c4','3',?,?,?)");
                    $query->execute(array($reseller_id));
					foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $query2->execute(array($row['active'], $row['mintime'], $row['maxtime'], $row['timesteps'], $row['minplayer'], $row['maxplayer'], $row['playersteps'], $row['vomintime'], $row['vomaxtime'], $row['votimesteps'], $row['vominplayer'], $row['vomaxplayer'], $row['voplayersteps'], $row['shutdownempty'], $row['shutdownemptytime'], $row['ftpupload'], $row['lastcheck'], $row['oldcheck'],$id));
                    $query = $sql->prepare("SELECT * FROM `translations` WHERE `type`='em' AND `resellerID`=?");
                    $query2 = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('em',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
                    $query->execute(array($reseller_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $query2->execute(array($row['transID'], $row['lang'], $row['text'],$id));
                    $resellersid=($reseller_id==0) ? $resellerid : $reseller_id;
                    $query = $sql->prepare("INSERT INTO `resellerdata` (`useractive`,`ips`,`maxuser`,`maxgserver`,`maxvoserver`,`maxdedis`,`maxvserver`,`maxuserram`,`maxusermhz`,`resellerid`,`resellersid`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                    $query->execute(array($useractive,$ips,$maxuser,$maxgserver,$maxvoserver,$maxdedis,$maxvserver,$maxuserram,$maxusermhz,$resellerid,$resellersid));
                    $query = $sql->prepare("INSERT INTO `eac` (resellerid) VALUES (?)");
                    $query->execute(array($resellerid));
				}
				if (!isset($resellersid)) $resellersid = $reseller_id;
                $salt=md5(mt_rand().date('Y-m-d H:i:s:u'));
                $security2=createHash($cnamenew,$password,$salt,$aeskey);
                $query = $sql->prepare("UPDATE `userdata` SET `cname`=?,`security`=?,`salt`=?,`resellerid`=? WHERE `id`=? LIMIT 1");
				if ($user_accounttype=="a") {
                    $query->execute(array($cnamenew,$security2,$salt,$resellerid,$id));
				} else if ($user_accounttype=="r" and $admin_id==$reseller_id) {
                    $query->execute(array($cnamenew,$security2,$salt,$reseller_id,$id));
				} else if ($user_accounttype=="r" and $admin_id != $reseller_id) {
                    $query->execute(array($cnamenew,$security2,$salt,$admin_id,$id));
				}
				sendmail('emailuseradd',$id,$cnamenew,$password);
				$template_file = $sprache->user_create .": <b>$cnamenew</b>.";
				$loguseraction="%add% %user% $cnamenew";				
				$insertlog->execute();
			} else {
				$template_file = $sprache->error_cname;
			}
		}
	} else {
        $randompass=passwordgenerate(10);
        $randompass2=passwordgenerate(10);
        $query = $sql->prepare("SELECT `prefix1` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        $prefix1 = $query->fetchColumn();
        $groups = array();
        $lookUpID=($reseller_id != 0) ? $reseller_id: 0;
        $groups=array('a' => array(),'r' => array(),'u' => array());
        $defaultGroups = array();
        $query = $sql->prepare("SELECT `id`,`grouptype`,`name`,`defaultgroup` FROM `usergroups` WHERE `active`='Y' AND `resellerid`=?");
        $query->execute(array($lookUpID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ($row['defaultgroup'] == 'Y') $defaultGroups[$row['grouptype']][$row['id']] = $row['name'];
            $groups[$row['grouptype']][$row['id']] = $row['name'];
        }
        if ($reseller_id==0 or $reseller_id==$admin_id) {
            $availableips=freeips($reseller_id);
        } else {
            $availableips=freeips($admin_id);
        }
        $selectlanguages=getlanguages($template_to_use);
        $template_file = 'admin_user_add.tpl';
    }
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get') != $admin_id and ($pa['user'] or $pa['user_users'])) {
    $id = $ui->id('id', 10, 'get');
    if (!$ui->smallletters('action',2, 'post')) {
        if($reseller_id==0) {
            $query = $sql->prepare("SELECT `cname`,`name`,`accounttype` FROM `userdata` WHERE `id`=? AND (`resellerid`=? OR `id`=`resellerid`) LIMIT 1");
        } else {
            $query = $sql->prepare("SELECT `cname`,`name`,`accounttype` FROM `userdata` WHERE `id`=? AND `resellerid`=? AND `resellerid`!=`id` LIMIT 1");
        }
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (($row['accounttype'] == 'a' and $pa['user']) or  ($row['accounttype'] != 'a') and ($pa['user'] or $pa['user_users'])) {
                $cname = $row['cname'];
                $name = $row['name'];
            }
        }
        if (isset($cname)) {
            $template_file = 'admin_user_dl.tpl';
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else if ($ui->smallletters('action',2, 'post') == 'dl') {
        if ($reseller_id != 0 and $admin_id != $reseller_id) $reseller_id = $admin_id;
        $template_file = '';
        if($reseller_id==0) {
            $query = $sql->prepare("SELECT `cname`,`resellerid`,`accounttype` FROM `userdata` WHERE `id`=? AND (`resellerid`=? OR `id`=resellerid) LIMIT 1");
        } else {
            $query = $sql->prepare("SELECT `cname`,`resellerid`,`accounttype` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        }
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (($row['accounttype'] == 'a' and $pa['user']) or  ($row['accounttype'] != 'a') and ($pa['user'] or $pa['user_users'])) {
                $deleted = true;
                $cname = $row['cname'];
                $resellerid = $row['resellerid'];
                $update = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
                $update->execute(array($id,$resellerid));
                $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('U','us',?,?,?,?,NULL,NOW(),'dl',?)");
                $insert->execute(array($admin_id,$id,$id, $row['cname'],$resellerid));
                updateJobs($id,$reseller_id);
            }
        }
        if($query->rowCount()>0 and isset($deleted)) {
            $update = $sql->prepare("UPDATE `userdata` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=?");
            $update->execute(array($id,$resellerid));
            $template_file .= $spracheResponse->table_del ."<br />";
            $loguseraction="%del% %user% $cname";
            $insertlog->execute();
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d', 'get') == 'md' and $ui->id('id', 10, 'get')) {
    $id = $ui->id('id', 10, 'get');
    $resellerid=($reseller_id != 0 and $admin_id != $reseller_id) ? $admin_id : $reseller_id;
    if (!$ui->smallletters('action',2, 'post')) {
        $query=($reseller_id==0) ? $sql->prepare("SELECT * FROM `userdata` WHERE id=? AND (`resellerid`=? OR `id`=resellerid) LIMIT 1") : $sql->prepare("SELECT * FROM `userdata` WHERE id=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$resellerid));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active = 'N';
            if ($row['jobPending'] == 'Y') {
                $query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='us' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
                $query2->execute(array($row['id'], $row['resellerid']));
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    if ($row2['action'] == 'ad') $jobPending = $gsprache->add;
                    else if ($row2['action'] == 'dl') $jobPending = $gsprache->del;
                    else $jobPending = $gsprache->mod;
                    $json=@json_decode($row2['extraData']);
                    $active=(is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
                }
            } else {
                $jobPending = $gsprache->no;
                $active = $row['active'];
            }
            $cname = $row['cname'];
            $name = $row['name'];
            $vname = $row['vname'];
            $mail = $row['mail'];
            $phone = $row['phone'];
            $handy = $row['handy'];
            $city = $row['city'];
            $cityn = $row['cityn'];
            $street = $row['street'];
            $streetn = $row['streetn'];
            $fdlpath = $row['fdlpath'];
            $accounttype = $row['accounttype'];
            $salutation = $row['salutation'];
            $birthday = $row['birthday'];
            $country = $row['country'];
            $fax = $row['fax'];
            $mail_backup = $row['mail_backup'];
            $mail_gsupdate = $row['mail_gsupdate'];
            $mail_securitybreach = $row['mail_securitybreach'];
            $mail_serverdown = $row['mail_serverdown'];
            $mail_ticket = $row['mail_ticket'];
            $mail_vserver = $row['mail_vserver'];
            $creationTime = $row['creationTime'];
            $updateTime = $row['updateTime'];
            if ($user_language == 'de') {
                $creationTime=date('d-m-Y H:i:s',strtotime($row['creationTime']));
                $updateTime=date('d-m-Y H:i:s',strtotime($row['updateTime']));
            }
        }
        if (isset($accounttype) and (($accounttype == 'a' and $pa['user']) or ($accounttype!='a') and ($pa['user'] or $pa['user_users']))){
            $groups = array();
            $groupsAssigned = array();
            $lookUpID=($reseller_id != 0) ? $reseller_id : 0;
            $query = $sql->prepare("SELECT `id`,`name` FROM `usergroups` WHERE `active`='Y' AND `grouptype`=? AND `resellerid`=?");
            $query->execute(array($accounttype,$lookUpID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $groups[$row['id']] = $row['name'];
            }
            if ($accounttype == 'r' and $reseller_id==0) $lookUpID = $id;
            $query = $sql->prepare("SELECT `groupID` FROM `userdata_groups` WHERE `userID`=? AND `resellerID`=?");
            $query->execute(array($id,$lookUpID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $groupsAssigned[] = $row['groupID'];
            }
            if ($accounttype == 'r') {
                $ips=($reseller_id==0 or $reseller_id==$admin_id) ? freeips($reseller_id) : freeips($admin_id);
                $ipsAssigned = array();
                $query = $sql->prepare("SELECT * FROM `resellerdata` WHERE `resellerid`=?");
                $query->execute(array($id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $useractive = $row['useractive'];
                    foreach(ipstoarray($row['ips']) as $ip) {
                        $ipsAssigned[] = $ip;
                        $ips[] = $ip;
                    }
                    $maxuser = $row['maxuser'];
                    $maxgserver = $row['maxgserver'];
                    $maxvoiceserver = $row['maxvoserver'];
                    $maxdedis = $row['maxdedis'];
                    $maxvserver = $row['maxvserver'];
                    $maxuserram = $row['maxuserram'];
                    $maxusermhz = $row['maxusermhz'];
                }
                $ipsAssigned=array_unique($ipsAssigned);
                $ips=array_unique($ips);
                natsort($ipsAssigned);
                natsort($ips);
            }
            $selectlanguages=getlanguages($template_to_use);
            $template_file = 'admin_user_md.tpl';
        } else $template_file = 'admin_404.tpl';
    } else if ($ui->smallletters('action',2, 'post') == 'md') {
        $errors = array();
        if (!$ui->ismail('mail', 'post')){
            $errors[] = $sprache->error_mail;
        } else {
            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE `mail`=? AND `id`!=? LIMIT 1");
            $query->execute(array($ui->ismail('mail', 'post'),$id));
            if ($query->fetchColumn()>0) $error[] = $sprache->error_mail;
        }
        if (!$ui->id('groups',30, 'post') and $id != $admin_id){
            $errors[] = 'Error: Group';
        }
        if (count($errors)>0) {
            $template_file = implode('<br />',$errors);
        } else {
            $jobPending = '';
            if ($reseller_id==0){
                $query = $sql->prepare("SELECT `accounttype`,`active`,`cname`,`resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
                $query->execute(array($id));
            } else {
                $query = $sql->prepare("SELECT `accounttype`,`active`,`cname`,`resellerid` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id,$reseller_id));
            }
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $accounttype = $row['accounttype'];
                $oldactive = $row['active'];
                $cname = $row['cname'];
                $resellerlockupid = $row['resellerid'];
            }
            if (isset($oldactive)) {
                $fdlpath = $ui->url('fdlpath', 'post');
                $active=(in_array($ui->escaped('active', 'post'), array('N','Y','R'))) ? $ui->escaped('active', 'post') : 'N';
                $mail_backup=yesNo('mail_backup');
                $mail_gsupdate=yesNo('mail_gsupdate');
                $mail_securitybreach=yesNo('mail_securitybreach');
                $mail_serverdown=yesNo('mail_serverdown');
                $mail_ticket=yesNo('mail_ticket');
                $mail_vserver=yesNo('mail_vserver');
                $template_file = '';
                $name = $ui->names('name',255, 'post');
                $vname = $ui->names('vname',255, 'post');
                $mail = $ui->ismail('mail', 'post');
                $phone = $ui->phone('phone',50, 'post');
                $handy = $ui->phone('handy',50, 'post');
                $city = $ui->names('city',50, 'post');
                $cityn = $ui->id('cityn',6, 'post');
                $street = $ui->names('street',50, 'post');
                $streetn = $ui->streetNumber('streetn', 'post');
                $salutation = $ui->id('salutation',1, 'post');
                $birthday=date('Y-m-d',strtotime($ui->isDate('birthday', 'post')));
                $country = $ui->st('country', 'post');
                $fax = $ui->phone('fax',50, 'post');
                $useractive=($ui->active('useractive', 'post')) ? $ui->active('useractive', 'post') : 'N';
                if ($ui->ips('ips', 'post') or $ui->id('maxuser',10, 'post') and $accounttype='r') {
                    if ($reseller_id==0) {
                        $availableips=freeips($reseller_id);
                    } else if ($resellerlockupid==0 or $resellerlockupid==$admin_id) {
                        $availableips=freeips($resellerlockupid);
                    } else {
                        $availableips=freeips($admin_id);
                    }
                    if ($resellerlockupid==0) {
                        $resellerlockupid = $id;
                    }
                    $post_ips2=array_unique((array)$ui->ips('ips', 'post'));
                    $query = $sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellerid`=? LIMIT 1");
                    $query->execute(array($id));
                    $oldips=ipstoarray($query->fetchColumn());
                    $ips = array();
                    foreach ($post_ips2 as $ip) {
                        if (in_array($ip,$availableips) or in_array($ip,$oldips)) $ips[] = $ip;
                    }
                    $ips=implode("\r\n",$ips);
                    $maxuser = $ui->id('maxuser',10, 'post');
                    $maxgserver = $ui->id('maxgserver',10, 'post');
                    $maxvoserver = $ui->id('maxvoiceserver',10, 'post');
                    $maxdedis = $ui->id('maxdedis',10, 'post');
                    $maxvserver = $ui->id('maxgserver',10, 'post');
                    $maxuserram = $ui->id('maxuserram',255, 'post');
                    $maxusermhz = $ui->id('maxusermhz',255, 'post');
                    $query = $sql->prepare("SELECT `useractive` FROM `resellerdata` WHERE `resellerid`=? LIMIT 1");
                    $query->execute(array($id));
                    if($query->fetchColumn() != $useractive) {
                        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `resellerid`=?");
                        $query->execute(array($id));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row){
                            $update = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
                            $update->execute(array($id,$reseller_id));
                            $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('U','us',?,?,?,?,NULL,NOW(),'md',?,?)");
                            $insert->execute(array($admin_id, $row['id'], $row['id'], $row['cname'],json_encode(array('newActive' => $useractive)),$id));
                            updateJobs($row['id'],$reseller_id);
                        }
                    }
                    $query = $sql->prepare("UPDATE `resellerdata` SET `useractive`=?,`ips`=?,`maxuser`=?,`maxgserver`=?,`maxvoserver`=?,`maxdedis`=?,`maxvserver`=?,`maxuserram`=?,`maxusermhz`=? WHERE `resellerid`=? LIMIT 1");
                    $query->execute(array($useractive,$ips,$maxuser,$maxgserver,$maxvoserver,$maxdedis,$maxvserver,$maxuserram,$maxusermhz,$id));
                }
                if ($oldactive != $active) {
                    $jobPending=",`jobPending`='Y'";
                    $update = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
                    $update->execute(array($id,$reseller_id));
                    $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('U','us',?,?,?,?,NULL,NOW(),'md',?,?)");
                    $insert->execute(array($admin_id,$id,$id,$cname,json_encode(array('newActive' => $active)),$reseller_id));
                    updateJobs($id,$reseller_id);
                }
                $query = $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`salutation`=?,`birthday`=?,`country`=?,`fax`=?,`name`=?,`vname`=?,`mail`=?,`phone`=?,`handy`=?,`city`=?,`cityn`=?,`street`=?,`streetn`=?,`fdlpath`=?,`mail_backup`=?,`mail_gsupdate`=?,`mail_securitybreach`=?,`mail_serverdown`=?,`mail_ticket`=?,`mail_vserver`=?$jobPending WHERE `id`=? and `resellerid`=? LIMIT 1");
                $query->execute(array($salutation,$birthday,$country,$fax,$name,$vname,$mail,$phone,$handy,$city,$cityn,$street,$streetn,$fdlpath,$mail_backup,$mail_gsupdate,$mail_securitybreach,$mail_serverdown,$mail_ticket,$mail_vserver,$id,$resellerlockupid));
                customColumns('U',$id,'save');
                if ($id != $admin_id) {
                    $tempArray = array();
                    $query=($accounttype == 'r' and $reseller_id==0) ? $sql->prepare("SELECT `id` FROM `usergroups` WHERE `id`=? AND `grouptype`=? AND `resellerid`=0 LIMIT 1") : $sql->prepare("SELECT `id` FROM `usergroups` WHERE `id`=? AND `grouptype`=? AND `resellerid`=? LIMIT 1");
                    $query2 = $sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`,`resellerID`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `groupID`=VALUES(`groupID`)");
                    foreach ($ui->id('groups',10, 'post') as $gid) {
                        $tempArray[] = $gid;
                        if ($accounttype == 'r' and $reseller_id==0) $query->execute(array($gid,$accounttype));
                        else $query->execute(array($gid,$accounttype,$resellerlockupid));
                        if(isid($query->fetchColumn(),10)) $query2->execute(array($id,$gid,$resellerlockupid));
                    }
                    $query = $sql->prepare("SELECT `groupID` FROM `userdata_groups` WHERE `userID`=? AND `resellerID`=?");
                    $query2 = $sql->prepare("DELETE FROM `userdata_groups` WHERE `groupID`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($id,$resellerlockupid));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        if (!in_array($row['groupID'],$tempArray)) $query2->execute(array($row['groupID'],$id,$resellerlockupid));
                    }
                }
                $query = $sql->prepare("DELETE FROM `userpermissions` WHERE `userid`=? LIMIT 1");
                $query->execute(array($id));
                if (isset($template_file)) $template_file .= $spracheResponse->table_add ."<br />";
                else $template_file = $spracheResponse->table_add ."<br />";
                $loguseraction="%mod% %user% $cname";
                $insertlog->execute();
            } else {
                $template_file = 'userpanel_404.tpl';
            }
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d', 'get') == 'pw' and $ui->id('id', 10, 'get') and $pa['userPassword']) {
    $id = $ui->id('id', 10, 'get');
    $query=($reseller_id==0) ? $sql->prepare("SELECT `cname`,`accounttype` FROM `userdata` WHERE `id`=? AND (`resellerid`=? OR `id`=`resellerid`) LIMIT 1") : $sql->prepare("SELECT `cname`,`accounttype` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (($row['accounttype'] == 'a' and $pa['user']) or ($row['accounttype'] != 'a') and ($pa['user'] or $pa['user_users'])) $cname = $row['cname'];
    }
    if (!$ui->smallletters('action',2, 'post') and isset($cname)) {
        $template_file = 'admin_user_pass.tpl';
    } else if ($ui->smallletters('action',2, 'post') == 'pw' and isset($cname)) {
        $errors = array();
        if (!$ui->password('password',20, 'post')) $errors[] = $sprache->error_pass;
        if (!$ui->password('pass2',20, 'post'))$errors[] = $sprache->error_pass;
        if ($ui->password('password',20, 'post') != $ui->password('pass2',20, 'post')) $errors[] = $sprache->error_passw_succ;
        if (count($errors)>0) {
            $template_file = implode('<br />',$errors);
        } else {
            if ($reseller_id != 0 and $admin_id != $reseller_id) $reseller_id = $admin_id;
            $password = $ui->password('password',20, 'post');
            $salt=md5(mt_rand().date('Y-m-d H:i:s:u'));
            $security=createHash($cname,$password,$salt,$aeskey);
            $query=($reseller_id==0) ? $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`security`=?,`salt`=? WHERE id=? AND (`resellerid`=? OR `id`=`resellerid`) LIMIT 1") : $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`security`=?,`salt`=? WHERE id=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($security,$salt,$id,$reseller_id));
            $template_file = $spracheResponse->table_add ."<br />";
            $loguseraction="%psw% %user% $cname";
            $insertlog->execute();
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else {
    $ticketLinks['Y'] = 'admin.php?w=us&amp;a='.$ui->id('a',3, 'get');
    $ticketLinks['N'] = 'admin.php?w=us&amp;a='.$ui->id('a',3, 'get');
    $ticketLinks['R'] = 'admin.php?w=us&amp;a='.$ui->id('a',3, 'get');
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'da') {
        $orderby = '`active` DESC';
    } else if ($ui->st('o', 'get') == 'aa') {
        $orderby = '`active` ASC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = '`name` DESC';
    } else if ($ui->st('o', 'get') == 'an') {
        $orderby = '`name` ASC';
    } else if ($ui->st('o', 'get') == 'du') {
        $orderby = '`cname` DESC';
    } else if ($ui->st('o', 'get') == 'au') {
        $orderby = '`cname` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = '`accounttype` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = '`accounttype` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = '`id` DESC';
    } else {
        $orderby = '`id` ASC';
        $o = 'ai';
    }
    $and = '';
    if (!$pa['user']) $and=" AND `accounttype` IN ('u','r')";
    $selected = array();
    if (isset($ui->get['state'])) {
        foreach ($ui->get['state'] as $get) {
            if (preg_match('/[YNR]/',$get)) $selected[] = $get;
        }
    } else {
        $selected=array('Y','N','R');
    }
    foreach ($ticketLinks as $k=>$v) {
        foreach (array('Y','N','R') as $s) {
            if ((in_array($s,$selected) and $k != $s) or (!in_array($s,$selected) and $k==$s)) $ticketLinks[$k] .= '&amp;state[] = '.$s;
        }
    }
    if(count($selected)==1) $and.=" AND `active`='${selected[0]}'";
    else if(count($selected)==2) $and.=" AND (`active`='${selected[0]}' OR `active`='${selected[1]}')";
    if($reseller_id==0) {
        $query = $sql->prepare("SELECT `id`,`active`,`cname`,`name`,`accounttype`,`jobPending`,`resellerid` FROM `userdata` WHERE (`resellerid`=0 OR `id`=`resellerid`) ${and} ORDER BY $orderby LIMIT $start,$amount");
        $query->execute();
    } else {
        $query = $sql->prepare("SELECT `id`,`active`,`cname`,`name`,`accounttype`,`jobPending`,`resellerid` FROM `userdata` WHERE `resellerid`=? ${and} ORDER BY $orderby LIMIT $start,$amount");
        if ($admin_id==$reseller_id) {
            $query->execute(array($reseller_id));
        } else {
            $query->execute(array($admin_id));
        }
    }
    $query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='us' AND (`status` IS NULL OR `status`=1 OR `status`=4) ORDER BY `jobID` DESC LIMIT 1");
    $table = array();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $adminaccount = false;
        if ($row['accounttype']=="a") {
            $adminaccount = true;
            $accounttype = $sprache->accounttype_admin;
        } else if ($row['accounttype']=="r") {
            $accounttype = $sprache->accounttype_reseller;
        } else {
            $accounttype = $sprache->accounttype_user;
        }
        if ($row['jobPending'] == 'Y') {
            $query2->execute(array($row['id'], $row['resellerid']));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if ($row2['action'] == 'ad') $jobPending = $gsprache->add;
                else if ($row2['action'] == 'dl') $jobPending = $gsprache->del;
                else $jobPending = $gsprache->mod;
                $json=@json_decode($row2['extraData']);
                $tobeActive=(is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
            }
        } else {
            $jobPending = $gsprache->no;
        }
        if (($row['active'] == 'Y' and $row['jobPending'] == 'N') or ($row['jobPending'] == 'Y') and isset($tobeActive) and $tobeActive == 'Y') {
            $imgName='16_ok';
            $imgAlt='Active';
        } else {
            $imgName='16_bad';
            $imgAlt='Inactive';
        }
        $table[] = array('id' => $row['id'], 'img' => $imgName,'alt' => $imgAlt,'adminaccount' => $adminaccount,'accounttype' => $accounttype,'cname' => $row['cname'], 'name' => $row['name'], 'jobPending' => $jobPending,'active' => $row['active']);
    }
    $next = $start+$amount;
    if ($reseller_id==0) {
        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE (`resellerid`=0 OR `id`=`resellerid`) ${and}");
        $query->execute();
    } else {
        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE `resellerid`=? ${and}");
        if ($admin_id==$reseller_id) $query->execute(array($reseller_id));
        else $query->execute(array($admin_id));
    }
    $colcount = $query->fetchColumn();
    if ($colcount>$next) {
        $vor = $start+$amount;
    } else {
        $vor = $start;
    }
    $back = $start - $amount;
    if ($back>=0){
        $zur = $start - $amount;
    } else {
        $zur = $start;
    }
    $pageamount = ceil($colcount / $amount);
    $link='<a href="admin.php?w=us&amp;d=md&amp;o='.$o.'&amp;a=';
    if(!isset($amount)) {
        $amount=20;
    }
    $link .= $amount;
    if ($start==0) {
        $link .= '&p=0" class="bold">1</a>';
    } else {
        $link .= '&p=0">1</a>';
    }
    $pages[] = $link;
    $i = 1;
    while ($i<$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="admin.php?w=us&amp;d=md&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>';
        } else {
            $pages[] = '<a href="admin.php?w=us&amp;d=md&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = 'admin_user_list.tpl';
}