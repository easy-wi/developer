<?php
/**
 * File: update_207-208.php.
 * Author: Ulrich Block
 * Date: 22.12.11
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
$insert_qstatshorten_gtasamp=$sql->prepare("INSERT INTO `qstatshorten` (`qstat`,`description`) VALUES ('gtasamp', 'GTA San Andreas')");
$insert_qstatshorten_gtasamp->execute();
$response->add('Action: insert_qstatshorten_gtasamp done: ');
$error=$insert_qstatshorten_gtasamp->errorinfo();
$insert_qstatshorten_gtasamp->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$alter_servertypes_configedit=$sql->prepare("ALTER TABLE `servertypes` ADD COLUMN `configedit` TEXT AFTER `configs`");
$alter_servertypes_configedit->execute();
$response->add('Action: alter_servertypes_configedit done: ');
$error=$alter_servertypes_configedit->errorinfo();
$alter_servertypes_configedit->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$alter_rserverdata_hyperthreading_cores=$sql->prepare("ALTER TABLE `rserverdata`
ADD COLUMN `hyperthreading` ENUM('Y','N') DEFAULT 'N' AFTER `active`,
ADD COLUMN `cores` INT(3) UNSIGNED DEFAULT '4' AFTER `hyperthreading`");
$alter_rserverdata_hyperthreading_cores->execute();
$response->add('Action: alter_rserverdata_hyperthreading_cores done: ');
$error=$alter_rserverdata_hyperthreading_cores->errorinfo();
$alter_rserverdata_hyperthreading_cores->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$alter_serverlist=$sql->prepare("ALTER TABLE `serverlist`
ADD COLUMN `taskset` ENUM('Y','N') DEFAULT 'N' AFTER `userconfig`,
ADD COLUMN `cores` VARCHAR(255) DEFAULT NULL AFTER `taskset`,
ADD COLUMN `port4` INT(5) UNSIGNED DEFAULT NULL AFTER `port3`,
ADD COLUMN `minram` INT(10) UNSIGNED DEFAULT NULL AFTER `port4`,
ADD COLUMN `maxram` INT(10) UNSIGNED DEFAULT NULL AFTER `minram`,
ADD COLUMN `upload` INT(1) UNSIGNED DEFAULT '0' AFTER `cores`,
ADD COLUMN `uploaddir` BLOB AFTER `upload`,
ADD COLUMN `user_uploaddir` ENUM('Y','N') DEFAULT 'N' AFTER `userconfig`");
$alter_serverlist->execute();
$response->add('Action: alter_serverlist done: ');
$error=$alter_serverlist->errorinfo();
$alter_serverlist->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$alter_gserver_restarts_worldsafe=$sql->prepare("ALTER TABLE `gserver_restarts`
ADD COLUMN `upload` ENUM('Y','N') DEFAULT 'N' AFTER `backup`,
ADD COLUMN `worldsafe` ENUM('Y','N') DEFAULT 'N' AFTER `upload`");
$alter_gserver_restarts_worldsafe->execute();
$response->add('Action: alter_gserver_restarts_worldsafe done: ');
$error=$alter_gserver_restarts_worldsafe->errorinfo();
$alter_gserver_restarts_worldsafe->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$alter_easywi_version=$sql->prepare("ALTER TABLE `easywi_version`
ADD COLUMN `de` TEXT,
ADD COLUMN `en` TEXT");
$alter_easywi_version->execute();
$response->add('Action: alter_easywi_version done: ');
$error=$alter_easywi_version->errorinfo();
$alter_easywi_version->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$alter_settings=$sql->prepare("ALTER TABLE `settings`
ADD COLUMN `version` DECIMAL(4,2) DEFAULT '2.08' AFTER `id`,
ADD COLUMN `template` VARCHAR(50) DEFAULT 'default' AFTER `language`");
$alter_settings->execute();
$response->add('Action: alter_settings done: ');
$error=$alter_settings->errorinfo();
$alter_settings->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$pselect=$sql->prepare("SELECT `resellerid` FROM `settings`");
$pselect->execute();
foreach ($pselect->fetchAll() as $row) {
	$inster_servertypes_mc=$sql->prepare("INSERT INTO `servertypes` (`steamgame`, `updates`, `shorten`, `description`, `type`, `gamebinary`, `binarydir`, `modfolder`, `fps`, `slots`, `map`, `cmd`, `tic`, `qstat`, `gamemod`, `gamemod2`, `configs`, `configedit`, `qstatpassparam`, `resellerid`) VALUES ('N', 3, 'mc', 'Minecraft', 'gserver', 'minecraft_server.jar', NULL, NULL, NULL, 0, NULL, 'java -Xmx%maxram%M -Xms%minram%M -XX:+UseConcMarkSweepGC -XX:+CMSIncrementalPacing -XX:ParallelGCThreads=%maxcores% -XX:+AggressiveOpts -jar %binary% nogui', NULL, 'minecraft', 'N', 'css', NULL, '[server.properties] ini\r\nserver-port=%port%\r\nquery.port=%port%\r\nrcon.port=%port2%\r\nserver-ip=%ip%\r\nmax-players=%slots%', NULL, ?)");
	$inster_servertypes_mc->execute(array($row['resellerid']));
	$response->add('Action: inster_servertypes_mc (Resellerid '.$row['resellerid'].') done: ');
	$error=$inster_servertypes_mc->errorinfo();
	$inster_servertypes_mc->closecursor();
	if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
	else $response->add('OK<br />');
}

$insert_easywi_version=$sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('2.00','<div align=\"right\">22.12.2011</div>
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>Lizenzmodell bei Servern von Modulen zu einer Lizenz f&uuml;r alle Serverarten ge&auml;ndert. Alte Lizenzen werden zum neuen Typ umgewandelt, so dass man mit einer 20er Gameserverlizenz nun z.B. 12 Game und 8 Voiceserver verwalten kann.</li>
<li>Bei dem Graphen der Traffikstatistik k&ouml;nnen nun die Farben &uuml;ber das Interface eingestellt werden.</li>
<li>Server, die deaktiert sind, oder deaktivierten Usern geh&ouml;ren, werden nicht mehr bei der Berechnung der Lizenzaussch&ouml;pfung ber&uuml;cksichtigt.</li>
<li>Vserver k&ouml;nnen nun deaktiviert werden.</li>
<li>Rechteverwaltung um die neuen Module erweiter.</li>
<li>Das Verleihmodul ist nun auch bei der Privatlizenz verf&uuml;gbar.</li>
<li>Wenn in der stuff/config.php ".'$debug'."=\"1\"; gesetzt ist, wird bei s&auml;mtlichen SQL Fehlern und Notices die Skriptausf&uuml;hrung abgebrochen und der Fehler ausgegeben.</li>
<li>Der Updater kann ab dieser Version anhand der Versionsnummer feststellen, welche Version vorliegt und anhand dessen alle notwendigen Updates selber raussuchen.</li>
<li>Password Recovery hinzugef&uuml;gt.</li>
<li>Teamspeak 3 Server k&ouml;nnen nun verwaltet werden.</li>
<li>TSDNS wird nun unterst&uuml;tzt.</li>
<li>Bei HL1 und HL2 basierenden Servern werden die bin/ Ordner nun auf manipulierte Dateien &uuml;berpr&uuml;ft.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Fehler behoben, dass im protection Mode keine Addons und Mappakete installiert werden k&ouml;nnen.</li>
<li>Fehler beim Absturzstatus eines EAC Servers behoben.</li>
<li>Abgelaufene IP Bans werden nun gel&ouml;scht.</li>
<li>Fehler beim Reinstall eines Gameservers, der EAC nutzt, mit einem Admin acount behoben: \"Fatal error: Call to a member function execute() on a non-object in ssh_exec.php on line 121\"</li>
</ul>','<div align=\"right\">12.22.2011</div>
<b>Changes and new functions:</b><br/>
<ul>
<li>Licence has changed regarding Gameserver from modules to one serverlicence for all types. Old licences will be changed to the new type so that a old 20 gameserver licence can manage 12 game and 8 voice for an example.</li>
<li>Colors of the traffic graph can now be edited over the panel.</li>
<li>Deactivated server and server that belong to deactivated users are no longer taken in count when calculating the licence usage.</li>
<li>Vserver can now be deactivated.</li>
<li>Userpermissions modified to support the new modules.</li>
<li>Lend module is available for Users with the private licence.</li>
<li>If ".'$debug'."=\"1\"; is set in the file stuff/config.php the runtime will stopped if any SQL error or notice is returned.</li>
<li>Since this version the updater can detect the version and can select the required updates on its own.</li>
<li>Added Password recovery.</li>
<li>Teamspeak 3 server can be managed now.</li>
<li>Support for TSDNS has been added.</li>
<li>The bin/ folders of HL1 and HL2 based servers are now checked for manipulated files.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Fixed error that addons and mappackages can not be installed if gameserver is protected</li>
<li>Error with EAC crash status has been resolved.</li>
<li>Outdated IP bans are deleted now.</li>
<li>Fixed error when reinstalling a gameserver that uses EAC as an admin user: \"Fatal error: Call to a member function execute() on a non-object in ssh_exec.php on line 121\"</li>
</ul>'),
('2.05','<div align=\"right\">07.12.2011</div>
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>Die Reiter f&uuml;r Gameserver und Voiceserver werden dem Benutzer nur noch dann angezeigt, wenn welche seinem Account zugeordnet sind.</li>
<li>Teamspeak 3 Server k&ouml;nnen verliehen werden.</li>
<li>Teamspeak 3 Server k&ouml;nnen resettet werden.</li>
<li>Die Anforderung FTP Server wurde f&uuml;r TSDNS entfernt. Alle Aktionen werden nun &uuml;ber SSH abgewickelt.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Bug behoben, dass ein neu angelegter Gameserver immer als Warserver eingetragen wird.</li>
<li>Gameserver wird nun gestoppt, bevor der Port ge&auml;ndert wird.</li>
</ul>','<div align=\"right\">12.07.2011</div>
<b>Changes and new functions:</b><br/>
<ul>
<li>The gameserver und voiceserver links at the userpanel are only shown to the user if he has such a server.</li>
<li>Teamspeak 3 server can be lended.</li>
<li>Teamspeak 3 server can be resetted.</li>
<li>The requirement FTP server has been removed for TSDNS. Now everything is done via SSH.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Fixed bug that a newly created Gameserver is alway a warserver no matter what the admin entered.</li>
<li>Gameserver will be stopped now if the port is going to change.</li>
</ul>'),
('2.06','<div align=\"right\">19.12.2011</div>
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>Benutzer k&ouml;nnen Teamspeak 3 Server  stoppen und starten.</li>
<li>Die Uptime und Slotbelegung wird bei den Teamspeak 3 Servern angezeigt.</li>
<li>Die Optionen Game- und Voiceserver auszuleihen, werden beim Verleihformular ausgeblendet, wenn keine Server dieser Art eingerichtet worden sind.</li>
<li>Sollte die TS 3 Instanz bei einem Statuscheck nicht erreicht werden k&ouml;nnen, werden die Admins mittels einer E-Mail informiert und versucht, den Server neu zu starten.</li>
<li>Server aus einer TS 3 Instanz, die nicht in der Datenbank sind, k&ouml;nnen nun auch nachtr&auml;glich importiert werden.</li>
<li>Belegungsstatistik f&uuml;r TS 3 Slots hinzugef&uuml;gt.</li>
<li>Von Easy-Wi versendete Mails werden gelogt und in der Datenbank gespeichert.</li>
<li>Der Reset eines TS 3 Servers entfernt nun auch hochgeladene Dateien.</li>
<li>Ist ein Server offline, wird der Status nun nochmals nachgefragt, bevor er als \"Offline\" eingestuft und der Neustart versucht wird.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Shorttags <? ?> in den admin_*_list.tpl Dateien durch <?php ?> ersetzt.</li>
<li>500 Fehler beim Import eines TS 3 Masterservers ohne virtuelle Server, oder Servern ohne Admins behoben.</li>
<li>Column `lendedserver` von VARCHAR(20) auf VARCHAR(60) vergr&ouml;&szlig;ert, damit der initiale TS 3 Admin Berechtigungsschl&uuml;ssel gespeichert werden kann.</li>
<li>Fatal Error bei deaktiviertem Debugger beim protection Mode behoben.</li>
</ul>','<div align=\"right\">12.19.2011</div>
<b>Changes and new functions:</b><br/>
<ul>
<li>User can start and stop Teamspeak 3 servers.</li>
<li>Uptime and slotusage will be shown at Teamspeak 3 servers.</li>
<li>The menu to lend voice- or gameservers will be removed if none are set up.</li>
<li>In case a TS 3 masterserver can not be reached the admins will be informed by e-mail. Also Easy-Wi will try to restart the server.</li>
<li>Virtual TS3 server of a masterserver which are not in the database yet can now be imported at any time.</li>
<li>Statistic that shows TS3 slotusage added.</li>
<li>Mails send by Easy-Wi will be logged and stored at the database.</li>
<li>The TS 3 server reset now removes fiels and folders uploaded by the users.</li>
<li>In case a server is offline the offline status will be rechecked before the panel treats him as offline and tries the restart.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Shorttags <? ?> in the admin_*_list.tpl Files replaced with <?php ?>.</li>
<li>Fixed 500 error at the import of a TS 3 masterserver in cases no virtual servers exist or a virtual server has no admins.</li>
<li>Column `lendedserver` changed from VARCHAR(20) to VARCHAR(60) so that the initial TS3 admin token can be stored.</li>
<li>Solved fatal error that occured at the protection mode when the debugger is set to on .</li>
</ul>'),
('2.07','<div align=\"right\">22.12.2011</div>
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>Ist ein Addon mittels Easy-Wi installiert worden, werden die beim Benutzer vorhandenen Dateien von Mani Admins \"gametypes.txt\" und Dateien aus Sourcemods Ordner \"plugins\" nun mit denen in \"masteraddons\" abgeglichen. Ist die Datei im Masteraddons Ordner neuer, so wird die Datei beim User aktualisiert.</li>
<li>Backupsystem f&uuml;r die einzelnen Virtuellen Server von Teamspeak 3 hinzugef&uuml;gt.</li>
<li>Monatlicher Traffikverbrauch f&uuml;r Teamspeak 3 Server einstellbar.</li>
<li>Erweiterte Sortierfunktionen f&uuml;r die &Uuml;bersichtsseiten.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>2 Notice Meldungen aus der lend.php entfernt.</li>
</ul>','<div align=\"right\">12.22.2011</div>
<b>Changes and new functions:</b><br/>
<ul>
<li>If an addon is installed with Easy-Wi. The Mani Admin file \"gametypes.txt\" and Sourcemod files from the folders  \"plugins\" at the user side will be matched against the files in the masteraddons folder. If the file in masteraddons folder is newer the user´s file which is older will be replaced with the newer one.</li>
<li>Added backupsystem fot single virtual TS 3 server.</li>
<li>Monthly filetransfertraffic can be limited for TS 3 Server.</li>
<li>Extended sort functions for the overview pages.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Removed 2 notice errors from the lend.php</li>
</ul>
'),
('2.08','<div align=\"right\">05.01.2012</div>
<br />
<a href=\"http://wiki.easy-wi.com/de:admin:install\">F&uuml;r Minecraft muss die /etc/proftpd/proftpd.conf angepasst werden.</a><br />
Wenn alle Funktionen des SourceTV Demo Uploaders genutzt werden sollen, m&uuml;ssen die Programme lsof und zip installiert sein.<br />
<br />
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>Menupunkte f&uuml;r das Hinzuf&uuml;gen, wurden in die &Uuml;bersichten verlegt, um das Menu &uuml;bersichtlicher zu gestalten.</li>
<li>In den Templateeinstellungen der Gameserver k&ouml;nnen nun bestimmt werden, welche Cvars in welcher Config nicht ver&auml;ndert werden d&uuml;rfen. Die definierten Cvars werden vor jedem Serverneustart &uuml;berpr&uuml;ft und abge&auml;ndert.</li>
<li>Gameserver k&ouml;nnen nun &uuml;ber Easy-Wi auf einzelne CPU Kerne gebunden werden. Dabei wird eine &Uumlbersicht angezeigt, welcher Core schon wie stark ausgelastet ist.</li>
<li>Bei Minecraft Servern kann im Restartkalender nun auch die Weltbildspeicherung ausgew&auml;hlt werden.</li>
<li>Wird ein Minecraft Server gestopt, so wird vorher das Weltbild gesichert.</li>
<li>Wird ein Source Server gestopt, so wird das Aufnehmen einer Demo gegebenenfalls gestopt.</li>
<li>Versions&uuml;berpr&uuml;fung f&uuml;r das Webend hinzugef&uuml;gt.</li>
<li>Updates f&uuml;r den Webend k&ouml;nnen nun mit einem Klick im Webend eingespielt werden.</li>
<li>GTA San Andreas Server Query Funktion f&uuml;r die Server Status&uuml;berpr&uuml;fung hinzugef&uuml;gt.</li>
<li>Beim Statuscheck wird nun getrennt gepr&uuml;ft, ob der TSDNS und TS 3 Server am laufen ist. Vorher wurde der TSDNS Server nur dann neu gestartet, wenn der TS 3 Server nicht erreichbar war.</li>
<li>Wenn man beim Editieren eine TS3 Instanz deaktiviert wird der TS3 Server und ggf. auch der TSDNS Server gestopt.</li>
<li>Templates können nun mittels Unterordnern von template/ und languages/ angelegt werden.</li>
<li>Erweiterte Einstellungsmöglichkeiten beim Anlegen und Editieren eines Gameserver, wie Min Ram und Max Ram, um Spiele wie Minecraft besser unterst&uuml;tzen zu k&ouml;nnen.</li>
<li>SourceTV Demos können nun automatisiert und manuell auf einen externen Webspace in bereits komprimierter Form geuploaded werden.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Fehler behoben, dass bei der Verleihserver Statusabfrage von Voiceservern &uuml;ber XML immer mit \"tooslow\" geantwortet wurde.</li>
</ul>','<div align=\"right\">01.05.2012</div>
<br />
<a href=\"http://wiki.easy-wi.com/en:de:admin:install\">To improve the Minecraft support changes at the /etc/proftpd/proftpd.conf are required</a><br />
If you want to make use of all functions the SourceTV Demo Uploader you need the programs zip and lsof installed.<br />
<br />
<b>Changes and new functions:</b><br/>
<ul>
<li>The add links from the menu have been moved to the overviews. This step has been made to make the menu easier to use.</li>
<li>It is now possible to setup a protection for cvars in config files at the template settings.</li>
<li>The core affinity for gameservers can be setup via the panel now. It will be displayed for each core how many gameservers already have been bound to it.</li>
<li>Added the option to save the world with the restart planer for minecraft servers.</li>
<li>If a minecraftserver is stopped the world will be saved before stopping.</li>
<li>If a source server is stopped the demo recording will be stopped before stopping the server.</li>
<li>Added a versioncheck for the webpart.</li>
<li>Webend Updates can now be done with a click at the webend.</li>
<li>Added a GTA San Andreas query function for the server status check.</li>
<li>When doing a status check the state of the TSDNS and TS 3 server will be looked up indipently. Before the TSDNS has been only restarted if the TS 3 server could not be reached.</li>
<li>If a TS 3 masterserver is edited and set to inactive the TS 3 server and when needed the TSDNS server are stopped.</li>
<li>Added a the possibilty to manage templates by adding subfolders in the folders template/ and languages/.</li>
<li>Added more settings like min ram and max ram at the server add and edit menu to improve the support for games like Minecraft.</li>
<li>SourceTV Demos can now be uploaded automated and manual in a compressed archive to an external webspace.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Fixed the error that a lendserver statusrequest over XML regarding voiceservers is always answered with \"tooslow\".</li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error=$insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

} else {
	echo "Error: this file needs to be included by the updater!<br />";
}