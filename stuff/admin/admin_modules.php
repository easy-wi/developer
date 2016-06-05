<?php

/**
 * Ticket: https://github.com/easy-wi/developer/issues/61
 * File: admin_modules.php.
 * Author: Ulrich Block
 * Date: 22.09.13
 * Time: 12:21
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

if (!isset($admin_id) or !isset($reseller_id) or (isset($reseller_id) and $reseller_id != 0) or (isset($admin_id) and isset($pa) and !$pa['root'])) {
    header('Location: login.php');
    die;
}
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
$logreseller = 0;
$logsubuser = 0;
$sprache = getlanguagefile('modules', $user_language, $reseller_id);

unset($name);

# array with easy-wi core modules to prevent legacy issues and users from removing
$coreModules = array(
    1 => array('id' => 1, 'active' => 'Y', 'name' => $gsprache->gameserver, 'sub' => 'gs', 'type' => $sprache->type_core),
    2 => array('id' => 2, 'active' => 'Y', 'name' => 'Easy Anti Cheat', 'sub' => 'ea', 'type' => $sprache->type_core),
    3 => array('id' => 3, 'active' => 'Y', 'name' => 'MySQL', 'sub' => 'my', 'type' => $sprache->type_core),
    4 => array('id' => 4, 'active' => 'Y', 'name' => $gsprache->voiceserver, 'sub' => 'vo', 'type' => $sprache->type_core),
    5 => array('id' => 5, 'active' => 'Y', 'name' => $gsprache->lendserver, 'sub' => 'le', 'type' => $sprache->type_core),
    6 => array('id' => 6, 'active' => 'Y', 'name' => $gsprache->support, 'sub' => 'ti', 'type' => $sprache->type_core),
    7 => array('id' => 7, 'active' => 'Y', 'name' => 'Rootserver', 'sub' => 'ro', 'type' => $sprache->type_core),
    8 => array('id' => 8, 'active' => 'Y', 'name' => $gsprache->imprint, 'sub' => 'ip', 'type' => $sprache->type_core),
    9 => array('id' => 9, 'active' => 'Y', 'name' => 'CMS', 'sub' => 'pn', 'type' => $sprache->type_core),
    10 => array('id' => 10, 'active' => 'Y', 'name' => $gsprache->webspace, 'sub' => 'ws', 'type' => $sprache->type_core)
);

$query = $sql->prepare("SELECT `id` FROM `modules` WHERE `type`='C' AND `get`=? LIMIT 1");
$query2 = $sql->prepare("INSERT INTO `modules` (`file`,`get`,`sub`,`type`,`active`) VALUES ('',?,?,'C',?) ON DUPLICATE KEY UPDATE `active`=VALUES(`active`)");
$query3 = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('mo',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");

foreach ($coreModules as $module) {

    $query->execute(array($module['sub']));

    $coreModuleID = (int) $query->fetchColumn();

    if ($coreModuleID == 0) {

        $query2->execute(array($module['sub'], $module['sub'], 'Y'));

        $instertedID = $sql->lastInsertId();

        $query3->execute(array($instertedID, $user_language, $module['name'], 0));

    } else {
        $query3->execute(array($coreModuleID, $user_language, $module['name'], 0));
    }
}

if ($ui->st('action', 'post') and !token(true)) {

    unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_modules_ad.tpl' : 'admin_modules_md.tpl';

} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    $errors = array();

    $id = $ui->id('id', 10, 'get');
    $active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
    $langAvailable = getlanguages($template_to_use);

    if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

        $dbSuccess = false;
        $file = $ui->config('file', 'post');
        $sub = $ui->st('sub', 'post');
        $get = $ui->smallletters('get', 255, 'post');
        $type = $ui->w('type', 1, 'post');

        $files = array();
        $dir = EASYWIDIR . '/stuff/custom_modules/';

        if (is_dir($dir)){
            $dirs = scandir($dir);
            foreach ($dirs as $row) {
                if (substr($row, -4) == '.php') {
                    $files[] = $row;
                }
            }
        }

        if ($ui->st('action', 'post')) {

            $coreModuleFound = false;

            if (!$sub or !in_array($sub, array('gs', 'pa', 'mo', 'my', 'ro', 'ti', 'us', 'vo','ws')) ) {
                $errors['sub'] = $sprache->sub;
            }

            if (!$type or !in_array($type, array('A','P','U')) ) {
                $errors['type'] = $sprache->type;
            }

            if ($get and strlen($get) != 2) {
                $query = $sql->prepare("SELECT 1 FROM `modules` WHERE `get`=? AND `id`!=? LIMIT 1");
                $query->execute(array($get, $id));
                if ($query->rowCount() > 0) {
                    $errors['get'] = $sprache->get;
                }
            } else {
                $errors['get'] = $sprache->get;
            }

            if (in_array($file, $files)) {

                $query = $sql->prepare("SELECT 1 FROM `modules` WHERE `file`=? AND `id`!=? LIMIT 1");
                $query->execute(array($file, $id));
                if ($query->rowCount() > 0) {
                    $errors['file'] = $sprache->file;
                }

            } else {
                $errors['file'] = $sprache->file;
            }

            $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `modules` WHERE `type`='C' AND `id`=? LIMIT 1");
            $query->execute(array($id));

            if ($query->fetchColumn() == 1) {

                $errors = array();

                $coreModuleFound = true;
            }

            if (count($errors) == 0) {

                if ($ui->st('action', 'post') == 'md') {

                    if ($coreModuleFound) {
                        $query = $sql->prepare("UPDATE `modules` SET `active`=? WHERE `id`=? LIMIT 1");
                        $query->execute(array($active, $id));
                    } else {
                        $query = $sql->prepare("UPDATE `modules` SET `get`=?,`file`=?,`sub`=?,`active`=?,`type`=? WHERE `id`=? LIMIT 1");
                        $query->execute(array($get, $file, $sub, $active, $type, $id));
                    }

                    if ($query->rowCount() > 0) {
                        $dbSuccess = true;
                    }

                } else if (count($errors) == 0 and $ui->st('action', 'post') == 'ad') {
                    $query = $sql->prepare("INSERT INTO `modules` (`get`,`file`,`sub`,`active`,`type`) VALUES (?,?,?,?,?)");
                    $query->execute(array($get, $file, $sub, $active, $type));

                    if ($query->rowCount() > 0) {
                        $dbSuccess = true;
                    }

                    $id = $sql->lastInsertId();
                }

                if (!$coreModuleFound) {
                    if ($ui->smallletters('lang', 2, 'post')) {
                        $array = (array) $ui->smallletters('lang', 2, 'post');

                        $query = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('mo',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
                        foreach($array as $lang) {

                            if (small_letters_check($lang, 2)) {
                                $query->execute(array($id, $lang, $ui->description('translation', 'post', $lang), 0));

                                if ($dbSuccess === false and $query->rowCount() > 0) {
                                    $dbSuccess = true;
                                }
                            }
                        }

                        $query = $sql->prepare("SELECT `lang` FROM `translations` WHERE `type`='mo' AND `transID`=? AND `resellerID`=?");
                        $query2 = $sql->prepare("DELETE FROM `translations` WHERE `type`='mo' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
                        $query->execute(array($id, 0));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                            if (!in_array($row['lang'],$array)) {

                                $query2->execute(array($id, $row['lang'], 0));

                                if ($dbSuccess === false and $query2->rowCount() > 0) {
                                    $dbSuccess = true;
                                }
                            }
                        }

                    } else {

                        $query = $sql->prepare("DELETE FROM `translations` WHERE `type`='mo' AND `transID`=? AND `resellerID`=?");
                        $query->execute(array($id, 0));

                        if ($dbSuccess === false and $query->rowCount() > 0) {
                            $dbSuccess = true;
                        }
                    }
                }

                if ($dbSuccess === true) {
                    $loguseraction = ($ui->st('d', 'get') == 'md') ? '%mod% %modules% ' . $file : '%add% %modules% ' . $file;
                    $insertlog->execute();
                    $template_file = $spracheResponse->table_add;
                } else {
                    $template_file = $spracheResponse->error_table;
                }

            } else {
                unset($header, $text);
                $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_modules_ad.tpl' : 'admin_modules_md.tpl';
            }

        } else {

            if ($ui->st('d', 'get') == 'md') {

                $query = $sql->prepare("SELECT * FROM `modules` WHERE `id`=? LIMIT 1");
                $query->execute(array($id));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $active = $row['active'];
                    $file = $row['file'];
                    $sub = $row['sub'];
                    $get = $row['get'];
                    $type = $row['type'];
                    $found = true;
                }

                $languageTexts = array();

                foreach ($langAvailable as $lg) {
                    $languageTexts[$lg] = '';
                }

                if (isset($found)) {
                    $query = $sql->prepare("SELECT `text`,`lang` FROM `translations` WHERE `type`='mo' AND `transID`=?");
                    $query->execute(array($id));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['lang'] == $rSA['language']) {
                            $name = $row['text'];
                        }
                        $languageTexts[$row['lang']] = $row['text'];
                    }
                    if (!isset($name)) {
                        $name = $file;
                    }

                    $template_file = 'admin_modules_md.tpl';

                } else {
                    $template_file = 'admin_404.tpl';
                }

            } else {
                $template_file = 'admin_modules_ad.tpl';
            }
        }
    }

    if (!isset($template_file)) {
        $template_file = 'admin_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id',10, 'get');

    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `modules` WHERE `type`='C' AND `id`=? LIMIT 1");
    $query->execute(array($id));

    if ($query->fetchColumn() == 1) {

        $template_file = $sprache->error_core;

    } else  {

        $query = $sql->prepare("SELECT `file` FROM `modules` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        $moduleFile = $query->fetchColumn();

        if ($query->rowCount() > 0) {

            if ($ui->st('action', 'post') == 'dl') {
                $query = $sql->prepare("DELETE FROM `modules` WHERE `id`=? LIMIT 1");
                $query->execute(array($id));

                if ($query->rowCount() > 0) {
                    $template_file = $spracheResponse->table_del;
                    $loguseraction = '%del% %modules% '.$moduleFile;
                    $insertlog->execute();

                } else {
                    $template_file = $spracheResponse->error_table;
                }

            } else {
                $template_file = 'admin_modules_dl.tpl';
            }

        } else {
            $template_file = 'admin_404.tpl';
        }
    }
} else {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=custommodule');

    $template_file = 'admin_modules_list.tpl';
}