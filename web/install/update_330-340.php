<?php
/**
 * File: update_330-340.php.
 * Author: Ulrich Block
 * Date: 20.04.13
 * Time: 15:53
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
('3.40','<div align=\"right\">09.05.2013</div>
<b>Änderungen:</b>
<ul>
<li>Generell:
<ul>
<li>Geändert: Javascript Dateien nun im js/ Ordner.</li>
<li>Geändert: CSS Dateien nun im css/ Ordner.</li>
</ul></li>
<li>CMS:
<ul>
<li>Geändert: Das Kontakt Formularnutzt als Versender die Kontakt E-Mail und fügt die eingegeben E-Mail und Namen vor dem eingegebenen Text ein.</li>
</ul></li>
</ul>
<br/>
<b>Bugfixes:</b>
<ul>
<li>Generell:
<ul>
<li>Login mit email an Stelle von Username nun wieder möglich.</li>
<li>Hinzufügen von Ticket Kategorien (Error: Topic).</li>
</ul></li>
<li>API:
<ul>
<li>User List Funktion produziert keine Exception im Debug Modus mehr.</li>
</ul></li>
<li>CMS:
<ul>
<li>Zeilenumbrüche, die beim Kontakt Formular eingegeben werden, werden in HTML umgewandelt. </li>
</ul></li>
<li>Gameserver:
<ul>
<li>In der Useransicht wird der VAC Mode korrekt dargestellt.</li>
<li>Server kann ohne VAC gestartet werden.</li>
<li>Gameservernamen die nicht ASCII Zeichen enthalten, können erfasst werden.</li>
<li>Gameservernamen werden beim Statuscheck für die Anzeige auf der Konsole zu ISO-8859-1 konvertiert.</li>
<li>Die Überprüfungszeit wird korrekt dargestellt, auch wenn der User Serverregeln bricht.</li>
<li>Map aus dem Restartplaner wird beim Restart korrekt verwendet.</li>
</ul></li>
<li>Benutzer:
<ul>
<li>Beim Entfernen von usern wird die TS3 Class ordnungsgemäß geladen.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Das letzte TS3 Update macht das Senden von virtualserver_ip unmöglich, deswegen aus der Klasse entfernt.</li>
</ul></li>
</ul>
<br/>
','<div align=\"right\">05.09.2013</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Changed: Javascript files moved to js/ folder.</li>
<li>Changed: CSS filesmoved to css/ folder.</li>
</ul></li>
<li>CMS:
<ul>
<li>Changed: Contact form will use support email as sender instead of entered and prepend name and email to the message.</li>
</ul></li>
</ul>
<br/>
<b>Bugfixes:</b><br/>
<ul>
<li>General:
<ul>
<li>Login with email instead of username again possible.</li>
<li>Adding categories at ticketing (Error: Topic).</li>
</ul></li>
<li>API:
<ul>
<li>User List function does not produce an exception at the debug mode anymore.</li>
</ul></li>
<li>CMS:
<ul>
<li>Newlines at the contact form will be replaced with HTML to keep the formatting.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>The VAC mode is correctly displayed at the userpanel.</li>
<li>Server can be restartet without VAC.</li>
<li>Gameservernames with non ASCII names can be handeled.</li>
<li>Gameservernames will be coverted to ISO-8859-1 only for display at the console while doing the statuscheck.</li>
<li>The checktime will be displayed correctly if customer breaks server rules.</li>
<li>Map named at the restart planer is correctly used at a planned restart.</li>
</ul></li>
<li>User:
<ul>
<li>On user remove with TS3 servers class gets loaded properly.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Last TS3 update broke sending virtualserver_ip, so removed from query.</li>
</ul></li>
</ul>
')");

    $insert_easywi_version->execute();
    $response->add('Action: insert_easywi_version done: ');
    $insert_easywi_version->closecursor();

    $query=$sql->prepare("ALTER TABLE `resellerimages` ADD COLUMN `active` enum('Y','N') NOT NULL DEFAULT 'Y' AFTER `id`, ADD COLUMN `pxelinux` text NULL AFTER `bitversion`");
    $query->execute();

    $query=$sql->prepare("INSERT INTO `resellerimages` (`distro`, `description`, `bitversion`, `pxelinux`) VALUES
('other', 'Rescue 64bit', 64, 'DISPLAY boot.txt\r\nDEFAULT rescue\r\nTIMEOUT 10\r\n\r\nLABEL default\r\n        kernel /rescue/vmlinuz-rescue\r\n        append initrd=/rescue/initram.igz setkmap=de dodhcp rootpass=%rescuepass% scandelay=5 boothttp=http://1.1.1.1/rescue/64/sysrcd.dat'),
('other', 'Rescue 32bit', 32, 'DISPLAY boot.txt\r\nDEFAULT rescue\r\nTIMEOUT 10\r\n\r\nLABEL default\r\n        kernel /rescue/vmlinuz-rescue\r\n        append initrd=/rescue/initram.igz setkmap=de dodhcp rootpass=%rescuepass% scandelay=5 boothttp=http://1.1.1.1/rescue/32/sysrcd.dat')");
    $query->execute();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}