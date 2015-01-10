<?php

/**
 * File: jobs_voice.php.
 * Author: Ulrich Block
 * Date: 26.05.12
 * Time: 19:31
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

$query = $sql->prepare("SELECT `hostID`,`resellerID` FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='vo' GROUP BY `hostID`");
$query2 = $sql->prepare("SELECT `active`,`usedns`,`defaultdns`,`bitversion`,`defaultname`,`defaultwelcome`,`defaulthostbanner_url`,`defaulthostbanner_gfx_url`,`defaulthostbutton_tooltip`,`defaulthostbutton_url`,`defaulthostbutton_gfx_url`,`queryport`,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,`maxserver`,`maxslots`,`rootid`,`addedby`,`publickey`,`ssh2ip`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password`,`serverdir`,`keyname`,`notified` FROM `voice_masterserver` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");

$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    unset($queryport);

    $query2->execute(array(':aeskey' => $aeskey,':id' => $row['hostID'], ':reseller_id' => $row['resellerID']));
    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

        $active = $row2['active'];
        $addedby = $row2['addedby'];
        $usedns = $row2['usedns'];
        $defaultdns = $row2['defaultdns'];
        $queryport = $row2['queryport'];
        $querypassword = $row2['decryptedquerypassword'];
        $maxserver = $row2['maxserver'];
        $maxslots = $row2['maxslots'];
        $serverdir = $row2['serverdir'];
        $mnotified = $row2['notified'];
        $name = $row2['defaultname'];
        $welcome = $row2['defaultwelcome'];
        $hostbanner_url = $row2['defaulthostbanner_url'];
        $hostbanner_gfx_url = $row2['defaulthostbanner_gfx_url'];
        $hostbutton_tooltip = $row2['defaulthostbutton_tooltip'];
        $hostbutton_url = $row2['defaulthostbutton_url'];
        $hostbutton_gfx_url = $row2['defaulthostbutton_gfx_url'];

        if ($addedby == 1) {

            $query3 = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query3->execute(array($row2['rootid'], $row['resellerID']));
            $queryip = $query3->fetchColumn();

        } else {
            $publickey = $row2['publickey'];
            $queryip = $row2['ssh2ip'];
            $ssh2port = $row2['decryptedssh2port'];
            $ssh2user = $row2['decryptedssh2user'];
            $ssh2password = $row2['decryptedssh2password'];
            $keyname = $row2['keyname'];
            $bitversion = $row2['bitversion'];
        }
    }

    if (isset($queryip, $queryport)) {

        $connection = new TS3($queryip, $queryport, 'serveradmin', $querypassword);
        $errorcode = $connection->errorcode;

        if (!isset($errorcode) or strpos($errorcode, 'error id=0') === false) {

            $query2 = $sql->prepare("UPDATE `jobs` SET `status`='1' WHERE `status` IS NULL AND `type`='vo' AND `hostID`=?");
            $query2->execute(array($row['hostID']));

        } else {

            $query2 = $sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='vo' AND `hostID`=?");
            $query2->execute(array($row['hostID']));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                $extraData = @json_decode($row2['extraData']);

                $query3 = $sql->prepare("SELECT * FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query3->execute(array($row2['affectedID'], $row2['resellerID']));
                while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {

                    $active = $row3['active'];
                    $localserverid = $row3['localserverid'];
                    $backup = $row3['backup'];
                    $lendserver = $row3['lendserver'];
                    $ip = $row3['ip'];
                    $port = $row3['port'];
                    $slots = $row3['slots'];
                    $initialpassword = $row3['initialpassword'];
                    $password = $row3['password'];
                    $forcebanner = $row3['forcebanner'];
                    $forcebutton = $row3['forcebutton'];
                    $forceservertag = $row3['forceservertag'];
                    $forcewelcome = $row3['forcewelcome'];
                    $maxtraffic = $row3['maxtraffic'];
                    $filetraffic = $row3['filetraffic'];
                    $max_download_total_bandwidth = $row3['max_download_total_bandwidth'];
                    $max_upload_total_bandwidth = $row3['max_upload_total_bandwidth'];
                    $dns = $row3['dns'];
                    $masterserver = $row3['masterserver'];
                }

                if ($row2['action'] == 'dl' and isset($localserverid) and isid($localserverid, 30)) {

                    $command = $gsprache->del . ' voiceserverID: ' . $row2['affectedID'] . ' name:'.$row2['name'];
                    $connection->DelServer($localserverid);

                    $query3 = $sql->prepare("DELETE FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query3->execute(array($row2['affectedID'], $row['resellerID']));

                    customColumns('T', $row2['affectedID'], 'del');

                    $query3 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type`='vo' LIMIT 1");
                    $query3->execute(array($row2['jobID']));

                    if ($usedns == 'Y') {
                        tsdns('dl', $queryip, $ssh2port, $ssh2user, $publickey, $keyname, $ssh2password, $mnotified, $serverdir, $bitversion, array($ip), array($port), array($dns), $row['resellerID']);
                    }

                    tsbackup('delete', $ssh2user, $serverdir, $masterserver, $localserverid, '*');

                    $query3 = $sql->prepare("DELETE v.* FROM `voice_server_backup` v LEFT JOIN `userdata` u ON v.`uid`=u.`id` WHERE u.`id` IS NULL");
                    $query3->execute();

                } else if ($row2['action'] == 'ad' and isset($active)) {

                    if (isid($localserverid, 30)) {

                        $command = $gsprache->add.' voiceserverID: '.$row2['affectedID'].'; Skipping, virtual ID already exists in Easy-WI DB: '.$localserverid;

                        $query3 = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `jobID`=? AND `type`='vo' LIMIT 1");
                        $query3->execute(array($row2['jobID']));

                    } else {

                        $virtualserver_id = $connection->AddServer($slots, $ip, $port, $initialpassword, $name, array('Y', $welcome), $max_download_total_bandwidth, $max_upload_total_bandwidth, array('Y', $hostbanner_url), $hostbanner_gfx_url, array('Y', $hostbutton_url), $hostbutton_gfx_url, $hostbutton_tooltip);

                        if (isid($virtualserver_id, 19)) {

                            $command = $gsprache->add.' voiceserverID: '.$row2['affectedID'].'; Name:'.$row2['name'];

                            if ($active == 'N') {
                                $connection->StopServer($virtualserver_id);
                            }

                            $query3 = $sql->prepare("UPDATE `voice_server` SET `localserverid`=?,`jobPending`='N' WHERE `id`=? LIMIT 1");
                            $query3->execute(array($virtualserver_id, $row2['affectedID']));

                            if ($usedns == 'Y') {
                                $template_file = tsdns('md', $queryip, $ssh2port, $ssh2user, $publickey, $keyname, $ssh2password, $mnotified, $serverdir, $bitversion, array($ip), array($port), array($dns), $row['resellerID']);
                            }

                            $query3 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `affectedID`=? AND `type`='vo' LIMIT 1");
                            $query3->execute(array($row2['affectedID']));

                        } else {

                            $command = $gsprache->add.' voiceserverID: '.$row2['affectedID'].'; Error: '.$virtualserver_id;

                            $query3 = $sql->prepare("UPDATE `jobs` SET `status`='1' WHERE `jobID`=? AND `type`='vo' LIMIT 1");
                            $query3->execute(array($row2['jobID']));
                        }
                    }

                } else if ($row2['action'] == 'md' and isset($localserverid) and isid($localserverid, 30)) {

                    $command = $gsprache->mod . ' voiceserverID: ' . $row2['affectedID'] . ' name:' . $row2['name'];

                    $query3 = $sql->prepare("SELECT `active`,`slots`,`ip`,`port`,`dns` FROM `voice_server` WHERE `id`=? LIMIT 1");
                    $query3->execute(array($row2['affectedID']));
                    foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {

                        $oldip = $row3['ip'];
                        $oldport = $row3['port'];
                        $olddns = $row3['dns'];
                        $connection->StartServer($localserverid);
                        $serverdetails = $connection->ServerDetails($localserverid);
                        $name = $serverdetails['virtualserver_name'];
                        $welcome = $serverdetails['virtualserver_welcomemessage'];
                        $hostbanner_url = $serverdetails['virtualserver_hostbanner_url'];
                        $hostbanner_gfx_url = $serverdetails['virtualserver_hostbanner_gfx_url'];
                        $hostbutton_tooltip = $serverdetails['virtualserver_hostbutton_tooltip'];
                        $hostbutton_url = $serverdetails['virtualserver_hostbutton_url'];
                        $hostbutton_gfx_url = $serverdetails['virtualserver_hostbutton_gfx_url'];

                        $connection->ModServer($localserverid, $slots, $ip, $port, $initialpassword, $name, $welcome, $max_download_total_bandwidth, $max_upload_total_bandwidth, $hostbanner_url, $hostbanner_gfx_url, $hostbutton_url, $hostbutton_gfx_url, $hostbutton_tooltip);

                        if ($forcebanner== 'Y') {
                            $removelist[] = 'b_virtualserver_modify_hostbanner';
                            $removelist[] = 'i_needed_modify_power_virtualserver_modify_hostbanner';
                        } else if ($forcebanner== 'N') {
                            $addlist[] = 'b_virtualserver_modify_hostbanner';
                            $addlist[] = 'i_needed_modify_power_virtualserver_modify_hostbanner';
                        }

                        if ($forcebutton == 'Y') {
                            $removelist[] = 'b_virtualserver_modify_hostbutton';
                            $removelist[] = 'i_needed_modify_power_virtualserver_modify_hostbutton';
                        } else if ($forcebutton == 'N') {
                            $addlist[] = 'b_virtualserver_modify_hostbutton';
                            $addlist[] = 'i_needed_modify_power_virtualserver_modify_hostbutton';
                        }

                        if ($forcewelcome == 'Y') {
                            $removelist[] = 'b_virtualserver_modify_welcomemessage';
                            $removelist[] = 'i_needed_modify_power_virtualserver_modify_welcomemessage';
                        } else if ($forcewelcome == 'N') {
                            $addlist[] = 'b_virtualserver_modify_welcomemessage';
                            $addlist[] = 'i_needed_modify_power_virtualserver_modify_welcomemessage';
                        }

                        if (isset($addlist)) {
                            $connection->AdminPermissions($localserverid,'add', $addlist);
                        }

                        if (isset($removelist)) {
                            $connection->AdminPermissions($localserverid,'del', $removelist);
                        }

                        if ($usedns == 'Y') {
                            $template_file = tsdns('md', $queryip, $ssh2port, $ssh2user, $publickey, $keyname, $ssh2password, $mnotified, $serverdir, $bitversion, array($ip, $oldip), array($port, $oldport), array($dns, $olddns), $row2['resellerID']);
                        }

                        if ($row3['active'] == 'N' or $extraData->newActive == 'N') {
                            $connection->StopServer($localserverid);
                        }

                        $query3 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type`='vo' LIMIT 1");
                        $query3->execute(array($row2['jobID']));

                        $query3 = $sql->prepare("UPDATE `voice_server` SET `jobPending`='N' WHERE `id`=? LIMIT 1");
                        $query3->execute(array($row2['affectedID']));
                    }
                } else if (!isset($localserverid) or !isid($localserverid, 30)) {

                    $command = 'Error: can not find voiceserver';

                    $query3 = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `jobID`=? AND `type`='vo' LIMIT 1");
                    $query3->execute(array($row2['jobID']));

                } else {
                    $command='Error: unknown command';
                }

                $theOutput->printGraph($command);
            }

            $connection->CloseConnection();
        }

    } else {

        $query2 = $sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='vo' AND `hostID`=?");
        $query2->execute(array($row['hostID']));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

            $query3 = $sql->prepare("DELETE FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query3->execute(array($row2['affectedID'], $row['resellerID']));

            $query3 = $sql->prepare("DELETE v.* FROM `voice_server_backup` v LEFT JOIN `userdata` u ON v.`uid`=u.`id` WHERE u.`id` IS NULL");
            $query3->execute();

            $query3 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type`='vo' LIMIT 1");
            $query3->execute(array($row2['jobID']));
        }
    }
}