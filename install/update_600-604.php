<?php

/**
 * File: update_600-604.php.
 * Author: Ulrich Block
 * Date: 06.01.18
 * Time: 13:39
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
('6.0.4','<div align=\"right\">06.01.2018</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>General
<ul>
<li>IPv6 Addressen k&ouml;nnen nun geloggt werden</li>
<li>Diverse Verbesserungen im italienischen Sprachpacket</li>
</ul></li>
<li>Game Server
<ul>
<li>GameQ Query Libary aktualisiert</li>
<li>Start/Stop Button bei der Konsole im Benutzerbereich hinzugef&uuml;gt</li>
<li>Workaround f&uuml;r Debian 9 und screen -L hinzugef&uuml;gt</li>
<li>CSV Dateien werden nun kopiert</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>Name des Spiels wird nicht in der Konsole bei der statuscheck.php angezeigt</li>
<li>Game Server Status Zeitstempel in der Benutzer &Uuml;bersicht</li>
<li>Bei DB Entfernen von Game Servern wird der Shell Debug Output angezeigt</li>
<li>Beim Neustart von Game Server und fehlender gesch&uuml;tzter Datei wird dir Ordnerstruktur rekusiv noch einmal erstellt</li>
<li>Der \"Notified count\" wird nicht zur&uuml;ck gesetzt sobald ein Game Server wieder erreichbar ist</li>
<li>Update Success wird bei SteamCMD Spielen nicht mehr korrekt erkannt</li>
<li>Restart Planer funktioniert nicht mit neuestem MySQL auf Ubuntu 16.04</li>
<li>ARK Template</li>
<li>MTA:SA Template</li>
<li>Seiten Liste funktioniert nicht auf neuesten MySQL Server mit Ubuntu 16.04</li>
<li>CMS Settings nicht immer editiertbar</li>
<li>Geh&ouml;rt ein TS3 Masterserver einem Reseller wird der Status nicht korrekt abgepr&uuml;ft</li>
<li>Initiales TS3 Passwort wird nicht in der DB gespeichert</li>
<li>suhosin check beim Import von TS3 Servern</li>
<li>Falsche &Uuml;berschrift in der TSDNS Admin &Uuml;bersicht</li>
<li>Gruppen Anlegen mit aktiviertem Debugger funktioniert nicht</li>
<li>Diverse undefined Variablen Notices</li>
<li>Falsches Icon von Font Awesome bei Hybridauth f&uuml;r Twitch benutzt</li>
<li>Security Problem beim Switch vom Reseller zu Benutzern</li>
<li>SQL Syntax bei Game und Voice Server API mit External ID</li>
<li>Login Loop (Erster Login kann fehl schlagen)</li>
<li>\"Please allow redirection settings\" bei Logout und einigen Server Konfigurationen</li>
<li>Ticket Kategorie kann auf neuesten MySQL Server mit Ubuntu 16.04 nicht angelegt werden</li>
<li>Redirect im Default Apache2 Vhost Template</li>
</ul>','<div align=\"right\">01.06.2018</div>
<b>Changes:</b><br/>
<ul>
<li>General
<ul>
<li>IPv6 adresses can be logged</li>
<li>Multiple improvements at the Italian language package</li>
</ul></li>
<li>Game Server
<ul>
<li>GameQ Query library updated</li>
<li>Start/Stop button added at the console in the user area</li>
<li>Workaround for Debian 9 and screen -L added</li>
<li>CSV files are copied instead of being linked</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>Game name not displayed at console output of statuscheck.php</li>
<li>Game server status time stamp at user overview</li>
<li>DB remove only of game server outputs shell debug and fails</li>
<li>Restart of game server with a not existing protected file creates folders recursively</li>
<li>\"Notified count\" not reset in case game server is reachable again</li>
<li>Update success for SteamCMD games no longer detected</li>
<li>Restart planer not working with latest MySQL on Ubuntu 16.04</li>
<li>ARK Template</li>
<li>MTA:SA Template</li>
<li>Page list not working with latest MySQL on Ubuntu 16.04</li>
<li>CMS cannot be edited in some cases</li>
<li>In case a TS3 master belongs to a reseller the instances are not checked properly by status check</li>
<li>Initial TS3 password not saved to DB</li>
<li>suhosin check at TS3 import</li>
<li>Incorrect headline at TSDNS admin overview</li>
<li>Adding groups with active debugger not working</li>
<li>Multiple undefined variable notices (with active debugger)</li>
<li>Incorrect icon used from Font Awesome in case of Hybridauth and Twitch</li>
<li>Security issue in case Reseller switches to a user account</li>
<li>SQL syntax at game and voice server API in combination with external ID</li>
<li>Login Loop (first login might fail)</li>
<li>\"Please allow redirection settings\" error in case of logout on some systems</li>
<li>Adding a ticket category with latest MySQL on Ubuntu 16.04 fails</li>
<li>Redirect at default Apache2 vhost template</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}