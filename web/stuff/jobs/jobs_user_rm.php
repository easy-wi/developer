<?php

/**
 * File: jobs_user_rm.php.
 * Author: Ulrich Block
 * Date: 05.08.12
 * Time: 23:42
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

if (!function_exists('removeUser')) {
    function removeUser ($userID, $tables, $reseller = null) {

        global $sql;

        foreach ($tables as $table => $column) {
            if ($reseller == null) {
                $query = $sql->prepare("DELETE FROM `" . $table . "` WHERE `" . $column . "`=?");
                $query->execute(array($userID));
            } else {
                $query = $sql->prepare("DELETE FROM `" . $table . "` WHERE `" . $column . "`=? AND `" . $reseller['column'] . "`=?");
                $query->execute(array($userID, $reseller['value']));
            }
        }
    }
}

$query = $sql->prepare("SELECT * FROM `jobs` j WHERE `status`='4' AND `type`='us' AND `action` IN ('dl','md') AND NOT EXISTS (SELECT 1 FROM `jobs` WHERE `userID`=j.`userID` AND (`status`=1 OR `status` IS NULL) AND `type`!='us' LIMIT 1)");
$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $ok = true;

    if ($row['action'] == 'dl') {
        $query2 = $sql->prepare("SELECT `accounttype`,`resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query2->execute(array($row['affectedID']));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

            $query3 = $sql->prepare("UPDATE `rootsIP4` SET `ownerID`=0 WHERE `ownerID`=?");
            $query3->execute(array($row['affectedID']));

            if ($row2['accounttype'] == 'r') {

                if ($row2['resellerid'] == $row['affectedID']) {
                    $query3 = $sql->prepare("UPDATE `rootsIP4` SET `ownerID`=0,`resellerID`=0 WHERE `resellerID`=?");
                    $query3->execute(array($row['affectedID']));
                }

                removeUser($row['affectedID'], array(
                    'userdata' => 'id',
                    'userpermissions' => 'userid'
                    )
                );

                $tables = array(
                    'addons' => 'resellerid',
                    'addons_installed' => 'resellerid',
                    'addons_allowed' => 'reseller_id',
                    'gsswitch' => 'resellerid',
                    'rserverdata' => 'resellerid',
                    'rservermasterg' => 'resellerid',
                    'serverlist' => 'resellerid',
                    'servertypes' => 'resellerid',
                    'settings' => 'resellerid',
                    'tickets' => 'resellerid',
                    'ticket_topics' => 'resellerid',
                    'userdata' => 'resellerid',
                    'userpermissions' => 'resellerid',
                    'userlog' => 'resellerid',
                    'resellerdata' => 'resellerid',
                    'gserver_restarts' => 'resellerid',
                    'eac' => 'resellerid',
                    'imprints' => 'resellerid',
                    'lendedserver' => 'resellerid',
                    'lendsettings' => 'resellerid',
                    'lendstats' => 'resellerID',
                    'voice_server' => 'resellerid',
                    'voice_masterserver' => 'resellerid',
                    'translations' => 'resellerID',
                    'voice_server_stats' => 'resellerid',
                    'voice_stats_settings' => 'resellerid',
                    'mysql_external_servers' => 'resellerid',
                    'mysql_external_dbs' => 'resellerid',
                    'usergroups' => 'resellerid',
                    'api_ips' => 'resellerID',
                    'api_settings' => 'resellerID',
                    'voice_tsdns' => 'resellerid',
                    'voice_dns' => 'resellerID'
                );
                removeUser($row['affectedID'],$tables);

                if ($row2['resellerid'] == $row['affectedID']) {
                    removeUser($row['affectedID'], array(
                        'traffic_data' => 'userid',
                        'traffic_data_day' => 'userid'
                        )
                    );

                    removeUser($row['affectedID'], array(
                        'traffic_data' => 'resellerid',
                        'traffic_data_day' => 'resellerid'
                        )
                    );
                }
            }
        }

        customColumns('U', $row['affectedID'], 'del');

        $query2 = $sql->prepare("DELETE FROM `userdata` WHERE `id`=? LIMIT 1");
        $query2->execute(array($row['affectedID']));

        $command = $gsprache->del.' userID: ' . $row['affectedID'] . ' name:' . $row['name'];

    } else {

        $extraData = @json_decode($row['extraData']);

        if (is_object($extraData)) {

            $query2 = $sql->prepare("UPDATE `userdata` SET `active`=?,`jobPending`='N' WHERE `id`=? LIMIT 1");
            $query2->execute(array($extraData->newActive, $row['affectedID']));

            $command = $gsprache->mod.' userID: ' . $row['affectedID'] . ' name:' . $row['name'];

        } else {

            $ok = false;

            $command='Error: ' . $gsprache->mod . ' userID: ' . $row['affectedID'] . ' name:' . $row['name'];
        }
    }

    if ($ok == true) {
        $query2 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? LIMIT 1");
        $query2->execute(array($row['jobID']));
    }

    $theOutput->printGraph($command);
}

// following queries will clean up the database. In case we have a NULL value joining failed and the entry needs to be removed.
$sql->exec("DELETE p.* FROM `userpermissions` p LEFT JOIN `userdata` u ON p.`userid`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE g.* FROM `userdata_groups` g LEFT JOIN `userdata` u ON g.`userID`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE s.* FROM `userdata_substitutes` s LEFT JOIN `userdata` u ON s.`userID`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE o.* FROM `userdata_substitutes_servers` o LEFT JOIN `userdata_substitutes` s ON o.`sID`=s.`sID` WHERE s.`sID` IS NULL");
$sql->exec("DELETE s.* FROM `userdata_social_identities` s LEFT JOIN `userdata` u ON s.`userID`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE s.* FROM `userdata_social_identities_substitutes` s LEFT JOIN `userdata_substitutes` u ON s.`userID`=u.`sID` WHERE u.`sID` IS NULL");
$sql->exec("DELETE g.* FROM `gsswitch` g LEFT JOIN `userdata` u ON g.`userid`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE FROM `gsswitch` WHERE NOT EXISTS (SELECT 1 FROM `serverlist` WHERE `switchID`=`gsswitch`.`id`)");
$sql->exec("DELETE s.* FROM `serverlist` s LEFT JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE g.`id` IS NULL");
$sql->exec("DELETE a.* FROM `addons_installed` a LEFT JOIN `serverlist` s ON a.`serverid`=s.`id` WHERE s.`id` IS NULL");
$sql->exec("DELETE a.* FROM `addons_installed` a LEFT JOIN `userdata` u ON a.`userid`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE m.* FROM `rservermasterg` m LEFT JOIN `rserverdata` r ON m.`serverid`=r.`id` WHERE r.`id` IS NULL");
$sql->exec("DELETE s.* FROM `serverlist` s LEFT JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE g.`id` IS NULL");
$sql->exec("DELETE a.* FROM `addons` a LEFT JOIN `userdata` u ON a.`resellerid`=u.`id` WHERE a.`resellerid` IS NULL OR (a.`resellerid`!=0 AND u.`id` IS NULL)");
$sql->exec("DELETE a.* FROM `addons_installed` a LEFT JOIN `serverlist` s ON a.`serverid`=s.`id` LEFT JOIN `userdata` u ON a.`userid`=u.`id` LEFT JOIN `addons` t ON a.`addonid`=t.`id` WHERE s.`id` IS NULL OR u.`id` IS NULL");
$sql->exec("DELETE a.* FROM `addons_allowed` a LEFT JOIN `userdata` u ON a.`reseller_id`=u.`id` LEFT JOIN `addons` t ON a.`addon_id`=t.`id` WHERE a.`reseller_id` IS NULL OR (a.`reseller_id`!=0 AND u.`id` IS NULL) OR t.`id` IS NULL");
$sql->exec("DELETE d.* FROM `mysql_external_dbs` d LEFT JOIN `userdata` u ON d.`uid`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE v.* FROM `virtualcontainer` v LEFT JOIN `userdata` u ON v.`userid`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE v.* FROM `voice_dns` v LEFT JOIN `userdata` u ON v.`userID`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE v.* FROM `voice_server` v LEFT JOIN `userdata` u ON v.`userid`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE v.* FROM `voice_server_backup` v LEFT JOIN `userdata` u ON v.`uid`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE v.* FROM `webVhost` v LEFT JOIN `userdata` u ON v.`userID`=u.`id` WHERE u.`id` IS NULL");
$sql->exec("DELETE v.* FROM `webVhost` v LEFT JOIN `webMaster` m ON v.`webMasterID`=m.`webMasterID` WHERE m.`webMasterID` IS NULL");
$sql->exec("DELETE s.* FROM `easywi_statistics_current` s LEFT JOIN `userdata` u ON s.`userID`=u.`id` WHERE s.`userID`!=0 AND u.`id` IS NULL");
$sql->exec("DELETE s.* FROM `easywi_statistics` s LEFT JOIN `userdata` u ON s.`userID`=u.`id` WHERE s.`userID`!=0 AND u.`id` IS NULL");