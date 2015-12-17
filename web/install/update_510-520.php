<?php

/**
 * File: update_510-520.php.
 * Author: Ulrich Block
 * Date: 16.12.15
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

    include(EASYWIDIR . '/stuff/keyphrasefile.php');

    // Execute correct, so we can update game templates
    $tables->correctExistingTables();

    // Steam Server Token
    $query = $sql->prepare("UPDATE `servertypes` SET `steamGameserverToken`='Y' WHERE `shorten` IN ('nmrih','csgo','zps','tf','hl2mp','ageofchivalry','pvkii','left4dead2','left4dead','dods','css')");
    $query->execute();

    // UT4 game binary copy
    $query = $sql->prepare("UPDATE `servertypes` SET `copyStartBinary`='Y' WHERE `shorten` IN ('ark')");
    $query->execute();

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('5.20','<div align=\"right\">20.12.2015</div>
<b>Änderungen:</b><br/>
<ul>
<li>Gameserver:
<ul></li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li></li>
</ul>','<div align=\"right\">12.20.2015</div>
<b>Changes:</b><br/>
<ul>
<li>Gameserver:
<ul>
<li></li>
</ul></li>
</ul>
<b>Bugfixes:</b>
<ul>
<li</li>
</ul>')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}