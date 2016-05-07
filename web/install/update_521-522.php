<?php

/**
 * File: update_521-522.php.
 * Author: Ulrich Block
 * Date: 05.03.16
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

    $response->add('Action: Update to new skin color template system');

    $query = $sql->prepare("ALTER TABLE `settings` ADD `templateColor` VARCHAR(50)");
    $query->execute();
    $query->closecursor();

    $query = $sql->prepare("UPDATE `settings` SET `template`='default',`templateColor`=`template` WHERE `template` IN ('black','black-light','blue','blue-light','green','green-light','purple','purple-light','red','red-light','yellow','yellow-light')");
    $query->execute();
    $query->closecursor();

    $query = $sql->prepare("UPDATE `settings` SET `templateColor`='blue' WHERE `templateColor`='default'");
    $query->execute();
    $query->closecursor();

    $query = $sql->prepare("UPDATE `servertypes` SET `shorten`='arkse' WHERE `shorten`='ark'");
    $query->execute();
    $query->closecursor();

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('5.22','<div align=\"right\">25.03.2016</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>Gameserver:
<ul>
<li>Mehr als 1TB Ram beim Masterserver einstellbar</li>
</ul></li>
<li>Panel Einstellungen:
<ul>
<li>Favicon Einstellungen</li>
<li>Coloring Optionen für templates</li>
</ul></li>
<li>Diverses:
<ul>
<li>3rd Party Libraries aktualisiert</li>
<li>Unterstützung von Sonderzeichen in Passw&ouml;rtern</li>
<li>Pretty Output bei der statuscheck.php via GET</li>
<li>Unterst&uuml;tzung von optionaler Argumente via GET bei der statuscheck.php</li>
<li>Alte Update Scripts entfernt</li>
<li>Italian &Uuml;bersetzung hinzugef&uuml;gt</li>
<li>Reseller k&ouml;nnen Leihserver Modul nutzen</li>
<li>Standalone CMS Seiten in Admin LTE umgesetzt</li>
<li></li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>TSDNS und TS3 werden auf Grund neuer Naming Patterns nicht neu gestartet</li>
<li>Game und Voice Server werden neu gestartet, wenn der Besitzer inactive ist</li>
<li>GS Reinstall im User Panel with nicht gelogt</li>
<li>Typo Im Installer</li>
<li>CDN auf der der Login Seite benutzt</li>
<li>ExternalID beim Voice Server nicht einstellbar</li>
<li>Repquota Bug in der statuscheck.php</li>
</ul>','<div align=\"right\">03.25.2016</div>
<b>Changes:</b><br/>
<ul>
<li>Gameserver:
<ul>
<li>More than 1TB Ram can be managed at the masterserver</li>
</ul></li>
<li>Panel Settings:
<ul>
<li>Add favicon configuration</li>
<li>Coloring option for templates</li>
</ul></li>
<li>Miscellaneous:
<ul>
<li>3rd party libaries updated</li>
<li>Support of special characters at passwords</li>
<li>Pretty output at statuscheck.php via GET</li>
<li>Support optional args as GET at statuscheck.php</li>
<li>Remove old update scripts</li>
<li>Italian translation added</li>
<li>Reseller can make use of the lend server modul</li>
<li>Align standalone cms pages with Admin LTE</li>
<li></li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li>TSDNS and TS3 not restarted due to new binary names</li>
<li>Server restart when user is inactive</li>
<li>GS reinstall not logged at user panel</li>
<li>Typo error installer</li>
<li>CDN used at login pages</li>
<li>No external ID shown on voice edit</li>
<li>Repquota bug in statuscheck.php</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}