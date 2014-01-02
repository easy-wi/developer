<?php

/**
 * File: gserver_file_templates.php.
 * Author: Ulrich Block
 * Date: 02.01.14
 * Time: 11:46
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


if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['gserver'])) {
    header('Location: admin.php');
    die;
}
$sprache = getlanguagefile('images', $user_language, $resellerLockupID);
$gssprache = getlanguagefile('gserver', $user_language, $resellerLockupID);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    $logsubuser =  (isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller = 0;
}

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->st('d', 'get') == 'ad' or ($ui->st('d', 'get') == 'md' and $ui->id('id', 10, 'get'))) {

    $errors = array();

    $id = $ui->id('id', 10, 'get');

    $servertype = $ui->gamestring('servertype', 'post');
    $name = $ui->startparameter('name', 'post');
    $content = $ui->escaped('content', 'post');

    $table = array();

    // Collect the shorten we need for game modification
    $query = $sql->prepare("SELECT DISTINCT(`shorten`) FROM `servertypes` WHERE `resellerid`=?");
    $query->execute(array($resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[] = array('shorten' => $row['shorten']);
    }

    if (!$ui->smallletters('action', 2, 'post')) {

        if ($ui->st('d', 'get') == 'ad') {

            $template_file = 'admin_gserver_file_template_add.tpl';

        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT `steamgame`,`name`,`content` FROM `gserver_file_templates` WHERE `templateID`=? AND `resellerID`=? AND `userID` IS NULL");
            $query->execute(array($id, $resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $servertype = $row['servertype'];
                $name = $row['name'];
                $content = $row['content'];
            }

            $template_file = ($query->rowCount() > 0) ? 'admin_images_md.tpl' : 'admin_404.tpl';

        } else {
            $template_file = 'admin_404.tpl';
        }

    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$servertype) {
            $errors['servertype'] = $sprache->steam;
        }
        if (!$name) {
            $errors['name'] = $sprache->mods;
        }
        if (!$content) {
            $errors['content'] = 'Autoupdate';
        }

        if ($servertype and $ui->smallletters('action',2, 'post') == 'ad') {

            $query = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($shorten, $resellerLockupID));
            if ($query->rowCount() > 0) {
                $errors['shorten'] = $sprache->abkuerz;
            }

        } else if ($servertype and $ui->smallletters('action',2, 'post') == 'md') {

            $query = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `id`!=? AND `shorten`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $shorten, $resellerLockupID));
            if ($query->rowCount() > 0) {
                $errors['shorten'] = $sprache->abkuerz;
            }

        } else {

            $errors['shorten'] = $sprache->abkuerz;
        }

        if (count($errors) == 0) {

            if ($ui->st('action', 'post') == 'ad') {

                $resellerInsertIDs = array();
                $rowCount = 0;

                if ($reseller_id == 0) {

                    $resellerInsertIDs[] = 0;

                    $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `accounttype`='r'");
                    $query->execute();

                } else {
                    $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='r'");
                    $query->execute(array($resellerLockupID));
                }

                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $resellerInsertIDs[] = (int) $row['id'];
                }

                $query = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
                $query2 = $sql->prepare("INSERT INTO `servertypes` (`iptables`,`protectedSaveCFGs`,`steamgame`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`map`,`mapGroup`,`workShop`,`cmd`,`modcmds`,`gameq`,`gamemod`,`gamemod2`,`configs`,`configedit`,`appID`,`portMax`,`portStep`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`protected`,`ramLimited`,`ftpAccess`,`os`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                foreach ($resellerInsertIDs as $rID) {

                    $query->execute(array($shorten, $rID));

                    if ($query->rowCount() == 0) {
                        $query2->execute(array($iptables, $protectedSaveCFGs, $steamgame, $updates, $shorten, $description, 'gserver', $gamebinary, $binarydir, $modfolder, $map, $mapGroup, $workShop, $cmd, $modcmds, $gameq, $gamemod, $gamemod2, $configs, $configedit, $appID, $portMax, $portStep, $portOne, $portTwo, $portThree, $portFour, $portFive, $protected, $ramLimited, $ftpAccess, $os, $rID));
                        $rowCount += $query2->rowCount();
                    }

                }

                $loguseraction = '%add% %template% ' . $shorten;

            } else if ($ui->st('action', 'post') == 'md') {

                $query = $sql->prepare("UPDATE `servertypes` SET `iptables`=?,`protectedSaveCFGs`=?,`steamgame`=?,`updates`=?,`shorten`=?,`description`=?,`gamebinary`=?,`binarydir`=?,`modfolder`=?,`map`=?,`mapGroup`=?,`workShop`=?,`cmd`=?,`modcmds`=?,`gameq`=?,`gamemod`=?,`gamemod2`=?,`configs`=?,`configedit`=?,`appID`=?,`portMax`=?,`portStep`=?,`portOne`=?,`portTwo`=?,`portThree`=?,`portFour`=?,`portFive`=?,`protected`=?,`ramLimited`=?,`ftpAccess`=?,`os`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($iptables, $protectedSaveCFGs, $steamgame, $updates, $shorten, $description, $gamebinary, $binarydir, $modfolder, $map, $mapGroup, $workShop, $cmd, $modcmds, $gameq, $gamemod, $gamemod2, $configs, $configedit, $appID, $portMax, $portStep, $portOne, $portTwo, $portThree, $portFour, $portFive, $protected, $ramLimited, $ftpAccess, $os, $ui->id('id', 10, 'get'), $resellerLockupID));
                $rowCount = $query->rowCount();
                $loguseraction = '%mod% %template% ' . $shorten;
            }

            if (isset($rowCount) and $rowCount > 0) {
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;
            } else {
                $template_file = $spracheResponse->error_table;
            }
        } else {

            unset($header, $text);
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_images_add.tpl' : 'admin_images_md.tpl';
        }
    }

} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->st('action', 'post')) {

        $query = $sql->prepare("SELECT `description` FROM `servertypes` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        $description = $query->fetchColumn();
        $template_file = ($description != '') ? 'admin_images_dl.tpl' : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'dl'){

        $query = $sql->prepare("SELECT `shorten` FROM `servertypes` WHERE id=? AND resellerid=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        $shorten = $query->fetchColumn();

        $query = $sql->prepare("DELETE FROM `servertypes` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));

        if ($query->rowCount() > 0) {
            $loguseraction = '%del% %template% ' . $shorten;
            $insertlog->execute();
            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = $spracheResponse->error_table;
        }

        $query = $sql->prepare("DELETE FROM `rservermasterg` WHERE `servertypeid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));

    } else {

        $template_file = 'admin_404.tpl';
    }

} else {
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'di') {
        $orderby = '`id` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = '`id` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = '`id` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = '`id` ASC';
    } else if ($ui->st('o', 'get') == 'dd') {
        $orderby = '`description` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = '`description` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = '`shorten` DESC';
    } else {
        $orderby = '`shorten` ASC';
        $o = 'as';
    }
    $query = $sql->prepare("SELECT `id`,`shorten`,`steamgame`,`description`,`type` FROM `servertypes` $where ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array(':reseller_id' => $resellerLockupID));
    $table = array();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[] = array('id' => $row['id'], 'shorten' => $row['shorten'], 'steamgame' => $row['steamgame'], 'type' => $row['type'], 'description' => $row['description']);
    }
    $next = $start + $amount;

    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `servertypes` $where");
    $query->execute(array(':reseller_id' => $resellerLockupID));
    $colcount = $query->fetchColumn();

    $vor = ($colcount>$next) ? $start + $amount : $start;
    $back = $start - $amount;
    $zur = ($back >= 0) ? $start - $amount : $start;
    $pageamount = ceil($colcount / $amount);
    $link = '<a href="admin.php?w=im&amp;d=md&amp;o=' . $o . '&amp;a=';
    $link .= (!isset($amount)) ? 20 : $amount;
    $link .= ($start == 0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';
    $pages[] = $link;
    $i = 2;
    while ($i <= $pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[] = ($start == $selectpage) ? '<a href="admin.php?w=im&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '&amp;o=' . $o . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=im&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '&amp;o=' . $o . '">' . $i . '</a>';
        $i++;
    }
    $pages = implode(', ', $pages);
    $template_file = 'admin_images_list.tpl';
}