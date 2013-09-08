<?php
/**
 * File: update_208-209.php.
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
$insert_easywi_version=$sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('2.09','<div align=\"right\">09.01.2012</div>
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>Externe MYSQL Datenbanken k&ouml;nnen nun samt Eintr&auml;gen in der Host Tabelle verwaltet werden. Das Anlegen kann manuell und automatisiert beim Anlegen eines Gameservers erfolgen.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Fehler behoben, dass die L&auml;nderauswahl nicht angezeigt wird.</li>
<li>Fehler \"Unknown column customerid in where clause in gserver.php:517\" behoben.</li>
</ul>','<div align=\"right\">01.09.2012</div>
<b>Changes and new functions:</b><br/>
<ul>
<li>External MYSQL databases can now be managed. This includes entries at the MYSQL host table. Databases can be managed manually and automated when adding a gameserver.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Fixed error that the language select does not appear.</li>
<li>Fixed error \"Unknown column customerid in where clause in gserver.php:517\".</li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error=$insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$create_mysql_external_servers=$sql->prepare("CREATE TABLE IF NOT EXISTS `mysql_external_servers` (
`id` INT(30) UNSIGNED NOT NULL auto_increment,
`active` ENUM('Y','N') DEFAULT 'Y',
`ip` VARCHAR(15) NOT NULL,
`port` INT(5) UNSIGNED DEFAULT '3306',
`user` VARCHAR(255) NOT NULL,
`password` BLOB,
`max_databases` INT(30) UNSIGNED DEFAULT '100',
`interface` VARCHAR(255) DEFAULT NULL,
`max_queries_per_hour` INT(255) UNSIGNED DEFAULT '0',
`max_updates_per_hour` INT(255) UNSIGNED DEFAULT '0',
`max_connections_per_hour` INT(255) UNSIGNED DEFAULT '0',
`max_userconnections_per_hour` INT(255) UNSIGNED DEFAULT '0',
`resellerid` INT(30) UNSIGNED DEFAULT '0',
PRIMARY KEY  (`id`)
)");
$create_mysql_external_servers->execute();
$response->add('Action: create_mysql_external_servers done: ');
$error=$create_mysql_external_servers->errorinfo();
$create_mysql_external_servers->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$create_mysql_external_dbs=$sql->prepare("CREATE TABLE IF NOT EXISTS `mysql_external_dbs` (
`id` INT(30) UNSIGNED NOT NULL auto_increment,
`active` ENUM('Y','N') DEFAULT 'Y',
`sid` INT(30) UNSIGNED NOT NULL,
`uid` INT(30) UNSIGNED NOT NULL,
`gsid` INT(30) UNSIGNED DEFAULT '0',
`dbname` VARCHAR(255) NOT NULL,
`password` BLOB,
`ips` TEXT,
`max_queries_per_hour` INT(255) UNSIGNED DEFAULT '0',
`max_updates_per_hour` INT(255) UNSIGNED DEFAULT '0',
`max_connections_per_hour` INT(255) UNSIGNED DEFAULT '0',
`max_userconnections_per_hour` INT(255) UNSIGNED DEFAULT '0',
`resellerid` INT(30) UNSIGNED DEFAULT '0',
PRIMARY KEY  (`id`)
)");
$create_mysql_external_dbs->execute();
$response->add('Action: create_mysql_external_dbs done: ');
$error=$create_mysql_external_dbs->errorinfo();
$create_mysql_external_dbs->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

} else {
	echo "Error: this file needs to be included by the updater!<br />";
}
?>