<?php
/**
 * File: update_340-360.php.
 * Author: Ulrich Block
 * Date: 09.05.13
 * Time: 18:37
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
('3.60','<div align=\"right\">21.07.2013</div>
<b>Änderungen:</b><br/>
<ul>
<li>Generell:
<ul>
<li>Geändert: Die Hinzufügen Links werden ausgeblendet, wenn das Lizenzmaximum erreicht wurde.</li>
<li>Geändert: Ticketsystem komplett überarbeitet und verbessert.</li>
<li>Hinzugefügt: Emails können mit PHP Mail und SMTP gesendet, oder ausgestellt werden.</li>
<li>Hinzugefügt: Überprüfung der Cronjobs und Warnung im Adminpanel.</li>
<li>Hinzugefügt: Rückmeldung beim Login, welcher genaue Fehler vorliegt, an Stelle von Lizenz abgelaufen in jedem Fall.</li>
<li>Hinzugefügt: CSFR Schutz mittels Token bei allen Formularen.</li>
</ul></li>
<li>API:
<ul>
<li>Multiple Berechtigungsgruppen können einem User bei der Benutzer API hinzugefügt werden.</li>
</ul></li>
<li>Benutzer:
<ul>
<li>Hinzugefügt: Neue Berechtigung hinzugefügt, die das Editieren von Passwörtern als Admin limitiert.</li>
<li>Hinzugefügt: Multiple Berechtigungsgruppen können einem User hinzugefügt werden.</li>
<li>Geändert: Löschen und (in)active setzen wird in eine Warteschlange geschrieben und mit jobs.php via cronjob ausgeführt.</li>
<li>Entfernt: Pagination in der Gruppenübersicht.</li>
<li>Hinzugefügt: Neue Seiten mit denen jeder Benutzer seine Details und Passwort im Panel umsetzen kann.</li>
<li>Hinzugefügt: Parameter coolDown:N zur statuscheck welcher das Skript N Nanosekunden zwischen den TS3 serverqueries schlafen lässt.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Hinzugefügt: Eine Mapgroup kann als Default beim Template vorgegeben werden.</li>
<li>Hinzugefügt: System User wird beim Gameserver Reinstall neu angelegt.</li>
</ul></li>
<li>MYSQL:
<ul>
<li>Entfernt: Pagination im Userpanel.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Geändert: Berechtigungen werden nach einem Reset ebenfalls resettet.</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br />
<ul>
<li>cloud.php</li>
<li>Benutzer:
<ul>
<li>Änderungen an Gruppenberechtigungen für Tickets werden wieder übernommen.</li>
</ul></li>
<li>CMS:
<ul>
<li>Zusätzliche Überprüfungen im Code der Settings von Leihservern um 500er Fehler zu verhindern.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>SteamCMD Updates werden korrekt gestartet, wenn das Spiel Template mit Vendor+Sync und der Root mit Vendor Update definiert ist.</li>
<li>Gameserver können nicht mehr angelegt werden, wenn das Lizenzmaximum erschöpft ist.</li>
<li>VAC kann ausgeschaltet werden wenn EAC global deaktiviert ist.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Voiceserver können nicht mehr angelegt werden, wenn das Lizenzmaximum erschöpft ist.</li>
</ul></li>
</ul>
<br/><br/>
<b>Bearbeitete Template Dateien:</b><br />
Alle Templates wurden geändert und CSFR Token hinzugefügt.
<br/><br/>
<b>Bearbeitete XML Sprachdateien:</b><br />
<ul>
<li>general.xml</li>
<li>gserver.xml</li>
<li>settings.xml</li>
<li>tickets.xml</li>
<li>user.xml</li>
</ul>
','<div align=\"right\">07.21.2013</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Changed: Add links will not be displayed in case the licence maximum has been reached.</li>
<li>Changed: Ticket System completely reworked and enhanced.</li>
<li>Added: Emails can be send with PHP Mail, SMTP or turned off.</li>
<li>Added: Cronjob check and warning at admin panel.</li>
<li>Added: Return at the login which explains which type of error exists instead of licence expired in any case.</li>
<li>Added: CSFR protections with tokens for all input forms.</li>
<li>Added: Parameter coolDown:N to statuscheck which will send script to sleep for N nanoseconds between TS3 serverqueries.</li>
</ul></li>
<li>API:
<ul>
<li>Added: Multiple permission groups can be assigned to one user at the user API.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Added: Mapgroup can be setup as default at template.</li>
<li>Added: System user will be readded on gameserver reinstall.</li>
</ul></li>
<li>MYSQL:
<ul>
<li>Removed: Pagination at Userpanel.</li>
</ul></li>
<li>User:
<ul>
<li>Added: New permission to allow/disallow editing user passwords.</li>
<li>Added: New pages where each user can edit his own details and password.</li>
<li>Added: Multiple permission groups can be assigned to one user.</li>
<li>Changed: Delete and (in)active is queued and actions processed by jobs.php via cronjob.</li>
<li>Removed: Pagination at groups overview.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Changed: Permissions are reconfigured after reset.</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br />
<ul>
<li>cloud.php</li>
<li>CMS:
<ul>
<li>Additional checks at lend page regarding lend settings to prevent 500 error.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>SteamCMD updates are properly started when game is set to Vendor+Sync and Root to Vendor.</li>
<li>Gameservers cannot be added if licence maximum is already reached.</li>
<li>VAC can be turned off if EAC is globaly set to inactive.</li>
</ul></li>
<li>User:
<ul>
<li>Group permission edit regarding tickets is processed.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Voiceserver cannot be added if licence maximum is already reached.</li>
</ul></li>
</ul>
<br/><br/>
<b>Bearbeitete Template Dateien:</b><br />
All template files have been altered and hidden fields for CSFR token added!
<br/><br/>
<b>Bearbeitete XML Sprachdateien:</b><br />
<ul>
<li>general.xml</li>
<li>gserver.xml</li>
<li>settings.xml</li>
<li>tickets.xml</li>
<li>user.xml</li>
</ul>
')");

    $insert_easywi_version->execute();
    $response->add('Action: insert_easywi_version done: ');
    $insert_easywi_version->closecursor();

    $add=$sql->prepare("CREATE TABLE IF NOT EXISTS `tickets_text` (`ticketID` bigint(19) unsigned DEFAULT NULL,`userID` int(10) unsigned NOT NULL,`writedate` datetime DEFAULT NULL,`message` text,`resellerID` int(10) unsigned DEFAULT '0',KEY(`ticketID`),KEY(`userID`),KEY(`resellerID`)) ENGINE=InnoDB");
    $add->execute();
    $query=$sql->prepare("ALTER TABLE `tickets` ADD COLUMN `state` enum('A','C','D','N','P','R') NULL DEFAULT 'N' AFTER `id`");
    $query->execute();
    $query=$sql->prepare("SELECT * FROM `tickets`");
    $query->execute();
    $query2=$sql->prepare("INSERT INTO `tickets_text` (`ticketID`,`userID`,`writeDate`,`message`,`resellerID`) VALUES (?,?,?,?,?)");
    $query3=$sql->prepare("UPDATE `tickets` SET `state`=? WHERE `id`=? LIMIT 1");
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $state=($row['open']=='N') ? 'C' : 'P';
        $query2->execute(array($row['id'],$row['writerid'],$row['writedate'],$row['ticket'],$row['resellerid']));
        $query3->execute(array($state,$row['id']));
    }
    $query=$sql->prepare("DELETE FROM `tickets` WHERE `mainticket` IS NOT NULL");
    $query->execute();
    $query=$sql->prepare("SELECT `id`,`usergroup`,`resellerid` FROM `userdata`");
    $query2=$sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`,`resellerID`) VALUES (?,?,?)");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $query2->execute(array($row['id'],$row['usergroup'],$row['resellerid']));
    $query=$sql->prepare("UPDATE `settings` SET `template`='twitterbootstrap' WHERE `template`='default'");
    $query->execute();
    $query=$sql->prepare("INSERT INTO `resellerimages` (`distro`, `description`, `bitversion`, `pxelinux`) VALUES
('other', 'Rescue 64bit', 64, 'DISPLAY boot.txt\r\nDEFAULT rescue\r\nTIMEOUT 10\r\n\r\nLABEL default\r\n        kernel /rescue/vmlinuz-rescue\r\n        append initrd=/rescue/initram.igz setkmap=de dodhcp rootpass=%rescuepass% scandelay=5 boothttp=http://1.1.1.1/rescue/64/sysrcd.dat'),
('other', 'Rescue 32bit', 32, 'DISPLAY boot.txt\r\nDEFAULT rescue\r\nTIMEOUT 10\r\n\r\nLABEL default\r\n        kernel /rescue/vmlinuz-rescue\r\n        append initrd=/rescue/initram.igz setkmap=de dodhcp rootpass=%rescuepass% scandelay=5 boothttp=http://1.1.1.1/rescue/32/sysrcd.dat')");
    $query->execute();
} else {
    echo "Error: this file needs to be included by the updater!<br />";
}