<?php
/**
 * File: voice_tsdnsrecords.php.
 * Author: Ulrich Block
 * Date: 23.09.12
 * Time: 14:43
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

include(EASYWIDIR . '/stuff/methods/functions_ts3.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('voice', $user_language, $reseller_id);
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

$table = array();
$table2 = array();

$id = $ui->id('id', 10, 'get');
$externalID = $ui->externalID('externalID', 'post');
$userID = $ui->id('userID', 10, 'post');
$rootID = $ui->id('rootID', 10, 'post');
$ip = $ui->ip4('ip', 'post');
$port = ($ui->port('port', 'post')) ? $ui->port('port', 'post') : 9987;
$dns = ($ui->domain('dns', 'post')) ? strtolower($ui->domain('dns', 'post')) : $ui->domain('dns', 'post');
$active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action', 4, 'post') and !token(true)) {

    unset($header, $text);

    $errors = array('token' => $spracheResponse->token);

} else {
    $errors = array();
}

if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Add jQuery plugin chosen to the header
    $htmlExtraInformation['css'][] = '<link href="css/default/chosen/chosen.min.css" rel="stylesheet" type="text/css">';
    $htmlExtraInformation['js'][] = '<script src="js/default/plugins/chosen/chosen.jquery.min.js" type="text/javascript"></script>';

    $query = $sql->prepare("SELECT `defaultdns`,`publickey`,`keyname`,`ssh2ip`,`serverdir`,`bitversion`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
    $query->execute(array(':aeskey' => $aeskey, ':id' => $rootID, ':reseller_id' => $resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $rootServer) {
        $foundRoot = true;
    }

    if ($id and $ui->st('d', 'get') == 'md') {

        $query = $sql->prepare("SELECT d.`active`,d.`dns`,d.`ip`,d.`port`,d.`tsdnsID`,d.`userID`,m.`ssh2ip`,m.`description`,CONCAT(u.`cname`,' ',u.`vname`,' ',u.`name`) AS `user_name` FROM `voice_dns` AS d INNER JOIN `voice_tsdns` AS m ON m.`id`=d.`tsdnsID` INNER JOIN `userdata` AS u ON u.`id`=d.`userID` WHERE d.`dnsID`=? AND d.`resellerID`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            if (!$ui->st('action', 'post')) {
                $active = $row['active'];
                $ip = $row['ip'];
                $port = $row['port'];
            }

            $userName = trim($row['user_name']);

            $table2[$row['tsdnsID']] = trim($row['ssh2ip'] . ' ' . $row['description']);

            $oldActive = $row['active'];
            $oldDns = $row['dns'];
            $oldIp = $row['ip'];
            $oldPort = $row['port'];
        }
    }

    if (count($errors) == 0 and ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') and isset($rootServer)) {

        if (!$active) {
            $errors['active'] = $sprache->active;
        }

        if (!$dns) {
            $errors['dns'] = $sprache->dns;
        }

        if (!$ip) {
            $errors['ip'] = $sprache->ip;
        }

        if (!$port) {
            $errors['port'] = $sprache->port;
        }

        if ($ui->st('action', 'post') == 'ad') {

            if (!$userID) {

                $errors['userID'] = $sprache->user;

            } else {

                $query = $sql->prepare("SELECT 1 FROM `userdata` WHERE `id`=? AND `resellerid`=? AND `accounttype`='u' LIMIT 1");
                $query->execute(array($userID, $resellerLockupID));

                if ($query->rowCount() == 0) {
                    $errors['userID'] = $sprache->user;
                }
            }
        }

        if ($dns and $ip and $port) {
            $query = $sql->prepare("SELECT `dns`,`ip`,`port` FROM `voice_dns` WHERE `dnsID`!=? AND `resellerID`=? AND `active`='Y' AND (`dns`=? OR (`ip`=? AND `port`=?)) LIMIT 1");
            $query->execute(array($id, $resellerLockupID, $dns, $ip, $port));
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                if (isset($rootServer['defaultdns']) and $dns != $rootServer['defaultdns'] and $dns == $row['dns']) {
                    $errors['dns'] = $sprache->dns;
                } else if ($ip == $row['ip'] and $port == $row['port']) {
                    $errors['ip'] = $sprache->ip;
                    $errors['port'] = $sprache->port;
                }
            }
        }

        if (count($errors) == 0) {

            if ($ui->st('action', 'post') == 'ad' and isset($rootServer)) {

                $query = $sql->prepare("INSERT INTO `voice_dns` (`active`,`dns`,`ip`,`port`,`tsdnsID`,`userID`,`externalID`,`resellerID`) VALUES (?,?,?,?,?,?,?,?)");
                $query->execute(array($active, $dns, $ip, $port, $rootID, $userID, $externalID, $resellerLockupID));
                $rowCount = $query->rowCount();

                $id = $sql->lastInsertId();

                if ($dns == $rootServer['defaultdns']) {

                    $dns = strtolower($id . '-' . getusername($userID) . '.' . $rootServer['defaultdns']);

                    $query = $sql->prepare("UPDATE `voice_dns` SET `dns`=? WHERE `dnsID`=? LIMIT 1");
                    $query->execute(array($dns, $id));

                    $rowCount += $query->rowCount();
                }

                $loguseraction = '%add% %voserver% ' . $ip . ':' . $port . ' ' . $dns;

            } else if ($ui->st('action', 'post') == 'md' and $id and isset($rootServer)) {

                $query = $sql->prepare("UPDATE `voice_dns` SET `active`=?,`dns`=?,`ip`=?,`port`=?,`externalID`=? WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($active, $dns, $ip, $port, $externalID, $id, $resellerLockupID));
                $rowCount = $query->rowCount();

                $loguseraction = '%mod% %voserver% ' . $ip . ':' . $port . ' ' . $dns;
            }

            if (isset($rowCount) and $rowCount > 0) {

                if (isset($rootServer)) {

                    if ($active == 'Y' and ($ui->st('action', 'post') == 'ad' or ($ui->st('action', 'post') == 'md' and isset($oldActive) and $oldActive == 'N'))) {
                        $serverReturn = tsdns('md', $rootServer['ssh2ip'], $rootServer['decryptedssh2port'], $rootServer['decryptedssh2user'], $rootServer['publickey'], $rootServer['keyname'], $rootServer['decryptedssh2password'], 0, $rootServer['serverdir'], $rootServer['bitversion'], array($ip), array($port), array($dns), $resellerLockupID);
                    }

                    if ($ui->st('action', 'post') == 'md') {

                        if ($active == 'N' and isset($oldActive) and $oldActive == 'Y') {
                            $serverReturn = tsdns('md', $rootServer['ssh2ip'], $rootServer['decryptedssh2port'], $rootServer['decryptedssh2user'], $rootServer['publickey'], $rootServer['keyname'], $rootServer['decryptedssh2password'], 0, $rootServer['serverdir'], $rootServer['bitversion'], array($oldIp), array($oldPort), array($oldDns), $resellerLockupID);
                        }

                        if ($active == 'Y' and ($ip != $oldIp or $port != $oldPort or $dns != $oldDns)) {
                            $serverReturn = tsdns('md', $rootServer['ssh2ip'], $rootServer['decryptedssh2port'], $rootServer['decryptedssh2user'], $rootServer['publickey'], $rootServer['keyname'], $rootServer['decryptedssh2password'], 0, $rootServer['serverdir'], $rootServer['bitversion'], array($ip, $oldIp), array($port, $oldPort), array($dns, $oldDns), $resellerLockupID);
                        }
                    }
                }

                if (isset($serverReturn) and $serverReturn != 'ok') {

                    $template_file = $serverReturn;

                } else {
                    $insertlog->execute();
                    $template_file = $spracheResponse->table_add;
                }


                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }
        }
    }

    // An error occurred during validation
    // unset the redirect information and display the form again
    if (!$ui->smallletters('action', 2, 'post') or count($errors) != 0) {

        unset($header, $text);

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $table = getUserList($resellerLockupID);

            $query = $sql->prepare("SELECT m.`id`,m.`ssh2ip`,m.`description`, COUNT(d.`dnsID`)/(m.`max_dns`/100) AS `usedpercent` FROM `voice_tsdns` AS m LEFT JOIN `voice_dns` AS d ON d.`tsdnsID`=m.`id` WHERE m.`resellerid`=? AND m.`active`='Y' GROUP BY m.`id` HAVING `usedpercent`<100 ORDER BY `usedpercent` ASC");
            $query->execute(array($resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $table2[$row['id']] = trim($row['ssh2ip'] . ' ' . $row['description']);
            }

            $template_file = 'admin_voice_dns_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            // Check if database entry exists and if not display 404 page
            $template_file =  (isset($oldActive)) ? 'admin_voice_dns_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }
    }

} else if ($ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT `dns`,`ip`,`port`,`tsdnsID` FROM `voice_dns` WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $ip = $row['ip'];
        $port = $row['port'];
        $dns = $row['dns'];
        $tsdnsRootID = $row['tsdnsID'];
        $deleteDNS = $row['ip'] . ' ' . $row['port'] . ' ' . $row['dns'];
    }

    $serverFound = $query->rowCount();

    if ($ui->st('action', 'post') == 'dl' and count($errors) == 0 and $serverFound > 0) {

        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
        $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsRootID,':reseller_id' => $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $dnsReturn = tsdns('dl', $row['ssh2ip'], $row['decryptedssh2port'], $row['decryptedssh2user'], $row['publickey'], $row['keyname'], $row['decryptedssh2password'], 0, $row['serverdir'], $row['bitversion'], array($ip), array($port), array($dns), $resellerLockupID);
        }

        $query = $sql->prepare("DELETE FROM `voice_dns` WHERE `dnsID`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));

        if ($query->rowCount() > 0) {

            $loguseraction = '%del% %voserver% %dns% ' . $deleteDNS;
            $insertlog->execute();

            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = $spracheResponse->error_table;
        }
    }

    // Nothing submitted yet or csfr error, display the delete form
    if (!$ui->st('action', 'post') or count($errors) != 0) {
        // Check if we could find an entry and if not display 404 page
        $template_file = ($serverFound > 0) ? 'admin_voice_dns_dl.tpl' : 'admin_404.tpl';
    }

} else {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=tsdns');

    $template_file = 'admin_voice_dns_list.tpl';
}