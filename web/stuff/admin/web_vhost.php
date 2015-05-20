<?php

/**
 * File: web_vhost.php.
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

if (!isset($admin_id) or $main != 1 or !isset($admin_id) or !isset($reseller_id) or !$pa['webvhost']) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_httpd.php');

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
$externalID = $ui->externalID('externalID', 'post');
$id = $ui->id('id', 10, 'get');
$webMasterID = $ui->id('webMasterID', 10, 'post');
$userID = $ui->id('userID', 10, 'post');
$active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
$hdd = ($ui->id('hdd', 10, 'post')) ? $ui->id('hdd', 10, 'post') : 1000;
$ftpPassword = ($ui->password('ftpPassword', 255, 'post')) ? $ui->password('ftpPassword', 255, 'post') : passwordgenerate(10);
$vhostTemplate = $ui->escaped('vhostTemplate', 'post');
$ownVhost = ($ui->active('ownVhost', 'post')) ? $ui->active('ownVhost', 'post') : 'N';

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {

    unset($header, $text);

    $tokenError = true;

    $errors = array($spracheResponse->token);
}

// Add and modify entries. Same validation can be used.
if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Add jQuery plugin chosen to the header
    $htmlExtraInformation['css'][] = '<link href="css/default/chosen/chosen.min.css" rel="stylesheet" type="text/css">';
    $htmlExtraInformation['js'][] = '<script src="js/default/plugins/chosen/chosen.jquery.min.js" type="text/javascript"></script>';

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        $table = array();
        $table2 = array();

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            // Get useraccounts
            $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' AND `active`='Y' ORDER BY `id` DESC");
            $query->execute(array($resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $table[$row['id']] = trim($row['cname'] . ' ' . trim($row['vname'] . ' ' . $row['name']));
            }

            // Get masterserver. Sort by usage.
            $query = $sql->prepare("SELECT m.`webMasterID`,m.`ip`,m.`description`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(m.`maxVhost`/100) AS `percentVhostUsage`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(IF(m.`hddOverbook`='Y',(m.`maxHDD`/100) * (100+m.`overbookPercent`),`maxHDD`)/100) AS `percentHDDUsage` FROM `webMaster` AS m WHERE m.`active`='Y' AND m.`resellerID`=? GROUP BY m.`webMasterID` HAVING (`percentVhostUsage`<100 OR `percentVhostUsage`IS NULL) AND (`percentHDDUsage`<100 OR `percentHDDUsage`IS NULL) ORDER BY `percentHDDUsage` ASC,`percentVhostUsage` ASC");
            $query->execute(array($resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $table2[$row['webMasterID']] = trim($row['ip'] . ' ' . $row['description']);
            }

            $template_file = 'admin_web_vhost_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT v.*,AES_DECRYPT(v.`ftpPassword`,?) AS `decryptedFTPPass`,u.`cname`,u.`vname`,u.`name` FROM `webVhost` AS v INNER JOIN `userdata` AS u ON u.`id`=v.`userID` WHERE v.`webVhostID`=? AND v.`resellerID`=? LIMIT 1");
            $query->execute(array($aeskey, $id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                // Userdata from JOIN, trim in case attributes are not provided
                $table = array($row['userID'] => trim($row['cname'] . ' ' . trim($row['vname'] . ' ' . $row['name'])));

                // Vhost data
                $externalID = $row['externalID'];
                $userID = $row['userID'];
                $active = $row['active'];
                $hdd = $row['hdd'];
                $hddUsage = (int) $row['hddUsage'];
                $ftpPassword = $row['decryptedFTPPass'];
                $dns = 'web-' . $id;

                // Get masterserver. Sort by usage.
                $query2 = $sql->prepare("SELECT m.`ip`,m.`description` FROM `webMaster` AS m WHERE m.`active`='Y' AND m.`webMasterID`=? AND m.`resellerID`=?");
                $query2->execute(array($row['webMasterID'], $resellerLockupID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $table2[$row['webMasterID']] = trim($row2['ip'] . ' ' . $row2['description']);
                }
            }

            // Check if database entry exists and if not display 404 page
            $template_file = ($query->rowCount() > 0) ? 'admin_web_vhost_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        $domainConfigurations = array();

        if (!$active) {
            $errors['active'] = $dedicatedLanguage->active;
        }

        if (!$ftpPassword) {
            $errors['ftpPassword'] = $sprache->ftpPassword;
        }

        $oldHDD = 0;

        // Only at ADD user and masterserver can be defined. We need to check if they exist
        if ($ui->st('action', 'post') == 'ad') {

            if ($userID) {

                $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($userID, $resellerLockupID));

                if ($query->rowCount() < 1) {
                    $errors['userID'] = $dedicatedLanguage->user;
                }

            } else {
                $errors['userID'] = $dedicatedLanguage->user;
            }

        } else {

            $query = $sql->prepare("SELECT `webMasterID`,`active`,`hdd`,`userID`,AES_DECRYPT(`ftpPassword`,?) AS `decryptedFTPPass` FROM `webVhost` WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($aeskey, $id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $webMasterID = $row['webMasterID'];
                $oldHDD = $row['hdd'];
                $oldActive = $row['active'];
                $oldUserID = $row['userID'];
                $oldFtpPassword = $row['decryptedFTPPass'];
            }
        }

        $phpConfiguration = array();
        $phpConfigurationMaster = array();

        if ($webMasterID) {

            $maxHDD = 0;

            $query = $sql->prepare("SELECT `defaultdns`,`vhostTemplate`,`phpConfiguration`,IF(`hddOverbook`='Y',(`maxHDD`/100) * (100+`overbookPercent`),`maxHDD`) AS `maxHDD` FROM `webMaster` WHERE `webMasterID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($webMasterID, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $defaultDns = $row['defaultdns'];
                $defaultVhostTemplate = $row['vhostTemplate'];
                $maxHDD = (int) $row['maxHDD'];
                $phpConfigurationMaster = @parse_ini_string($row['phpConfiguration'], true, INI_SCANNER_RAW);
            }

            if ($query->rowCount() == 0) {
                $errors['webMasterID'] = $gsprache->master;
            }

            $query = $sql->prepare("SELECT SUM(`hdd`) AS `a` FROM `webVhost` WHERE `webMasterID`=? AND `resellerID`=?");
            $query->execute(array($id, $resellerLockupID));

            if (($maxHDD + $oldHDD - $query->fetchColumn() - $hdd) < 0) {
                $errors['hdd'] = $sprache->hdd;
            }

        } else {
            $errors['webMasterID'] = $gsprache->master;
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            foreach ($phpConfigurationMaster as $groupName => $array) {

                $groupNameSelect = $ui->escaped(str_replace(' ', '', $groupName), 'post');

                if ($groupNameSelect and isset($phpConfigurationMaster[$groupName][$groupNameSelect])) {
                    $phpConfiguration[$groupName] = $groupNameSelect;
                } else {
                    reset($phpConfigurationMaster[$groupName]);
                    $phpConfiguration[$groupName] = key($phpConfigurationMaster[$groupName]);
                }
            }

            $phpConfiguration = @json_encode($phpConfiguration);

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `webVhost` (`webMasterID`,`userID`,`active`,`hdd`,`ftpPassword`,`phpConfiguration`,`externalID`,`resellerID`) VALUES (?,?,?,?,AES_ENCRYPT(?,?),?,?,?)");
                $query->execute(array($webMasterID, $userID, $active, $hdd, $ftpPassword, $aeskey, $phpConfiguration, $externalID, $resellerLockupID));

                $id = (int) $sql->lastInsertId();

                $ftpUser = 'web-' . $id;

                $query = $sql->prepare("UPDATE `webVhost` SET `ftpUser`=? WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($ftpUser, $id, $resellerLockupID));

                $rowCount = $query->rowCount();
                $loguseraction = '%add% %webvhost% ' . $ftpUser;

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                $ftpUser = 'web-' . $id;

                $query = $sql->prepare("UPDATE `webVhost` SET `active`=?,`hdd`=?,`ftpPassword`=AES_ENCRYPT(?,?),`phpConfiguration`=?,`externalID`=? WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($active, $hdd, $ftpPassword, $aeskey, $phpConfiguration, $externalID, $id, $resellerLockupID));

                // Needs to be set for domain inserts
                $userID = $oldUserID;

                $rowCount = $query->rowCount();
                $loguseraction = '%mod% %webvhost% ' . $ftpUser;
            }

            $domainRemove = array();

            $query = $sql->prepare("SELECT `domain` FROM `webVhostDomain` WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=?");
            $query->execute(array($id, $userID, $resellerLockupID));
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $domainRemove[$row['domain']] = $row['domain'];
            }

            $domains = $ui->domain('domain', 'post');

            if ($domains) {

                $paths = $ui->path('path', 'post');
                $ownVhosts = $ui->active('ownVhost', 'post');
                $vhostTemplates = $ui->escaped('vhostTemplate', 'post');

                foreach($domains as $index => $domain) {

                    if ($defaultDns == $domain) {
                        $domain = str_replace('..', '.', $ftpUser . '.' . $defaultDns);
                    }

                    $path = (property_exists($paths, $index)) ? $paths->$index : '';
                    $ownVhost = (property_exists($ownVhosts, $index)) ? $ownVhosts->$index : 'N';
                    $vhostTemplate = (property_exists($vhostTemplates, $index)) ? $vhostTemplates->$index : $defaultVhostTemplate;

                    // Check for file traversal and similar
                    $path = str_replace('..', '', $path);

                    while (strpos($path, './') !== false) {
                        $path = str_replace('./', '/', $path);
                    }

                    while (strpos($path, '//') !== false) {
                        $path = str_replace('//', '/', $path);
                    }

                    while (substr($path, 0, 1) == '/') {
                        $path = substr($path, 1);
                    }

                    $domainConfigurations[$domain] = array(
                        'path' => $path,
                        'ownVhost' => $ownVhost,
                        'vhostTemplate' => $vhostTemplate
                    );
                }
            }

            // Insert/Delete of the domain configuration(s) Unique key is the domain
            if (count($domainConfigurations) == 0) {
                $domainConfigurations[str_replace('..', '.', $ftpUser . '.' . $defaultDns)] = '';
            }

            $query = $sql->prepare("INSERT INTO `webVhostDomain` (`webVhostID`,`userID`,`resellerID`,`domain`,`path`,`ownVhost`,`vhostTemplate`) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `path`=VALUES(`path`),`ownVhost`=VALUES(`ownVhost`),`vhostTemplate`=VALUES(`vhostTemplate`)");
            foreach($domainConfigurations as $domain => $path) {

                $query->execute(array($id, $userID, $resellerLockupID, $domain, $path['path'], $path['ownVhost'], $path['vhostTemplate']));
                $rowCount += $query->rowCount();

                unset($domainRemove[$domain]);
            }

            if (count($domainRemove) > 0) {
                $query = $sql->prepare("DELETE FROM `webVhostDomain` WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=? AND `domain` IN('" . implode("','", $domainRemove) . "')");
                $query->execute(array($id, $userID, $resellerLockupID));
                $rowCount += $query->rowCount();
            }

            // Check if a row was affected during insert or update
            if (isset($rowCount) and $rowCount > 0) {

                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

                $vhostObject = new HttpdManagement($webMasterID, $resellerLockupID);

                if ($vhostObject != false and $vhostObject->ssh2Connect() and $vhostObject->sftpConnect()) {

                    if ($ui->st('action', 'post') == 'ad') {

                        $vhostObject->vhostCreate($id);

                    } else {

                        if ($oldActive == 'Y' and $oldActive != $active) {

                            $vhostObject->setInactive($id);

                        } else if ($oldFtpPassword != $ftpPassword) {

                            $vhostObject->changePassword($id, $ftpPassword);

                        } else {

                            $vhostObject->vhostMod($id, $domainRemove);

                        }
                    }

                    $vhostObject->restartHttpdServer();
                }

                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }

            // An error occurred during validation unset the redirect information and display the form again
        } else {

            unset($header, $text);

            if ($ui->st('d', 'get') == 'ad') {

                $table = array();
                $table2 = array();

                // Get useraccounts
                $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' AND `active`='Y' ORDER BY `id` DESC");
                $query->execute(array($resellerLockupID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $table[$row['id']] = trim($row['cname'] . ' ' . trim($row['vname'] . ' ' . $row['name']));
                }

                // Get masterserver. Sort by usage.
                $query = $sql->prepare("SELECT m.`webMasterID`,m.`ip`,m.`description`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(m.`maxVhost`/100) AS `percentVhostUsage`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(IF(m.`hddOverbook`='Y',(m.`maxHDD`/100) * (100+m.`overbookPercent`),`maxHDD`)/100) AS `percentHDDUsage` FROM `webMaster` AS m WHERE m.`active`='Y' AND m.`resellerID`=? GROUP BY m.`webMasterID` HAVING (`percentVhostUsage`<100 OR `percentVhostUsage`IS NULL) AND (`percentHDDUsage`<100 OR `percentHDDUsage`IS NULL) ORDER BY `percentHDDUsage` ASC,`percentVhostUsage` ASC");
                $query->execute(array($resellerLockupID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $table2[$row['webMasterID']] = trim($row['ip'] . ' ' . $row['description']);
                }

                $template_file = 'admin_web_vhost_add.tpl';

            } else {
                $template_file = 'admin_web_vhost_md.tpl';
            }
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if (!isset($tokenError) and $ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT v.`webMasterID`,u.`cname`,u.`vname`,u.`name` FROM `webVhost` AS v LEFT JOIN `userdata` AS u ON v.`userID`=u.`id` WHERE v.`webVhostID`=? AND v.`resellerID`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $webMasterID = $row['webMasterID'];
        $user = trim($row['cname'] . ' ' . trim($row['vname'] . ' ' . $row['name']));
    }

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post') and isset($user)) {

        $dns = 'web-' . $id;

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_web_vhost_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl' and isset($user)) {

        $vhostObject = new HttpdManagement($webMasterID, $resellerLockupID);

        if ($vhostObject != false and $vhostObject->ssh2Connect() and $vhostObject->sftpConnect()) {
            $vhostObject->vhostDelete($id);
            $vhostObject->restartHttpdServer();
        }

        $query = $sql->prepare("DELETE FROM `webVhost` WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        $queryCount = $query->rowCount();

        $$query = $sql->prepare("DELETE d.* FROM `webVhostDomain` d LEFT JOIN `webVhost` v ON d.`webVhostID`=v.`webVhostID` WHERE v.`webVhostID` IS NULL");
        $query->execute();
        $queryCount += $query->rowCount();

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount() > 0) {

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% %webvhost% web-' . $id;
            $insertlog->execute();

            // Nothing was deleted, display an error
        } else {
            $template_file = $spracheResponse->error_table;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

} else if (!isset($tokenError) and $ui->st('d', 'get') == 'ri' and $id) {

    $query = $sql->prepare("SELECT v.`webMasterID`,u.`cname`,u.`vname`,u.`name` FROM `webVhost` AS v LEFT JOIN `userdata` AS u ON v.`userID`=u.`id` WHERE v.`webVhostID`=? AND v.`resellerID`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $webMasterID = $row['webMasterID'];
        $user = trim($row['cname'] . ' ' . trim($row['vname'] . ' ' . $row['name']));
    }

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        $dns = 'web-' . $id;

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_web_vhost_ri.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'ri') {

        $vhostObject = new HttpdManagement($webMasterID, $resellerLockupID);

        if ($vhostObject != false and $vhostObject->ssh2Connect() and $vhostObject->sftpConnect()) {

            $vhostObject->vhostReinstall($id);
            $vhostObject->restartHttpdServer();

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%ri% %webvhost% web-' . $id;
            $insertlog->execute();

        } else {
            $template_file = $spracheResponse->error_table;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

// List the available entries
} else {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=webvhost');

    $template_file = 'admin_web_vhost_list.tpl';
}