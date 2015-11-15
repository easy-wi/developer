<?php

/**
 * File: crud.php.
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


// Define the ID variable which will be used at the form and SQLs
$id = $ui->id('id', 10, 'get');

// Default variables. Mostly needed for the add operation
$defaultVar = ($ui->id('id', 10, 'get')) ? $ui->id('id', 10, 'get') : 10;

// At this point all variables are defined that can come from the user

$table = array();

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action', 4, 'post') and !token(true)) {

    unset($header, $text);

    $errors = array('token' => $spracheResponse->token);

} else {
    $errors = array();
}

if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Add jQuery plugin chosen to the header
	/*
    $htmlExtraInformation['css'][] = '<link href="css/default/chosen/chosen.min.css" rel="stylesheet" type="text/css">';
    $htmlExtraInformation['js'][] = '<script src="js/default/plugins/chosen/chosen.jquery.min.js" type="text/javascript"></script>';
	*/

    if ($id and $ui->st('d', 'get') == 'md') {

        $query = $sql->prepare("SELECT `a`,b`,`c` FROM `table` WHERE `id`=? AND `reseller_id`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            if (!$ui->st('action', 'post')) {
                $a = $row['a'];
                $b = $row['b'];
                $c = $row['c'];
            }

            $oldA = $row['a'];
            $oldB = $row['b'];
            $oldC = $row['c'];
        }
    }

    if (count($errors) == 0 and ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad')) {

        if (!$active) {
            $errors['active'] = $sprache->active;
        }

        if (count($errors) == 0) {

            if ($ui->st('action', 'post') == 'ad' and isset($rootServer)) {

                $query = $sql->prepare("INSERT INTO `table` (`a`,`b`,`c`,`reseller_id`) VALUES (?,?,?,?)");
                $query->execute(array($a, $b, $c, $resellerLockupID));
                $rowCount = $query->rowCount();

                $loguseraction = '%add% %yourmodule% ' . $a;

            } else if ($ui->st('action', 'post') == 'md' and $id and isset($rootServer)) {

                $query = $sql->prepare("UPDATE `table` SET `a`=?,`b`=?,`c`=? WHERE `id`=? AND `reseller_id`=? LIMIT 1");
                $query->execute(array($a, $b, $c, $id, $resellerLockupID));
                $rowCount = $query->rowCount();

                $loguseraction = '%mod% %yourmodule% ' . $a;
            }

            if (isset($rowCount) and $rowCount > 0) {

                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

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

            $template_file = 'admin_your_module_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            // Check if database entry exists and if not display 404 page
            $template_file =  (isset($oldActive)) ? 'admin_your_module_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }
    }

} else if ($ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT `a`,`b`,`c` FROM `table` WHERE `id`=? AND `reseller_id`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $a = $row['a'];
    }

    $serverFound = $query->rowCount();

    if ($ui->st('action', 'post') == 'dl' and count($errors) == 0 and $serverFound > 0) {

        $query = $sql->prepare("DELETE FROM `table` table `id`=? AND `reseller_id`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));

        if ($query->rowCount() > 0) {

            $loguseraction = '%del% %yourmodule% ' . $a;
            $insertlog->execute();

            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = $spracheResponse->error_table;
        }
    }

    // Nothing submitted yet or csfr error, display the delete form
    if (!$ui->st('action', 'post') or count($errors) != 0) {
        // Check if we could find an entry and if not display 404 page
        $template_file = ($serverFound > 0) ? 'admin_your_module_dl.tpl' : 'admin_404.tpl';
    }

} else {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=yourmodule');

    $template_file = 'admin_your_module_list.tpl';
}