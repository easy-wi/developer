<?php
/**
 * File: update_306-307.php.
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
} else if (@is_file('stuff/keyphrasefile.php')){
	$aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
	$aeskey=$aesfilecvar['aeskey'];
} else if (@is_file('keyphrasefile.php')){
	$aesfilecvar=getconfigcvars('keyphrasefile.php');
	$aeskey=$aesfilecvar['aeskey'];
}
$insert_easywi_version=$sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('3.07','<div align=\"right\">01.11.2012</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>Generelles
<ul>
<li>Login mittels E-Mail ist nun m&ouml;glich.</li>
<li>Benutzer haben nun die zus&auml;tzlichen Attribute creationTime, updateTime, birthday, country, fax and salutation.</li>
</ul>
</li>
<li>CMS
<ul>
<li>page_home.tpl hinzugef&uuml;gt.</li>
<li>Sidemap hinzugef&uuml;gt.</li>
<li>Die Anzeige von Protectioncheck und Verleih kann deaktiviert werden.</li>
<li>Das Page Object enth&auml;lt nun die letzten News.</li>
<li>Kontaktformular hinzugef&uuml;gt.</li>
<li>Custom 404 Seite.</li>
<li>Galerie Seite hinzugef&uuml;gt.</li>
<li>Suche hinzugef&uuml;gt.</li>
<li>Navigationspfad kann bei Seiten angezeigt werden.</li>
</ul></li>
<li>Server
<ul>
<li>CS:GO wird aktualisiert, egal ob appID 730, oder 740 verwendet wird.</li>
<li>Gameserver k&ouml;nnen nun aus der Admin&uuml;bersicht gestartet und gestoppt werden.</li>
<li>Steam APP IDs k&ouml;nnen nun bis zu 9 Stellen, an Stelle von 5 haben.</li>
</ul></li>
<li>API
<ul>
<li>403 Error hinzugef&uuml;gt, wenn weder xml, noch json per POST gesendet wurde</li>
<li>Zus&auml;tzliche Fehlermeldung hinzugef&uuml;gt, wenn bei Gameservern keine Eintr&auml;ge f&uuml;r die/den gesendeten Shorten existieren.</li>
<li>Neue Methode hinzugef&uuml;gt: Gameserver restarten</li>
<li>User Importer hinzugef&uuml;gt</li>
<li>Attribut groupID kann nun gesendet werden, mittels dessen man die Usergruppe bestimmen kann.</li>
<li>Beim Editieren ist es nun nicht mehr zwingend, eine E-Mail mit anzugeben.</li>
<li>ExternalID kann nun eine L&auml;nge von 255 haben und die Charakter [a-zA-Z0-9] enthalten.</li>
<li>Gameserver k&ouml;nnen gestoppt und gestartet werden.</li>
</ul></li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>CMS
<ul>
<li>PHP Notice bei den Tags behoben, wenn der Debugger an ist.</li>
<li>PDO Exception im Protectioncheck behoben, wenn der Debugger an ist.</li>
</ul></li>
<li>Server
<ul>
<li>PDO Exception in der freeIP behoben, wenn der Debugger an ist.</li>
<li>Der Benutzer bekommt nun keinen Fehler mehr gezeigt, wenn er den Fastdownload Server initial setzt.</li>
<li>Gameserver werden geskippt, sollte keine Quakestat Variable gegeben sein.</li>
</ul></li>
<li>API
<ul>
<li>Fehler behoben, dass die GruppenID nicht gespeichert wurde.</li>
<li>Error behoben, dass das initiale Passwort eines Users nicht funktioniert.</li>
<li>Fehler im Voiceteil behoben, dass bei der Auswahl von IP und Port kein Array gegeben war.</li>
</ul></li>
</ul>','<div align=\"right\">11.01.2012</div>
<b>Changes:</b><br/>
<ul>
<li>General
<ul>
<li>Login with e-mail possible.</li>
<li>User have additional attributes creationTime, updateTime, birthday, country, fax and salutation.</li>
</ul></li>
<li>CMS
<ul>
<li>page_home.tpl added.</li>
<li>Sidemap added.</li>
<li>Protectioncheck and Lendpages can be deactivated.</li>
<li>Page Object contains the last news.</li>
<li>Contact form added.</li>
<li>Custom 404 page added.</li>
<li>Gallery added.</li>
<li>Search added.</li>
<li>Navigationpath can be shown at pages.</li>
</ul></li>
<li>Server</li>
<ul>
<li>CS:GO will be updated, no matter if appID 730 or 740 is given.</li>
<li>Gameserver can be stopped and started at the adminoverview.</li>
<li>Steam appIDs can be 9 signs long, instead of 5.</li>
</ul></li>
<li>API
<ul>
<li>403 Error added, if neither xml, nor json has been send.</li>
<li>Additional error message, if masterserver does not exists with the shorten.</li>
<li>New method: Gameserver (re)start/stop</li>
<li>User Importer added.</li>
<li>Attribute groupID can be send.</li>
<li>E-Mail is not mandetory anymore.</li>
<li>ExternalID can be 255 signs long and contain [a-zA-Z0-9].</li>
<li>gameserver can be (re)started/stopped.</li>
</ul></li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>CMS
<ul>
<li>PHP Notice fixed if no tag is set in case debugger is on.</li>
<li>PDO Exception removed at the Protectioncheck in case debugger is on.</li>
</ul></li>
<li>Server
<ul>
<li>PDO Exception solved at freeIP function in case debugger is on.</li>
<li>Error removed in case the initial Fastdownload Server has not been set yet at the userpanel.</li>
<li>Gameserver will be skippt, if the Quakestat variable is not set.</li>
</ul></li>
<li>API
<ul>
<li>Solved groupID was not set.</li>
<li>Solved initial user password does not work.</li>
<li>Solved no array set in case IP and Port has been given.</li>
</ul></li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error=$insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$update_servertypes_csgo=$sql->prepare("UPDATE `servertypes` SET `steamgame`='S' WHERE `shorten`='csgo'");
$update_servertypes_csgo->execute();
$response->add('Action: update_servertypes_csgo done: ');
$error=$update_servertypes_csgo->errorinfo();
$update_servertypes_csgo->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

} else {
	echo "Error: this file needs to be included by the updater!<br />";
}