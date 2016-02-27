<?php

/**
 * File: update_520-521.php.
 * Author: Ulrich Block
 * Date: 27.02.16
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
('5.21','<div align=\"right\">27.02.2016</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>CMS:
<ul>
<li>Liste von Sub Pages im Page Template verf&uuml;gbar</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Benutzer kann Auto Restarts deaktivieren</li>
<li>JSON kann über den Web FTP editiert werden</li>
<li>Restart nach Update kann deaktiviert werden</li>
<li>RAM wird bei der Serverbelegeung berücksichtigt</li>
<li>Live Console mit Monospace Font</li>
<li>Passiv FTP wird unterstützt</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Standard Wert für connectIpOnly gesetzt</li>
<li>Flex Slots Konfiguration ausgeblendet</li>
</ul></li>
<li>MySQL:
<ul>
<li>Mehr Fehler Details im MySQL DB CRUD</li>
<li>Mehr Standard Werte bei MySQL Master hinzufügen gesetzt</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>MySQL hinzufügen/mod behandelt external SQL Fehler nicht.</li>
<li>Falsche Templates beim Page CRUD gesetzt</li>
<li>Page Edit setzt Variable naviDisplay nicht</li>
<li>MySQL Master Passwort ist Typ Text</li>
<li>GS k&ouml;nnen nicht editiert werden, wenn der Root voll belegt ist</li>
<li>PHP Config beim Web Master wird nicht angezeigt</li>
<li>HDD Wert wird bei der GS API nicht übergeben</li>
<li>Alle GS eines Roots werden nach irgend einem Update neu gestartet</li>
<li>Maximale Voice Backups k&ouml;nnen nicht gr&ouml;ßer als 9 gesetzt werden</li>
<li>Neueste TS3 Version kann nicht erfasst werden</li>
<li>GS Restart funktioniert unter besondern MC Umst&auml;nden nicht immer</li>
<li>Page Keywords funktioniert unter manchen Umst&auml;nden nicht</li>
<li>GS Backups haben deploy Probleme</li>
<li>Falsche Success Anzeige beim Job Log</li>
<li>Voice API convertiert JSON Config nicht</li>
<li>Webspace Master Auswahl</li>
<li>Zu viele Dateien beim GS sync kopiert</li>
<li>GS backups werden beim restart gel&ouml;scht</li>
<li>Regex wird bei GS config edit nicht immer escaped</li>
<li>Configs werden bei manchen GS templates zwemail gelistet</li>
<li>E-Mail Umlaute und Anordnung</li>
<li>Quota bei mehreren Partitionen</li>
</ul>','<div align=\"right\">02.27.2015</div>
<b>Changes:</b><br/>
<ul>
<li>CMS:
<ul>
<li>List of sub pages available at the page template</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Restart after Update can be deactivated</li>
<li>User can deactivate auto restarts</li>
<li>JSON can be edited via Web FTP</li>
<li>RAM is taken into account on master usage</li>
<li>Live console changed to monospace font</li>
<li>Passiv FTP is supported</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Default value for connectIpOnly defined</li>
<li>Flex slots configuration hidden</li>
</ul></li>
<li>MySQL:
<ul>
<li>More error details in at MySQL DB CRUD</li>
<li>More default values at MySQL Master add defined</li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>GS cannot be edited in case root is full</li>
<li>All GS of root will be restarted in case of any update</li>
<li>HDD value not saved at GS API</li>
<li>GS restart not working for some MC cases</li>
<li>GS cannot be deployed</li>
<li>Too many files are copied with GS sync/add</li>
<li>GS backups removed on restart</li>
<li>Configs listed twice at some templates</li>
<li>Regex not properly escaped in same config edit cases</li>
<li>MySQL master password displayed with type text</li>
<li>MySQL add/mod does not handle SQL external errors</li>
<li>Incorrect templates at page CRUD</li>
<li>Page edit does not set variable naviDisplay</li>
<li>Page keywords not always working</li>
<li>PHP config not displayed at Web Master</li>
<li>Maximum voice backups are limited to 9</li>
<li>Newest TS3 version not detected</li>
<li>Voice API does not convert JSON config</li>
<li>Incorrect sucess display at job log</li>
<li>E-Mail Umlaute und Anordnung</li>
<li>Web master selection</li>
<li>Web quota with multiple partitions</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}