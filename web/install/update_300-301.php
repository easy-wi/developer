<?php
/**
 * File: update_300-301.php.
 * Author: Ulrich Block
 *
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
$insert_easywi_version=$sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('3.01','<div align=\"right\">31.05.2012</div>
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>API hinzugef&uuml;gt, mittels der man Benutzer und Voiceserver verwalten kann</li>
<li>Job System hinzugef&uuml;gt, dass die API Auftr&auml;ge abarbeitet.</li>
<li>API Verwaltung der Leihserver entfernt, weil nun eine m&auml;chtigere zentrale Verwaltung f&uuml;r alle Aktionen verwendet wird.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Diverse Layoutanpassungen im Bereich (versteckter) Inputfelder und Grafiken.</li>
</ul>','<div align=\"right\">31.05.2012</div>
<b>Changes and new functions:</b><br/>
<ul>
<li>API added that allows to manage User and Voiceserver.</li>
<li>Job system added that manages the commands that are send to the API.</li>
<li>API settings removed from lendserver since the new more prowerfull centrel conrol for all APIs is used now.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Multiple layoutchanges regarding (hidden) input fields and images.</li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error=$insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();
if (isset($error[2]) and $error[2]!="" and $error[2]!=null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

} else {
	echo "Error: this file needs to be included by the updater!<br />";
}