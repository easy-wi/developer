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
include(EASYWIDIR . '/third_party/gameq/GameQ.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('images', $user_language, $resellerLockupID);
$rsprache = getlanguagefile('roots', $user_language, $resellerLockupID);
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

    $token = token();

	unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_images_add.tpl' : 'admin_images_md.tpl';

} else if ($ui->st('d', 'get') == 'ex' and $ui->id('id', 10, 'get')) {

    $xml = new DOMDocument('1.0', 'utf-8');
    $element = $xml->createElement('image');

    $query = $sql->prepare("SELECT * FROM `servertypes` WHERE `id`=? AND `resellerid`=?");
    $query->execute(array($ui->id('id', 10, 'get'), $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $shorten = $row['shorten'];
        foreach ($row as $k => $v) {
            if (!in_array($k, array('id', 'resellerid', 'steamVersion', 'downloadPath'))) {
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
    $gamebinaryWin = $ui->startparameter('gamebinaryWin', 'post');
    $cmd = $ui->startparameter('cmd', 'post');
    $iptables = $ui->startparameter('iptables', 'post');
    $protectedSaveCFGs = $ui->startparameter('protectedSaveCFGs', 'post');
    $description = $ui->startparameter('description', 'post');
    $binarydir = $ui->folder('binarydir', 'post');
    $modfolder = $ui->folder('modfolder', 'post');
    $map = $ui->mapname('map', 'post');
    $mapGroup = $ui->mapname('mapGroup', 'post');
    $configs = $ui->startparameter('configs', 'post');
    $configedit = $ui->startparameter('configedit', 'post');
    $modcmds = $ui->startparameter('modcmds', 'post');
    $gameq = $ui->w('gameq', 255, 'post');
    $gamemod2 = $ui->w('gamemod2', 10, 'post');
    $gamemod = ($ui->active('gamemod', 'post')) ? $ui->active('gamemod', 'post') : 'N';
    $portMax = ($ui->id('portMax', 1, 'post')) ? $ui->id('portMax', 1, 'post') : 1;
    $portStep = ($ui->id('portStep', 4, 'post')) ? $ui->id('portStep',4, 'post') : 100;
    $portOne = ($ui->id('portOne', 5, 'post')) ? $ui->id('portOne', 5, 'post') : 27015;
    $portTwo = ($ui->id('portTwo', 5, 'post')) ? $ui->id('portTwo', 5, 'post') : null;
    $portThree = ($ui->id('portThree', 5, 'post')) ? $ui->id('portThree', 5, 'post') : null;
    $portFour = ($ui->id('portFour', 5, 'post')) ? $ui->id('portFour', 5, 'post') : null;
    $portFive = ($ui->id('portFive', 5, 'post')) ? $ui->id('portFive', 5, 'post') : null;
    $useQueryPort = ($ui->id('useQueryPort', 1, 'post')) ? $ui->id('useQueryPort', 1, 'post') : 1;
    $appID = ($ui->id('appID', 19, 'post')) ? $ui->id('appID', 19, 'post') : null;
    $protected = ($ui->active('protected', 'post')) ? $ui->active('protected', 'post') : 'N';
    $ramLimited = ($ui->active('ramLimited', 'post')) ? $ui->active('ramLimited', 'post') : 'N';
    $workShop = ($ui->active('workShop', 'post')) ? $ui->active('workShop', 'post') : 'N';
    $ftpAccess = ($ui->active('ftpAccess', 'post')) ? $ui->active('ftpAccess', 'post') : 'Y';
    $os = ($ui->w('os', 1, 'post')) ? $ui->w('os', 1, 'post') : 'L';
    $iptables = $ui->startparameter('iptables', 'post');
    $protectedSaveCFGs = $ui->startparameter('protectedSaveCFGs', 'post');
    $steamAccount = $ui->username('steamAccount', 255, 'post');
    $steamPassword = $ui->password('steamPassword', 255, 'post');

    // Add jQuery plugin chosen to the header
    $htmlExtraInformation['css'][] = '<link href="css/adminlte/chosen/chosen.min.css" rel="stylesheet" type="text/css">';
    $htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/chosen/chosen.jquery.min.js" type="text/javascript"></script>';
    
    if (!$ui->smallletters('action', 2, 'post') or $ui->id('import', 1, 'post') == 1) {

        // Protocol list code taken from https://github.com/Austinb/GameQ/blob/v2/examples/list.php
        $protocols_path = GAMEQ_BASE . 'gameq/protocols/';

        // Grab the dir with all the classes available
        $dir = dir($protocols_path);

        $protocols = array();

        // Now lets loop the directories
        while (false !== ($entry = $dir->read()))
        {
            if(!is_file($protocols_path.$entry))
            {
                continue;
            }

            // Figure out the class name
            $class_name = 'GameQ_Protocols_' . ucfirst(pathinfo($entry, PATHINFO_FILENAME));

            // Lets get some info on the class
            $reflection = new ReflectionClass($class_name);

            // Check to make sure we can actually load the class
            try {

                if(!$reflection->IsInstantiable()) {
                    continue;
                }

                // Load up the class so we can get info
                $class = new $class_name;

                // Add it to the list
                $protocols[$class->name()] = $class->name_long();

                // Unset the class
                unset($class);
            } catch (ReflectionException $e) {
                $errors['reflection'] = $e->getMessage();
            }

        }

        // Close the directory
        unset($dir);

        ksort($protocols);

        // GameQ protocol listing done. Easy-WI Code again.
        if ($ui->st('d', 'get') == 'ad') {

            $table = array();

            $token = token();

            // Collect the shorten we need for game modification
            $query = $sql->prepare("SELECT DISTINCT(`shorten`) FROM `servertypes` WHERE `resellerid`=?");
            $query->execute(array($resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $table[] = array('shorten' => $row['shorten']);
            }
            
            if ($ui->id('import', 1, 'post') == 1 and $_FILES['file']['error'] == 0 and $_FILES['file']['type'] == 'text/xml') {

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
                            if ($node->nodeName == 'gamebinaryWin') {
                                $gamebinaryWin = $node->nodeValue;
                            }
                            if ($node->nodeName == 'binarydir') {
                                $binarydir = $node->nodeValue;
                            }
                            if ($node->nodeName == 'modfolder') {
                                $modfolder = $node->nodeValue;
                            }
                            if ($node->nodeName == 'fps') {
                                $fps = $node->nodeValue;
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
                            if ($node->nodeName == 'gameq') {
                                $gameq = $node->nodeValue;
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
                            if ($node->nodeName == 'ramLimited') {
                                $ramLimited = $node->nodeValue;
                            }
                            if ($node->nodeName == 'ftpAccess') {
                                $ftpAccess = $node->nodeValue;
                            }
                            if ($node->nodeName == 'os') {
                                $os = $node->nodeValue;
                            }
                            if ($node->nodeName == 'useQueryPort') {
                                $useQueryPort = $node->nodeValue;
                            }
                        }
                    }
                } catch(Exception $error) {
                    $active = '';
                }
            }

            $template_file = 'admin_images_add.tpl';

        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT *,AES_DECRYPT(`steam_account`,?) AS `steamAcc`,AES_DECRYPT(`steam_password`,?) AS `steamPwd` FROM `servertypes` WHERE `id`=? AND `resellerid`=?");
            $query->execute(array($aeskey, $aeskey, $id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $steamgame = $row['steamgame'];
                $updates = $row['updates'];
                $shorten = $row['shorten'];
                $description = $row['description'];
                $gamebinary = $row['gamebinary'];
                $gamebinaryWin = $row['gamebinaryWin'];
                $binarydir = $row['binarydir'];
                $modfolder = $row['modfolder'];
                $fps = $row['fps'];
                $map = $row['map'];
                $mapGroup = $row['mapGroup'];
                $cmd = $row['cmd'];
                $modcmds = $row['modcmds'];
                $tic = $row['tic'];
                $gameq = $row['gameq'];
                $gamemod = $row['gamemod'];
                $gamemod2 = $row['gamemod2'];
                $configs = $row['configs'];
                $configedit = $row['configedit'];
                $appID = $row['appID'];
                $portMax = $row['portMax'];
                $portStep = $row['portStep'];
                $portOne = $row['portOne'];
                $portTwo = $row['portTwo'];
                $portThree = $row['portThree'];
                $portFour = $row['portFour'];
                $portFive = $row['portFive'];
                $useQueryPort = $row['useQueryPort'];
                $protected = $row['protected'];
                $iptables = $row['iptables'];
                $protectedSaveCFGs = $row['protectedSaveCFGs'];
                $os = $row['os'];
                $ftpAccess = $row['ftpAccess'];
                $workShop = $row['workShop'];
                $ramLimited = $row['ramLimited'];
                $steamAccount = $row['steamAcc'];
                $steamPassword = $row['steamPwd'];
            }

            if ($query->rowCount() > 0) {

                $query = $sql->prepare("SELECT DISTINCT(`shorten`) FROM `servertypes` WHERE `resellerid`=?");
                $query->execute(array($resellerLockupID));
                $table = array();
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $table[] = array('shorten' => $row['shorten']);
                }

                $template_file = 'admin_images_md.tpl';

            } else {
                $template_file = 'admin_404.tpl';
            }


        } else {
            $template_file = 'admin_404.tpl';
        }

    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$steamgame) {
            $errors['steamgame'] = $sprache->steam;
        }
        if (!$gamemod) {
            $errors['gamemod'] = $sprache->mods;
        }
        if (!$updates) {
            $errors['updates'] = 'Autoupdate';
        }
        if (!$cmd) {
            $errors['cmd'] = $sprache->start;
        }
        if (!$gamebinary and $os != 'W') {
            $errors['gamebinary'] = $sprache->bin;
        }
        if (!$gamebinaryWin and $os != 'L') {
            $errors['gamebinaryWin'] = $sprache->bin;
        }
        
        if ($ui->gamestring('shorten', 'post') and $ui->smallletters('action',2, 'post') == 'ad') {

            $query = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `shorten`=? AND (`os`=? OR `os`='B') AND `resellerid`=? LIMIT 1");
            $query->execute(array($shorten, $os, $resellerLockupID));

            if ($query->rowCount() > 0) {
                $errors['shorten'] = $sprache->abkuerz;
            }
            
        } else if ($ui->gamestring('shorten', 'post') and $ui->smallletters('action',2, 'post') == 'md') {
            
            $query = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `id`!=? AND `shorten`=? AND (`os`=? OR `os`='B') AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $shorten, $os, $resellerLockupID));

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
                $query2 = $sql->prepare("INSERT INTO `servertypes` (`iptables`,`protectedSaveCFGs`,`steamgame`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`gamebinaryWin`,`binarydir`,`modfolder`,`map`,`mapGroup`,`workShop`,`cmd`,`modcmds`,`gameq`,`gamemod`,`gamemod2`,`configs`,`configedit`,`appID`,`portMax`,`portStep`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`useQueryPort`,`protected`,`ramLimited`,`ftpAccess`,`os`,`steam_account`,`steam_password`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,AES_ENCRYPT(?,?),AES_ENCRYPT(?,?),?)");

                foreach ($resellerInsertIDs as $rID) {

                    $query->execute(array($shorten, $rID));

                    if ($query->rowCount() == 0) {
                        $query2->execute(array($iptables, $protectedSaveCFGs, $steamgame, $updates, $shorten, $description, 'gserver', $gamebinary, $gamebinaryWin, $binarydir, $modfolder, $map, $mapGroup, $workShop, $cmd, $modcmds, $gameq, $gamemod, $gamemod2, $configs, $configedit, $appID, $portMax, $portStep, $portOne, $portTwo, $portThree, $portFour, $portFive, $useQueryPort, $protected, $ramLimited, $ftpAccess, $os, $steamAccount, $aeskey, $steamPassword, $aeskey, $rID));
                        $rowCount += $query2->rowCount();
                    }

                }

                $loguseraction = '%add% %template% ' . $shorten;
                $actionText = $gsprache->add;

            } else if ($ui->st('action', 'post') == 'md') {
                
                $query = $sql->prepare("UPDATE `servertypes` SET `iptables`=?,`protectedSaveCFGs`=?,`steamgame`=?,`updates`=?,`shorten`=?,`description`=?,`gamebinary`=?,`gamebinaryWin`=?,`binarydir`=?,`modfolder`=?,`map`=?,`mapGroup`=?,`workShop`=?,`cmd`=?,`modcmds`=?,`gameq`=?,`gamemod`=?,`gamemod2`=?,`configs`=?,`configedit`=?,`appID`=?,`portMax`=?,`portStep`=?,`portOne`=?,`portTwo`=?,`portThree`=?,`portFour`=?,`portFive`=?,`useQueryPort`=?,`protected`=?,`ramLimited`=?,`ftpAccess`=?,`os`=?,`steam_account`=AES_ENCRYPT(?,?),`steam_password`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($iptables, $protectedSaveCFGs, $steamgame, $updates, $shorten, $description, $gamebinary, $gamebinaryWin, $binarydir, $modfolder, $map, $mapGroup, $workShop, $cmd, $modcmds, $gameq, $gamemod, $gamemod2, $configs, $configedit, $appID, $portMax, $portStep, $portOne, $portTwo, $portThree, $portFour, $portFive, $useQueryPort, $protected, $ramLimited, $ftpAccess, $os, $steamAccount, $aeskey, $steamPassword, $aeskey, $ui->id('id', 10, 'get'), $resellerLockupID));
                $rowCount = $query->rowCount();
                $loguseraction = '%mod% %template% ' . $shorten;

                $actionText = $gsprache->mod;
            }

            if (isset($rowCount) and $rowCount > 0) {
                $insertlog->execute();
                $template_file = $actionText . ' ' . $spracheResponse->table_add;
            } else {
                $template_file = $actionText . ' ' . $spracheResponse->error_table;
            }
        } else {

            $token = token();
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

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=gameimages');

    $template_file = 'admin_images_list.tpl';
}