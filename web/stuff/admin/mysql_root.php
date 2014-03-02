<?php
/**
 * File: mysql_root.php.
 * Author: Ulrich Block
 * Date: 13.06.12
 * Time: 20:39
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

if (!isset($admin_id) or $main != 1 or !isset($reseller_id) or !$pa['root']) {
    header('Location: admin.php');
    die('No acces');
}


if ($ui->st('d', 'get') == 'bu' and $ui->st('action', 'post') == 'bu' and $reseller_id == 0) {

    include(EASYWIDIR . '/stuff/methods/mysql_backup_class.php');
    $createBackup = true;

    header('Content-type: application/sql; charset=utf-8');
    header('Content-Description: Downloaded File');
    header('Content-Disposition: attachment; filename=' . $dbConnect['db'] . '.sql');

    $theBackup = new createDBDump($dbConnect['db'], $ewVersions['version'], $sql);
    $theBackup->createDump();
    echo $theBackup->getDump();

    die();

} else if ($ui->st('d', 'get') == 'rp' and $ui->st('action', 'post') == 'rp' and $reseller_id == 0) {

    $updateinclude = true;

    class UpdateResponse {
        public $response = '';
        function __construct() {
            $this->response = '';
        }
        function add ($newtext) {
            $this->response .= $newtext;
        }
        function __destruct() {
            unset($this->response);
        }
    }
    $response = new UpdateResponse();

    $sql->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);

    if (!isset($alreadyRepaired)) {
        $response->add('Adding tables if needed.');
        include(EASYWIDIR . '/stuff/methods/tables_add.php');
    }

    if (!isset($alreadyRepaired)) {
        $response->add('Repairing tables if needed.');
        include(EASYWIDIR . '/stuff/methods/tables_repair.php');
    }

    $response->add('Fixing data entries if needed.');
    include(EASYWIDIR . '/stuff/methods/tables_entries_repair.php');

    $template_file = $response->response;

} else if ($ui->st('d', 'get') == 'rg') {

    include(EASYWIDIR . '/stuff/methods/gameslist.php');

    if ($ui->st('action', 'post') == 'rg') {

        $template_file = '';
        $array = (array) $ui->pregw('games', 255, 'post');

        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
        $query2 = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`gamebinary`,`gamebinaryWin`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`gameq`,`gamemod`,`gamemod2`,`configs`,`configedit`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`mapGroup`,`protected`,`protectedSaveCFGs`,`ramLimited`,`os`,`resellerid`) VALUES (:steamgame,:appID,:updates,:shorten,:description,:gamebinary,:gamebinaryWin,:binarydir,:modfolder,:fps,:slots,:map,:cmd,:modcmds,:tic,:gameq,:gamemod,:gamemod2,:configs,:configedit,:portStep,:portMax,:portOne,:portTwo,:portThree,:portFour,:portFive,:mapGroup,:protected,:protectedSaveCFGs,:ramLimited,:os,:resellerid)");
        $query3 = $sql->prepare("UPDATE `servertypes` SET `steamgame`=:steamgame,`appID`=:appID,`updates`=:updates,`shorten`=:shorten,`description`=:description,`gamebinary`=:gamebinary,`gamebinaryWin`=:gamebinaryWin,`binarydir`=:binarydir,`modfolder`=:modfolder,`fps`=:fps,`slots`=:slots,`map`=:map,`cmd`=:cmd,`modcmds`=:modcmds,`tic`=:tic,`gameq`=:gameq,`gamemod`=:gamemod,`gamemod2`=:gamemod2,`configs`=:configs,`configedit`=:configedit,`portStep`=:portStep,`portMax`=:portMax,`portOne`=:portOne,`portTwo`=:portTwo,`portThree`=:portThree,`portFour`=:portFour,`portFive`=:portFive,`mapGroup`=:mapGroup,`protected`=:protected,`protectedSaveCFGs`=:protectedSaveCFGs,`ramLimited`=:ramLimited,`os`=:os WHERE `shorten`=:shorten AND `resellerid`=:resellerid LIMIT 1");

        foreach ($gameImages as $image) {

            if (in_array($image[':shorten'], $array) and count($image) == 32) {

                $image[':resellerid'] = $resellerLockupID;

                $query->execute(array($image[':shorten'], $resellerLockupID));
                $imageExists = (int) $query->fetchColumn();

                if ($imageExists == 0) {

                    $query2->execute($image);

                    if ($query2->rowCount() > 0) {
                        $template_file .= $gsprache->add . ': ' . $image[':description'] .'<br>';
                    } else {
                        $template_file .= 'Error ' . $gsprache->add . ': ' . $image[':description'] .'<br>';
                    }

                } else if ($ui->id('actionType', 1 ,'post') == 2) {

                    $query3->execute($image);

                    if ($query3->rowCount() > 0) {
                        $template_file .= $gsprache->mod . ': ' . $image[':description'] .'<br>';
                    } else {
                        $template_file .= 'Error ' . $gsprache->mod . ': ' . $image[':description'] .'<br>';
                    }

                } else {
                    $template_file .= 'Skipped: ' . $image[':description'] .'<br>';
                }
            }
        }

    } else {

        $template_file = 'admin_db_operations_gs.tpl';
    }

} else if ($ui->st('d', 'get') == 'ra') {

    require_once(EASYWIDIR . '/stuff/methods/addonslist.php');

    if ($ui->st('action', 'post') == 'ra') {

        $template_file = '';
        $array = (array) $ui->pregw('addons', 255, 'post');

        $query = $sql->prepare("SELECT `id` FROM `addons` WHERE `addon`=? AND `resellerid`=? LIMIT 1");
        $query2 = $sql->prepare("INSERT INTO `addons` (`active`,`depending`,`paddon`,`addon`,`type`,`folder`,`menudescription`,`configs`,`cmd`,`rmcmd`,`resellerid`) VALUES ('Y',?,?,?,?,?,?,?,?,?,?)");
        $query3 = $sql->prepare("UPDATE `addons` SET `depending`=?,`type`=?,`paddon`=?,`folder`=?,`menudescription`=?,`configs`=?,`cmd`=?,`rmcmd`=? WHERE `addon`=? AND `resellerid`=? LIMIT 1");
        $query4 = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
        $query5 = $sql->prepare("INSERT INTO `addons_allowed` (`addon_id`,`servertype_id`,`reseller_id`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `servertype_id`=VALUES(`servertype_id`),`addon_id`=VALUES(`addon_id`)");
        $query6 = $sql->prepare("DELETE FROM `addons_allowed` WHERE `addon_id`=? AND `reseller_id`=?");

        foreach ($gameAddons as $addon) {


            if (in_array($addon[':addon'], $array) and count($addon) == 10) {

                $query->execute(array($addon[':addon'], $resellerLockupID));
                $addonID = (int) $query->fetchColumn();

                $dependsID = 0;

                if (strlen($addon[':depends']) > 0) {
                    $query->execute(array($addon[':depends'], $resellerLockupID));
                    $dependsID = $query->fetchColumn();
                }


                if ($addonID == 0) {

                    $query2->execute(array($dependsID, $addon[':paddon'], $addon[':addon'], $addon[':type'], $addon[':folder'], $addon[':menudescription'], $addon[':configs'], $addon[':cmd'], $addon[':rmcmd'], $resellerLockupID));

                    if ($query2->rowCount() > 0) {
                        $template_file .= $gsprache->add . ': ' . $addon[':menudescription'] .'<br>';
                    } else {
                        $template_file .= 'Error ' . $gsprache->add . ': ' . $addon[':menudescription'] .'<br>';
                    }

                    $addonID = $sql->lastInsertId();

                    foreach ($addon[':supported'] as $supported) {

                        $query4->execute(array($supported, $resellerLockupID));

                        $query5->execute(array($addonID, $query4->fetchColumn(), $resellerLockupID));

                    }
                } else if ($ui->id('actionType', 1 ,'post') == 2) {

                    $query3->execute(array($dependsID, $addon[':type'], $addon[':paddon'], $addon[':folder'], $addon[':menudescription'], $addon[':configs'], $addon[':cmd'], $addon[':rmcmd'], $addon[':addon'], $resellerLockupID));

                    $editCount = 0;

                    foreach ($addon[':supported'] as $supported) {

                        $query4->execute(array($supported, $resellerLockupID));

                        $query6->execute(array($addonID, $resellerLockupID));

                        $query5->execute(array($addonID, $query4->fetchColumn(), $resellerLockupID));

                        $editCount += $query5->rowCount();
                    }

                    if ($query3->rowCount() > 0 or $editCount > 0) {
                        $template_file .= $gsprache->mod . ': ' . $addon[':menudescription'] .'<br>';

                    } else {
                        $template_file .= 'Error ' . $gsprache->mod . ': ' . $addon[':menudescription'] .'<br>';
                    }

                } else {
                    $template_file .= 'Skipped: ' . $addon[':menudescription'] .'<br>';
                }
            }

        }
    } else {

        $template_file = 'admin_db_operations_ao.tpl';
    }

} else {
    $template_file = 'admin_db_operations.tpl';
}