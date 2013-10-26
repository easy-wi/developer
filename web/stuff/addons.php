<?php

/**
 * File: addons.php.
 * Author: Ulrich Block
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

if ((!isset($admin_id) or !$main == 1) or (isset($admin_id) and !$pa['addons'])) {
	header('Location: admin.php');
	die('No acces');
}

$sprache = getlanguagefile('images', $user_language, $reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
    $logsubuser = (isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller = 0;
}
if ($reseller_id != 0 and $admin_id != $reseller_id) {
    $reseller_id = $admin_id;
}

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;

// A simple exporter. Offers the current addon settings as download
} else if ($ui->st('d', 'get') == 'ex' and $ui->id('id', 10, 'get')) {

    $xml = new DOMDocument('1.0','utf-8');
    $element = $xml->createElement('addon');

    $query = $sql->prepare("SELECT * FROM `addons` WHERE `id`=? AND `resellerid`=?");
    $query->execute(array($ui->id('id', 10, 'get'), $reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $addon = $row['addon'];
        foreach ($row as $k => $v) {
            if (!in_array($k, array('id','resellerid','depending'))) {
                $key = $xml->createElement($k, $v);
                $element->appendChild($key);
            }
        }
    }

    $xml->appendChild($element);

    if (isset($addon)) {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename = ${addon}.xml");
        header("Content-Type: text/xml; charset=UTF-8");
        header("Content-Transfer-Encoding: binary");
        $xml->formatOutput = true;
        echo $xml->saveXML();
        die;
    } else {
        $template_file = 'admin_404.tpl';
    }

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // At this point all variables are defined that can come from the user
    $id = $ui->id('id', 10, 'get');
    $shortens = (array) $ui->id('shorten', 10, 'post');
    $type = (string) $ui->smallletters('type', 99, 'post');
    $addon = (string) $ui->gamestring('addon', 'post');
    $folder = (string) $ui->folder('folders', 'post');
    $active = (string) $ui->active('active', 'post');
    $menudescription = (string) $ui->description('menudescription', 'post');
    $configs = (string) $ui->startparameter('configs', 'post');
    $cmd = (string) $ui->startparameter('cmd', 'post');
    $rmcmd = (string) $ui->startparameter('rmcmd', 'post');

    // Default variables. Mostly needed for the add operation
    $gamesAssigned = array();
    $dependings = array();
    $foundLanguages = array();
    $default_language = $rSA['language'];
    $paddon = ($ui->active('paddon', 'post')) ? (string) $ui->active('paddon', 'post') : 'N';
    $depending = ($ui->id('depending',19, 'post')) ? (int) $ui->id('depending', 19, 'post') : 0;

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post') or $ui->id('import', 1, 'post')) {



        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad' or $ui->id('import',1, 'post') == 1) {

            $token = token();

            $query = $sql->prepare("SELECT `id`,`menudescription` FROM `addons` WHERE `type`='tool' AND `resellerid`=? ORDER BY `menudescription`");
            $query->execute(array($reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $dependings[] = '<option value="'.$row['id'].'">'.$row['menudescription'].'</option>';
            }

            if ($ui->id('import',1, 'post') == 1 and $_FILES["file"]["error"] == 0 and $_FILES["file"]["type"] == 'text/xml') {
                $shorten = $_FILES["file"]["name"];
                try {
                    $xml=new DOMDocument();
                    if (@$xml->load($_FILES["file"]["tmp_name"]) !== false) {
                        $childNodes = $xml->documentElement;
                        foreach ($childNodes->childNodes AS $node) {
                            if ($node->nodeName == 'active') {
                                $active = $node->nodeValue;
                            }
                            if ($node->nodeName == 'paddon') {
                                $paddon = $node->nodeValue;
                            }
                            if ($node->nodeName == 'addon') {
                                $addon = $node->nodeValue;
                            }
                            if ($node->nodeName == 'type') {
                                $type = $node->nodeValue;
                            }
                            if ($node->nodeName == 'folder') {
                                $folder = $node->nodeValue;
                            }
                            if ($node->nodeName == 'menudescription') {
                                $menudescription = $node->nodeValue;
                            }
                            if ($node->nodeName == 'configs') {
                                $configs = $node->nodeValue;
                            }
                            if ($node->nodeName == 'cmd') {
                                $cmd = $node->nodeValue;
                            }
                            if ($node->nodeName == 'rmcmd') {
                                $rmcmd = $node->nodeValue;
                            }
                        }
                    }
                } catch(Exception $error) {
                    $active = '';
                }
            }
            $template_file = 'admin_addons_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT * FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $shorten = $row['shorten'];
                $type = $row['type'];
                $addon = $row['addon'];
                $paddon = $row['paddon'];
                $folder = $row['folder'];
                $active = $row['active'];
                $configs = $row['configs'];
                $menudescription = $row['menudescription'];
                $cmd = $row['cmd'];
                $rmcmd = $row['rmcmd'];
                $depending = $row['depending'];
            }

            $rowCount = $query->rowCount();

            $query = $sql->prepare("SELECT `servertype_id` FROM `addons_allowed` WHERE `addon_id`=? AND `reseller_id`=?");
            $query->execute(array($id, $reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $shortens[] = $row['servertype_id'];
            }
            $rowCount += $query->rowCount();

            // Check if database entry exists and if not display 404 page
            $template_file =  ($rowCount > 0) ? 'admin_addons_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {


        if (!$ui->active('active', 'post')) {
            $errors['active'] = $sprache->active;
        }
        if (!$ui->smallletters('type', 99, 'post')) {
            $errors['type'] = $sprache->type;
        }
        if (!$ui->description('menudescription', 'post')) {
            $errors['menudescription'] = $sprache->addon2;
        }
        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `addons` WHERE `addon`=? AND `id`!=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($addon, (int) $id, $reseller_id));
        if ($query->fetchColumn() > 0) {
            $errors['addon'] = $sprache->addon;
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `addons` (`type`,`addon`,`paddon`,`folder`,`active`,`menudescription`,`configs`,`cmd`,`rmcmd`,`depending`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $query->execute(array($type, $addon, $paddon, $folder, $active, $menudescription, $configs, $cmd, $rmcmd, $depending, $reseller_id));

                $id = $sql->lastInsertId();

                $rowCount = $query->rowCount();
                $loguseraction = '%add% %addon% ' . $addon;

            } else if ($ui->st('action', 'post') == 'md') {

                $query = $sql->prepare("UPDATE `addons` SET `menudescription`=?,`active`=?,`folder`=?,`addon`=?,`paddon`=?,`type`=?,`configs`=?,`cmd`=?,`rmcmd`=?,`depending`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($menudescription, $active, $folder, $addon, $paddon, $type, $configs, $cmd, $rmcmd, $depending, $id, $reseller_id));

                $rowCount = $query->rowCount();
                $loguseraction = '%mod% %addon% ' . $addon;
            }

            // Insert and update translations
            if ($id > 0 and $ui->smallletters('language', 2, 'post')) {

                $array = (array) $ui->smallletters('language', 2, 'post');
                $query = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('ad',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
                foreach($array as $language) {
                    if (small_letters_check($language, 2)) {
                        $description = $ui->description('description', 'post', $language);
                        $query->execute(array($id, $language, $description, $reseller_id));

                        $rowCount += $query->rowCount();
                    }
                }

                $query = $sql->prepare("SELECT `lang` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `resellerID`=?");
                $query2 = $sql->prepare("DELETE FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($id, $reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (!in_array($row['lang'], $array)) {
                        $query2->execute(array($id, $row['lang'], $reseller_id));

                        $rowCount += $query2->rowCount();
                    }
                }

            } else {
                $query = $sql->prepare("DELETE FROM `translations` WHERE `type`='ad' AND `transID`=? AND `resellerID`=?");
                $query->execute(array($id, $reseller_id));

                $rowCount += $query->rowCount();
            }

            // Insert and update game relations
            if ($id > 0 and count($shortens) > 0) {
                $query = $sql->prepare("INSERT INTO `addons_allowed` (`addon_id`,`servertype_id`,`reseller_id`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `addon_id`=`addon_id`");
                foreach ($shortens as $shorten) {
                    $query->execute(array($id, $shorten, $reseller_id));
                    $rowCount += $query->rowCount();
                }

                $query = $sql->prepare("SELECT `servertype_id` FROM `addons_allowed` WHERE `addon_id`=? AND `reseller_id`=?");
                $query2 = $sql->prepare("DELETE FROM `addons_allowed` WHERE `addon_id`=? AND `servertype_id`=? AND `reseller_id`=? LIMIT 1");
                $query->execute(array($id, $reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (!in_array($row['servertype_id'], $shortens)) {
                        $query2->execute(array($id, $row['servertype_id'], $reseller_id));

                        $rowCount += $query2->rowCount();
                    }
                }

            } else {
                $query = $sql->prepare("DELETE FROM `addons_allowed` WHERE `addon_id`=? AND `reseller_id`=?");
                $query->execute(array($id, $reseller_id));

                $rowCount += $query->rowCount();
            }


            // Check if a row was affected during insert or update
            if (isset($rowCount) and $rowCount > 0) {

                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }

            // An error occurred during validation unset the redirect information and display the form again
        } else {
            unset($header, $text);

            $token = token();

            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_addons_add.tpl' : 'admin_addons_md.tpl';
        }
    }

    $query = $sql->prepare("SELECT `id`,`description` FROM `servertypes` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $gamesAssigned[$row['id']] = $row['description'];
    }

    $query = $sql->prepare("SELECT `id`,`menudescription` FROM `addons` WHERE `type`='tool' AND `type`=? AND `resellerid`=? ORDER BY `menudescription`");
    $query->execute(array($type,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $dependings[] = (isset($depending) and $depending == $row['id']) ? '<option value="' . $row['id'] . '" selected="selected">' . $row['menudescription'] . '</option>' : '<option value="' . $row['id'] . '">' . $row['menudescription'] . '</option>';
    }

    $query = $sql->prepare("SELECT `lang`,`text` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
    foreach ($languages as $row) {
        if (small_letters_check($row, 2)) {
            unset($lang);
            $description = '';

            $query->execute(array($id, $row,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $lang = $row2['lang'];
                $description = $row2['text'];
            }

            if (isset($lang)) {
                $style = '';
                $displayNone = '';
                $checkbox = '<input type="checkbox" name="language[]" value="' . $row . '" onclick="textdrop(' . "'" . $row . "'" . ');" checked>';
            } else {
                $displayNone = 'display_none';
                $style = 'style="display: none;"';
                $checkbox = '<input type="checkbox" name="language[]" value="' . $row . '" onclick="textdrop(' . "'" . $row . "'" . ');">';
            }
            $foundLanguages[] = array('style' => $style,'lang' => $row,'checkbox' => $checkbox,'description' => $description,'display' => $displayNone);
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

    // Define the ID variable which will be used at the form and SQLs
    $id = $ui->id('id', 10, 'get');

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        $query = $sql->prepare("SELECT `menudescription` FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        $menudescription = $query->fetchColumn();

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_addons_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl') {

        $query = $sql->prepare("SELECT `addon` FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        $addon = $query->fetchColumn();

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount()>0) {
            $query = $sql->prepare("DELETE FROM `addons_allowed` WHERE `addon_id`=? AND `reseller_id`=?");
            $query->execute(array($id, $reseller_id));
            $query = $sql->prepare("DELETE FROM `addons_installed` WHERE `addonid`=? AND `resellerid`=?");
            $query->execute(array($id, $reseller_id));
            $query = $sql->prepare("DELETE FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $reseller_id));
            $query = $sql->prepare("DELETE FROM `translations` WHERE `type`='ad' AND `transID`=? AND `resellerID`=?");
            $query->execute(array($id, $reseller_id));

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% %addon% ' . $addon;
            $insertlog->execute();

            // Nothing was deleted, display an error
        } else {
            $template_file = $spracheResponse->error_table;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

// List the available entries
} else {

    $table = array();

    if (!isset($start)) {
        $start = 0;
    }
    if (!isset($amount)) {
        $amount = 20;
    }

    $o = (string) $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'ds') {
        $orderby = '`active` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = '`active` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = '`type` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = '`type` ASC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = '`menudescription` DESC';
    } else if ($ui->st('o', 'get') == 'an') {
        $orderby = '`menudescription` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = '`shorten` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = '`shorten` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = '`id` DESC';
    } else{
        $o = 'ai';
        $orderby = '`id` ASC';
    }

    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `addons` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    $colcount = $query->fetchColumn();

    $next = $start + $amount;
    $vor = ($colcount>$next) ? $next : $start;

    $back = $start - $amount;
    $zur = ($back >= 0) ? ($start - $amount) : $start;

    $pageamount = ceil($colcount / $amount);
    $link = '<a href="admin.php?w=ad&amp;d=md&amp;o=' . $o . '&amp;a=' . $amount;
    $link .= ($start == 0) ? '&amp;p=0" class="bold">1</a>' : '&amp;p=0">1</a>';
    $i = 2;
    $pages[] = $link;
    while ($i <= $pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[] = ($start == $selectpage) ? '<a href="admin.php?w=ad&amp;d=md&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=ad&amp;d=md&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        $i++;
    }
    $pages = implode(', ', $pages);

    $query = $sql->prepare("SELECT `id`,`menudescription`,`active`,`type` FROM `addons` WHERE `resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
    $query2 = $sql->prepare("SELECT GROUP_CONCAT(DISTINCT s.`shorten` ORDER BY s.`shorten` ASC SEPARATOR ', ') AS `list`, COUNT(s.`id`) AS `amount` FROM `addons_allowed` AS a INNER JOIN `servertypes` AS s ON a.`servertype_id`=s.`id` WHERE a.`addon_id`=? AND s.`resellerid`=?");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $gamesList = '(0)';

        if ($row['active'] == 'Y') {
            $imgName = '16_ok';
            $imgAlt = 'Active';
        } else {
            $imgName = '16_bad';
            $imgAlt = 'Inactive';
        }

        $query2->execute(array($row['id'], $reseller_id));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $gamesList = '(' . $row2['amount'] . ') ' . $row2['list'];
        }

        if (strlen($gamesList) > 40) {
            $gamesList = substr($gamesList, 0, 40) . '...';
        }

        $table[] = array('id' => $row['id'], 'active' => $row['active'], 'img' => $imgName,'alt' => $imgAlt, 'gametype' => $gamesList, 'description' => $row['menudescription'], 'type' => ($row['type'] == 'map') ? $sprache->map : $sprache->tool);
    }

    $template_file = 'admin_addons_list.tpl';
}