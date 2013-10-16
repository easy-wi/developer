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

 
// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

	// Error handling. Check if required attributes are set and can be validated
    $errors = array();

	// At this point all variables are defined that can come from the user
    $id = $ui->id('id', 10, 'get');

	// Default variables. Mostly needed for the add operation
    $defaultVar = ($ui->id('id', 10, 'get')) ? $ui->id('id', 10, 'get') : 10;

	// Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

		// Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {
            $template_file = 'admin_roots_add.tpl';

		// Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {
		
			// Check if database entry exists and if not display 404 page
            $template_file =  ($query->rowCount() > 0) ? 'admin_roots_md.tpl' : 'admin_404.tpl';

		// Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

	// Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$ui->active('active', 'post')) {
            $errors['active'] = $sprache->active;
        }

		// Submitted values are OK
        if (count($errors) == 0) {

			// Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $rowCount = $query->rowCount();
                $loguseraction = '%add% %root% ' . $ip;

            } else if ($ui->st('action', 'post') == 'md') {

               $rowCount = $query->rowCount();
                $loguseraction = '%mod% %root% ' . $ip;
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
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_roots_add.tpl' : 'admin_roots_md.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

	// Define the ID variable which will be used at the form and SQLs
    $id = $ui->id('id', 10, 'get');

	// Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

		// Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_roots_dl.tpl' : 'admin_404.tpl';

	// User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl') {

		// Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount()>0) {
		
            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% %root% ' . $ip;
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
}