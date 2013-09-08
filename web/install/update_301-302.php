<?php
/**
 * File: update_301-302.php.
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
('3.02','<div align=\"right\">01.07.2012</div>
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>MYSQL Datenbanken k&ouml;nnen &uuml;ber die API verwaltet werden.</li>
<li>Easy-Wi SQL Dump kann per Mausklick erstellt werden.</li>
<li>Externe Authentifizierung ist nun mg&ouml;glich, damit Easy-Wi in der Cloud betrieben werden kann.</li>
<li>Einmal am Tag wird nun f&uuml;r alle Tabellen REPAIR und OPTIMIZE gemacht.</li>
<li>12 neue Berechtigungen hinzugef&uuml;gt, um den Userzugriff genauer gestaltet zu k&ouml;nnen.</li>
<li>Passw&ouml;rter werden nun mit einem pers&ouml;nlichen Salt und Iteration gespeichert.</li>
<li>Spielicon wird nun in der Useransicht f&uuml;r Gameserver angezeigt.</li>
<li>TS3 Statistikzusammenfassung aus der Teamp Table wird nur noch einmal am Tag gemacht.</li>
<li>RSS und Twitter Feeds k&ouml;nnen in der Login&uuml;bersicht angezeigt werden.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Fehler behoben, dass das Gameserver FTP Passwort nicht in der Gameserver&uuml;bersicht beim User angezeigt wird.</li>
<li>Gameserver Ausleihmodul funktioniert wieder.</li>
<li>Wenn IP, oder Port bei einem Gameserver ver&auml;ndert werden werden nun alle Eintr&auml;ge im Restartkalender angepasst.</li>
<li>Fehler behoben, dass Backups und Restarts, die im Restartcalender eingetragen sind, unter Umst&auml;nden nicht ausgef&uuml;hrt werden.</li>
<li>User wird mit dem Gameserver vom Root gel&ouml;scht.</li>
<li>Problem behoben, dass manchmal im protected und unprotected Mode gleichzeitig Prozesse liefen.</li>
<li>Problem behoben, dass das protected Mode Password beim automatischen Restart &uuml;berschrieben wird.</li>
<li>Beim Editieren des initialen Users werden die Rechte nicht mehr entzogen.</li>
</ul>','<div align=\"right\">07.01.2012</div>
<b>Changes and new functions:</b><br/>
<ul>
<li>MYSQL databases can be managed via the API.</li>
<li>Easy-Wi SQL dump can be gerenerated via mousclick.</li>
<li>External authentication added for the usage in cloud computing.</li>
<li>Once a day REPAIR and OPTIMIZE will be run for all tables.</li>
<li>Added 12 more user permissions to allow a more detailed user access configuration.</li>
<li>Passwords are now being stored with a personal salt and iteration.</li>
<li>Added gameicon at users gameserveroverview.</li>
<li>TS3 stats from the temp table are streamlined only once per day.</li>
<li>RSS and Twitter feeds can be shown at the login overview.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Fixed gameserver FTP password not showing for the user at the overview.</li>
<li>Gameserver lendmodule is working again.</li>
<li>If IP or port for a gameserver is changed all restart calender entries are adjusted now. </li>
<li>Fixed error that backups and restarts from the restartcalender are not executed.</li>
<li>User is removed with the gameserver from the rootserver.</li>
<li>Fixed issue that sometimes a process is running in protected and unprotected mode.</li>
<li>Protected password is not longer overwritten at the automated restart.</li>
<li>Permissions won´t be removed if the initial user is edited.</li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error=$insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$query="CREATE TABLE IF NOT EXISTS `feeds_url` (
  `feedID` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `active` enum('Y','N') DEFAULT 'Y',
  `twitter` enum('Y','N') DEFAULT 'N',
  `feedUrl` varchar(255),
  `loginName` varchar(255),
  `modified` datetime,
  `resellerID` bigint(19) unsigned DEFAULT '0',
  PRIMARY KEY (`feedID`)
);";
$add=$sql->prepare($query);
$add->execute();

$pselect=$sql->prepare("SELECT `resellerid` FROM `resellerdata`");
$pselect->execute(array());
foreach ($pselect->fetchall() as $row) {
	$instert_feeds_url=$sql->prepare("INSERT INTO `feeds_url` (`active`, `twitter`, `feedUrl`, `resellerID`, `loginName`) VALUES ('Y', 'Y', 'https://twitter.com/EasyWI', ?, 'EasyWI');");
	$instert_feeds_url->execute(array($row['resellerid']));
	$response->add('Action: instert_feeds_url done: ');
	$error=$instert_feeds_url->errorinfo();
	$instert_feeds_url->closecursor();
	if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
	else $response->add('OK<br />');
}

} else {
	echo "Error: this file needs to be included by the updater!<br />";
}
?>