<?php

/**
 * File: images.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['gimages'])) {
	header('Location: admin.php');
	die('No acces');
}
include(EASYWIDIR . '/stuff/keyphrasefile.php');
$sprache = getlanguagefile('images', $user_language, $reseller_id);
$gssprache = getlanguagefile('gserver', $user_language, $reseller_id);
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
if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;

} else if ($ui->st('d', 'get') == 'ex' and $ui->id('id', 10, 'get')) {

    $xml=new DOMDocument('1.0', 'utf-8');
    $element = $xml->createElement('image');

    $query = $sql->prepare("SELECT * FROM `servertypes` WHERE `id`=? AND `resellerid`=?");
    $query->execute(array($ui->id('id', 10, 'get'), $reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $shorten = $row['shorten'];
        foreach ($row as $k=>$v) {
            if (!in_array($k, array('id', 'resellerid', 'steamVersion'))) {
                $key = $xml->createElement($k, $v);
                $element->appendChild($key);
            }
        }
    }

    $xml->appendChild($element);
    if (isset($shorten)) {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename = ${shorten}.xml");
        header("Content-Type: text/xml; charset=UTF-8");
        header("Content-Transfer-Encoding: binary");
        $xml->formatOutput = true;
        echo $xml->saveXML();
        die;

    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d', 'get') == 'ad' or ($ui->st('d', 'get') == 'md' and $ui->id('id', 10, 'get'))) {

    $errors = array();
    $id = $ui->id('id', 10, 'get');
    $shorten = $ui->gamestring('shorten', 'post');
    $steamgame = $ui->w('steamgame', 1, 'post');
    $updates = $ui->id('updates', 1, 'post');
    $gamebinary = $ui->startparameter('gamebinary', 'post');
    $cmd = $ui->startparameter('cmd', 'post');
    $iptables = $ui->startparameter('iptables', 'post');
    $protectedSaveCFGs = $ui->startparameter('protectedSaveCFGs', 'post');
    $description = $ui->startparameter('description', 'post');
    $binarydir = $ui->w('binarydir',20, 'post');
    $modfolder = $ui->w('modfolder',20, 'post');
    $map = $ui->mapname('map', 'post');
    $mapGroup = $ui->mapname('mapGroup', 'post');
    $configs = $ui->startparameter('configs', 'post');
    $configedit = $ui->startparameter('configedit', 'post');
    $modcmds = $ui->startparameter('modcmds', 'post');
    $qstat = $ui->w('qstat', 20, 'post');
    $gamemod = $ui->active('gamemod', 'post');
    $gamemod2 = $ui->w('gamemod2', 10, 'post');
    $qstatpassparam = $ui->startparameter('qstatpassparam', 'post');
    $portMax = ($ui->id('portMax', 1, 'post')) ? $ui->id('portMax', 1, 'post') : 1;
    $portStep = ($ui->id('portStep',4, 'post')) ? $ui->id('portStep',4, 'post') : 100;
    $portOne = ($ui->id('portOne',5, 'post')) ? $ui->id('portOne',5, 'post') : 27015;
    $portTwo = ($ui->id('portTwo',5, 'post')) ? $ui->id('portTwo',5, 'post') : null;
    $portThree = ($ui->id('portThree',5, 'post')) ? $ui->id('portThree',5, 'post') : null;
    $portFour = ($ui->id('portFour',5, 'post')) ? $ui->id('portFour',5, 'post') : null;
    $portFive = ($ui->id('portFive',5, 'post')) ? $ui->id('portFive',5, 'post') : null;
    $appID = ($ui->id('appID', 19, 'post')) ? $ui->id('appID', 19, 'post') : null;
    $protected = ($ui->active('protected', 'post')) ? $ui->active('protected', 'post') : 'N';
    $iptables = $ui->startparameter('iptables', 'post');
    $protectedSaveCFGs = $ui->startparameter('protectedSaveCFGs', 'post');
    
    if (!$ui->smallletters('action', 2, 'post') or $ui->id('import', 1, 'post') == 1) {

        if ($ui->st('d', 'get') == 'ad') {
            $token=token();
            $query = $sql->prepare("SELECT DISTINCT(`shorten`) FROM `servertypes` WHERE `resellerid`=?");
            $query->execute(array($reseller_id));
            $table = array();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $table[] = array('shorten' => $row['shorten']);
            $query = $sql->prepare("SELECT `qstat`,`description` FROM `qstatshorten` ORDER BY `description`");
            $query->execute();
            $table2 = array();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $table2[] = array('qstat' => $row['qstat'], 'description' => $row['description']);
            }
            
            if ($ui->id('import', 1, 'post') == 1 and $_FILES['file']['error']==0 and $_FILES['file']['type'] == 'text/xml') {

                try {
                    $xml = new DOMDocument();

                    if (@$xml->load($_FILES['file']['tmp_name']) !== false) {
                        $childNodes = $xml->documentElement;
                        foreach ($childNodes->childNodes AS $node) {
                            if ($node->nodeName == 'shorten') {
                                $shorten = $node->nodeValue;
                            }
                            if ($node->nodeName == 'steamgame') {
                                $steamgame = $node->nodeValue;
                            }
                            if ($node->nodeName == 'appID') {
                                $appID = $node->nodeValue;
                            }
                            if ($node->nodeName == 'updates ') {
                                $updates = $node->nodeValue;
                            }
                            if ($node->nodeName == 'description') {
                                $description = $node->nodeValue;
                            }
                            if ($node->nodeName == 'type') {
                                $type = $node->nodeValue;
                            }
                            if ($node->nodeName == 'gamebinary') {
                                $gamebinary = $node->nodeValue;
                            }
                            if ($node->nodeName == 'binarydir') {
                                $binarydir = $node->nodeValue;
                            }
                            if ($node->nodeName == 'modfolder') {
                                $modfolder = $node->nodeValue;
                            }
                            if ($node->nodeName == 'fps') {
                                $fps = $node->configs;
                            }
                            if ($node->nodeName == 'slots') {
                                $slots = $node->nodeValue;
                            }
                            if ($node->nodeName == 'modcmds') {
                                $modcmds = $node->nodeValue;
                            }
                            if ($node->nodeName == 'tic') {
                                $tic = $node->nodeValue;
                            }
                            if ($node->nodeName == 'qstat') {
                                $qstat = $node->nodeValue;
                            }
                            if ($node->nodeName == 'gamemod') {
                                $gamemod = $node->nodeValue;
                            }
                            if ($node->nodeName == 'gamemod2') {
                                $gamemod2 = $node->nodeValue;
                            }
                            if ($node->nodeName == 'configs') {
                                $configs = $node->nodeValue;
                            }
                            if ($node->nodeName == 'configedit') {
                                $configedit = $node->nodeValue;
                            }
                            if ($node->nodeName == 'qstatpassparam') {
                                $qstatpassparam = $node->nodeValue;
                            }
                            if ($node->nodeName == 'portStep') {
                                $portStep = $node->nodeValue;
                            }
                            if ($node->nodeName == 'portMax') {
                                $portMax = $node->nodeValue;
                            }
                            if ($node->nodeName == 'portOne') {
                                $portOne = $node->nodeValue;
                            }
                            if ($node->nodeName == 'portTwo') {
                                $portTwo = $node->nodeValue;
                            }
                            if ($node->nodeName == 'portThree') {
                                $portThree = $node->nodeValue;
                            }
                            if ($node->nodeName == 'portFour') {
                                $portFour = $node->nodeValue;
                            }
                            if ($node->nodeName == 'portFive') {
                                $portFive = $node->nodeValue;
                            }
                            if ($node->nodeName == 'cmd') {
                                $cmd = $node->nodeValue;
                            }
                            if ($node->nodeName == 'protected') {
                                $protected = $node->nodeValue;
                            }
                            if ($node->nodeName == 'protectedSaveCFGs') {
                                $protectedSaveCFGs = $node->nodeValue;
                            }
                            if ($node->nodeName == 'iptables') {
                                $iptables = $node->nodeValue;
                            }
                            if ($node->nodeName == 'mapGroup') {
                                $mapGroup = $node->nodeValue;
                            }
                        }
                    }
                } catch(Exception $error) {
                    $active = '';
                }
            }

            $query = $sql->prepare("SELECT `qstat`,`description` FROM `qstatshorten`");
            $query->execute();
            $table3 = array();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $selected = (isset($qstat) and $qstat == $row['qstat']) ? ' selected="selected"' : '';
                $table3[] = array('option' => '<option value="' . $row['qstat'] . '" ' . $selected . '>' . $row['description'] . '</option>');
            }
            $template_file = 'admin_images_add.tpl';

        } else if ($ui->st('d', 'get') == 'md' and $id) {
            $query = $sql->prepare("SELECT * FROM `servertypes` WHERE `id`=? AND `resellerid`=?");
            $query->execute(array($id, $reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $steamgame = $row['steamgame'];
                $updates = $row['updates'];
                $shorten = $row['shorten'];
                $description = $row['description'];
                $gamebinary = $row['gamebinary'];
                $binarydir = $row['binarydir'];
                $modfolder = $row['modfolder'];
                $fps = $row['fps'];
                $map = $row['map'];
                $mapGroup = $row['mapGroup'];
                $cmd = $row['cmd'];
                $modcmds = $row['modcmds'];
                $tic = $row['tic'];
                $qstat = $row['qstat'];
                $gamemod = $row['gamemod'];
                $gamemod2 = $row['gamemod2'];
                $configs = $row['configs'];
                $configedit = $row['configedit'];
                $qstatpassparam = $row['qstatpassparam'];
                $appID = $row['appID'];
                $portMax = $row['portMax'];
                $portStep = $row['portStep'];
                $portOne = $row['portOne'];
                $portTwo = $row['portTwo'];
                $portThree = $row['portThree'];
                $portFour = $row['portFour'];
                $portFive = $row['portFive'];
                $protected = $row['protected'];
                $iptables = $row['iptables'];
                $protectedSaveCFGs = $row['protectedSaveCFGs'];
            }

            if ($query->rowCount() > 0) {
                $query = $sql->prepare("SELECT distinct(`shorten`) FROM `servertypes` WHERE `resellerid`=?");
                $query->execute(array($reseller_id));
                $table = array();
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $table[] = array('shorten' => $row['shorten']);
                }

                $query = $sql->prepare("SELECT `qstat`,`description` FROM `qstatshorten`");
                $query->execute();
                $table3 = array();
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $selected = (isset($qstat) and $qstat == $row['qstat']) ? ' selected="selected"' : '';
                    $table3[] = array('option' => '<option value="' . $row['qstat'] . '" ' . $selected . '>' . $row['description'] . '</option>');
                }
                $template_file = 'admin_images_md.tpl';
            } else {
                $template_file = 'admin_404.tpl';
            }


        } else {
            $template_file = 'admin_404.tpl';
        }

    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$ui->w('steamgame', 1, 'post')) {
            $errors['steamgame'] = $sprache->steam;
        }
        if (!$ui->active('gamemod', 'post')) {
            $errors['gamemod'] = $sprache->mods;
        }
        if (!$ui->id('updates', 1, 'post')) {
            $errors['updates'] = 'Autoupdate';
        }
        if (!$ui->startparameter('cmd', 'post')) {
            $errors['cmd'] = $sprache->start;
        }
        if (!$ui->startparameter('gamebinary', 'post')) {
            $errors['gamebinary'] = $sprache->bin;
        }
        
        if ($ui->gamestring('shorten', 'post') and $ui->smallletters('action',2, 'post') == 'ad') {
            
            $query = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($shorten, $reseller_id));
            if ($query->rowCount()>0) {
                $errors['shorten'] = $sprache->abkuerz;
            }
            
        } else if ($ui->gamestring('shorten', 'post') and $ui->smallletters('action',2, 'post') == 'md') {
            
            $query = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `id`!=? AND `shorten`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $shorten, $reseller_id));
            if ($query->rowCount()>0) {
                $errors['shorten'] = $sprache->abkuerz;
            }
            
        } else {
            
            $errors['shorten'] = $sprache->abkuerz;
        }
        
        if (count($errors) == 0) {

            if ($ui->st('action', 'post') == 'ad' and $reseller_id == 0) {

                $query = $sql->prepare("INSERT INTO `servertypes` (`iptables`,`protectedSaveCFGs`,`steamgame`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`map`,`mapGroup`,`cmd`,`modcmds`,`qstat`,`gamemod`,`gamemod2`,`configs`,`configedit`,`qstatpassparam`,`appID`,`portMax`,`portStep`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`protected`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $query->execute(array($iptables, $protectedSaveCFGs, $steamgame, $updates, $shorten, $description, 'gserver', $gamebinary, $binarydir, $modfolder, $map, $mapGroup, $cmd, $modcmds, $qstat, $gamemod, $gamemod2, $configs, $configedit, $qstatpassparam, $appID, $portMax, $portStep, $portOne, $portTwo, $portThree, $portFour, $portFive, $protected, $reseller_id));
                $rowCount = $query->rowCount();
                $loguseraction = '%add% %template% ' . $shorten;

            } else if ($ui->st('action', 'post') == 'md') {
                
                $query = $sql->prepare("UPDATE `servertypes` SET `iptables`=?,`protectedSaveCFGs`=?,`steamgame`=?,`updates`=?,`shorten`=?,`description`=?,`gamebinary`=?,`binarydir`=?,`modfolder`=?,`map`=?,`mapGroup`=?,`cmd`=?,`modcmds`=?,`qstat`=?,`gamemod`=?,`gamemod2`=?,`configs`=?,`configedit`=?,`qstatpassparam`=?,`appID`=?,`portMax`=?,`portStep`=?,`portOne`=?,`portTwo`=?,`portThree`=?,`portFour`=?,`portFive`=?,`protected`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($iptables, $protectedSaveCFGs, $steamgame, $updates, $shorten, $description, $gamebinary, $binarydir, $modfolder, $map, $mapGroup, $cmd, $modcmds, $qstat, $gamemod, $gamemod2, $configs, $configedit, $qstatpassparam, $appID, $portMax, $portStep, $portOne, $portTwo, $portThree, $portFour, $portFive, $protected, $ui->id('id', 10, 'get'), $reseller_id));
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
        $query->execute(array($id, $reseller_id));
        $description = $query->fetchColumn();
        $template_file = ($description != '') ? 'admin_images_dl.tpl' : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'dl'){

        $query = $sql->prepare("SELECT `shorten` FROM `servertypes` WHERE id=? AND resellerid=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        $shorten = $query->fetchColumn();

        $query = $sql->prepare("DELETE FROM `servertypes` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));

        if ($query->rowCount()>0) {
            $loguseraction = '%del% %template% ' . $shorten;
            $insertlog->execute();
            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = $spracheResponse->error_table;
        }

        $query = $sql->prepare("DELETE FROM `rservermasterg` WHERE `servertypeid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));

    } else {

        $template_file = 'admin_404.tpl';
    }

} else {
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'ds') {
        $orderby = '`shorten` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = '`shorten` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = '`id` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = '`id` ASC';
    } else if ($ui->st('o', 'get') == 'dd') {
        $orderby = '`description` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = '`description` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = '`id` DESC';
    } else {
        $orderby = '`id` ASC';
        $o = 'ai';
    }
    $query = $sql->prepare("SELECT `id`,`shorten`,`steamgame`,`description`,`type` FROM `servertypes` $where ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array(':reseller_id' => $reseller_id));
    $table = array();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[] = array('id' => $row['id'], 'shorten' => $row['shorten'], 'steamgame' => $row['steamgame'], 'type' => $row['type'], 'description' => $row['description']);
    }
    $next = $start + $amount;

    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `servertypes` $where");
    $query->execute(array(':reseller_id' => $reseller_id));
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