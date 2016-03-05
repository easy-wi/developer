<?php

/**
 * File: update_521-522.php.
 * Author: Ulrich Block
 * Date: 05.03.16
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


if (isset($include) and $include == true) {

    $response->add('Action: Update to new skin color template system');

    $query = $sql->prepare("UPDATE `settings` SET `template`='default',`templateColor`=`template` WHERE `template` IN ('black','black-light','blue','blue-light','green','green-light','purple','purple-light','red','red-light','yellow','yellow-light')");
    $query->execute();
    $query->closecursor();

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES ('5.22','','')");
    $query->execute();
    $query->closecursor();

    $response->add('Action: insert_easywi_version done.');

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}