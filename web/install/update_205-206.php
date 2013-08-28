<?php
/**
 * File: update_205-206.php.
 * Author: Ulrich Block
 * Date: 20.10.11
 *
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
if (isset($include) and $include==true) {
$create_voice_server_backup=$sql->prepare("CREATE TABLE IF NOT EXISTS `voice_server_backup` (
`id` INT(30) UNSIGNED NOT NULL AUTO_INCREMENT,
`sid` INT(30) UNSIGNED NOT NULL,
`uid` INT(30) UNSIGNED DEFAULT NULL,
`name` VARCHAR(50) NULL,
`snapshot` BLOB,
`date` DATETIME DEFAULT NULL,
`resellerid` INT(30) UNSIGNED DEFAULT NULL,
PRIMARY KEY (`id`)
)");
$create_voice_server_backup->execute();
$response->add('Action: create_voice_server_backup done: ');
$error=$create_voice_server_backup->errorinfo();
$create_voice_server_backup->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$alter_settings=$sql->prepare("ALTER TABLE `settings`
ADD COLUMN `voice_autobackup` ENUM('Y','N') DEFAULT 'Y' AFTER `master`,
ADD COLUMN `voice_autobackup_intervall` INT(5) UNSIGNED DEFAULT '7' AFTER `voice_autobackup`,
ADD COLUMN `voice_maxbackup` INT(5) UNSIGNED DEFAULT '5' AFTER `voice_autobackup_intervall`");
$alter_settings->execute();
$response->add('Action: alter_settings done: ');
$error=$alter_settings->errorinfo();
$alter_settings->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');
} else {
	echo "Error: this file needs to be included by the updater!<br />";
}