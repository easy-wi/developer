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

    if (!isset($displayToUser)) {
        $displayToUser = '';
    }

    $response->add('Adding tables if needed.');
    include(EASYWIDIR . '/stuff/methods/tables_add.php');

    $query = $sql->prepare("DROP TABLE IF EXISTS `voice_stats_settings`");
    $query->execute();

    $query = $sql->prepare("DELETE FROM `easywi_statistics`");
    $query->execute();

    // move email related stuff from global settings into own table
    $query2 = $sql->prepare("INSERT INTO `settings_email` (`reseller_id`,`email_setting_name`,`email_setting_value`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `email_setting_value`=VALUES(`email_setting_value`)");
    $query = $sql->prepare("SELECT *,AES_DECRYPT(`email_settings_password`,?) AS `decryptedpassword` FROM `settings`");
    $query->execute(array($aeskey));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $query2->execute(array($row['resellerid'], 'emailbackup', @gzuncompress($row['emailbackup'])));
        $query2->execute(array($row['resellerid'], 'emailbackuprestore', @gzuncompress($row['emailbackuprestore'])));
        $query2->execute(array($row['resellerid'], 'emaildown', @gzuncompress($row['emaildown'])));
        $query2->execute(array($row['resellerid'], 'emaildownrestart', @gzuncompress($row['emaildownrestart'])));
        $query2->execute(array($row['resellerid'], 'emailgserverupdate', @gzuncompress($row['emailgserverupdate'])));
        $query2->execute(array($row['resellerid'], 'emailpwrecovery', @gzuncompress($row['emailpwrecovery'])));
        $query2->execute(array($row['resellerid'], 'emailsecuritybreach', @gzuncompress($row['emailsecuritybreach'])));
        $query2->execute(array($row['resellerid'], 'emailnewticket', @gzuncompress($row['emailnewticket'])));
        $query2->execute(array($row['resellerid'], 'emailuseradd', @gzuncompress($row['emailuseradd'])));
        $query2->execute(array($row['resellerid'], 'emailvinstall', @gzuncompress($row['emailvinstall'])));
        $query2->execute(array($row['resellerid'], 'emailvrescue', @gzuncompress($row['emailvrescue'])));
        $query2->execute(array($row['resellerid'], 'emailregister', @gzuncompress($row['emailregister'])));
        $query2->execute(array($row['resellerid'], 'email', $row['email']));
        $query2->execute(array($row['resellerid'], 'emailregards', $row['emailregards']));
        $query2->execute(array($row['resellerid'], 'emailfooter', $row['emailfooter']));
        $query2->execute(array($row['resellerid'], 'email_settings_host', $row['email_settings_host']));
        $query2->execute(array($row['resellerid'], 'email_settings_password', $row['decryptedpassword']));
        $query2->execute(array($row['resellerid'], 'email_settings_port', $row['email_settings_port']));
        $query2->execute(array($row['resellerid'], 'email_settings_ssl', $row['email_settings_ssl']));
        $query2->execute(array($row['resellerid'], 'email_settings_type', $row['email_settings_type']));
        $query2->execute(array($row['resellerid'], 'email_settings_user', $row['email_settings_user']));
    }

    // Try catch as some admins upgrade vom DEV to stable
    try {
        $query = $sql->prepare("SELECT `webVhostID`,`userID`,`resellerID`,`dns`,`ownVhost`,`vhostTemplate` FROM `webVhost`");
        $query->execute();
        $query2 = $sql->prepare("INSERT INTO `webVhostDomain` (`webVhostID`,`userID`,`resellerID`,`domain`,`path`,`ownVhost`,`vhostTemplate`) VALUES (?,?,?,?,'',?,?)");
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // Try catch as some admins might have maintained domains not be so unique
            try {
                $query2->execute(array($row['webVhostID'], $row['userID'], $row['resellerID'], $row['dns'], $row['ownVhost'], $row['vhostTemplate']));
                $response->add('Migrated ' . $row['dns'] . ' to new table.');
            } catch(PDOException $error) {
                $response->add($error->getMessage());
            }
        }
    } catch (PDOException $error) {
        $response->add($error->getMessage());
    }

    $query = $sql->prepare("SELECT `id` FROM `modules` WHERE `get`='ro' LIMIT 1");
    $query->execute();
    $rootModuleId = (int) $query->fetchColumn();

    if ($rootModuleId > 0) {
        $query = $sql->prepare("UPDATE `modules` SET `active`='N' WHERE `id`=? LIMIT 1");
        $query->execute(array($rootModuleId));
    } else {
        $query = $sql->prepare("INSERT INTO `modules` (`get`,`sub`,`file`,`active`,`type`) VALUES ('ro','ro','','N','C')");
        $query->execute();
    }

    $response->add('Repairing tables if needed.');
    include(EASYWIDIR . '/stuff/methods/tables_repair.php');

    $query = $sql->prepare("UPDATE `servertypes` SET `useQueryPort`=2 WHERE `gameq` IN ('armedassault2', 'armedassault2oa', 'armedassault3', 'bf2', 'cube2', 'mta', 'ut', 'ut2004', 'ut3')");
    $query->execute();

    $query = $sql->prepare("UPDATE `servertypes` SET `cmd`='./%binary% -n' WHERE `shorten`='mtasa' AND `cmd`='./%binary%'");
    $query->execute();

    // Add new games if not existing
    include(EASYWIDIR . '/stuff/methods/gameslist.php');

    $addGames = array('nmrih', 'projectcars');

    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=0 LIMIT 1");
    $query2 = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`gamebinary`,`gamebinaryWin`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`gameq`,`gamemod`,`gamemod2`,`configs`,`configedit`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`useQueryPort`,`mapGroup`,`protected`,`protectedSaveCFGs`,`ramLimited`,`os`,`resellerid`) VALUES (:steamgame,:appID,:updates,:shorten,:description,:gamebinary,:gamebinaryWin,:binarydir,:modfolder,:fps,:slots,:map,:cmd,:modcmds,:tic,:gameq,:gamemod,:gamemod2,:configs,:configedit,:portStep,:portMax,:portOne,:portTwo,:portThree,:portFour,:portFive,:useQueryPort,:mapGroup,:protected,:protectedSaveCFGs,:ramLimited,:os,:resellerid)");

    foreach ($gameImages as $image) {

        if (in_array($image[':shorten'], $addGames) and count($image) == 33) {

            $image[':resellerid'] = 0;

            $query->execute(array($image[':shorten']));
            $imageExists = (int) $query->fetchColumn();

            if ($imageExists == 0) {

                $query2->execute($image);

                if ($query2->rowCount() > 0) {
                    $response->add('Added : ' . $image[':description']);
                }
            }
        }
    }

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('5.00','<div align=\"right\">25.05.2014</div>
<b>Änderungen:</b><br/>
<ul>
<li>API:
<ul>
<li>TSNDS API hinzugefügt</li>
<li>Aussagekräftigere Fehlermeldung bei Zugang verweigert</li>
<li>Rückgabe der gesendeten Operation für besseres Debugging</li>
<li>Daten werden bei der Edit Operation vollständig zurück gegeben</li>
<li>IP wird beim Add/Edit von Game und Voiceservern zurück gegeben</li>
<li>Gameswitch Spiele können bei Edit Operation hinzugefügt und gelöscht werden</li>
<li>FTP User wird bei Gameservern zurück gegeben</li>
<li>Das initiale Passwort kann bei Gameservern gesetzt werden</li>
<li>Der Corecount wird bei Gameservern zurück gegeben</li>
<li>Liste aller installierter Game Typen/Master Apps</li>
<li>Liste aller Masterserver mit optionalen Limit</li>
<li>Multiple Master IDs können in allen Add Operationen verwendet werden</li>
<li>Beschreibung wird bei Master Listen zurück gegeben</li>
<li>Webspace Master Liste kann angezeigt werden</li>
<li>MySQL Master Liste kann angezeigt werden</li>
<li>Generierter Benutzername wird zurück gegeben, wenn keiner gesendet wurde</li>
<li>User Liste kann angezeigt werden</li>
<li>Neue Methode clean User externalID</li>
</ul></li>
<li>CMS:
<ul>
<li>hreflang Unterstützung</li>
<li>index.php wird nach Home im Seo Mode geroutet</li>
<li>WYSIWYG Editor Summernote beim Seiten und News Management</li>
</ul></li>
<li>Feeds:
<ul>
<li>Externe News des Feeds werden ebenfalls eingelesen</li>
<li>Bilder werden aus dem Feed entfernt</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Quota Support</li>
<li>Multiple Festplatten werden unterstützt</li>
<li>SteamCMD Login kann pro Template/Image gepflegt werden</li>
<li>control.sh durch PHP Klasse ersetzt</li>
<li>Konfiguration der control.sh durch App Server Konfiguration ersetzt</li>
<li>Protected Linux System User wird nur angelegt, wenn der Modus generell für den Gameserver verfügbar ist</li>
<li>SteamCMD Updates werden nacheinander ausgeführt</li>
<li>Option [no_padding] für optionale Startbefehle bei Addons</li>
<li>Query Port kann im Template definiert werden</li>
<li>Neues Template No More Room in Hell</li>
<li>Neues Template Project Cars</li>
<li>FTP Server ohne Chroot werden unterstützt</li>
<li>Support des Verleihs von Minecraft, Samp und Teeworlds </li>
<li>Statuscheck.php Timeout erhöht</li>
<li>Restart durch einen Cronjob wird mit der IP 127.0.0.1 gelogt</li>
<li>\"@\" und \".\" im FTP Usernamen beim FastDL zulässig</li>
</ul></li>
<li>Generell:
<ul>
<li>Neues Admin/User Template in 6 Farben</li>
<li>Große Teile des Codes refactored</li>
<li>Angelegeprozesse nur noch in einem Schritt</li>
<li>SSH IPs können ausschließlich für den Connect verwendet werden (DMZ)</li>
<li>Icon und Text des Headers in den Einstellungen konfigurierbar</li>
<li>Statusseite für Cronjobs und PHP Extensions</li>
<li>Third party CSS, JS und Fonts werden mitgeliefert</li>
<li>Query Resultate werden mit while an Stelle von foreach geloopt umd Ram Verbrauch zu senken</li>
<li>Modul Konzept aktualisiert und robuster gestaltet</li>
<li>Unterstützzung von CURRENT_TIMESTAMP bei der Tabellen Reparieren Funktion</li>
<li>Benutzer kann Info Texte deaktivieren</li>
<li>Charakter \"-\" kann bei Passwörtern benutzt werden</li>
<li>Default externalID ist nun \"leer\"</li>
<li>Job Einträge werden geschrieben um alles zu stoppen, wenn User deaktiviert oder gelöscht wird</li>
<li>Verbesserte Fehler Meldungen bei External Auth</li>
</ul></li>
<li>MySQL:
<ul>
<li>externalID für Datenbanken verwaltbar</li>
</ul></li>
<li>Tickets:
<ul>
<li>HTML5 Validierung hinzugefügt um 404 Fehler zu vermeiden</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>externalID zu TSNDS hinzugefügt</li>
<li>Maximale TSNDS Menge am Master einstellbar</li>
<li>Beschreibung kann bei Voice Mastern gepflegt werden</li>
</ul></li>
<li>Webspace:
<ul>
<li>Domains können Webspace zugeordnet werden</li>
<li>Frei definierbare optionale php.ini Konfiguration</li>
</ul></li>
<li>Third Party:
<ul>
<li>DataTables hinzugefügt</li>
<li>Chosen hinzugefügt</li>
<li>moment.js hinzugefügt</li>
<li>Daterangepicker hinzugefügt</li>
<li>Bootstrap aktualisiert</li>
<li>Font Awesome aktualisiert</li>
<li>HybridAuth aktualisiert</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>Validator Klasse und multidimensionale Arrays</li>
<li>Installer wirft einen Fehler bei falschen MySQL Daten</li>
<li>Nur eine Zeitquelle bei der lend.php</li>
<li>External Auth benutzt falschen SSL Port</li>
<li>SSL/TLS Support beim PHPMailer</li>
<li>Custom Colums</li>
<li>Bcrypt Support wird in jedem Fall überprüft</li>
<li>Prefix beim User Anlegen als Reseller ignoriert</li>
<li>Voice Master mit externem TSDNS Master</li>
<li>Ungültiger Wert für nextfree in der Verleih API</li>
<li>PHP Notice Meldungen bei gestopptem Voice Server</li>
<li>MySQL Übersicht benutzt falschen Index</li>
<li>SQL Exception bei API MySQL DB Edit</li>
<li>TSDNS Key Login funktioniert nicht</li>
<li>Http Server startet nicht nach Edit</li>
<li>E-Mail Template von Useradd und Registration nicht bearbeitbar</li>
<li>DB Dump kann nicht gedownloaded werden</li>
</ul>
','<div align=\"right\">05.25.2014</div>
<b>Changes:</b><br/>
<ul>
<li>API:
<ul>
<li>TSNDS API added</li>
<li>More speaking access denied error message</li>
<li>Send operation is returned for debugging</li>
<li>Complete data set is returned at edit operations</li>
<li>IP is returned at Game and voice server edit</li>
<li>Game switch games can be added/removed at the edit operation</li>
<li>FTP user is returned at game server operations</li>
<li>Initial password can be set for game servers</li>
<li>Core count is returned for game servers</li>
<li>Added list of all installed game types/master apps</li>
<li>Added list of all master server with optional limit</li>
<li>Multiple master IDs can be send during all add operations</li>
<li>Description is returned at master lists</li>
<li>Added Web space master list</li>
<li>Added MySQL master list</li>
<li>Generated user name is returned in case none is send</li>
<li>Added user list</li>
<li>New operation clean users externalID</li>
</ul></li>
<li>CMS:
<ul>
<li>hreflang support</li>
<li>index.php is routed to home when seo mode is active</li>
<li>WYSIWYG editor Summernote for page and news management</li>
</ul></li>
<li>Feeds:
<ul>
<li>External news in steam feeds are read and added</li>
<li>Pictures are removed from feeds</li>
</ul></li>
<li>Game server:
<ul>
<li>Quota support</li>
<li>Multiple hard disks are supported</li>
<li>SteamCMD login can be maintained per template/image</li>
<li>control.sh replaced by PHP Class</li>
<li>Configuration previously done at the control.sh replaced by app server configuration</li>
<li>Protected Linux system user will be only created when the game server has the mode enabled</li>
<li>SteamCMD updates are executed after each other instead of combined</li>
<li>Optional option [no_padding] at add ons and additional start commands</li>
<li>Query port can be defined at the game server template</li>
<li>New template No More Room in Hell</li>
<li>New template Project Cars</li>
<li>FTP server without chroot are supported</li>
<li>Lending Minecraft, Samp and Teeworlds is supported</li>
<li>statuscheck.php timeout increased</li>
<li>Restart done by a cronjob is logged with ip 127.0.0.1</li>
<li>\"@\" and \".\" allowed for FTP user at FastDL</li>
</ul></li>
<li>Generell:
<ul>
<li>New admin/user template with 6 different colours</li>
<li>Large parts of the code re factored</li>
<li>Creation processes are 1 step only</li>
<li>SSH ips can be used for ssh connect only to be able to set up a dmz</li>
<li>Icon and text at the header can be configured at the settings</li>
<li>Status page for cronjobs and PHP extensions</li>
<li>Third party CSS, JS and Fonts are shipped with Easy-Wi</li>
<li>Query results are processed with while instead of foreach loops to reduce ram usage</li>
<li>Modul concept redone</li>
<li>CURRENT_TIMESTAMP supported at table repair</li>
<li>User can deactive info texts</li>
<li>Char \"-\" can be used for passwords</li>
<li>Default externalID is now \"empty\"</li>
<li>Job entries for stopping all services are written when a user is deactivated or deleted</li>
<li>Improved error response at external auth</li>
</ul></li>
<li>MySQL:
<ul>
<li>externalID can be maintained for databases</li>
</ul></li>
<li>Tickets:
<ul>
<li>HTML5 validation added to avoid 404 errors</li>
</ul></li>
<li>Voice server:
<ul>
<li>externalID can be maintained for TSNDS</li>
<li>Maximum amount of TSNDS per master can be defined</li>
<li>Description for Voice Server can be maintained</li>
</ul></li>
<li>Web space:
<ul>
<li>Multiple domains can be mapped to a web space</li>
<li>Optional php.ini configurations can be defined</li>
</ul></li>
<li>Third Party:
<ul>
<li>Added DataTables</li>
<li>Added Chosen</li>
<li>Added moment.js</li>
<li>Added Daterangepicker</li>
<li>Updated Bootstrap</li>
<li>Updated Font Awesome</li>
<li>Updated HybridAuth</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>Validator class and multi dimensional arrays</li>
<li>Installer without error message in case of incorrect MySQL access data</li>
<li>Only one source of time at lend.php</li>
<li>External auth is using wring port for SSL</li>
<li>SSL/TLS support at PHPMailer</li>
<li>Custom Colums</li>
<li>Bcrypt support not checked in any case</li>
<li>Prefix ignored at user adding as resller</li>
<li>Voice master with external TSDNS master</li>
<li>Incorrect value for nextfree at lend API</li>
<li>PHP notice message with stopped voice server</li>
<li>MySQL overview with incorrect index</li>
<li>SQL exception at MySQL DB API edit</li>
<li>TSDNS key login not working</li>
<li>Http server not starting after edit</li>
<li>E-Mail template for user add and registration cannot be edited</li>
<li>DB dump cannot be downloaded</li>
</ul>
')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}