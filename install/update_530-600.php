<?php

/**
 * File: update_530-600.php.
 * Author: Ulrich Block
 * Date: 12.05.16
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

    if (!function_exists('workAroundForValveChaos')) {
        include(EASYWIDIR . '/stuff/methods/functions.php');
    }

    $response->add('Action: Update to new skin color template system');

    $query = $sql->prepare("SHOW COLUMNS FROM `settings` WHERE `Field`='serverID'");
    $query->execute();

    if ($query->rowCount() == 0) {
        $query = $sql->prepare("ALTER TABLE `settings` ADD `serverID` INT(10) UNSIGNED");
        $query->execute();
        $query->closecursor();
    }

    $query2 = $sql->prepare("UPDATE `servertypes` SET `appID`=?,`serverID`=`appID` WHERE `id`=? LIMIT 1");
    $query = $sql->prepare("SELECT `id`,`appID`,`shorten` FROM `servertypes` WHERE `appID` IS NOT NULL AND `serverID` IS NULL");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $query->execute(array(workAroundForValveChaos($row['appID'], $row['shorten'], true), $row['id']));
    }

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('6.0.0','<div align=\"right\">14.05.2017</div>
<b>Hinweis:</b><br/>
Nach dem Update auf jedem Root Server mit Game Servern das Migration Skriot ausf&uuml;hren:<br/>
https://github.com/easy-wi/server/blob/master/migrate.sh<br/>
<br/>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>General:
<ul>
<li>Hybrid Auth auf 2.9.5 aktualsiert</li>
<li>Support von Ubuntu 16.04</li>
<li>Russische Sprache hinzugef&uuml;gt</li>
<li>Globale Suche entfernt</li>
<li>Admins werden bei veralteter Easy-Wi Installation informiert</li>
<li>L&auml;nge der Namen von Public Keyfiles global auf 255 erh&ouml;ht</li>
<li>Versionierung &uuml;ber GitHub an Stelle von easy-wi.com</li>
</ul></li>
<li>CMS:
<ul>
<li>Download Modul kann externe Downloads verwalten</li>
<li>HTTP Only wird bei https gesetzt</li>
</ul></li>
<li>Gameserver:
<ul>
<li>ip_port Unterordner entfernt</li>
<li>Workaround -nobreakpad bei CS:GO Template</li>
<li>GameQ auf die neueste Version aktualsiert</li>
<li>Server wird vor Reinstall gestoppt</li>
<li>Project Cars Template angepasst</li>
<li>Sonderzeichen @ kann bei Addon Ordnern angeben werden</li>
<li>Backup Erstellen wird ignoriert, wenn bereits eines l&auml;uft</li>
<li>Server wird gestoppt, bevor der Umzug gestartet wird</li>
<li>Beschreibung des Servers kann verwaltet werden</li>
<li>Fast Download Anweisungen f&uuml;r UnrealEngine hinzugef&uuml;gt</li>
<li>Steam Server und Game ID nun beide konfiguriertbar</li>
</ul></li>
<li>Voice Server:
<ul>
<li>Token k&ouml;nnen beim Anlegen &uuml;bergeben werden</li>
<li>Inifile wird beim Restart angegeben</li>
<li>Beschreibung des Servers kann verwaltet werden
</ul></li>
<li>Web:
<ul>
<li>PHP-FPM Support hinzugef&uuml;gt</li>
<li>Default Redirects in den standard Templates</li>
</ul></li>
<li>MySQL:
<ul>
<li>MySQL Server 5.7 Unterst&uuml;tzung hinzugef&uuml;gt</li>
<li>MySQL Master Beschreibung kann verwaltet werden</li>
</ul></li>
<li>Installer:
<ul>
<li>Developer und Stable Release Train ausw&auml;hlbar</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>General:
<ul>
<li>Glyphicons definiert, aber nicht verf&uuml;gbar</li>
<li>Updater von 5.22 auf 5.30 fehlerhaft bei mehrfachem Aufruf</li>
<li>Variable feedArray in der Admin Home Ansicht undefined in manchen F&auml;llen</li>
<li>Custom Modules werden in der User Ansicht nicht korrekt ins Men&uuml; eingebunden</li>
<li>Zu Kurzes Passwort des API Users kann API unbrauchbar machen</li>
<li>Vertreter wird nicht korrekt gegr&uuml;&szlig;t</li>
<li>Admin kann auf User zugreifen, f&uuml;r die er keine Berechtigung hat</li>
<li>SQL Fehler beim Update von mit md5 Hash importierter User</li>
<li>Redirect nach Logout auf System ohne Domain funktioniert nicht</li>
</ul></li>
<li>CMS:
<ul>
<li>Summernote Editor funktioniert nicht bei Seiten und News</li>
<li>News bearbeiten und Keyword entfernen zur gleichen Zeit geht nicht</li>
<li>User Aktivation Link</li>
<li>Leih Server funktionieren nicht bei deaktierten SEO URLs</li>
</ul></li>
<li>Game Server:
<ul>
<li>FTP Connect beim Serverlog fehlerhaft dargestellt</li>
<li>Sperren von CVARs in mehreren Datein funktioniert nicht</li>
<li>Lange INI Dateien werden abgeschnitten</li>
<li>API FTP Passwort &Auml;nderung wird nicht auf den Root &uuml;bertragen</li>
<li>Italienische &Uuml;bersetzung bei Monsta FTP</li>
<li>Typo bei Game Server Update Mail</li>
<li>Typo bei Master Server Menu Eintrag</li>
<li>Shorten mit Nummer am Anfang crashen API</li>
<li>Upload von leeren Dateien via MonstaFTP nicht m&ouml;glich</li>
<li>Kopie der Configs beim Protection Mode funktioniert nicht</li>
</ul></li>
<li>Voice:
<ul>
<li>Erstellen via API schl&auml;gt fehl</li>
<li>SSH Keys beim Master funktionieren nicht</li>
<li>SSH Keys mit Passwort beim TSDNS Master funktionieren nicht</li>
<li>Reset von notified beim Statuscheck schl&auml;gt fehl</li>
<li>API TSDNS L&ouml;schen funktioniert nicht</li>
<li>Masterid beim Reimport nicht gesetzt</li>
<li>Typo bei Voice Master veraltet Mail</li>
<li>Suhosin check beim Import auf Systemen ohne, l&ouml;st Fehler aus</li>
<li>Fehlerhafte Fehlermeldungen beim Connection Check</li>
<li>Versions Lookup bei nicht existierendem Mirror entfernt</li>
</ul></li>
<li>Web:
<ul>
<li>SSH Keys beim Master funktionieren nicht</li>
<li>Angabe von URLs im Vhost Template nicht m&ouml;glich</li>
</ul></li>
<li>User:
<ul>
<li>API User Liste funktioniert im Debug Mode nicht</li>
</ul></li>
<li>Installer:
<ul>
<li>Single Quotes bei Eintr&auml;gen f&uuml;r die config.php l&ouml;sen Syntax Fehler aus</li>
<li>User Passwort Validation nicht vorhanden</li>
<li>Valides Passwortformat wird abgelehnt</li>
<li>Undefined Variable ui</li>
</ul></li>
</ul>','<div align=\"right\">05.14.2017</div>
<b>Advice:</b><br/>
The migration script has to be executed on each root server with game servers after the update:<br/>
https://github.com/easy-wi/server/blob/master/migrate.sh<br/>
<br/>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Hybrid Auth updated to 2.9.5</li>
<li>Ubuntu 16.04 support</li>
<li>added russian language</li>
<li>Globale search removed</li>
<li>Admins will be notified in case Easy-Wi installation becomes outdated</li>
<li>Length for names for public keyfiles globally increased to 255</li>
<li>Versioning via GitHub instead of easy-wi.com</li>
</ul></li>
<li>CMS:
<ul>
<li>Download modul can maintain external downloads</li>
<li>HTTP Only is set in case of https</li>
</ul></li>
<li>Game Server:
<ul>
<li>ip_port sub folder removed</li>
<li>Workaround -nobreakpad added at CS:GO template</li>
<li>GameQ updated to latest dev version</li>
<li>Server is stopped before reinstall</li>
<li>Project Cars template updated</li>
<li>Special character @ can be used for addon folders</li>
<li>Backup create command will be ignored in case one is running</li>
<li>Server is stopped before migration is done</li>
<li>Server description can be maintained</li>
<li>Fast download instructions added for UnrealEngine</li>
<li>Steam Server and Game ID can be maintained in parallel</li>
</ul></li>
<li>Voice Server:
<ul>
<li>Token can be used while adding via API</li>
<li>Inifile is specified on restart</li>
<li>Server description can be maintained</li>
</ul></li>
<li>Web:
<ul>
<li>Added PHP-FPM support</li>
<li>Default redirects added to standard templates</li>
</ul></li>
<li>MySQL Server:
<ul>
<li>Added MySQL 5.7 support</li>
<li>MySQL master description can be maintained</li>
</ul></li>
<li>Installer:
<ul>
<li>Developer and stable release train configurable</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>General:
<ul>
<li>Glyphicons used, but not available</li>
<li>Updater from 5.22 to 5.30 error prone if executed multiple times</li>
<li>Variable feedArray undefined in some cases at admin home</li>
<li>Custom modules not properly listed at user menu</li>
<li>To short password leads to failing API</li>
<li>Substitute is not greeted properly</li>
<li>Admin can access users without having the proper permission</li>
<li>SQL error in case imported users are updated and have been imported with md5 password</li>
<li>Redirect after logout on systems without domain not working</li>
</ul></li>
<li>CMS:
<ul>
<li>Summernote editor broken at sites and news</li>
<li>News edit and removal of keywords not possible in parallel</li>
<li>User activation link</li>
<li>Lend servers do not work in case SEO URLs are deactivated</li>
</ul></li>
<li>Game Server:
<ul>
<li>FTP connect at serverlog incorrectly displayed</li>
<li>Securing CVARs in multiple files at once not working</li>
<li>Long INI files are cut off</li>
<li>API FTP password change not reflected to root</li>
<li>Italian translation at Monsta FTP</li>
<li>Typo at game server update mail</li>
<li>Typo at master server menu entry</li>
<li>Shorten with a leading number will crash the API</li>
<li>Upload of empty files at MonstaFTP not possible</li>
<li>Copy of configs at protection mode not working</li>
</ul></li>
<li>Voice:
<ul>
<li>Creation via API fails</li>
<li>SSH Keys at for master do not work</li>
<li>SSH Keys with password fail for TSDNS master</li>
<li>Reset of notified at statuscheck failing</li>
<li>API TSDNS delete does not work</li>
<li>Masterid at reimport not set</li>
<li>Typo at voice master outdated mail</li>
<li>Suhosin check during import will break importer on systems without Suhosin</li>
<li>Incorrect error message at connection check</li>
<li>Versions lookup at removed mirror removed</li>
</ul></li>
<li>Web:
<ul>
<li>SSH Keys at for master do not work</li>
<li>Usage of fixed urls at vhost template not possible</li>
</ul></li>
<li>User:
<ul>
<li>API user list not working with active debig mode</li>
</ul></li>
<li>Installer:
<ul>
<li>Single quote at values for config.php result in syntax error</li>
<li>User passwort validation missing</li>
<li>Valid password format might be rejected</li>
<li>Undefined variable ui</li>
</ul></li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}