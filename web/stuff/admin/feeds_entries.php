<?php

/**
 * File: feeds_entries.php.
 * Author: Ulrich Block
 * Date: 30.06.12
 * Time: 12:24
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

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('feeds',$user_language,$reseller_id);
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

$lookUpID = ($reseller_id != 0 and $admin_id != $reseller_id) ? $admin_id: $reseller_id;

if ($ui->st('d', 'get') == 'ud') {

    $newsInclude = true;

    include(EASYWIDIR . '/stuff/methods/feeds_function.php');

} else if ($ui->st('d', 'get') == 'md') {

    $ids = (array) $ui->active('ids', 'post');

    $query = $sql->prepare("DELETE FROM `feeds_news` WHERE `newsID`=? AND `resellerID`=? LIMIT 1");
    $query2 = $sql->prepare("UPDATE `feeds_news` SET `active`=? WHERE `newsID`=? AND `resellerID`=?");

    foreach($ids as $id => $values) {

        if (isset($values->dl) and $values->dl == 'Y') {

            $query->execute(array($id, $lookUpID));

        } else {

            if (isset($values->active) and $values->active == 'Y') {
                $query2->execute(array('Y', $id, $lookUpID));
            } else {
                $query2->execute(array('N', $id, $lookUpID));
            }
        }
    }

    $template_file = $spracheResponse->table_add;

} else {

    configureDateTables('-2', '2, "desc"', 'ajax.php?w=datatable&d=feedsnewsentries');

    $template_file = 'admin_feeds_entries_list.tpl';
}