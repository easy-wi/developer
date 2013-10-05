<?php
/**
 * File: update_203-204.php.
 * Author: Ulrich Block
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
$create_mail_log=$sql->prepare("CREATE TABLE IF NOT EXISTS `mail_log` (
`id` INT(30) UNSIGNED NOT NULL AUTO_INCREMENT,
`uid` INT(30) UNSIGNED DEFAULT NULL,
`topic` VARCHAR(255) NOT NULL,
`text` BLOB,
`date` DATETIME NOT NULL,
`resellerid` INT(30) UNSIGNED DEFAULT '0',
PRIMARY KEY (`id`)
)");
$create_mail_log->execute();
$create_mail_log->closecursor();

$drop_rootstatus=$sql->prepare("DROP TABLE `rootstatus`");
$drop_rootstatus->execute();
$response->add('Action: drop_rootstatus done: ');
$error=$drop_rootstatus->errorinfo();
$drop_rootstatus->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$alter_voice_masterserver=$sql->prepare("ALTER TABLE `voice_masterserver` ADD COLUMN `autorestart` ENUM('Y','N') DEFAULT 'Y' AFTER `notified`");
$alter_voice_masterserver->execute();
$response->add('Action: alter_voice_masterserver done: ');
$error=$alter_voice_masterserver->errorinfo();
$alter_voice_masterserver->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');
} else {
	echo "Error: this file needs to be included by the updater!<br />";
}