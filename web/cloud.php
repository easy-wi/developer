<?php

/**
 * File: cloud.php.
 * Author: Ulrich Block
 * Date: 21.10.12
 * Time: 10:24
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

define('EASYWIDIR', dirname(__FILE__));

if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $timelimit = (isset($_GET['timeout']) and is_numeric($_GET['timeout'])) ? (int) $_GET['timeout'] : ini_get('max_execution_time') - 10;
} else {
    $timelimit = 600;
}

set_time_limit($timelimit);

if (!isset($ip) or $_SERVER['SERVER_ADDR'] == $ip) {

    function printText ($text) {
        echo $text."\r\n";
    }

    function getParam ($v) {
        global $value;

        // need for triming as some interfaces do not validate and sanitize
        return (isset($value->$v)) ? trim($value->$v) : '';
    }

    printText('Cloud jobs started');

    include(EASYWIDIR . '/stuff/vorlage.php');
    include(EASYWIDIR . '/stuff/functions.php');
    include(EASYWIDIR . '/stuff/class_validator.php');
    include(EASYWIDIR . '/stuff/class_voice.php');
    include(EASYWIDIR . '/stuff/settings.php');
    include(EASYWIDIR . '/stuff/ssh_exec.php');
    include(EASYWIDIR . '/stuff/keyphrasefile.php');

    printText('File include and parameters fetched. Start connecting to external systems.');

    $query = $sql->prepare("SELECT * FROM `api_import` WHERE `active`='Y'");

    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $resellerID = $row['resellerID'];
        $start = 0;

        if ($row['ssl'] == 'Y') {
            $ssl = 'https://';
            $port = 443;
        } else {
            $ssl = 'http://';
            $port = 80;
        }

        printText('Connect to: '.$ssl.$row['domain']);

        // Users first

        // prepare queries ahead to avoid overhead
        $query2 = $sql->prepare("UPDATE `userdata` SET `salutation`=?,`mail`=?,`cname`=?,`name`=?,`vname`=?,`birthday`=?,`country`=?,`phone`=?,`fax`=?,`handy`=?,`city`=?,`cityn`=?,`street`=?,`streetn`=? WHERE `sourceSystemID`=? AND `externalID`=? AND `resellerid`=? LIMIT 1");
        $query3 = $sql->prepare("INSERT INTO `userdata` (`accounttype`,`salutation`,`mail`,`cname`,`vname`,`name`,`birthday`,`country`,`phone`,`fax`,`handy`,`city`,`cityn`,`street`,`streetn`,`sourceSystemID`,`externalID`,`security`,`resellerid`) VALUES ('u',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $query4 = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE `sourceSystemID`=? AND `externalID`=? LIMIT 1");
        $query5 = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE LOWER(`mail`)=? AND LOWER(`cname`)=? LIMIT 1");
        $query6 = $sql->prepare("UPDATE `userdata` SET `salutation`=?,`mail`=?,`cname`=?,`vname`=?,`name`=?,`birthday`=?,`country`=?,`phone`=?,`fax`=?,`handy`=?,`city`=?,`cityn`=?,`street`=?,`streetn`=? WHERE LOWER(`mail`)=? AND LOWER(`cname`)=? AND `resellerid`=? LIMIT 1");
        $query7 = $sql->prepare("UPDATE `api_import` SET `lastCheck`=?,`lastID`=? WHERE `importID`=? LIMIT 1");
        $query8 = $sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`,`resellerID`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `userID`=`userID`");

        while (!isset($left) or $left > 0) {

            $getRequest = '/' . $row['file'] . '?passwordToken=' . urlencode($row['token']) . '&list=user&start=' . urlencode($start) . '&chunkSize=' . urlencode($row['chunkSize']) . '&lastID=' . urlencode($row['lastID']) . '&updateTime=' . urlencode($row['lastCheck']);
            $rawResponse = webhostRequest($row['domain'], 'https://easy-wi.com', $getRequest, null, $port);
            $response = cleanFsockOpenRequest($rawResponse, '{', '}');
            $decoded = json_decode($response);

            unset($response);

            if ($decoded and isset($decoded->error)) {
                $left = 0;

                if (is_array($decoded->error)) {
                    printText('Error: ' . implode(', ', $decoded->error));
                } else {
                    printText('Error: ' . $decoded->error);
                }

            } else if ($decoded and isset($decoded->total)) {

                if (isset($left)) {
                    $left -= $row['chunkSize'];
                } else {
                    $left = $decoded->total - $row['chunkSize'];
                }

                $start += $row['chunkSize'];
                unset($lastID);

                foreach ($decoded->entries as $value) {

                    if (isset($value->externalID)) {
                        $query4->execute(array(json_encode(array('I' => $row['importID'])), $value->externalID));
                        $checkAmount = $query4->fetchColumn();

                        if ($checkAmount > 0 and $row['fetchUpdates'] == 'Y') {
                            $query2->execute(array(getParam('salutation'), strtolower(getParam('email')), getParam('loginName'), getParam('lastName'), getParam('firstName'), getParam('birthday'), getParam('country'), getParam('phone'), getParam('fax'), getParam('handy'), getParam('city'), getParam('cityn'), getParam('street'), getParam('streetn'),json_encode(array('I' => $row['importID'])), getParam('externalID'), $row['resellerID']));
                            printText('User updated. Loginname: ' . $value->loginName.' e-mail: ' . strtolower($value->email));

                        } else if ($checkAmount > 0) {
                            printText('User update skipped. Loginname: ' . $value->loginName.' e-mail: ' . strtolower($value->email));

                        } else {

                            $query5->execute(array(strtolower($value->email), strtolower($value->loginName)));

                            if ($query5->fetchColumn()>0 and $row['fetchUpdates'] == 'Y') {
                                $query6->execute(array(getParam('salutation'), strtolower(getParam('email')), getParam('loginName'), getParam('firstName'), getParam('lastName'), getParam('birthday'), getParam('country'), getParam('phone'), getParam('fax'), getParam('handy'), getParam('city'), getParam('cityn'), getParam('street'), getParam('streetn'), strtolower($value->email), strtolower($value->loginName), $row['resellerID']));
                                printText('User updated. Loginname: ' . $value->loginName.' e-mail: ' . strtolower($value->email));

                            } else if ($checkAmount > 0) {
                                printText('User update skipped because source system differ. Loginname: ' . $value->loginName.' e-mail: ' . strtolower($value->email));

                            } else {
                                printText('Import user. Loginname: ' . $value->loginName.' e-mail: ' . strtolower($value->email));
                                $query3->execute(array(getParam('salutation'), strtolower(getParam('email')), getParam('loginName'), getParam('firstName'), getParam('lastName'), getParam('birthday'), getParam('country'), getParam('phone'), getParam('fax'), getParam('handy'), getParam('city'), getParam('cityn'), getParam('street'), getParam('streetn'), json_encode(array('I' => $row['importID'])), getParam('externalID'), getParam('password'), $row['resellerID']));
                                $query8->execute(array($sql->lastInsertId(), $row['groupID'], $row['resellerID']));
                            }
                        }

                        if (getParam('updatetime') != '' and (isset($lastCheck) and strtotime(getParam('updatetime')) > strtotime($lastCheck)) or !isset($lastCheck)) {
                            $lastCheck = getParam('updatetime');
                        }

                        $lastID = $value->externalID;
                    }
                }

                if (isset($lastID)) {
                    if (!isset($lastCheck)) {
                        $lastCheck=date('Y-m-d H:i:s');
                    }
                    $query7->execute(array($lastCheck, $lastID, $row['importID']));
                }

                if ($left > 0){
                    printText('Total amount is: ' . $decoded->total . ' User left: ' . $left . ' need to make another run');
                    sleep(1);
                } else {
                    printText('Total amount is: ' . $decoded->total . ' No user left.');
                }

            } else if ($decoded) {
                printText('JSON Response does not contain expected values');
                $left = 0;

            } else {
                if (strpos(strtolower($rawResponse), 'file not found') === false) {
                    printText('No Json Response. Will retry.');
                } else {
                    $left = 0;
                    printText('404: File not found');
                }
            }
        }

        // Get available Gameroot IDs and map to their Easy-WI IDs

        $gameRootIPs = array();

        $query2 = $sql->prepare("SELECT *,AES_DECRYPT(`user`,?) AS `duser`,AES_DECRYPT(`pass`,?) AS `dpass` FROM `rserverdata` WHERE `resellerid`=?");
        $query2->execute(array($aeskey, $aeskey, $resellerID));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            if (($row2['publickey'] == 'Y' and strlen($row2['keyname']) > 0) or ($row2['publickey'] == 'N' and strlen($row2['dpass']) > 0)) {
                $gameRootIPs[$row2['ip']] = array('id' => $row2['id'], 'ftpPort' => $row2['ftpport'], 'user' => $row2['duser'], 'sourceSystemID' => $row2['sourceSystemID'], 'externalID' => $row2['externalID']);
                foreach (ipstoarray($row2['altips']) as $ip) {
                    $gameRootIPs[$ip] = array('id' => $row2['id'], 'ftpPort' => $row2['ftpport'], 'user' => $row2['duser'], 'sourceSystemID' => $row2['sourceSystemID'], 'externalID' => $row2['externalID']);
                }
            }
        }

        // Game Rootserver

        unset($left);
        $start = 0;

        // Prepare queries only once to avoid overhead
        $query2 = $sql->prepare("UPDATE `rserverdata` SET `userID`=?,`ip`=?,`altips`=?,`port`=AES_ENCRYPT(?,?),`ftpport`=?,`cores`=?,`ram`=? WHERE `sourceSystemID`=? AND `externalID`=? LIMIT 1");
        $query3 = $sql->prepare("INSERT INTO `rserverdata` (`userID`,`ip`,`altips`,`port`,`ftpport`,`cores`,`ram`,`sourceSystemID`,`externalID`,`resellerid`) VALUES (?,?,?,AES_ENCRYPT(?,?),?,?,?,?,?,?)");

        while (!isset($left) or $left > 0) {

            $getRequest = '/' . $row['file'] . '?passwordToken=' . urlencode($row['token']) . '&list=rootserver&start=' . urlencode($start) . '&chunkSize=' . urlencode($row['chunkSize']) . '&lastID=' . urlencode($row['lastID']) . '&updateTime=' . urlencode($row['lastCheck']);
            $rawResponse = webhostRequest($row['domain'], 'https://easy-wi.com', $getRequest, null, $port);
            $response = cleanFsockOpenRequest($rawResponse, '{', '}');
            $decoded = json_decode($response);

            unset($response);

            if ($decoded and isset($decoded->error)) {
                $left = 0;

                if (is_array($decoded->error)) {
                    printText('Error: ' . implode(', ', $decoded->error));
                } else {
                    printText('Error: ' . $decoded->error);
                }

            } else if ($decoded and isset($decoded->total)) {

                if (isset($left)) {
                    $left -= $row['chunkSize'];
                } else {
                    $left = $decoded->total - $row['chunkSize'];
                }

                $start += $row['chunkSize'];

                foreach ($decoded->entries as $value) {

                    if (isset($value->externalID)) {

                        // Check if rootserver entry already exists at easy-wi
                        unset($gameRootID);
                        foreach ($value->ips as $ip) {
                            if (isset($gameRootIPs[$ip]['id'])) {
                                $gameRootID = $gameRootIPs[$ip]['id'];
                                $sourceSystemID = $gameRootIPs[$ip]['sourceSystemID'];
                                $externalID = $gameRootIPs[$ip]['externalID'];
                                break;
                            }
                        }

                        // IPs need to converted in Easy-WI format
                        $ip = $value->ips[0];
                        unset($value->ips[0]);
                        $ips = implode("\r\n", $value->ips);

                        if (isset($gameRootID)) {
                            if (json_encode(array('I' => $row['importID'])) == $sourceSystemID and getParam('externalID') == $externalID and $row['fetchUpdates'] == 'Y') {
                                $query2->execute(array(getParam('belongsToID'), $ip, $ips, getParam('sshPort'), $aeskey, getParam('ftpPort'), getParam('cores'), getParam('ram'), json_encode(array('I' => $row['importID'])), getParam('externalID')));
                                printText('Rootserver updated. IP: ' . $ip);
                            } else if (json_encode(array('I' => $row['importID'])) == $sourceSystemID and getParam('externalID') == $externalID) {
                                printText('Rootserver found but update skipped since in import only mode. IP: ' . $ip);
                            } else {
                                printText('Rootserver found but update skipped because source system differ. IP: ' . $ip);
                            }

                        } else {
                            $query3->execute(array(getParam('belongsToID'), $ip, $ips, getParam('sshPort'), $aeskey, getParam('ftpPort'), getParam('cores'), getParam('ram'), json_encode(array('I' => $row['importID'])), getParam('externalID'), $resellerID));

                            $gameRootIPs[$ip] = array('id' => $sql->lastInsertId(), 'ftpPort' => getParam('sshPort'), 'user' => '', 'sourceSystemID' => json_encode(array('I' => $row['importID'])), 'externalID' => getParam('externalID'));

                            printText('Import rootserver. IP: ' . $ip);
                        }
                    }
                }
                if ($left > 0){
                    printText('Total amount is: ' . $decoded->total . ' rootservers left: ' . $left . ' need to make another run');
                    sleep(1);
                } else {
                    printText('Total amount is: ' . $decoded->total . ' No rootservers left.');
                }

            } else if ($decoded) {
                printText('JSON Response does not contain expected values');
                $left = 0;

            } else {
                if (strpos(strtolower($rawResponse), 'file not found') === false) {
                    printText('No Json Response. Will retry.');
                } else {
                    $left = 0;
                    printText('404: File not found');
                }
            }
        }

        // Gameserver
        unset($left);
        $start = 0;
        $gameRootCmds = array();

        // Prepare queries only once to avoid overhead
        $query2 = $sql->prepare("SELECT t.`id`,t.`modfolder`,t.`gamebinary`,t.`map` FROM `servertypes` t INNER JOIN `rservermasterg` m ON t.`id`=m.`servertypeid` WHERE t.`shorten`=? AND t.`resellerid`=? AND m.`serverid`=? AND m.`updating`='N' LIMIT 1");
        $query3 = $sql->prepare("SELECT `id`,`sourceSystemID`,`externalID` FROM `gsswitch` WHERE `serverip`=? AND `port`=? AND `resellerid`=? LIMIT 1");
        $query4 = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `sourceSystemID`=? AND `externalID`=? AND `resellerid`=? LIMIT 1");
        $query5 = $sql->prepare("INSERT INTO `gsswitch` (`stopped`,`ftppassword`,`userid`,`rootID`,`serverip`,`port`,`port2`,`slots`,`taskset`,`cores`,`pallowed`,`sourceSystemID`,`externalID`,`resellerid`) VALUES ('Y',AES_ENCRYPT(?,?),?,?,?,?,?,?,?,?,?,?,?,?)");
        $query6 = $sql->prepare("INSERT INTO `serverlist` (`tic`,`map`,`switchID`,`servertype`,`usermap`,`resellerid`) VALUES (?,?,?,?,'Y',?)");
        $query7 = $sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? LIMIT 1");
        $query8 = $sql->prepare("UPDATE `gsswitch` SET `slots`=?,`taskset`=?,`cores`=?,`pallowed`=? WHERE `id`=? LIMIT 1");

        while (!isset($left) or $left > 0) {

            $getRequest = '/' . $row['file'] . '?passwordToken=' . urlencode($row['token']) . '&list=gameserver&start=' . urlencode($start) . '&chunkSize=' . urlencode($row['chunkSize']) . '&lastID=' . urlencode($row['lastID']) . '&updateTime=' . urlencode($row['lastCheck']);
            $rawResponse = webhostRequest($row['domain'], 'https://easy-wi.com', $getRequest, null, $port);
            $response = cleanFsockOpenRequest($rawResponse, '{', '}');
            $decoded = json_decode($response);

            unset($response);

            if ($decoded and isset($decoded->error)) {
                $left = 0;

                if (is_array($decoded->error)) {
                    printText('Error: ' . implode(', ', $decoded->error));
                } else {
                    printText('Error: ' . $decoded->error);
                }

            } else if ($decoded and isset($decoded->total)) {

                if (isset($left)) {
                    $left -= $row['chunkSize'];
                } else {
                    $left = $decoded->total - $row['chunkSize'];
                }

                $start += $row['chunkSize'];

                foreach ($decoded->entries as $value) {

                    if (isset($value->externalID)) {

                        // Check if a rootserver entry already exists at easy-wi with the used IP
                        $arrayIP = getParam('ip');
                        if (isset($gameRootIPs[$arrayIP]['id'])) {

                            unset($servertypeID,$servertypeModFolder,$switchID);

                            // Check if the rootserver has a masterserver with this shorten
                            $query2->execute(array(getParam('shorten'), $row['resellerID'], $gameRootIPs[$arrayIP]['id']));
                            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                                $servertypeID = $row2['id'];
                                $defaultMap = $row2['map'];

                                // If no srcds or hlds game we will work with the root folder.
                                // If yes than we need to set or all files will be downloaded into incorrect subfolder.
                                $servertypeModFolder = ($row2['gamebinary'] == 'srcds_run' or $row2['gamebinary'] == 'hlds_run') ? $row2['modfolder'] . '/' : '';
                            }


                            if (isset($servertypeID) and isid($servertypeID, 11)) {

                                if (isid(getParam('assignedCore'), 11)) {
                                    $taskset = 'Y';
                                    $core = getParam('assignedCore');
                                } else {
                                    $taskset = 'N';
                                    $core = '';
                                }

                                $query3->execute(array(getParam('ip'), getParam('port'), $resellerID));
                                foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                                    $switchID = $row2['id'];
                                    $sourceSystemID = $row2['sourceSystemID'];
                                    $externalID = $row2['externalID'];
                                }

                                if (isset($switchID) and isid($switchID, 11)) {

                                    if (json_encode(array('I' => $row['importID'])) == $sourceSystemID and getParam('externalID') == $externalID and $row['fetchUpdates'] == 'Y') {

                                        $query8->execute(array(getParam('slots'), $taskset, $core, getParam('protectionMode'), $switchID));

                                        printText('Gameserver found and updated. Address: ' . getParam('ip') . ':' . getParam('port') . ' (' . getParam('shorten') . ')');

                                    } else if (json_encode(array('I' => $row['importID'])) == $sourceSystemID and getParam('externalID') == $externalID) {
                                        printText('Gameserver found but update skipped since import only mode. Address: ' . getParam('ip') . ':' . getParam('port') . ' (' . getParam('shorten') . ')');
                                    } else {
                                        printText('Gameserver found but update skipped because source system differ. Address: ' . getParam('ip') . ':' . getParam('port') . ' (' . getParam('shorten') . ')');
                                    }

                                } else {

                                    unset($internalUserID, $customer);

                                    $query4->execute(array(json_encode(array('I' => $row['importID'])), getParam('belongsToID'), $resellerID));
                                    foreach($query4->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                                        $internalUserID = $row2['id'];
                                        $customer = $row2['cname'];
                                    }

                                    if (isset($internalUserID) and isset($customer) and isid($internalUserID, 11)) {

                                        $passwordGenerate = passwordgenerate(10);

                                        $query5->execute(array($passwordGenerate, $aeskey, $internalUserID, $gameRootIPs[$arrayIP]['id'], getParam('ip'), getParam('port'), getParam('port2'), getParam('slots'), $taskset, $core, getParam('protectionMode'), json_encode(array('I' => $row['importID'])), getParam('externalID'), $resellerID));
                                        $switchID = $sql->lastInsertId();

                                        $tickrate = (getParam('tickrate') > 0) ? getParam('tickrate') : 66;
                                        $startMap = (strlen(getParam('startMap')) > 0) ? getParam('startMap') : $defaultMap;

                                        $query6->execute(array($tickrate, $startMap, $switchID, $servertypeID, $resellerID));

                                        $query7->execute(array($sql->lastInsertId(), $switchID));

                                        $gameRootIP = $gameRootIPs[$arrayIP]['user'];
                                        $gameRootCmds[$gameRootIPs[$arrayIP]['id']][] = "./control.sh add ${customer}-${switchID} ${passwordGenerate} ${gameRootIP} ${passwordGenerate}";

                                        $ftpConnect = 'ftp://' . str_replace('//', '/', getParam('ip') . ':' . $gameRootIPs[$arrayIP]['ftpPort'] . '/' . getParam('path') . '/' . $servertypeModFolder);
                                        $gameRootCmds[$gameRootIPs[$arrayIP]['id']][] = "sudo -u ${customer}-${switchID} ./control.sh migrateserver ${customer}-${switchID} 1_" . getParam('shorten') . " " .getParam('ip') . "_" . getParam('port'). " 1 " . getParam('ftpUser') ." " . getParam('ftpPass'). " ${ftpConnect} ${servertypeModFolder}";

                                        printText('Import Gameserver. Address: ' . getParam('ip') . ':' . getParam('port') . '. And shorten:' . getParam('shorten'));

                                    } else {
                                        printText('Error: Import skipped since no user with external userID ' . getParam('belongsToID') . ' for gameserver with address: ' . getParam('ip') . ':' . getParam('port') . ' and shorten:' . getParam('shorten'));
                                    }
                                }

                            } else {
                                printText('Error: No masterserver with the shorten ' . getParam('shorten') . ' found. Gameserver update skipped for address: ' . getParam('ip') . ':' . getParam('port'));
                            }


                        } else {
                            printText('Error: No game rootserver found with the IP ' . getParam('ip') . '. Gameserver not imported: ' . getParam('ip') . ':' . getParam('port') . ' (' . getParam('shorten') . ')');
                        }
                    }
                }


                if ($left > 0){
                    printText('Total amount is: ' . $decoded->total . ' Gameservers left: ' . $left . ' need to make another run');
                    sleep(1);
                } else {
                    printText('Total amount is: ' . $decoded->total . ' No Gameservers left.');
                }

            } else if ($decoded) {
                printText('JSON Response does not contain expected values');
                $left = 0;

            } else {
                if (strpos(strtolower($rawResponse), 'file not found') === false) {
                    printText('No Json Response. Will retry.');
                } else {
                    $left = 0;
                    printText('404: File not found');
                }
            }
        }

        // Start the migration of newly imported gameservers
        foreach ($gameRootCmds as $k => $v) {
            ssh2_execute('gs', $k, $v);
        }

        // Set to null instead of unset() because PHP garbage collector does not work very efficient
        $gameRootCmds = null;
        $gameRootIPs = null;


        // TS3 Master server array
        $ts3MasterIPs = array();

        $query2 = $sql->prepare("SELECT `id`,`ssh2ip`,`ips`,`sourceSystemID`,`externalID` FROM `voice_masterserver` WHERE `resellerid`=?");
        $query2->execute(array($resellerID));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $ts3MasterIPs[$row2['ssh2ip']] = array('id' => $row2['id'], 'sourceSystemID' => $row2['sourceSystemID'], 'externalID' => $row2['externalID']);
            foreach (ipstoarray($row2['ips']) as $ip) {
                $ts3MasterIPs[$ip] = array('id' => $row2['id'], 'sourceSystemID' => $row2['sourceSystemID'], 'externalID' => $row2['externalID']);
            }
        }


        // TS3 Masterserver
        unset($left);
        $start = 0;

        // Prepare queries only once to avoid overhead
        $query2 = $sql->prepare("UPDATE `voice_masterserver` SET `ssh2ip`=?,`defaultdns`=?,`queryport`=?,`querypassword`=AES_ENCRYPT(?,?),`ssh2user`=AES_ENCRYPT(?,?),`serverdir`=? WHERE `id`=? LIMIT 1");
        $query3 = $sql->prepare("INSERT INTO `voice_masterserver` (`addedby`,`usedns`,`ssh2ip`,`defaultdns`,`queryport`,`querypassword`,`ssh2user`,`serverdir`,`sourceSystemID`,`externalID`,`resellerid`) VALUES (2,'N',?,?,?,AES_ENCRYPT(?,?),AES_ENCRYPT(?,?),?,?,?,?)");

        while (!isset($left) or $left > 0) {

            $getRequest = '/' . $row['file'] . '?passwordToken=' . urlencode($row['token']) . '&list=voicemaster&start=' . urlencode($start) . '&chunkSize=' . urlencode($row['chunkSize']) . '&lastID=' . urlencode($row['lastID']) . '&updateTime=' . urlencode($row['lastCheck']);
            $rawResponse = webhostRequest($row['domain'], 'https://easy-wi.com', $getRequest, null, $port);
            $response = cleanFsockOpenRequest($rawResponse, '{', '}');
            $decoded = json_decode($response);

            unset($response);

            if ($decoded and isset($decoded->error)) {
                $left = 0;

                if (is_array($decoded->error)) {
                    printText('Error: ' . implode(', ', $decoded->error));
                } else {
                    printText('Error: ' . $decoded->error);
                }

            } else if ($decoded and isset($decoded->total)) {

                if (isset($left)) {
                    $left -= $row['chunkSize'];
                } else {
                    $left = $decoded->total - $row['chunkSize'];
                }

                $start += $row['chunkSize'];

                foreach ($decoded->entries as $value) {

                    if (isset($value->externalID)) {

                        // Check if rootserver entry already exists at easy-wi
                        if (isset($ts3MasterIPs[getParam('ip')]['id'])) {

                            if (json_encode(array('I' => $row['importID'])) == $sourceSystemID and getParam('externalID') == $externalID and $row['fetchUpdates'] == 'Y') {

                                $query2->execute(array(getParam('ip'), getParam('dns'), getParam('port'), getParam('queryPassword'), $aeskey, getParam('sshUser'), $aeskey, getParam('path'), $ts3MasterIPs[getParam('ip')]['id']));

                                printText('TS3 masterserver updated. IP: ' . getParam('ip'));

                            } else if (json_encode(array('I' => $row['importID'])) == $sourceSystemID and getParam('externalID') == $externalID) {
                                printText('TS3 masterserver found but update skipped since in import only mode. IP: ' . getParam('ip'));
                            } else {
                                printText('TS3 masterserver found but update skipped because source system differ. IP: ' . getParam('ip'));
                            }

                        } else {

                            $query3->execute(array(getParam('ip'), getParam('dns'), getParam('port'), getParam('queryPassword'), $aeskey, getParam('sshUser'), $aeskey, getParam('path'), json_encode(array('I' => $row['importID'])), getParam('externalID'), $resellerID));

                            $keyIP = getParam('ip');
                            $ts3MasterIPs[$keyIP][] = array('id' => $sql->lastInsertId(), 'sourceSystemID' => json_encode(array('I' => $row['importID'])), 'externalID' => getParam('externalID'));

                            printText('TS3 masterserver impororted. IP: ' . getParam('ip'));
                        }
                    }
                }
                if ($left > 0){
                    printText('Total amount is: ' . $decoded->total . ' TS3 masterserver left: ' . $left . ' need to make another run');
                    sleep(1);
                } else {
                    printText('Total amount is: ' . $decoded->total . ' TS3 masterserver left.');
                }

            } else if ($decoded) {
                printText('JSON Response does not contain expected values');
                $left = 0;

            } else {
                if (strpos(strtolower($rawResponse), 'file not found') === false) {
                    printText('No Json Response. Will retry.');
                } else {
                    $left = 0;
                    printText('404: File not found');
                }
            }
        }

        // TS3 virtual server
        unset($left);
        $start = 0;

        // Prepare queries only once to avoid overhead
        $query2 = $sql->prepare("SELECT `id`,`sourceSystemID`,`externalID` FROM `voice_server` WHERE `ip` =? AND `port`=? AND `resellerid`=? LIMIT 1");
        $query3 = $sql->prepare("UPDATE `voice_server` SET `ip`=?,`port`=?,`dns`=?,`slots`=? WHERE `id`=? LIMIT 1");
        $query4 = $sql->prepare("SELECT `id` FROM `userdata` WHERE `sourceSystemID`=? AND `externalID`=? AND `resellerid`=? LIMIT 1");
        $query5 = $sql->prepare("INSERT INTO `voice_server` (`ip`,`port`,`dns`,`slots`,`userid`,`masterserver`,`sourceSystemID`,`externalID`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?)");

        while (!isset($left) or $left > 0) {

            $getRequest = '/' . $row['file'] . '?passwordToken=' . urlencode($row['token']) . '&list=voiceserver&start=' . urlencode($start) . '&chunkSize=' . urlencode($row['chunkSize']) . '&lastID=' . urlencode($row['lastID']) . '&updateTime=' . urlencode($row['lastCheck']);
            $rawResponse = webhostRequest($row['domain'], 'https://easy-wi.com', $getRequest, null, $port);
            $response = cleanFsockOpenRequest($rawResponse, '{', '}');
            $decoded = json_decode($response);

            unset($response);

            if ($decoded and isset($decoded->error)) {

                $left = 0;

                if (is_array($decoded->error)) {
                    printText('Error: ' . implode(', ', $decoded->error));
                } else {
                    printText('Error: ' . $decoded->error);
                }

            } else if ($decoded and isset($decoded->total)) {

                if (isset($left)) {
                    $left -= $row['chunkSize'];
                } else {
                    $left = $decoded->total - $row['chunkSize'];
                }

                $start += $row['chunkSize'];

                foreach ($decoded->entries as $value) {

                    if (isset($value->externalID)) {

                        $ts3MasterIP = getParam('ip');

                        // Check if TS3 masterserver entry exists at easy-wi
                        if (isset($ts3MasterIPs[$ts3MasterIP]['id'])) {

                            unset($ts3ID);

                            // Get TS3 data if server exists
                            $query2->execute(array(getParam('ip'), getParam('port'), $resellerID));
                            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                                $ts3ID = $row2['id'];
                                $sourceSystemID = $row2['sourceSystemID'];
                                $externalID = $row2['externalID'];
                            }

                            if (isset($ts3ID) and isid($ts3ID, 11) and json_encode(array('I' => $row['importID'])) == $sourceSystemID and getParam('externalID') == $externalID and $row['fetchUpdates'] == 'Y') {

                                $query3->execute(array(getParam('ip'), getParam('port'), getParam('dns'), getParam('slots'), $ts3ID));

                                printText('TS3 server updated. Address: ' . getParam('ip') . ':'. getParam('port') . ' '. getParam('dns'));

                            } else if (isset($ts3ID) and isid($ts3ID, 11) and json_encode(array('I' => $row['importID'])) == $sourceSystemID and getParam('externalID') == $externalID) {

                                printText('TS3 server update skipped because import only mode. Address: ' . getParam('ip') . ':'. getParam('port') . ' '. getParam('dns'));

                            } else if (isset($ts3ID) and isid($ts3ID, 11)) {

                                printText('TS3 server update skipped because Source System ID differ. Address: ' . getParam('ip') . ':'. getParam('port') . ' '. getParam('dns'));

                            } else {

                                $query4->execute(array(json_encode(array('I' => $row['importID'])), getParam('belongsToID'), $resellerID));
                                $userID = $query4->fetchColumn();

                                if (isid($userID, 11)) {
                                    $query5->execute(array(getParam('ip'), getParam('port'), getParam('dns'), getParam('slots'), $userID, $ts3MasterIPs[$ts3MasterIP]['id'], json_encode(array('I' => $row['importID'])), getParam('belongsToID'), $resellerID));

                                    printText('Imported TS3 server. Address + DNS: ' . getParam('ip') . ':'. getParam('port') . ' '. getParam('dns'));

                                } else {
                                    printText('Error: Cannot import TS3 server due to missing user with external userID ' . getParam('belongsToID') . '. Address + DNS: ' . getParam('ip') . ':'. getParam('port') . ' '. getParam('dns'));
                                }
                            }

                        } else {
                            printText('Error: Cannot import TS3 server due to missing masterserver. Address + DNS: ' . getParam('ip') . ':'. getParam('ip') . ' '. getParam('dns'));
                        }
                    } else {
                        printText('Error: externalID not set');
                    }
                }
                if ($left > 0){
                    printText('Total amount is: ' . $decoded->total . ' TS3 virtual server left: ' . $left . ' need to make another run');
                    sleep(1);
                } else {
                    printText('Total amount is: ' . $decoded->total . ' No TS3 virtual server left.');
                }

            } else if ($decoded) {
                printText('JSON Response does not contain expected values');
                $left = 0;

            } else {
                if (strpos(strtolower($rawResponse), 'file not found') === false) {
                    printText('No Json Response. Will retry.');
                } else {
                    $left = 0;
                    printText('404: File not found');
                }
            }
        }

        // As we cannot import the virtual server IDs we need to get them afterwards
        $query = $sql->prepare("SELECT DISTINCT(`masterserver`) FROM `voice_server` WHERE `resellerid`=? AND (`localserverid`<1 OR `localserverid` IS NULL)");
        $query2 = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `active`='Y' AND `id`=:id LIMIT 1");
        $query3 = $sql->prepare("UPDATE `voice_server` SET `localserverid`=? WHERE `masterserver`=? AND `resellerid`=? AND `port`=? AND (`localserverid`<1 OR `localserverid` IS NULL)");

        $query->execute(array($resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $query2->execute(array(':aeskey' => $aeskey, ':id' => $row2['masterserver']));
            foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row3) {

                $ts3 = new TS3($row3['ssh2ip'], $row3['queryport'], 'serveradmin', $row3['decryptedquerypassword'], false);

                if (strpos($ts3->errorcode, 'error id=0') !== false) {

                    $serverlist = $ts3->ServerList();

                    if (!isset($serverlist[0]['id']) or $serverlist[0]['id'] == 0) {

                        foreach ($serverlist as $server) {
                            $query3->execute(array($server['virtualserver_id'], $row2['masterserver'], $resellerID, $server['virtualserver_port']));
                        }

                    }

                }
            }
        }


        // Substitutes
        unset($left);
        $start = 0;
/**
        // Prepare queries only once to avoid overhead

        while (!isset($left) or $left > 0) {

            $getRequest = '/' . $row['file'] . '?passwordToken=' . urlencode($row['token']) . '&list=substitutes&start=' . urlencode($start) . '&chunkSize=' . urlencode($row['chunkSize']) . '&lastID=' . urlencode($row['lastID']) . '&updateTime=' . urlencode($row['lastCheck']);
            $rawResponse = webhostRequest($row['domain'], 'https://easy-wi.com', $getRequest, null, $port);
            $response = cleanFsockOpenRequest($rawResponse, '{', '}');
            $decoded = json_decode($response);

            unset($response);

            if ($decoded and isset($decoded->error)) {
                $left = 0;

                if (is_array($decoded->error)) {
                    printText('Error: ' . implode(', ', $decoded->error));
                } else {
                    printText('Error: ' . $decoded->error);
                }

            } else if ($decoded and isset($decoded->total)) {

                if (isset($left)) {
                    $left -= $row['chunkSize'];
                } else {
                    $left = $decoded->total - $row['chunkSize'];
                }

                $start += $row['chunkSize'];

                foreach ($decoded->entries as $value) {

                    if (isset($value->externalID)) {

                        $query2 = $sql->prepare("SELECT `sID` FROM `userdata_substitutes` WHERE `sourceSystemID`=? AND `externalID`=? AND `resellerID`=? LIMIT 1");
                        $query2->execute(array(json_encode(array('I' => $row['importID'])), getParam('externalID'), $resellerID));
                        $localID = $query2->fetchColumn();

                        // Check if substitute exists at easy-wi
                        if (isid($localID, 11) and $row['fetchUpdates'] == 'Y') {

                            $query3 = $sql->prepare("UPDATE `userdata_substitutes` SET `loginName`=?,`name`=?,`vname`=? WHERE `sID`=? LIMIT 1");
                            $query3->execute(array(getParam('loginName'), getParam('lastName'), getParam('firstName'), $localID));
                            printText('Substitute updated. Loginname: ' . getParam('loginName'));

                        } else if (isset($ts3ID) and isid($ts3ID, 11) and json_encode(array('I' => $row['importID'])) == $sourceSystemID and getParam('externalID') == $externalID) {

                            printText('Substitute update skipped because import only mode. Loginname: ' . getParam('loginName'));

                        } else {

                            $query4 = $sql->prepare("SELECT `id` FROM `userdata` WHERE `sourceSystemID`=? AND `externalID`=? AND `resellerID`=? LIMIT 1");
                            $query4->execute(array(json_encode(array('I' => $row['importID'])), getParam('belongsToID'), $resellerID));
                            $belongsToLocalID = $query4->fetchColumn();

                            if (isid($belongsToLocalID, 11)) {
                                $query5 = $sql->prepare("INSERT INTO `userdata_substitutes` (`userID`,`loginName`,`name`,`vname`,`passwordHashed`,`sourceSystemID`,`externalID`,`resellerID`) VALUES (?,?,?,?,?,?,?,?)");
                                $query5->execute(array($belongsToLocalID, getParam('loginName'), getParam('lastName'), getParam('firstName'), getParam('password'), json_encode(array('I' => $row['importID'])), getParam('belongsToID'), $resellerID));
                                $localID = $sql->lastInsertId();

                                printText('Imported substitute. Loginname: ' . getParam('loginName'));

                            } else {

                                $localID = false;

                                printText('Error: Cannot import substitute ' . getParam('loginName') . 'because there is no user with external ID ' . getParam('belongsToID'));
                            }
                        }
                    }
                }
                if ($left > 0){
                    printText('Total amount is: ' . $decoded->total . ' substitutes left: ' . $left . ' need to make another run');
                    sleep(1);
                } else {
                    printText('Total amount is: ' . $decoded->total . ' No substitute left.');
                }

            } else if ($decoded) {
                printText('JSON Response does not contain expected values');
                $left = 0;

            } else {
                if (strpos(strtolower($rawResponse), 'file not found') === false) {
                    printText('No Json Response. Will retry.');
                } else {
                    $left = 0;
                    printText('404: File not found');
                }
            }
        }

**/

    }

    $query = $sql->prepare("UPDATE `settings` SET `lastCronCloud`=UNIX_TIMESTAMP() WHERE `resellerid`=0 LIMIT 1");
    $query->execute();

} else {
    header('Location: login.php');
    die('Cloud can only be run via console and or a cronjob');
}