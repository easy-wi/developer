<?php
/**
 * File: update_304-305.php.
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
('3.05','<div align=\"right\">22.08.2012</div>
<b>Änderungen:</b><br/>
<ul>
<li>Beim Anlegen eines Switchservers kann ein Spiel für den User vorselektiert werden.</li>
<li>Rootserver können nun auch mit einer externen ID bei der Game und Voiceserver API vorbestimmt werden.</li>
<li>Die Game und Voiceserver API antwortet mit wesentlich mehr Details.</li>
<li>Viele neue Parameter bei der Game und Voiceserver API.</li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>Mehrere Fehler im Zusammenhang mit der neuen Gameserver Datenbankstruktur behoben.</li>
<li>Addon Installation/Löschen gefixt.</li>
<li>FastDL Sync gefixt.</li>
</ul>','<div align=\"right\">08.22.2012</div>
<b>Changes:</b><br/>
<ul>
<li>While adding a switchserver it is possible to define the game that is preselected for the user.</li>
<li>Rootserver can be predefined by an external ID at the game and voiceserver API.</li>
<li>The game and voiceserver API replies much more detailed.</li>
<li>Many new parameters at the game and voice server API.</li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>Fixed multiple issues regarding new Gameserver database structure.</li>
<li>Addons removal/add fixed.</li>
<li>FastDL fixed.</li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error=$insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');


$query=$sql->prepare("SELECT `id`,`maxtraffic`,`filetraffic`,`lastfiletraffic` FROM `voice_server`");
$query2=$sql->prepare("UPDATE `voice_server` SET `maxtraffic`=?,`filetraffic`=?,`lastfiletraffic`=? WHERE `id`=? LIMIT 1");
$query->execute(array());
foreach ($query->fetchall() as $row) {
	$query2->execute(array(($row['maxtraffic']/1048576),($row['filetraffic']/1048576),($row['lastfiletraffic']/1048576),$row['id']));
}
} else {
	echo "Error: this file needs to be included by the updater!<br />";
}
?>