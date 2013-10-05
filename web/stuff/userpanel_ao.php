<?php
/**
 * File: userpanel_ao.php.
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
if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['useraddons'])) {
	header('Location: userpanel.php');
	die('No acces');
}
$sprache = getlanguagefile('images',$user_language,$reseller_id);
$loguserid=$user_id;
$logusername=getusername($user_id);
$logusertype="user";
$logreseller = 0;
if (isset($admin_id)) {
	$logsubuser=$admin_id;
} else if (isset($subuser_id)) {
	$logsubuser=$subuser_id;
} else {
	$logsubuser = 0;
}
if (isset($admin_id) and $reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}
if (isset($admin_id)) {
	$logsubuser=$admin_id;
} else if (isset($subuser_id)) {
	$logsubuser=$subuser_id;
} else {
	$logsubuser = 0;
}
if ($ui->id('id', 10, 'get') and $ui->id('adid',10,'get') and in_array($ui->smallletters('action',2,'get'), array('ad','dl')) and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'),$substituteAccess['gs']))) {
    include(EASYWIDIR . '/stuff/ssh_exec.php');
	include(EASYWIDIR . '/stuff/keyphrasefile.php');
    $gameserverid=$ui->id('id',19,'get');
    $addonid=$ui->id('adid',10,'get');
    $action=$ui->smallletters('action',2,'get');
    $query = $sql->prepare("SELECT g.`rootID`,g.`newlayout`,g.`serverid`,g.`serverip`,g.`port`,g.`protected`,AES_DECRYPT(g.`ftppassword`,?) AS `dftpppassword`,AES_DECRYPT(g.`ppassword`,?) AS `decryptedppassword`, t.`modfolder`,t.`shorten`,s.`servertemplate`,u.`cname` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey,$aeskey,$gameserverid,$user_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $protected=$row['protected'];
        $rootID=$row['rootID'];
        $serverip=$row['serverip'];
        $serverid=$row['serverid'];
        $port=$row['port'];
        $modfolder=$row['modfolder'];
        $ppassword=$row['decryptedppassword'];
        $ftppass=$row['dftpppassword'];
        $servertemplate=$row['servertemplate'];
        if ($servertemplate==1) {
            $shorten=$row['shorten'];
        } else {
            $shorten=$row['shorten'] . '-' . $servertemplate;
        }
        $customer=$row['cname'];
        $newlayout=$row['newlayout'];
    }
    if (isset($rootID)) {
        $query = $sql->prepare("SELECT `addon`,`paddon`,`type`,`folder` FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($addonid,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $addon=$row['addon'];
            $paddon=$row['paddon'];
            $type=$row['type'];
            $folder=$row['folder'];
        }
        $rdata=serverdata('root',$rootID,$aeskey);
        $sship=$rdata['ip'];
        $sshport=$rdata['port'];
        $sshuser=$rdata['user'];
        $sshpass=$rdata['pass'];
        if ($newlayout == 'Y') {
            $customer=$customer . '-' . $gameserverid;
        }
        if ($protected=="N") {
            $serverfolder=$customer.'/server/'.$serverip . '_' . $port. '/' . $shorten;
        } else {
            $serverfolder=$customer . '/pserver/' . $serverip . '_' . $port. '/' . $shorten;
            $ftppass=$ppassword;
            $customer=$customer."-p";
        }
        if ($ui->st('action','get') == 'ad' and ($protected=="N" or ($protected=="Y" and $paddon=="Y"))) {
            if(ssh2_execute('gs',$rootID,"sudo -u $customer ./control.sh addaddon $type $addon \"$serverfolder\" \"$modfolder\"")!==false){
                $query = $sql->prepare("INSERT INTO `addons_installed` (`userid`,`addonid`,`serverid`,`servertemplate`,`paddon`,`resellerid`) VALUES (?,?,?,?,?,?)");
                $query->execute(array($user_id,$addonid,$serverid,$servertemplate,$protected,$reseller_id));
                $template_file = $sprache->addon_inst;
                $actionstatus="ok";
            } else {
                $template_file = $sprache->failed;
                $actionstatus="fail";
            }
        } else if ($ui->st('action','get') == 'dl' and $ui->id('rid',19,'get')) {
            $installedid=$ui->id('rid',19,'get');
            $cmds = array();
            $cmds[]="sudo -u $customer ./control.sh deladdon $type $addon \"$serverfolder\" \"$modfolder\" \"$folder\"";
            $delids=$addonid;
            while (isset($delids) and isset($installedid)) {
                $query = $sql->prepare("SELECT `id`,`folder`,`addon` FROM `addons` WHERE `depending`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($delids,$reseller_id));
                if (isset($installedid)) {
                    $query2 = $sql->prepare("DELETE FROM `addons_installed` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($installedid,$reseller_id));
                    unset($installedid);
                    if (isset($deladdon)) {
                        $cmds[]="sudo -u $customer ./control.sh deladdon $type $deladdon \"$serverfolder\" \"$modfolder\" \"$delfolder\"";
                        unset($deladdon);
                        unset($delfolder);
                    }
                }
                unset($delids);
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $delids=$row['id'];
                    $delfolder=$row['folder'];
                    $deladdon=$row['addon'];
                    $query2 = $sql->prepare("SELECT `id` FROM `addons_installed` WHERE `addonid`=? AND `serverid`=? AND `servertemplate`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($delids,$serverid,$servertemplate,$reseller_id));
                    $installedid=$query2->fetchColumn();
                }
            }
            if(ssh2_execute('gs',$rootID,$cmds)!==false){
                $template_file = $sprache->addon_del;
                $actionstatus="ok";
            } else {
                $template_file = $sprache->failed;
                $actionstatus="fail";
            }
        }
        if (isset($actionstatus) and ($protected=="N" or ($protected=="Y" and $paddon=="Y"))) {
            $loguseraction="%$action% %addon% $addon $serverip:$port %$actionstatus%";
            $insertlog->execute();
        } else {
            $template_file = $sprache->failed;
        }
    } else {
        $template_file = $sprache->failed;
    }
} else if ($ui->id('id',19,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'),$substituteAccess['gs']))) {
	$username=getusername($user_id);
    $switchID=$ui->id('id',19,'get');
	$table = array();
    $query = $sql->prepare("SELECT `language` FROM `settings` WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    $default_language=$query->fetchColumn();
	$query = $sql->prepare("SELECT g.`serverid`,g.`serverip`,g.`port`,g.`protected`,g.`queryName`,s.`servertemplate`,t.`shorten`,t.`qstat` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`userid`=? AND g.`id`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($user_id,$switchID,$reseller_id));
	$i = 0;
	foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table2 = array();
        $table3 = array();
		$description = '';
        $serverip=$row['serverip'];
		$serverport=$row['port'];
		$qstat=$row['qstat'];
		$serverid=$row['serverid'];
        $servershorten=$row['shorten'];
		$servertemplate=$row['servertemplate'];
        $currentTemplate=$servershorten;
        if ($servertemplate>1) $currentTemplate=$servershorten . '-' . $servertemplate;
		$protected=$row['protected'];
        $description=$row['queryName'];
		if ($protected== 'Y') {
            $query2 = $sql->prepare("SELECT `id`,`menudescription`,`depending`,`type` FROM `addons` WHERE `active`='Y' AND (`shorten`=? OR `shorten`=?) AND `paddon`='Y' AND `resellerid`=? ORDER BY `shorten`,`depending`,`menudescription`");
		} else {
            $query2 = $sql->prepare("SELECT `id`,`menudescription`,`depending`,`type` FROM `addons` WHERE `active`='Y' AND (`shorten`=? OR `shorten`=?) AND `resellerid`=? ORDER BY `shorten`,`depending`,`menudescription`");
		}
        $query2->execute(array($servershorten,$qstat,$reseller_id));
		foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
			$adid=$row2['id'];
			$depending=$row2['depending'];
			$menudescription=$row2['menudescription'];
			$descriptionrow = '';
			$lang = '';
			$query3 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
            $query3->execute(array($adid,$user_language,$reseller_id));
            $descriptionrow=$query3->fetchColumn();
			if(empty($descriptionrow)) {
                $query3 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
                $query3->execute(array($adid,$default_language,$reseller_id));
                $descriptionrow=$query->fetchColumn();
			}
            $addescription=nl2br($descriptionrow);
            if ($protected=="Y") {
                $query3 = $sql->prepare("SELECT `id` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `addonid`=? AND `servertemplate`=? AND `paddon`='Y' AND `resellerid`=? LIMIT 1");
            } else {
                $query3 = $sql->prepare("SELECT `id` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `addonid`=? AND `servertemplate`=? AND `resellerid`=? LIMIT 1");
            }
            $query3->execute(array($user_id,$serverid,$adid,$servertemplate,$reseller_id));
            $installedid=$query3->fetchColumn();
            $delete = '';
            if (isid($installedid,19)){
                $imgName='16_delete';
                $imgAlt='Remove';
                $bootstrap='icon-remove-sign';
                $action='dl';
                $delete='&amp;rid='.$installedid;
            } else {
                $query3 = $sql->prepare("SELECT `id` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `servertemplate`=? AND `addonid`=? AND `resellerid`=?");
                $query3->execute(array($user_id,$serverid,$servertemplate,$depending,$reseller_id));
                $colcount=$query3->rowcount();
                if ($row2['type'] == 'map' or $depending==0 or ($depending>0 and $colcount>0)) {
                    $action='ad';
                    $imgName='16_add';
                    $bootstrap='icon-plus-sign';
                    $imgAlt='Install';
                } else {
                    $action='none';
                    $query3 = $sql->prepare("SELECT `menudescription` FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query3->execute(array($depending,$reseller_id));
                    $imgName='16_notice';
                    $bootstrap='icon-warning-sign';
                    $imgAlt=$sprache->requires.': '.$query3->fetchColumn();
                }
            }
            $link='userpanel.php?w=ao&amp;id='.$switchID.'&amp;adid='.$adid.'&amp;action='.$action.$delete.'&amp;r=gs';
            if ($row2['type'] == 'tool') {
                $table2[]=array('adid' => $adid,'menudescription' => $menudescription,'addescription' => $addescription,'installedid' => $installedid,'img' => $imgName,'bootstrap' => $bootstrap,'alt' => $imgAlt,'link' => $link);
            } else if ($row2['type'] == 'map') {
                $table3[]=array('adid' => $adid,'menudescription' => $menudescription,'addescription' => $addescription,'installedid' => $installedid,'img' => $imgName,'bootstrap' => $bootstrap,'alt' => $imgAlt,'link' => $link);
            }
		}
		$table=array('id' => $switchID,'serverip' => $serverip,'port' => $serverport,'tools' => $table2,'maps' => $table3,'name' => $description);
        unset($table2,$table3);
	}			
	$template_file = "userpanel_gserver_addon.tpl";
} else {
    $template_file = 'userpanel_404.tpl';
}