<?php

/**
 * File: admin_social_provider.php.
 * Author: Ulrich Block
 * Date: 16.02.14
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['root'])) {
    header('Location: admin.php');
    die('No acces');
}

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

$sprache = getlanguagefile('login', $user_language, $reseller_id);

// Define the ID variable which will be used at the form and SQLs
$id = $ui->id('id', 10, 'get');
$active = $ui->active('active', 'post');
$name = $ui->w('name', 255, 'post');
$keyID = $ui->escaped('keyID', 'post');
$providerToken = $ui->escaped('providerToken', 'post');


// At this point all variables are defined that can come from the user

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    $serviceProviders = array();

    foreach (scandir(EASYWIDIR . '/third_party/hybridauth/Hybrid/Providers/') as $sp) {
        if ($sp != '.' and $sp != '..') {
            $serviceProviders[] = substr($sp, 0 , (strlen($sp) - 4));
        }
    }

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        $query = $sql->prepare("SELECT `pageurl` FROM `page_settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($resellerLockupID));

        $sprache->multipleHelperEndpoint = str_replace('//login.php', '/login.php', str_replace('%url%',  '<b>' . $query->fetchColumn() . '/login.php' . '</b>', $sprache->multipleHelperEndpoint));

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $template_file = 'admin_social_provider_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT * FROM `userdata_social_providers` WHERE `serviceProviderID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $active = (string) $row['active'];
                $name = (string) $row['filename'];
                $keyID = (string) $row['identifier'];
                $providerToken = (string) $row['token'];
            }

            // Check if database entry exists and if not display 404 page
            $template_file =  ($query->rowCount() > 0) ? 'admin_social_provider_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$active) {
            $errors['active'] = $sprache->active;
        }

        if (!$name or !in_array($name, $serviceProviders)) {

            $errors['name'] = 'Social Auth Provider';

        } else {

            if ($ui->st('d', 'get') == 'ad') {

                $query = $sql->prepare("SELECT 1 FROM `userdata_social_providers` WHERE `filename`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($name, $resellerLockupID));

            } else {

                $query = $sql->prepare("SELECT 1 FROM `userdata_social_providers` WHERE `filename`=? AND `serviceProviderID`!=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($name, $id, $resellerLockupID));

            }

            if ($query->rowCount() > 0) {
                $errors['name'] = 'Social Auth Provider';
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `userdata_social_providers` (`filename`,`active`,`identifier`,`token`,`resellerID`) VALUES (?,?,?,?,?)");
                $query->execute(array($name, $active, $keyID, $providerToken, $resellerLockupID));
                $rowCount = $query->rowCount();
                $loguseraction = '%add% Social Provider ' . $name;

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                $query = $sql->prepare("UPDATE `userdata_social_providers` SET `filename`=?,`active`=?,`identifier`=?,`token`=?WHERE `serviceProviderID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($name, $active, $keyID, $providerToken, $id, $resellerLockupID));
                $rowCount = $query->rowCount();
                $loguseraction = '%mod% Social Provider ' . $name;
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
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_social_provider_add.tpl' : 'admin_social_provider_md.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $id) {

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        $query = $sql->prepare("SELECT `filename` FROM `userdata_social_providers` WHERE `serviceProviderID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        $name = $query->fetchColumn();

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_social_provider_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl') {

        $query = $sql->prepare("DELETE FROM `userdata_social_providers` WHERE `serviceProviderID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount() > 0) {

            $query = $sql->prepare("DELETE FROM `userdata_social_identities` WHERE `serviceProviderID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% Social Provider ' . $name;
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

    $query = $sql->prepare("SELECT `serviceProviderID`,`active`,`filename`  FROM `userdata_social_providers` WHERE `resellerID`=?");
    $query->execute(array($resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[] = array('id' => $row['serviceProviderID'], 'active' => $row['active'], 'name' => $row['filename']);
    }

    $template_file = 'admin_social_provider_list.tpl';
}