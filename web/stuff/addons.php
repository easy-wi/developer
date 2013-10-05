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
$sprache = getlanguagefile('images',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id==0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller = 0;
}
if ($reseller_id != 0 and $admin_id != $reseller_id) $reseller_id=$admin_id;
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d','get') == 'ex' and $ui->id('id', 10, 'get')) {
    $xml=new DOMDocument('1.0','utf-8');
    $element=$xml->createElement('addon');
    $query = $sql->prepare("SELECT * FROM `addons` WHERE `id`=? AND `resellerid`=?");
    $query->execute(array($ui->id('id', 10, 'get'),$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $addon=$row['addon'];
        foreach ($row as $k=>$v) {
            if (!in_array($k, array('id','resellerid','depending'))) {
                $key=$xml->createElement($k,$v);
                $element->appendChild($key);
            }
        }
    }
    $xml->appendChild($element);
    if (isset($addon)) {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=${addon}.xml");
        header("Content-Type: text/xml; charset=UTF-8");
        header("Content-Transfer-Encoding: binary");
        $xml->formatOutput = true;
        echo $xml->saveXML();
        die;
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d','get') == 'ad') {
    if ($ui->smallletters('action',2,'post') == 'ad' and $ui->id('import',1,'post')!=1) {
        $fail = 0;
        $template_file = '';
        if(!$ui->gamestring('shorten','post')) {
            $template_file .="Shorten<br />";
            $fail = 1;
        }
        if(!$ui->smallletters('type',99,'post')) {
            $template_file .="type<br />";
            $fail = 1;
        }
        if(!$ui->gamestring('addon','post')) {
            $template_file .="Addon<br />";
            $fail = 1;
        }
        if(!$ui->description('menudescription','post')) {
            $template_file .="Menuescription<br />";
            $fail = 1;
        }
        if(!$ui->active('paddon','post')) {
            $fail = 1;
        }
        if(!$ui->id('depending',19,'post') and $ui->escaped('depending','post') != 0) {
            $fail = 1;
        }
        if ($fail!=1){
            $shorten=$ui->gamestring('shorten','post');
            $type=$ui->smallletters('type',99,'post');
            $addon=$ui->gamestring('addon','post');
            $paddon=$ui->active('paddon','post');
            $depending=$ui->escaped('depending','post');
            $folder=$ui->folder('folders','post');
            $active=$ui->active('active','post');
            $menudescription=$ui->description('menudescription','post');
            $configs=$ui->startparameter('configs','post');
            $cmd=$ui->startparameter('cmd','post');
            $rmcmd=$ui->startparameter('rmcmd','post');
            if ($reseller_id==0) {
                $query2 = $sql->prepare("SELECT `id` FROM `userdata` WHERE `accounttype`='r'");
                $query2->execute();
                $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `addons` WHERE ((`shorten`=? AND `addon`=?) OR `folder`=?) AND `resellerid`=? LIMIT 1");
                $query->execute(array($shorten,$addon,$folder,$reseller_id));
                if ($query->fetchColumn()<=0) {
                    $query = $sql->prepare("INSERT INTO `addons` (`shorten`,`type`,`addon`,`paddon`,`folder`,`active`,`menudescription`,`configs`,`cmd`,`rmcmd`,`depending`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                    $query->execute(array($shorten,$type,$addon,$paddon,$folder,$active,$menudescription,$configs,$cmd,$rmcmd,$depending,$reseller_id));
                    $query = $sql->prepare("SELECT `id` FROM `addons` WHERE `shorten`=? AND `addon`=? AND `menudescription`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($shorten,$addon,$menudescription,$reseller_id));
                    $id=$query->fetchColumn();
                    $query = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('ad',?,?,?,?) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
                    if ($ui->smallletters('language',2,'post')) {
                        $array=(array)$ui->smallletters('language',2,'post');
                        foreach($array as $language) {
                            if (small_letters_check($language, '2')) {
                                $query->execute(array($id,$language,$ui->description("description_$language",'post'),$reseller_id));
                            }
                        }
                    }
                    $template_file = $sprache->addon_add;
                } else {
                    $template_file = 'Error: Addon with the same name already exists';
                }
            } else {
                $query2 = $sql->prepare("SELECT `id` FROM `userdata` WHERE `accounttype`='r' AND `resellerid`=? LIMIT 1");
                $query2->execute(array($reseller_id));
            }
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $resellerid=$row['id'];
                $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `addons` WHERE ((`shorten`=? AND `addon`=?) OR `folder`=?) AND `resellerid`=? LIMIT 1");
                $query->execute(array($shorten,$addon,$folder,$resellerid));
                if ($query->fetchColumn()<=0) {
                    $query = $sql->prepare("INSERT INTO `addons` (`shorten`,`type`,`addon`,`paddon`,`folder`,`active`,`menudescription`,`configs`,`cmd`,`rmcmd`,`depending`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                    $query->execute(array($shorten,$type,$addon,$paddon,$folder,$active,$menudescription,$configs,$cmd,$rmcmd,$depending,$resellerid));
                    $query = $sql->prepare("SELECT `id` FROM `addons` WHERE `shorten`=? AND `addon`=? AND `menudescription`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($shorten,$addon,$menudescription,$resellerid));
                    $id=$query->fetchColumn();
                    if ($ui->smallletters('language',2,'post')) {
                        $array=(array)$ui->smallletters('language',2,'post');
                        $query = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('ad',?,?,?,?) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
                        foreach($array as $language) {
                            if (small_letters_check($language, '2')) {
                                $query->execute(array($id,$language,$ui->description("description_$language",'post'),$resellerid));
                            }
                        }
                    }
                    $template_file = $sprache->addon_add;
                }
            }
            if (!isset($template_file)) {
                $template_file = $sprache->error_exist;
            }
            $loguseraction="%add% %addon% $addon";
            $insertlog->execute();
        } else {
            $template_file = "Error: ".$template_file;
        }
    } else {
        $token=token();
        $table = array();
        $query = $sql->prepare("SELECT `shorten`,`description` FROM `servertypes` WHERE `resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[]=array('shorten' => $row['shorten'],'description' => $row['description']);
        }
        $query = $sql->prepare("SELECT `qstat`,`description` FROM `qstatshorten`");
        $query->execute();
        $table2 = array();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $exist=$row['qstat'];
            $query2 = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `servertypes` WHERE `qstat`=? AND `resellerid`=? LIMIT 1");
            $query2->execute(array($exist,$reseller_id));
            if ($query2->fetchColumn()>0) {
                $table2[]=array('qstat' => $row['qstat'],'description' => $row['description']);
            }
        }
        $foundlanguages = array();
        foreach ($languages as $row) {
            if (small_letters_check($row, '2')) {
                if ($row==$rSA['language']) {
                    $style = '';
                    $displayNone = '';
                    $checkbox='<input type="checkbox" name="language[]" value="'.$row.'" onclick="textdrop('."'".$row."'".');" checked /> ';
                } else {
                    $style='style="display: none;"';
                    $displayNone='display_none';
                    $checkbox='<input type="checkbox" name="language[]" value="'.$row.'" onclick="textdrop('."'".$row."'".');" /> ';
                }
                $foundlanguages[]=array('style' => $style,'lang' => $row,'checkbox' => $checkbox,'display' => $displayNone);
            }
        }
        $dependings = array();
        $query = $sql->prepare("SELECT `id`,`menudescription` FROM `addons` WHERE `type`='tool' AND `resellerid`=? ORDER BY `menudescription`");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $dependings[] = '<option value="'.$row['id'].'">'.$row['menudescription'].'</option>';
        }
        $active = '';
        $paddon = '';
        $shorten = '';
        $addon = '';
        $type = '';
        $folder = '';
        $menudescription = '';
        $configs = '';
        $cmd = '';
        $rmcmd = '';
        if ($ui->id('import',1,'post')==1 and $_FILES["file"]["error"]==0 and $_FILES["file"]["type"] == 'text/xml') {
            $shorten=$_FILES["file"]["name"];
            try {
                $xml=new DOMDocument();
                if (@$xml->load($_FILES["file"]["tmp_name"])!==false) {
                    $childNodes=$xml->documentElement;
                    foreach ($childNodes->childNodes AS $node) {
                        if($node->nodeName == 'active') $active=$node->nodeValue;
                        if($node->nodeName == 'paddon ') $paddon=$node->nodeValue;
                        if($node->nodeName == 'shorten') $shorten=$node->nodeValue;
                        if($node->nodeName == 'addon') $addon=$node->nodeValue;
                        if($node->nodeName == 'type') $type=$node->nodeValue;
                        if($node->nodeName == 'folder') $folder=$node->nodeValue;
                        if($node->nodeName == 'menudescription') $menudescription=$node->nodeValue;
                        if($node->nodeName == 'configs') $configs=$node->configs;
                        if($node->nodeName == 'cmd') $cmd=$node->nodeValue;
                        if($node->nodeName == 'rmcmd') $rmcmd=$node->nodeValue;
                    }
                }
            } catch(Exception $error) {
                $active = '';
            }
        }
        $template_file = "admin_addons_add.tpl";
    }
} else if ($ui->st('d','get') == 'dl' and $ui->id('id','30','get')) {
    $addonid=$ui->id('id','30','get');
    if (!isset($action)) {
        $query = $sql->prepare("SELECT `menudescription` FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($addonid,$reseller_id));
        $menudescription=$query->fetchColumn();
        $template_file = "admin_addons_dl.tpl";
    } else if ($action == 'dl'){
        $query = $sql->prepare("SELECT menudescription,type,folder,addon FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($addonid,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $menudescription=$row['menudescription'];
            $type=$row['type'];
            $folder=$row['folder'];
            $addon=$row['addon'];
        }
        $query = $sql->prepare("DELETE FROM `addons_installed` WHERE `addonid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($addonid,$reseller_id));
        $query = $sql->prepare("DELETE FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($addonid,$reseller_id));
        $query = $sql->prepare("DELETE FROM `translations` WHERE `type`='ad' AND `transID`=? AND `resellerID`=?");
        $query->execute(array($addonid,$reseller_id));
        $loguseraction="%del% %addon% $addon";
        $insertlog->execute();
        $template_file = $sprache->addon_del;
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d','get') == 'md' and $ui->id('id','30','get')) {
    $addonid=$ui->id('id','30','get');
    if (!isset($action)) {
        $table = array();
        $table2 = array();
        $query = $sql->prepare("SELECT `shorten`,`description` FROM `servertypes` WHERE `resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[]=array('shorten' => $row['shorten'],'description' => $row['description']);
        }
        $query = $sql->prepare("SELECT `qstat`,`description` FROM `qstatshorten`");
        $countp=$sql->prepare("SELECT `id` FROM `servertypes` WHERE `qstat`=? AND `resellerid`=? LIMIT 1");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $exist=$row['qstat'];
            $countp->execute(array($exist,$reseller_id));
            $exnum=$countp->rowCount();
            if ($exnum>=1) {
                $table2[]=array('qstat' => $row['qstat'],'description' => $row['description']);
            }
        }
        $query = $sql->prepare("SELECT * FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($addonid,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $shorten=$row['shorten'];
            $type=$row['type'];
            $addon=$row['addon'];
            $paddon=$row['paddon'];
            $folder=$row['folder'];
            $active=$row['active'];
            $configs=$row['configs'];
            $menudescription=$row['menudescription'];
            $cmd=$row['cmd'];
            $rmcmd=$row['rmcmd'];
            $depending=$row['depending'];
        }
        $default_language=$rSA['language'];
        $foundlanguages = array();
        $query = $sql->prepare("SELECT `lang`,`text` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
        foreach ($languages as $row) {
            if (small_letters_check($row, '2')) {
                unset($lang);
                $description = '';
                $query->execute(array($addonid, $row,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    $lang=$row2['lang'];
                    $description=$row2['text'];
                }
                if (isset($lang)) {
                    $style = '';
                    $displayNone = '';
                    $checkbox="<input type=\"checkbox\" name=\"language[]\" value=\"$row\" onclick=\"textdrop('$row');\" checked /> ";
                } else {
                    $displayNone='display_none';
                    $style="style=\"display: none;\"";
                    $checkbox="<input type=\"checkbox\" name=\"language[]\" value=\"$row\" onclick=\"textdrop('$row');\" /> ";
                }
                $foundlanguages[]=array('style' => $style,'lang' => $row,'checkbox' => $checkbox,'description' => $description,'display' => $displayNone);
            }
        }
        $dependings = array();
        $query = $sql->prepare("SELECT `id`,`menudescription` FROM `addons` WHERE `type`='tool' AND `type`=? AND `resellerid`=? ORDER BY `menudescription`");
        $query->execute(array($type,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($depending) and $depending==$row['id']) $dependings[] = '<option value="'.$row['id'].'" selected="selected">'.$row['menudescription'].'</option>';
            else $dependings[] = '<option value="'.$row['id'].'">'.$row['menudescription'].'</option>';
        }
        $template_file = "admin_addons_md.tpl";
    } else if ($action == 'md'){
        $fail = 0;
        $template_file = '';
        if(!$ui->gamestring('shorten','post')) {
            $template_file .="Shorten<br />";
            $fail = 1;
        }
        if(!$ui->smallletters('type',99,'post')) {
            $template_file .="type<br />";
            $fail = 1;
        }
        if(!$ui->gamestring('addon','post')) {
            $template_file .="Addon<br />";
            $fail = 1;
        }
        if(!$ui->description('menudescription','post')) {
            $template_file .="Menuescription<br />";
            $fail = 1;
        }
        if(!$ui->active('paddon','post')) {
            $fail = 1;
        }
        if(!$ui->id('depending',19,'post') and $ui->escaped('depending','post') != 0) {
            $fail = 1;
        }
        if ($fail!=1){
            $shorten=$ui->gamestring('shorten','post');
            $type=$ui->smallletters('type',99,'post');
            $addon=$ui->gamestring('addon','post');
            $paddon=$ui->active('paddon','post');
            $depending=$ui->escaped('depending','post');
            $folder=$ui->folder('folders','post');
            $active=$ui->active('active','post');
            $menudescription=$ui->description('menudescription','post');
            $configs=$ui->startparameter('configs','post');
            $cmd=$ui->startparameter('cmd','post');
            $rmcmd=$ui->startparameter('rmcmd','post');
            $query = $sql->prepare("UPDATE `addons` SET `shorten`=?,`menudescription`=?,`active`=?,`folder`=?,`addon`=?,`paddon`=?,`type`=?,`configs`=?,`cmd`=?,`rmcmd`=?,`depending`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($shorten,$menudescription,$active,$folder,$addon,$paddon,$type,$configs,$cmd,$rmcmd,$depending,$addonid,$reseller_id));
            if ($ui->smallletters('language',2,'post')) {
                $array=(array)$ui->smallletters('language',2,'post');
                $query2 = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('ad',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
                foreach($array as $language) {
                    if (small_letters_check($language, '2')) {
                        $description=$ui->description("description_$language",'post');
                        $query2->execute(array($addonid,$language,$description,$reseller_id));
                    }
                }
                $query = $sql->prepare("SELECT `lang` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `resellerID`=?");
                $query->execute(array($addonid,$reseller_id));
                $query2 = $sql->prepare("DELETE FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    if (!in_array($row2['lang'],$array)) {
                        $query2->execute(array($addonid, $row2['lang'],$reseller_id));
                    }
                }
            } else {
                $query = $sql->prepare("DELETE FROM `translations` WHERE `type`='ad' AND `transID`=? AND `resellerID`=?");
                $query->execute(array($addonid,$reseller_id));
            }
            $template_file = $sprache->addon_ud;
            $loguseraction="%mod% %addon% $addon";
            $insertlog->execute();
        } else {
            $template_file = "Error:<br />".$template_file;
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else {
    $o=$ui->st('o','get');
    if ($ui->st('o','get') == 'ds') {
        $orderby='`active` DESC';
    } else if ($ui->st('o','get') == 'as') {
        $orderby='`active` ASC';
    } else if ($ui->st('o','get') == 'dt') {
        $orderby='`type` DESC';
    } else if ($ui->st('o','get') == 'at') {
        $orderby='`type` ASC';
    } else if ($ui->st('o','get') == 'dn') {
        $orderby='`menudescription` DESC';
    } else if ($ui->st('o','get') == 'an') {
        $orderby='`menudescription` ASC';
    } else if ($ui->st('o','get') == 'dt') {
        $orderby='`shorten` DESC';
    } else if ($ui->st('o','get') == 'at') {
        $orderby='`shorten` ASC';
    } else if ($ui->st('o','get') == 'di') {
        $orderby='`id` DESC';
    } else{
        $o='ai';
        $orderby='`id` ASC';
    }
    $table = array();
    $pselect=$sql->prepare("SELECT `id`,`menudescription`,`shorten`,`active`,`type` FROM `addons` $where ORDER BY $orderby LIMIT $start,$amount");
    $pselect->execute(array(':reseller_id' => $reseller_id));
    foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $atype = '';
        $gtype = '';
        $shorten=$row['shorten'];
        $pselect2=$sql->prepare("SELECT `description` FROM `qstatshorten` WHERE `qstat`=? LIMIT 1");
        $pselect2->execute(array($shorten));
        foreach ($pselect2->fetchAll(PDO::FETCH_ASSOC) as $exrow) {
            $atype=$sprache->multi;
            $gtype=$exrow['description'];
        }
        if ($atype == '') {
            $atype=$sprache->single;
            $gtype=$shorten;
        }
        if ($row['active'] == 'Y') {
            $imgName='16_ok';
            $imgAlt='Active';
        } else {
            $imgName='16_bad';
            $imgAlt='Inactive';
        }
        if ($row['type'] == 'map') {
            $type=$sprache->map;
        } else {
            $type=$sprache->tool;
        }
        $table[]=array('id' => $row['id'],'active' => $row['active'],'img' => $imgName,'alt' => $imgAlt,'gametype'=>"${gtype} (${atype})",'description' => $row['menudescription'],'type' => $type);
    }
    $table2 = array();
    $pselect2=$sql->prepare("SELECT DISTINCT(`shorten`) FROM `addons` WHERE `resellerid`=:reseller_id");
    $pselect2->execute(array(':reseller_id' => $reseller_id));
    foreach ($pselect2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
        $atype = '';
        $gtype = '';
        $shorten=$row2['shorten'];
        $pselect3=$sql->prepare("SELECT `description` FROM `qstatshorten` WHERE `qstat`=:shorten LIMIT 1");
        $pselect3->execute(array(':shorten' => $shorten));
        foreach ($pselect3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
            $atype=$sprache->multi;
            $gtype=$row3['description'];
        }
        if ($atype == '') {
            $atype=$sprache->single;
            $gtype=$shorten;
        }
        $gametype="$gtype ($atype)";
        $table2[]=array('shorten' => $shorten,'description' => $gametype);
    }
    $next=$start+$amount;
    $countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `addons` $where");
    $countp->execute(array(':reseller_id' => $reseller_id));
    foreach ($countp->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $colcount=$row['amount'];
    }
    if ($colcount>$next) {
        $vor=$start+$amount;
    } else {
        $vor=$start;
    }
    $back=$start - $amount;
    if ($back>=0){
        $zur=$start - $amount;
    } else {
        $zur=$start;
    }
    if (!isset($list_gtype) or $list_gtype == '') {
        $list_gtype='all';
    }
    $pageamount = ceil($colcount / $amount);
    $link='<a href="admin.php?w=ad&amp;d=md&amp;o='.$o.'&amp;a=';
    if(!isset($amount)) {
        $link .="20";
    } else {
        $link .=$amount;
    }
    if ($start==0) {
        $link .='&p=0&amp;t='.$list_type.'&amp;g='.$list_gtype.'" class="bold">1</a>';
    } else {
        $link .='&p=0&amp;t='.$list_type.'&amp;g='.$list_gtype.'">1</a>';
    }
    $i = 2;
    $pages[] = $link;
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="admin.php?w=ad&amp;d=md&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'&amp;t='.$list_type.'&amp;g='.$list_gtype.'" class="bold">'.$i.'</a>';
        } else {
            $pages[] = '<a href="admin.php?w=ad&amp;d=md&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'&amp;t='.$list_type.'&amp;g='.$list_gtype.'">'.$i.'</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = "admin_addons_list.tpl";
}