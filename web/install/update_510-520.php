<?php

/**
 * File: update_510-520.php.
 * Author: Ulrich Block
 * Date: 16.12.15
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

    include(EASYWIDIR . '/stuff/keyphrasefile.php');

    // Execute correct, so we can update game templates
    $tables->correctExistingTables();

    // Steam Server Token
    $query = $sql->prepare("UPDATE `servertypes` SET `steamGameserverToken`='Y' WHERE `shorten` IN ('nmrih','csgo','zps','tf','hl2mp','ageofchivalry','pvkii','left4dead2','left4dead','dods','css')");
    $query->execute();

    // UT4 game binary copy
    $query = $sql->prepare("UPDATE `servertypes` SET `copyStartBinary`='Y' WHERE `shorten` IN ('ark')");
    $query->execute();

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('5.20','<div align=\"right\">28.12.2015</div>
<b>Änderungen:</b><br/>
<ul>
<li>CMS:
<ul>
<li>Standard sortierung der news geändert und sortieren nach Datum</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Universelle Gameserver Konsole hinzugefügt</li>
<li>Unreal Tournament 3/4 Support hinzugefügt</li>
<li>Versionsnummer bezüglich Ark Survival Evolved Version wird von arkdedicated.com an Stelle der Steam API bezogen</li>
<li>Verbesserter Ark Survival Evolved Restart Prozess</li>
<li>Steam Server Token support hinzugefügt</li>
<li>Standard Imageserver ist nicht mehr hard coded.</li>
<li>Standard Start Updates Minute ist nun 10</li>
<li>Optionale Parameter all_root und force_update zu startupdates.php hinzugefügt </li>
<li>Auf aktuellste GameQ v3 Version aktualisiert</li>
<li>Zusätzliche Spiele zu GameQ v3 hinzugefügt</li>
<li>Fallback auf GameQ v2 erstellt</li>
<li>Logging in der job.php hinzugefügt</li>
<li>Gameserver werden nach einem Master Update neu gestartet</li>
<li>Fallbacks für den Minecraft Download Pfad eingeführt</li>
<li>File Extension lang wird kopiert</li>
<li>Verbesserte Fehler Behandlung beim Image Import</li>
<li>Online Modus den MC Templates hinzugefügt</li>
<li>Templates Arma3, Rust, Spigot, Hexxit hinzugefügt</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Flexible Konfigurationen möglich</li>
</ul></li>
<li>Webspace:
<ul>
<li>Apache Vhost Template um Versionsprüfung erweitert</li>
</ul></li>
<li>Allgemeines:
<ul>
<li>Neues Tabellen Konfigurationsmanagement</li>
<li>BB Code unterstützung im News Fedd</li>
<li>Zusätzliche neue Light Skins für alle bereits verfügbaren Farben</li>
<li>Alle eingesetzten UI frameworks auf aktuellste Version aktualisiert</li>
<li>Minimale PHP Version ist nun 5.4</li>
<li>Anzeige des letztmaligen Ausführens der Cronjobs</li>
<li>CMS standardmäßig deaktiviert</li>
<li>Root Modul Code auskommentiert</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>CMS Frontend aktiv, obwohl im Backend deaktiviert</li>
<li>FastDL zeigt Benutzername an Stelle der Domain</li>
<li>Installer funktioniert nicht mit dem MySQL Paket von Oracle</li>
<li>Liste der zugelassenen Dateien in der .htaccess korrigiert</li>
<li>.htaccess an Apache 2.4 angepasst</li>
<li>Webmaster: Falscher Redirect zur Übersiccht im Falle eines Fehlers</li>
<li>% Zeichen nicht innerhalb einer span Gruppe bei mehreren Template Dateien</li>
<li>Falscher standard MB Wert beim Web Master</li>
<li>Falsche URL beim Installer</li>
<li>Csgo Template veraltet</li>
<li>DataTables funktionieren nicht, wenn Upper Case Character verwendet werden</li>
<li>Voice API Hinzufügen Methode funktioniert beim aktivierten Debugger nicht</li>
<li>Admin GS Reinstall: Gebrauch nicht existierender Variable</li>
<li>Falscher Gebrauch von ID bei Web Master Hinzufügen</li>
<li>Falscher Gebrauch von array im ajax App Details Template</li>
<li>Folsche rechtschreibung von access</li>
<li>False Positive bezüglicher veralterter Voice Server Versionen</li>
<li>Fehlende queryPassword Spalte bei Voice Servern</li>
<li>Gameserver Easy Config und Special Character, wie Newline</li>
<li>cloud.php kann nicht mit external Cronjob genutzt werden</li>
<li>Game Server file copy exclude Pattern nicht angewendet</li>
<li>On error, \"My Voiceserver\" is prepended on the voice master servers default name</li>
<li>Falsches open_basedir im Template</li>
<li>Falscher Default apache2 reload Befehl</li>
</ul>','<div align=\"right\">12.28.2015</div>
<b>Changes:</b><br/>
<ul>
<li>CMS:
<ul>
<li>Default order of news and date as sort option</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Added universal game server console</li>
<li>Added Unreal Tournament 3/4 support</li>
<li>Fetch Ark Survival Evolved Version from arkdedicated.com instead of steam API</li>
<li>Improved Ark Survival Evolved restart process</li>
<li>Added Steam Server Token support</li>
<li>Default imageserver is not hard coded anymore</li>
<li>Set default start updates at to minute 10</li>
<li>Added optional parameter at startupdates.php all_root and force_update</li>
<li>Upgrade to latest GameQ v3 version</li>
<li>Added additional games to GameQ v3</li>
<li>Added fallback to GameQ v2</li>
<li>Added logging at job.php</li>
<li>Restart all gameserver after master update in any case</li>
<li>Ensure MC download path is set</li>
<li>File extension lang is copied</li>
<li>Better error handling at image import</li>
<li>Added online mode to MC templates</li>
<li>Added templates Arma3, Rust, Spigot, Hexxit</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Flexible configurations possible</li>
</ul></li>
<li>Webspace:
<ul>
<li>Added version check in Apache Vhost</li>
</ul></li>
<li>General:
<ul>
<li>New table version and configuration management</li>
<li>BB code at feed news display added</li>
<li>Added new skins light skins for all existing colours</li>
<li>Upgraded UI frameworks to latest versions</li>
<li>Increased minimum PHP to 5.4</li>
<li>Display last cron run at system overview</li>
<li>Deactivate CMS module by default</li>
<li>Deactivate root module</li>
<li>Incorrect default open_basedir template</li>
<li>Incorrect default apache2 reload command</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>CMS frontend still active when deactivated in backend</li>
<li>FastDL is showing username instead of domain</li>
<li>Installer does not work with MySQL package from Oracle</li>
<li>Corrected list of allowed files at .htaccess</li>
<li>Align .htaccess with 2.4</li>
<li>Webmaster: Incorrect redirect to overview in case of error</li>
<li>% character not within span group in some template files</li>
<li>Incorrect default MB value at web master</li>
<li>Wrong URL at installer</li>
<li>Csgo template outdated</li>
<li>DataTables not working without because of upper case parameters</li>
<li>Voice API add method failing with debug on</li>
<li>Admin GS reinstall: Usage of not existing variable</li>
<li>Incorrect usage of ID at web master add</li>
<li>Incorrect array used at ajax app details template</li>
<li>Incorrect spelling of access</li>
<li>False positive with outdated voice server version</li>
<li>Missing queryPassword column at voice server</li>
<li>Gameserver easy config and special character like newline</li>
<li>cloud.php cannot be used via external cronjob</li>
<li>Game Server file copy exclude pattern not applied</li>
<li>Im Fehler Fall wird \"My Voiceserver\" erneut voran gestellt</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}