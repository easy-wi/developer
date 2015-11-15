<?php
/**
 * File: update_305-306.php.
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
('3.06','<div align=\"right\">30.09.2012</div>
<b>Änderungen:</b><br/>
<ul>
<li>Api Änderungen:
<ul>
<li>Ports können in der API vorgegeben werden.</li>
<li>Slots, Active und Private sind nun optional.</li>
<li>Die Daten zu einem User können nun samt seiner Server gelistet werden.</li>
<li>Es werden nun die Defaultsport vom primary Game genommen, sofern es keinen anderen Switchserver gibt, der mehr besitzt.</li>
</ul></li>
<li>In der Versionsübersicht wird ab dem nächsten Update das kommende Changelog verlinkt.</li>
<li>Bei den Addons können nun Befehle angegeben werden, die aus dem Startbefehl entfernt werden, wenn das Addon installiert ist.</li>
<li>Bei den Gameserverimages können nun Mods inklusive angepasster Startbefehle verwaltet werden.</li>
<li>Support für das neue steamCmd Tool hinzugefügt. CS:GO wird nun direkt unterstützt.</li>
<li>Zusatzparameter für die Mapgroup von CS:GO eingeführt.</li>
<li>Das Interface fragt nun regelmäßig die Steam API ab, um Updates zu bestimmen und speichert die Version der installierten Master.</li>
<li>TSDNS Server können nun getrennt von TS3 masterservern verwaltet werden.</li>
<li>TS3 Masterserver können angewiesen werden die neu eingeführten externen TSDNS Server zu nutzen.</li>
<li>TS3 DNS Adressen können losgelöst von TS3 Servern verwaltet werden.</li>
<li>Veralterte und nicht mehr genutzet Funktionen aus der functions.php entfernt.</li>
<li>Steamnews können bei den Feeds über die Steam API geladen werden.</li>
<li>Beim Anlegen von Items für einen User und in den Übersichten wird nun zusätzlich sein Vor- und Nachname angezeigt, sofern vorhanden.</li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>SQL Exception gefixt, wenn eine ServerID beim Anlegen eines Gameservers per API vorgegeben wird.</li>
<li>Beim Verwenden von mt_rand() werden nun die Defaultwerte verwendet.</li>
<li>Fehlerhaften SQL query in der tsdns Funktion behoben.</li>
<li>Bei TS3 DNS Adressen wird nun Kleinschreibung erzwungen.</li>
<li>Ist ein TS3 Masterserver abgestürzt, wird nun korrekt bei der Warnung in der Adminübersicht verlinkt.</li>
<li>Diverse Fehler in der API behoben, bei denen Variablen nicht gesetzt waren.</li>
</ul>','<div align=\"right\">09.30.2012</div>
<b>Changes:</b><br/>
<ul>
<li>Api changes:
<ul>
<li>Ports can be send.</li>
<li>Slots, Active and Private are optional.</li>
<li>Userdata can be listed along with his servers.</li>
<li>In case a primary game is given its default ports are used unless another switchserver has more.</li>
</ul></li>
<li>With the next Update the changelog of the upcomming release will be linked.</li>
<li>At the addons commads and parameters can be defined which will be removed if the addon is installed.</li>
<li>At the gameserverimages mods can be defined with additional startparameters.</li>
<li>The steamCmd is now supported. CS:GO can be activly supported that way.</li>
<li>+mapgroup can be set and managed now for CS:GO.</li>
<li>Steam API is used to check if a masterserver needs updating.</li>
<li>TSDNS server can be managed indipendend from TS3 masterservers.</li>
<li>TS3 masterserver can be setup to use external TSDNS server.</li>
<li>TS3 DNS address can be managed undependend from TS3 servers.</li>
<li>Removed outdated and unused from functions.php.</li>
<li>Steamnews can be pulled at Feeds from the Steam API.</li>
<li>While adding Items for user and at the overviews the first and last name is displayed if has been entered.</li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>Fixed a SQL Exception if a ServerID is given when adding a gameservers at the API.</li>
<li>If mt_rand() is used the default values are used.</li>
<li>Fixed SQL query at the tsdns function.</li>
<li>At TS3 DNS adresssmall letters are forced.</li>
<li>In case a TS3 masterserver is crashed the overview is correctly linked.</li>
<li>Fixed multiple cases at the API where variables are not set.</li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error = $insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$alter_servertypes_modcmds = $sql->prepare("ALTER TABLE `servertypes` ADD COLUMN `modcmds` TEXT DEFAULT NULL AFTER `cmd`");
$alter_servertypes_modcmds->execute();
$response->add('Action: alter_servertypes_modcmds done: ');
$error = $alter_servertypes_modcmds->errorinfo();
$alter_servertypes_modcmds->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$update_servertypes_csgo = $sql->prepare("UPDATE `servertypes` SET `steamgame`='S',`appID`=730,`updates`=1,`description`='Counter-Strike: Global Offensive',`type`='gserver',`gamebinary`='srcds_run',`binarydir`=NULL,`modfolder`='csgo',`fps`=NULL,`slots`=12,`map`='de_dust',`cmd`='./%binary% -game csgo -console -usercon +ip %ip% +port %port% -maxplayers_override %slots% +map %map% +mapgroup %mapgroup%',`modcmds`='[Classic Casual = default]\r\n+game_type 0 +game_mode 0\r\n\r\n[Classic Competitive]\r\n+game_type 0 +game_mode 1\r\n\r\n[Arms Race]\r\n+game_type 1 +game_mode 0\r\n\r\n[Demolition]\r\n+game_type 1 +game_mode 1',`tic`=NULL,`qstat`='a2s',`gamemod`='N',`gamemod2`='css',`configs`='cfg/server.cfg both\r\ncfg/autoexec.cfg both\r\ngamemodes.txt\r\ngamemodes_server.txt',`configedit`=NULL,`qstatpassparam`='password:1',`portStep`=100,`portMax`=5,`portOne`=27015,`portTwo`=27016,`portThree`=27017,`portFour`=27018,`portFive`=27019,`mapGroup`='mg_bomb' WHERE `shorten`='csgo'");
$update_servertypes_csgo->execute();
$response->add('Action: update_servertypes_csgo done: ');
$error = $update_servertypes_csgo->errorinfo();
$update_servertypes_csgo->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');


$query = $sql->prepare("SELECT `resellerid` FROM `resellerdata`");
$query2 = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `resellerid`=? AND `shorten`='csgo'");
$query3 = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`qstat`,`gamemod`,`gamemod2`,`configs`,`configedit`,`qstatpassparam`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`mapGroup`,`resellerid`) VALUES('S',730,1,'csgo','Counter-Strike: Global Offensive','gserver','srcds_run',NULL,'csgo',NULL,0,'de_dust','./%binary% -game csgo -console -usercon +ip %ip% +port %port% -maxplayers_override %slots% +map %map%  +mapgroup %mapgroup%','[Classic Casual = default]\r\n+game_type 0 +game_mode 0\r\n\r\n[Classic Competitive]\r\n+game_type 0 +game_mode 1\r\n\r\n[Arms Race]\r\n+game_type 1 +game_mode 0\r\n\r\n[Demolition]\r\n+game_type 1 +game_mode 1',NULL,'a2s','N','css','cfg/server.cfg both\r\ncfg/autoexec.cfg both\r\ngamemodes.txt\r\ngamemodes_server.txt',NULL,'password:1',100,4,27015,27016,27017,27018,27019,'mg_bomb',?)");
$query2->execute(array(0));
if ($query2->rowCount()==0) {
	$query3->execute(array(0));
}
$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
	$query2->execute(array($row['resellerid']));
	if ($query2->rowCount()==0) {
		$query3->execute(array($row['resellerid']));
	}
}
} else {
	echo "Error: this file needs to be included by the updater!<br />";
}