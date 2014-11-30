<?php

/**
 * File: update_430-440.php.
 * Author: Ulrich Block
 * Date: 02.02.14
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
('4.40','<div align=\"right\">10.05.2014</div>
<b>Änderungen:</b><br/>
<ul>
<li>Generell:
<ul>
<li>Wiki mit Handbuch Links ersetzt.</li>
<li>Globale Belegungsstatistiken werden gelogt und dem Admin dargestellt.</li>
<li>Easy-Wi Facebook Seite in den Headern verlinkt.</li>
<li>Überflüssige Fallbacks auf user_language entfernt.</li>
<li>Nur relevante Steam News werden im User Dashboard angezeigt.</li>
<li>Liste verfügbarer Dateien für Custom Module wird angezeigt.</li>
<li>Vertreter können ihre eigenen Daten verwalten.</li>
<li>Neue PHP Ordnerstruktur.</li>
<li>PNGs entfernt.</li>
<li>Social Auth hinzugefügt.</li>
</ul></li>
<li>CMS:
<ul>
<li>User Prefix auf Nein erlaubt die freie Nickname Wahl beim Registrieren.</li>
<li>Der HTML Title wird je nach Seite angepasst.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Mehrere Unterordner sind nun bei Images erlaubt.</li>
<li>Restart Jobs, werden angelegt, wenn ein Steam Update für as Spiel erfolgt ist.</li>
<li>Masterserver Ajax aus der settings.php entfernt.</li>
<li>GameQ aktualisiert.</li>
<li>Garrysmod Template hinzugefügt.</li>
<li>Geringere Ram Verbrauch bei der Darstellung von Server Logs.</li>
<li>Workaround in der statuscheck.php für Spiele mit unterschiedlichen Query Port.</li>
<li>Nach dem Editieren Redirect zurück zum Restartplaner.</li>
<li>Workaround für Servercolor/Branding.</li>
<li>SQL Support für EAC.</li>
<li>hldsupdatetool entfernt.</li>
</ul></li>
<li>MySQL:
<ul>
<li>Hosttabellen Verwaltung kann beim User deaktiviert werden.</li>
<li>Reinstall Funktion hinzugefügt.</li>
<li>Datenbankgröße wird erhiben und dem User dargestellt.</li>
<li>Layout Struktur im Userpanel den anderen Übersichten angeglichen.</li>
</ul></li>
<li>Root:
<ul>
<li>Subnet Verwaltung verbessert.</li>
<li>Rootserver IP system überarbeitet.</li>
<li>Vlan Support hinzugefügt.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Bannlisten Funktion im Userpanel.</li>
<li>Slot- und Traffikverbrauch bei TS3 Servern wird im Userbereich angezeigt.</li>
<li>Zusätzlich zu Slots, wird auch der Traffik gelogt.</li>
</ul></li>
<li>Webspace:
<ul>
<li>Webspace/FastDL Modul hinzugefügt.</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>API: Beim Anlegen von Usersn wird kein Passwort gespeichert.</li>
<li>API: Legacy Verhalten wieder herstellen.</li>
<li>CMS: Canurl war bei statischen Seiten nicht gesetzt.</li>
<li>CMS: EasyWi CMS Verlinkung teilweise fehlerhaft.</li>
<li>Gameserver: Popup beim Anlegen einer neuen Datei im WebFTP.</li>
<li>Gameserver: Eigener Gameserver Startbefehl.</li>
<li>Gameserver: Minecraft Migration funktioniert nicht.</li>
<li>Gameserver: Gameserver Settings Mapgroup Selektion.</li>
<li>Gameserver: Updates starten nicht bei Minute 0.</li>
<li>Gameserver: Der Restart Kalender funktioniert nur für Montag.</li>
<li>Gameserver: downloadPath nicht mit dem Gmeserver Image exportieren.</li>
<li>Gameserver: Protection Mode kopiert keine Datein vom ungeschützten Server.</li>
<li>Gameserver: Online Servers ohne Namen werden als Offline angezeigt.</li>
<li>Gameserver: Gameserver wird nicht von der job.php gestoppt.</li>
<li>Generell: Workaround für Admins ohne eingestellte Zeitzone.</li>
<li>Generell: Nach frischer Installation werden E-Mails ohne Text gesendet.</li>
<li>Generell: Redirect enthält doppelte Slashes.</li>
<li>Voice: TS3 Slots können nach einem Reset bearbeitet werden.</li>
<li>Voice: Fehlender Include beim Voice Server löschen.</li>
</ul>
','<div align=\"right\">05.10.2014</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Replaced Wiki with manual links</li>
<li>Log global usage statistics and display at admin dashboard.</li>
<li>Easy-wi Facebook page added to headers.</li>
<li>Additional fallbacks for user_language removed.</li>
<li>Display only relevant Steam news at user dashboard.</li>
<li>Display list of available custom modules.</li>
<li>Substitutes can maintain their own data.</li>
<li>New PHP folderstructure.</li>
<li>PNG usage entfernt.</li>
<li>Social Auth added.</li>
</ul></li>
<li>CMS:
<ul>
<li>user prefix is NO allows nickname pick at register.</li>
<li>Display html title depending on the page.</li>
</ul>
<li>Gameserver:
<ul>
<li>Allow multiple subfolders for game images.</li>
<li>Add restart jobs, when steamgame update.</li>
<li>Moved masterserver ajax out of settings.php</li>
<li>GameQ updated.</li>
<li>Add garrysmod template.</li>
<li>Reduce memory usage with serverlogs.</li>
<li>Workaround at statuscheck.php for games with different query port.</li>
<li>Redirect back to restartplaner after edit.</li>
<li>Workaround for servercolor/branding</li>
<li>SQL support for EAC.</li>
<li>Removed hldsupdatetool.</li>
</ul></li>
<li>MySQL:
<ul>
<li>Hosttable management can be deactivated for users.</li>
<li>Added reinstall feature.</li>
<li>Collect and display DB size at MySQL Module.</li>
<li>Align layout for MySQL to GS at userpanel.</li>
</ul></li>
<li>Rootserver:
<ul>
<li>Enhanced subnet management.</li>
<li>Reworked rootserver IP system.</li>
<li>Added Vlan Support.</li>
</ul></li>
<li>User:
<ul>
<li>Add reseller fix job</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Add banlist for users to TS3.</li>
<li>Display slot and traffic usage for TS3 at userpanel.</li>
<li>Log traffic in addition to slots for TS3 server.</li>
</ul></li>
<li>Webspace:
<ul>
<li>Webspace/FastDL Modul added.</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>API: Creating users no password will be saved in the database.</li>
<li>API: Restore legacy API behaviour.</li>
<li>CMS: Canurl not set for static pages.</li>
<li>Gameserver: Fix Popup for create NEW FILE WebFTP.</li>
<li>Gameserver: Own gameserver start command.</li>
<li>Gameserver: Minecraft migration does not work.</li>
<li>Gameserver: Gameserver Settings Mapgroup selection.</li>
<li>Gameserver: Updates not starting at minute 0.</li>
<li>Gameserver: Restart calendar works for monday only.</li>
<li>Gameserver: Do not export downloadPath with gameserver image.</li>
<li>Gameserver: Protection mode not copying over files from non-protected server.</li>
<li>Gameserver: Online servers with empty server names show up as offline in Webinterface.</li>
<li>Gameserver: Gameserver not stopped by job.php on delete.</li>
<li>General: Workaround for admins without timezone set.</li>
<li>General: After fresh install mails send without text.</li>
<li>General: Redirect to URIs with double slashes.</li>
<li>General: EasyWi CMS links partially incorrect.</li>
<li>Voice: Missing include while voice server delete.</li>
<li>Voice: TS3 slots can be edited after reset.</li>
</ul>
')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

    if (!isset($displayToUser)) {
        $displayToUser = '';
    }

    $response->add('Adding tables if needed.');
    include(EASYWIDIR . '/stuff/methods/tables_add.php');

    $insert = $sql->prepare("INSERT INTO `easywi_statistics_current` (`userID`) VALUES (?) ON DUPLICATE KEY UPDATE `userID`=`userID`");
    $insert->execute(array(0));

    $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `accounttype`!='a'");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $insert->execute(array($row['id']));
    }

    $query = $sql->prepare("DROP TABLE IF EXISTS `rootsSubnets`");
    $query = $sql->prepare("DROP TABLE IF EXISTS `rootsIP4`");

    $query = $sql->prepare("CREATE TABLE IF NOT EXISTS `rootsSubnets` (
  `subnetID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dhcpServer` int(10) unsigned NOT NULL,
  `active` enum('Y','N') DEFAULT 'Y',
  `subnet` varchar(15) DEFAULT NULL,
  `netmask` varchar(15) DEFAULT NULL,
  `subnetOptions` text,
  `vlan` enum('Y','N') DEFAULT 'N',
  `vlanName` varchar(255),
  PRIMARY KEY (`subnetID`)
) ENGINE=InnoDB");
    $query->execute();

    $query = $sql->prepare("CREATE TABLE IF NOT EXISTS `rootsIP4` (
  `subnetID` int(10) unsigned,
  `ip` varchar(15) DEFAULT NULL,
  `ownerID` int(10) unsigned DEFAULT 0,
  `resellerID` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`subnetID`,`ip`),KEY(`ownerID`),KEY(`resellerID`)
) ENGINE=InnoDB");
    $query->execute();

    $query = $sql->prepare("SELECT * FROM `rootsDHCP`");
    $query2 = $sql->prepare("SELECT 1 FROM `rootsSubnets` WHERE `subnet`=? LIMIT 1");
    $query3 = $sql->prepare("INSERT INTO `rootsSubnets` (`active`,`subnet`,`subnetOptions`,`netmask`,`vlan`,`vlanName`) VALUES (?,?,?,?,'N','')");
    $query4 = $sql->prepare("INSERT INTO `rootsIP4` (`subnetID`,`ip`) VALUES (?,?) ON DUPLICATE KEY UPDATE `ip`=VALUES(`ip`)");

    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        if (isset($row['subnetOptions'])) {
            foreach (explode("\r\n", $row['ips']) as $exip) {

                $ex = explode('.', $exip);

                if (isset($ex[2])) {
                    $query2->execute(array($ex[0] . '.' . $ex[1] . '.' . $ex[2] . '.0'));
                    if ($query2->rowCount() == 0) {
                        $query3->execute(array($row['active'], $ex[0] . '.' . $ex[1] . '.' . $ex[2] . '.0', str_replace("option subnet-mask %subnet-mask%;\r\n", '', $row['subnetOptions']), $row['netmask']));
                        $lastID = $sql->lastInsertId();
                        for ($lastTriple = 2; $lastTriple < 255; $lastTriple++) {
                            $query4->execute(array($lastID, $ex[0] . '.' . $ex[1] . '.' . $ex[2] . '.' . $lastTriple));
                        }
                    }
                }
            }
        }
    }

    $query = $sql->prepare("SELECT `ips`,`resellerid`,`resellersid` FROM `resellerdata`");
    $query2 = $sql->prepare("UPDATE `rootsIP4` SET `ownerID`=?,`resellerID`=? WHERE `ip`=? LIMIT 1");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        foreach (ipstoarray($row['ips']) as $usedip) {
            $query2->execute(array($row['resellerid'], $row['resellersid'], $usedip));
        }
    }

    $dirSource = EASYWIDIR . '/stuff/';
    $dirTarget = EASYWIDIR . '/stuff/custom_modules/';

    if (!is_dir($dirTarget)) {
        @mkdir($dirTarget);
    }

    if (is_dir($dirTarget)) {

        $query = $sql->prepare("SELECT `file` FROM `modules`");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            if (is_file($dirSource . $row['file'])) {
                rename($dirSource . $row['file'], $dirTarget . $row['file']);
            }
        }
    }

    foreach (array('admin', 'api', 'cms', 'custom_modules', 'jobs', 'methods', 'user') as $dir) {
        if (is_dir(EASYWIDIR . '/stuff/' . $dir . '/')) {
            foreach (scandir(EASYWIDIR . '/stuff/' . $dir . '/') as $row) {
                if (substr($row, -4) == '.php') {
                    @unlink($dirSource . $row);
                }
            }
        }
    }

    $customDirs = array();

    foreach (scandir(EASYWIDIR . '/template/') as $row) {
        if (strpos($row, '.') === false) {
            $customDirs[] = $row;
            foreach (array('admin', 'ajax', 'cms', 'custom_modules', 'user') as $dir) {
                @mkdir(EASYWIDIR . '/template/' . $row);
            }
        }
    }

    foreach (array('admin', 'ajax', 'cms', 'custom_modules', 'user') as $dir) {

        if (is_dir(EASYWIDIR . '/template/default/' . $dir . '/')) {

            foreach (scandir(EASYWIDIR . '/template/default/' . $dir . '/') as $row) {

                if (substr($row, -4) == '.tpl') {

                    if (is_file(EASYWIDIR . '/template/default/' . $row)) {
                        @unlink(EASYWIDIR . '/template/default/' . $row);
                    }

                    foreach ($customDirs as $custom) {
                        if (is_dir(EASYWIDIR . '/template/' . $custom) and is_file(EASYWIDIR . '/template/' . $custom . '/' . $row)) {
                            @rename(EASYWIDIR . '/template/' . $custom . '/' . $row, EASYWIDIR . '/template/' . $custom . '/' . $dir . '/' . $row);
                        }
                    }
                }
            }
        }
    }

    $query = $sql->prepare("UPDATE `servertypes` SET `steamgame`='S' WHERE `steamgame`='Y'");
    $query->execute();

    $query = $sql->prepare("ALTER TABLE `servertypes` ADD COLUMN `gamebinaryWin` varchar(255) NOT NULL AFTER `gamebinary`");
    $query->execute();

    $query = $sql->prepare("UPDATE `servertypes` SET `gamebinaryWin`='hlds.exe',`os`='B' WHERE `gamebinary`='hlds_run'");
    $query->execute();

    $query = $sql->prepare("UPDATE `servertypes` SET `gamebinaryWin`='srcds.exe',`os`='B' WHERE `gamebinary`='srcds_run'");
    $query->execute();

    $query = $sql->prepare("UPDATE `servertypes` SET `gameq`='mta' WHERE `gameq`='Mta'");
    $query->execute();

    $query = $sql->prepare("DROP TABLE IF EXISTS `voice_server_stats_hours`");
    $query->execute();

    $response->add('Repairing tables if needed.');
    include(EASYWIDIR . '/stuff/methods/tables_repair.php');

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}