<?php
/**
 * File: update_522_530.php
 * Author: Daniel Rodriguez Baumann
 * Date: 15.04.2016
 * Contact: <daniel@triopsi.com>
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

    $response->add('Action: Update Setting E-Mail');

    $query = $sql->prepare("INSERT INTO `settings_email_category` (`id`, `name`, `reseller_id`) VALUES (1, 'vServer', 0), (2, 'Server', 0), (3, 'Ticket', 0), (4, 'General', 0), (5, 'VoiceServer', 0), (6, 'GameServer', 0) ON DUPLICATE KEY UPDATE `id`=`id`");
    $query->execute();

    //Install E-Mail template
    include(EASYWIDIR . '/stuff/data/email_templates.php');
    foreach ($emailTemplates as $template) {
        $query = $sql->prepare($template['query']);
        $query->execute(array(0));
    }

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('5.30','<div align=\"right\">01.05.2016</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>Game Server:
<ul>
<li>GoTV Demo Upload hinzugefügt</li>
<li>Trotz falscher RAM Einstellungen können GS hinzugefügt werden</li>
</ul></li>
<li>Einstellungen:
<ul>
<li>SMTP Connection Test hinzugefügt</li>
<li>Neues E-Mail Template Management</li>
</ul></li>
<li>Diverses:
<ul>
<li>XSS beim Gameserver Log</li>
<li>Unvollständige TS3 Gruppen Validierung beim Token Erstellen</li>
<li>Fehlermeldungen fehlender Tabellen bei DB Repair unterdrückt</li>
<li>Code Vorbereitung für Semantic Versioning</li>
<li>RSS Feed der Twitter API 1.1 werden unterstützt</li>
<li>Automatischer Redirect zum install Ordner</li>
<li>Slogan Support bei der Login Page hinzugefügt</li>
<li>HybridAuth auf 2.6.0 aktualisiert</li>
<li>PHP Mailer auf 5.2.14 aktualisiert</li>
<li>jQuery auf 2.2.3 aktualisiert</li>
<li>Summernote auf 0.8.1 aktualisiert</li>
<li>Chosen auf 1.5.1 aktualisiert</li>
<li>Font Awesome auf 4.6.2 aktualisiert</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>Game Server Umzugsservice benutzt falschen Pfad</li>
<li>steamclient.so kann bei Valve Servern nicht gefunden werden</li>
<li>Minecraft Templates</li>
<li>Falsches HTML Tag br wird bei Addon Beschreibung angezeigt</li>
<li>Fehlender fastcgi.conf Include beim Nginx FPM Template</li>
</ul>','<div align=\"right\">01.05.2016</div>
<b>Changes:</b><br/>
<ul>
<li>Game server:
<ul>
<li>Added GoTV demo upload support</li>
<li>Ensure that incorrect RAM settings do not prevent gs add</li>
</ul></li>
<li>Settings:
<ul>
<li>Added SMTP connection</li>
<li>New E-Mail template management</li>
</ul></li>
<li>Miscellaneous:
<ul>
<li>Supressed error return of missing tables at DB repair</li>
<li>Code preperation for semantic versioning</li>
<li>Added RSS Feed with Twitter API 1.1</li>
<li>Automatic redirection to install folder</li>
<li>Slogan support at login page</li>
<li>Upgraded HybridAuth to 2.6.0</li>
<li>Upgraded PHP Mailer to 5.2.14</li>
<li>Upgraded jQuery to 2.2.3</li>
<li>Upgraded Summernote to 0.8.1</li>
<li>Upgraded Chosen to 1.5.1</li>
<li>Upgraded Font Awesome to 4.6.2</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>XSS at game server log</li>
<li>Incomplete TS3 group validation at token create</li>
<li>Migrate game server using incorrect path</li>
<li>steamclient.so not found on valve server start up</li>
<li>Minecraft templates</li>
<li>Incorrect HTML tag br is added at addon description display</li>
<li>Missing fastcgi.conf include at Nginx fpm template</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}