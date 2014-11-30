<?php
/**
 * File: update_309-310.php.
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

if (isset($include) and $include == true) {
$insert_easywi_version = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('3.10','<div align=\"right\">03.02.2013</div>
<b>Änderungen:</b><br/>
<ul>
<li>Generell:
<ul>
<li>Geändert: E-Mail Einstellungen sind nun von den Panel Einstellungen separiert.</li>
<li>Geändert: Mail und User Logs werden nach 31 Tagen gelöscht.</li>
<li>Geändert: Der E-Mail Text wird nicht mehr gelogged.</li>
<li>Hinzugefügt: Lizenzdetails werden unter Versionskontrolle angezeigt.</li>
<li>Entfernt: Limitierung der Installation auf das Root Verzeichnis.</li>
<li>Geändert: Bilder aus dem PHP Code Nun werden alle in *.tpls definiert.</li>
<li>Hinzugefügt: Fallback Logik für die Sprache bei E-Mails.</li>
<li>Hinzugefügt: Wenn der externe Auth erfolgreich war wird das Password lokal übernommen.</li>
</ul></li>
<li>API:
<ul>
<li>Hinzugefügt: TSDNS kann für TS3 Server verwaltet werden.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Hinzugefügt: Unterstützung von Rsync beim Sync mit dem Imageserver.</li>
<li>Hinzugefügt: Update Art kann für Gameroots and Game Images definiert werden.</li>
<li>Hinzugefügt: Template Auswahl beim Reinstall/Resync von Servern.</li>
<li>Geändert: Alle verfügbaren IPs werden beim Anlegen gelistet an Stelle nur der Primären.</li>
<li>Hinzugefügt: Anzeige, dass ein Update läuft.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Geändert: In der Slot Statistik wird IP:PORT an Stelle von TSDNS angezeigt.</li>
<li>Hinzugefügt: Flex Slots werden nun erst angepasst, wenn eine Abweichung von einstellbarer % Zahl besteht.</li>
<li>Geändert: TSDNS wird nun minütlich statt stündlich überwacht.</li>
<li>Geändert: TS3 TSDNS standard DNS wird nun als ID.definierteURL.tld generiert.</li>
<li>Geändert: Alle verfügbaren IPs werden beim Anlegen gelistet an Stelle nur der Primären.</li>
</ul></li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>Voiceserver:
<ul>
<li>DB ID wird an Stelle der virtual ID benutzt, Daten aus der Datenbank im Userbereich abzufragen.</li>
<li>TSDNS wird nach Crash restartet.</li>
</ul></li>
</ul>','<div align=\"right\">02.03.2013</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Changed: E-Mail settings are seperated from panel settings.</li>
<li>Changed: Mail and user logs will be kept only for 31 days.</li>
<li>Changed: Mail text is not logged anymore.</li>
<li>Added: Licence details will be shown at version control.</li>
<li>Removed: Limitation of the installation to the domain´s root folder.</li>
<li>Changed: Images within PHP code removed and added to .tpl files.</li>
<li>Added: Fallback logic regarding languages at e-mails.</li>
<li>Added: In case external Auth is successfull, the password will be stored locally.</li>
</ul></li>
<li>API:
<ul>
<li>Added: TSDNS address  for a TS3 server can be maintained.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Added: Rsync support for sync with imageserver.</li>
<li>Added: Update Method can be defined for Gameroots and Game images.</li>
<li>Added: Template selection for reinstall/resync.</li>
<li>Changed: All available IPs will be listed at adding step 1 instead of primary only.</li>
<li>Added: A running update will be displayed.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Changed: IP:PORT will be displayed instead of TSDNS at the slot usage statistic.</li>
<li>Added: Flex Slots will only be changed, if a difference of X % percent is reached. X can be defined.</li>
<li>Changed: TS3 TSDNS will be checked minutly instead of hourly.</li>
<li>Changed: TS3 TSDNS default DNS will be generated as ID.definedURL.tld.</li>
<li>Changed: All available IPs will be listed at adding step 1 instead of primary only.</li>
</ul></li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>Voiceserver:
<ul>
<li>DB ID is used instead of virtual ID to query the database at the userpanel.</li>
<li>TSDNS will be restarted after crash.</li>
</ul></li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$insert_easywi_version->closecursor();

$query = $sql->prepare("ALTER TABLE `voice_server_stats_hours` DROP `id`");
$query->execute();
$query->closecursor();

$query = $sql->prepare("ALTER TABLE `voice_server_stats_hours` DROP KEY `sid`");
$query->execute();
$query->closecursor();

$query = $sql->prepare("ALTER TABLE `voice_server_stats_hours` DROP KEY `date`");
$query->execute();
$query->closecursor();

$query = $sql->prepare("ALTER TABLE `voice_server_stats_hours` ADD PRIMARY KEY(`sid`,`date`)");
$query->execute();
$query->closecursor();

$query = $sql->prepare("ALTER TABLE `voice_server_stats` DROP `id`");
$query->execute();
$query->closecursor();

$query = $sql->prepare("ALTER TABLE `voice_server_stats` DROP KEY `sid`");
$query->execute();
$query->closecursor();

$query = $sql->prepare("ALTER TABLE `voice_server_stats` DROP KEY `date`");
$query->execute();
$query->closecursor();

$query = $sql->prepare("ALTER TABLE `voice_server_stats` ADD PRIMARY KEY(`sid`,`date`)");
$query->execute();
$query->closecursor();

$query="DROP TABLE IF EXISTS `lendstats`;
CREATE TABLE IF NOT EXISTS `lendstats` (
  `lendDate` datetime NOT NULL,
  `serverID` int(10) unsigned NOT NULL,
  `serverType` enum('v','g') NOT NULL,
  `lendtime` smallint(3) unsigned NOT NULL,
  `slots` smallint(3) unsigned NOT NULL,
  `resellerID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lendDate`,`serverID`,`serverType`),KEY(`resellerID`)
) ENGINE=InnoDB;";
$add = $sql->prepare($query);
$add->execute();

$query="CREATE TABLE IF NOT EXISTS `translations` (
  `type` varchar(2) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `transID` varchar(255) NOT NULL,
  `resellerID` int(10) unsigned NOT NULL DEFAULT '0',
  `text` text,
  PRIMARY KEY (`type`,`lang`,`transID`,`resellerID`)
) ENGINE=InnoDB";
$add = $sql->prepare($query);
$add->execute();

$query = $sql->prepare("SELECT * FROM `addons_desc`");
$query->execute();
$insert = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('ad',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=`text`");
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
	$insert->execute(array($row['language'], $row['addonid'], $row['description'], $row['resellerid']));
}
$query = $sql->prepare("DROP TABLE `addons_desc`");
$query->execute();
$query = $sql->prepare("SELECT * FROM `ticket_language`");
$query->execute();
$insert = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('ti',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=`text`");
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
	$insert->execute(array($row['language'], $row['topicid'], $row['subject'], $row['resellerid']));
}
$query = $sql->prepare("DROP TABLE `ticket_language`");
$query->execute();
$query = $sql->prepare("SELECT * FROM `email_languages`");
$query->execute();
$insert = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=`text`");
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
	$insert->execute(array($row['language'], $row['content'],@gzuncompress($row['xml']), $row['resellerid']));
}
$query = $sql->prepare("DROP TABLE `email_languages`");
$query->execute();

} else {
	echo "Error: this file needs to be included by the updater!<br />";
}