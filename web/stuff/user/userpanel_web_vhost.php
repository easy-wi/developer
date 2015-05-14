<?php

/**
 * File: userpanel_fdl.php.
 * Author: Ulrich Block
 * Time: 08:04
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

if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['webvhost'])) {
    header('Location: userpanel.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_httpd.php');

$sprache = getlanguagefile('web', $user_language, $reseller_id);
$gsSprache = getlanguagefile('gserver', $user_language, $reseller_id);
$dedicatedLanguage = getlanguagefile('reseller', $user_language, $reseller_id);

$loguserid = $user_id;
$logusername = getusername($user_id);
$logusertype = 'user';
$logreseller = 0;

if (isset($admin_id)) {
    $logsubuser = $admin_id;
} else if (isset($subuser_id)) {
    $logsubuser = $subuser_id;
} else {
    $logsubuser = 0;
}

if ($ui->id('id', 10, 'get') and in_array($ui->st('d', 'get'), array('if', 'pw', 'ri', 'md','dm'))) {

    $query = $sql->prepare("SELECT v.`webMasterID`,v.`phpConfiguration`,v.`phpConfiguration`,m.`usageType`,m.`defaultdns`,m.`connect_ip_only`,m.`ftpIP`,m.`ip`,m.`phpConfiguration` AS `phpMasterConfiguration` FROM `webVhost` AS v INNER JOIN `webMaster` AS m ON m.`webMasterID`=v.`webMasterID` WHERE v.`webVhostID`=? AND v.`userID`=? AND v.`resellerID`=? AND v.`active`='Y'");
    $query->execute(array($ui->id('id', 10, 'get'), $user_id, $reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $defaultDns = 'web-' . $ui->id('id', 10, 'get') . '.' . $row['defaultdns'];
        $dns = 'web-' . $ui->id('id', 10, 'get');
        $webMasterID = $row['webMasterID'];
        $usageType = $row['usageType'];
        $phpConfigurationVhost = @json_decode($row['phpConfiguration']);
        $phpConfigurationMaster = @parse_ini_string($row['phpMasterConfiguration'], true, INI_SCANNER_RAW);
        $serverIP = ($row['connect_ip_only'] == 'Y') ? $row['ftpIP'] : $row['ip'];
    }
}

if (isset($webMasterID) and $ui->st('d', 'get') == 'pw' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['ws']))) {

    $id = $ui->id('id', 10, 'get');

    $errors = array();

    if ($ui->st('action', 'post') == 'pw') {

        if ($ui->w('action', 4, 'post') and !token(true)) {
            $errors[] = $spracheResponse->token;
        }

        if (!$ui->password('password1', 40, 'post') or !$ui->password('password2', 40, 'post')) {
            $errors[] = $sprache->error_password_not_set;
        }

        if ($ui->password('password1', 40, 'post') != $ui->password('password2', 40, 'post')) {
            $errors[] = $sprache->error_password_not_match;
        }

        if (count($errors) == 0) {
            $query = $sql->prepare("UPDATE `webVhost` SET `ftpPassword`=AES_ENCRYPT(?,?) WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=? AND `active`='Y' LIMIT 1");
            $query->execute(array($ui->password('password1', 40, 'post'), $aeskey, $id, $user_id, $reseller_id));

            if ($query->rowCount() == 0) {
                $errors[] = $spracheResponse->error_table;
            }
        }

        if (count($errors) > 0) {

            unset($header, $text);

            $template_file = 'userpanel_web_vhost_pw.tpl';

        } else {

            $vhostObject = new HttpdManagement($webMasterID, $reseller_id);

            if ($vhostObject != false and $vhostObject->ssh2Connect()) {

                $vhostObject->changePassword($id);
                $template_file = $sprache->ftpPasswordChanged;
            }

        }

    } else {
        $template_file = 'userpanel_web_vhost_pw.tpl';
    }
} else if (isset($webMasterID, $dns, $usageType, $phpConfigurationMaster) and $usageType == 'W' and $ui->st('d', 'get') == 'dm' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['ws']))) {

    $id = $ui->id('id', 10, 'get');

    if ($ui->st('action', 'post') == 'dm') {

        $domainRemove = array();
        $domainConfigurations = array();
        $notice = array();

        $query = $sql->prepare("SELECT `domain` FROM `webVhostDomain` WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=?");
        $query->execute(array($id, $user_id, $reseller_id));
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $domainRemove[$row['domain']] = $row['domain'];
        }

        $domains = $ui->domain('domain', 'post');

        if ($domains) {

            $query = $sql->prepare("SELECT 1 FROM `webVhostDomain` WHERE `webVhostID`!=? AND `domain`=? AND `resellerID`=? LIMIT 1");

            $paths = $ui->path('path', 'post');
            $ownVhosts = $ui->active('ownVhost', 'post');
            $vhostTemplates = $ui->escaped('vhostTemplate', 'post');

            foreach($domains as $index => $domain) {

                $query->execute(array($id, $domain, $reseller_id));

                if ($query->rowCount() > 0) {

                    $notice[] = str_replace('%domain%', $domain, $sprache->domainNotUsed);

                } else {

                    $givenPath = (property_exists($paths, $index)) ? $paths->$index : '';

                    // Check for file traversal and similar
                    $path = str_replace('..', '', $givenPath);

                    while (strpos($path, './') !== false) {
                        $path = str_replace('./', '/', $path);
                    }

                    while (strpos($path, '//') !== false) {
                        $path = str_replace('//', '/', $path);
                    }

                    while (substr($path, 0, 1) == '/') {
                        $path = substr($path, 1);
                    }

                    if ($givenPath != $path) {
                        $notice[] = str_replace(array('%pathGiven%', '%pathConverted%', '%domain%'), array($givenPath, $path, $domain), $sprache->pathCorrected);
                    }

                    $aRecordIPs = gethostbynamel($domain);

                    if ($aRecordIPs === false or !in_array($serverIP, $aRecordIPs)) {
                        $notice[] = str_replace(array('%domain%', '%ip%'), array($domain, $serverIP), $sprache->aRecordMissing);
                    }

                    $domainConfigurations[$domain] = $path;
                }
            }
        }

        // Insert/Delete of the domain configuration(s) Unique key is the domain
        if (count($domainConfigurations) == 0) {
            $domainConfigurations[$defaultDns] = '';
        }

        $rowCount = 0;

        $query = $sql->prepare("INSERT INTO `webVhostDomain` (`webVhostID`,`userID`,`resellerID`,`domain`,`path`,`ownVhost`,`vhostTemplate`) VALUES (?,?,?,?,?,'N',?) ON DUPLICATE KEY UPDATE `path`=VALUES(`path`)");
        foreach($domainConfigurations as $domain => $path) {

            $query->execute(array($id, $user_id, $reseller_id, $domain, $path, $path['vhostTemplate']));
            $rowCount += $query->rowCount();

            unset($domainRemove[$domain]);
        }

        if (count($domainRemove) > 0) {
            $query = $sql->prepare("DELETE FROM `webVhostDomain` WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=? AND `domain` IN('" . implode("','", $domainRemove) . "')");
            $query->execute(array($id, $user_id, $reseller_id));
            $rowCount += $query->rowCount();
        }

        $vhostObject = new HttpdManagement($webMasterID, $reseller_id);

        if ($rowCount and $vhostObject != false and $vhostObject->ssh2Connect() and $vhostObject->sftpConnect()) {

            $vhostObject->vhostMod($id);
            $vhostObject->restartHttpdServer();

            $template_file = $spracheResponse->table_add;
            $loguseraction = '%md% %webvhost% ' . $dns;
            $insertlog->execute();

        } else {
            $template_file = $spracheResponse->error_table;
        }

        if (count($notice) > 0) {
            $template_file .= '<ul><li>' . implode('</li><li>', $notice) . '</li></ul>';
        }

    } else if (!$ui->st('action', 'post')) {

        $dnsArray = array();

        $query = $sql->prepare("SELECT `domain`,`path` FROM `webVhostDomain` WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=? ORDER BY `domain`");
        $query->execute(array($id, $user_id, $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $dnsArray[] = array('domain' => $row['domain'], 'path' => $row['path']);
        }

        $template_file = 'userpanel_web_vhost_dm.tpl';

        // Request did not add up. Display 404 error.
    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else if (isset($webMasterID, $dns, $usageType, $phpConfigurationMaster) and $usageType == 'W' and $ui->st('d', 'get') == 'md' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['ws']))) {

    $id = $ui->id('id', 10, 'get');

    if ($ui->st('action', 'post') == 'md') {

        $phpConfiguration = array();

        if ($phpConfigurationMaster) {

            foreach ($phpConfigurationMaster as $groupName => $array) {

                $groupNameSelect = $ui->escaped(str_replace(' ', '', $groupName), 'post');

                if ($groupNameSelect and isset($phpConfigurationMaster[$groupName][$groupNameSelect])) {
                    $phpConfiguration[$groupName] = $groupNameSelect;
                } else {
                    reset($phpConfigurationMaster[$groupName]);
                    $phpConfiguration[$groupName] = key($phpConfigurationMaster[$groupName]);
                }
            }
        }

        $phpConfiguration = @json_encode($phpConfiguration);

        $query = $sql->prepare("UPDATE `webVhost` SET `phpConfiguration`=? WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($phpConfiguration, $id, $user_id, $reseller_id));

        $vhostObject = new HttpdManagement($webMasterID, $reseller_id);

        if ($query->rowCount() and $vhostObject != false and $vhostObject->ssh2Connect() and $vhostObject->sftpConnect()) {

            $vhostObject->vhostMod($id);
            $vhostObject->restartHttpdServer();

            $template_file = $spracheResponse->table_add;
            $loguseraction = '%md% %webvhost% ' . $dns;
            $insertlog->execute();

        } else {
            $template_file = $spracheResponse->error_table;
        }

    } else if (!$ui->st('action', 'post')) {

        $template_file = 'userpanel_web_vhost_md.tpl';

    // Request did not add up. Display 404 error.
    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else if (isset($webMasterID, $dns) and $ui->st('d', 'get') == 'ri' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['ws']))) {

    $id = $ui->id('id', 10, 'get');

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        // Check if we could find an entry and if not display 404 page
        $template_file = 'userpanel_web_vhost_ri.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'ri') {

        $vhostObject = new HttpdManagement($webMasterID, $reseller_id);

        if ($vhostObject != false and $vhostObject->ssh2Connect() and $vhostObject->sftpConnect()) {

            $vhostObject->vhostReinstall($id);
            $vhostObject->restartHttpdServer();

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%ri% %webvhost% ' . $dns;
            $insertlog->execute();

        } else {
            $template_file = $spracheResponse->error_table;
        }

        // Request did not add up. Display 404 error.
    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else if (isset($dns) and $ui->st('d', 'get') == 'if' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['ws']))) {

    $hlCfg = 'sv_downloadurl "http://' . $dns . '/"
sv_allowdownload "1"
sv_allowupload "1"';

    $codCfg = 'set sv_allowDownload "1"
set sv_wwwBaseURL "http://' . $dns . '/"
set sv_wwwDlDisconnected "0"
set sv_wwwDownload "1"';

    $template_file = 'userpanel_web_vhost_info.tpl';

} else {

    $table = array();

    $query = $sql->prepare("SELECT v.`webVhostID`,v.`hdd`,v.`hddUsage`,v.`ftpUser`,AES_DECRYPT(v.`ftpPassword`,?) AS `decryptedFTPPass`,m.`ip`,m.`ftpIP`,m.`ftpPort`,m.`quotaActive`,m.`usageType`,m.`defaultdns`,m.`connect_ip_only` FROM `webVhost` AS v INNER JOIN `webMaster` AS m ON m.`webMasterID`=v.`webMasterID` WHERE v.`userID`=? AND v.`resellerID`=? AND v.`active`='Y'");
    $query->execute(array($aeskey, $user_id, $reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($_SESSION['sID']) or in_array($row['webVhostID'], $substituteAccess['ws'])) {
            $table[] = array('id' => $row['webVhostID'], 'dns' => 'web-' . $row['webVhostID'], 'hdd' => $row['hdd'], 'hddUsage' => $row['hddUsage'], 'quotaActive' => $row['quotaActive'], 'ftpIP' => ($row['connect_ip_only'] == 'Y') ? $row['ftpIP'] : $row['ip'], 'ftpPort' => $row['ftpPort'], 'ftpUser' => $row['ftpUser'], 'ftpPass' => $row['decryptedFTPPass'], 'usageType' => $row['usageType']);
        }
    }

    $template_file = 'userpanel_web_vhost_list.tpl';
}