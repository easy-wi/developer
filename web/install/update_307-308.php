<?php
/**
 * File: update_307-308.php.
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
if (@is_file('../stuff/keyphrasefile.php')){
	$aesfilecvar=getconfigcvars('../stuff/keyphrasefile.php');
	$aeskey=$aesfilecvar['aeskey'];
} else if (@is_file(EASYWIDIR.'/stuff/keyphrasefile.php')){
	$aesfilecvar=getconfigcvars(EASYWIDIR.'/stuff/keyphrasefile.php');
	$aeskey=$aesfilecvar['aeskey'];
} else if (@is_file(EASYWIDIR.'keyphrasefile.php')){
	$aesfilecvar=getconfigcvars(EASYWIDIR.'keyphrasefile.php');
	$aeskey=$aesfilecvar['aeskey'];
}
$insert_easywi_version=$sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('3.08','<div align=\"right\">03.12.2012</div>
<b>Änderungen:</b><br/>
<ul>
<li>Generell:
<ul>
<li>Das Komprimieren der Voice Server und Traffik Statistiken wurde optimiert.
</ul>
</li>
<li>API:
<ul>
<li>Hinzugefügt: Liste der nicht instalierten Masterserver ist in der Fehlermeldung enthalten.</li>
</ul>
<li>CMS:
<ul>
<li>Hinzugefügt: Anzahl von allen und verfügbaren Servern wird im Verleih angezeigt.</li>
<li>Hinzugefügt: Kanonische URLs</li>
<li>Hinzugefügt: Kommentar Funktionen</li>
<li>Hinzugefügt: Project Honeypot Key kann für das identifizieren von Spam genutzt werden.</li>
<li>Hinzugefügt: dnsbl.tornevall.org kann für das identifizieren von Spam genutzt werden.</li>
<li>Hinzugefügt: Verleihserver Liste.</li>
</ul>
</li>
<li>Gameserver:
<ul>
<li>Geändert: Addons sind nur noch über die Gameserverübersicht und das aktuelle Spiel und Template verwaltbar (Userpanel).</li>
<li>Geändert: Nur das ausgewählte Spiel kann editiert werden (Userpanel).</li>
</ul>
</li>
<li>Voiceserver:
<ul>
<li>Hinzugefügt: Querydaten queryName, queryNumplayers, queryMaxplayers, queryPassword, queryUpdatetime werden gespeichert.</li>
</ul>
</li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>Generell:
<ul>
<li>Masterserver Updatemail wird nur noch geschickt, wenn die gespeicherte Version kleiner als die Steam Version ist.</li>
<li>Zahlreiche neue Indexe der Datenbank hinzugefügt, was die Gesamtperformance verbessert.</li>
</ul>
</li>
<li>API:
<ul>
<li>Restart kann, wie in der Wiki beschrieben, durchgeführt werden. Kein Workaround mehr nötig.</li>
<li>Das Hinzufügen von Gameservern mit und ohne Master ID funktioniert nun, wie erwartet.</li>
</ul>
</li>
<li>CMS:
<ul>
<li>Kategorien und Tags werden nicht auf der Newsseite angezeigt.</li>
</ul>
</li>
<li>Gameserver:
<ul>
<li>Gameserver are marked as crashed under the same conditions like in the admin panel.</li>
</ul>
</li>
</ul>','<div align=\"right\">12.03.2012</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Compressing Voiceserver and traffic stats has been optimized.</li>
<li>Added several Indexes to the database which will increase the overall interface speed.</li>
</ul>
</li>
<li>API:
<ul>
<li>Added: List of missing mastergameserver is included in the error response</li>
</ul>
</li>
<li>CMS:
<ul>
<li>Added: Amount of total and available servers are shown at the lendpage</li>
<li>Added: Canonical URLs</li>
<li>Added: Comment functions</li>
<li>Added: Project Honeypot Key can be used for Spam detection.</li>
<li>Added: dnsbl.tornevall.org can be used for Spam detection.</li>
<li>Added: Lendserver list.</li>
</ul>
<li>Gameserver:
<ul>
<li>Changed: Addons are only accessable via gameserver overview and can be installed for the current game and template (Userpanel).</li>
<li>Changed: Only the selected game can be edited (Userpanel).</li>
</ul>
</li>
<li>Voiceserver:
<ul>
<li>Added: Querydata queryName, queryNumplayers, queryMaxplayers, queryPassword, queryUpdatetime will be saved.</li>
</ul>
</li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>General:
<ul>
<li>Masterserver updatemail is only being send if the stored version is less than the current steam version.</li>
</ul>
</li>
<li>API:
<ul>
<li>Restart can be done as described at the wiki. No Workaround needed.</li>
<li>Adding Gameservers with and without master ID given is working now as expected.</li>
</ul>
</li>
<li>CMS:
<ul>
<li>Fixed: categories and tags not shown at news overview.</li>
</ul>
</li>
<li>Gameserver:
<ul>
<li>Gameserver are marked as crashed under the same conditions like in the admin panel.</li>
</ul>
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