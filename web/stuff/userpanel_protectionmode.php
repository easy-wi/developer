<?php

/**
 * File: userpanel_protectionmode.php.
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
if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['restart']) or !$ui->id('id', 10, 'get')) {
	header('Location: userpanel.php');
	die;
}

include(EASYWIDIR . '/stuff/class_ftp.php');
include(EASYWIDIR . '/stuff/functions_gs.php');
include(EASYWIDIR . '/stuff/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('gserver', $user_language, $reseller_id);
$loguserid = $user_id;
$logusername = getusername($user_id);
$logusertype = 'user';
$logreseller = 0;

if (isset($admin_id)) {
	$logsubuser = $admin_id;
} else if (isset($subuser_id)) {
	$logsubuser = $subuser_id;
} else {
	$logsubuser = 0;
}

if (isset($admin_id) and $reseller_id != 0 and $admin_id != $reseller_id) {
    $reseller_id = $admin_id;
}

$files = array();

$query = $sql->prepare("SELECT g.*,AES_DECRYPT(g.`ftppassword`,?) AS `dftppassword`,AES_DECRYPT(g.`ppassword`,?) AS `dpftppassword`,t.`protected` AS `tpallowed`,t.`shorten`,t.`protectedSaveCFGs`,t.`gamebinary`,t.`binarydir`,t.`modfolder`,u.`cname`,s.`servertemplate` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`userid`=? AND s.`resellerid`=? LIMIT 1");
$query->execute(array($aeskey, $aeskey, $ui->id('id', 10, 'get'), $user_id, $reseller_id));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $currentID = $row['serverid'];
	$serverip = $row['serverip'];
	$port = $row['port'];
    $protected = $row['protected'];
    $rootid = $row['rootID'];
    $ftppass = $row['dftppassword'];
    $ftppassProtected = $row['dpftppassword'];
    $shorten = $row['shorten'];
    $gsfolder = $serverip . '_' . $port;
    $gamestring = '1_' . $row['shorten'];
    $pallowed=($row['pallowed'] == 'Y' and $row['tpallowed'] == 'Y') ? 'Y' : 'N';
    $customer=($row['newlayout'] == 'Y') ? $row['cname'] . '-' . $ui->id('id', 10, 'get') : $row['cname'];
    $customerp = $customer . '-p';
    $serverTemplate = ($row['servertemplate'] != 1) ? $row['shorten'] . '-' . $row['servertemplate'] : $row['shorten'];

    foreach (explode("\r\n", $row['protectedSaveCFGs']) as $cfg) {
        if ($cfg != '') {
            $files[] = $cfg;
        }
    }

    if ($row['gamebinary'] == 'srcds_run') {
        $gamePath = $row['binarydir'] . '/' . $row['modfolder'];
    } else if ($row['gamebinary'] == 'hlds_run') {
        $gamePath = $row['modfolder'];
    } else {
        $gamePath = '';
    }

    $gamePath = str_replace(array('//', '///', '////'), '/', $gamePath);
}

if ($query->rowCount() == 0 or (isset($pallowed) and $pallowed== 'N') or (isset($_SESSION['sID']) and !in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

	redirect('userpanel.php');

} else if (isset($rootid)) {

    $rdata = serverdata('root', $rootid, $aeskey);
    $sship = $rdata['ip'];
    $sshport = $rdata['port'];
    $sshuser = $rdata['user'];
    $sshpass = $rdata['pass'];
    $ftpport = $rdata['ftpport'];

    if (isset($protected, $serverip, $port) and $protected == 'Y' and isset($currentID)) {

        $query = $sql->prepare("UPDATE `serverlist` SET `anticheat`='1' WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($currentID, $reseller_id));

        $query = $sql->prepare("UPDATE `gsswitch` SET `protected`='N' WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id', 10, 'get'), $reseller_id));

        $ftp = new EasyWiFTP($sship, $ftpport, $customerp, $ftppassProtected);
        $ftp->createSecondFTPConnect($sship, $ftpport, $customer, $ftppass);
        if ($ftp->loggedIn and $ftp->secondLoggedIn) {
            $ftp->downloadToTemp($gsfolder . '/' . $shorten . '/' . $gamePath . '/', 0, $files);
            $ftp->uploadFileFromTemp('server/'. $gsfolder . '/' . $serverTemplate . '/' . $gamePath .'/');
        }

        $ftp = null;

        $cmds = gsrestart($ui->id('id', 10, 'get'),'re', $aeskey, $reseller_id);
        ssh2_execute('gs', $rootid, $cmds);

        $loguseraction = '%stop% %pmode% ' . $serverip . ':' .$port;
        $insertlog->execute();
        $template_file = $sprache->protect . ' ' . $sprache->off2;

    } else if (isset($protected, $serverip, $port, $rootid, $customer, $ftppass) and $protected == 'N') {

        $cmds = gsrestart($ui->id('id', 10, 'get'), 'sp', $aeskey, $reseller_id);
        $randompass = passwordgenerate(10);
        $cmds[] = './control.sh mod '.$customer . ' ' . $ftppass . ' ' . $randompass;
        $cmds[] = "sudo -u ${customer}-p ./control.sh reinstserver ${customer}-p ${gamestring} ${gsfolder} protected";

        $query = $sql->prepare("UPDATE `gsswitch` SET `ppassword`=AES_ENCRYPT(?,?),`protected`='Y',`psince`=NOW() WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($randompass, $aeskey, $ui->id('id', 10, 'get'), $reseller_id));

        ssh2_execute('gs', $rootid, $cmds);

        $ftp = new EasyWiFTP($sship, $ftpport, $customer, $ftppass);
        $ftp->createSecondFTPConnect($sship, $ftpport, $customerp, $randompass);

        if ($ftp->loggedIn and $ftp->secondLoggedIn) {
            $ftp->downloadToTemp('server/' . $gsfolder . '/' . $serverTemplate . '/' . $gamePath . '/', 0, $files);
            $ftp->uploadFileFromTemp($gsfolder . '/' . $shorten . '/' . $gamePath . '/');
        }

        $ftp = null;

        $loguseraction = '%restart% %pmode% ' . $serverip . ':' .$port;
        $insertlog->execute();
        $template_file = $sprache->protect . ' ' . $sprache->on;
    }
}