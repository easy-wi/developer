<?php

/**
 * File: update_600-605.php.
 * Author: Ulrich Block
 * Date: 06.01.18
 * Time: 13:39
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
('6.0.5','<div align=\"right\">08.03.2019</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>General
<ul>
<li>Portugiesisch hinzugef&uuml;gt</li>
<li>kosmetische Fehler behoben</li>
<li>Unterst&uuml;tzung f&uuml;r folgende Betriebssysteme:
<li>CentOS 7</li>
<li>Debian 9</li>
<li>Ubuntu 18.10</li>
</ul></li></li></ul>
<b>Bugfixes:</b>
<ul>
<li>MC Forge Template</li>
<li>Counter-Strike: Condition Zero</li>
<li>Teamspeak Bansystem</li>
<li>Umlaute Fehler behoben</li>
</ul>','<div align=\"right\">08.03.2019</div>
<b>Changes:</b><br/>
<ul>
<li>General
<ul>
<li>Portuguese added</li>
<li>fixed cosmetic Mistakes</li>
<li>Add Support for the following Operating Systems:
<li>CentOS 7</li>
<li>Debian 9</li>
<li>Ubuntu 18.10</li>
</ul></li></li>
<b>Bugfixes:</b>
<ul>
<li>MC Forge Template</li>
<li>Counter-Strike: Condition Zero</li>
<li>Teamspeak Bansystem</li>
<li>Fixed Umlaut errors</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}