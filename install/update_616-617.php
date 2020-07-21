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
('6.1.7','<div align=\"right\">21.07.2020</div>
<b>&Auml;nderungen:</b><br/>
<ul>
<li>General
<ul>
<li>Update Email-Template Hinzugefügt</li>
<li>Beispiel Modul Hinzugefügt</li>
<li>kosmetische Fehler behoben</li>
<li>Ticketsystem überarbeitet</li>
<li>Modul Liste geupdatet -> Example Modules</li>
<li>PhpMailer auf version 6 geupdated</li>
</ul></li></ul>
<b>Bugfixes:</b>
<ul>
<li>Updater Bug Behoben</li>
<li>Sendmail Bug Behoben</li>
<li>Ticketsystem unterkategorie Bug behoben</li>
<li>Masterapps MC update Bug Behoben</li>
<li>DNS bei der Gameserver Anzeige Bug Behoben</li>
</ul>','<div align=\"right\">21.07.2020</div>
<b>Changes:</b><br/>
<ul>
<li>General
<ul>
<li>Update Email-Template Added </li>
<li>Exemple Module Added </li>
<li>cosmetic errors fixed</li>
<li>Ticketsystem upgraded</li>
<li>Modul List Updated -> Example Modules</li>
<li>Upgrade PhpMailer to version 6</li>
</ul></li></ul>
<b>Bugfixes:</b>
<ul>
<li>Updater Bug Fixed</li>
<li>Sendmail Bug Fixed</li>
<li>Supportsystem Subcategory Bug Fixed</li>
<li>Masterapps Minecraft update Bug Fixed</li>
<li>Dns not showing correctly by Gameserver view Bug Fixed</li>
</ul>')");
    $query->execute();


    $query2 = $sql->prepare("SELECT `id` FROM `modules` WHERE `type`='A' AND `get`=? LIMIT 1");
    $query2->execute(array('ex'));
    $modulesid = (int) $query2->fetchColumn();

    if($modulesid == 0){
        $query3 = $sql->prepare("INSERT INTO `modules` (`get`,`file`,`sub`,`active`, `type`) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE `active`=VALUES(`active`)");
        $query3->execute(array('ex', 'example.php', 'mo', 'Y', 'A'));
        $modulesid = $sql->lastInsertId();
    }

    $modullanguage = [
        ["type" => "mo", "lang" => "de", "transID" => $modulesid, "resellerID" => 0, "text" => "Beispiel Modul"],
        ["type" => "mo", "lang" => "dk", "transID" => $modulesid, "resellerID" => 0, "text" => "Example Module"],
        ["type" => "mo", "lang" => "it", "transID" => $modulesid, "resellerID" => 0, "text" => "Example Module"],
        ["type" => "mo", "lang" => "pt", "transID" => $modulesid, "resellerID" => 0, "text" => "Example Module"],
        ["type" => "mo", "lang" => "ru", "transID" => $modulesid, "resellerID" => 0, "text" => "Example Module"],
        ["type" => "mo", "lang" => "uk", "transID" => $modulesid, "resellerID" => 0, "text" => "Example Module"],
    ];

    if($modulesid != 0){
        $query4 = $sql->prepare("SELECT `transID` FROM `translations` WHERE `type`='mo' AND `lang`=? And `transID`=? LIMIT 1");
        $query5 = $sql->prepare("INSERT INTO `translations` (`type`, `lang`, `transID`, `resellerID`, `text`) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
        foreach ($modullanguage as $lang){
            $query4->execute(array($lang["lang"], $modulesid));
            $transID = (int)$query4->fetchColumn();
            if($transID == 0){
                $query5->execute(array($lang["type"], $lang["lang"], $lang["transID"], $lang["resellerID"], $lang["text"]));
            }
        }
    }


    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();
    $query2->closecursor();
    $query3->closecursor();
    $query4->closecursor();
    $query5->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}
