<?php

/**
 * File: tables_entries_repair.php.
 * Author: Ulrich Block
 * Date: 10.01.14
 * Time: 22:04
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

if (!isset($displayToUser) and (!isset($admin_id) or $main != 1 or $reseller_id != 0)) {
    header('Location: admin.php');
    die('No Access');
}

$query = $sql->prepare("SELECT DISTINCT(`id`) FROM `userdata` u WHERE `accounttype`='r' AND NOT EXISTS (SELECT 1 FROM `settings` WHERE `resellerid`=u.`id`)");
$query2 = $sql->prepare("INSERT INTO `settings` (`resellerid`) VALUES (?)");
$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $query2->execute(array($row['id']));
}

$query = $sql->prepare("SELECT DISTINCT(`id`) FROM `userdata` u WHERE `accounttype`='r' AND NOT EXISTS (SELECT 1 FROM `lendsettings` WHERE `resellerid`=u.`id`)");
$query2 = $sql->prepare("INSERT INTO `lendsettings` (`resellerid`) VALUES (?)");
$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $query2->execute(array($row['id']));
}