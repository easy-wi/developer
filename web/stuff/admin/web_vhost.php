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

if (!isset($admin_id) or $main != 1 or !isset($admin_id) or !isset($reseller_id) or !$pa['fastdl']) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

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
$webMasterID = $ui->id('webMasterID', 10, 'post');
$userID = $ui->id('userID', 10, 'post');
$active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
$hdd = ($ui->id('hdd', 10, 'post')) ? $ui->id('hdd', 10, 'post') : 1000;
$dns = (string) strtolower($ui->domain('dns', 'post'));
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

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $table = array();
            $table2 = array();

            $maxVhost = 0;
            $maxHDD = 0;
            $totalVhosts = 0;
            $leftHDD = 0;
            $quotaActive = 'N';

            // Get useraccounts
            $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' AND `active`='Y' ORDER BY `id` DESC");
            $query->execute(array($resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $table[$row['id']] = trim($row['cname'] . ' ' . trim($row['vname'] . ' ' . $row['name']));
            }

            // Get masterserver. Sort by usage.

            $query = $sql->prepare("SELECT m.`webMasterID`,m.`ip`,m.`description`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(m.`maxVhost`/100) AS `percentVhostUsage`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(m.`maxHDD`/100) AS `percentHDDUsage` FROM `webMaster` AS m WHERE m.`active`='Y' AND m.`resellerID`=? GROUP BY m.`webMasterID` HAVING (`percentVhostUsage`<100 OR `percentVhostUsage`IS NULL) AND (`percentHDDUsage`<100 OR `percentHDDUsage`IS NULL) ORDER BY `percentHDDUsage` ASC,`percentVhostUsage` ASC");
            $query->execute(array($resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $table2[$row['webMasterID']] = trim($row['ip'] . ' ' . $row['description']);
            }

            if (count($table2) > 0) {

                $bestID = key($table2);

                $query = $sql->prepare("SELECT m.`vhostTemplate`,m.`maxVhost`,m.`maxHDD`,m.`quotaActive`,m.`defaultdns`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`) AS `totalVhosts`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`) AS `totalHDD` FROM `webMaster` AS m WHERE m.`webMasterID`=? AND m.`resellerID`=? LIMIT 1");
                $query->execute(array($bestID, $resellerLockupID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $vhostTemplate = $row['vhostTemplate'];
                    $maxVhost = (int) $row['maxVhost'];
                    $maxHDD = (int) $row['maxHDD'];
                    $totalVhosts = (int) $row['totalVhosts'];
                    $leftHDD = (int) $row['maxHDD'] - $row['totalHDD'];
                    $quotaActive = $row['quotaActive'];
                    $dns = $row['defaultdns'];
                }
            }

            $template_file = 'admin_web_vhost_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT v.*,AES_DECRYPT(v.`ftpPassword`,?) AS `decryptedFTPPass`,m.`ip`,m.`ftpIP`,m.`ftpPort`,m.`description`,m.`maxHDD`,m.`quotaActive`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`) AS `totalHDD`,u.`cname`,u.`vname`,u.`name` FROM `webVhost` AS v INNER JOIN `webMaster` AS m ON m.`webMasterID`=v.`webMasterID` INNER JOIN `userdata` AS u ON u.`id`=v.`userID` WHERE v.`webVhostID`=? AND v.`resellerID`=? LIMIT 1");
            $query->execute(array($aeskey, $id, $resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                // Userdata from JOIN, trim in case attributes are not provided
                $userName = trim($row['cname'] . ' ' . trim($row['vname'] . ' ' . $row['name']));

                // Masterserver from JOIN. Display FTP IP in case it is provided
                $ftpServer = (isip($row['ftpIP'], 'ip4')) ? $row['ftpIP'] : $row['ip'];
                $ftpServer .= ':' . $row['ftpPort'];
                $description = $row['description'];
                $maxHDD = (int) $row['maxHDD'];
                $leftHDD = (int) $row['maxHDD'] - $row['totalHDD'];
                $quotaActive = $row['quotaActive'];

                // Vhost data
                $active = $row['active'];
                $hdd = $row['hdd'];
                $dns = $row['dns'];
                $ftpPassword = $row['decryptedFTPPass'];
                $ownVhost = $row['ownVhost'];
                $vhostTemplate = $row['vhostTemplate'];
            }

            // Check if database entry exists and if not display 404 page
            $template_file = ($query->rowCount() > 0) ? 'admin_web_vhost_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$active) {
            $errors['active'] = $dedicatedLanguage->active;
        }

        if (!$ftpPassword) {
            $errors['ftpPassword'] = $sprache->ftpPassword;
        }

        if (!$dns) {
            $errors['dns'] = $sprache->dns;
        }

        // Only at ADD user and masterserver can be defined. We need to check if they exist
        if ($ui->st('action', 'post') == 'ad') {

            if ($userID) {

                $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($userID, $resellerLockupID));
                $ftpUser = $query->fetchColumn();

                if (strlen($ftpUser) < 1) {
                    $errors['userID'] = $dedicatedLanguage->user;
                }

            } else {
                $errors['userID'] = $dedicatedLanguage->user;
            }

            if ($webMasterID) {

                $query = $sql->prepare("SELECT `defaultdns` FROM `webMaster` WHERE `webMasterID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($webMasterID, $resellerLockupID));
                $defaultDns = (string) $query->fetchColumn();

                if (strlen($defaultDns) < 1) {
                    $errors['webMasterID'] = $gsprache->master;
                }

            } else {
                $errors['webMasterID'] = $gsprache->master;
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `webVhost` (`webMasterID`,`userID`,`active`,`hdd`,`ftpPassword`,`ownVhost`,`vhostTemplate`,`resellerID`) VALUES (?,?,?,?,AES_ENCRYPT(?,?),?,?,?)");
                $query->execute(array($webMasterID, $userID, $active, $hdd, $ftpPassword, $aeskey, $ownVhost, $vhostTemplate, $resellerLockupID));

                $id = (int) $sql->lastInsertId();

                $ftpUser .= '-' . $id;

                if ($defaultDns == $dns) {
                    $dns = str_replace('..', '.', $ftpUser . '.' .$defaultDns);
                }

                $query = $sql->prepare("UPDATE `webVhost` SET `dns`=?,`ftpUser`=? WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($dns, $ftpUser, $id, $resellerLockupID));

                $rowCount = $query->rowCount();
                $loguseraction = '%add% %webvhost% ' . $dns;

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                $query = $sql->prepare("SELECT `active` FROM `webVhost` WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($id, $resellerLockupID));
                $oldActive = $query->fetchColumn();

                $query = $sql->prepare("UPDATE `webVhost` SET `active`=?,`hdd`=?,`dns`=?,`ftpPassword`=AES_ENCRYPT(?,?),`ownVhost`=?,`vhostTemplate`=? WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($active, $hdd, $dns, $ftpPassword, $aeskey, $ownVhost, $vhostTemplate, $id, $resellerLockupID));

                // in case vhost is deactivated change password to random for later processing
                if ($oldActive == 'Y' and $oldActive != $active) {
                    $ftpPassword = passwordgenerate(10);
                }

                $rowCount = $query->rowCount();
                $loguseraction = '%mod% %webvhost% ' . $dns;
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

            if ($ui->st('d', 'get') == 'ad') {

                $table = array();
                $table2 = array();

                $maxVhost = 0;
                $maxHDD = 0;
                $webVhosts = 0;
                $leftHDD = 0;
                $totalHDD = 0;
                $totalVhosts = 0;
                $quotaActive = 'N';
                $dns = '';

                // Get useraccounts
                $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' AND `active`='Y' ORDER BY `id` DESC");
                $query->execute(array($resellerLockupID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $table[$row['id']] = trim($row['cname'] . ' ' . trim($row['vname'] . ' ' . $row['name']));
                }

                // Get masterserver. Sort by usage.

                $query = $sql->prepare("SELECT m.`webMasterID`,m.`ip`,m.`description`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(m.`maxVhost`/100) AS `percentVhostUsage`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(m.`maxHDD`/100) AS `percentHDDUsage` FROM `webMaster` AS m WHERE m.`active`='Y' AND m.`resellerID`=? GROUP BY m.`webMasterID` HAVING (`percentVhostUsage`<100 OR `percentVhostUsage`IS NULL) AND (`percentHDDUsage`<100 OR `percentHDDUsage`IS NULL) ORDER BY `percentHDDUsage` ASC,`percentVhostUsage` ASC");
                $query->execute(array($resellerLockupID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $table2[$row['webMasterID']] = trim($row['ip'] . ' ' . $row['description']);
                }

                if (count($table2) > 0) {

                    $bestID = key($table2);

                    $query = $sql->prepare("SELECT m.`vhostTemplate`,m.`maxVhost`,m.`maxHDD`,m.`quotaActive`,m.`defaultdns`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`) AS `totalVhosts`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`) AS `totalHDD` FROM `webMaster` AS m WHERE m.`webMasterID`=? AND m.`resellerID`=? LIMIT 1");
                    $query->execute(array($bestID, $resellerLockupID));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $vhostTemplate = $row['vhostTemplate'];
                        $maxVhost = (int) $row['maxVhost'];
                        $maxHDD = (int) $row['maxHDD'];
                        $totalVhosts = (int) $row['totalVhosts'];
                        $leftHDD = (int) $row['maxHDD'] - $row['totalHDD'];
                        $quotaActive = $row['quotaActive'];
                        $dns = $row['defaultdns'];
                    }
                }

                $template_file = 'admin_web_vhost_add.tpl';

            } else {
                $template_file = 'admin_web_vhost_md.tpl';
            }
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if (!isset($tokenError) and $ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT v.`dns`,u.`cname`,u.`vname`,u.`name` FROM `webVhost` AS v LEFT JOIN `userdata` AS u ON v.`userID`=u.`id` WHERE v.`webVhostID`=? AND v.`resellerID`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $dns = $row['dns'];
        $user = trim($row['cname'] . ' ' . trim($row['vname'] . ' ' . $row['name']));
    }


    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post') and isset($user)) {

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_web_vhost_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl' and isset($user)) {

        $query = $sql->prepare("DELETE FROM `webVhost` WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount() > 0) {

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% %webvhost% ' . $dns;
            $insertlog->execute();

            // Nothing was deleted, display an error
        } else {
            $template_file = $spracheResponse->error_table;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

// List the available entries
} else {

    $table = array();

    $query = $sql->prepare("SELECT COUNT(`webVhostID`) AS `amount` FROM `webVhost` WHERE `resellerID`=?");
    $query->execute(array($resellerLockupID));
    $colcount = $query->fetchColumn();

    if (!isset($start)) {
        $start = 0;
    }

    if (!isset($amount)) {
        $amount = 20;
    }

    if ($start > $colcount) {
        $start = $colcount - $amount;
    }

    if ($start < 0) {
        $start = 0;
    }

    $next = $start + $amount;
    $vor = ($colcount > $next) ? $start + $amount : $start;
    $back = $start - $amount;
    $zur = ($back >= 0) ? $start - $amount : $start;

    $o = (string) $ui->st('o', 'get');

    if ($ui->st('o', 'get') == 'dd') {
        $orderby = 'v.`dns` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = 'v.`dns` ASC';
    } else if ($ui->st('o', 'get') == 'dc') {
        $orderby = 'u.`cname` DESC';
    } else if ($ui->st('o', 'get') == 'ac') {
        $orderby = 'u.`cname` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = 'v.`active` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = 'v.`active` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = 'v.`webVhostID` DESC';
    } else {
        $orderby = 'v.`webVhostID` ASC';
        $o = 'ai';
    }

    $query = $sql->prepare("SELECT v.*,u.`cname` FROM `webVhost` AS v LEFT JOIN `userdata` u ON v.`userID`=u.`id` WHERE v.`resellerID`=? ORDER BY " . $orderby . " LIMIT " . $start . "," . $amount);
    $query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `type`='fd' AND (`status` IS NULL OR `status`=1 OR `status`=4) ORDER BY `jobID` DESC LIMIT 1");

    $query->execute(array($resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $jobPending = $gsprache->no;

        if ($row['jobPending'] == 'Y') {
            $query2->execute(array($row['dedicatedID']));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

                if ($row2['action'] == 'ad') {
                    $jobPending = $gsprache->add;
                } else if ($row2['action'] == 'dl') {
                    $jobPending = $gsprache->del;
                } else {
                    $jobPending = $gsprache->mod;
                }

                $json = @json_decode($row2['extraData']);
                $tobeActive = (is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
            }
        }

        $active = 'Y';

        if ($row['jobPending'] == 'Y' and isset($tobeActive) and $tobeActive == 'Y') {
            $active = 'Y';
        } else if ($row['active'] == 'N') {
            $active = 'N';
        }

        $table[] = array('id' => $row['webVhostID'], 'active' => $row['active'], 'dns' => $row['dns'], 'hdd' => $row['hdd'], 'jobPending' => $jobPending, 'userID' => $row['userID'], 'cname' => $row['cname']);
    }

    $pageamount = ceil($colcount / $amount);

    $link = '<a href="admin.php?w=fv&amp;o=' . $o . '&amp;a=' . $amount;
    $link .= ($start == 0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';

    $pages[] = $link;

    $i = 2;

    while ($i <= $pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[] = ($start == $selectpage) ? '<a href="admin.php?w=fv&amp;o=' . $o . '&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=fv&amp;o=' . $o . '&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        $i++;
    }

    $pages = implode(', ',$pages);

    $template_file = 'admin_web_vhost_list.tpl';
}