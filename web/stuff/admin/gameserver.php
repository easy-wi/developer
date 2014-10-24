<?php

/**
 * File: gameserver.php.
 * Author: Ulrich Block
 * Date: 29.05.14
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['gserver'])) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$sprache = getlanguagefile('gserver', $user_language, $resellerLockupID);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    $logsubuser = (isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller = 0;
}

// Define the ID variable which will be used at the form and SQLs
$id = $ui->id('id', 10, 'get');

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {

    unset($header, $text);

    $template_file = $spracheResponse->token;

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Add jQuery plugin chosen to the header
    $htmlExtraInformation['css'][] = '<link href="css/adminlte/chosen/chosen.min.css" rel="stylesheet" type="text/css">';
    $htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/chosen/chosen.jquery.min.js" type="text/javascript"></script>';

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    // Add or mod is opened
    if (!$ui->st('action', 'post')) {


        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $table = getUserList($resellerLockupID);
            $table2 = getAppMasterList($resellerLockupID);

            $template_file = 'admin_gserver_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT `serverip`,`port`,`port2`,`port3`,`port4`,`port5`,`userid`,`rootID`,`externalID`,`active`,`autoRestart`,`lendserver`,`eacallowed`,`slots`,`pallowed`,`pallowed`,`pallowed`,`brandname`,`war`,`tvenable`,`minram`,`maxram`,AES_DECRYPT(`ftppassword`,?) AS `ftpPassword`,(SELECT `servertype` FROM `serverlist` WHERE `id`=g.`serverid` LIMIT 1) AS `gameID`  FROM `gsswitch` AS g WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($aeskey, $id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $ip = $row['serverip'];
                $port = $row['port'];
                $port2 = ($row['port2'] == 0) ? '' : $row['port2'];
                $port3 = ($row['port3'] == 0) ? '' : $row['port3'];
                $port4 = ($row['port4'] == 0) ? '' : $row['port4'];
                $port5 = ($row['port5'] == 0) ? '' : $row['port5'];
                $userID = $row['userid'];
                $rootID = $row['rootID'];
                $externalID = $row['externalID'];
                $active = $row['active'];
                $autoRestart = $row['autoRestart'];
                $lendServer = $row['lendserver'];
                $ftpPassword = $row['ftpPassword'];
                $eacAllowed = $row['eacallowed'];
                $slots = $row['slots'];
                $protectionAllowed = $row['pallowed'];
                $brandname = $row['brandname'];
                $war = $row['war'];
                $tvEnable = $row['tvenable'];
                $minRam = $row['minram'];
                $maxRam = $row['maxram'];
                $currentGameID = $row['gameID'];
            }

            $table = getAppMasterList($resellerLockupID);

            // Check if database entry exists and if not display 404 page
            $template_file =  ($query->rowCount() > 0) ? 'admin_gserver_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        $gamesToBeRemoved = array();
        $gamesToBeInstalled = array();
        $gameDetails = array();
        $usedCores = array();
        $installedGames = array();
        $rowCount = 0;
        $technicalUser = '';
        $oldProtected = '';
        $oldIp = '';
        $oldPort = 0;
        $oldRootID = 0;
        $oldActiveGame = 0;
        $oldFtpPassword = '';
        $oldActive = '';
        $oldHomeLabel = '';

        $userID = $ui->id('userID', 10, 'post');
        $rootID = $ui->id('rootID', 10, 'post');
        $active = $ui->active('active', 'post');
        $autoRestart = $ui->active('autoRestart', 'post');
        $lendServer = $ui->active('lendserver', 'post');
        $ftpPassword = $ui->password('ftpPassword', 50, 'post');
        $eacAllowed = $ui->active('eacAllowed', 'post');
        $protectionAllowed = $ui->active('protectionAllowed', 'post');
        $brandname = $ui->active('brandname', 'post');
        $war = $ui->active('war', 'post');
        $tvEnable = $ui->active('tvEnable', 'post');
        $ip = $ui->ip4('ip', 'post');
        $port = $ui->port('port', 'post');
        $slots = ($ui->id('slots', 5, 'post')) ? $ui->id('slots', 5, 'post') : 12;
        $minRam = ($ui->id('minRam', 5, 'post')) ? $ui->id('minRam', 5, 'post') : 512;
        $maxRam = ($ui->id('maxRam', 5, 'post')) ? $ui->id('maxRam', 5, 'post') : 1024;
        $homeLabel = ($ui->username('homeDir', 255, 'post')) ? $ui->username('homeDir', 255, 'post') : 'home';
        $hdd = ($ui->id('hdd', 10, 'post')) ? $ui->id('hdd', 10, 'post') : 0;

        // Array conversion allows easier handling
        $gameIDs = (array) $ui->id('gameIDs', 10, 'post');

        // Get old data, so we can see, if shell commands need to be run
        $query = $sql->prepare("SELECT g.`serverip`,g.`homeLabel`,g.`port`,g.`pallowed`,g.`rootID`,g.`serverid`,g.`active`,AES_DECRYPT(`ftppassword`,?) AS `ftpPassword`,u.`cname` FROM `gsswitch` AS g INNER JOIN `userdata` AS u ON u.`id`=g.`userid` WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $technicalUser = $row['cname'];
            $oldProtected = $row['pallowed'];
            $oldIp = $row['serverip'];
            $oldPort = $row['port'];
            $oldRootID = $row['port'];
            $oldActiveGame = $row['serverid'];
            $oldActive = $row['active'];
            $oldFtpPassword = $row['ftpPassword'];
            $oldHomeLabel = $row['homeLabel'];
        }

        if (!$active) {
            $errors['active'] = $sprache->active;
        }

        if (!$ip) {
            $errors['ip'] = $sprache->ip;
        }

        if (!$port) {
            $errors['port'] = $sprache->port . ' 1';
        }

        // Check if IP and Port are already in use by another server daemon
        if ($ip != $oldIp or $port != $oldPort) {

            $usedPorts = usedPorts(array($ip));

            if (in_array($port, $usedPorts['ports'])) {
                $errors['ip'] = $sprache->ip;
                $errors['port'] = $sprache->port . ' 1';
            }
        }

        // Root send and allowed?
        if (!$rootID) {
            $errors['rootID'] = $sprache->root;
        } else {

            $query = $sql->prepare("SELECT `hyperthreading`,`cores`,`install_paths`,`quota_active`,`quota_cmd`,`blocksize`,`inode_block_ratio` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($rootID, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $coreCount = ($row['hyperthreading'] == 'Y') ? $row['cores'] * 2 : $row['cores'];
                $postCores = (isset($ui->post['cores'])) ? (array) $ui->post['cores'] : array();

                $quotaActive = $row['quota_active'];
                $quotaCmd = $row['quota_cmd'];
                $blockSize = $row['blocksize'];
                $inodeBlockRatio = $row['inode_block_ratio'];

                for ($c = 0; $c < $coreCount; $c++) {
                    if (in_array($c, $postCores)) {
                        $usedCores[] = $c;
                    }
                }

                // Verify that given homedir is allowed
                $iniVars = parse_ini_string($row['install_paths'], true);

                if ((!$iniVars and $homeLabel != 'home') or ($iniVars and !isset($iniVars[$homeLabel]))) {
                    $errors['homeDir'] = $sprache->homeDir;
                }
            }

            $usedCores = implode(',', $usedCores);

            if (!isset($coreCount)) {
                $errors['rootID'] = $sprache->root;
            }
        }

        // We need to check if we have gameIDs at all, and if yes, if they add up with our database
        // First check against DB and remove from array if given gameID does not add up
        // We will store the found shorten in an array to avoid another SQL at a later point
        $query = $sql->prepare("SELECT t.`shorten`,t.`modcmds`,t.`gamemod`,t.`gamemod2` FROM `rservermasterg` AS m INNER JOIN `servertypes` AS t ON t.`id`=m.`servertypeid` WHERE m.`servertypeid`=? AND m.`serverid`=? AND m.`resellerid`=? LIMIT 1");

        foreach ($gameIDs as $key => $gameID) {

            $query->execute(array($gameID, $rootID, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                // We will run the if/else case within the previous as the install logic differs from the general existence of games in the form data
                if ($ui->st('action', 'post') == 'ad') {

                    // Game is primary or all games should be installed
                    if (($ui->id('installGames', 1, 'post') == 2 and $gameID == $ui->id('primary', 10, 'post')) or $ui->id('installGames', 1, 'post') == 1) {
                        $gamesToBeInstalled[$gameID] = $row['shorten'];
                    }


                } else {
                    $gamesToBeInstalled[$gameID] = $row['shorten'];
                }

                // In case a game has mod commands, we need to get the deault, or any, if none is set
                // If we do not do this, the initial start command might fail and the customer raise a ticket
                $doNot = false;
                $modCmd = '';

                foreach (explode("\r\n", $row['modcmds']) as $line) {
                    if (preg_match('/^(\[[\w\/\.\-\_\= ]{1,}\])$/', $line)) {

                        $name = trim($line, '[]');
                        $ex = preg_split("/\=/", $name, -1, PREG_SPLIT_NO_EMPTY);

                        if (isset($ex[1]) and trim($ex[1]) == 'default') {
                            $modCmd = trim($ex[0]);
                            $doNot = true;
                        }

                        if ($doNot === false) {
                            $modCmd = trim($ex[0]);
                        }
                    }
                }

                $gameDetails[$gameID] = array('modCmd' => $modCmd, 'gamemod' => $row['gamemod'], 'gamemod2' => $row['gamemod2']);
            }

            if ($query->rowCount() == 0) {
                unset($gameIDs[$key]);
            }
        }

        // Now check if any games are left
        // A form submit without any valid game needs to be aborted
        if (count($gameIDs) == 0) {
            $errors['gameIDs'] = $sprache->games;
        }

        // User send and allowed during add server? Login name will be part of linux user name. So grab it while we can
        if ($ui->st('action', 'post') == 'ad') {
            if (!$userID) {
                $errors['userID'] = $sprache->user;
            } else {

                $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($userID, $resellerLockupID));
                $technicalUser = $query->fetchColumn();

                if (strlen($technicalUser) == 0) {
                    $errors['userID'] = $sprache->user;
                }
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // We need to check the installed games in order to know what needs to be removed
            $query = $sql->prepare("SELECT l.`id`,l.`servertype`,t.`shorten` FROM `serverlist` AS l INNER JOIN `gsswitch` AS g ON g.`id`=l.`switchID` INNER JOIN `servertypes` AS t ON t.`id`=l.`servertype` WHERE g.`id`=? AND g.`resellerid`=?");
            $query->execute(array($id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                // In case the gameID has not been submitted flag for removal
                if (!in_array($row['servertype'], $gameIDs)) {
                    $gamesToBeRemoved[$row['servertype']] = $row['shorten'];
                }

                // Avoid overhead with adding server types during modify operations
                if (isset($gamesToBeInstalled[$row['servertype']])) {

                    unset($gamesToBeInstalled[$row['servertype']]);
                    unset($gameDetails[$row['servertype']]);

                    $installedGames[] = $row['id'];
                }
            }

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `gsswitch` (`active`,`hdd`,`taskset`,`cores`,`userid`,`pallowed`,`eacallowed`,`lendserver`,`serverip`,`rootID`,`homeLabel`,`tvenable`,`port`,`port2`,`port3`,`port4`,`port5`,`minram`,`maxram`,`slots`,`war`,`brandname`,`autoRestart`,`ftppassword`,`resellerid`,`serverid`,`stopped`,`externalID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,AES_ENCRYPT(?,?),?,1,'Y',?)");
                $query->execute(array($active, $hdd, $ui->active('taskset', 'post'), $usedCores, $userID, $protectionAllowed, $eacAllowed, $lendServer, $ip, $rootID, $homeLabel, $tvEnable, $port, $ui->port('port2', 'post'), $ui->port('port3', 'post'), $ui->port('port4', 'post'), $ui->port('port5', 'post'), $minRam, $maxRam, $slots, $war, $brandname, $autoRestart, $ftpPassword, $aeskey, $resellerLockupID, $ui->externalID('externalID', 'post')));

                $id = $sql->lastInsertId();

                $rowCount += $query->rowCount();

                $loguseraction = '%add% %gserver% ' . $ip . ':' . $port;

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                // We need to correct the active game in case the current one is not existing anymore
                if ($oldActiveGame == null or !in_array($oldActiveGame, $installedGames) or isset($gamesToBeRemoved[$oldActiveGame])) {
                    reset($installedGames);
                    $currentActiveGame = (isset($insertedServerIDs[0])) ? $insertedServerIDs[0] : current($installedGames);
                } else {
                    $currentActiveGame = $oldActiveGame;
                }

                $query = $sql->prepare("UPDATE `gsswitch` SET `active`=?,`hdd`=?,`taskset`=?,`cores`=?,`pallowed`=?,`eacallowed`=?,`lendserver`=?,`serverip`=?,`homeLabel`=?,`tvenable`=?,`port`=?,`port2`=?,`port3`=?,`port4`=?,`port5`=?,`minram`=?,`maxram`=?,`slots`=?,`war`=?,`brandname`=?,`autoRestart`=?,`ftppassword`=AES_ENCRYPT(?,?),`serverid`=?,`externalID`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($active, $hdd, $ui->active('taskset', 'post'), $usedCores, $protectionAllowed, $eacAllowed, $lendServer, $ip, $homeLabel, $tvEnable, $port, $ui->port('port2', 'post'), $ui->port('port3', 'post'), $ui->port('port4', 'post'), $ui->port('port5', 'post'), $minRam, $maxRam, $slots, $war, $brandname, $autoRestart, $ftpPassword, $aeskey, $currentActiveGame, $ui->externalID('externalID', 'post'), $id, $resellerLockupID));

                $rowCount += $query->rowCount();

                $loguseraction = '%mod% %gserver% ' . $ip . ':' . $port;
            }

            // Insert new games
            $query = $sql->prepare("INSERT INTO `serverlist` (`servertype`,`anticheat`,`switchID`,`fps`,`map`,`mapGroup`,`cmd`,`modcmd`,`owncmd`,`tic`,`gamemod`,`gamemod2`,`userfps`,`usertick`,`usermap`,`user_uploaddir`,`upload`,`uploaddir`,`resellerid`) VALUES (?,1,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,AES_ENCRYPT(?,?),?)");

            foreach ($gameDetails as $gameID => $gameValues) {

                $query->execute(array($gameID, $id, $ui->id('fps', 6, 'post', $gameID), $ui->mapname('map', 'post', $gameID), $ui->mapname('mapGroup', 'post', $gameID), $ui->startparameter('cmd', 'post', $gameID), $gameValues['modCmd'], $ui->active('ownCmd', 'post', $gameID), $ui->id('tic', 5, 'post', $gameID), $gameValues['gamemod'], $gameValues['gamemod2'], $ui->active('userFps', 'post', $gameID), $ui->active('userTick', 'post', $gameID), $ui->active('userMap', 'post', $gameID), $ui->active('userUploadDir', 'post', $gameID), $ui->id('upload', 1, 'post', $gameID), $ui->url('uploadDir', 'post', $gameID), $aeskey, $resellerLockupID));

                $insertedServerIDs[] = $sql->lastInsertId();

                $rowCount += $query->rowCount();
            };

            // Updating the serverlist if not set to be added, we need to run the update
            $query = $sql->prepare("UPDATE `serverlist` SET `fps`=?,`map`=?,`mapGroup`=?,`cmd`=?,`owncmd`=?,`tic`=?,`userfps`=?,`usertick`=?,`usermap`=?,`user_uploaddir`=?,`upload`=?,`uploaddir`=AES_ENCRYPT(?,?) WHERE `switchID`=? AND `servertype`=? AND `resellerid`=? LIMIT 1");

            foreach ($gameIDs as $gameID) {
                if (!isset($gameDetails[$gameID])) {

                    $query->execute(array($ui->id('fps', 6, 'post', $gameID), $ui->mapname('map', 'post', $gameID), $ui->mapname('mapGroup', 'post', $gameID), $ui->startparameter('cmd', 'post', $gameID), $ui->active('ownCmd', 'post', $gameID), $ui->id('tic', 5, 'post', $gameID), $ui->active('userFps', 'post', $gameID), $ui->active('userTick', 'post', $gameID), $ui->active('userMap', 'post', $gameID), $ui->active('userUploadDir', 'post', $gameID), $ui->id('upload', 1, 'post', $gameID), $ui->url('uploadDir', 'post', $gameID), $aeskey, $id, $gameID, $resellerLockupID));

                    $rowCount += $query->rowCount();
                }
            }

            // If a servertype has been added, update gsswitch, so joins add up
            if ($ui->st('action', 'post') == 'ad' and isset($insertedServerIDs) and count($insertedServerIDs) > 0)  {

                $query = $sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array((in_array($ui->id('primary', 10, 'post'), $insertedServerIDs)) ? $ui->id('primary', 10, 'post') : $insertedServerIDs[0], $id, $resellerLockupID));

                $rowCount += $query->rowCount();

                // Else something went wrong and we need to clean up and give an error
            } else if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("DELETE FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id, $resellerLockupID));

                // Setting $rowCount to 0 will result in DB insert error shown to user
                $rowCount = 0;
            }

            // Delete deselected games
            $query = $sql->prepare("DELETE FROM `serverlist` WHERE `servertype`=? AND `switchID`=? AND `resellerid`=? LIMIT 1");

            foreach ($gamesToBeRemoved as $gameID => $shorten) {

                $query->execute(array($gameID, $id, $resellerLockupID));

                $rowCount += $query->rowCount();
            }

            // If not checked like this we might get true for general server insert in add cases, when it failed
            if (($ui->st('action', 'post') == 'ad' and $rowCount > 0) or $ui->st('action', 'post') == 'md')  {

                // customColumns will return amount of changed columns
                $rowCount += customColumns('G', $id, 'save');
            }

            // Check if a row was affected during insert or update
            if ($rowCount > 0) {

                $gamesRemoveAmount = count($gamesToBeRemoved);
                $gamesAmount = count($gamesToBeInstalled);
                $homeDir = (isset($iniVars[$homeLabel]['path'])) ? $iniVars[$homeLabel]['path'] : '/home';

                // We will run the add user command in nearly any case
                // Reasons are that we ensure FTP password correctness and existence of linux user
                // Also we will add the protected user with variable 5
                if ($homeLabel != $oldHomeLabel or $oldFtpPassword != $ftpPassword or $oldActive != $active or $ip != $oldIp or $port != $oldPort or $oldProtected != $protectionAllowed or $gamesRemoveAmount > 0 or $gamesAmount > 0) {
                    $addProtectedUser = ($protectionAllowed == 'Y') ? passwordgenerate(10) : '';
                    $cmds[] = "./control.sh useradd {$technicalUser}-{$id} {$ftpPassword} {$homeDir} {$addProtectedUser}";
                }

                if ($quotaActive == 'Y' and strlen($quotaCmd) > 0 and $hdd > 0) {

                    // setquota works with KibiByte and Inodes; Stored is Megabyte
                    $sizeInKibiByte = $hdd * 1024;
                    $sizeInByte = $hdd * 1048576;
                    $blockAmount = round(($sizeInByte /$blockSize));
                    $inodeAmount = round($blockAmount / $inodeBlockRatio);
                    $mountPoint = (isset($iniVars[$homeLabel]['mountpoint'])) ? $iniVars[$homeLabel]['mountpoint'] : $homeDir;

                    $cmds[] = 'q() { ' . str_replace('%cmd%', " -u {$technicalUser}-{$id} {$sizeInKibiByte} {$sizeInKibiByte} {$inodeAmount} {$inodeAmount} {$mountPoint}", $quotaCmd) . ' > /dev/null 2>&1; }; q&';
                }

                if ($ui->st('action', 'post') == 'md' and ($oldFtpPassword != $ftpPassword or $oldActive != $active or $homeLabel != $oldHomeLabel)) {

                    if ($oldActive == 'Y' and $active == 'N') {
                        $ftpPassword = passwordgenerate(10);
                    }

                    $cmds[] = "./control.sh usermod {$technicalUser}-{$id} {$ftpPassword} {$homeDir}";
                }

                // Send delete request for protected user in case it has been removed from server
                if ($ui->st('action', 'post') == 'md' and $protectionAllowed == 'N' and $oldProtected == 'Y') {
                    $cmds[] = "./control.sh delSingleUser {$technicalUser}-{$id}-p";
                }

                if ($ui->st('action', 'post') == 'md' and (($oldActive == 'Y' and $active == 'N') or $ip != $oldIp or $port != $oldPort)) {

                    $stopCmds = gsrestart($id, 'so', $aeskey, $resellerLockupID);

                    if (is_array($stopCmds)) {
                        foreach ($stopCmds as $cmd) {
                            if (strpos($cmd, 'addserver') === false)  {
                                $cmds[] = $cmd;
                            }
                        }
                    }
                }

                // Remove if games got deselected. Cannot happen during gameserver adding
                if ($gamesRemoveAmount > 0) {

                    $loguseraction .= ', %del%: ' . implode(', ', $gamesToBeRemoved);

                    $gamesRemoveString = $gamesRemoveAmount . '_' . implode('_', $gamesToBeRemoved);

                    $cmds[] = "sudo -u {$technicalUser}-{$id} ./control.sh delserver {$technicalUser}-{$id} {$gamesRemoveString} {$oldIp}_{$oldPort} unprotected {$homeDir}";
                }

                // Admin has changed the ip or the main port. Now we need to move the server. Can only happen during server edit.
                // Should be done after possible deletes and before we add additional data
                if ($ui->st('action', 'post') == 'md' and  ($ip != $oldIp or $port != $oldPort)) {
                    $cmds[] = "sudo -u {$technicalUser}-{$id} ./control.sh ip_port_change {$technicalUser}-{$id} {$oldIp}_{$oldPort} {$ip}_{$port} {$homeDir}";
                }


                if ($gamesAmount > 0) {

                    $loguseraction .= ', %add%: ' . implode(', ', $gamesToBeInstalled);

                    $gamesAddString = $gamesAmount . '_' . implode('_', $gamesToBeInstalled);

                    $limitInstall = ($ui->id('installGames', 1, 'post') == 2) ? 1 : '';

                    $cmds[] = "sudo -u {$technicalUser}-{$id} ./control.sh addserver {$technicalUser}-{$id} {$gamesAddString} {$ip}_{$port} {$limitInstall} {$homeDir}";
                }

                $insertlog->execute();

                $template_file = $spracheResponse->table_add;

                if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1 and isset($cmds) and count($cmds) > 0) {
                    $template_file .= '<br><pre>' . implode("\r\n", $cmds) . "\r\n" . ssh2_execute('gs', $rootID, $cmds) . '</pre>';
                }

                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }

            // An error occurred during validation unset the redirect information and display the form again
        } else {

            unset($header, $text);

            $table = getUserList($resellerLockupID);
            $table2 = getAppMasterList($resellerLockupID);

            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_gserver_add.tpl' : 'admin_gserver_md.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT g.`id`,g.`serverip`,g.`port`,g.`newlayout`,g.`rootID`,u.`cname`,u.`vname`,u.`name`,u.`mail` FROM `gsswitch` AS g LEFT JOIN `userdata` AS u ON u.`id`=g.`userid` WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $ip = $row['serverip'];
        $port = $row['port'];
        $rootID = $row['rootID'];

        $user = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name'] . ' (' . $row['mail'] . ')');
        $gameServerUser = ($row['newlayout'] == 'Y') ? $row['cname'] . '-' . $row['id'] : $row['cname'];

        // If set to "D" the user requested to remove from only from DB
        if ($ui->w('safeDelete', 1, 'post') and $ui->w('safeDelete', 1, 'post') != 'D') {

            $cmds = gsrestart($id, 'so', $aeskey, $resellerLockupID);

            $cmds[] = "sudo -u {$gameServerUser} ./control.sh delscreen {$gameServerUser}" ;
            $cmds[] = "sudo -u {$gameServerUser}-p ./control.sh delscreen {$gameServerUser}-p";
            $cmds[] = "./control.sh delCustomer {$gameServerUser}";
        }
    }

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_gserver_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl') {

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount() > 0 and isset($rootID)) {

            $return = true;

            if (isset($cmds) and count($cmds) > 1) {

                // Unset the add command generated by the restart function
                unset($cmds[0]);

                $return = ssh2_execute('gs', $rootID, $cmds);
            }

            if (($return !== false and $ui->w('safeDelete', 1, 'post') == 'S') or in_array($ui->w('safeDelete', 1, 'post'), array('A', 'D'))) {

                // Wenn the gsswitch entry is removed, we cannot resolve the rest of the mappings.
                // Left Loins will result NULL and we can use for deleting

                $query = $sql->prepare("DELETE FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id, $resellerLockupID));

                $query = $sql->prepare("DELETE s.* FROM `serverlist` s LEFT JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE g.`id` IS NULL");
                $query->execute();

                $query = $sql->prepare("DELETE a.* FROM `addons_installed` a LEFT JOIN `serverlist` s ON a.`serverid`=s.`id` WHERE s.`id` IS NULL");
                $query->execute();

                $query = $sql->prepare("DELETE a.* FROM `addons_installed` a LEFT JOIN `userdata` u ON a.`userid`=u.`id` WHERE u.`id` IS NULL");
                $query->execute();

                $query = $sql->prepare("DELETE FROM `gserver_restarts` WHERE `switchID`=? AND `resellerid`=?");
                $query->execute(array($id, $resellerLockupID));

                customColumns('G', $id, 'del');
            }

            $loguseraction = '%del% %gserver% ' . $ip . ':' . $port;
            $insertlog->execute();

            $template_file = $spracheResponse->table_del;

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1 and isset($cmds)) {
                $template_file .= '<br><pre>' . implode("\r\n", $cmds) . "\r\n" . $return . '</pre>';
            }

            // Nothing was deleted, display an error
        } else {
            $template_file = $spracheResponse->error_table;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

// Gameserver Reinstall
} else if ($ui->st('d', 'get') == 'ri' and $id) {

    if (!$ui->w('action', 4, 'post')) {

        $table = array();
        $shorten = '';

        $query = $sql->prepare("SELECT `serverip`,`port`,`serverid` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query2 = $sql->prepare("SELECT s.`id`,s.`servertemplate`,t.`shorten`,t.`description` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
        $query->execute(array($id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $serverip = $row['serverip'];
            $port = $row['port'];

            $query2->execute(array($id, $resellerLockupID));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                if (strlen($shorten) == 0) {
                    $shorten = $row2['shorten'];
                }

                $servertemplate = ($row['serverid'] == $row2['id']) ? $row2['servertemplate'] : '';

                $table[] = array('id' => $row2['id'], 'description' => $row2['description'], 'shorten' => $row2['shorten'], 'servertemplate' => $servertemplate);
            }
        }

        $template_file = (isset($serverip) and isset($port)) ? 'admin_gserver_ri.tpl' : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'ri') {

        $i = 0;
        $gamestring = array();
        $template = array();

        $query = $sql->prepare("SELECT AES_DECRYPT(g.`ftppassword`,?) AS `cftppass`,AES_DECRYPT(g.`ppassword`,?) AS `pftppass`,g.`id`,g.`newlayout`,g.`rootID`,g.`serverip`,g.`port`,g.`pallowed`,g.`protected`,g.`homeLabel`,u.`cname`,r.`install_paths` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` INNER JOIN `rserverdata` r ON r.`id`=g.`rootID` WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $aeskey, $ui->id('id', 10, 'get'), $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $customer = $row['cname'];
            $ftppass = ($row['pallowed'] == 'Y' and $row['protected'] == 'Y') ? $row['pftppass'] : $row['cftppass'];
            $rootID = $row['rootID'];
            $serverip = $row['serverip'];
            $port = $row['port'];
            $gsfolder = $serverip . '_' . $port;

            $addProtectedUser = ($row['pallowed'] == 'Y') ? passwordgenerate(10) : '';

            if ($row['newlayout'] == 'Y') {
                $customer = $customer . '-' . $row['id'];
            }

            $iniVars = parse_ini_string($row['install_paths'], true);

            $homeDir = ($iniVars and isset($iniVars[$row['homeLabel']]['path'])) ? $iniVars[$row['homeLabel']]['path'] : '/home';
        }

        # https://github.com/easy-wi/developer/issues/69
        $game = $ui->id('game',10, 'post');
        $template = (in_array($ui->id('template', 10, 'post'), array(1, 2, 3, 4))) ? $ui->id('template', 10, 'post') : 4;

        if ($ui->active('type', 'post') == 'Y') {
            $query = $sql->prepare("DELETE FROM `addons_installed` WHERE `serverid`=? AND `resellerid`=?");
            $query->execute(array($game, $reseller_id));
        }

        $query = $sql->prepare("SELECT s.`gamemod`,s.`gamemod2`,t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
        $query->execute(array($game, $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $shorten = $row['shorten'];
            $gamemod2 = $row['gamemod2'];
            $gamestring[]=($row['gamemod'] == 'Y') ? $shorten . $gamemod2 : $shorten;
        }

        if (isset($gsfolder) and count($gamestring) > 0 and $ui->active('type', 'post')) {

            $cmds = array();

            $gamestring = count($gamestring) . '_' . implode('_',$gamestring);

            if ($ui->active('type', 'post') == 'Y') {

                $cmds[] = "./control.sh useradd {$customer} {$ftppass} {$homeDir} {$addProtectedUser}";
                $cmds[] = "sudo -u {$customer} ./control.sh reinstserver {$customer} {$gamestring} ${gsfolder} \"${template}\" {$homeDir}";

                $loguseraction = "%reinstall% %gserver% ${serverip}:${port}";

            } else {

                $cmds[] = "sudo -u {$customer} ./control.sh addserver {$customer} {$gamestring} {$gsfolder} \"{$template}\" {$homeDir}";

                $loguseraction = "%resync% %gserver% {$serverip}:{$port}";

            }

            $return = ssh2_execute('gs', $rootID, $cmds);

            $template_file = $sprache->server_installed;

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<br><pre>' . implode("\r\n", $cmds) . "\r\n" . $return . '</pre>';
            }

            $insertlog->execute();

        } else {
            $template_file = 'admin_404.tpl';
        }

    } else {
        $template_file = 'admin_404.tpl';
    }

// Gameserver Restart
} else if (in_array($ui->st('d', 'get'), array('rs','st','du')) and $ui->id('id', 10, 'get')) {

    $query = $sql->prepare("SELECT `serverip`,`port`,`rootID` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $gsip = $row['serverip'];
        $port = $row['port'];
        $port = $row['port'];
        $rootID = $row['rootID'];
    }

    if ($query->rowCount() > 0) {

        if ($ui->st('d', 'get') == 'rs') {

            $template_file = $sprache->serverrestart;
            $cmds = gsrestart($id, 're', $aeskey, $resellerLockupID);
            $loguseraction = '%start% %gserver% ' . $gsip . ':' . $port;

        } else if ($ui->st('d', 'get') == 'st') {

            $template_file = $sprache->serverstop;
            $cmds = gsrestart($id, 'so', $aeskey, $resellerLockupID);
            $loguseraction = '%stop% %gserver% ' . $gsip . ':' . $port;
        }

        if (isset($cmds) and is_array($cmds) and count($cmds) > 0) {

            $return = ssh2_execute('gs', $rootID, $cmds);

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<br><pre>' . implode("\r\n", $cmds) . "\r\n" . $return . '</pre>';
            }
        }

        $insertlog->execute();

    } else {
        $template_file = 'admin_404.tpl';
    }

// List the available entries
} else {

    configureDateTables('-1, -2', '0, "asc"', 'ajax.php?w=datatable&d=gameserver');

    $template_file = 'admin_gserver_list.tpl';
}