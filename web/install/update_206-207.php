<?php
/**
 * File: update_207-208.php.
 * Author: Ulrich Block
 * Date: 23.11.11
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
$alter_voice_server_add=$sql->prepare("ALTER TABLE `voice_server` ADD COLUMN `backup` ENUM('Y','N') DEFAULT 'Y' AFTER `active`");
$alter_voice_server_add->execute();
$response->add('Action: alter_voice_server_add done: ');
$error=$alter_voice_server_add->errorinfo();
$alter_voice_server_add->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');
$alter_voice_server_change=$sql->prepare("ALTER TABLE `voice_server` CHANGE `file_sent` `maxtraffic` INT( 255 ) NULL DEFAULT '1048576000',
CHANGE `file_received` `filetraffic` INT( 255 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE `sent` `lastfiletraffic` INT( 255 ) UNSIGNED NULL DEFAULT NULL");
$alter_voice_server_change->execute();
$response->add('Action: alter_voice_server_change done: ');
$error=$alter_voice_server_change->errorinfo();
$alter_voice_server_change->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');
$alter_voice_server_drop=$sql->prepare("ALTER TABLE `voice_server` DROP `received`");
$alter_voice_server_drop->execute();
$response->add('Action: alter_voice_server_drop done: ');
$error=$alter_voice_server_drop->errorinfo();
$alter_voice_server_drop->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');
} else {
	echo "Error: this file needs to be included by the updater!<br />";
}