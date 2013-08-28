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

$query=$sql->prepare("SELECT `hostID`,`resellerID` FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='my' GROUP BY `hostID`");
$query->execute();
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $query2=$sql->prepare("SELECT `ip`,`port`,`user`,AES_DECRYPT(`password`,?) AS `decryptedpassword` FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query2->execute(array($aeskey,$row['hostID'],$row['resellerID']));
    foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
        $ip=$row2['ip'];
        $port=$row2['port'];
        $user=$row2['user'];
        $pwd=$row2['decryptedpassword'];
    }
    $remotesql=new ExternalSQL ($ip,$port,$user,$pwd);
    if ($remotesql->error=='ok') {
        $query2=$sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='my' AND `hostID`=?");
        $query2->execute(array($row['hostID']));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $pselect=$sql->prepare("SELECT e.`active`,e.`dbname`,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,e.`ips`,e.`max_queries_per_hour`,e.`max_updates_per_hour`,e.`max_connections_per_hour`,e.`max_userconnections_per_hour`,s.`ip`,u.`cname` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`id`=? AND e.`resellerid`=? LIMIT 1");
            $pselect->execute(array($aeskey,$row2['affectedID'],$row2['resellerID']));
            foreach ($pselect->fetchall(PDO::FETCH_ASSOC) as $row) {
                $ip=$row['ip'];
                $ips=$row['ips'];
                $cname=$row['cname'];
                $dbname=$row['dbname'];
                $password=$row['decryptedpassword'];
                $extraData=@json_decode($row['extraData']);
                if (is_object($extraData->newActive) and isset($extraData->newActive) and $extraData->newActive=='N') {
                    $password=passwordgenerate(20);
                }
                $max_queries_per_hour=$row['max_queries_per_hour'];
                $max_updates_per_hour=$row['max_updates_per_hour'];
                $max_connections_per_hour=$row['max_connections_per_hour'];
                $max_userconnections_per_hour=$row['max_userconnections_per_hour'];
                if ($row2['action']=='dl') {
                    $command=$gsprache->del.' MYSQLDBID: '.$row2['affectedID'].' DBName: '.$row['dbname'];
                    $remotesql->DelDB($dbname);
                    $delete=$sql->prepare("DELETE FROM `mysql_external_dbs` WHERE `id`=? LIMIT 1");
                    $delete->execute(array($row2['affectedID']));
                    customColumns('M',$row2['affectedID'],'del');
                } else if ($row2['action']=='ad') {
                    $command=$gsprache->add.' MYSQLDBID: '.$row2['affectedID'].' DBName: '.$row['dbname'];
                    $remotesql->AddDB($dbname,$password,$ips,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour);
                } else {
                    $command=$gsprache->mod.' MYSQLDBID: '.$row2['affectedID'].' DBName: '.$row['dbname'];
                    $remotesql->ModDB($dbname,$password,$ips,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour);
                }
                $theOutput->printGraph($command);
            }
            $update=$sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? LIMIT 1");
            $update->execute(array($row2['jobID']));
        }
    } else {
        $update=$sql->prepare("UPDATE `jobs` SET `status`='1' WHERE `status` IS NULL AND `type`='my' AND `hostID`=?");
        $update->execute(array($row['hostID']));
    }
}