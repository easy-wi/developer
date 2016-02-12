<?php
/**
 * File: voice.php.
 * Author: Ulrich Block
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['voiceserver'])) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_ts3.php');
include(EASYWIDIR . '/stuff/methods/functions_ts3.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$sprache = getlanguagefile('voice', $user_language, $reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

$logreseller = 0;
$logsubuser = 0;

if ($reseller_id != 0) {
    $logsubuser = (isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller = 0;
}

// Define the ID variable which will be used at the form and SQLs
$id = $ui->id('id', 10, 'get');
$externalID = $ui->externalID('externalID', 'post');
$userID = $ui->id('userID', 10, 'post');
$rootID = $ui->id('rootID', 10, 'post');
$ip = $ui->ip4('ip', 'post');
$port = $ui->port('port', 'post');
$slots = ($ui->id('slots', 10, 'post')) ? $ui->id('slots', 10, 'post') : 12;
$dns = strtolower($ui->domain('dns', 'post'));
$name = $ui->startparameter('name', 'post');
$welcome = $ui->description('welcome', 'post');
$hostbanner_url = $ui->url('hostbanner_url', 'post');
$hostbanner_gfx_url = $ui->url('hostbanner_gfx_url', 'post');
$hostbutton_tooltip = $ui->description('hostbutton_tooltip', 'post');
$hostbutton_url = $ui->url('hostbutton_url', 'post');
$hostbutton_gfx_url = $ui->url('hostbutton_gfx_url', 'post');
$max_download_total_bandwidth = ($ui->isinteger('max_download_total_bandwidth', 'post')) ? $ui->isinteger('max_download_total_bandwidth', 'post') : 65536;
$max_upload_total_bandwidth = ($ui->isinteger('max_upload_total_bandwidth', 'post')) ? $ui->isinteger('max_upload_total_bandwidth', 'post') : 65536;
$maxtraffic = ($ui->escaped('maxtraffic', 'post') === 0 or $ui->escaped('maxtraffic', 'post') == '-1' or $ui->id('maxtraffic', 255, 'post')) ? $ui->escaped('maxtraffic', 'post') : 1024;
$flexSlots = ($ui->active('flexSlots', 'post')) ? $ui->active('flexSlots', 'post') : 'N';
$autoRestart = ($ui->active('autoRestart', 'post')) ? $ui->active('autoRestart', 'post') : 'N';
$active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
$backup = ($ui->active('backup', 'post')) ? $ui->active('backup', 'post') : 'Y';
$password = ($ui->active('password', 'post')) ? $ui->active('password', 'post') : 'Y';
$lendserver = ($ui->active('lendserver', 'post')) ? $ui->active('lendserver', 'post') : 'Y';
$forcebanner = ($ui->active('forcebanner', 'post')) ? $ui->active('forcebanner', 'post') : 'Y';
$forcebutton = ($ui->active('forcebutton', 'post')) ? $ui->active('forcebutton', 'post') : 'Y';
$forceservertag = ($ui->active('forceservertag', 'post')) ? $ui->active('forceservertag', 'post') : 'Y';
$forcewelcome = ($ui->active('forcewelcome', 'post')) ? $ui->active('forcewelcome', 'post') : 'Y';
$flexSlotsPercent = $ui->id('flexSlotsPercent', 3, 'post');
$flexSlotsFree = $ui->id('flexSlotsFree', 11, 'post');
$oldSlots= 0;

if ($password == 'N') {
    $initialpassword = '';
} else {
    $initialpassword = ($ui->password('initialpassword', 50, 'post')) ? $ui->password('initialpassword', 50, 'post') : passwordgenerate(10);
}

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action', 4, 'post') and !token(true)) {

    unset($header, $text);

    $errors = array('token' => $spracheResponse->token);

} else {
    $errors = array();
}

if ($ui->st('d', 'get') == 'ad' and is_numeric($licenceDetails['lVo']) and $licenceDetails['lVo']>0 and $licenceDetails['left']>0 and !is_numeric($licenceDetails['left'])) {

    $template_file = $gsprache->licence;

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Add jQuery plugin chosen to the header
    $htmlExtraInformation['css'][] = '<link href="css/default/chosen/chosen.min.css" rel="stylesheet" type="text/css">';
    $htmlExtraInformation['js'][] = '<script src="js/default/plugins/chosen/chosen.jquery.min.js" type="text/javascript"></script>';

    if ($ui->st('d', 'get') == 'md' and $id) {

        $query = $sql->prepare("SELECT v.*,CONCAT(u.`cname`,' ',u.`vname`,' ',u.`name`) AS `user_name` FROM `voice_server` AS v INNER JOIN  `userdata` AS u ON u.`id`=v.`userid` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            // Should only be set in case of GET requests
            if (!$ui->st('action', 'post')) {
                $externalID = $row['externalID'];
                $active = $row['active'];
                $backup = $row['backup'];
                $lendserver = $row['lendserver'];
                $userid = $row['userid'];
                $slots = $row['slots'];
                $initialpassword = $row['initialpassword'];
                $password = $row['password'];
                $forcebanner = $row['forcebanner'];
                $forcebutton = $row['forcebutton'];
                $forceservertag = $row['forceservertag'];
                $forcewelcome = $row['forcewelcome'];
                $autoRestart = $row['autoRestart'];
                $max_download_total_bandwidth = $row['max_download_total_bandwidth'];
                $max_upload_total_bandwidth = $row['max_upload_total_bandwidth'];
                $maxtraffic = $row['maxtraffic'];
            }

            $localServerID = $row['localserverid'];
            $rootID = $row['masterserver'];

            $userName = trim($row['user_name']);

            $filetraffic = round(( $row['filetraffic'] / 1024 ), 2);

            $oldActive = $row['active'];
            $oldIp = $row['ip'];
            $oldPort = $row['port'];
            $oldDns = $row['dns'];
            $oldSlots = $row['slots'];
            $oldForceBanner = $row['forcebanner'];
            $oldForceButton = $row['forcebutton'];
            $oldForceWelcome = $row['forcewelcome'];
        }
    }

    $query = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND (`resellerid`=:reseller_id OR `managedForID`=:managedForID) AND `active`='Y' LIMIT 1");
    $query->execute(array(':aeskey' => $aeskey, ':id' => $rootID,':reseller_id' => $resellerLockupID, ':managedForID' => $admin_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $masterServerData) {

        // In case of an external DNS server, we might have to use a different default DNS
        if ($masterServerData['externalDefaultDNS'] == 'Y' and isid($masterServerData['tsdnsServerID'], 19) and $masterServerData['usedns'] == 'Y') {

            $query2 = $sql->prepare("SELECT `defaultdns` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
            $query2->execute(array($masterServerData['tsdnsServerID'], $resellerLockupID));
            $masterServerData['defaultdns'] = $query2->fetchColumn();
        }
    }

    if (count($errors) == 0 and ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') and isset($masterServerData)) {

        $iniConfigurationMaster = array();
        $iniConfiguration = array();

        if ($ui->st('action', 'post') == 'ad') {
            if (!$userID) {

                $error['userID'] = $sprache->user;

            } else {

                $query = $sql->prepare("SELECT 1 FROM `userdata` WHERE `id`=? AND `resellerid`=? AND `accounttype`='u' LIMIT 1");
                $query->execute(array($userID, $resellerLockupID));

                if ($query->rowCount() == 0) {
                    $error['userID'] = $sprache->user;
                }
            }
        }

        if (!$active) {
            $errors['active'] = $sprache->active;
        }

        if (!$ip) {
            $errors['ip'] = $sprache->ip;
        }

        if (!$port) {

            $errors['port'] = $sprache->port;

        } else {

            $used = usedPorts(array($ip));

            if (in_array($port, $used['ports']) and ($ui->st('action', 'post') == 'ad' or ($ui->st('action', 'post') == 'md') and isset($oldPort, $oldIp) and ($oldPort != $port or $ip != $oldIp))) {
                $errors['port'] = $sprache->port;
            }
        }

        if (!$slots) {

            $errors['slots'] = $sprache->slots;

        } else {

            $query2 = $sql->prepare("SELECT SUM(`slots`) AS `installedslots`  FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=? LIMIT 1");
            $query2->execute(array($rootID, $resellerLockupID));
            $futureSlots = (int) $query2->fetchColumn() - $oldSlots + $slots;

            if ($futureSlots > $masterServerData['maxslots']) {
                $errors['slots'] = $sprache->slots;
            }
        }

        if ($ui->st('action', 'post') == 'ad' and !$userID) {
            $errors['userID'] = $sprache->user;
        }

        if (!$rootID or !isset($masterServerData)) {
            $errors['rootID'] = $sprache->rootserver;
        }

        if ($dns and $dns != $masterServerData['defaultdns'] and (!isset($oldDns) or $dns != $oldDns)) {

            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_server` WHERE `id`!=? AND `dns`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $dns, $resellerLockupID));

            $query2 = $sql->prepare("SELECT COUNT(`tsdnsID`) AS `amount` FROM `voice_dns` WHERE `dnsID`!=? AND `dns`=? AND `resellerID`=? LIMIT 1");
            $query2->execute(array($masterServerData['tsdnsServerID'], $dns, $resellerLockupID));

            if ($query->fetchColumn() > 0 or $query2->fetchColumn() > 0) {
                $errors['dns'] = $sprache->dns;
            }
        }

        // No need to check if we can connect if the user messed up the input fields anyway
        if (count($errors) == 0) {

            $connection = new TS3($masterServerData['ssh2ip'], $masterServerData['queryport'], 'serveradmin', $masterServerData['decryptedquerypassword']);
            $errorcode = $connection->errorcode;

            if (strpos($errorcode, 'error id=0') === false) {
                $errors['ts3Connect'] = $errorcode;
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            $iniConfigurationMaster = array();
            $iniConfiguration = array();
            $customConfigurations = array();

            $iniConfigurationMaster = @parse_ini_string($masterServerData['iniConfiguration'], true, INI_SCANNER_RAW);

            foreach ($iniConfigurationMaster as $groupName => $array) {

                $groupNameSelect = $ui->escaped(str_replace(' ', '', $groupName), 'post');

                if ($groupNameSelect and isset($iniConfigurationMaster[$groupName][$groupNameSelect])) {
                    $iniConfiguration[$groupName] = $groupNameSelect;
                } else {
                    reset($iniConfigurationMaster[$groupName]);
                    $iniConfiguration[$groupName] = key($iniConfigurationMaster[$groupName]);
                }

                $customConfigurations[] = $iniConfiguration[$groupName];
            }

            $iniConfiguration = @json_encode($iniConfiguration);

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $localServerID = $connection->AddServer($slots, $ip, $port, $initialpassword, $name, array($forcewelcome, $welcome), $max_download_total_bandwidth, $max_upload_total_bandwidth, array($forcebanner, $hostbanner_url), $hostbanner_gfx_url, array($forcebutton, $hostbutton_url), $hostbutton_gfx_url, $hostbutton_tooltip, $customConfigurations);

                if (isid($localServerID, 255)) {

                    $username = strtolower(getusername($userID));

                    $query = $sql->prepare("INSERT INTO `voice_server` (`active`,`iniConfiguration`,`backup`,`lendserver`,`userid`,`masterserver`,`ip`,`port`,`slots`,`initialpassword`,`password`,`forcebanner`,`forcebutton`,`forceservertag`,`forcewelcome`,`max_download_total_bandwidth`,`max_upload_total_bandwidth`,`localserverid`,`dns`,`maxtraffic`,`serverCreated`,`flexSlots`,`flexSlotsFree`,`flexSlotsPercent`,`autoRestart`,`externalID`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,?,?,?,?,?)");
                    $query->execute(array($active, $iniConfiguration, $backup, $lendserver, $userID, $rootID, $ip, $port, $slots, $initialpassword, $password, $forcebanner, $forcebutton, $forceservertag, $forcewelcome, $max_download_total_bandwidth, $max_upload_total_bandwidth, $localServerID, $dns, $maxtraffic, $flexSlots, $flexSlotsFree, $flexSlotsPercent, $autoRestart, $externalID, $resellerLockupID));
                    $rowCount = $query->rowCount();

                    $id = $sql->lastInsertId();

                } else {
                    $ts3ErrorCode = 'TS errorcode: ' . $localServerID;
                }

                $loguseraction = '%add% %voserver% ' . $ip . ':' . $port;

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                $query = $sql->prepare("UPDATE `voice_server` SET `active`=?,`iniConfiguration`=?,`backup`=?,`lendserver`=?,`ip`=?,`port`=?,`slots`=?,`password`=?,`forcebanner`=?,`forcebutton`=?,`forceservertag`=?,`forcewelcome`=?,`max_download_total_bandwidth`=?,`max_upload_total_bandwidth`=?,`dns`=?,`flexSlots`=?,`flexSlotsFree`=?,`flexSlotsPercent`=?,`maxtraffic`=?,`autoRestart`=?,`externalID`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($active, $iniConfiguration, $backup, $lendserver, $ip, $port, $slots, $password, $forcebanner, $forcebutton, $forceservertag, $forcewelcome, $max_download_total_bandwidth, $max_upload_total_bandwidth, $dns, $flexSlots, $flexSlotsFree, $flexSlotsPercent, $maxtraffic, $autoRestart, $externalID, $id, $resellerLockupID));
                $rowCount = $query->rowCount();

                $return = $connection->ModServer($localServerID, $slots, $ip, $port, $initialpassword, $name, $welcome, $max_download_total_bandwidth, $max_upload_total_bandwidth, $hostbanner_url, $hostbanner_gfx_url, $hostbutton_url, $hostbutton_gfx_url, $hostbutton_tooltip, null, null, null, null, null, null, null, $customConfigurations);

                if (isset($return[0]['msg']) and $return[0]['msg'] == 'ok') {
                    $rowCount++;
                }

                $removeList = array();
                $addList = array();

                if ($forcebanner != $oldForceBanner and $forcebanner == 'Y') {
                    $removeList[] = 'b_virtualserver_modify_hostbanner';
                    $removeList[] = 'i_needed_modify_power_virtualserver_modify_hostbanner';
                } else if ($forcebanner != $oldForceBanner and $forcebanner == 'N') {
                    $addList[] = 'b_virtualserver_modify_hostbanner';
                    $addList[] = 'i_needed_modify_power_virtualserver_modify_hostbanner';
                }

                if ($forcebutton != $oldForceButton and $forcebutton == 'Y') {
                    $removeList[] = 'b_virtualserver_modify_hostbutton';
                    $removeList[] = 'i_needed_modify_power_virtualserver_modify_hostbutton';
                } else if ($forcebutton != $oldForceButton and $forcebutton == 'N') {
                    $addList[] = 'b_virtualserver_modify_hostbutton';
                    $addList[] = 'i_needed_modify_power_virtualserver_modify_hostbutton';
                }

                if ($forcewelcome != $oldForceWelcome and $forcewelcome == 'Y') {
                    $removeList[] = 'b_virtualserver_modify_welcomemessage';
                    $removeList[] = 'i_needed_modify_power_virtualserver_modify_welcomemessage';
                } else if ($forcewelcome != $oldForceWelcome and $forcewelcome == 'N') {
                    $addList[] = 'b_virtualserver_modify_welcomemessage';
                    $addList[] = 'i_needed_modify_power_virtualserver_modify_welcomemessage';
                }

                if (isset($addList)) {
                    $connection->AdminPermissions ($localServerID, 'add', $addList);
                }

                if (isset($removeList)) {
                    $connection->AdminPermissions ($localServerID, 'del', $removeList);
                }

                $rowCount += count($addList) + count($removeList);

                $loguseraction = '%mod% %voserver% ' . $ip . ':' . $port;
            }

            if (isset($localServerID) and isid($localServerID, 255)) {

                if ($active == 'N') {
                    $connection->StopServer($localServerID);
                } else if ($ui->st('action', 'post') == 'md' and $active == 'Y' and $oldActive == 'N') {
                    $connection->StartServer($localServerID);
                }

                $serverName = $ip . ':' . $port;
                $connectList = array($serverName);

                if ($masterServerData['usedns'] == 'Y') {

                    if ($ui->st('action', 'post') == 'ad' and $dns == strtolower($username . '.' . $masterServerData['defaultdns']) or $dns == $masterServerData['defaultdns']) {

                        $dns = strtolower($id . '.' . $masterServerData['defaultdns']);
                        $serverName = $dns;
                        $connectList[] = $dns;

                        $query = $sql->prepare("UPDATE `voice_server` SET `dns`=? WHERE `id`=? LIMIT 1");
                        $query->execute(array($dns, $id));

                        $rowCount += $query->rowCount();
                    }

                    if (isid($masterServerData['tsdnsServerID'], 19)) {

                        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                        $query->execute(array(':aeskey' => $aeskey,':id' => $masterServerData['tsdnsServerID'],':reseller_id' => $resellerLockupID));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            tsdns('md', $row['ssh2ip'], $row['decryptedssh2port'], $row['decryptedssh2user'], $row['publickey'], $row['keyname'], $row['decryptedssh2password'], 0, $row['serverdir'], $row['bitversion'], array($ip), array($port), array($dns), $resellerLockupID);
                        }

                    } else {
                        tsdns('md', $masterServerData['ssh2ip'], $masterServerData['decryptedssh2port'], $masterServerData['decryptedssh2user'], $masterServerData['publickey'], $masterServerData['keyname'], $masterServerData['decryptedssh2password'], 0, $masterServerData['serverdir'], $masterServerData['bitversion'], array($ip), array($port), array($dns), $resellerLockupID);
                    }
                }

                if ($ui->st('action', 'post') == 'ad') {

                    $mailConnectInfo = array(
                        'ip' => $ip,
                        'port' => $port
                    );

                    sendmail('emailserverinstall', $userID, $serverName, implode(', ', $connectList), $mailConnectInfo);
                }
            }

            $rowCount += customColumns('T', $id, 'save');

            // Check if a row was affected during insert or update
            if (isset($rowCount) and $rowCount > 0 and !isset($ts3ErrorCode)) {

                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

                // No update or insert failed
            } else {
                $template_file = (isset($ts3ErrorCode)) ? $ts3ErrorCode : $spracheResponse->error_table;
            }

            $connection->CloseConnection();
        }
    }

    // An error occurred during validation
    // unset the redirect information and display the form again
    if (!$ui->smallletters('action', 2, 'post') or count($errors) != 0) {

        unset($header, $text);

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $table = getUserList($resellerLockupID);
            $table2 = getVoiceMasterList($resellerLockupID, $admin_id);

            $template_file = 'admin_voiceserver_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id and isset($masterServerData) and isset($oldIp)) {

            $table2[$rootID] = $masterServerData['ssh2ip'] . ' ' . $masterServerData['description'];

            $server = ($masterServerData['usedns'] == 'Y' and strlen($oldDns) > 0) ? $oldDns . ' (' . $oldIp . ':' . $oldPort . ')' : $oldIp . ':' . $oldPort;

            // Check if database entry exists and if not display 404 page
            $template_file =  ($query->rowCount() > 0) ? 'admin_voiceserver_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT `ip`,`port`,`dns`,`masterserver`,`localserverid` FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $server = ($row['dns'] == null or $row['dns'] == '') ? $row['ip'] . ':' . $row['port'] : $row['dns'] . ' (' . $row['ip'] . ':' . $row['port'] . ')';
        $dns = $row['dns'];
        $ip = $row['ip'];
        $port = $row['port'];
        $rootID = $row['masterserver'];
        $localserverid = $row['localserverid'];
    }

    $serverFound = $query->rowCount();

    if ($ui->st('action', 'post') == 'dl' and count($errors) == 0 and $serverFound > 0) {

        $query = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND (`resellerid`=:reseller_id OR `managedForID`=:managedForID) LIMIT 1");
        $query->execute(array(':aeskey' => $aeskey,':id' => $rootID,':reseller_id' => $resellerLockupID,':managedForID' => $admin_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $serverdir = $row['serverdir'];
            $addedby = $row['addedby'];
            $usedns = $row['usedns'];
            $queryport = $row['queryport'];
            $querypassword = $row['decryptedquerypassword'];
            $mnotified = $row['notified'];
            $tsdnsServerID = $row['tsdnsServerID'];

            if ($addedby == 2) {

                $publickey = $row['publickey'];
                $queryip = $row['ssh2ip'];
                $ssh2port = $row['decryptedssh2port'];
                $ssh2user = $row['decryptedssh2user'];
                $ssh2password = $row['decryptedssh2password'];
                $keyname = $row['keyname'];
                $bitversion = $row['bitversion'];

            } else if ($addedby == 1) {

                $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($row['rootid'], $resellerLockupID));
                $queryip = $query->fetchColumn();
            }
        }

        if (isset($queryip) and $ui->w('safeDelete',1, 'post') != 'D') {

            $connection = new TS3($queryip, $queryport,'serveradmin', $querypassword);
            $errorcode = $connection->errorcode;

            if (isset($localserverid) and strpos($errorcode,'error id=0') !== false) {
                $connection->DelServer($localserverid);
                $errorcode = $connection->errorcode;
                $connection->CloseConnection();
            }
        }

        if (($ui->w('safeDelete',1, 'post') != 'S' or (isset($errorcode) and strpos($errorcode,'error id=0') !== false))) {

            $query = $sql->prepare("DELETE FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `affectedID`=? AND `type`='vo'");
            $query->execute(array($id));

            customColumns('T', $id,'del');

            $query = $sql->prepare("DELETE b.* FROM `voice_server_backup` b LEFT JOIN `voice_server` v ON b.`sid`=v.`id` WHERE v.`id` IS NULL");
            $query->execute();

            tsbackup('delete', $ssh2user, $serverdir, $rootID, $localserverid, '*');

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% %voserver% ' . $ip . ':' . $port;
            $insertlog->execute();

            if (isset($usedns) and $usedns == 'Y') {

                if (isset($tsdnsServerID) and isid($tsdnsServerID, 10)) {

                    $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND (`resellerid`=:reseller_id OR `managedForID`=:managedForID) LIMIT 1");
                    $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsServerID,':reseller_id' => $resellerLockupID,':managedForID' => $admin_id));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $publickey = $row['publickey'];
                        $queryip = $row['ssh2ip'];
                        $ssh2port = $row['decryptedssh2port'];
                        $ssh2user = $row['decryptedssh2user'];
                        $ssh2password = $row['decryptedssh2password'];
                        $serverdir = $row['serverdir'];
                        $keyname = $row['keyname'];
                        $bitversion = $row['bitversion'];
                    }
                }

                tsdns('dl', $queryip, $ssh2port, $ssh2user, $publickey, $keyname, $ssh2password, $mnotified, $serverdir, $bitversion, array($ip), array($port), array($dns), $resellerLockupID);
            }

        } else if ( $ui->w('safeDelete',1, 'post') == 'S' and (!isset($errorcode) or strpos($errorcode,'error id=0') === false)) {
            $template_file = (isset($errorcode)) ? 'Error: ' . $errorcode : 'Error: Could not connect to TS3 masterserver';
        } else {
            $template_file = $spracheResponse->error_table;
        }
    }

    // Nothing submitted yet or csfr error, display the delete form
    if (!$ui->st('action', 'post') or count($errors) != 0) {
        // Check if we could find an entry and if not display 404 page
        $template_file = ($serverFound > 0) ? 'admin_voiceserver_dl.tpl' : 'admin_404.tpl';
    }

// List the available entries
} else {

    configureDateTables('-1, -2, -3', '1, "asc"', 'ajax.php?w=datatable&d=voiceserver');

    $template_file = 'admin_voiceserver_list.tpl';
}