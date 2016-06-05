<?php

/**
 * File: masterserver.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['masterServer'])) {
	header('Location: admin.php');
	die('No Access');
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_masterserver.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$sprache = getlanguagefile('roots',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller = 0;
}

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->st('d', 'get') == 'ad') {

    $id = $ui->id('id', 10, 'get');

    if ($ui->smallletters('action',2, 'post') == 'ad') {

        $rootServer = new masterServer($id, $aeskey);

        if ($ui->id('id', 10, 'post')) {

            $template_file = '';

            $query = $sql->prepare("SELECT `id` FROM `rservermasterg` WHERE `serverid`=? AND `servertypeid`=? AND `resellerid`=?");
            $query2 = $sql->prepare("SELECT * FROM `servertypes` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query3 = $sql->prepare("INSERT INTO rservermasterg (`serverid`,`servertypeid`,`installing`,`installstarted`,`resellerid`) VALUES (?,?,'Y',NOW(),?)");

            foreach ($ui->id('id', 10, 'post') as $masterID) {

                $query->execute(array($id, $masterID, $resellerLockupID));

                if ($query->rowCount() == 0) {

                    $query2->execute(array($masterID, $resellerLockupID));
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                        $template_file .= '<b>' . $row2['description'] . '</b> ' . $sprache->root_masterinstall . '<br>';
                        $loguseraction = '%add% %master% ' . $row2['shorten'];
                        $insertlog->execute();
                    }

                    $query3->execute(array($id, $masterID, $resellerLockupID));
                }

                $rootServer->collectData($masterID, true);
            }

            $rootServer->sshConnectAndExecute();

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<br>' . nl2br($rootServer->getCommands());
            }

        } else {
            $template_file = $sprache->error_root_noselect;
        }

    } else {

        $table = array();

        $query = $sql->prepare("SELECT `ip`,`os`,`description` FROM `rserverdata` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $ip = $row['ip'];
            $os = $row['os'];
            $description = $row['description'];
        }

        if (isset($ip) and isset($os)) {

            $query = $sql->prepare("SELECT `id`,`shorten`,`steamgame`,`description` FROM `servertypes` AS t WHERE `resellerid`=? AND (`os`='B' OR `os`=?) AND NOT EXISTS (SELECT 1 FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`serverid`=? AND s.`shorten`=t.`shorten`) ORDER BY `description`");
            $query->execute(array($resellerLockupID, $os, $id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $table[] = array('id' => $row['id'], 'shorten' => $row['shorten'], 'description' => $row['description']);
            }

            $template_file = 'admin_master_add.tpl';

        } else {
            $template_file = 'admin_404.tpl';
        }
    }

} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

    if ($ui->smallletters('action', 2, 'post') == 'dl') {

        $rootServer = new masterServer($ui->id('id', 10, 'get'), $aeskey);

        if ($ui->id('id', 10, 'post')) {

            $template_file = '';

            $query = $sql->prepare("SELECT s.`shorten` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`id`=? AND r.`resellerid`=? LIMIT 1");
            $query2 = $sql->prepare("DELETE FROM `rservermasterg` WHERE `id`=? AND `resellerid`=? LIMIT 1");

            foreach ($ui->id('id', 10, 'post') as $id) {

                $query->execute(array($id, $resellerLockupID));
                $shorten = $query->fetchColumn();

                $rootServer->masterRemove($shorten);

                $query2->execute(array($id, $resellerLockupID));

                $template_file .= $spracheResponse->table_del . ': ' . $shorten . '<br />';

                $loguseraction = '%del% %master% ' . $rootServer->sship . ' ' . $shorten;
                $insertlog->execute();
            }

            $rootServer->sshConnectAndExecute(false);

            $template_file .= $sprache->root_masterdel;

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<br>' . nl2br($rootServer->getCommands());
            }

        } else {
            $template_file = $sprache->error_root_noselect;
        }

    } else {

        $table = array();

        $id = $ui->id('id',19, 'get');

        $query = $sql->prepare("SELECT `ip`,`description` FROM `rserverdata` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $ip = $row['ip'];
            $description = $row['description'];
        }

        $query = $sql->prepare("SELECT r.`id`,s.`shorten`,s.`description` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`serverid`=? AND r.`resellerid`=? ORDER BY `description`");
        $query->execute(array($id,$resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $table[] = array('id' => $row['id'], 'shorten' => $row['shorten'], 'description' => $row['description']);
        }

        $template_file = (isset($ip) and strlen($ip) > 0) ? 'admin_master_dl.tpl' : 'Error: No such ID!';
 
    }

} else if ($ui->st('d', 'get') == 'md') {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=appmasterserver');

    $template_file = 'admin_master_list.tpl';

} else if ($ui->st('d', 'get') == 'ud' and $ui->st('action', 'post') == 'ud') {

    $ips = array();
    $ajaxStrings = array();

    if (is_object($ui->id('serverID', 10, 'post')) or is_array($ui->id('serverID', 10, 'post'))) {
        foreach($ui->id('serverID', 10, 'post') as $id) {
            $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $ips[$id] = $row['ip'];
            }
        }
    }

    if (count($ips) > 0) {

        $query = $sql->prepare("SELECT s.`id` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE s.`id`=? AND r.`serverid`=? AND r.`installing`='N' AND r.`resellerid`=?");

        $serverIdArray = (array) $ui->id('serverID', 10, 'post');

        foreach($serverIdArray as $id) {

            $ajaxStringIDs = array();

            $masterIdArray = (array) $ui->id('masterID', 10, 'post');

            foreach($masterIdArray as $masterID) {

                $query->execute(array($masterID, $id, $resellerLockupID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $ajaxStringIDs[] = $row['id'];
                }
            }

            if ($query->rowCount() > 0) {
                $ajaxStrings[$id] = $ajaxStringIDs;
            }
        }

        $template_file = 'admin_master_ud2.tpl';

    } else {
        $template_file = 'admin_404.tpl';
    }
} else {

    $appServer = array();
    $masterList = array();

    $query = $sql->prepare("SELECT `id`,`ip`,`description` FROM `rserverdata` WHERE `active`='Y' AND `resellerid`=?");
    $query->execute(array($resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $appServer[$row['id']] = array('ip' => $row['ip'], 'description' => $row['description']);
    }

    $query = $sql->prepare("SELECT `id`,`shorten`,`description` FROM `servertypes` s WHERE `resellerid`=? ORDER BY `description` ASC");
    $query2 = $sql->prepare("SELECT r.`id` FROM `rservermasterg` AS m INNER JOIN `rserverdata` AS r ON r.`id`=m.`serverid` WHERE m.`servertypeid`=?");

    $query->execute(array($resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $serverIDs = array();

        $query2->execute(array($row['id']));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
            $serverIDs[] = $row2['id'];
        }

        if (count($serverIDs) > 0) {
            $masterList[$row['id']] = array('description' => $row['description'], 'shorten' => $row['shorten'], 'serverIDs' => implode(',', $serverIDs));
        }
    }

    $template_file = 'admin_master_ud.tpl';
}