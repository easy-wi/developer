<?php
/**
 * File: update_310-320.php.
 * Author: Ulrich Block
 * Date: 10.03.13
 * Time: 18:44
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
('3.20','<div align=\"right\">11.03.2013</div>
<b>Änderungen:</b>
<ul>
<li>Generell:
<ul>
<li>Hinzugefügt: Suchfunktion für IDs, externen IDs, Adressen, IPs und Namen.</li>
<li>Hinzugefügt: Hostname bei Userlogs.</li>
</ul></li>
<li>API:
<ul>
<li>Hinzugefügt: Für Benutzer zusätzliche Attribute mail_backup,mail_gsupdate,mail_securitybreach,mail_serverdown,mail_ticket,mail_vserver</li>
<li>Hinzugefügt: Game- und Voiceserver Attribut autoRestart</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Hinzugefügt: CS:GO Workshop Maps werden unterstützt.</li>
<li>Hinzugefügt: Protection Mode kann für jedes Image deaktiviert werden.</li>
<li>Hinzugefügt: Auto Restart kann je Gameserver deaktiviert werden.</li>
<li>Hinzugefügt: Drei Wege den Server zu löschen (Sicheres Löschen/In jedem Fall Löschen/Aus der Datenbank Löschen).</li>
<li>Entfernt: userpanel_gserver_protectionmode.tpl und damit Zwischenschritt.</li>
<li>Geändert: Reinstall im Userpanel nun als eigener Menüpunkt und benutzerfreundlicher.</li>
<li>Geändert: Reinstall im Adminpanel benutzerfreundlicher.</li>
<li>Geändert: Startmap wird dem User nur noch angezeigt, wenn eine im Template definiert wurde.</li>
<li>Geändert: Beim Gameswitch wird der Protectionmode gestoppt, falls aktiviert.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Hinzugefügt: Zusätzliche Logfunktionen für Useraktionen.</li>
<li>Hinzugefügt: Auto Restart kann je Gameserver deaktiviert werden.</li>
<li>Hinzugefügt: Drei Wege den Server zu löschen (Sicheres Löschen/In jedem Fall Löschen/Aus der Datenbank Löschen).</li>
<li>Entfernt: Pagination im Userpanel bei TS3 und TSDNS.</li>
<li>Entfernt: userpanel_voiceserver_reset.tpl und damit Zwischenschritt.</li>
<li>Geändert: File Traffic wird nun in KB an Stelle von MB berechnet, um Ungenauigkeiten zu vermeiden.</li>
</ul></li>
</ul>
<br/>
<b>Bugfixes:</b>
<ul>
<li>Generell:
<ul>
<li>Mehrere Platzhalter im Userlog werden werden durch den korrekten Text ersetzt.</li>
<li>Ticket Übersicht im Userpanel.</li>
</ul></li>
<li>API:
<ul>
<li>Standard Mapgroup wird beim Anlegen eines Gameserver verwendet.</li>
</ul></li>
<li>CMS:
<ul>
<li>Startzeit des Protection Modes wird korrekt angeteigt.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Gamemod Selektion, wenn Gameswitch verwendet wird.</li>
<li>Protection Mode zeigt korrekte Zeit an.</li>
</ul></li>
<li>Benutzer:
<ul>
<li>Mit eingeschränkten Rechten wird in der Übersicht korrekt dargestellt.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Reset eines TS3 Virtual Servers zeigt nicht mehr in jedem Fall Error ID an.</li>
<li>TS3 Master hinzufügen.</li>
</ul></li>
</ul><br/>
','<div align=\"right\">03.11.2013</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Added: Search for IDs, external IDs, adresses, IPs and names.</li>
<li>Added: Hostname at userlogs.</li>
</ul></li>
<li>API:
<ul>
<li>Added: User attributes mail_backup,mail_gsupdate,mail_securitybreach,mail_serverdown,mail_ticket,mail_vserver</li>
<li>Added: Game- and voiceserver attribute autoRestart</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Added: CS:GO Workshop maps are supported.</li>
<li>Added: Protection mode can be deactivated for each image.</li>
<li>Added: Auto restart can be deactivated for each server.</li>
<li>Added: Three different ways of deletion (Safe Delete/Delete in any case/Remove from database).</li>
<li>Removed: userpanel_gserver_protectionmode.tpl and with it extra step.</li>
<li>Changed: Reinstall at Userpanel is now an extra menu entry and usage more userfriendly.</li>
<li>Changed: Reinstall at Adminpanel is more userfriendly.</li>
<li>Changed: Startmap will only displayed to the user if a default map is configured at the game template.</li>
<li>Changed: Using the gameswitch will stop the protectionmode in case it is active.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Added: Increased logging of user actions.</li>
<li>Added: Auto restart can be deactivated for each server.</li>
<li>Added: Three different ways of deletion (Safe Delete/Delete in any case/Remove from database).</li>
<li>Removed: Pagination at the user part for TS3 and TSDNS.</li>
<li>Removed: userpanel_voiceserver_reset.tpl and with it extra step.</li>
<li>Changed: File Traffic is calculated in KB in stead of MB to prevent inaccuracies.</li>
</ul></li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>General:
<ul>
<li>Several placeholders at log display are replaced with correct text.</li>
<li>Ticketoverview at userpanel.</li>
</ul></li>
<li>API:
<ul>
<li>default mapgroup is used while adding a new gameswitch server.</li>
</ul></li>
<li>CMS:
<ul>
<li>Protection Mode´s start time is displayed correct.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Gamemod selected if gameswitch is used.</li>
<li>Protection Mode shows correct time.</li>
</ul></li>
<li>User:
<ul>
<li>Amount is displayed correctly with restricted permissions.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>TS3 virtual server reset not displaying Error ID anymore.</li>
<li>TS3 master add.</li>
</ul></li>
</ul>
')");
    $insert_easywi_version->execute();
    $response->add('Action: insert_easywi_version done: ');
    $insert_easywi_version->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}