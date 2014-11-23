<?php

/**
 * File: functions_gs.php.
 * Author: Ulrich Block
 * Date: 26.01.14
 * Time: 10:46
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

if (!defined('EASYWIDIR')) {
    define('EASYWIDIR', '');
}

if (!function_exists('eacchange')) {

    function eacchange($what, $serverid, $rcon, $reseller_id) {

        global $sql;
        global $dbConnect;

        $subfolder = '';
        $parameter = '';

        $query = $sql->prepare("SELECT `active`,`cfgdir`,`type`,`mysql_server`,`mysql_port`,`mysql_db`,`mysql_table`,`mysql_user`,`mysql_password` FROM `eac` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $cfgdir = $row['cfgdir'];
            $active = $row['active'];
            $type = $row['type'];
            $mysql_server = $row['mysql_server'];
            $mysql_port = $row['mysql_port'];
            $mysql_db = $row['mysql_db'];
            $mysql_table = $row['mysql_table'];
            $mysql_user = $row['mysql_user'];
            $mysql_password = $row['mysql_password'];

            $query2 = $sql->prepare("SELECT g.`serverip`,g.`port`,s.`anticheat`,t.`shorten` FROM `gsswitch` g LEFT JOIN `serverlist` s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
            $query2->execute(array($serverid, $reseller_id));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

                $gsip = $row2['serverip'];
                $gsport = $row2['port'];
                $sqlParameter = 0;

                if ($row2['anticheat'] == 3) {
                    $parameter = '';
                } else if ($row2['anticheat'] == 4) {
                    $parameter = '-2';
                    $sqlParameter = 2;
                } else if ($row2['anticheat'] == 5) {
                    $parameter = '-1';
                    $sqlParameter = 1;
                } else if ($row2['anticheat'] == 6) {
                    $parameter = '-3';
                    $sqlParameter = 3;
                }

                $gameID = 0;

                if ($row2['shorten'] == 'cstrike' or $row2['shorten'] == 'czero') {

                    $subfolder = 'hl1';
                    $gameID = 1;

                } else if ($row2['shorten'] == 'css' or $row2['shorten'] == 'tf') {

                    $subfolder = 'hl2';
                    $gameID = 2;

                } else if ($row2['shorten'] == 'csgo') {

                    $subfolder = 'csgo';
                    $gameID = 4;

                }

                if ($type == 'M') {

                    $mysql_port = (port($mysql_port)) ? $mysql_port : 3306;

                    $eacSql = new PDO("mysql:host=${mysql_server};dbname=${mysql_db};port=${mysql_port}", $mysql_user, $mysql_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

                    if ($dbConnect['debug'] == 1) {
                        $eacSql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    }

                    $query3 = $eacSql->prepare("SELECT 1 FROM `" . $mysql_table . "` WHERE `IP`=? LIMIT 1");
                    $query3->execute(array($gsip . ':' . $gsport));
                    $entryExists = $query3->rowCount();

                    if ($entryExists > 0 and $what == 'change') {

                        $query3 = $eacSql->prepare("UPDATE `" . $mysql_table . "` SET `GAME`=?,`RCONPWD`=?,`FLAGS`=?,`EAC_ENABLED`=1 WHERE `IP`=? LIMIT 1");
                        $query3->execute(array($gameID, $rcon, $sqlParameter, $gsip . ':' . $gsport));

                    } else if ($entryExists == 0 and $what == 'change') {

                        $query3 = $eacSql->prepare("INSERT INTO `" . $mysql_table . "` (`GAME`,`IP`,`RCONPWD`,`FLAGS`,`EAC_ENABLED`) VALUES (?,?,?,?,1)");
                        $query3->execute(array($gameID, $gsip . ':' . $gsport, $rcon, $sqlParameter));

                    } else if ($entryExists > 0 and $what == 'remove') {

                        $query3 = $eacSql->prepare("DELETE FROM `" . $mysql_table . "` WHERE `IP`=?");
                        $query3->execute(array($gsip . ':' . $gsport));

                    }

                } else {

                    $file = $cfgdir . '/' . $subfolder . '/' . $gsip . '-' . $gsport;
                    $file = preg_replace('/\/\//', '/', $file);

                    if ($what == 'change') {
                        $ssh2cmd = 'echo "' . $gsip . ':' . $gsport . '-' . $rcon . $parameter . '" > '.$file;
                    } else if ($what == 'remove') {
                        $ssh2cmd = 'rm -f ' . $file;
                    }

                    if (isset($ssh2cmd) and $active == 'Y') {

                        if (!function_exists('ssh2_execute')) {
                            include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
                        }

                        if (isset($ssh2cmd)) {
                            ssh2_execute('eac', $reseller_id, $ssh2cmd);
                        }
                    }
                }
            }
        }
    }

    function normalizeName ($value) {

        // control characters
        $value = str_replace(array("\r", "\n"), '', $value);

        // COD colors
        $value = preg_replace('/\^[0-9]/i', '', $value);

        // Unreal Tournament Colors
        $value = preg_replace('/\x1B...|\^\d/', '', $value);

        // Minecraft Motd Colors
        $value = preg_replace('/\\[u]00A7[\w]/i', '', $value);

        // Minecraft standard colors
        $value = preg_replace('/§[0-9a-f]/i', '', $value);

        return $value;

    }

    function getAppMasterList ($resellerID) {

        $table = array();

        global $sql;

        $query = $sql->prepare("SELECT r.`id`,r.`ip`,r.`description`,(r.`maxserver` - COUNT(DISTINCT s.`id`)) AS `freeserver`,r.`active` AS `hostactive`,r.`resellerid` AS `resellerid` FROM `rserverdata` r LEFT JOIN `gsswitch` s ON s.`rootID` = r.`id` WHERE r.`active`='Y' AND r.`resellerid`=? GROUP BY r.`id` HAVING (`freeserver`>0 OR `freeserver` IS NULL) ORDER BY `freeserver` DESC");
        $query->execute(array($resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $table[$row['id']] = ($row['description'] != null and $row['description'] != '') ? $row['ip'] . ' ' . $row['description'] : $row['ip'];
        }

        return $table;
    }
}