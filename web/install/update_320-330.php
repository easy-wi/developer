<?php
/**
 * File: update_320-330.php.
 * Author: Ulrich Block
 * Date: 23.03.13
 * Time: 12:36
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
('3.30','<div align=\"right\">04.04.2013</div>
<b>Änderungen:</b>
<ul>
<li>Generell:
<ul>
<li>Geändert: Gameserver Icons nun PNGs an Stelle von JPGs.</li>
<li>Hinzugefügt: 404 Fehlerseite, wenn die ID nicht vorhanden, oder der Zugriff nicht erlaubt ist.</li>
<li>Hinzugefügt: Custom Columns.</li>
<li>Hinzugefügt: Suche nach Ports.</li>
<li>Hinzugefügt: Überprüfung, ob das PHP SSH2 Modul installiert worden ist.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Entfernt: opt1-opt5. Wenn genutzt durch Custom Columns ersetzt.</li>
<li>Geändert: An Stelle einer Fehlermeldung zu zeigen, werden fehlende Dateien beim Config Editor angelegt.</li>
<li>Hinzugefügt: User wird über Fehler beim Umzugsservice informiert, wenn kein Zielserver ausgewählt wurde.</li>
</ul></li>
</ul>
<br/>
<b>Bugfixes:</b>
<ul>
<li>Korrekte IP(s) werden beim Anlegen Schritt 1 angezeigt (Gameserver).</li>
</ul>
<br/>
','<div align=\"right\">04.04.2013</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Changed: Gameserver icons are now PNGs instead of JPGs.</li>
<li>Added: 404 Error Page if ID cannot be found or access is fobidden.</li>
<li>Added: Custom Columns.</li>
<li>Added: Search by port.</li>
<li>Added: Check if PHP SSH2 module is installed.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Removed: opt1-opt5. If used replaced by Custom Columns.</li>
<li>Changed: Instead of showing error message file editor will create missing files.</li>
<li>Added: User will be informed that he forgot to choose the target server when using the migration service.</li>
</ul></li>
</ul>
<b>Bugfixes:</b><br/>
<ul>
<li>Only correct IP(s) shown at adding step 1 (Gameserver)</li>
</ul>
')");

    $insert_easywi_version->execute();
    $response->add('Action: insert_easywi_version done: ');
    $insert_easywi_version->closecursor();

    $query="CREATE TABLE IF NOT EXISTS `custom_columns` (`customID` int(10) unsigned NOT NULL,`itemID` int(10) unsigned NOT NULL,`var` VARCHAR(255) NOT NULL,PRIMARY KEY (`customID`,`itemID`)) ENGINE=InnoDB";
    $add=$sql->prepare($query);
    $add->execute();
    $query="CREATE TABLE IF NOT EXISTS `custom_columns_settings` (`customID` int(10) unsigned NOT NULL AUTO_INCREMENT,`active` enum('Y','N') NOT NULL DEFAULT 'Y',`item` enum('D','G','S','T','U','V') NOT NULL,`type` enum('I','V') NOT NULL,`length` int(10) unsigned,`name` VARCHAR(255) NOT NULL,PRIMARY KEY (`customID`),KEY(`item`)) ENGINE=InnoDB";
    $add=$sql->prepare($query);
    $add->execute();

    $add=$sql->prepare("UPDATE `servertypes` SET `appID`=232290,`steamgame`='S',`binarydir`=NULL WHERE `shorten`='dods'");
    $add->execute();

    $insert=$sql->prepare("INSERT INTO `custom_columns_settings` (`active`,`item`,`type`,`length`,`name`) VALUES (?,?,?,?,?)");
    $insert2=$sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('cc',?,?,?,0) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
    $copy = array();
    $opts = array();
    foreach(array('opt1' => 'Opt 1','opt2' => 'Opt 2','opt3' => 'Opt 3','opt4' => 'Opt 4','opt5' => 'Opt 5') as $opt=>$trans) {
        $query = $sql->prepare("SELECT COUNT(`id`) AS `a` FROM `gsswitch` WHERE `$opt` IS NOT NULL AND `$opt`!='' LIMIT 1");
        $query->execute();
        if ($query->fetchColumn()>0) {
            $insert->execute(array('Y','G','V',255,$opt));
            $id=$sql->lastInsertId();
            $insert2->execute(array($id,'de',$trans));
            $insert2->execute(array($id,'en',$trans));
            $copy[$id] = $opt;
            $opts[]="`${opt}`";
        }
    }
    if (count($copy)>0) {
        $query = $sql->prepare("SELECT `id`,".implode(',',$opts)." FROM `gsswitch`");
        $query->execute();
        $insert=$sql->prepare("INSERT INTO `custom_columns` (`customID`,`itemID`,`var`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `var`=VALUES(`var`)");
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            foreach ($copy as $k=>$v){
                $val=$row[$v] == null ? '' : $row[$v];
                $insert->execute(array($k, $row['id'],$val));
            }
        }
    }

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}