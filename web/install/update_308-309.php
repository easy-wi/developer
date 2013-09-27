<?php
/**
 * File: update_308-309.php.
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
('3.09','<div align=\"right\">01.01.2013</div>
<b>Änderungen:</b><br/>
<ul>
<li>Generell:
<ul>
<li>Geändert: Zahlreiche DELETE Statements geändert, so dass sie zusätzlich Karteileichen in der Datenbank erkennen und mit dem zu löschenden Eintrag entfernen.
</ul>
<li>API:
<ul>
<li>Hinzugefügt: Alle Attribute aus dem Backend können nun auch per API für einen User gesendet werden.
<li>Hinzugefügt: Flex Slots Optionen für Teamspeak 3
</ul>
<li>CMS:
<ul>
<li>Hinzugefügt: Option Seiten in der Navi auszublenden.
</ul>
<li>Jobs:
<ul>
<li>Hinzugefügt: Aktueller Ram Verbrauch wird angezeigt.
<li>Hinzugefügt: Objects werden nach jedem Durchlauf im Deamon mode neu erstellt.
</ul>
<li>News Feeds:
<ul>
<li>Hinzugefügt: Unterstützung für Facebook RSS Feeds inklusive CDATA Inhalte.
<li>Geändert: Steam News Feeds werden nur noch importiert, wenn das Spiel als Masterserver installiert wurde. Ablauf optimiert.
</ul>
<li>Voiceserver:
<ul>
<li>Hinzugefügt: Formularfeld für die \"externalID\" und Flex Slots für Teamspeak 3
<li>Hinzugefügt: VirtualID und Flex Slots Anzeige.
<li>Geändert: Beim Userpanel wird die virtualID an Stelle der easy-WI ID angezeigt
</ul>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>Gameserver:
<ul>
<li>statuscheck.php erfasst nicht von quakestat unterstütze Spiele wieder korrekt.</li>
<li>Installationsstatus von Mappaketen werden beim User korrekt dargestellt.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Das Passwort funktioniert nun, wenn ein neuer User beim Importieren von TS3 Servern angelegt wird.</li>
</ul></li>
</ul>','<div align=\"right\">01.01.2013</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Changed: Changed multiple DELETE statements not to only remove the wanted entry but also to remove those who do not have corrosponding entries in other rows.</li>
</ul></li>
<li>API:
<ul>
<li>Added: Attributes that can be altered at the backend can be send with the API regarding users.</li>
<li>Added: Flex Slots options regarding Teamspeak 3</li>
</ul></li>
<li>CMS:
<ul>
<li>Added: Option to not show pages in navi.</li>
</ul></li>
<li>Jobs:
<ul>
<li>Added: Output of current memory usage.</li>
<li>Added: Objects will be recreated after each run in deamon mode.</li>
</ul></li>
<li>News Feeds:
<ul>
<li>Added: Facebook RSS Feed Support including CDATA block.</li>
<li>Changed: Steam News Feeds will be only imported if Game is installed as a masterserver. Optimized Feed importing.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Added: Input fields for \"externalID\" and Flex Slots for Teamspeak 3</li>
<li>Added: VirtualID and Flex Slots display</li>
<li>Changed: At the userpanel the virtualID is displayed instead of easy-WI ID</li>
</ul></li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>Gameserver:
<ul>
<li>statuscheck.php can handel games without quakestat support correctly again.</li>
<li>Install state regarding mappackages is displayed correctly.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Password works when creating users while importing TS3 server.</li>
</ul></li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error=$insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();

$query=$sql->prepare("ALTER TABLE `voice_server` CHANGE `max_download_total_bandwidth` `max_download_total_bandwidth` BIGINT(19),
CHANGE `max_upload_total_bandwidth` `max_upload_total_bandwidth` BIGINT(19),
CHANGE `maxtraffic` `maxtraffic` BIGINT(19)");
$query->execute();
$response->add('Action: Change voice_server table done: ');
$error=$query->errorinfo();
$query->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$query=$sql->prepare("UPDATE `voice_server` SET `max_download_total_bandwidth`='' WHERE `max_download_total_bandwidth`='0';
UPDATE `voice_server` SET `max_upload_total_bandwidth`='' WHERE `max_upload_total_bandwidth`='0';
UPDATE `voice_server` SET `maxtraffic`='' WHERE `maxtraffic`='0'");
$query->execute();
$error=$query->errorinfo();
$query->closecursor();

} else {
	echo "Error: this file needs to be included by the updater!<br />";
}