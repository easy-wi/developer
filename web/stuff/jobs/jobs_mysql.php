<?php

/**
 * File: jobs_mysql.php.
 * Author: Ulrich Block
 * Date: 27.05.12
 * Time: 14:43
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

$query = $sql->prepare("SELECT `hostID`,`resellerID` FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='my' GROUP BY `hostID`");
$query2 = $sql->prepare("SELECT `ip`,`port`,`user`,AES_DECRYPT(`password`,?) AS `decryptedpassword`,CASE WHEN `connect_ip_only`='Y' THEN `external_address` ELSE `ip` END AS `user_connect_ip` FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
$query3 = $sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='my' AND `hostID`=?");
$query4 = $sql->prepare("SELECT e.`uid`,e.`active`,e.`dbname`,e.`description`,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,e.`ips`,e.`max_queries_per_hour`,e.`max_updates_per_hour`,e.`max_connections_per_hour`,e.`max_userconnections_per_hour`,s.`ip`,u.`cname` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`id`=? AND e.`resellerid`=? LIMIT 1");
$query5 = $sql->prepare("DELETE FROM `mysql_external_dbs` WHERE `id`=? LIMIT 1");
$query6 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? LIMIT 1");
$query7 = $sql->prepare("UPDATE `jobs` SET `status`='1' WHERE (`status` IS NULL OR `status`='1') AND `type`='my' AND `hostID`=?");
$query8 = $sql->prepare("UPDATE `jobs` SET `action`='dl' WHERE `hostID`=? AND `type`='my'");

$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    unset($remotesql);

    $query2->execute(array($aeskey, $row['hostID'], $row['resellerID']));
    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

        $remotesql = new ExternalSQL ($row2['ip'], $row2['port'], $row2['user'], $row2['decryptedpassword']);

        $mailData = array(
            'mailConnectInfo' => array(
                'ip' => $row2['user_connect_ip'],
                'port' => $row2['port']
            )
        );
    }

    if (isset($remotesql) or $remotesql->error == 'ok') {

        $query3->execute(array($row['hostID']));
        while ($row2 = $query3->fetch(PDO::FETCH_ASSOC)) {

            $query4->execute(array($aeskey, $row2['affectedID'], $row2['resellerID']));
            while ($row4 = $query4->fetch(PDO::FETCH_ASSOC)) {

                $ip = $row4['ip'];
                $ips = $row4['ips'];
                $cname = $row4['cname'];
                $dbname = $row4['dbname'];
                $password = $row4['decryptedpassword'];
                $max_queries_per_hour = $row4['max_queries_per_hour'];
                $max_updates_per_hour = $row4['max_updates_per_hour'];
                $max_connections_per_hour = $row4['max_connections_per_hour'];
                $max_userconnections_per_hour = $row4['max_userconnections_per_hour'];

                $extraData = @json_decode($row4['extraData']);
                if (is_object($extraData) and $extraData->newActive == 'N') {
                    $password = passwordgenerate(20);
                }

                if ($row2['action'] == 'dl') {

                    $command = $gsprache->del . ' MySQLDBID: ' . $row2['affectedID'] . ' DBName: ' . $row4['dbname'];

                    $remotesql->DelDB($dbname);

                    $query5->execute(array($row2['affectedID']));

                    customColumns('M', $row2['affectedID'], 'del');

                } else if ($row2['action'] == 'ad') {

                    $command = $gsprache->add.' MySQLDBID: '.$row2['affectedID'].' DBName: '.$row4['dbname'];

                    $mailData['userId'] = $row4['uid'];
                    $mailData['name'] = (strlen($row4['description']) > 0) ? $row4['description'] : $row4['dbname'];

                    $remotesql->AddDB($mailData, $dbname,$password,$ips,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour);

                } else {

                    $command = $gsprache->mod.' MySQLDBID: '.$row2['affectedID'].' DBName: '.$row4['dbname'];

                    $remotesql->ModDB($dbname,$password,$ips,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour);

                }

                $theOutput->printGraph($command);

            }

            $query6->execute(array($row2['jobID']));
        }

    } else if (isset($remotesql)) {

        $query7->execute(array($row['hostID']));

    } else {

        $query8->execute(array($row['hostID']));

        $query3->execute(array($row['hostID']));
        while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
            if ($row2['action'] == 'dl') {

                $query5->execute(array($row2['affectedID']));

                customColumns('M', $row2['affectedID'], 'del');
            }
        }
    }
}