<?php
/**
 * File: update_202-203.php.
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
$create_voice_stats=$sql->prepare("CREATE TABLE IF NOT EXISTS `voice_server_stats` (
`id` INT(30) UNSIGNED NOT NULL AUTO_INCREMENT,
`sid` INT(30) UNSIGNED NOT NULL,
`mid` INT(30) UNSIGNED NOT NULL,
`installed` DECIMAL(6,2) UNSIGNED NOT NULL,
`used` DECIMAL(6,2) UNSIGNED NOT NULL,
`date` DATETIME DEFAULT NULL,
`uid` INT(30) UNSIGNED DEFAULT NULL,
`resellerid` INT(30) UNSIGNED DEFAULT NULL,
PRIMARY KEY (`id`)
)");
$create_voice_stats->execute();
$create_voice_stats->closecursor();
$response->add('Action: create_voice_stats done: ');
$create_voice_stats_hours=$sql->prepare("CREATE TABLE IF NOT EXISTS `voice_server_stats_hours` (
`id` INT(30) UNSIGNED NOT NULL AUTO_INCREMENT,
`sid` INT(30) UNSIGNED NOT NULL,
`mid` INT(30) UNSIGNED NOT NULL,
`installed` DECIMAL(6,2) UNSIGNED NOT NULL,
`used` DECIMAL(6,2) UNSIGNED NOT NULL,
`date` DATETIME DEFAULT NULL,
`uid` INT(30) UNSIGNED DEFAULT NULL,
`resellerid` INT(30) UNSIGNED DEFAULT NULL,
PRIMARY KEY (`id`)
)");
$create_voice_stats_hours->execute();
$create_voice_stats_hours->closecursor();
$response->add('Action: create_voice_stats_hours done: ');
$create_voice_stats_settings=$sql->prepare("CREATE TABLE IF NOT EXISTS `voice_stats_settings` (
`id` INT(30) UNSIGNED NOT NULL AUTO_INCREMENT,
`text_colour_1` INT(3) UNSIGNED DEFAULT '0' NULL,
`text_colour_2` INT(3) UNSIGNED DEFAULT '0' NULL,
`text_colour_3` INT(3) UNSIGNED DEFAULT '0' NULL,
`barin_colour_1` INT(3) UNSIGNED DEFAULT '0' NULL,
`barin_colour_2` INT(3) UNSIGNED DEFAULT '206' NULL,
`barin_colour_3` INT(3) UNSIGNED DEFAULT '209' NULL,
`barout_colour_1` INT(3) UNSIGNED DEFAULT '0' NULL,
`barout_colour_2` INT(3) UNSIGNED DEFAULT '191' NULL,
`barout_colour_3` INT(3) UNSIGNED DEFAULT '255' NULL,
`bg_colour_1` INT(3) UNSIGNED DEFAULT '240' NULL,
`bg_colour_2` INT(3) UNSIGNED DEFAULT '240' NULL,
`bg_colour_3` INT(3) UNSIGNED DEFAULT '255' NULL,
`border_colour_1` INT(3) UNSIGNED DEFAULT '200' NULL,
`border_colour_2` INT(3) UNSIGNED DEFAULT '200' NULL,
`border_colour_3` INT(3) UNSIGNED DEFAULT '200' NULL,
`line_colour_1` INT(3) UNSIGNED DEFAULT '220' NULL,
`line_colour_2` INT(3) UNSIGNED DEFAULT '220' NULL,
`line_colour_3` INT(3) UNSIGNED DEFAULT '220' NULL,
`resellerid` INT(30) UNSIGNED DEFAULT NULL,
 PRIMARY KEY (`id`)
)");
$create_voice_stats_settings->execute();
$create_voice_stats_settings->closecursor();
$response->add('Action: create_voice_stats_hours done: ');
$instert_voice_stats_settings=$sql->prepare("INSERT INTO `voice_stats_settings` (`resellerid`) VALUES ('0')");
$instert_voice_stats_settings->execute();
$response->add('Action: instert_voice_stats_settings done: ');
$error=$instert_voice_stats_settings->errorinfo();
$instert_voice_stats_settings->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');
$pselect=$sql->prepare("SELECT `resellerid` FROM `resellerdata`");
$pselect->execute(array());
foreach ($pselect->fetchall() as $row) {
	$instert_voice_stats_settings=$sql->prepare("INSERT INTO `voice_stats_settings` (`resellerid`) VALUES (?)");
	$instert_voice_stats_settings->execute(array($row['resellerid']));
	$response->add('Action: instert_voice_stats_settings done: ');
	$error=$instert_voice_stats_settings->errorinfo();
	$instert_voice_stats_settings->closecursor();
	if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
	else $response->add('OK<br />');
}
} else {
	echo "Error: this file needs to be included by the updater!<br />";
}