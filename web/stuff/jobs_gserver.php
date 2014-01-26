<?php

/**
 * File: jobs_gserver.php.
 * Author: Ulrich Block
 * Date: 05.08.12
 * Time: 23:31
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

$sprache = getlanguagefile('gserver', 'en', 0);

$query = $sql->prepare("SELECT `hostID`,`resellerID` FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='gs' GROUP BY `hostID`");
$query2 = $sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='gs' AND `hostID`=?");
$query3 = $sql->prepare("SELECT g.*,AES_DECRYPT(g.`ftppassword`,?) AS `ftp`,AES_DECRYPT(g.`ppassword`,?) AS `ppasswordftp`,u.`cname` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? LIMIT 1");
$query->execute();
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

    $cmds = array();

    $query2->execute(array($row['hostID']));

    $serverData = serverdata('root', $row['hostID'], $aeskey);
    $sshuser = $serverData['user'];

    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

        unset($customer, $i);

        $extraData = @json_decode($row2['extraData']);
        $installGames = (is_object($extraData) and preg_match('/[AP]/', $extraData->installGames)) ? $extraData->installGames : 'A';

        $query3->execute(array($aeskey, $aeskey, $row2['affectedID']));
        foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {

            $active = $row3['active'];
            $port = $row3['port'];
            $gsIP = $row3['serverip'];
            $gsfolder = $row3['serverip'] . '_' . $row3['port'];
            $ftppass = $row3['ftp'];
            $ftppass2 = $row3['ppasswordftp'];
            $customer = ($row3['newlayout'] == 'Y') ? $row3['cname'] . '-' . $row3['id'] : $row3['cname'];
            $gamestring = '';

            if ($installGames == 'P') {

                $query4 = $sql->prepare("SELECT t.`gamemod`,t.`gamemod2`,t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`id`=? LIMIT 1");
                $query4->execute(array($row3['serverid']));
                foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row4) {
                    $gamestring .= ($row4['gamemod'] == 'Y' and strlen($row4['gamemod']) > 0) ?  '_' . $row4['shorten'] . $row4['gamemod2'] : '_' . $row4['shorten'];
                }

            } else {

                $query4 = $sql->prepare("SELECT t.`gamemod`,t.`gamemod2`,t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=?");
                $query4->execute(array($row2['affectedID']));
                foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row4) {
                    $gamestring .= ($row4['gamemod'] == 'Y' and strlen($row4['gamemod']) > 0) ?  '_' . $row4['shorten'] . $row4['gamemod2'] : '_' . $row4['shorten'];
                }

            }

            $i = (int) $query4->rowCount();
            $gamestring = $i . $gamestring;

        }

        if (isset($i) and $row2['action'] == 'dl' and isset($customer)) {

            $cmds[] = 'su -u ' . $customer . ' ./control.sh delscreen ' . $customer;
            $cmds[] = 'su -u ' . $customer . '-p ./control.sh delscreen ' . $customer . '-p';
            $cmds[] = './control.sh delCustomer ' . $customer;

            $query4 = $sql->prepare("DELETE FROM `gsswitch` WHERE `id`=? LIMIT 1");
            $query4->execute(array($row2['affectedID']));

            customColumns('G', $row2['affectedID'], 'del');

            $query4 = $sql->prepare("DELETE s.* FROM `serverlist` s LEFT JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE g.`id` IS NULL");
            $query4->execute();
            $query4 = $sql->prepare("DELETE a.* FROM `addons_installed` a LEFT JOIN `serverlist` s ON a.`serverid`=s.`id` WHERE s.`id` IS NULL");
            $query4->execute();
            $query4 = $sql->prepare("DELETE a.* FROM `addons_installed` a LEFT JOIN `userdata` u ON a.`userid`=u.`id` WHERE u.`id` IS NULL");
            $query4->execute();

            $query4 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type`='gs' LIMIT 1");
            $query4->execute(array($row2['jobID']));

            $command = $gsprache->del . ' gsswitchID: ' . $row2['affectedID'] . ' name:' . $row2['name'] . ' gsswitchID:' . $row2['affectedID'];

        } else if (isset($i) and $row2['action'] == 'ad' and isset($customer)) {

            if ($i > 0) {
                $cmds[] = './control.sh add ' . $customer . ' ' . $ftppass . ' ' . $sshuser . ' ' . passwordgenerate(10);

                if ($installGames != 'N') {
                    $cmds[] = 'sudo -u ' . $customer . ' ./control.sh addserver "' . $customer . '" "' . $gamestring . '" "' . $gsfolder . '" 1';
                }

                $query4 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type`='gs' LIMIT 1");
                $query4->execute(array($row2['jobID']));

                $command = $gsprache->add . ' gsswitchID: ' . $row2['affectedID'] . ' name:' . $row2['name'] . ' gsswitchID:' . $row2['affectedID'];

            } else {
                $command = 'Error no switchserver found for this gsswitchID: ' . $row2['affectedID'] . ' name:' . $row2['name'] . ' gsswitchID:' . $row2['affectedID'];
            }

        } else if (isset($i) and $row2['action'] == 'md' and isset($customer) and isset($active)) {

            $newActive = (is_object($extraData) and isset($extraData->newActive)) ? $extraData->newActive : $active;

            if ($active != $newActive and $newActive == 'N') {

                $ftppass = passwordgenerate(15);
                $ftppass2 = passwordgenerate(15);

                $tmp = gsrestart($row2['affectedID'], 'so', $aeskey, $row['resellerID']);
                if (is_array($tmp)) {
                    foreach($tmp as $t) {
                        $cmds[] = $t;
                    }
                }
            }

            $newPort = (is_object($extraData) and isset($extraData->newPort)) ? $extraData->newPort : $port;

            if ($port != $newPort) {
                $cmds[] = "sudo -u ${customer} ./control.sh move ${customer} ${gsfolder} ${gsIP}_${port}";
            }

            $cmds[] = './control.sh mod ' . $customer . ' ' . $ftppass . ' ' . $ftppass2;

            $query4 = $sql->prepare("UPDATE `gsswitch` SET `active`=?,`port`=?,`jobPending`='N' WHERE `id`=? LIMIT 1");
            $query4->execute(array($newActive, $newPort, $row2['affectedID']));

            $query4 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type`='gs' LIMIT 1");
            $query4->execute(array($row2['jobID']));

            $command = $gsprache->mod . ' gsswitchID: ' . $row2['affectedID'] . ' name: ' . $row2['name'] . ' gsswitchID:' . $row2['affectedID'];

        } else if (isset($i) and $row2['action'] == 're' and isset($customer)) {

            $tmp = gsrestart($row2['affectedID'], 're', $aeskey, $row2['resellerID']);
            if (is_array($tmp)) {
                foreach($tmp as $t) {
                    $cmds[] = $t;
                }
            }

            $query4 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type`='gs' LIMIT 1");
            $query4->execute(array($row2['jobID']));

            $command = '(Re)Start gsswitchID: ' . $row2['affectedID'] . ' name: ' . $row2['name'];

        } else if (isset($i) and $row2['action'] == 'st' and isset($customer)) {

            $tmp = gsrestart($row2['affectedID'], 'so', $aeskey, $row2['resellerID']);
            if (is_array($tmp)) {
                foreach($tmp as $t) {
                    $cmds[] = $t;
                }
            }

            $query4 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type`='gs' LIMIT 1");
            $query4->execute(array($row2['jobID']));

            $command = 'Stop gsswitchID: ' . $row2['affectedID'] . ' name: ' . $row2['name'];

        } else if (!isset($i) and !isset($customer)) {

            $query4 = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `jobID`=? AND `type`='gs' LIMIT 1");
            $query4->execute(array($row2['jobID']));

            $command = 'Error: can not find gsswitchID';

        } else {
            $command = 'Error: unknown command';
        }

        $theOutput->printGraph($command);
    }

    if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
        print_r($cmds);
    }

    if (count($cmds)>0) {
        ssh2_execute('gs', $row['hostID'], $cmds);
    }
}