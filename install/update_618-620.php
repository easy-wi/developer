<?php

/**
 * File: update_616-620.php.
 * Author: Ulrich Block
 * Date: 19.07.20
 * Time: 18:38
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
('6.2.0','<div align=\"right\">28.07.2020</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>General
<ul>
<li>Twitter Timeline ins Dashboard Hinzugefügt</li>
<li>Twitter Follow Knopf im Admin Menu Hinzugefügt</li>
<li>Keyfile eingabe verbessert</li>
<li>Update auf Font Awesome 5.14</li>
<li>Speichernutzung im New Theme hinzugefügt</li>
</ul></li></ul>
<b>Bugfixes:</b>
<ul>
<li>App/Master Server fehler behoben</li>
<li>New Theme: allgemeine verbesserungen</li>
<li>Mobile Navigation Fix</li>
<li>Speichernutzung auf dem Masterserver Fix</li>
</ul>
<b>Zukunft:</b>
<li>Vorbereitung um die V-Server / Netzwerkmodule zu entfernen</li>
</ul>','<div align=\"right\">28.07.2020</div>
<b>Changes:</b><br/>
<ul>
<li>General
<ul>
<li>Twitter timeline added</li>
<li>Twitter follow button added</li>
<li>Enhanced Keyfile handling</li>
<li>Update to Font Awesome 5.14</li>
<li>Added Diskspace view in the new theme</li>
</ul></li></ul>
<b>Bugfixes:</b>
<ul>
<li>App/Masterserver bug fixes</li>
<li>New theme: general enhancements</li>
<li>Mobile navigation buttons fixed</li>
<li>Fixed Diskspace view on Masterserver</li>
</ul>
<b>Future:</b>
<li>preparation to remove V-Server / Network modules</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}
