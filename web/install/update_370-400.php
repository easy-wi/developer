<?php
/**
 * File: update_370-400.php.
 * Author: Ulrich Block
 * Date: 27.08.13
 * Time: 20:27
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
    $query=$sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('4.00','<div align=\"right\">31.08.2013</div>
<b>Änderungen:</b><br/>
<p>
Easy-WI ist mit der Version 4.0 Open Source. Lizenz ist die GNU GPL v3. Das Upgrade von früheren Versionen auf 4.0 liefert auf Grund von Ioncube Restriktionen noch verschlüsselte Dateien aus.
Die Dateien können jederzeit durch quelloffene ersetzt werden. Die GNU GPL v3 lizensierte Version hat den Funktionsumfang der früheren unbegrenzten gewerblichen Version.
</p>
<p>
Die bearbeiteten Entwickler Tickets können in unserem <a href=\"https://github.com/easy-wi/developer/issues?milestone=1&page=1&state=closed\">Github Repository</a> eingesehen werden.
</p>
<ul>
<li>Generell:
<ul>
<li>Geändert: famfamfam Flag Icons verwendet</li>
<li>Geändert: Entfernen Buttons sind rot</li>
<li>Geändert: Bootstrap Template ist nun default</li>
<li>Hinzugefügt: Dänische Sprache (thx @MikkelDK)</li>
</ul></li>
<li>CMS:
<ul>
<li>Hinzugefügt: Download Modul</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Hinzugefügt: Spieltemplates und Addon Einstellungen können mit XML Dateien im- und exportiert werden</li>
<li>Geändert: SSH2 connect und execute Funktionen wurden komplett überarbeitet und erlauben ein schnelleres prozessieren</li>
<li>Hinzugefügt: Der Serverstatus wird im Admin Menü in der Übersicht farblich dargestellt</li>
</ul></li>
<li>User:
<ul>
<li>Hinzugefügt: Changelog wenn Userdaten geändert werden</li>
<li>Hinzugefügt: Benutzer können Vertreter verwalten</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Hinzugefügt: Im Userpanel können nun folgende Werte geändert werden: virtualserver_reserved_slots, virtualserver_needed_identity_security_level, virtualserver_hostmessage_mode, virtualserver_hostbanner_gfx_interval, virtualserver_antiflood_points_tick_reduce, virtualserver_antiflood_points_needed_command_block, virtualserver_antiflood_points_needed_ip_block</li>
<li>Hinzugefügt: Der Serverstatus wird im Admin Menü in der Übersicht farblich dargestellt</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br />
<ul>
<li>ESX(i) VM nun korrekt gestartet</li>
<li>Config Liste enthält nur noch zulässige Configs im Protection Mode</li>
<li>Empfänger und Sender waren SMTP Modus vertauscht</li>
</ul>
','<div align=\"right\">08.31.2013</div>
<p>
Easy-WI becomes open source with version 4.0. License is GNU GPL v3. An upgrade from older versions to 4.0 will still hand out ioncube encrypted files due to ioncube restrictions.
All files can be replaced by unencrypted files. The GNU GPL v3 licensed version has the same functions as the previous unlimited commercial version.
</p>
<p>
All processed developer tickets can be seen at <a href=\"https://github.com/easy-wi/developer/issues?milestone=1&page=1&state=closed\">our github repository</a>
</p>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Changed: famfamfam flag icons are used</li>
<li>Changed: Remove buttons are highlighted in red</li>
<li>Changed: Bootstrap template is now default template</li>
<li>Added: Danish language (thx @MikkelDK)</li>
</ul></li>
<li>CMS:
<ul>
<li>Added: Download modul </li>
</ul></li>
<li>Gameserver:
<ul>
<li>Added: games and addons settings can be ex- and imported as/with XML files</li>
<li>Changed: Rewrite of ssh2 connect and execute function allowing faster processing</li>
<li>Added: Colored highlighting at the overview</li>
</ul></li>
<li>User:
<ul>
<li>Added: Changelog regarding userdata</li>
<li>Added: Users can manage substitutes</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Added: server vars can be edited at userpanel  virtualserver_reserved_slots, virtualserver_needed_identity_security_level, virtualserver_hostmessage_mode, virtualserver_hostbanner_gfx_interval, virtualserver_antiflood_points_tick_reduce, virtualserver_antiflood_points_needed_command_block, virtualserver_antiflood_points_needed_ip_block</li>
<li>Added: Colored highlighting at the overview</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br />
<ul>
<li>ESX(i) VM is not properly added/started</li>
<li>Configlist is not showing addon configs in protection mode</li>
<li>Receiver and sender flipped at SMTP mode</li>
</ul>
')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

    $query=$sql->prepare("UPDATE `settings` SET `template`='default' WHERE `template`='twitterbootstrap'");
    $query->execute();
} else {
    echo "Error: this file needs to be included by the updater!<br />";
}