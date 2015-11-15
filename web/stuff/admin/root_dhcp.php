<?php

/**
 * File: root_dhcp.php.
 * Author: Ulrich Block
 * Date: 29.04.12
 * Time: 11:56
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

if (!isset($admin_id) or $main != 1 or  $reseller_id != 0 or !$pa['vserversettings']) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$sprache = getlanguagefile('reseller', $user_language, $reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id == 0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller = 0;
}

// Define the ID variable which will be used at the form and SQLs
$id = $ui->id('id', 10, 'get');
$publickey = $ui->w('publickey', 1, 'post');
$keyname = $ui->startparameter('keyname', 'post');
$active = $ui->active('active', 'post');
$ip = $ui->ip('ip', 'post');
$port = $ui->port('port', 'post');
$user = $ui->username('user',255, 'post');
$pass = $ui->password('pass',255, 'post');
$startCmd = $ui->startparameter('startCmd', 'post');
$dhcpFile = $ui->startparameter('dhcpFile', 'post');
$description = $ui->escaped('description', 'post');

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {

	unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_root_dhcp_ad.tpl' : 'admin_root_dhcp_md.tpl';

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    // At this point all variables are defined that can come from the user

    // Default variables. Mostly needed for the add operation
    $defaultVar = ($ui->id('id', 10, 'get')) ? $ui->id('id', 10, 'get') : 10;

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $template_file = 'admin_root_dhcp_ad.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass` FROM `rootsDHCP` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':aeskey' => $aeskey,':id' => $id,':reseller_id' => $reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $active = $row['active'];
                $ip = $row['ip'];
                $port = $row['decryptedport'];
                $user = $row['decrypteduser'];
                $pass = $row['decryptedpass'];
                $publickey = $row['publickey'];
                $keyname = $row['keyname'];
                $ips = $row['ips'];
                $netmask = $row['netmask'];
                $startCmd = $row['startCmd'];
                $dhcpFile = $row['dhcpFile'];
                $description = $row['description'];
            }

            // Check if database entry exists and if not display 404 page
            $template_file =  ($query->rowCount() > 0) ? 'admin_root_dhcp_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$ui->active('active', 'post')) {
            $errors['active'] = $sprache->active;
        }

        if (!$ui->w('publickey', 1, 'post')) {
            $errors['publickey'] = $sprache->keyuse;
        }

        if (!$ui->ip('ip', 'post')) {
            $errors['ip'] = $sprache->ssh_ip;
        }

        if (!$ui->port('port', 'post')) {
            $errors['port'] = $sprache->ssh_port;
        }

        if (!$ui->username('user', 255, 'post')) {
            $errors['user'] = $sprache->ssh_user;
        }


        if ($publickey != 'N' and !is_file(EASYWIDIR . '/keys/' . $keyname)) {
            $errors['keyname'] = $sprache->keyname;
        }

        $ssh2Check = (count($errors) == 0) ? ssh_check($ip, $port, $user, $publickey, $keyname, $pass) : true;

        if ($ssh2Check !== true) {

            if ($ssh2Check == 'ipport') {
                $errors['ip'] = $sprache->ssh_ip;
                $errors['port'] = $sprache->ssh_port;

            } else {

                $errors['user'] = $sprache->ssh_user;
                $errors['publickey'] = $sprache->keyuse;

                if ($publickey == 'N') {

                    $errors['pass'] = $sprache->ssh_pass;

                } else if (!$ui->active('publickey', 'post') == 'B') {

                    $errors['pass'] = $sprache->ssh_pass;
                    $errors['keyname'] = $sprache->keyname;

                } else {

                    $errors['keyname'] = $sprache->keyname;

                }
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `rootsDHCP` (`active`,`ip`,`port`,`user`,`pass`,`publickey`,`keyname`,`startCmd`,`dhcpFile`,`description`,`resellerid`) VALUES (:active,:ip,AES_ENCRYPT(:port,:aeskey),AES_ENCRYPT(:user,:aeskey),AES_ENCRYPT(:pass,:aeskey),:publickey,:keyname,:startCmd,:dhcpFile,:description,:reseller_id)");
                $query->execute(array(':active' => $active,':ip' => $ip,':port' => $port,':aeskey' => $aeskey,':user' => $user,':pass' => $pass,':publickey' => $publickey,':keyname' => $keyname,':startCmd' => $startCmd,':dhcpFile' => $dhcpFile,':description' => $description,':reseller_id' => $reseller_id));

                $rowCount = $query->rowCount();
                $loguseraction = '%add% DHCP ' . $ip;

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                $query = $sql->prepare("UPDATE `rootsDHCP` SET `active`=:active,`ip`=:ip,`port`=AES_ENCRYPT(:port,:aeskey),`user`=AES_ENCRYPT(:user,:aeskey),`pass`=AES_ENCRYPT(:pass,:aeskey),`publickey`=:publickey,`keyname`=:keyname,`startCmd`=:startCmd,`dhcpFile`=:dhcpFile,`description`=:description WHERE `id`=:id AND `resellerid`=:reseller_id");
                $query->execute(array(':active' => $active,':ip' => $ip,':port' => $port,':aeskey' => $aeskey,':user' => $user,':pass' => $pass,':publickey' => $publickey,':keyname' => $keyname,':startCmd' => $startCmd,':dhcpFile' => $dhcpFile,':description' => $description,':id' => $id,':reseller_id' => $reseller_id));

                $rowCount = $query->rowCount();
                $loguseraction = '%mod% DHCP ' . $ip;
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
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_root_dhcp_ad.tpl' : 'admin_root_dhcp_md.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT `ip`,`description` FROM `rootsDHCP` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id, $reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $ip = $row['ip'];
        $description = $row['description'];
    }

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_root_dhcp_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl') {

        $query = $sql->prepare("DELETE FROM `rootsDHCP` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount() > 0) {

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% DHCP ' . $ip;
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
        $orderby = '`id` DESC';
    } else {
        $orderby = '`id` ASC';
        $o = 'ai';
    }

    $query = $sql->prepare("SELECT `active`,`id`,`ip`,`description`,`notified` FROM `rootsDHCP` WHERE `resellerid`=? ORDER BY $orderby");
    $query->execute(array($reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        if ($row['active'] == 'Y' and $row['notified'] > 0) {
            $imgName = '16_error';
            $imgAlt = 'Crashed';
        } else if ($row['active'] == 'Y') {
            $imgName = '16_ok';
            $imgAlt = 'Active';
        } else {
            $imgName = '16_bad';
            $imgAlt = 'Inactive';
        }

        $table[] = array('id' => $row['id'], 'active' => $row['active'], 'ip' => $row['ip'], 'description' => $row['description'], 'img' => $imgName,'alt' => $imgAlt);

    }

    $template_file = 'admin_root_dhcp_list.tpl';

}