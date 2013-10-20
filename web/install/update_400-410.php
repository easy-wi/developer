<?php

/**
 * File: update_400-410.php.
 * Author: Ulrich Block
 * Date: 03.10.13
 * Time: 12:25
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
('4.10','<div align=\"right\">20.10.2013</div>
<b>Änderungen:</b><br/>
<ul>
<li>Generell:
<ul>
<li>News Feeds sind standardmäßig aktiviert</li>
<li>CMS ist standardmäßig aktiviert</li>
<li>Cronjobs können von einem externen Server aufgerufen werden, wenn er whitelisted ist</li>
<li>Link in der Versionsübersicht an den neuen Ort des Easy-WI Forums angepasst</li>
<li>Passwortfelder werden mit Sternen dargestellt</li>
<li>Die News aus dem CMS werden im Dashboard angezeigt</li>
<li>Zahlreiche legacy Funktionen entfernt und optimiert</li>
<li>Keyfiles können nun mit und ohne die Endung pub definiert werden</li>
<li>Entfernen Buttons mit Kreuz als Icon und roter Darstellung</li>
<li>Modul Management hinzugefügt</li>
</ul></li>
<li>API:
<ul>
<li>Passwort Hashes werden ebenfalls importiert</li>
</ul></li>
<li>Benutzer:
<ul>
<li>Sprachvariablen für Erstell- und Änderungsdatum hinzugefügt</li>
<li>Passwörter werden mit PHP 55 eingeführten Hash API bzw einem Fallback gehasht</li>
<li>Migration von alten Hashes und aus anderen Systemen importierten Hashes</li>
<li>Loginname wird bei Willkommen: () angezeigt, wenn kein Familien-, oder Vorname gepflegt wurde</li>
<li>Rückgaben, die verraten, ob ein Account existiert, aus dem Passwort Reset entfernt</li>
</ul></li>
<li>CMS:
<ul>
<li>Im Falle der Sprachwahl im CMS wird die entsprechende Seite an Stelle der Home Seite angezeigt</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Root Beschreibung wird beim Anlegen eines Gameserver angezeigt und als Fallback die IP</li>
<li>Beim Ändern und Anlegen des Masterservers werden die Verbindungsdaten überprüft und nur zugelassen, wenn erfolgreich</li>
<li>Multiselect für unterstütze Spiele an Stelle von einem Spiel bzw ganzem Engine Typ</li>
<li>Minecraft Craft Bukkit Startbefehl erweitert</li>
<li>l4d, l4d2 und tf2 Images werden mit steamCmd Installer definiert</li>
<li>l4d2 und tf2 zum Woraround für appIDs bezüglich API und Updater hinzugefügt</li>
<li>Multi Theft San Andreas Image und Query hinzugefügt</li>
<li>GTA San Andreas Multiplayer Image und Query hinzugefügt</li>
<li>Teeworlds Image und Query hinzugefügt</li>
<li>Autoupdater für Minecraft hinzugefügt</li>
<li>Autoupdater für Minecraft Craft Bukkit hinzugefügt</li>
<li>Im Falle eines Totalverlustes des Roots können alle Gameserver mit einem Klick neu erstellt werden</li>
</ul></li>
<li>MYSQL:
<ul>
<li>Beschreibung kann bei MYSQL Datenbanken mit angegeben werden</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Beim Ändern und Anlegen des Masterservers werden die Verbindungsdaten überprüft und nur zugelassen, wenn erfolgreich</li>
<li>Masterserver kann einem Reseller hinzugefügt werden, ohne dass er Verbindungsdaten usw einsehen kann</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>Farben in der Admin Gameserverübersicht</li>
<li>Game Leihserver werden nun korrekt beendet</li>
<li>Kein Timeout mehr, wenn der TS3 Server den webserver während einer Operation bannt</li>
<li>Erstell- und Änderungsdatum wird beim Anlegen des initialen Admin Accounts gesetzt</li>
<li>Alte Icon Referenz beim des Userswitches aus der Logübersicht entfernt</li>
<li>Rückgabe nach Useraktion im Ticket Bereich des Users korrigiert</li>
<li>Validator beim Editieren der eigenen Userdaten korrigiert</li>
<li>Veraltete Icons in der Protectionabfrage des CMS ersetzt</li>
<li>Leihserverübersicht im CMS wird korrekt dargestellt</li>
<li>MYSQL Modul verwaltet die Datenbankuser korrekt</li>
<li>Erfolgreiches Anlegen eines Vertreters gibt nun den Erfolg an den User zurück</li>
<li>Fehlendes Template admin_voicemasterserver_dltpl hinzugefügt</li>
<li>Reinstall von Gameservern im Adminpanel korrigiert</li>
<li>Startbefehl kann nun das Zeichen # enthalten, welches zB für das Forking bei l4d benötigt wird</li>
<li>Image ALT der Anzeige vom Protection Mode korrigiert</li>
<li>Error Handling der TS3 Klasse für den Fall erweitert, dass keine Serverinfo abgefragt werden konnte</li>
<li>Hausnummer kann Querstriche enthalten</li>
<li>PHP Notice beim Gameserver Reinstall korrigiert</li>
<li>Userpanel Gamerserverübersicht bezüglich FTP Passwörtern korrigiert</li>
<li>FastDL Abgleich startet wieder</li>
<li>Gebrauch von " . '$_SERVER' . " in der login.php entfernt</li>
<li>Installer definiert das default template</li>
</ul>
','<div align=\"right\">10.20.2013</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>News feeds are active per default</li>
<li>CMS is activated per default</li>
<li>Cronjobs can be executed from remote host in case the IP is whitelisted</li>
<li>Link at versionckeck are changed to the new Easy-WI forums location</li>
<li>Passwordforms are masked with stars</li>
<li>News created at the CMS module are displayed at the dashboard as well</li>
<li>Multiple legacy functions removed or optimized</li>
<li>Keyfiles can be entered with and without pub</li>
<li>Removal buttons are displayed with fitting icon and in red</li>
<li>Modul Management added</li>
</ul></li>
<li>API:
<ul>
<li>Passwort hashes are imported as well</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Root description will be displayed while adding a gameserver Fallback is the IP</li>
<li>While adding or editing a masterserver the connection data will be verified and a connection check done</li>
<li>Addons now have a multiselect regarding supported games in case of selecting a game or a whole engine type</li>
<li>Minecraft Craft Bukkit startcommand extended</li>
<li>l4d, l4d2 and tf2 images are defined with steamCmd installer</li>
<li>l4d2 and tf2 are added to the workaround function regarding appIDs for API and updater</li>
<li>Added Multi Theft San Andreas image and query</li>
<li>Added GTA San Andreas Multiplayer image and query</li>
<li>Added Teeworlds image and query</li>
<li>Added Minecraft autoupdater</li>
<li>Added Minecraft Craft Bukkit autoupdater</li>
<li>In case a whole rootserver is lost and replaced all users and server can be recreated with one click</li>
</ul></li>
<li>MYSQL:
<ul>
<li>Description can be added with a MYSQL database</li>
</ul></li>
<li>User:
<ul>
<li>Language variables added regarding users creation and modifcation date</li>
<li>Passwords are hashed with PHP 55 Hash API or fallback if PHP is older</li>
<li>Migration of old hashes once a user logs in</li>
<li>Loginname will be shown at Welcome: () in case no first and last name is maintained</li>
<li>Any hints regarding account existence removed from password reset</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>While adding or editing a masterserver the connection data will be verified and a connection check done</li>
<li>Masterserver can be added to a reseller account which then will be able to use it but cannot see configurations like passwords</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>Colours correct at admins gameserver overview</li>
<li>Game lendserver are shut down correctly</li>
<li>No PHP time out in case the TS3 server bans the webserver during command execution</li>
<li>Creation- and modification date correctly setup for initial admin account</li>
<li>Outdated icon regarding userswitch removed from logoverview</li>
<li>Return after useraction at the ticket module corrected</li>
<li>Correct validator used in case of editing own userdata</li>
<li>Outdated icons replaced at protectioncheck within the CMS</li>
<li>Lendserver overview is displayed correctly at CMS</li>
<li>MYSQL module maintains DB users without errors</li>
<li>Success message in case of adding a substitute</li>
<li>Added missing template admin_voicemasterserver_dltpl</li>
<li>Corrected reinstall gameservern at adminpanel</li>
<li>Startcommand can now contain the character #</li>
<li>Corrected image ALT at protection mode display</li>
<li>Enhanced error handling at TS3 class in case no serverinfo has been retrieved</li>
<li>Streetnumber can contain a horizontal line</li>
<li>Corrected PHP Notice in case of gameserver reinstall</li>
<li>Corrected userpanel gamerserver overview regarding FTP passwords</li>
<li>FastDL matching working again</li>
<li>Usage of " . '$_SERVER' . " removed from login.php</li>
<li>Installer definines the default template</li>
</ul>
')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

    $query="CREATE TABLE IF NOT EXISTS `addons_allowed` (
  `addon_id` int(10) unsigned NOT NULL,
  `servertype_id` int(10) unsigned NOT NULL,
  `reseller_id` int(10) unsigned NULL DEFAULT 0,
  PRIMARY KEY (`addon_id`,`servertype_id`),KEY(`reseller_id`)
) ENGINE=InnoDB";
    $add = $sql->prepare($query);
    $add->execute();

    $query = $sql->prepare("SELECT s.`id` AS `servertype_id`,s.`resellerid`,a.`id` AS `addon_id` FROM `servertypes` AS s LEFT JOIN `addons` AS a ON s.`shorten`=a.`shorten` OR s.`qstat`=a.`shorten` WHERE a.`id` IS NOT NULL");
    $query2 = $sql->prepare("INSERT INTO `addons_allowed` (`addon_id`,`servertype_id`,`reseller_id`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `addon_id`=`addon_id`");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $query2->execute(array($row['addon_id'], $row['servertype_id'], $row['resellerid']));
    }

    $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='samp' AND `resellerid`=0 LIMIT 1");
    $query->execute();
    if ($query->rowCount() == 0) {
        $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`qstat`,`gamemod`,`gamemod2`,`configs`,`configedit`,`qstatpassparam`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`resellerid`,`mapGroup`) VALUES ('N',NULL,1,'samp','San Andreas Multiplayer','gserver','samp03svr',NULL,NULL,NULL,0,NULL,'./%binary%',NULL,NULL,'gtasamp','N','','server.cfg','[server.cfg] cfg\r\nmaxplayers %slots%\r\nport %port%','',10,1,7777,NULL,NULL,NULL,NULL,0,NULL)");
        $query->execute();
    }

    $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='mtasa' AND `resellerid`=0 LIMIT 1");
    $query->execute();
    if ($query->rowCount() == 0) {
        $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`qstat`,`gamemod`,`gamemod2`,`configs`,`configedit`,`qstatpassparam`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`resellerid`,`mapGroup`) VALUES ('N',NULL,1,'mtasa','Multi Theft Auto San Andreas','gserver','mta-server',NULL,NULL,NULL,0,NULL,'./%binary%',NULL,NULL,'mtasa','N','','','[mods/deathmatch/mtaserver.conf] xml\r\n<serverip>%ip%</serverip>\r\n<serverport>%port%</serverport> \r\n<httpport>%port2%</httpport>\r\n<maxplayers>%slots%</maxplayers>\r\n<httpserver>0</httpserver>','',10,3,22003,22005,22126,NULL,NULL,0,NULL)");
        $query->execute();
    }

    $query = $sql->prepare("INSERT INTO `qstatshorten` (`qstat`,`description`) VALUES ('teeworlds', 'Teeworlds'),('mtasa', 'Multi Theft Auto San Andreas')");
    $query->execute();

    $query = $sql->prepare("UPDATE `qstatshorten` SET `description`='San Andreas Multiplayer' WHERE `qstat`='gtasamp'");
    $query->execute();

    if ($query->rowCount() == 0) {
        $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`qstat`,`gamemod`,`gamemod2`,`configs`,`configedit`,`qstatpassparam`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`resellerid`,`mapGroup`) VALUES ('N',NULL,1,'teeworlds','Teeworlds','gserver','teeworlds_srv',NULL,NULL,NULL,0,NULL,'./%binary%','[Capture the Flag = default]\r\n-f config_ctf.cfg\r\n\r\n[Deathmatch]\r\n-f config_dm.cfg\r\n\r\n[Team Deathmatch]\r\n-f config_tdm.cfg',NULL,'teeworlds','N','','config_ctf.cfg\r\nconfig_dm.cfg\r\nconfig_tdm.cfg', '[autoexec.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_ctf.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_dm.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_tdm.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%','',10,1,8303,NULL,NULL,NULL,NULL,0,NULL)");
        $query->execute();
    }

    $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='teeworlds' AND `resellerid`=0 LIMIT 1");
    $query->execute();

    $query = $sql->prepare("UPDATE `servertypes` SET `cmd`='java -Xincgc -Xmx%maxram%M -Xms%minram%M -jar %binary% -o true -h %ip% -p %port% -s %slots% --log-append false --log-limit 50000' WHERE `shorten`='bukkit'");
    $query->execute();



} else {
    echo "Error: this file needs to be included by the updater!<br />";
}