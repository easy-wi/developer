<?php

/**
 * File: update_430-440.php.
 * Author: Ulrich Block
 * Date: 02.02.14
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

    if (!isset($displayToUser)) {
        $displayToUser = '';
    }

    $response->add('Adding tables if needed.');
    include(EASYWIDIR . '/stuff/methods/tables_add.php');

    $query = $sql->prepare("DROP TABLE IF EXISTS `voice_stats_settings`");
    $query->execute();

    $query = $sql->prepare("DELETE FROM `easywi_statistics`");
    $query->execute();

    $query = $sql->prepare("SELECT `webVhostID`,`userID`,`resellerID`,`dns`,`ownVhost`,`vhostTemplate` FROM `webVhost`");
    $query->execute();
    $query2 = $sql->prepare("INSERT INTO `webVhostDomain` (`webVhostID`,`userID`,`resellerID`,`domain`,`path`,`ownVhost`,`vhostTemplate`) VALUES (?,?,?,?,'',?,?)");
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        // Try catch as some admins might have maintained domains not be so unique
        try {
            $query2->execute(array($row['webVhostID'], $row['userID'], $row['resellerID'], $row['dns'], $row['ownVhost'], $row['vhostTemplate']));
            $response->add('Migrated ' . $row['dns'] . ' to new table.');
        } catch(PDOException $error) {
            $response->add($error->getMessage());
        }
    }

    $response->add('Repairing tables if needed.');
    include(EASYWIDIR . '/stuff/methods/tables_repair.php');

    $query = $sql->prepare("UPDATE `servertypes` SET `useQueryPort`=2 WHERE `gameq` IN ('armedassault2', 'armedassault2oa', 'armedassault3', 'bf2', 'cube2', 'mta', 'ut', 'ut2004', 'ut3')");
    $query->execute();

    $query = $sql->prepare("UPDATE `servertypes` SET `cmd`='./%binary% -n' WHERE `shorten`='mtasa' AND `cmd`='./%binary%'");
    $query->execute();

    // Add new games if not existing
    include(EASYWIDIR . '/stuff/methods/gameslist.php');

    $addGames = array('nmrih');

    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=0 LIMIT 1");
    $query2 = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`gamebinary`,`gamebinaryWin`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`gameq`,`gamemod`,`gamemod2`,`configs`,`configedit`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`useQueryPort`,`mapGroup`,`protected`,`protectedSaveCFGs`,`ramLimited`,`os`,`resellerid`) VALUES (:steamgame,:appID,:updates,:shorten,:description,:gamebinary,:gamebinaryWin,:binarydir,:modfolder,:fps,:slots,:map,:cmd,:modcmds,:tic,:gameq,:gamemod,:gamemod2,:configs,:configedit,:portStep,:portMax,:portOne,:portTwo,:portThree,:portFour,:portFive,:useQueryPort,:mapGroup,:protected,:protectedSaveCFGs,:ramLimited,:os,:resellerid)");

    foreach ($gameImages as $image) {

        if (in_array($image[':shorten'], $addGames) and count($image) == 33) {

            $image[':resellerid'] = 0;

            $query->execute(array($image[':shorten'], 0));
            $imageExists = (int) $query->fetchColumn();

            if ($imageExists == 0) {

                $query2->execute($image);

                if ($query2->rowCount() > 0) {
                    $response->add($gsprache->add . ': ' . $image[':description']);
                }
            }
        }
    }

    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('5.00','<div align=\"right\">10.05.2014</div>
<b>Änderungen:</b><br/>
<ul>
<li>Generell:
<ul>
<li></li>
</ul></li>
<li>CMS:
<ul>
<li</li>
</ul></li>
<li>Gameserver:
<ul>
<li</li>
</ul></li>
<li>MySQL:
<ul>
<li></li>
</ul></li>
<li>Root:
<ul>
<li></li>
</ul></li>
<li>Voiceserver:
<ul>
<li></li>
</ul></li>
<li>Webspace:
<ul>
<li></li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li></li>
</ul>
','<div align=\"right\">05.10.2014</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li></li>
</ul></li>
<li>CMS:
<ul>
<li></li>
</ul>
<li>Gameserver:
<ul>
<li></li>
</ul></li>
<li>MySQL:
<ul>
<li></li>
</ul></li>
<li>Rootserver:
<ul>
<li></li>
</ul></li>
<li>User:
<ul>
<li></li>
</ul></li>
<li>Voiceserver:
<ul>
<li></li>
</ul></li>
<li>Webspace:
<ul>
<li></li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li></li>
</ul>
')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}