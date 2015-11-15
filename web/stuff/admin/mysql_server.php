<?php

/**
 * File: mysql_server.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['mysql_settings'])) {
    header('Location: login.php');
    die;
}

include(EASYWIDIR . '/stuff/methods/class_mysql.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('mysql', $user_language, $reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
    $logsubuser = (isset($_SESSION['oldid'])) ? $_SESSION['oldid']: 0;
	$logreseller = 0;
}
// Define the ID variable which will be used at the form and SQLs
$id = $ui->id('id', 10, 'get');
$externalID = $ui->externalID('externalID', 'post');
$active = $ui->active('active', 'post');
$ip = $ui->ip4('ip', 'post');
$port = $ui->port('port', 'post');
$user = $ui->username('user', 255, 'post');
$password = $ui->password('password', 255, 'post');
$interface = $ui->url('interface', 'post');
$max_databases = ($ui->id('max_databases', 255, 'post')) ? $ui->id('max_databases', 255, 'post'): 100;
$max_queries_per_hour = ($ui->id('max_queries_per_hour', 255, 'post')) ? $ui->id('max_queries_per_hour', 255, 'post') : 0;
$max_updates_per_hour = ($ui->id('max_updates_per_hour', 255, 'post')) ? $ui->id('max_updates_per_hour', 255, 'post') : 0;
$max_connections_per_hour = ($ui->id('max_connections_per_hour', 255, 'post')) ? $ui->id('max_connections_per_hour', 255, 'post') : 0;
$max_userconnections_per_hour = ($ui->id('max_userconnections_per_hour', 255, 'post')) ? $ui->id('max_userconnections_per_hour', 255, 'post') : 0;
$connectIpOnly = ($ui->active('connectIpOnly', 'post')) ? $ui->active('connectIpOnly', 'post') : 'Y';
$externalAddress = ($ui->ip('externalAddress', 'post')) ? $ui->ip('externalAddress', 'post') : $ui->domain('externalAddress', 'post');

// At this point all variables are defined that can come from the user

$table = array();

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action', 4, 'post') and !token(true)) {

    unset($header, $text);

    $errors = array('token' => $spracheResponse->token);

} else {
    $errors = array();
}

if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    if (count($errors) == 0 and ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad')) {

        if (!$active) {
            $errors['active'] = $sprache->active;
        }

        if (!$ui->ip('ip', 'post')) {
            $errors['ip'] = 'IP';
        }

        if (!$port) {
            $errors['port'] = 'Port';
        }

        if (!$user) {
            $errors['user'] = $sprache->user;
        }

        if (!$password) {
            $errors['password'] = $sprache->password;
        }

        if (count($errors) == 0) {

            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `mysql_external_servers` (`externalID`,`active`,`ip`,`port`,`user`,`password`,`max_databases`,`interface`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour`,`connect_ip_only`,`external_address`,`resellerid`) VALUES (?,?,?,?,?,AES_ENCRYPT(?,?),?,?,?,?,?,?,?,?,?)");
                $query->execute(array($externalID, $active, $ip, $port, $user, $password, $aeskey, $max_databases, $interface, $max_queries_per_hour, $max_updates_per_hour, $max_connections_per_hour, $max_userconnections_per_hour, $connectIpOnly, $externalAddress, $resellerLockupID));
                $rowCount = $query->rowCount();

                $loguseraction = '%add% MySQL Server ' . $ip;

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                $query = $sql->prepare("UPDATE `mysql_external_servers` SET `externalID`=?,`active`=?,`ip`=?,`port`=?,`user`=?,`password`=AES_ENCRYPT(?,?),`max_databases`=?,`interface`=?,`max_queries_per_hour`=?,`max_updates_per_hour`=?,`max_connections_per_hour`=?,`max_userconnections_per_hour`=?,`connect_ip_only`=?,`external_address`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($externalID, $active, $ip, $port, $user, $password, $aeskey, $max_databases, $interface, $max_queries_per_hour, $max_updates_per_hour, $max_connections_per_hour, $max_userconnections_per_hour, $connectIpOnly, $externalAddress, $id, $resellerLockupID));
                $rowCount = $query->rowCount();

                $loguseraction = '%mod% MySQL Server ' . $ip;
            }

            if (isset($rowCount) and $rowCount > 0) {

                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }
        }
    }

    // An error occurred during validation
    // unset the redirect information and display the form again
    if (!$ui->smallletters('action', 2, 'post') or count($errors) != 0) {

        unset($header, $text);

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $template_file = 'admin_mysql_server_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT *,AES_DECRYPT(`password`,?) AS `decryptedpassword`FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($aeskey, $id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $active = $row['active'];
                $ip = $row['ip'];
                $externalID = $row['externalID'];
                $port = $row['port'];
                $user = $row['user'];
                $password = $row['decryptedpassword'];
                $max_databases = $row['max_databases'];
                $interface = $row['interface'];
                $max_queries_per_hour = $row['max_queries_per_hour'];
                $max_updates_per_hour = $row['max_updates_per_hour'];
                $max_connections_per_hour = $row['max_connections_per_hour'];
                $max_userconnections_per_hour = $row['max_userconnections_per_hour'];
                $connectIpOnly = $row['connect_ip_only'];
                $externalAddress = $row['external_address'];
            }

            // Check if database entry exists and if not display 404 page
            $template_file = ($query->rowCount()) ? 'admin_mysql_server_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }
    }

} else if ($ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT `ip`,`interface` FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $interface = $row['interface'];
        $ip = $row['ip'];
    }

    $serverFound = $query->rowCount();

    if ($ui->st('action', 'post') == 'dl' and count($errors) == 0 and $serverFound > 0) {

        $query = $sql->prepare("DELETE FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        $queryCount = $query->rowCount();

        $query = $sql->prepare("DELETE FROM `mysql_external_dbs` WHERE `sid`=? AND `resellerid`=?");
        $query->execute(array($id, $resellerLockupID));
        $queryCount += $query->rowCount();

        if ($queryCount > 0) {

            $loguseraction = '%del% MySQL Server ' . $ip;
            $insertlog->execute();

            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = $spracheResponse->error_table;
        }
    }

    // Nothing submitted yet or csfr error, display the delete form
    if (!$ui->st('action', 'post') or count($errors) != 0) {
        // Check if we could find an entry and if not display 404 page
        $template_file = ($serverFound > 0) ? 'admin_mysql_server_dl.tpl' : 'admin_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'ri' and $id) {

    $query = $sql->prepare("SELECT `ip`,AES_DECRYPT(`password`,?) AS `decryptedpassword2`,`port`,`user`,CASE WHEN `connect_ip_only`='Y' THEN `external_address` ELSE `ip` END AS `user_connect_ip` FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($aeskey, $id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $serverFound = true;

        $ip = $row['ip'];

        if ($ui->st('action', 'post') == 'ri' and count($errors) == 0) {

            $remotesql = new ExternalSQL ($row['ip'], $row['port'], $row['user'], $row['decryptedpassword2']);

            if ($remotesql->error == 'ok') {

                $reinstalledDBs = array();

                $query2 = $sql->prepare("SELECT *,AES_DECRYPT(`password`,?) AS `decryptedpassword` FROM `mysql_external_dbs` WHERE `id`=? AND `sid`=? LIMIT 1");

                foreach ((array) $ui->id('db', 10, 'post') as $dbID) {

                    if (isid($dbID, 10)) {
                        $query2->execute(array($aeskey, $dbID, $id));
                        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                            $remotesql->DelDB($row2['dbname']);
                            $remotesql->DelUser($row2['dbname']);

                            $mailData = array(
                                'userId' => $row2['uid'],
                                'name' => (strlen($row2['description']) > 0) ? $row2['description'] : $row2['dbname'],
                                'mailConnectInfo' => array(
                                    'ip' => $row['user_connect_ip'],
                                    'port' => $row['port']
                                )
                            );

                            $remotesql->AddDB($mailData, $row2['dbname'], $row2['decryptedpassword'], $row2['ips'], $row2['max_queries_per_hour'], $row2['max_connections_per_hour'], $row2['max_updates_per_hour'], $row2['max_userconnections_per_hour']);

                            $reinstalledDBs[] = $row2['dbname'];

                            $loguseraction = '%ri% MYSQL DB ' . $row2['dbname'] . ' (' . $row['ip'] . ')';
                            $insertlog->execute();
                        }
                    }
                }

                $template_file = ($query2->rowCount() > 0) ? $spracheResponse->reinstall_success . ': ' . implode(', ', $reinstalledDBs) : 'admin_404.tpl';

            } else {
                $template_file = $remotesql->error;
            }
        }

        if (!$ui->st('action', 'post') or count($errors) != 0) {

            $table = array();

            $query = $sql->prepare("SELECT `id`,`dbname` FROM `mysql_external_dbs` WHERE `sid`=? AND `resellerid`=?");
            $query->execute(array($id, $reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $table[$row['id']] = $row['dbname'];
            }

            $template_file = ($query->rowCount() > 0) ? 'admin_mysql_server_ri.tpl' : 'admin_404.tpl';
        }
    }

    if (!isset($serverFound)) {
        $template_file = 'admin_404.tpl';
    }

} else {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=mysqlserver');

    $template_file = 'admin_mysql_server_list.tpl';
}