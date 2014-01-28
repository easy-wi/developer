<?php

/**
 * File: update_420-430.php.
 * Author: Ulrich Block
 * Date: 28.01.14
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
    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('4.30','<div align=\"right\">23.10.2013</div>
<b>Änderungen:</b><br/>
<ul>
<li>Generell:
<ul>
<li>Rootserver Menü in Netzwerk und eigentliche Server getrennt</li>
<li>Zusätzliche Login Methode Key + Passwort</li>
<li>SSH2 Pecl mit phpseclib ersetzt</li>
<li>Rote Buttons mit Mülleimer zum löschen</li>
<li>Zeitzone wird gesetzt, wenn Admin die php.ini nicht konfiguriert</li>
<li>dbConnect[debug] an Stelle von debug</li>
<li>REPAIR und OPTIMIZE nur einmal pro Tag</li>
<li>FTP Klasse statt redundanten Code</li>
<li>Klasse PHP Mailer für besseren Mail Support</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Asyncronen Web FTP hinzugefügt</li>
<li>Datei Templates für Gameserver</li>
<li>Catch Exceptions für die GameQ Klasse bei der image.php</li>
<li>GS Masterupdate aus der reboot.php nach startupdates.php verlagert</li>
<li>functions_gs.php angelegt</li>
<li>GameQ aktualisiert</li>
<li>Select all zur DB reparieren Funktion bei GS + Addons hinzugefügt</li>
<li>Einzigartigkeit von Spielen bei Masterupdate gesichert</li>
<li>Farbtags werden aus dem Servernamen entfernt</li>
<li>Rückgabe der reboot.php/startupdates.php verbessert</li>
<li>Verbessertes Error Handling bei screenlogs</li>
<li>ESL CS GO Addon hinzugefügt</li>
<li>Tekkit classic hinzugefügt</li>
<li>Mehr debugging Debugging Informationen bei der statuscheck.php</li>
<li>binport beim teeworlds Template</li>
<li>CSS BHOP Mappackage hinzugefügt</li>
<li>JSON Support bei Config Support hinzugefügt</li>
<li>Beim Resync/Reinstall wird Template 1 vorausgewählt</li>
<li>Starbound Template hinzugefügt</li>
<li>Maximale Länge für die gamebinary erhöht</li>
<li>/ am Anfang des Import Pfades hinzugefügt</li>
<li>Gametemplate wird bei Resellern hinzugefügt, wenn es ein Admin bei sich anlegt</li>
</ul></li>
<li>Installer:
<ul>
<li>Cronjob Timestamps werden in die Zukunft gesetzt</li>
<li>Beide möglichen Cronjob Einstellungen, per wget und PHP-CLI werden angezeigt</li>
<li>Versionsüberprüfung wird beim Installer ausgeführt</li>
</ul></li>
<li>Rootserver:
<ul>
<li>Wenn ein Image installiert wird, im Anschluss nur PXE Eintrag und nicht DHCP entfernen</li>
</ul></li>
<li>Benutzer:
<ul>
<li>Reseller Fix Job hinzugefügt</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Funktionen aus der class_voice.php entfernt und in functions_voice.php ausgelagert</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>API defekt durch inkorrekte Validatoren in vorlage.php</li>
<li>GS Roots Reinstall benutzt falsche ID</li>
<li>Protectioncheck Formularlink wenn Easy-WI im Unterordner liegt</li>
<li>Gameserver API <installGames> wird ignoriert</li>
<li>Registrations Aktivierungslink falsch, wenn SEO Links aus sind</li>
<li>Falsche Rückgabe, wenn Ticket editiert wurde</li>
<li>Benutzer kann nicht auf Tickets im Status In Process antworten</li>
<li>Spamfilter auf NEIN, wird ignoriert</li>
<li>Installer nutzt nicht existierende Sprache als Fallback</li>
<li>Cronjob Timestamps werden für Reseller nicht aktualisiert</li>
<li>Kein Error Handling wenn die keyfile fehlt</li>
<li>Multiple falsche Texte im Installer verwendet</li>
<li>install.php: Undefined index: USER</li>
<li>+1 Slot beim GS Check hinzufügen, wenn SourceTV aktiv ist</li>
<li>Fehler im Leihserver Modul</li>
<li>Veraltete SQLs werden im TS3 Importer benutzt</li>
<li>Initiales Passwort wird während des Imports nicht gesetzt</li>
<li>implode(): Invalid arguments userpanel_gserver.php</li>
<li>Undefined index: active at statuscheck.php</li>
<li>Include für queries.php</li>
<li>benutzernamen beim TS3 Importer</li>
<li>Korrekete Abhängigkeiten, wenn das Default Addon angelegt/korrigiert wird</li>
</ul>
','<div align=\"right\">10.23.2013</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Split rootserver menu in two</li>
<li>Allow additional login method key + password</li>
<li>Replace ssh2 pecl module with phpseclib</li>
<li>Change to red button and trash icon for delete</li>
<li>Define timezone</li>
<li>use dbConnect[debug] instead of debug</li>
<li>Run REPAIR and OPTIMIZE only once a day</li>
<li>Create FTP class</li>
<li>Use PHP Mailer for better Mail support</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Add Web FTP</li>
<li>File templates for gameserver</li>
<li>Catch Exceptions from GameQ at image.php</li>
<li>Move GS masterupdate reboot.php -> startupdates.php</li>
<li>Create functions_gs.php</li>
<li>Upgrade GameQ to latest version</li>
<li>Add select all to DB repair GS + Addons</li>
<li>Ensure unique games at gs masterupdate</li>
<li>Remove color tags from servername</li>
<li>Enhance output reboot.php/startupdates.php</li>
<li>Enhance error reporting at screenlog</li>
<li>Add ESL CS GO addons</li>
<li>Add tekkit classic</li>
<li>More debugging output to statuscheck.php</li>
<li>use binport at teeworlds template</li>
<li>Add CSS BHOP mappackage</li>
<li>Add JSON support to config protect</li>
<li>On Resync/Reinstall preselect template 1</li>
<li>Add starbound template</li>
<li>Increase length of gamebinary</li>
<li>/ in front of import path</li>
<li>Add a gametemplate for reseller if admin does</li>
</ul></li>
<li>Installer:
<ul>
<li>Predate cronjob timestamps during install</li>
<li>Display both wget options at install</li>
<li>Add version check at install</li>
</ul></li>
<li>Rootserver:
<ul>
<li>After image install only PXE remove, no DHCP remove</li>
</ul></li>
<li>User:
<ul>
<li>Add reseller fix job</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>split content of class_voice.php </li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>API not working due to incorrect validators at vorlage.php</li>
<li>GS roots reinstall all uses wrong ID</li>
<li>Protectioncheck form link and subfolders</li>
<li>Gameserver API <installGames> getting ignored</li>
<li>Registration activation link incorrect if SEO is OFF</li>
<li>Incorrect display if ticket has been edited</li>
<li>User cannot reply to tickets in state In Process</li>
<li>Spamfilter set to NO not respected</li>
<li>Installer fallbacks to non existing language</li>
<li>Cronjob timestamps for resellers are not updated</li>
<li>No error handling in case keyfile is missing</li>
<li>Multiple wrong text displays at installer</li>
<li>install.php: Undefined index: USER</li>
<li>Add +1 Slot at check when sourcetv is active</li>
<li>Error at lending module</li>
<li>Old SQL on ts3 import regarding usergroup</li>
<li>Initial password not set during import</li>
<li>implode(): Invalid arguments userpanel_gserver.php</li>
<li>Undefined index: active at statuscheck.php</li>
<li>Remove include for queries.php</li>
<li>Usernames and TS3 import</li>
<li>Correct depencies while correct/add default addons</li>
</ul>
')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}