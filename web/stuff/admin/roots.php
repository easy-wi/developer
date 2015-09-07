<?php

/**
 * File: roots.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['roots'])) {
    header('Location: admin.php');
    die('No access);
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$sprache = getlanguagefile('roots', $user_language, $reseller_id);
$gsSprache = getlanguagefile('gserver', $user_language, $reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;

} else {
    $logsubuser = (isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller = 0;
}

if ($ui->w('action', 4, 'post') and !token(true)) {

	unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_roots_add.tpl' : 'admin_roots_md.tpl';

} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    $errors = array();
    $table = array();
    $ownerName = '';

    $id = $ui->id('id', 10, 'get');
    $cores = ($ui->id('cores', 3, 'post')) ? $ui->id('cores', 3, 'post') : 4;
    $externalID = $ui->escaped('externalID', 'post');
    $steamAccount = $ui->username('steamAccount', 255, 'post');
    $steamPassword = $ui->password('steamPassword', 255, 'post');
    $keyname = $ui->startparameter('keyname', 'post');
    $hyperthreading = $ui->active('hyperthreading', 'post');
    $ip = $ui->ip('ip', 'post');
    $altips = $ui->ips('altips', 'post');
    $user = $ui->username('user', 20, 'post');
    $pass = $ui->password('pass', 255, 'post');
    $os = $ui->w('os', 1, 'post');
    $bit = $ui->id('bit', 2, 'post');
    $desc = $ui->description('desc', 'post');
    $ram = $ui->id('ram', 5, 'post');
    $updates = $ui->id('updates', 1, 'post');

    $ownerID = ($ui->active('assignToReseller', 'post') == 'Y' and $ui->id('ownerID', 10, 'post')) ? $ui->id('ownerID', 10, 'post') : 0;
    $publickey = ($ui->w('publickey', 1, 'post')) ? $ui->w('publickey', 1, 'post') : 'N';
    $assignToReseller = ($ui->active('assignToReseller', 'post')) ? $ui->active('assignToReseller', 'post') : 'N';
    $connectIpOnly = ($ui->active('connectIpOnly', 'post')) ? $ui->active('connectIpOnly', 'post') : 'N';
    $active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
    $updateMinute = ($ui->id('updateMinute', 2, 'post')) ? $ui->id('updateMinute', 2, 'post') : 0;
    $ftpport = ($ui->port('ftpport', 'post')) ? $ui->port('ftpport', 'post') : 21;
    $port = ($ui->port('port', 'post')) ? $ui->port('port', 'post') : 22;
    $maxserver = ($ui->id('maxserver',4, 'post')) ? $ui->id('maxserver',4, 'post') : 10;
    $maxslots = ($ui->id('maxslots', 5, 'post')) ? $ui->id('maxslots', 5, 'post') : 512;
    $installPaths = ($ui->escaped('installPaths', 'post')) ? $ui->escaped('installPaths', 'post') : "[home]\r\npath = /home\r\nmountpoint = /\r\nsize = 500GB\r\ndefault = 1";
    $quotaActive = ($ui->active('quotaActive', 'post')) ? $ui->active('quotaActive', 'post') : 'N';
    $quotaCmd = ($ui->startparameter('quotaCmd', 'post')) ? $ui->startparameter('quotaCmd', 'post') : 'sudo /usr/sbin/setquota %cmd%';
    $repquotaCmd = ($ui->startparameter('repquotaCmd', 'post')) ? $ui->startparameter('repquotaCmd', 'post') : 'sudo /usr/sbin/repquota %cmd%';
    $blocksize = ($ui->id('blocksize', 10, 'post')) ? $ui->id('blocksize', 10, 'post') : 4096;
    $inodeBlockRatio = ($ui->id('inodeBlockRatio', 10, 'post')) ? $ui->id('inodeBlockRatio', 10, 'post') : 4;
    $configLogTime = ($ui->id('configLogTime', 3, 'post')) ? $ui->id('configLogTime', 3, 'post') : 7;
    $configDemoTime = ($ui->id('configDemoTime', 3, 'post')) ? $ui->id('configDemoTime', 3, 'post') : 365;
    $configZtmpTime = ($ui->id('configZtmpTime', 3, 'post')) ? $ui->id('configZtmpTime', 3, 'post') : 0;
    $configBadTime = ($ui->id('configBadTime', 3, 'post')) ? $ui->id('configBadTime', 3, 'post') : 0;
    $configUserID = ($ui->id('configUserID', 10, 'post')) ? $ui->id('configUserID', 10, 'post') : 1000;
    $configIonice = ($ui->active('configIonice', 'post')) ? $ui->active('configIonice', 'post') : 'Y';
    $configBinaries = ($ui->startparameter('configBinaries', 'post')) ? $ui->startparameter('configBinaries', 'post') : 'srcds_run,srcds_linux,hlds_run,hlds_amd,hlds_i686,ucc-bin,ucc-bin-real';
    $configFiles = ($ui->startparameter('configFiles', 'post')) ? $ui->startparameter('configFiles', 'post') : '*/cfg/valve.rc';
    $configBadFiles = ($ui->startparameter('configBadFiles', 'post')) ? $ui->startparameter('configBadFiles', 'post') : 'zip,rar,7zip,bz2';

    if ($reseller_id == 0) {

        $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `accounttype`='r' ORDER BY `id` DESC");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }

    } else {

        $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='r' ORDER BY `id` DESC");
        $query->execute(array($resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }
    }

    if (!$ui->smallletters('action', 2, 'post')) {

        if ($ui->st('d', 'get') == 'ad' and $reseller_id == 0) {

            $template_file = 'admin_roots_add.tpl';

        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query2 = $sql->prepare("SELECT CONCAT(`cname`,' ',`vname`,' ',`name`)  FROM `userdata` WHERE `accounttype`='r' AND `id`=? LIMIT 1");

            if ($reseller_id == 0) {

                $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `steamAcc`,AES_DECRYPT(`steamPassword`,:aeskey) AS `steamPwd` FROM `rserverdata` WHERE `id`=:id LIMIT 1");
                $query->execute(array(':aeskey' => $aeskey, ':id' => $id));

            } else {

                if ($admin_id == $reseller_id) {
                    $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `steamAcc`,AES_DECRYPT(`steamPassword`,:aeskey) AS `steamPwd` FROM `rserverdata` AS r WHERE `id`=:id AND (`resellerid`=:reseller_id OR EXISTS (SELECT 1 FROM `userdata` WHERE `resellerid`=:reseller_id AND `id`=r.`resellerid`)) LIMIT 1");
                    $query->execute(array(':aeskey' => $aeskey, ':id' => $id, ':reseller_id' => $resellerLockupID));
                } else {
                    $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `steamAcc`,AES_DECRYPT(`steamPassword`,:aeskey) AS `steamPwd` FROM `rserverdata` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                    $query->execute(array(':aeskey' => $aeskey, ':id' => $id, ':reseller_id' => $resellerLockupID));
                }
            }

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $active = $row['active'];
                $externalID = $row['externalID'];
                $hyperthreading = $row['hyperthreading'];
                $cores = $row['cores'];
                $os = $row['os'];
                $bit = $row['bitversion'];
                $desc = $row['description'];
                $ip = $row['ip'];
                $altips = $row['altips'];
                $ftpport = $row['ftpport'];
                $publickey = $row['publickey'];
                $keyname = $row['keyname'];
                $maxslots = $row['maxslots'];
                $maxserver = $row['maxserver'];
                $updates = $row['updates'];
                $updateMinute = $row['updateMinute'];
                $resellerid = $row['resellerid'];
                $steamAccount = $row['steamAcc'];
                $steamPassword = $row['steamPwd'];
                $ram = $row['ram'];
                $os = $row['os'];
                $port = $row['dport'];
                $user = $row['duser'];
                $pass = $row['dpass'];
                $ownerID = $row['resellerid'];
                $connectIpOnly = $row['connect_ip_only'];
                $installPaths = $row['install_paths'];
                $quotaActive = $row['quota_active'];
                $quotaCmd = $row['quota_cmd'];
                $repquotaCmd = $row['repquota_cmd'];
                $blocksize = $row['blocksize'];
                $inodeBlockRatio = $row['inode_block_ratio'];
                $assignToReseller = ($ownerID > 0) ? 'Y' : 'N';
                $query2->execute(array($row['resellerid']));
                $ownerName = trim($query2->fetchColumn());
                $configLogTime = $row['config_log_time'];
                $configDemoTime = $row['config_demo_time'];
                $configZtmpTime = $row['config_ztmp_time'];
                $configBadTime = $row['config_bad_time'];
                $configUserID = $row['config_user_id'];
                $configIonice = $row['config_ionice'];
                $configBinaries = $row['config_binaries'];
                $configFiles = $row['config_files'];
                $configBadFiles = $row['config_bad_files'];
            }

            $template_file =  ($query->rowCount() > 0) ? 'admin_roots_md.tpl' : 'admin_404.tpl';

        } else {
            $template_file = 'admin_404.tpl';
        }

    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$ui->active('active', 'post')) {
            $errors['active'] = $sprache->active;
        }
        if (!$ui->ip('ip', 'post')) {
            $errors['ip'] = $sprache->haupt_ip;
        }
        if (!$ui->port('port', 'post')) {
            $errors['port'] = $sprache->ssh_port;
        }
        if (!$ui->username('user', 20, 'post')) {
            $errors['user'] = $sprache->ssh_user;
        }
        if (!$ui->w('publickey', 1, 'post')) {
            $errors['publickey'] = $sprache->keyuse;
        }
        if (!$ui->id('bit', 2, 'post')) {
            $errors['bit'] = $sprache->os_bit;
        }
        if (!$ui->active('hyperthreading', 'post')) {
            $errors['hyperthreading'] = 'Hyper Threading';
        }

        if (isid($ownerID, 10)) {
            if ($reseller_id == 0) {
                $query = $sql->prepare("SELECT 1 FROM `userdata` WHERE `id`=? LIMIT 1");
                $query->execute(array($ownerID));
            } else {
                $query = $sql->prepare("SELECT 1 FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($ownerID, $resellerLockupID));
            }

            if ($query->rowCount() == 0) {
                $ownerID = ($reseller_id == 0) ? 0 : $reseller_id;
            }

        } else if (!isid($ownerID, 10)) {
            $ownerID = $reseller_id;
        }

        if ($publickey != 'N' and !is_file(EASYWIDIR . '/keys/' . $keyname)) {
            $errors['keyname'] = $sprache->keyname;
        }

        $ssh2Check = (count($errors) == 0 and $ui->active('active', 'post') == 'Y') ? ssh_check($ip, $port, $user, $publickey, $keyname, $pass) : true;

        if ($ssh2Check !== true) {

            if ($ssh2Check == 'ipport') {
                $errors['ip'] = $sprache->haupt_ip;
                $errors['port'] = $sprache->ssh_port;

            } else {
                $errors['user'] = $sprache->ssh_user;
                $errors['publickey'] = $sprache->keyuse;

                if ($publickey == 'N') {
                    $errors['pass'] = $sprache->ssh_pass;
                } else if (!$ui->active('publickey', 'post') == 'B') {
                    $errors['pass'] = $sprache->ssh_pass;
                    $errors['keyname'] = $sprache->keyname;
                } else {
                    $errors['keyname'] = $sprache->keyname;
                }
            }
        }

        if (count($errors) == 0) {

            if ($ui->st('action', 'post') == 'ad' and $reseller_id == 0) {
                $insertOwner = (isid($ownerID, 10)) ? $ownerID : 0;
                $query = $sql->prepare("INSERT INTO `rserverdata` (`config_log_time`,`config_demo_time`,`config_ztmp_time`,`config_bad_time`,`config_user_id`,`config_ionice`,`config_binaries`,`config_files`,`config_bad_files`,`active`,`steamAccount`,`steamPassword`,`hyperthreading`,`cores`,`ip`,`altips`,`port`,`user`,`pass`,`os`,`bitversion`,`description`,`ftpport`,`publickey`,`keyname`,`maxslots`,`maxserver`,`updates`,`updateMinute`,`ram`,`connect_ip_only`,`install_paths`,`quota_active`,`quota_cmd`,`repquota_cmd`,`blocksize`,`inode_block_ratio`,`externalID`,`resellerid`) VALUES (:configLogTime,:configDemoTime,:configZtmpTime,:configBadTime,:configUserID,:configIonice,:configBinaries,:configFiles,:configBadFiles,:active,AES_ENCRYPT(:steamAccount,:aeskey),AES_ENCRYPT(:steamPassword,:aeskey),:hyperthreading,:cores,:ip,:altips,AES_ENCRYPT(:port,:aeskey),AES_ENCRYPT(:user,:aeskey),AES_ENCRYPT(:pass,:aeskey),:os,:bit,:desc,:ftpport,:publickey,:keyname,:maxslots,:maxserver,:updates,:updateMinute,:ram,:connect_ip_only,:install_paths,:quota_active,:quota_cmd,:repquota_cmd,:blocksize,:inode_block_ratio,:externalID,:reseller)");
                $query->execute(array(':configLogTime' => $configLogTime, ':configDemoTime' => $configDemoTime, ':configZtmpTime' => $configZtmpTime, ':configBadTime' => $configBadTime, ':configUserID' => $configUserID, ':configIonice' => $configIonice, ':configBinaries' => $configBinaries, ':configFiles' => $configFiles, ':configBadFiles' => $configBadFiles, ':active' => $active, ':steamAccount' => $steamAccount, ':steamPassword' => $steamPassword, ':hyperthreading' => $hyperthreading, ':cores' => $cores, ':ip' => $ip, ':altips' => $altips, ':port' => $port, ':aeskey' => $aeskey, ':user' => $user, ':pass' => $pass, ':os' => $os, ':bit' => $bit, ':desc' => $desc, ':ftpport' => $ftpport, ':publickey' => $publickey, ':keyname' => $keyname, ':maxslots' => $maxslots, ':maxserver' => $maxserver, ':updates' => $updates, ':updateMinute' => $updateMinute, ':ram' => $ram, ':connect_ip_only' => $connectIpOnly, ':install_paths' => $installPaths, ':quota_active' => $quotaActive, ':quota_cmd' => $quotaCmd, ':repquota_cmd' => $repquotaCmd, ':blocksize' => $blocksize, ':inode_block_ratio' => $inodeBlockRatio, ':externalID' => $externalID, ':reseller' => $ownerID));

                $rowCount = $query->rowCount();
                $loguseraction = '%add% %root% ' . $ip;

            } else if ($ui->st('action', 'post') == 'md') {

                if ($reseller_id == 0) {
                    $query = $sql->prepare("UPDATE `rserverdata` SET `config_log_time`=:configLogTime,`config_demo_time`=:configDemoTime,`config_ztmp_time`=:configZtmpTime,`config_bad_time`=:configBadTime,`config_user_id`=:configUserID,`config_ionice`=:configIonice,`config_binaries`=:configBinaries,`config_files`=:configFiles,`config_bad_files`=:configBadFiles,`active`=:active,`steamAccount`=AES_ENCRYPT(:steamAccount,:aeskey),`steamPassword`=AES_ENCRYPT(:steamPassword,:aeskey),`hyperthreading`=:hyperthreading,`cores`=:cores,`ip`=:ip,`altips`=:altips,`port`=AES_ENCRYPT(:port,:aeskey),`user`=AES_ENCRYPT(:user, :aeskey),`pass`=AES_ENCRYPT(:pass, :aeskey),`os`=:os,`bitversion`=:bit,`description`=:desc,`ftpport`=:ftpport,`publickey`=:publickey,`keyname`=:keyname,`maxslots`=:maxslots,`maxserver`=:maxserver,`updates`=:updates,`updateMinute`=:updateMinute,`ram`=:ram,`connect_ip_only`=:connect_ip_only,`install_paths`=:install_paths,`quota_active`=:quota_active,`quota_cmd`=:quota_cmd,`repquota_cmd`=:repquota_cmd,`blocksize`=:blocksize,`inode_block_ratio`=:inode_block_ratio,`externalID`=:externalID,`resellerid`=:reseller_id WHERE `id`=:id LIMIT 1");
                    $query->execute(array(':configLogTime' => $configLogTime, ':configDemoTime' => $configDemoTime, ':configZtmpTime' => $configZtmpTime, ':configBadTime' => $configBadTime, ':configUserID' => $configUserID, ':configIonice' => $configIonice, ':configBinaries' => $configBinaries, ':configFiles' => $configFiles, ':configBadFiles' => $configBadFiles, ':active' => $active, ':steamAccount' => $steamAccount, ':steamPassword' => $steamPassword, ':hyperthreading' => $hyperthreading, ':cores' => $cores, ':ip' => $ip, ':altips' => $altips, ':port' => $port, ':aeskey' => $aeskey, ':user' => $user, ':pass' => $pass, ':os' => $os, ':bit' => $bit, ':desc' => $desc, ':publickey' => $publickey, ':ftpport' => $ftpport, ':keyname' => $keyname, ':maxslots' => $maxslots, ':maxserver' => $maxserver, ':updates' => $updates, ':updateMinute' => $updateMinute, ':ram' => $ram, ':connect_ip_only' => $connectIpOnly, ':install_paths' => $installPaths, ':quota_active' => $quotaActive, ':quota_cmd' => $quotaCmd, ':repquota_cmd' => $repquotaCmd, ':blocksize' => $blocksize, ':inode_block_ratio' => $inodeBlockRatio, ':externalID' => $externalID, ':reseller_id' => $ownerID, ':id' => $id));
                } else {
                    $query = $sql->prepare("UPDATE `rserverdata` AS r SET `config_log_time`=:configLogTime,`config_demo_time`=:configDemoTime,`config_ztmp_time`=:configZtmpTime,`config_bad_time`=:configBadTime,`config_user_id`=:configUserID,`config_ionice`=:configIonice,`config_binaries`=:configBinaries,`config_files`=:configFiles,`config_bad_files`=:configBadFiles,`active`=:active,`steamAccount`=AES_ENCRYPT(:steamAccount,:aeskey),`steamPassword`=AES_ENCRYPT(:steamPassword,:aeskey),`hyperthreading`=:hyperthreading,`cores`=:cores,`ip`=:ip,`altips`=:altips,`port`=AES_ENCRYPT(:port,:aeskey),`user`=AES_ENCRYPT(:user, :aeskey),`pass`=AES_ENCRYPT(:pass, :aeskey),`os`=:os,`bitversion`=:bit,`description`=:desc,`ftpport`=:ftpport,`publickey`=:publickey,`keyname`=:keyname,`maxslots`=:maxslots,`maxserver`=:maxserver,`updates`=:updates,`updateMinute`=:updateMinute,`ram`=:ram,`connect_ip_only`=:connect_ip_only,`install_paths`=:install_paths,`quota_active`=:quota_active,`quota_cmd`=:quota_cmd,`repquota_cmd`=:repquota_cmd,`blocksize`=:blocksize,`inode_block_ratio`=:inode_block_ratio,`externalID`=:externalID,`resellerid`=:ownerID WHERE `id`=:id AND (`resellerid`=:reseller_id OR EXISTS (SELECT 1 FROM `userdata` WHERE `resellerid`=:reseller_id AND `id`=r.`resellerid`)) LIMIT 1");
                    $query->execute(array(':configLogTime' => $configLogTime, ':configDemoTime' => $configDemoTime, ':configZtmpTime' => $configZtmpTime, ':configBadTime' => $configBadTime, ':configUserID' => $configUserID, ':configIonice' => $configIonice, ':configBinaries' => $configBinaries, ':configFiles' => $configFiles, ':configBadFiles' => $configBadFiles, ':active' => $active, ':steamAccount' => $steamAccount, ':steamPassword' => $steamPassword, ':hyperthreading' => $hyperthreading, ':cores' => $cores, ':ip' => $ip, ':altips' => $altips, ':port' => $port, ':aeskey' => $aeskey, ':user' => $user, ':pass' => $pass, ':os' => $os, ':bit' => $bit, ':desc' => $desc, ':publickey' => $publickey, ':ftpport' => $ftpport, ':keyname' => $keyname, ':maxslots' => $maxslots, ':maxserver' => $maxserver, ':updates' => $updates, ':updateMinute' => $updateMinute, ':ram' => $ram, ':connect_ip_only' => $connectIpOnly, ':install_paths' => $installPaths, ':quota_active' => $quotaActive, ':quota_cmd' => $quotaCmd, ':repquota_cmd' => $repquotaCmd, ':blocksize' => $blocksize, ':inode_block_ratio' => $inodeBlockRatio, ':externalID' => $externalID, ':ownerID' => $ownerID, ':id' => $id, ':reseller_id' => $resellerLockupID));
                }

                $rowCount = $query->rowCount();
                $loguseraction = '%mod% %root% ' . $ip;
            }

            if (isset($rowCount) and $rowCount > 0) {
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;
            } else {
                $template_file = $spracheResponse->error_table;
            }
        } else {
            unset($header, $text);
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_roots_add.tpl' : 'admin_roots_md.tpl';
        }
    }

} else if ($ui->st('d', 'get') == 'dl' and $reseller_id == 0 and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->st('action', 'post')) {

        $query = $sql->prepare("SELECT `ip`,`description` FROM `rserverdata` WHERE `id`=? AND (`userID` IS NULL OR `userID` IN ('',0)) LIMIT 1");
        $query->execute(array($id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $desc = $row['description'];
            $ip = $row['ip'];
        }

        $template_file = ($query->rowCount() > 0) ? 'admin_roots_dl.tpl' : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'dl') {

        $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        $ip = $query->fetchColumn();

        $query = $sql->prepare("DELETE FROM `rserverdata` WHERE `id`=? AND (`userID` IS NULL OR `userID` IN ('',0)) LIMIT 1");
        $query->execute(array($id));

        if ($query->rowCount() > 0) {

            $query = $sql->prepare("DELETE m.* FROM `rservermasterg` m LEFT JOIN `rserverdata` r ON m.`serverid`=r.`id` WHERE r.`id` IS NULL");
            $query->execute();

            $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE `rootID`=?");
            $query2 = $sql->prepare("SELECT `id` FROM `serverlist` WHERE `switchID`=?");
            $query3 = $sql->prepare("DELETE FROM `addons_installed` WHERE `serverid`=?");

            $query->execute(array($id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $query2->execute(array($row['id']));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $query3->execute(array($row2['id']));
                }

                $query = $sql->prepare("DELETE FROM `serverlist` WHERE `switchID`=?");
                $query->execute(array($row['id']));

                $query = $sql->prepare("DELETE FROM `gserver_restarts` WHERE `switchID`=?");
                $query->execute(array($row['id']));
            }

            $query = $sql->prepare("DELETE FROM `gsswitch` WHERE `rootID`=?");
            $query->execute(array($id));

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% %root% ' . $ip;
            $insertlog->execute();

        } else {
            $template_file = $spracheResponse->error_table;
        }
    } else {
        $template_file = 'admin_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'ri' and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id', 10, 'get');

    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
    } else {
        $query = $sql->prepare("SELECT `ip` FROM `rserverdata` AS r WHERE `id`=? AND (`resellerid`=? OR EXISTS (SELECT 1 FROM `userdata` WHERE `resellerid`=? AND `id`=r.`resellerid`)) LIMIT 1");
        $query->execute(array($id, $resellerLockupID, $resellerLockupID));
    }

    $ip = $query->fetchColumn();

    if (strlen($ip) > 0) {

        if (!$ui->st('action', 'post')) {

            $table = array();

            if ($reseller_id == 0) {
                $query = $sql->prepare("SELECT `id`,`serverip`,`port` FROM `gsswitch` WHERE `rootID`=?");
                $query->execute(array($id));
            } else {
                $query = $sql->prepare("SELECT `id`,`serverip`,`port` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
                $query->execute(array($id, $resellerLockupID));
            }

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $table[$row['id']] = array('ip' => $row['serverip'], 'port' => $row['port']);
            }

            $template_file = 'admin_roots_ri.tpl';

        } else if ($ui->st('action', 'post') == 'ri' and $ui->id('serverID', 10, 'post')) {

            include(EASYWIDIR . '/stuff/methods/class_app.php');

            $appServer = new AppServer($id);

            $query = $sql->prepare("SELECT t.`shorten` FROM `serverlist` AS s INNER JOIN `servertypes` AS t  ON t.`id`=s.`servertype` WHERE s.`switchID`=?");
            $query2 = $sql->prepare("SELECT g.`serverip`,g.`port`,t.`shorten` FROM `gsswitch` AS g INNER JOIN `serverlist` AS s ON s.`id`=g.`serverid` INNER JOIN `servertypes` AS t ON t.`id`=s.`servertype` WHERE g.`id`=? LIMIT 1");

            foreach ((array) $ui->id('serverID', 10, 'post') as $serverID) {

                $removeTemplates = array();

                $appServer->getAppServerDetails($serverID);
                $appServer->userCud('add');
                $appServer->stopAppHard();

                $query->execute(array($serverID));

                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $removeTemplates[] = $row['shorten'];
                    $removeTemplates[] = $row['shorten'] . '-2';
                    $removeTemplates[] = $row['shorten'] . '-3';
                }

                if (count($removeTemplates) > 0) {
                    $appServer->removeApp($removeTemplates);
                }

                $query2->execute(array($serverID));
                while ($row = $query2->fetch(PDO::FETCH_ASSOC)) {

                    $started[] = $row['serverip'] . ':' . $row['port'];

                    $appServer->addApp(array(), true);
                }
            }

            if (count($started) > 0) {

                $template_file = $gsSprache->reinstall . ': ' . implode('<br>', $started);

                $appServer->execute();

                if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                    $template_file .= '<br><pre>' . implode("\r\n", $appServer->debug()) . '</pre>';
                }

            } else {
                $template_file = 'admin_404.tpl';
            }
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else {
        $template_file = 'admin_404.tpl';
    }

} else {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=appserver');

    $template_file = 'admin_roots_list.tpl';
}