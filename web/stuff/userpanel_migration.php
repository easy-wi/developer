<?php
/**
 * File: userpanel_migration.php.
 * Author: Ulrich Block
 * Date: 01.02.13
 * Time: 17:13
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

if ((!isset($main) or $main!=1) or (!isset($user_id) or (isset($user_id) and !$pa['restart']))) {
    header('Location: userpanel.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache=getlanguagefile('gserver',$user_language,$reseller_id);
$loguserid=$user_id;
$logusername=getusername($user_id);
$logusertype="user";
$logreseller=0;
$logsubuser=0;
if (isset($admin_id)) {
    $logsubuser=$admin_id;
} else if (isset($subuser_id)) {
    $logsubuser=$subuser_id;
}
if (isset($admin_id) and $reseller_id!=0) {
    $reseller_id=$admin_id;
}
$ftpAddress='';
$ftpPort=21;
$ftpUser='';
$ftpPassword='';
$ftpPath='';
$thisID=0;
$thisTemplate='';
$ssl=($ui->active('ssl','post')) ? $ui->active('ssl','post') : 'N';
$error=array();
$table=array();
$query=$sql->prepare("SELECT AES_DECRYPT(g.`ftppassword`,?) AS `cftppass`,g.`id`,g.`newlayout`,g.`rootID`,g.`serverip`,g.`port`,g.`pallowed`,g.`protected`,u.`cname` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`userid`=? AND g.`resellerid`=?");
$query2=$sql->prepare("SELECT s.`id`,t.`description`,t.`shorten`,t.`gamebinary`,t.`binarydir`,t.`modfolder`,t.`appID` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? GROUP BY t.`shorten` ORDER BY t.`shorten`");
$query->execute(array($aeskey,$user_id,$reseller_id));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    if (!isset($_SESSION['sID']) or in_array($row['id'],$substituteAccess['gs'])) {
        $customer=$row['cname'];
        if ($row['newlayout']=='Y') $customer=$row['cname'].'-'.$row['id'];
        $temp=array();
        $query2->execute(array($row['id']));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $search='';
            if ($row2['gamebinary']=='hlds_run' or ($row2['gamebinary']=='srcds_run' and ($row2['appID']==740 or $row2['appID']==730))) {
                $search='/'.$row2['modfolder'];
            } else if ($row2['gamebinary']=='srcds_run' and $row2['appID']!=740 and $row2['appID']!=730) {
                $search='/'.$row2['binarydir'].'/'.$row2['modfolder'];
            }
            $temp[$row2['shorten']]=array('shorten'=>$row2['shorten'],'description'=>$row2['description'],'searchFor'=>$search,'modfolder'=>$row2['modfolder']);
        }
        $table[$row['id']]=array('id'=>$row['id'],'address'=>$row['serverip'].':'.$row['port'],'games'=>$temp,'rootID'=>$row['rootID'],'gsfolder'=>$row['serverip'].'_'.$row['port'],'customer'=>$customer,'cftppass'=>$row['cftppass']);
    }
}
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->smallletters('action',2,'post')=='ms') {
    function checkFolders ($dir,$searchFor,$maxDepth=false,$currentDepth=0) {
        global $ftp;
        $donotsearch=array('bin','cfg','cl_dlls','dlls','gfx','hl2','manual','maps','materials','models','particles','recource','scenes','scripts','sound','sounds','textures','valve','reslists');
        if ($dir!='/') $dir=$dir.'/';
        $spl=strlen($searchFor)*(-1);
        $rawList=@ftp_rawlist($ftp,$dir);
        if ($rawList) {
            $folders=array();
            foreach ($rawList as $d) {
                $list=preg_split('/(\s|\s+)/',$d,-1,PREG_SPLIT_NO_EMPTY);
                if (preg_match('/^d[rwx\-]{9}+$/',$list[0]) and !preg_match('/^[\.\/]{0,}Steam[\/]{0,}+$/',$list[count($list)-1]) and !in_array($list[count($list)-1],$donotsearch)) {
                    if (substr($dir.$list[count($list)-1],$spl)==$searchFor) return $dir.$list[count($list)-1];
                    $folders[]=$dir.$list[count($list)-1];
                    if (is_numeric($maxDepth) and $currentDepth<($maxDepth+1)) {
                        $array=checkFolders($dir.$list[count($list)-1],$searchFor,$maxDepth,$currentDepth+1);
                        if (is_array($array)) {
                            foreach ($array as $f){
                                if (substr($f,$spl)==$searchFor) return $f;
                                $folders[]=$f;
                            }
                        } else if (substr($array,$spl)==$searchFor) {
                            return $array;
                        }
                    }
                }
            }
            return $folders;
        }
        return $dir;
    }
    if (!$ui->domain('ftpAddress','post') and !$ui->ip('ftpAddress','post')) {
        $error[]=$sprache->ftp_adresse;
    } else {
        $ftpAddress=$ui->post['ftpAddress'];
    }
    if (!$ui->port('ftpPort','post')) {
        $error[]=$sprache->ftp_port;
    } else {
        $ftpPort=$ui->port('ftpPort','post');
    }
    if (!$ui->config('ftpUser','post')) {
        $error[]=$sprache->ftp_user;
    } else {
        $ftpUser=$ui->config('ftpUser','post');
    }
    if (!$ui->config('ftpPassword','post')) {
        $error[]=$sprache->ftp_password;
    } else {
        $ftpPassword=$ui->config('ftpPassword','post');
    }
    if (!$ui->id('switchID',10,'post') or !isset($table[$ui->id('switchID',10,'post')])) {
        $error[]=$sprache->server;
    } else {
        $thisID=$ui->id('switchID',10,'post');
        $address=$table[$ui->id('switchID',10,'post')]['address'];
        $rootID=$table[$ui->id('switchID',10,'post')]['rootID'];
        $gsfolder=$table[$ui->id('switchID',10,'post')]['gsfolder'];
        $customer=$table[$ui->id('switchID',10,'post')]['customer'];
        $cftppass=$table[$ui->id('switchID',10,'post')]['cftppass'];
    }
    if (!$ui->config('template','post',$thisID) or !isset($table[$ui->id('switchID',10,'post')]['games'])) {
        $error[]=$gsprache->template;
    } else if (isset($table[$ui->id('switchID',10,'post')]['games'])) {
        foreach($table[$ui->id('switchID',10,'post')]['games'] as $game) {
            unset($temp);
            if ($ui->config('template','post',$thisID)==$game['shorten']) {
                $temp=1;
            } else if ($ui->config('template','post',$thisID)==$game['shorten'].'-2') {
                $temp=2;
            } else if ($ui->config('template','post',$thisID)==$game['shorten'].'-3') {
                $temp=3;
            }
            if (isset($temp)) {
                $template=$temp;
                $shorten=$game['shorten'];
                $searchFor=str_replace('/','',$game['searchFor']);
                $modFolder=$game['modfolder'];
            }
        }
        if (isset($shorten)) {
            $thisTemplate=$ui->config('template','post',$thisID);
        } else if (!in_array($gsprache->template,$error)) {
            $error[]=$gsprache->template;
        }
    }
    if ($ui->anyPath('ftpPath','post')) $ftpPath=$ui->anyPath('ftpPath','post');
    $ftp=($ssl=='N') ? @ftp_connect($ftpAddress,$ftpPort,5) : @ftp_ssl_connect($ftpAddress,$ftpPort,5);
    if ($ftp) {
        $login=@ftp_login($ftp,$ftpUser,$ftpPassword);
        if ($login) {
            if (isset($searchFor)) {
                if ($ftpPath!='') @ftp_chdir($ftp,$ftpPath);
                $currentPath=@ftp_pwd($ftp);
                if (substr($currentPath,strlen($searchFor)*(-1))==$searchFor) {
                    $ftpPath=$currentPath;
                } else {
                    $error[]=$sprache->ftp_path.'. '.$sprache->import_corrected;
                    $foundPath=checkFolders($currentPath,$searchFor,5);
                    $ftpPath=(is_array($foundPath)) ? '' : $foundPath;
                }
            }
        } else {
            $error[]=$sprache->ftp_user;
            $error[]=$sprache->ftp_password;
        }
        ftp_close($ftp);
    } else {
        if (!in_array($sprache->ftp_adresse,$error)) $error[]=$sprache->ftp_adresse;
        if (!in_array($sprache->ftp_port,$error)) $error[]=$sprache->ftp_port;
    }
    if (count($error)==0 and isset($rootID)) {
        include(EASYWIDIR . '/stuff/ssh_exec.php');
        $rdata=serverdata('root',$rootID,$aeskey);
        $sship=$rdata['ip'];
        $sshport=$rdata['port'];
        $sshuser=$rdata['user'];
        $sshpass=$rdata['pass'];
        if ($ssl=='N') {
            $ftpConnect='ftp://';
        } else {
            $ftpConnect='ftps://';
        }
        $ftpConnect.=str_replace('//','/',$ftpAddress.':'.$ftpPort.'/'.$ftpPath);
        ssh2_execute('gs',$rootID,"sudo -u ${customer} ./control.sh migrateserver ${customer} 1_${shorten} ${gsfolder} ${template} ${ftpUser} ${ftpPassword} ${ftpConnect} ${modFolder}");
        $loguseraction="%import% %gserver% ${address}";
        $template_file=$sprache->import_start;
        $insertlog->execute();
    }
}
if (!isset($template_file) and isset($customer)) {
    $template_file="userpanel_gserver_migration.tpl";
} else if (!isset($template_file)) {
    $template_file='userpanel_404.tpl';
}