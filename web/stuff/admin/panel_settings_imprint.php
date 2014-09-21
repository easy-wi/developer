<?php

/**
 * File: panel_settings_imprint.php.
 * Author: Ulrich Block
 * Date: 24.11.13
 * Time: 11:22
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['settings'])) {
    header('Location: login.php');
    die;
}

$sprache = getlanguagefile('settings', $user_language, $reseller_id);
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

if ($reseller_id != 0 and $admin_id != $reseller_id) {
    $reseller_id = $admin_id;
}

if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;

} else if ($ui->st('action', 'post') == 'md') {

    $query = $sql->prepare("SELECT `id` FROM `imprints` WHERE `language`=? AND `resellerid`=? LIMIT 1");
    $query2 = $sql->prepare("UPDATE imprints SET `imprint`=? WHERE `language`=? AND `resellerid`=? LIMIT 1");
    $query3 = $sql->prepare("INSERT INTO `imprints` (`language`,`imprint`,`resellerid`) VALUES (?,?,?)");

    if ($ui->st('languages', 'post')) {

        $languages = (array) $ui->st('languages', 'post');

        foreach ($languages as $language) {

            $description = $ui->escaped('description', 'post', $language);

            $query->execute(array($language, $reseller_id));

            if ($query->rowCount() == 1) {

                $query2->execute(array($description, $language, $reseller_id));

                if ($query2->rowCount() > 0) {
                    $changed = true;
                }

            } else {
                $query3->execute(array($language, $description, $reseller_id));

                if ($query3->rowCount() > 0) {
                    $changed = true;
                }
            }
        }

        $query = $sql->prepare("SELECT `language` FROM `imprints` WHERE `resellerid`=?");
        $query2 = $sql->prepare("DELETE FROM `imprints` WHERE `language`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            if (isset($row['language']) and !in_array($row['language'], $languages)) {
                $query2->execute(array($row['language'], $reseller_id));
                if ($query2->rowCount() > 0) {
                    $changed = true;
                }
            }

        }

    } else {
        $query = $sql->prepare("DELETE FROM `imprints` WHERE `resellerid`=?");
        $query->execute(array($reseller_id));
        if ($query->rowCount() > 0) {
            $changed = true;
        }
    }

    if (isset($changed)) {
        $loguseraction = "%mod% %settings% %imprint%";
        $insertlog->execute();
        $template_file = $spracheResponse->table_add;
    } else {
        $template_file = $spracheResponse->error_table;
    }

} else {

    $foundLanguages = array();

    $query = $sql->prepare("SELECT `template` FROM `settings`  WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    $template_choosen = $query->fetchColumn();

    if ($query->rowCount() > 0) {

        foreach (getlanguages($template_choosen) as $langrow) {

            $query = $sql->prepare("SELECT `imprint` FROM `imprints` WHERE `language`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($langrow, $reseller_id));

            $foundLanguages[] = array('lang' => $langrow, 'imprint' => $query->fetchColumn(), 'style' => $query->rowCount());
        }

        $template_file = 'admin_settings_imprint.tpl';

    } else {
        $template_file = 'admin_404.tpl';
    }
}