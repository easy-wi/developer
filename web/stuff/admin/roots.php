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
    die('No acces');
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
    $active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
    $updateMinute = ($ui->id('updateMinute', 2, 'post')) ? $ui->id('updateMinute', 2, 'post') : 0;
    $ftpport = ($ui->port('ftpport', 'post')) ? $ui->port('ftpport', 'post') : 21;
    $port = ($ui->port('port', 'post')) ? $ui->port('port', 'post') : 22;
    $maxserver = ($ui->id('maxserver',4, 'post')) ? $ui->id('maxserver',4, 'post') : 10;
    $maxslots = ($ui->id('maxslots', 5, 'post')) ? $ui->id('maxslots', 5, 'post') : 512;

    if (!$ui->smallletters('action', 2, 'post')) {

        if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `accounttype`='r' ORDER BY `id` DESC");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
            }
        } else {
            $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='r' ORDER BY `id` DESC");
            $query->execute(array($resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
            }
        }

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

            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
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
                $assignToReseller = ($ownerID > 0) ? 'Y' : 'N';
                $query2->execute(array($row['resellerid']));
                $ownerName = trim($query2->fetchColumn());
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

        $ssh2Check = (count($errors) == 0) ? ssh_check($ip, $port, $user, $publickey, $keyname, $pass) : true;

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
                $query = $sql->prepare("INSERT INTO `rserverdata` (`active`,`steamAccount`,`steamPassword`,`hyperthreading`,`cores`,`ip`,`altips`,`port`,`user`,`pass`,`os`,`bitversion`,`description`,`ftpport`,`publickey`,`keyname`,`maxslots`,`maxserver`,`updates`,`updateMinute`,`ram`,`externalID`,`resellerid`) VALUES (:active,AES_ENCRYPT(:steamAccount,:aeskey),AES_ENCRYPT(:steamPassword,:aeskey),:hyperthreading,:cores,:ip,:altips,AES_ENCRYPT(:port,:aeskey),AES_ENCRYPT(:user,:aeskey),AES_ENCRYPT(:pass,:aeskey),:os,:bit,:desc,:ftpport,:publickey,:keyname,:maxslots,:maxserver,:updates,:updateMinute,:ram,:externalID,:reseller)");
                $query->execute(array(':active' => $active, ':steamAccount' => $steamAccount, ':steamPassword' => $steamPassword, ':hyperthreading' => $hyperthreading, ':cores' => $cores, ':ip' => $ip, ':altips' => $altips, ':port' => $port, ':aeskey' => $aeskey, ':user' => $user, ':pass' => $pass, ':os' => $os, ':bit' => $bit, ':desc' => $desc, ':ftpport' => $ftpport, ':publickey' => $publickey, ':keyname' => $keyname, ':maxslots' => $maxslots, ':maxserver' => $maxserver, ':updates' => $updates, ':updateMinute' => $updateMinute, ':ram' => $ram, ':externalID' => $externalID, ':reseller' => $ownerID));
                $rowCount = $query->rowCount();
                $loguseraction = '%add% %root% ' . $ip;

            } else if ($ui->st('action', 'post') == 'md') {

                if ($reseller_id == 0) {
                    $query = $sql->prepare("UPDATE `rserverdata` SET `active`=:active,`steamAccount`=AES_ENCRYPT(:steamAccount,:aeskey),`steamPassword`=AES_ENCRYPT(:steamPassword,:aeskey),`hyperthreading`=:hyperthreading,`cores`=:cores,`ip`=:ip,`altips`=:altips,`port`=AES_ENCRYPT(:port,:aeskey),`user`=AES_ENCRYPT(:user, :aeskey),`pass`=AES_ENCRYPT(:pass, :aeskey),`os`=:os,`bitversion`=:bit,`description`=:desc,`ftpport`=:ftpport,`publickey`=:publickey,`keyname`=:keyname,`maxslots`=:maxslots,`maxserver`=:maxserver,`updates`=:updates,`updateMinute`=:updateMinute,`ram`=:ram,`externalID`=:externalID,`resellerid`=:reseller_id WHERE `id`=:id LIMIT 1");
                    $query->execute(array(':active' => $active, ':steamAccount' => $steamAccount, ':steamPassword' => $steamPassword, ':hyperthreading' => $hyperthreading, ':cores' => $cores, ':ip' => $ip, ':altips' => $altips, ':port' => $port, ':aeskey' => $aeskey, ':user' => $user, ':pass' => $pass, ':os' => $os, ':bit' => $bit, ':desc' => $desc, ':publickey' => $publickey, ':ftpport' => $ftpport, ':keyname' => $keyname, ':maxslots' => $maxslots, ':maxserver' => $maxserver, ':updates' => $updates, ':updateMinute' => $updateMinute, ':ram' => $ram, ':externalID' => $externalID, ':reseller_id' => $ownerID, ':id' => $id));
                } else {
                    $query = $sql->prepare("UPDATE `rserverdata` AS r SET `active`=:active,`steamAccount`=AES_ENCRYPT(:steamAccount,:aeskey),`steamPassword`=AES_ENCRYPT(:steamPassword,:aeskey),`hyperthreading`=:hyperthreading,`cores`=:cores,`ip`=:ip,`altips`=:altips,`port`=AES_ENCRYPT(:port,:aeskey),`user`=AES_ENCRYPT(:user, :aeskey),`pass`=AES_ENCRYPT(:pass, :aeskey),`os`=:os,`bitversion`=:bit,`description`=:desc,`ftpport`=:ftpport,`publickey`=:publickey,`keyname`=:keyname,`maxslots`=:maxslots,`maxserver`=:maxserver,`updates`=:updates,`updateMinute`=:updateMinute,`ram`=:ram,`externalID`=:externalID,`resellerid`=:ownerID WHERE `id`=:id AND (`resellerid`=:reseller_id OR EXISTS (SELECT 1 FROM `userdata` WHERE `resellerid`=:reseller_id AND `id`=r.`resellerid`)) LIMIT 1");
                    $query->execute(array(':active' => $active, ':steamAccount' => $steamAccount, ':steamPassword' => $steamPassword, ':hyperthreading' => $hyperthreading, ':cores' => $cores, ':ip' => $ip, ':altips' => $altips, ':port' => $port, ':aeskey' => $aeskey, ':user' => $user, ':pass' => $pass, ':os' => $os, ':bit' => $bit, ':desc' => $desc, ':publickey' => $publickey, ':ftpport' => $ftpport, ':keyname' => $keyname, ':maxslots' => $maxslots, ':maxserver' => $maxserver, ':updates' => $updates, ':updateMinute' => $updateMinute, ':ram' => $ram, ':externalID' => $externalID, ':ownerID' => $ownerID, ':id' => $id, ':reseller_id' => $resellerLockupID));
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
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
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
            $query->execute(array($id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $query2->execute(array($row['id']));
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    $query = $sql->prepare("DELETE FROM `addons_installed` WHERE `serverid`=?");
                    $query->execute(array($row2['id']));
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

    if (!$ui->st('action', 'post')) {

        $table = array();

        if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT `id`,`serverip`,`port` FROM `gsswitch` WHERE `rootID`=?");
            $query->execute(array($id));
        } else {
            $query = $sql->prepare("SELECT `id`,`serverip`,`port` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
            $query->execute(array($id, $resellerLockupID));
        }

        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip = $row['serverip'];
            $table[$row['id']] = array('ip' => $row['serverip'], 'port' => $row['port']);
        }

        $template_file = ($query->rowCount() > 0) ? 'admin_roots_ri.tpl' : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'ri' and $ui->id('serverID', 10, 'post')) {

        $cmds = array();
        $started = array();
        $serverIDs = (array) $ui->id('serverID', 10, 'post');

        if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT g.`id`,g.`userid`,g.`serverip`,g.`port`,g.`serverid`,g.`newlayout`,AES_DECRYPT(g.`ftppassword`,?) AS `dftp`,t.`shorten`,u.`cname`,AES_DECRYPT(d.`user`,?) AS `duser` FROM `gsswitch` AS g INNER JOIN `serverlist` AS s ON g.`serverid`=s.`id` INNER JOIN `servertypes` AS t ON s.`servertype`=t.`id` INNER JOIN `rserverdata` AS d ON g.`rootID`=d.`id` INNER JOIN `userdata` AS u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`rootID`=?");
        } else {
            $query = $sql->prepare("SELECT g.`id`,g.`userid`,g.`serverip`,g.`port`,g.`serverid`,g.`newlayout`,AES_DECRYPT(g.`ftppassword`,?) AS `dftp`,t.`shorten`,u.`cname`,AES_DECRYPT(d.`user`,?) AS `duser` FROM `gsswitch` AS g INNER JOIN `serverlist` AS s ON g.`serverid`=s.`id` INNER JOIN `servertypes` AS t ON s.`servertype`=t.`id` INNER JOIN `rserverdata` AS d ON g.`rootID`=d.`id` INNER JOIN `userdata` AS u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`rootID`=? AND g.`resellerid`=?");
        }

        foreach ($serverIDs as $serverID) {

            if ($reseller_id == 0) {
                $query->execute(array($aeskey, $aeskey, $serverID, $id));
            } else {
                $query->execute(array($aeskey, $aeskey, $serverID, $id, $resellerLockupID));
            }

            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $started[] = $row['serverip'] . ':' . $row['port'];
                $customer = ($row['newlayout'] == 'Y') ? $row['cname'] . '-' . $row['id'] : $row['cname'];
                $cmds[] = './control.sh add ' . $customer . ' ' . $row['dftp'] . ' ' . $row['duser'] . ' ' . passwordgenerate(10);
                $cmds[] = 'sudo -u ' . $customer . ' ./control.sh reinstserver ' . $customer . ' 1_' . $row['shorten'] .' ' . $row['serverip'] . '_' . $row['port'];
            }
        }

        if (count($cmds) > 0) {

            include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

            $return = ssh2_execute('gs', $id, $cmds);

            $template_file = $gsSprache->reinstall . ': ' . implode('<br>', $started);

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<br>' . $return;
                $template_file .= '<br>' . implode('<br>', $cmds);
            }

        } else {
            $template_file = 'admin_404.tpl';
        }
        
    } else {
        $template_file = 'admin_404.tpl';
    }

} else {

    configureDateTables('-1, -2, -3', '1, "asc"', 'ajax.php?w=datatable&d=appserver');

    $template_file = 'admin_roots_list.tpl';
}