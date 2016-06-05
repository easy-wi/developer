<?php
/**
 * File: jobs_tsdns.php.
 * Author: Ulrich Block
 * Date: 16.06.13
 * Time: 14:52
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


$query = $sql->prepare("SELECT `hostID`,MAX(`resellerID`) AS `resellerID` FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='ds' GROUP BY `hostID`");
$query2 = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
$query3 = $sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='ds' AND `hostID`=?");
$query4 = $sql->prepare("SELECT `active`,`dns`,`ip`,`port` FROM `voice_dns` WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
$query5 = $sql->prepare("DELETE FROM `voice_dns` WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
$query6 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type`='ds' LIMIT 1");
$query7 = $sql->prepare("UPDATE `voice_dns` SET `active`=?,`jobPending`='N' WHERE  `dnsID`=? LIMIT 1");
$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $query2->execute(array(':aeskey' => $aeskey,':id' => $row['hostID'], ':reseller_id' => $row['resellerID']));
    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
        $publickey = $row2['publickey'];
        $queryip = $row2['ssh2ip'];
        $ssh2port = $row2['decryptedssh2port'];
        $ssh2user = $row2['decryptedssh2user'];
        $ssh2password = $row2['decryptedssh2password'];
        $serverdir = $row2['serverdir'];
        $keyname = $row2['keyname'];
        $bitversion = $row2['bitversion'];
    }

    $query3->execute(array($row['hostID']));
    while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {

        $query4->execute(array($row3['affectedID'], $row3['resellerID']));
        while ($row4 = $query4->fetch(PDO::FETCH_ASSOC)) {

            $active = $row4['active'];
            $ipArray = array($row4['ip']);
            $portArray = array($row4['port']);
            $dnsArray = array($row4['dns']);
            $dnsAction = 'md';

            if ($row3['action'] == 'dl') {

                $dnsAction = 'dl';

                $command = $gsprache->del.' TS DNS ID: '.$row3['affectedID'].' name:'.$row4['dns'];

                $query5->execute(array($row3['affectedID'], $row['resellerID']));

                customColumns('T', $row3['affectedID'], 'del');

            } else if ($row3['action'] == 'ad') {

                $command = $gsprache->add.' TS DNS ID: '.$row3['affectedID'].' name:'.$row4['dns'];

            } else if ($row3['action'] == 'md') {

                $extraData = @json_decode($row3['extraData']);

                if (is_object($extraData) and isset($extraData->newActive)) {
                    $active = $extraData->newActive;
                }

                $command = $gsprache->mod.' TS DNS ID: '.$row3['affectedID'].' name:'.$row4['dns'];

                if ($active == 'N') {

                    $dnsAction = 'dl';

                } else if ($active == 'Y' and isset($oldip) and ($row4['ip'] != $oldip or $row4['port'] != $oldport or $row4['dns'] != $olddns)) {
                    $ipArray = array($row4['ip'], $oldip);
                    $portArray = array($row4['port'], $oldport);
                    $dnsArray = array($row4['dns'], $olddns);
                }
            }

            $query7->execute(array($active, $row3['affectedID']));

            if (isset($dnsAction) and strlen($row4['ip']) > 0 and strlen($row4['port']) > 0 and strlen($row4['dns']) > 0) {
                tsdns($dnsAction, $queryip, $ssh2port, $ssh2user, $publickey, $keyname, $ssh2password, 0, $serverdir, $bitversion, $ipArray, $portArray, $dnsArray, $row3['resellerID']);
            }

            $query6->execute(array($row3['jobID']));
        }  
    }
}