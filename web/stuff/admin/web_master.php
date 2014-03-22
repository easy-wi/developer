<?php

/**
 * File: web_master.php.
 * Author: Ulrich Block
 * Date: 02.03.14
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

if (!isset($admin_id) or $main != 1 or !isset($admin_id) or !isset($reseller_id) or !$pa['webmaster']) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$dedicatedLanguage = getlanguagefile('reseller', $user_language, $resellerLockupID);
$sprache = getlanguagefile('web', $user_language, $resellerLockupID);

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

$serverType = ($ui->w('serverType', 1, 'post') and in_array($ui->w('serverType', 1, 'post'), array('A','H','L','N','O'))) ? $ui->w('serverType', 1, 'post') : 'N';
$createDirs = ($ui->startparameter('createDirs', 'post')) ? $ui->startparameter('createDirs', 'post') : '';

if ($serverType == 'N') {

    $defaultRestartCMD = 'sudo /etc/init.d/nginx reload';
    $defaultVhostConfigPath = '/etc/nginx/sites-enabled/';

} else if ($serverType == 'A') {

    $defaultRestartCMD = 'sudo /etc/init.d/apache reload';
    $defaultVhostConfigPath = '/etc/apache/sites-enabled/';

} else if ($serverType == 'L') {

    $defaultRestartCMD = 'sudo /etc/init.d/lighttpd reload';
    $defaultVhostConfigPath = '/etc/lighttpd/sites-enabled/';

} else if ($serverType == 'H') {

    $defaultRestartCMD = 'sudo /etc/init.d/hiawatha reload';
    $defaultVhostConfigPath = '/etc/hiawatha/sites-enabled/';

} else {

    $defaultRestartCMD = 'sudo /etc/init.d/toBeReplaced reload';
    $defaultVhostConfigPath = '/etc/other/sites-enabled/';

}

$publickey = ($ui->w('publickey', 1, 'post')) ? $ui->w('publickey', 1, 'post') : 'N';
$usageType = ($ui->w('usageType', 1, 'post')) ? $ui->w('usageType', 1, 'post') : 'F';
$keyname = $ui->startparameter('keyname', 'post');
$active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
$ip = $ui->ip('ip', 'post');
$port = ($ui->port('port', 'post')) ? $ui->port('port', 'post') : 22;
$user = $ui->username('user', 255, 'post');
$pass = $ui->password('pass', 255, 'post');
$ftpIP = $ui->ip('ftpIP', 'post');
$ftpPort = ($ui->port('ftpPort', 'post')) ? $ui->port('ftpPort', 'post') : 21;
$description = $ui->escaped('description', 'post');
$maxVhost = ($ui->id('maxVhost', 10, 'post')) ? $ui->id('maxVhost', 10, 'post') : 100;
$maxHDD = ($ui->id('maxHDD', 10, 'post')) ? $ui->id('maxHDD', 10, 'post') : 10000;
$defaultdns = strtolower($ui->domain('defaultdns', 'post'));
$quotaActive = ($ui->active('quotaActive', 'post')) ? $ui->active('quotaActive', 'post') : 'N';
$quotaCmd = ($ui->startparameter('quotaCmd', 'post')) ? $ui->startparameter('quotaCmd', 'post') : 'sudo /usr/sbin/setquota %cmd%';
$blocksize = ($ui->id('blocksize', 10, 'post')) ? $ui->id('blocksize', 10, 'post') : 4096;
$inodeBlockRatio = ($ui->id('inodeBlockRatio', 10, 'post')) ? $ui->id('inodeBlockRatio', 10, 'post') : 4;
$httpdCmd = ($ui->startparameter('httpdCmd', 'post')) ? $ui->startparameter('httpdCmd', 'post') : $defaultRestartCMD;
$userGroup = ($ui->startparameter('userGroup', 'post')) ? $ui->startparameter('userGroup', 'post') : 'www-data';
$userAddCmd = ($ui->startparameter('userAddCmd', 'post')) ? $ui->startparameter('userAddCmd', 'post') : 'sudo /usr/sbin/useradd %cmd%';
$userModCmd = ($ui->startparameter('userModCmd', 'post')) ? $ui->startparameter('userModCmd', 'post') : 'sudo /usr/sbin/usermod %cmd%';
$userDelCmd = ($ui->startparameter('userDelCmd', 'post')) ? $ui->startparameter('userDelCmd', 'post') : 'sudo /usr/sbin/userdel %cmd%';
$vhostStoragePath = ($ui->startparameter('vhostStoragePath', 'post')) ? $ui->startparameter('vhostStoragePath', 'post') : '/home/';
$vhostConfigPath = ($ui->startparameter('vhostConfigPath', 'post')) ? $ui->startparameter('vhostConfigPath', 'post') : $defaultVhostConfigPath;
$vhostTemplate = $ui->escaped('vhostTemplate', 'post');

if (!$vhostTemplate or strlen($vhostTemplate) < 2) {
    if ($serverType == 'N') {
        $vhostTemplate = 'server {
    listen 80;
    server_name %url%;
    autoindex off;
    access_log %vhostpath%/%user%/logs/access.log;
    error_log %vhostpath%/%user%/logs/error.log;
    root %vhostpath%/%user%/htdocs/;
    location / {
        index index.html index.htm;
    }
}';
    } else if ($serverType == 'A') {
        $vhostTemplate = '<VirtualHost *:80>
    ServerAdmin %email%
    DocumentRoot "%vhostpath%/%user%/htdocs"
    ServerName %url%
    ErrorLog "%vhostpath%/%user%/logs/error.log"
    CustomLog "%vhostpath%/%user%/logs/access.log" common
    <Directory %vhostpath%/%user%/htdocs>
        Options -Indexes FollowSymLinks Includes
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>';
    } else if ($serverType == 'L') {
        $vhostTemplate = '$HTTP["host"] == "%url%" {
    server.document-root = "%vhostpath%/%user%/htdocs"
    server.errorlog = "%vhostpath%/%user%/logs/error.log"
    accesslog.filename = "%vhostpath%/%user%/logs/access.log"
    dir-listing.activate = "disable"
}';
    } else if ($serverType == 'H') {
        $vhostTemplate = 'VirtualHost {
    Hostname = %url%
    WebsiteRoot = %vhostpath%/%user%/htdocs
    AccessLogfile = %vhostpath%/%user%/logs/access.log
    ErrorLogfile = %vhostpath%/%user%/logs/error.log
    ShowIndex = No
}';
    } else {
        $vhostTemplate = '';
    }
}

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {

    unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_web_master_add.tpl' : 'admin_web_master_md.tpl';

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        $htmlExtraInformation['js'][] = '<script src="js/default/httpd_default_values.js" type="text/javascript"></script>';

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $template_file = 'admin_web_master_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT *,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass` FROM `webMaster` WHERE `webMasterID`=:id AND `resellerID`=:reseller_id LIMIT 1");
            $query->execute(array(':aeskey' => $aeskey,':id' => $id,':reseller_id' => $resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $active = $row['active'];
                $ip = $row['ip'];
                $port = $row['port'];
                $user = $row['decrypteduser'];
                $pass = $row['decryptedpass'];
                $description = $row['description'];
                $ftpIP = $row['ftpIP'];
                $ftpPort = $row['ftpPort'];
                $publickey = $row['publickey'];
                $keyname = $row['keyname'];
                $maxVhost = $row['maxVhost'];
                $maxHDD = $row['maxHDD'];
                $defaultdns = $row['defaultdns'];
                $quotaActive = $row['quotaActive'];
                $quotaCmd = $row['quotaCmd'];
                $usageType = $row['usageType'];
                $blocksize = $row['blocksize'];
                $inodeBlockRatio = $row['inodeBlockRatio'];
                $serverType = $row['serverType'];
                $createDirs = $row['createDirs'];
                $httpdCmd = $row['httpdCmd'];
                $userGroup = $row['userGroup'];
                $userAddCmd = $row['userAddCmd'];
                $userModCmd = $row['userModCmd'];
                $userDelCmd = $row['userDelCmd'];
                $vhostStoragePath = $row['vhostStoragePath'];
                $vhostConfigPath = $row['vhostConfigPath'];
                $vhostTemplate = $row['vhostTemplate'];
            }

            // Check if database entry exists and if not display 404 page
            $template_file = ($query->rowCount() > 0) ? 'admin_web_master_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$ip) {
            $errors['ip'] = $dedicatedLanguage->ssh_ip;
        }

        if (!$port) {
            $errors['port'] = $dedicatedLanguage->ssh_port;
        }

        if (!$user) {
            $errors['user'] = $dedicatedLanguage->ssh_user;
        }

        if (!$publickey) {
            $errors['publickey'] = $dedicatedLanguage->keyuse;
        }

        if (!$ftpPort) {
            $errors['ftpPort'] = $sprache->ftpPort;
        }

        $ssh2Check = (count($errors) == 0) ? ssh_check($ip, $port, $user, $publickey, $keyname, $pass) : true;

        if ($ssh2Check !== true) {

            if ($ssh2Check == 'ipport') {
                $errors['ip'] = $dedicatedLanguage->ssh_ip;
                $errors['port'] = $dedicatedLanguage->ssh_port;

            } else {
                $errors['user'] = $dedicatedLanguage->ssh_user;
                $errors['publickey'] = $dedicatedLanguage->keyuse;

                if ($publickey == 'N') {
                    $errors['pass'] = $dedicatedLanguage->ssh_pass;

                } else if (!$ui->active('publickey', 'post') == 'B') {
                    $errors['pass'] = $dedicatedLanguage->ssh_pass;
                    $errors['keyname'] = $dedicatedLanguage->keyname;

                } else {
                    $errors['keyname'] = $dedicatedLanguage->keyname;
                }
            }
        }

        if ($ui->st('action', 'post') == 'md' and $id) {

            $query = $sql->prepare("SELECT `active`,`vhostTemplate` FROM `webMaster` WHERE `webMasterID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $oldActive = $row['active'];
                $oldVhostTemplate = $row['vhostTemplate'];
            }

            // This should only be true in case of REQUEST data manipulation
            if (!isset($oldActive) or !isset($oldVhostTemplate)) {
                $errors['keyname'] = 'ID';
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $serverType = $row['serverType'];
                $createDirs = $row['createDirs'];
                $query = $sql->prepare("INSERT INTO `webMaster` (`active`,`ip`,`port`,`user`,`pass`,`publickey`,`keyname`,`ftpIP`,`ftpPort`,`maxVhost`,`maxHDD`,`defaultdns`,`httpdCmd`,`serverType`,`createDirs`,`vhostStoragePath`,`vhostConfigPath`,`vhostTemplate`,`quotaActive`,`quotaCmd`,`description`,`userGroup`,`userAddCmd`,`userModCmd`,`userDelCmd`,`usageType`,`blocksize`,`inodeBlockRatio`,`resellerID`) VALUES (:active,:ip,:port,AES_ENCRYPT(:user,:aeskey),AES_ENCRYPT(:pass,:aeskey),:publickey,:keyname,:ftpIP,:ftpPort,:maxVhost,:maxHDD,:defaultdns,:httpdCmd,:serverType,:createDirs,:vhostStoragePath,:vhostConfigPath,:vhostTemplate,:quotaActive,:quotaCmd,:description,:userGroup,:userAddCmd,:userModCmd,:userDelCmd,:usageType,:blocksize,:inodeBlockRatio,:resellerID)");
                $query->execute(array(':active' => $active,':ip' => $ip,':port' => $port,':aeskey' => $aeskey,':user' => $user,':pass' => $pass,':publickey' => $publickey,':keyname' => $keyname,':ftpIP' => $ftpIP, ':ftpPort' => $ftpPort,':maxVhost' => $maxVhost,':maxHDD' => $maxHDD,':defaultdns' => $defaultdns,':httpdCmd' => $httpdCmd,':serverType' => $serverType, ':createDirs' => $createDirs,':vhostStoragePath' => $vhostStoragePath,':vhostConfigPath' => $vhostConfigPath,':vhostTemplate' => $vhostTemplate,':quotaActive' => $quotaActive,':quotaCmd' => $quotaCmd,':description' => $description,':userGroup' => $userGroup,':userAddCmd' => $userAddCmd,':userModCmd' => $userModCmd,':userDelCmd' => $userDelCmd, ':usageType' => $usageType, ':blocksize' => $blocksize, ':inodeBlockRatio' => $inodeBlockRatio,':resellerID' => $resellerLockupID));

                $rowCount = $query->rowCount();
                $loguseraction = '%add% %webmaster% ' . $ip;

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                // In case the template has been changed we need to add change jobs for every vhost that uses the global template.
                if ($oldVhostTemplate != $vhostTemplate) {

                    $query = $sql->prepare("SELECT `webVhostID`,`webMasterID`,`userID`,`dns` FROM `webVhost` WHERE `webMasterID`=? AND `resellerID`=? AND `ownVhost`='N'");
                    $query2 = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('S','wv',?,?,?,?,?,NULL,NOW(),'md','',?)");

                    $query->execute(array($id, $resellerLockupID));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $query2->execute(array($admin_id, $row['webVhostID'], $row['webMasterID'], $row['userID'], $row['dns'], $resellerLockupID));
                    }

                    $query = $sql->prepare("UPDATE `webVhost` SET `vhostTemplate`=? WHERE `webMasterID`=? AND `resellerID`=? AND `ownVhost`='N'");
                    $query->execute(array($vhostTemplate, $id, $resellerLockupID));
                }

                // Update Vhosts in case active changed
                if ($oldActive != $active) {
                    $query = $sql->prepare("UPDATE `webVhost` SET `active`=? WHERE `webMasterID`=? AND `resellerID`=?");
                    $query->execute(array($active, $id, $resellerLockupID));
                }

                $query = $sql->prepare("UPDATE `webMaster` SET `active`=:active,`ip`=:ip,`port`=:port,`user`=AES_ENCRYPT(:user,:aeskey),`pass`=AES_ENCRYPT(:pass,:aeskey),`publickey`=:publickey,`keyname`=:keyname,`ftpIP`=:ftpIP,`ftpPort`=:ftpPort,`maxVhost`=:maxVhost,`maxHDD`=:maxHDD,`defaultdns`=:defaultdns,`httpdCmd`=:httpdCmd,`serverType`=:serverType,`createDirs`=:createDirs,`vhostStoragePath`=:vhostStoragePath,`vhostConfigPath`=:vhostConfigPath,`vhostTemplate`=:vhostTemplate,`quotaActive`=:quotaActive,`quotaCmd`=:quotaCmd,`description`=:description,`userGroup`=:userGroup,`userAddCmd`=:userAddCmd,`userModCmd`=:userModCmd,`userDelCmd`=:userDelCmd,`usageType`=:usageType,`blocksize`=:blocksize,`inodeBlockRatio`=:inodeBlockRatio WHERE `webMasterID`=:id AND `resellerID`=:resellerID LIMIT 1");
                $query->execute(array(':active' => $active,':ip' => $ip,':port' => $port,':aeskey' => $aeskey,':user' => $user,':pass' => $pass,':publickey' => $publickey,':keyname' => $keyname,':ftpIP' => $ftpIP, ':ftpPort' => $ftpPort,':maxVhost' => $maxVhost,':maxHDD' => $maxHDD,':defaultdns' => $defaultdns,':httpdCmd' => $httpdCmd,':serverType' => $serverType, ':createDirs' => $createDirs,':vhostStoragePath' => $vhostStoragePath,':vhostConfigPath' => $vhostConfigPath,':vhostTemplate' => $vhostTemplate,':quotaActive' => $quotaActive,':quotaCmd' => $quotaCmd,':description' => $description, ':userGroup' => $userGroup,':userAddCmd' => $userAddCmd,':userModCmd' => $userModCmd,':userDelCmd' => $userDelCmd, ':usageType' => $usageType, ':blocksize' => $blocksize, ':inodeBlockRatio' => $inodeBlockRatio,':id' => $id,':resellerID' => $resellerLockupID));

                $rowCount = $query->rowCount();
                $loguseraction = '%mod% %webmaster% ' . $ip;
            }

            // Check if a row was affected during insert or update
            if (isset($rowCount) and $rowCount > 0) {
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }

            // An error occurred during validation unset the redirect information and display the form again
        } else {

            unset($header, $text);

            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_web_master_add.tpl' : 'admin_web_master_md.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT `ip`,`description` FROM `webMaster` WHERE `webMasterID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $ip = $row['ip'];
        $description = $row['description'];
    }

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_web_master_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl') {

        $query = $sql->prepare("DELETE FROM `webMaster` WHERE `webMasterID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount() > 0) {

            $query = $sql->prepare("DELETE FROM `webVhost` WHERE `webMasterID`=? AND `resellerID`=?");
            $query->execute(array($id, $resellerLockupID));

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% %webmaster% ' . $ip;
            $insertlog->execute();

            // Nothing was deleted, display an error
        } else {
            $template_file = $spracheResponse->error_table;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'ri' and $id) {

    if (!$ui->st('action', 'post')) {

        $table = array();

        $query = $sql->prepare("SELECT `ip` FROM `webMaster` WHERE `webMasterID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        $ip = $query->fetchColumn();

        $query = $sql->prepare("SELECT `webVhostID`,`dns` FROM `webVhost` WHERE `webMasterID`=? AND `resellerID`=?");
        $query->execute(array($id, $resellerLockupID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[$row['webVhostID']] = $row['dns'];
        }

        $template_file = 'admin_web_master_ri.tpl';

    } else if ($ui->st('action', 'post') == 'ri') {

        $insertCount = 0;
        $ids = (array) $ui->id('dnsID', 10, 'post');

        $query = $sql->prepare("SELECT `userID`,`dns` FROM `webVhost` WHERE `webVhostID`=? AND `resellerID`=?");
        $query2 = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('S','wv',?,?,?,?,?,NULL,NOW(),'ri','',?)");

        foreach ($ids as $v) {

            $query->execute(array($v, $resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $query2->execute(array($admin_id, $v, $id, $row['userID'], $row['dns'], $resellerLockupID));

                $insertCount += $query2->rowCount();
            }
        }

        if ($insertCount > 0) {
            $template_file = $spracheResponse->table_add;
        } else {
            $template_file = $spracheResponse->error_table;
        }
    }

// List the available entries
} else {

    $table = array();

    $o = $ui->st('o', 'get');

    if ($ui->st('o', 'get') == 'dd') {
        $orderby = '`description` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = '`description` ASC';
    } else if ($ui->st('o', 'get') == 'dp') {
        $orderby = '`ip` DESC';
    } else if ($ui->st('o', 'get') == 'ap') {
        $orderby = '`ip` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = '`active` DESC,`notified` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = '`active` ASC,`notified` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = '`webMasterID` DESC';
    } else {
        $orderby = '`webMasterID` ASC';
        $o = 'ai';
    }

    $query = $sql->prepare("SELECT `active`,`webMasterID`,`ip`,`maxVhost`,`maxHDD`,`description` FROM `webMaster` WHERE `resellerID`=? ORDER BY " . $orderby);
    $query2 = $sql->prepare("SELECT `webVhostID`,`active`,`dns`,`hdd` FROM `webVhost` WHERE `webMasterID`=? AND `resellerID`=?");

    $query->execute(array($resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $table2 = array();
        $hddSum = 0;
        $vhostCount = 0;

        $query2->execute(array($row['webMasterID'], $resellerLockupID));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $hddSum += $row2['hdd'];
            $vhostCount++;

            $table2[] = array('id' => $row2['webVhostID'], 'active' => $row2['active'], 'dns' => $row2['dns']);
        }

        $table[] = array('id' => $row['webMasterID'], 'active' => $row['active'], 'ip' => $row['ip'], 'description' => $row['description'], 'dns' => $table2, 'maxHDD' => $hddSum . '/' . $row['maxHDD'], 'maxVhost' => $vhostCount . '/' . $row['maxVhost']);

    }

    $template_file = 'admin_web_master_list.tpl';
}