<?php

/**
 * File: mysql_master_usage.php.
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

if (!defined('AJAXINCLUDED')) {
    die('Do not access directly!');
}

$sprache = getlanguagefile('mysql', $user_language, $reseller_id);

if ($ui->id('id', 10, 'get')) {

    $max_databases = 0;
    $max_queries_per_hour = 0;
    $max_updates_per_hour = 0;
    $max_connections_per_hour = 0;
    $max_userconnections_per_hour = 0;
    $installed = 0;
    $max = 0;

    $query = $sql->prepare("SELECT `id`,`ip`,`max_databases`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour`,(SELECT COUNT(1) AS `amount` FROM `mysql_external_dbs` d WHERE d.`sid`=s.`id`) AS `installed` FROM `mysql_external_servers` s WHERE `id`=? AND `active`='Y' AND `resellerid`=? LIMIT 1");
    $query->execute(array($ui->id('id', 10, 'get'), $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $max_databases = $row['max_databases'];
        $max_queries_per_hour = $row['max_queries_per_hour'];
        $max_updates_per_hour = $row['max_updates_per_hour'];
        $max_connections_per_hour = $row['max_connections_per_hour'];
        $max_userconnections_per_hour = $row['max_userconnections_per_hour'];
        $installed = (int) $row['installed'];
        $max = (int) $row['max_databases'];
    }

    if ($ui->id('serverID', 10, 'get') and $query->rowCount() > 0) {
        $query = $sql->prepare("SELECT `max_databases`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour` FROM `mysql_external_dbs` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('serverID', 10, 'get'), $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $max_queries_per_hour = $row['max_queries_per_hour'];
            $max_updates_per_hour = $row['max_updates_per_hour'];
            $max_connections_per_hour = $row['max_connections_per_hour'];
            $max_userconnections_per_hour = $row['max_userconnections_per_hour'];
        }
    }
}

require_once IncludeTemplate($template_to_use, 'ajax_admin_mysql_server_usage.tpl', 'ajax');