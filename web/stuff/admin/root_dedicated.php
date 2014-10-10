<?php

/**
 * File: root_dedicated.php.
 * Author: Ulrich Block
 * Date: 11.10.12
 * Time: 10:31
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

if (!isset($admin_id) or $main != 1 or !$pa['dedicatedServer']) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('reseller', $user_language, $reseller_id);
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

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action', 4, 'post') and !token(true)) {

	unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_root_dedicated_ad.tpl' : 'admin_root_dedicated_md.tpl';

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md' and $reseller_id == 0) {

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    // At this point all variables are defined that can come from the user
    $id = $ui->id('id', 10, 'get');
    $userID = $ui->id('userID', 19, 'post');
    $ip = $ui->ip('ip', 'post');
    $ips = $ui->ips('ips', 'post');
    $mac = $ui->mac('mac', 'post');
    $externalID = $ui->externalID('externalID', 'post');
    $restart = $ui->w('restart', 1, 'post');
    $apiURL = $ui->domainPath('apiURL', 'post');
    $apiRequestType = $ui->w('apiRequestType', 1, 'post');
    $apiRequestRestart = $ui->escaped('apiRequestRestart', 'post');
    $apiRequestStop = $ui->escaped('apiRequestStop', 'post');
    $description = $ui->escaped('description', 'post');
    $status = '';

    // Default variables. Mostly needed for the add operation
    $active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') :  'Y';
    $https = ($ui->active('https', 'post')) ? $ui->active('https', 'post') :  'Y';
    $useDHCP = ($ui->active('useDHCP', 'post')) ? $ui->active('useDHCP', 'post') :  'Y';
    $usePXE = ($ui->active('usePXE', 'post')) ? $ui->active('usePXE', 'post') :  'Y';


    // Check if we have PXE and DHCP setup
    $query = $sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsDHCP` WHERE `active`='Y' LIMIT 1");
    $query->execute();
    $dhcp = ($query->fetchColumn() > 0) ? 'Y' : 'N';
    $query = $sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsPXE` WHERE `active`='Y' LIMIT 1");
    $query->execute();
    $pxe = ($query->fetchColumn() > 0) ? 'Y' : 'N';

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        $table = array();
        $query = ($reseller_id == 0) ? $sql->prepare("SELECT `id`,`cname`,`vname`,`name`,`accounttype` FROM `userdata` WHERE (`id`=`resellerid` and  `accounttype`='r') OR (`resellerid`=? and `accounttype`='u') ORDER BY `id` DESC") : $sql->prepare("SELECT `id`,`cname`,`vname`,`name`,`accounttype` FROM `userdata` WHERE `resellerid`=? AND `accounttype` IN ('r','u') ORDER BY `id` DESC");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $type = ($row['accounttype'] == 'u') ? $gsprache->user : $gsprache->reseller;
            $table[$row['id']] = $type . ' ' . trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $ipsAvailable = freeips(0);

            $template_file = 'admin_root_dedicated_ad.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT * FROM `rootsDedicated` WHERE `dedicatedID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($id, $reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $active = $row['active'];
                $ip = $row['ip'];
                $ips = $row['ips'];
                $description = $row['description'];
                $externalID = $row['externalID'];
                $mac = $row['mac'];
                $restart = $row['restart'];
                $apiRequestType = $row['apiRequestType'];
                $https = $row['https'];
                $apiRequestRestart = $row['apiRequestRestart'];
                $apiRequestStop = $row['apiRequestStop'];
                $apiURL = $row['apiURL'];
                $userID = $row['userID'];
                $useDHCP = $row['useDHCP'];
                $usePXE = $row['usePXE'];

                if ($row['status'] == 1) {
                    $status = $sprache->stopped;
                } else if ($row['status'] == 2) {
                    $status = $sprache->installing;
                } else if ($row['status'] == 3) {
                    $status = $sprache->rescue;
                } else {
                    $status = $sprache->ok;
                }
            }

            // Check if database entry exists and if not display 404 page
            $template_file =  ($query->rowCount() > 0) ? 'admin_root_dedicated_md.tpl' : 'admin_404.tpl';

            $query = $sql->prepare("SELECT `resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($userID));
            $ipsAvailable = freeips($query->fetchColumn());
            $ipsAvailable[] = $ip;

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$ui->active('active', 'post')) {
            $errors['active'] = $sprache->active;
        }
        if (!$ui->ip('ip', 'post')) {
            $errors['ip'] = $sprache->ip;
        } else {

            if ($ui->st('action', 'post') == 'ad' and $ui->ip('ip', 'post')) {
                $query = $sql->prepare("SELECT 1 FROM `rootsDedicated` WHERE `ip`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($ip, $reseller_id));
            } else if ($ui->st('action', 'post') == 'md' and $ui->ip('ip', 'post')) {
                $query = $sql->prepare("SELECT 1 FROM `rootsDedicated` WHERE `dedicatedID`!=? AND `ip`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($id, $ip, $reseller_id));
            }

            if ($query->rowCount() > 0) {

                $errors['ip'] = $sprache->ip;

            } else {

                $query = $sql->prepare("SELECT `resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
                $query->execute(array($userID));

                if (!in_array($ip, freeips(($query->fetchColumn())))) {
                    $errors['ip'] = $sprache->ip;
                }
            }
        }

        if (!$ui->w('restart', 1, 'post')) {
            $errors['restart'] = $sprache->restart;
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `rootsDedicated` (`active`,`userID`,`description`,`ip`,`ips`,`restart`,`apiRequestType`,`apiRequestRestart`,`apiRequestStop`,`apiURL`,`https`,`mac`,`useDHCP`,`usePXE`,`externalID`,`resellerID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $query->execute(array($active, $userID, $description, $ip, $ips, $restart, $apiRequestType, $apiRequestRestart, $apiRequestStop, $apiURL, $https, $mac, $useDHCP, $usePXE, $externalID, $reseller_id));

                $rowCount = $query->rowCount();
                $loguseraction = '%add% ' . $gsprache->dedicated . $ip;

            } else if ($ui->st('action', 'post') == 'md') {

                $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='de' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
                $query->execute(array($id, $reseller_id));

                $query = $sql->prepare("SELECT `active`,`ip`,`mac`,`useDHCP`,`usePXE` FROM `rootsDedicated` WHERE `dedicatedID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($id, $reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if ($row['active'] != $active or $row['ip'] != $ip or $row['mac'] != $mac or $row['useDHCP'] != $useDHCP or $row['usePXE'] != $usePXE) {
                        $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('D','de',NULL,?,?,?,?,NULL,NOW(),'md',?,?)");
                        $query->execute(array($admin_id, $id, $userID, $ip, json_encode(array('oldactive' => $row['active'], 'oldip' => $row['ip'], 'oldmac' => $row['mac'])), $reseller_id));
                    }
                }
                $query = $sql->prepare("UPDATE `rootsDedicated` SET `active`=?,`userID`=?,`description`=?,`ip`=?,`ips`=?,`restart`=?,`apiRequestType`=?,`apiRequestRestart`=?,`apiRequestStop`=?,`apiURL`=?,`https`=?,`mac`=?,`useDHCP`=?,`usePXE`=?,`externalID`=?,`jobPending`='Y' WHERE `dedicatedID`=? AND `resellerID`=?");
                $query->execute(array($active, $userID, $description, $ip, $ips, $restart, $apiRequestType, $apiRequestRestart, $apiRequestStop, $apiURL, $https, $mac, $useDHCP, $usePXE, $externalID, $id, $reseller_id));

                $rowCount = $query->rowCount();
                $loguseraction = '%mod% ' . $gsprache->dedicated . $ip;
            }

            $query = $sql->prepare("SELECT `resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($userID));

            $query2 = $sql->prepare("UPDATE `rootsIP4` SET `ownerID`=?,`resellerID`=? WHERE `ip`=? LIMIT 1");
            $query2->execute(array($userID, $query->fetchColumn(), $ip));

            // Check if a row was affected during insert or update
            if (isset($rowCount) and $rowCount > 0) {

                customColumns('S', $id, 'save');

                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }

            // An error occurred during validation unset the redirect information and display the form again
        } else {
            unset($header, $text);
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_root_dedicated_ad.tpl' : 'admin_root_dedicated_md.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get') and $reseller_id == 0) {

    // Define the ID variable which will be used at the form and SQLs
    $id = $ui->id('id', 10, 'get');

    $query = $sql->prepare("SELECT `ip`,`description`,`restart`,`useDHCP`,`usePXE` FROM `rootsDedicated` WHERE `dedicatedID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($id, $reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $ip = $row['ip'];
        $restart = $row['restart'];
        $description = $row['description'];
        $useDHCP = $row['useDHCP'];
        $usePXE = $row['usePXE'];
    }

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_root_dedicated_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl' and $query->rowCount() > 0) {


        customColumns('S', $id, 'del');

        $query = $sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsDHCP` WHERE `active`='Y' LIMIT 1");
        $query->execute();
        $dhcp = ($query->fetchColumn() > 0) ? 'Y' : 'N';

        $query = $sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsPXE` WHERE `active`='Y' LIMIT 1");
        $query->execute();
        $pxe = ($query->fetchColumn() > 0) ? 'Y' : 'N';

        // Check if we need to remove data from DHCP or PXE server as well.
        if (($dhcp == 'Y' and $useDHCP == 'Y') or ($pxe == 'Y' and $usePXE == 'Y')) {

            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='de' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
            $query->execute(array($id, $reseller_id));

            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('D','de',NULL,?,?,NULL,?,NULL,NOW(),'dl',?)");
            $query->execute(array($admin_id, $id, $ip, $reseller_id));

            $query = $sql->prepare("UPDATE `rootsDedicated` SET `jobPending`='Y' WHERE `dedicatedID`=? AND `resellerID`=?");
            $query->execute(array($id, $reseller_id));

        } else {

            $query = $sql->prepare("DELETE FROM `rootsDedicated` WHERE `dedicatedID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($id, $reseller_id));
        }

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount() > 0) {

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del%' . $gsprache->dedicated;
            $insertlog->execute();

            // Nothing was deleted, display an error
        } else {
            $template_file = $spracheResponse->error_table;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }


} else if ($ui->st('d', 'get') == 'ri' and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->st('action', 'post')) {
        $option = array();

        $query = $sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsDHCP` WHERE `active`='Y' LIMIT 1");
        $query->execute();

        $dhcp = ($query->fetchColumn() > 0) ? 'Y' : 'N';
        $query = $sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsPXE` WHERE `active`='Y' LIMIT 1");
        $query->execute();
        $pxe = ($query->fetchColumn() > 0) ? 'Y' : 'N';

        $query = $sql->prepare("SELECT r.*,d.*,AES_DECRYPT(d.`initialPass`,?) AS `decryptedpass` FROM `rootsDedicated` d LEFT JOIN `resellerimages` r ON d.`imageID`=r.`id` WHERE d.`dedicatedID`=? LIMIT 1");
        $query->execute(array($aeskey, $id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $showImages = false;
            $description = $row['description'];
            $bitversion = $row['bitversion'];
            $pass = $row['decryptedpass'];
            $ip = $row['ip'];

            if ($row['userID'] == null) {
                $error = $sprache->userAdd;

            } else if ($dhcp == 'N' or $pxe == 'N') {
                $option[] = '<option value="rs">' . $sprache->restart . '</option>';
                $option[] = '<option value="st">' . $sprache->stop . '</option>';

            } else {
                $showImages = true;
                if ($row['status'] == null or $row['status'] == 2) {
                    $option[] = '<option value="rc">' . $sprache->rescue_start . '</option>';
                    $option[] = '<option value="ri">' . $sprache->reinstall . '</option>';
                } else if ($row['status'] == 0) {
                    $option[] = '<option value="rs">' . $sprache->restart . '</option>';
                    $option[] = '<option value="st">' . $sprache->stop . '</option>';
                    $option[] = '<option value="rc">' . $sprache->rescue_start . '</option>';
                    $option[] = '<option value="ri">' . $sprache->reinstall . '</option>';
                } else if ($row['status'] == 1) {
                    $option[] = '<option value="rs">' . $sprache->restart . '</option>';
                    $option[] = '<option value="rc">' . $sprache->rescue_start . '</option>';
                    $option[] = '<option value="ri">' . $sprache->reinstall . '</option>';
                } else if ($row['status'] == 3) {
                    $option[] = '<option value="rt">' . $sprache->rescue_stop . '</option>';
                    $option[] = '<option value="ri">' . $sprache->reinstall . '</option>';
                }
            }

            if ($row['status'] == 1) {
                $status = $sprache->stopped;
            } else if ($row['status'] == 2) {
                $status = $sprache->installing;
            } else if ($row['status'] == 3) {
                $status = $sprache->rescue;
            } else {
                $status = $sprache->ok;
            }
        }

        if ($query->rowCount() > 0) {

            $templates = array();
            $query = $sql->prepare("SELECT `id`,`description`,`bitversion` FROM `resellerimages` WHERE `description` NOT IN ('Rescue 32bit','Rescue 64bit') ORDER BY `distro`,`bitversion`,`description`");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $templates[] = array('id' => $row['id'], 'description' => $row['description']);
            }

            $template_file = 'admin_root_dedicated_ri.tpl';

        } else {
            $template_file = 'admin_404.tpl';
        }

    } else if (in_array($ui->st('action', 'post'), array('ri','rc','rs','st'))) {
        $query = $sql->prepare("SELECT d.`ip`,i.`bitversion` FROM `rootsDedicated` d LEFT JOIN `resellerimages` i ON d.`resellerImageID`=i.`id` WHERE d.`dedicatedID`=? LIMIT 1");
        $query->execute(array($id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip = $row['ip'];
            $bitversion = $row['bitversion'];
        }
        if (!isset($bitversion)) {
            $bitversion = 64;
        }
        if (isset($ip)) {
            $extraData = array();
            if ($ui->st('action', 'post') == 'ri') {
                $extraData['imageID'] = $ui->id('imageid',10, 'post');
            } else if ($ui->st('action', 'post') == 'rc') {
                $query = $sql->prepare("SELECT `id` FROM `resellerimages` WHERE `bitversion`=? AND `active`='Y' AND `distro`='other' AND `description` LIKE 'Rescue %' LIMIT 1");
                $query->execute(array($bitversion));
                $extraData['imageID'] = $query->fetchColumn();
            }
            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('D','de',NULL,?,?,NULL,?,NULL,NOW(),?,?,?)");
            $query->execute(array($admin_id, $id, $ip, $ui->st('action', 'post'), json_encode($extraData), $reseller_id));
            $query = $sql->prepare("UPDATE `rootsDedicated` SET `jobPending`='Y' WHERE `dedicatedID`=? AND `resellerID`=?");
            $query->execute(array($id, $reseller_id));
            $template_file = $spracheResponse->table_add;
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else {
        $template_file = 'admin_404.tpl';
    }

// List the available entries
} else {

    $table = array();

    $query = $sql->prepare("SELECT COUNT(`dedicatedID`) AS `amount` FROM `rootsDedicated` WHERE `resellerID`=?");
    $query->execute(array($reseller_id));
    $colcount = $query->fetchColumn();

    if ($start > $colcount) {
        $start = $colcount-$amount;
    }

    if ($start < 0) {
        $start = 0;
    }

    $next = $start + $amount;
    $vor = ($colcount > $next) ? $start + $amount : $start;
    $back = $start - $amount;
    $zur = ($back >= 0) ? $start - $amount : $start;

    $o = $ui->st('o', 'get');

    if ($ui->st('o', 'get') == 'dp') {
        $orderby = 'd.`ip` DESC';
    } else if ($ui->st('o', 'get') == 'ap') {
        $orderby = 'd.`ip` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = 'd.`active` DESC,`notified` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = 'd.`active` ASC,`notified` ASC';
    } else if ($ui->st('o', 'get') == 'dc') {
        $orderby = 'u.`cname` DESC';
    } else if ($ui->st('o', 'get') == 'ac') {
        $orderby = 'u.`cname` ASC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = 'u.`name` DESC,u.`vname` DESC';
    } else if ($ui->st('o', 'get') == 'an') {
        $orderby = 'u.`name` ASC,u.`vname` ASC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = 'd.`active` ASC,`notified` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = 'd.`dedicatedID` DESC';
    } else {
        $orderby = 'd.`dedicatedID` ASC';
        $o = 'ai';
    }

    $query = ($reseller_id == 0) ? $sql->prepare("SELECT d.*,u.`cname`,u.`name`,u.`vname` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userID`=u.`id` WHERE d.`resellerID`=? OR u.`id`=u.`resellerid` ORDER BY $orderby LIMIT $start,$amount") : $sql->prepare("SELECT d.*,u.`cname`,u.`name`,u.`vname` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userID`=u.`id` WHERE d.`resellerID`=? ORDER BY $orderby LIMIT $start,$amount");
    $query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `type`='de' AND (`status` IS NULL OR `status`=1 OR `status`=4) ORDER BY `jobID` DESC LIMIT 1");

    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $jobPending = $gsprache->no;

        if ($row['jobPending'] == 'Y') {
            $query2->execute(array($row['dedicatedID']));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if ($row2['action'] == 'ad') {
                    $jobPending = $gsprache->add;
                } else if ($row2['action'] == 'dl') {
                    $jobPending = $gsprache->del;
                } else if ($row2['action'] == 'ri') {
                    $jobPending = $sprache->reinstall;
                } else if ($row2['action'] == 'rc') {
                    $jobPending = $sprache->rescue_start;
                } else if ($row2['action'] == 'rs') {
                    $jobPending = $sprache->restart;
                } else if ($row2['action'] == 'st') {
                    $jobPending = $sprache->stop;
                } else {
                    $jobPending = $gsprache->mod;
                }

                $json = @json_decode($row2['extraData']);
                $tobeActive = (is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
            }
        }

        $imgName = '16_ok';
        $imgAlt = 'Active';
        $active = 'Y';

        if (($row['active'] == 'Y' and $row['jobPending'] == 'N' and $row['notified'] <= $rSA['down_checks']) or ($row['jobPending'] == 'Y') and isset($tobeActive) and $tobeActive == 'Y') {
            $imgName = '16_ok';
            $imgAlt = 'Active';
        } else if (($row['active'] == 'Y' and $row['jobPending'] == 'N' and $row['notified'] > $rSA['down_checks']) or ($row['jobPending'] == 'Y') and isset($tobeActive) and $tobeActive == 'Y') {
            $imgName = '16_error';
            $imgAlt = 'Crashed';
            $active='C';
        } else if ($row['active'] == 'N') {
            $imgName = '16_bad';
            $imgAlt = 'Inactive';
            $active = 'N';
        }

        if ($row['status'] == 1) {
            $status = $sprache->stopped;
        } else if ($row['status'] == 2) {
            $status = $sprache->installing;
        } else if ($row['status'] == 3) {
            $status = $sprache->rescue;
        } else {
            $status = $sprache->ok;
        }

        $table[] = array('id' => $row['dedicatedID'], 'ip' => $row['ip'], 'description' => $row['description'], 'status' => $status,'img' => $imgName,'alt' => $imgAlt,'userID' => $row['userID'], 'cname' => $row['cname'], 'names' => trim($row['name'] . ' ' . $row['vname']), 'active' => $active, 'jobPending' => $jobPending);

    }

    $pageamount = ceil($colcount / $amount);
    $link = '<a href="admin.php?w=rp&amp;o=' . $o . '&amp;a=';
    $link .= (isset($amount)) ? $amount : 20;

    $link .= ($start == 0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';

    $pages[] = $link;
    $i = 2;

    while ($i <= $pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[]=($start==$selectpage) ? '<a href="admin.php?w=rp&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=rp&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        $i++;
    }
    $pages = implode(', ',$pages);
    $template_file = 'admin_root_dedicated_list.tpl';
}