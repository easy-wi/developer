<?php

/**
 * File: global_gserver_file_templates.php.
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

if (isset($userInclude)) {

    $targetFile = 'userpanel.php';
    $loguserid = $user_id;
    $logusername = getusername($user_id);
    $logusertype = 'user';
    $logreseller = 0;

    if (isset($admin_id)) {
        $logsubuser = $admin_id;
    } else if (isset($subuser_id)) {
        $logsubuser = $subuser_id;
    } else {
        $logsubuser = 0;
    }

} else if (isset($adminInclude)) {

    $targetFile = 'admin.php';

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
}

if (!isset($main) or $main != 1 or !isset($targetFile) or !$pa['gserver']) {
    $targetFile = (isset($targetFile)) ? $targetFile : 'login.php';
    header('Location: ' . $targetFile . '');
    die;
}

$sprache = getlanguagefile('images', $user_language, $resellerLockupID);

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->st('d', 'get') == 'ad' or ($ui->st('d', 'get') == 'md' and $ui->id('id', 10, 'get'))) {

    $errors = array();

    $id = (int) $ui->id('id', 10, 'get');

    $servertype = $ui->gamestring('servertype', 'post');
    $name = $ui->startparameter('name', 'post');
    $content = $ui->escaped('content', 'post');

    $table = array();

    $query = $sql->prepare("SELECT `shorten`,`description` FROM `servertypes` WHERE `resellerid`=? GROUP BY `shorten`,`description` ORDER BY `shorten`");
    $query->execute(array($resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[$row['shorten']] = $row['description'];
    }

    if (!$ui->smallletters('action', 2, 'post')) {

        if ($ui->st('d', 'get') == 'ad') {

            $template_file = 'global_gserver_file_template_add.tpl';

        } else if ($ui->st('d', 'get') == 'md' and $id) {

            if (isset($adminInclude)) {
                $query = $sql->prepare("SELECT `servertype`,`name`,`content` FROM `gserver_file_templates` WHERE `templateID`=? AND `resellerID`=? AND `userID` IS NULL LIMIT 1");
                $query->execute(array($id, $resellerLockupID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $servertype = $row['servertype'];
                    $name = $row['name'];
                    $content = $row['content'];
                }
            } else {
                $query = $sql->prepare("SELECT `servertype`,`name`,`content` FROM `gserver_file_templates` WHERE `templateID`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($id, $user_id, $resellerLockupID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $servertype = $row['servertype'];
                    $name = $row['name'];
                    $content = $row['content'];
                }
            }

            $template_file = ($query->rowCount() > 0) ? 'global_gserver_file_template_md.tpl' : 'admin_404.tpl';

        } else {
            $template_file = 'admin_404.tpl';
        }

    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$servertype or !isset($table[$servertype])) {
            $errors['servertype'] = $sprache->game;
        }

        if (!$name) {
            $errors['name'] = $sprache->description;
        }

        if (!$content) {
            $errors['content'] = $gsprache->template;
        }

        if ($name and $ui->smallletters('action',2, 'post') == 'ad') {
            if (isset($adminInclude)) {
                $query = $sql->prepare("SELECT `templateID` FROM `gserver_file_templates` WHERE `name`=? AND `servertype`=? AND `resellerID`=? AND `userID` IS NULL LIMIT 1");
                $query->execute(array($name, $servertype, $resellerLockupID));
                if ($query->rowCount() > 0) {
                    $errors['name'] = $sprache->description;
                    $errors['servertype'] = $sprache->game;
                }
            } else {
                $query = $sql->prepare("SELECT `templateID` FROM `gserver_file_templates` WHERE `name`=? AND `servertype`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($name, $servertype, $user_id, $resellerLockupID));
                if ($query->rowCount() > 0) {
                    $errors['name'] = $sprache->description;
                    $errors['servertype'] = $sprache->game;
                }
            }

        } else if ($name and $ui->smallletters('action',2, 'post') == 'md') {
            if (isset($adminInclude)) {
                $query = $sql->prepare("SELECT `templateID` FROM `gserver_file_templates` WHERE `templateID`!=? AND `name`=? AND `servertype`=? AND `resellerID`=? AND `userID` IS NULL LIMIT 1");
                $query->execute(array($id, $name, $servertype, $resellerLockupID));
                if ($query->rowCount() > 0) {
                    $errors['name'] = $sprache->description;
                    $errors['servertype'] = $sprache->game;
                }
            } else {
                $query = $sql->prepare("SELECT `templateID` FROM `gserver_file_templates` WHERE `templateID`!=? AND `name`=? AND `servertype`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($id, $name, $servertype, $user_id, $resellerLockupID));
                if ($query->rowCount() > 0) {
                    $errors['name'] = $sprache->description;
                    $errors['servertype'] = $sprache->game;
                }
            }

        }

        if (count($errors) == 0) {

            if ($ui->st('action', 'post') == 'ad') {

                if (isset($adminInclude)) {
                    $query = $sql->prepare("INSERT INTO `gserver_file_templates` (`userID`,`servertype`,`name`,`content`,`resellerID`) VALUES (NULL,?,?,?,?)");
                    $query->execute(array($servertype, $name, $content, $resellerLockupID));
                } else {
                    $query = $sql->prepare("INSERT INTO `gserver_file_templates` (`userID`,`servertype`,`name`,`content`,`resellerID`) VALUES (?,?,?,?,?)");
                    $query->execute(array($user_id, $servertype, $name, $content, $resellerLockupID));
                }

                $rowCount = $query->rowCount();
                $loguseraction = '%add% %gserver% %file% %template% ' . $name;

            } else if ($ui->st('action', 'post') == 'md') {

                if (isset($adminInclude)) {
                    $query = $sql->prepare("UPDATE `gserver_file_templates` SET `servertype`=?,`name`=?,`content`=? WHERE `templateID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($servertype, $name, $content, $id, $resellerLockupID));
                } else {
                    $query = $sql->prepare("UPDATE `gserver_file_templates` SET `servertype`=?,`name`=?,`content`=? WHERE `templateID`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($servertype, $name, $content, $id, $user_id, $resellerLockupID));
                }

                $rowCount = $query->rowCount();
                $loguseraction = '%mod% %gserver% %file% %template% ' . $name;
            }

            if (isset($rowCount) and $rowCount > 0) {
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;
            } else {
                $template_file = $spracheResponse->error_table;
            }

        } else {

            unset($header, $text);
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'global_gserver_file_template_add.tpl' : 'global_gserver_file_template_md.tpl';
        }
    }

} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->st('action', 'post')) {

        if (isset($adminInclude)) {
            $query = $sql->prepare("SELECT `name` FROM `gserver_file_templates` WHERE `templateID`=? AND `userID` IS NULL AND `resellerID`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
        } else {
            $query = $sql->prepare("SELECT `name` FROM `gserver_file_templates` WHERE `templateID`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($id, $user_id, $resellerLockupID));
        }

        $name = $query->fetchColumn();

        $template_file = ($name != '') ? 'global_gserver_file_template_dl.tpl' : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'dl'){

        if (isset($adminInclude)) {
            $query = $sql->prepare("SELECT `name` FROM `gserver_file_templates` WHERE templateID=? AND `userID` IS NULL AND resellerID=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
        } else {
            $query = $sql->prepare("SELECT `name` FROM `gserver_file_templates` WHERE templateID=? AND `userID`=? AND resellerID=? LIMIT 1");
            $query->execute(array($id, $user_id, $resellerLockupID));
        }

        $name = $query->fetchColumn();

        if (isset($adminInclude)) {
            $query = $sql->prepare("DELETE FROM `gserver_file_templates` WHERE `templateID`=? AND `userID` IS NULL AND `resellerID`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
        } else {
            $query = $sql->prepare("DELETE FROM `gserver_file_templates` WHERE `templateID`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($id, $user_id, $resellerLockupID));
        }

        if ($query->rowCount() > 0) {
            $loguseraction = '%del% %template% ' . $name;
            $insertlog->execute();
            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = $spracheResponse->error_table;
        }

    } else {

        $template_file = 'admin_404.tpl';
    }

} else {

    $o = $ui->st('o', 'get');

    if ($ui->st('o', 'get') == 'di') {
        $orderby = '`id` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = '`id` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = '`servertype` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = '`servertype` ASC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = '`name` DESC';
    } else {
        $orderby = '`name` ASC';
        $o = 'as';
    }

    if (isset($adminInclude)) {
        $query = $sql->prepare("SELECT COUNT(`templateID`) AS `amount` FROM `gserver_file_templates` WHERE `userID` IS NULL AND `resellerID`=?");
        $query->execute(array($resellerLockupID));
    } else {
        $query = $sql->prepare("SELECT COUNT(`templateID`) AS `amount` FROM `gserver_file_templates` WHERE `userID`=? AND `resellerID`=?");
        $query->execute(array($user_id, $resellerLockupID));
    }
    $colcount = $query->fetchColumn();

    $start = (isset($start) and $start < $colcount) ? $start : 0;
    $amount = (isset($amount)) ? $amount : 20;
    $next = $start + $amount;
    $vor = ($colcount > $next) ? $start + $amount : $start;
    $back = $start - $amount;
    $zur = ($back >= 0) ? $start - $amount : $start;
    $pageamount = ceil($colcount / $amount);

    if (isset($adminInclude)) {
        $query = $sql->prepare("SELECT `templateID`,`name`,`servertype` FROM `gserver_file_templates` WHERE `userID` IS NULL AND `resellerID`=? ORDER BY $orderby LIMIT $start,$amount");
        $query->execute(array($resellerLockupID));
    } else {
        $query = $sql->prepare("SELECT `templateID`,`name`,`servertype` FROM `gserver_file_templates` WHERE `userID`=? AND `resellerID`=? ORDER BY $orderby LIMIT $start,$amount");
        $query->execute(array($user_id, $resellerLockupID));
    }
    $table = array();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[] = array('id' => $row['templateID'], 'name' => $row['name'], 'servertype' => $row['servertype']);
    }

    $link = '<a href="' . $targetFile . '?w=gt&amp;o=' . $o . '&amp;a=';
    $link .= (!isset($amount)) ? 20 : $amount;
    $link .= ($start == 0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';
    $pages[] = $link;
    $i = 2;
    while ($i <= $pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[] = ($start == $selectpage) ? '<a href="' . $targetFile . '?w=gt&amp;a=' . $amount . '&p=' . $selectpage . '&amp;o=' . $o . '" class="bold">' . $i . '</a>' : '<a href="' . $targetFile . '?w=gt&amp;a=' . $amount . '&p=' . $selectpage . '&amp;o=' . $o . '">' . $i . '</a>';
        $i++;
    }
    $pages = implode(', ', $pages);
    $template_file = 'global_gserver_file_template_list.tpl';
}