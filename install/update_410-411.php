<?php

/**
 * File: update_410-411.php.
 * Author: Ulrich Block
 * Date: 23.10.13
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
('4.11','<div align=\"right\">23.10.2013</div>
Leider haben sich in der 4.10 einige Fehler eingeschlichen. Dazu hat sie deutlich aufgezeigt, das viele Admins noch alte PHP Versionen nutzen und Easy-WI inkompatibel geworden ist.<br>
<br>
4.11 ist ein Hotfix Release, dass diese Probleme addressiert.<br>
<br>
<b>Änderungen:</b><br/>
<ul>
<li>Passwort Hash Fallback from Fallback</li>
<li>register_globals wird deaktiviert wenn an</li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>Vertreter Login schlägt fehl.</li>
<li>GS Backup Templates enthalten falsche Variable server_id</li>
<li>Falscher Link im Adminpanel für ESXI Host</li>
<li>Minecraft Query funktioniert nicht</li>
<li>Falscher tsdns_settings.ini Syntax</li>
</ul>
','<div align=\"right\">10.23.2013</div>
Unfortunately errors have slipped in 4.10. In addition the update revealed that there are still admins with old PHP versions around. Those admins could not login anymore since.<br>
<br>
4.11 is a hotfix release which addresses these problems.<br>
<br>
<b>Changes:</b><br/>
<ul>
<li>password hash fallback from fallback</li>
<li>deaktivate register_globals if on</li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li>Substitute login failing</li>
<li>gs backup templates with incorrect variable server_id</li>
<li>incorrect link at esxi host link</li>
<li>Minecraft Query not working</li>
<li>wrong tsdns_settings.ini syntax</li>
</ul>
')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}