<?php

/**
 * File: update_500-510.php.
 * Author: Ulrich Block
 * Date: 31.05.15
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

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('5.10','<div align=\"right\">27.06.2015</div>
<b>Änderungen:</b><br/>
<ul>
<li>Gameserver:
<ul>
<li>Ark: Survival Evolved hinzugefügt</li>
<li>YML Config Unterstützung</li>
<li>SMAC Addons entfernt</li>
<li>ESL Addon entfernt</li>
<li>Längere Namen bei Addons zulässig</li>
<li>Neueste Snapshots bei Metamod verfügbar</li>
<li>Neueste Snapshots bei Sourcemod verfügbar</li>
<li>E-Mail nach Installation</li>
</ul></li>
<li>Webspace:
<ul>
<li>Standard Domain kann gepflegt werden</li>
<li>Benutzer kann Beschreibung hinterlegen</li>
</ul></li>
<li>Benutzer:
<ul>
<li>Input Reihenfolge geändert</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Master Version wird überprüft</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>Quota wird nicht richtig erfasst</li>
<li>SSH2 IP wird beim TS3 Import genutzt</li>
<li>Voiceserver status check bei auto restart off funktioniert nicht</li>
<li>Userpanel TSDNS kann nicht editiert werden</li>
<li>Userpanel externer DNS bei TS3 kann nicht editiert werden</li>
<li>TS3 Gruppen Typ nicht überprüft</li>
<li>MySQL PW Änderung wird nicht übernommen</li>
<li>Gameserver Binary Dir wird nicht übergeben</li>
<li>Fehlendes Template Fallback bei der login.php</li>
<li>Feeds Funktion benutzt fehlerhaften ID check</li>
<li>Cronjobs Anzeige bei System Check teilweise fehlerhaft</li>
<li>Definierter RSYNC Server nicht genutzt</li>
<li>GS Reinstall bei fehlender HDD Config funktioniert nicht</li>
<li>Voice API Fehler</li>
<li>Ajax Suche im Webspace Admin funktioniert nicht</li>
<li>Userpanel Vertreter Verwaltung im Debug Modus funktioniert nicht</li>
<li>Registrierung fehlerhaft</li>
</ul>','<div align=\"right\">06.27.2015</div>
<b>Changes:</b><br/>
<ul>
<li>Gameserver:
<ul>
<li>Ark: Survival Evolved added</li>
<li>YML Config Support</li>
<li>SMAC Addons removed</li>
<li>ESL Addon removed</li>
<li>Longer names for addons possible</li>
<li>Newest Metamod snapshots added</li>
<li>Newest Sourcemod snapshots added</li>
<li>E-Mail after installation</li>
</ul></li>
<li>Webspace:
<ul>
<li>Default domain can be maintained</li>
<li>User can set a description</li>
</ul></li>
<li>User:
<ul>
<li>Changed order of input at add/mod</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Master version is checked</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>Quota not calculated properly</li>
<li>SSH2 IP used for import</li>
<li>Voiceserver status check not working with auto restart off</li>
<li>Userpanel TSDNS can not be edited</li>
<li>Userpanel external DNS at TS3 kcan not be edited</li>
<li>TS3 group type not checked</li>
<li>MySQL password change not reflected</li>
<li>Gameserver binary dir not used</li>
<li>Missing template fallback at login.php</li>
<li>Feeds function with wrong ID check</li>
<li>Cronjobs display partially incorrect at system check</li>
<li>Defined RSYNC server not used</li>
<li>GS reinstall with missing HDD config not working</li>
<li>Voice API</li>
<li>Ajax search at webspace admin not working properly</li>
<li>Userpanel substitute management with debig on not working</li>
<li>Registration not working</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}