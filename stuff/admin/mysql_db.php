<?php

/**
 * File: mysql_db.php.
 * Author: Ulrich Block
 * Date: 22.02.15
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['mysql'])) {
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
$rootID = $ui->id('rootID', 10, 'post');
$userID = $ui->id('userID', 10, 'post');
$externalID = $ui->externalID('externalID', 'post');
$active = $ui->active('active', 'post');
$manage_host_table = $ui->active('manage_host_table', 'post');
$description = $ui->description('description', 'post');
$password = ($ui->password('password', 255, 'post')) ? $ui->password('password', 255, 'post') : passwordgenerate(10);
$ips = $ui->ips('ips', 'post');
$max_queries_per_hour = ($ui->id('max_queries_per_hour', 255, 'post')) ? $ui->id('max_queries_per_hour', 255, 'post') : 0;
$max_updates_per_hour = ($ui->id('max_updates_per_hour', 255, 'post')) ? $ui->id('max_updates_per_hour', 255, 'post') : 0;
$max_connections_per_hour = ($ui->id('max_connections_per_hour', 255, 'post')) ? $ui->id('max_connections_per_hour', 255, 'post') : 0;
$max_userconnections_per_hour = ($ui->id('max_userconnections_per_hour', 255, 'post')) ? $ui->id('max_userconnections_per_hour', 255, 'post') : 0;

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

    // Add jQuery plugin chosen to the header
    $htmlExtraInformation['css'][] = '<link href="css/default/chosen/chosen.min.css" rel="stylesheet" type="text/css">';
    $htmlExtraInformation['js'][] = '<script src="js/default/plugins/chosen/chosen.jquery.min.js" type="text/javascript"></script>';

    if (count($errors) == 0 and ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad')) {

        if (!$active) {
            $errors['active'] = $sprache->active;
        }

        if ($ui->st('action', 'post') == 'ad') {

            if (!$userID) {

                $errors['userID'] = $sprache->user;

            } else {

                $query = $sql->prepare("SELECT 1 FROM `userdata` WHERE `id`=? AND `resellerid`=? AND `accounttype`='u' LIMIT 1");
                $query->execute(array($userID, $resellerLockupID));

                if ($query->rowCount() == 0) {
                    $errors['userID'] = $sprache->user;
                }
            }
        }

        if ($ui->st('action', 'post') == 'md') {

            $query = $sql->prepare("SELECT `sid`,`uid` FROM `mysql_external_dbs` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $rootID = $row['sid'];
                $userID = $row['uid'];
            }
        }

        if (!isid($rootID, 10)) {

            $errors['rootID'] = 'MySQL Server';

        } else {

            $query = $sql->prepare("SELECT `ip`,`port`,`user`,AES_DECRYPT(`password`,?) AS `decryptedpassword`,CASE WHEN `connect_ip_only`='Y' THEN `external_address` ELSE `ip` END AS `user_connect_ip` FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($aeskey, $rootID, $reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $mysqlServer) {
                $mysqlServerFount = true;
            }

            if (!isset($mysqlServerFount)) {
                $errors['rootID'] = 'MySQL Server';
            }
        }

        $rowCount = 0;

        if (count($errors) == 0 and isset($mysqlServer)) {

            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `mysql_external_dbs` (`externalID`,`active`,`sid`,`uid`,`description`,`password`,`ips`,`manage_host_table`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour`,`resellerid`) VALUES (?,?,?,?,?,AES_ENCRYPT(?,?),?,?,?,?,?,?,?)");
                $query->execute(array($externalID, $active, $rootID, $userID, $description, $password, $aeskey, $ips, $manage_host_table, $max_queries_per_hour, $max_updates_per_hour, $max_connections_per_hour, $max_userconnections_per_hour, $resellerLockupID));

                $rowCount = $query->rowCount();

                $id = $sql->lastInsertId();

                $dbName = 'sql' . $id;

                $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `dbname`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($dbName, $id, $resellerLockupID));

                $loguseraction = '%add% MySQL DB ' . $dbName . ' (' . $mysqlServer['ip'] .')';

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                $dbName = 'sql' . $id;

                $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `externalID`=?,`active`=?,`ips`=?,`manage_host_table`=?,`description`=?,`password`=AES_ENCRYPT(?,?),`max_queries_per_hour`=?,`max_updates_per_hour`=?,`max_connections_per_hour`=?,`max_userconnections_per_hour`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($externalID, $active, $ips, $manage_host_table, $description, $password, $aeskey, $max_queries_per_hour, $max_updates_per_hour, $max_connections_per_hour, $max_userconnections_per_hour, $id, $resellerLockupID));
                $rowCount = $query->rowCount();

                $loguseraction = '%mod% MySQL DB ' . $dbName . ' (' . $mysqlServer['ip'] .')';
            }

            $rowCount += customColumns('D', $id, 'save');

            if ($rowCount > 0) {

                $remotesql = new ExternalSQL($mysqlServer['ip'], $mysqlServer['port'], $mysqlServer['user'], $mysqlServer['decryptedpassword']);

                if ($remotesql->error == 'ok') {

                    if ($active == 'N') {
                        $password = passwordgenerate(10);
                    }

                    $dbReturn = '';

                    if ($ui->st('action', 'post') == 'ad') {

                        $mailData = array(
                            'userId' => $userID,
                            'name' => (strlen($description) > 0) ? $description : $dbName,
                            'mailConnectInfo' => array(
                                'ip' => $mysqlServer['user_connect_ip'],
                                'port' => $mysqlServer['port']
                            )
                        );

                        $dbReturn = $remotesql->AddDB($mailData, $dbName, $password, $ips, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour);
                    }

                    if ($ui->st('action', 'post') == 'md') {
                        $dbReturn = $remotesql->ModDB($dbName, $password, $ips, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour);
                    }

                    if ($dbReturn == 'ok') {
                        $insertlog->execute();
                        $template_file = $spracheResponse->table_add;
                    } else {
                        $template_file = $spracheResponse->error_table . "<br>" . $dbReturn;
                    }

                } else {
                    $template_file = $spracheResponse->error_table . "<br>" . $remotesql->error;
                }


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

            $table = getUserList($resellerLockupID);
            $table2 = array();

            $query = $sql->prepare("SELECT s.`id`,s.`ip`,s.`description`(s.`max_databases`/100)*COUNT(d.`id`) AS `usedpercent` FROM `mysql_external_servers` s LEFT JOIN `mysql_external_dbs` d ON s.`id`=d.`sid` WHERE s.`active`='Y' AND s.`resellerid`=? GROUP BY s.`id`,s.`ip` HAVING `usedpercent`<100 ORDER BY `usedpercent` ASC");
            $query->execute(array($reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $table2[$row['id']] = ($row['description'] != null and $row['description'] != '') ? $row['ip'] . ' ' . $row['description'] : $row['ip'];
            }

            $template_file = 'admin_mysql_db_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT e.*,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,s.`ip`,s.`description` AS `descriptionmserver`,u.`cname`,CONCAT(u.`vname`,' ',u.`name`) AS `full_name` FROM `mysql_external_dbs` AS e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`id`=? AND e.`resellerid`=? LIMIT 1");
            $query->execute(array($aeskey, $id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

	            $externalID = $row['externalID'];
	            $ip = $row['ip'];
	            $manage_host_table = $row['manage_host_table'];
	            $ips = $row['ips'];
	            $active = $row['active'];
	            $description = $row['description'];
	            $descriptionmserver = $row['descriptionmserver'];
	            $dbName = $row['dbname'];
	            $password = $row['decryptedpassword'];

	            $userName = trim($row['cname'] . ' ' . $row['full_name']);
	            $table2[$row['sid']] = ($row['descriptionmserver'] != null and $row['descriptionmserver'] != '') ? $row['ip'] . ' ' . $row['descriptionmserver'] : $row['ip'];
            }

            // Check if database entry exists and if not display 404 page
            $template_file =  ($query->rowCount() > 0) ? 'admin_mysql_db_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }
    }

} else if (($ui->st('d', 'get') == 'ri' or $ui->st('d', 'get') == 'dl') and $id) {

    $query = $sql->prepare("SELECT `dbname`,`description` FROM `mysql_external_dbs` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $dbName = $row['dbname'];
        $description = $row['description'];
    }

    $serverFound = $query->rowCount();

    if (($ui->st('action', 'post') == 'ri' or $ui->st('action', 'post') == 'dl') and count($errors) == 0 and $serverFound > 0) {

        $query = $sql->prepare("SELECT e.`uid`,e.`dbname`,e.`ips`,e.`max_queries_per_hour`,e.`max_connections_per_hour`,e.`max_updates_per_hour`,e.`max_userconnections_per_hour`,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,s.`ip`,AES_DECRYPT(s.`password`,?) AS `decryptedpassword2`,s.`port`,s.`user`,CASE WHEN s.`connect_ip_only`='Y' THEN s.`external_address` ELSE s.`ip` END AS `user_connect_ip` FROM `mysql_external_dbs` e INNER JOIN `mysql_external_servers` s ON e.`sid`=s.`id` WHERE e.`id`=? AND e.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $aeskey, $id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $remotesql = new ExternalSQL ($row['ip'], $row['port'], $row['user'], $row['decryptedpassword2']);

            if ($remotesql->error == 'ok') {

                $remotesql->DelUser($row['dbname']);
                $remotesql->DelDB($row['dbname']);

                if ($ui->st('action', 'post') == 'ri') {

                    $mailData = array(
                        'userId' => $row['uid'],
                        'name' => (strlen($description) > 0) ? $description : $row['dbname'],
                        'mailConnectInfo' => array(
                            'ip' => $row['user_connect_ip'],
                            'port' => $row['port']
                        )
                    );

                    $remotesql->AddDB($mailData, $row['dbname'], $row['decryptedpassword'], $row['ips'], $row['max_queries_per_hour'], $row['max_connections_per_hour'], $row['max_updates_per_hour'], $row['max_userconnections_per_hour']);

                    $loguseraction = '%ri% MYSQL DB ' . $row['dbname'] . ' (' . $row['ip'] . ')';

                    $template_file = $spracheResponse->reinstall_success;

                } else {

                    $query2 = $sql->prepare("DELETE FROM `mysql_external_dbs` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($id, $resellerLockupID));

                    customColumns('D', $id, 'del');

                    $loguseraction = '%del% MySQL DB ' . $row['dbname'] . ' (' . $row['ip'] .')';

                    $template_file = $spracheResponse->table_del;
                }

                $insertlog->execute();

            } else {
                $template_file = $remotesql->error;
            }
        }
    }

    // Nothing submitted yet or csfr error, display the delete form
    if (!$ui->st('action', 'post') or count($errors) != 0 and $serverFound > 0) {
        $template_file = ($ui->st('d', 'get') == 'ri') ? 'admin_mysql_db_ri.tpl' : 'admin_mysql_db_dl.tpl';
    }

    // Check if we could find an entry and if not display 404 page
    if (!isset($template_file)) {
        $template_file = 'admin_404.tpl';
    }

} else {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=mysqldb');

    $template_file = 'admin_mysql_db_list.tpl';
}
