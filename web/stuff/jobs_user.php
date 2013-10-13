<?php
/**
 * File: jobs_user.php.
 * Author: Ulrich Block
 * Date: 27.05.12
 * Time: 14:54
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

$query = $sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='us'");
$query->execute();
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    if ($row['action'] == 'dl') {
        $command = $gsprache->del.' cleanup userID: '.$row['affectedID'].' name:'.$row['name'];
    } else if ($row['action'] == 'ad') {
        $command = $gsprache->add.' userID: '.$row['affectedID'].' name:'.$row['name'];
    } else {
        $command = $gsprache->mod.' userID: '.$row['affectedID'].' name:'.$row['name'];
    }
    $query2 = $sql->prepare("SELECT `id`,`rootID`,`serverip`,`port`,`resellerid` FROM `gsswitch` WHERE `userid`=?");
    $query2->execute(array($row['affectedID']));
    $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('S','gs',?,?,?,?,?,NULL,NOW(),?,?,?)");
    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
        $insert->execute(array($row['invoicedByID'], $row2['id'], $row2['rootID'], $row['affectedID'], $row2['serverip'] . ':' . $row2['port'], $row['action'], $row['extraData'], $row2['resellerid']));
    }
    $query2 = $sql->prepare("SELECT `id`,`masterserver`,`ip`,`port`,`resellerid` FROM `voice_server` WHERE `userid`=?");
    $query2->execute(array($row['affectedID']));
    $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('S','vo',?,?,?,?,?,NULL,NOW(),?,?,?)");
    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
        $insert->execute(array($row['invoicedByID'], $row2['id'], $row2['masterserver'], $row['affectedID'], $row2['ip'] . ':' . $row2['port'], $row['action'], $row['extraData'], $row2['resellerid']));
    }
    $query2 = $sql->prepare("SELECT `dnsID`,`dns`,`ip`,`port`,`tsdnsID`,`resellerID` FROM `voice_dns` WHERE `userID`=?");
    $query2->execute(array($row['affectedID']));
    $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('S','ds',?,?,?,?,?,NULL,NOW(),?,?,?)");
    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
        $insert->execute(array($row['invoicedByID'], $row2['dnsID'], $row2['tsdnsID'], $row['affectedID'], $row2['ip'] . ':' . $row2['port'] . ' ' . $row2['dns'], $row['action'], $row['extraData'], $row2['resellerID']));
    }
    $query2 = $sql->prepare("SELECT `id`,`sid`,`dbname`,`resellerid` FROM `mysql_external_dbs` WHERE `uid`=?");
    $query2->execute(array($row['affectedID']));
    $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('S','my',?,?,?,?,?,NULL,NOW(),?,?,?)");
    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
        $insert->execute(array($row['invoicedByID'], $row2['id'], $row2['sid'], $row['affectedID'], $row2['dbname'], $row['action'], $row['extraData'], $row2['resellerid']));
    }
    $update = $sql->prepare("UPDATE `jobs` SET `status`='4' WHERE `jobID`=? LIMIT 1");
    $update->execute(array($row['jobID']));
    updateJobs($row['affectedID'], $row['resellerID'],$jobPending='Y');
    $theOutput->printGraph($command);
}