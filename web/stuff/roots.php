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
include(EASYWIDIR . '/stuff/ssh_exec.php');

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

if ($reseller_id != 0 and $admin_id != $reseller_id) {
    $reseller_id = $admin_id;
}

if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;

} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    $errors = array();

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
    $os = 'linux';
    $bit = $ui->id('bit', 2, 'post');
    $desc = $ui->description('desc', 'post');
    $maxslots = $ui->id('maxslots', 5, 'post');
    $maxserver = $ui->id('maxserver',4, 'post');
    $updates = $ui->id('updates',1, 'post');

    $active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
    $updateMinute = ($ui->id('updateMinute', 2, 'post')) ? $ui->id('updateMinute', 2, 'post') : 0;
    $publickey = ($ui->active('publickey', 'post')) ? $ui->active('publickey', 'post') : 'N';
    $ftpport = ($ui->port('ftpport', 'post')) ? $ui->port('ftpport', 'post') : 21;
    $port = ($ui->port('port', 'post')) ? $ui->port('port', 'post') : 22;

    if (!$ui->smallletters('action', 2, 'post')) {

        if ($ui->st('d', 'get') == 'ad' and $reseller_id == 0) {
            $template_file = 'admin_roots_add.tpl';

        } else if ($ui->st('d', 'get') == 'md' and $id) {
            $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `steamAcc`,AES_DECRYPT(`steamPassword`,:aeskey) AS `steamPwd` FROM `rserverdata` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
            $query2 = $sql->prepare("SELECT CONCAT(`cname`,' ',`vname`,' ',`name`)  FROM `userdata` WHERE `accounttype`='u' AND `resellerid`=? ORDER BY `id` DESC");
            $query->execute(array(':aeskey' => $aeskey, ':id' => $id, ':reseller_id' => $reseller_id));
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
                $port = $row['dport'];
                $user = $row['duser'];
                $pass = $row['dpass'];
                $ownerName = '';
                if ($row['userID'] == '' or $row['userID'] == null) {
                    $query2->execute(array($reseller_id));
                    $ownerName = trim($query2->fetchColumn());
                }
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
        if (!$ui->active('publickey', 'post')) {
            $errors['publickey'] = $sprache->keyuse;
        }
        if (!$ui->id('bit', 2, 'post')) {
            $errors['bit'] = $sprache->os_bit;
        }
        if (!$ui->active('hyperthreading', 'post')) {
            $errors['hyperthreading'] = 'Hyper Threading';
        }

        $ssh2Check = (count($errors) == 0) ? ssh_check($ui->ip('ip', 'post'), $ui->port('port', 'post'), $ui->username('user', 20, 'post'), $ui->active('publickey', 'post'), $ui->startparameter('keyname', 'post'), $ui->password('pass', 255, 'post')) : true;
        if ($ssh2Check !== true) {

            if ($ssh2Check == 'ipport') {
                $errors['ip'] = $sprache->haupt_ip;
                $errors['port'] = $sprache->ssh_port;

            } else {
                $errors['user'] = $sprache->ssh_user;
                $errors['publickey'] = $sprache->keyuse;

                if ($ui->active('publickey', 'post') == 'Y') {
                    $errors['keyname'] = $sprache->keyname;

                } else {
                    $errors['pass'] = $sprache->ssh_pass;
                }
            }
        }



        if (count($errors) == 0) {

            if ($ui->st('action', 'post') == 'ad' and $reseller_id == 0) {
                $query = $sql->prepare("INSERT INTO `rserverdata` (`active`,`steamAccount`,`steamPassword`,`hyperthreading`,`cores`,`ip`,`altips`,`port`,`user`,`pass`,`os`,`bitversion`,`description`,`ftpport`,`publickey`,`keyname`,`maxslots`,`maxserver`,`updates`,`updateMinute`,`externalID`,`resellerid`) VALUES (:active,AES_ENCRYPT(:steamAccount,:aeskey),AES_ENCRYPT(:steamPassword,:aeskey),:hyperthreading,:cores,:ip,:altips,AES_ENCRYPT(:port,:aeskey),AES_ENCRYPT(:user,:aeskey),AES_ENCRYPT(:pass,:aeskey),:os,:bit,:desc,:ftpport,:publickey,:keyname,:maxslots,:maxserver,:updates,:updateMinute,:externalID,:reseller)");
                $query->execute(array(':active' => $active, ':steamAccount' => $steamAccount, ':steamPassword' => $steamPassword, ':hyperthreading' => $hyperthreading, ':cores' => $cores, ':ip' => $ip, ':altips' => $altips, ':port' => $port, ':aeskey' => $aeskey, ':user' => $user, ':pass' => $pass, ':os' => $os, ':bit' => $bit, ':desc' => $desc, ':ftpport' => $ftpport, ':publickey' => $publickey, ':keyname' => $keyname, ':maxslots' => $maxslots, ':maxserver' => $maxserver, ':updates' => $updates, ':updateMinute' => $updateMinute, ':externalID' => $externalID, ':reseller' => $reseller_id));
                $rowCount = $query->rowCount();
                $loguseraction = '%add% %root% ' . $ip;

            } else if ($ui->st('action', 'post') == 'md') {
                $query = $sql->prepare("UPDATE `rserverdata` SET `active`=:active,`steamAccount`=AES_ENCRYPT(:steamAccount,:aeskey),`steamPassword`=AES_ENCRYPT(:steamPassword,:aeskey),`hyperthreading`=:hyperthreading,`cores`=:cores,`ip`=:ip,`altips`=:altips,`port`=AES_ENCRYPT(:port,:aeskey),`user`=AES_ENCRYPT(:user, :aeskey),`pass`=AES_ENCRYPT(:pass, :aeskey),`os`=:os,`bitversion`=:bit,`description`=:desc,`ftpport`=:ftpport,`publickey`=:publickey,`keyname`=:keyname,`maxslots`=:maxslots,`maxserver`=:maxserver,`updates`=:updates,`updateMinute`=:updateMinute,`externalID`=:externalID WHERE `id`=:id AND `resellerid`=:reseller_id");
                $query->execute(array(':active' => $active, ':steamAccount' => $steamAccount, ':steamPassword' => $steamPassword, ':hyperthreading' => $hyperthreading, ':cores' => $cores, ':ip' => $ip, ':altips' => $altips, ':port' => $port, ':aeskey' => $aeskey, ':user' => $user, ':pass' => $pass, ':os' => $os, ':bit' => $bit, ':desc' => $desc, ':publickey' => $publickey, ':ftpport' => $ftpport, ':keyname' => $keyname, ':maxslots' => $maxslots, ':maxserver' => $maxserver, ':updates' => $updates, ':updateMinute' => $updateMinute, ':externalID' => $externalID, ':id' => $id, ':reseller_id' => $reseller_id));
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

} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->st('action', 'post')) {
        $query = $sql->prepare("SELECT `ip`,`description` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? AND (`userID` IS NULL OR `userID` IN ('',0)) LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $desc = $row['description'];
            $ip = $row['ip'];
        }
        $template_file = ($query->rowCount() > 0) ? 'admin_roots_dl.tpl' : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'dl') {

        $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $ip = $query->fetchColumn();
        $query = $sql->prepare("DELETE FROM `rserverdata` WHERE `id`=? AND `resellerid`=? AND (`userID` IS NULL OR `userID` IN ('',0)) LIMIT 1");
        $query->execute(array($id,$reseller_id));

        if ($query->rowCount()>0) {
            $query = $sql->prepare("DELETE m.* FROM `rservermasterg` m LEFT JOIN `rserverdata` r ON m.`serverid`=r.`id` WHERE r.`id` IS NULL");
            $query->execute();
            $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
            $query2 = $sql->prepare("SELECT `id` FROM `serverlist` WHERE `switchID`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $query2->execute(array($row['id'],$reseller_id));
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    $query = $sql->prepare("DELETE FROM `addons_installed` WHERE `serverid`=? AND `resellerid`=?");
                    $query->execute(array($row2['id'],$reseller_id));
                }
                $query = $sql->prepare("DELETE FROM `serverlist` WHERE `switchID`=? AND `resellerid`=?");
                $query->execute(array($row['id'],$reseller_id));
                $query = $sql->prepare("DELETE FROM `gserver_restarts` WHERE `switchID`=? AND `resellerid`=?");
                $query->execute(array($row['id'],$reseller_id));
            }
            $query = $sql->prepare("DELETE FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
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

        $query = $sql->prepare("SELECT `id`,`serverip`,`port` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
        $query->execute(array($id, $reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip = $row['serverip'];
            $table[$row['id']] = array('ip' => $row['serverip'], 'port' => $row['port']);
        }

        $template_file = ($query->rowCount() > 0) ? 'admin_roots_ri.tpl' : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'ri' and $ui->id('serverID', 10, 'post')) {
        $cmds = array();
        $started = array();
        $serverIDs = (array) $ui->id('serverID', 10, 'post');

        $query = $sql->prepare("SELECT g.`id`,g.`userid`,g.`serverip`,g.`port`,g.`serverid`,g.`newlayout`,AES_DECRYPT(g.`ftppassword`,?) AS `dftp`,t.`shorten`,u.`cname`,AES_DECRYPT(d.`user`,?) AS `duser` FROM `gsswitch` AS g INNER JOIN `serverlist` AS s ON g.`serverid`=s.`id` INNER JOIN `servertypes` AS t ON s.`servertype`=t.`id` INNER JOIN `rserverdata` AS d ON g.`rootID`=d.`id` INNER JOIN `userdata` AS u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`rootID`=? AND g.`resellerid`=?");
        foreach ($serverIDs as $serverID) {
            $query->execute(array($aeskey, $aeskey, $serverID, $id, $reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $started[] = $row['serverip'] . ':' . $row['port'];
                $customer = ($row['newlayout'] == 'Y') ? $row['cname'] . '-' . $row['userid'] : $row['cname'];
                $cmds[] = './control.sh add ' . $customer . ' ' . $row['dftp'] . ' ' . $row['duser'] . ' ' . passwordgenerate(10);
                $cmds[] = 'sudo -u ' . $customer . ' ./control.sh reinstserver ' . $customer . ' 1_' . $row['shorten'] .' ' . $row['serverip'] . '_' . $row['port'];
            }
        }
        if (count($cmds) > 0) {
            include(EASYWIDIR . '/stuff/ssh_exec.php');
            ssh2_execute('gs', $id, $cmds);
            $template_file = $gsSprache->reinstall . ': ' . implode('<br>', $started);
        } else {
            $template_file = 'admin_404.tpl';
        }
        
    } else {
        $template_file = 'admin_404.tpl';
    }
} else {
    $table = array();
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'ar') {
        $orderby = 'u.`cname` ASC';
    } else if ($ui->st('o', 'get') == 'dr') {
        $orderby = 'u.`cname` DESC';
    } else if ($ui->st('o', 'get') == 'an') {
        $orderby = 'u.`name` ASC,u.`vname` ASC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = 'u.`name` DESC,u.`vname` DESC';
    } else if ($ui->st('o', 'get') == 'ap') {
        $orderby = 'r.`ip` ASC';
    } else if ($ui->st('o', 'get') == 'dp') {
        $orderby = 'r.`ip` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = 'r.`active` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = 'r.`active` DESC';
    } else if ($ui->st('o', 'get') == 'am') {
        $orderby = 'r.`maxserver` ASC';
    } else if ($ui->st('o', 'get') == 'dm') {
        $orderby = 'r.`maxserver` DESC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = 'r.`id` DESC';
    } else {
        $orderby = 'r.`id` ASC';
        $o = 'ai';
    }
    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `rserverdata` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    $colcount = $query->fetchColumn();
    if ($ui->id('p', 19, 'get') > $colcount) {
        $start = $colcount - $amount;
        if ($start < 0) {
            $start = 0;
        }
    } else {
        $start = (int) $ui->id('p', 19, 'get');
    }
    $query = $sql->prepare("SELECT r.*,u.`cname`,u.`name`,u.`vname` FROM `rserverdata` r LEFT JOIN `userdata` u ON r.`userID`=u.`id` WHERE r.`resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
    $query2 = $sql->prepare("SELECT g.`id`,CONCAT(g.`serverip`, ':',g.`port`) AS `address`,g.`active`,g.`stopped`,g.`queryName`,g.`queryNumplayers`,g.`slots`,t.`shorten` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`rootID`=? AND g.`resellerid`=?");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $used = 0;
        $available = 0;
        $i = 0;
        $gs = array();
        $id = $row['id'];
        $maxslots = $row['maxslots'];
        $maxserver = $row['maxserver'];
        if ($row['userID'] == 0 or $row['userID'] == null) {
            $deleteAllowed = true;
            $names = '';
        } else {
            $deleteAllowed = false;
            $names = trim($row['name'] . ' ' . $row['vname']);
        }
        $query2->execute(array($id,$reseller_id));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            if ($row2['active'] == 'N' or $row2['stopped'] == 'Y') {
                $gsStatus = 2;
            } else if ($row2['active'] == 'Y' and $row2['stopped'] == 'N' and $row2['queryName'] != 'OFFLINE') {
                $gsStatus = 1;
            } else {
                $gsStatus = 3;
            }
            $gs[] = array('id' => $row2['id'], 'address' => $row2['address'], 'shorten' => $row2['shorten'], 'name' => $row2['queryName'], 'status' => $gsStatus);
            $used+=$row2['queryNumplayers'];
            $available+=$row2['slots'];
            $i++;
        }
        if ($row['active'] == 'Y' and isset($downChecks) and $downChecks > $row['notified']) {
            $imgName = '16_ok';
            $imgAlt = 'Online';
        } else if ($row['active'] == 'Y' and isset($downChecks) and $downChecks <= $row['notified']) {
            $imgName = '16_error';
            $imgAlt = 'Crashed';
        } else {
            $imgName = '16_bad';
            $imgAlt = 'Inactive';
        }
        $table[] = array('id' => $id, 'names' => $names, 'deleteAllowed' => $deleteAllowed, 'img' => $imgName, 'alt' => $imgAlt, 'ip' => $row['ip'], 'active' => $row['active'], 'os' => $row['os'], 'bit' => $row['bitversion'], 'description' => $row['description'], 'used' => $used, 'max' => $available, 'maxslots' => $maxslots, 'maxserver' => $maxserver, 'installedserver' => $i, 'server' => $gs);
    }
    $next = $start + $amount;
    $vor = ($colcount > $next) ? $next : $start;
    $back = $start - $amount;
    $zur = ($back >= 0) ? $start - $amount : $start;
    $pageamount = ceil($colcount / $amount);
    $pages[] = '<a href="admin.php?w=ro&amp;d=md&amp;a=' . (!isset($amount)) ? 20 : $amount . ($start == 0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';
    $i = 2;
    while ($i <= $pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[] = ($start == $selectpage) ? '<a href="admin.php?w=ro&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=ro&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        $i++;
    }
    $pages = implode(', ', $pages);
    $template_file = 'admin_roots_list.tpl';
}