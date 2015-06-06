<?php

/**
 * File: userpanel_mysql.php.
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

if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['mysql'])) {
    header('Location: userpanel.php');
    die;
}

include(EASYWIDIR . '/stuff/methods/class_mysql.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('mysql', $user_language, $reseller_id);
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

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['db']))) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->smallletters('action',2, 'post')) {

        #https://github.com/easy-wi/developer/issues/42 column description added
        $query = $sql->prepare("SELECT e.`dbname`,e.`description`,e.`manage_host_table`,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,e.`ips`,s.`port`,s.`interface`,u.`cname`,CASE WHEN s.`connect_ip_only`='Y' THEN s.`external_address` ELSE s.`ip` END AS `address` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`id`=? AND e.`active`='Y' AND s.`active` AND e.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $id, $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $address = $row['address'];
            $manage_host_table = $row['manage_host_table'];
            $ips = $row['ips'];
            $port = $row['port'];
            $interface = trim($row['interface']);
            $dbname = $row['dbname'];
            $cname = $row['cname'];
            $description = trim($row['description']);
            $password = $row['decryptedpassword'];
        }

        $queryCount = $query->rowCount();

        if ($queryCount > 0 and $ui->st('d', 'get') == 'ri') {
            $template_file = 'userpanel_mysql_db_ri.tpl';
        } else if ($queryCount > 0 and $ui->st('d', 'get') != 'ri') {
            $template_file = 'userpanel_mysql_db_md.tpl';
        } else {
            $template_file = 'userpanel_404.tpl';
        }

    } else if ($ui->smallletters('action', 2, 'post') == 'ri' and $ui->st('d', 'get') == 'ri'){

        $query = $sql->prepare("SELECT e.`dbname`,e.`description`,e.`ips`,e.`max_queries_per_hour`,e.`max_connections_per_hour`,e.`max_updates_per_hour`,e.`max_userconnections_per_hour`,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,s.`ip`,AES_DECRYPT(s.`password`,?) AS `decryptedpassword2`,s.`port`,s.`user`,CASE WHEN s.`connect_ip_only`='Y' THEN s.`external_address` ELSE s.`ip` END AS `address` FROM `mysql_external_dbs` e INNER JOIN `mysql_external_servers` s ON e.`sid`=s.`id` WHERE e.`id`=? AND e.`active`='Y' AND s.`active`='Y' AND e.`uid`=? AND e.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $aeskey, $id, $user_id, $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $remotesql = new ExternalSQL ($row['ip'], $row['port'], $row['user'], $row['decryptedpassword2']);

            if ($remotesql->error == 'ok') {

                $remotesql->DelDB($row['dbname']);
                $remotesql->DelUser($row['dbname']);

                $mailData = array(
                    'userId' => $user_id,
                    'name' => (strlen($row['description']) > 0) ? $row['description'] : $row['dbname'],
                    'mailConnectInfo' => array(
                        'ip' => $row['address'],
                        'port' => $row['port']
                    )
                );

                $remotesql->AddDB($mailData, $row['dbname'], $row['decryptedpassword'], $row['ips'], $row['max_queries_per_hour'], $row['max_connections_per_hour'], $row['max_updates_per_hour'], $row['max_userconnections_per_hour']);

                $loguseraction = '%ri% MYSQL DB ' . $row['dbname'] . ' (' . $row['address'] . ')';
                $insertlog->execute();

                $template_file = $spracheResponse->reinstall_success;

            } else {
                $template_file = $remotesql->error;
            }
        }

        if (!isset($remotesql)) {
            $template_file = 'userpanel_404.tpl';
        }

    } else if ($ui->smallletters('action', 2, 'post') == 'md' and $ui->st('d', 'get') != 'ri'){

        if ($ui->password('password', 255, 'post')) {

            $query = $sql->prepare("SELECT e.`dbname`,e.`manage_host_table`,e.`ips`,e.`max_queries_per_hour`,e.`max_connections_per_hour`,e.`max_updates_per_hour`,e.`max_userconnections_per_hour`,s.`ip`,AES_DECRYPT(s.`password`,?) AS `decryptedpassword2`,s.`port`,s.`user`,CASE WHEN s.`connect_ip_only`='Y' THEN s.`external_address` ELSE s.`ip` END AS `address` FROM `mysql_external_dbs` e INNER JOIN `mysql_external_servers` s ON e.`sid`=s.`id` WHERE e.`id`=? AND e.`active`='Y' AND s.`active`='Y' AND e.`uid`=? AND e.`resellerid`=? LIMIT 1");
            $query->execute(array($aeskey, $id, $user_id, $reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $ips = ($row['manage_host_table'] == 'Y') ? $ui->ips('ips', 'post') : $row['ips'];

                $remotesql = new ExternalSQL ($row['ip'], $row['port'], $row['user'], $row['decryptedpassword2']);

                if ($remotesql->error == 'ok') {

                    #https://github.com/easy-wi/developer/issues/42 column description added
                    $query2 = $sql->prepare("UPDATE `mysql_external_dbs` SET `description`=?,`password`=AES_ENCRYPT(?,?),`ips`=? WHERE `id`=? AND `uid`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array(trim($ui->startparameter('description', 'post')), $ui->password('password', 255, 'post'), $aeskey, $ips, $id, $user_id, $reseller_id));

                    if ($query2->rowCount() > 0) {

                        $remotesql->ModDB($row['dbname'], $ui->password('password', 255, 'post'), $ips, $row['max_queries_per_hour'], $row['max_connections_per_hour'], $row['max_updates_per_hour'], $row['max_userconnections_per_hour']);

                        $loguseraction = '%mod% MYSQL DB ' . $row['dbname'] . ' (' . $row['address'] . ')';
                        $insertlog->execute();

                        $template_file = $spracheResponse->table_add;

                    } else {
                        $template_file = $spracheResponse->error_table;
                    }

                } else {
                    $template_file = $remotesql->error;
                }
            }

            if (!isset($remotesql)) {
                $template_file = 'userpanel_404.tpl';
            }

        } else {
            $template_file = 'Error: ' . $sprache->password;
        }

    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else {

    $table = array();

    $query = $sql->prepare("SELECT e.`id`,e.`dbname`,e.`description`,e.`dbSize`,s.`port`,s.`interface`,CASE WHEN s.`connect_ip_only`='Y' THEN s.`external_address` ELSE s.`ip` END AS `address` FROM `mysql_external_dbs` e INNER JOIN `mysql_external_servers` s ON e.`sid`=s.`id` WHERE e.`active`='Y' AND s.`active`='Y' AND e.`uid`=? AND e.`resellerid`=?");
    $query->execute(array($user_id, $reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($_SESSION['sID']) or in_array($row['id'], $substituteAccess['db'])) {
            $table[] = array('id' => $row['id'], 'dbname' => $row['dbname'], 'dbSize' => $row['dbSize'], 'address' => $row['address'], 'port' => $row['port'], 'description' => trim($row['description']), 'interface' => $row['interface']);
        }
    }

    $template_file = 'userpanel_mysql_db_list.tpl';

}