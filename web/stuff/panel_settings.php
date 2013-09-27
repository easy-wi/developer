<?php

/**
 * File: panel_settings.php.
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['settings'])) {
	header('Location: login.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache=getlanguagefile('settings',$user_language,$reseller_id);
$gssprache=getlanguagefile('gserver',$user_language,$reseller_id);
$loguserid=$admin_id;
$logusername=getusername($admin_id);
$logusertype='admin';
if ($reseller_id==0) {
	$logreseller=0;
	$logsubuser=0;
} else {
    $logsubuser= (isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller=0;
}
if ($reseller_id!=0 and $admin_id!=$reseller_id) {
	$reseller_id=$admin_id;
}

if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->st('action','post')=='md') {
	$fail=0;
	if (!$ui->active('prefix1','post')) $fail=1;
	if (!$ui->active('voice_autobackup','post')) $fail=1;
	if (!$ui->timezone('timezone','post')) $fail=1;
	if (!$ui->id('faillogins',2,'post')) $fail=1;
	if (!$ui->id('noservertag',1,'post')) $fail=1;
	if (!$ui->id('nopassword',1,'post')) $fail=1;
	if (!$ui->id('tohighslots',1,'post')) $fail=1;
	if (!$ui->id('voice_maxbackup',1,'post')) $fail=1;
    if (!$ui->id('down_checks',2,'post')) $fail=1;
	if (!$ui->id('voice_autobackup_intervall',1,'post')) $fail=1;
	if (!$ui->smallletters('language',2,'post')) $fail=1;
	if ($fail!=1) {
		if ($ui->folder('template','post')) {
			$template=$ui->folder('template','post');
		} else {
			$template='default';
		}
		$voice_autobackup=$ui->active('voice_autobackup','post');
		$voice_autobackup_intervall=$ui->id('voice_autobackup_intervall',1,'post');
		$voice_maxbackup=$ui->id('voice_maxbackup',1,'post');
		$prefix1=$ui->active('prefix1','post');
		$prefix2=$ui->w('prefix2',20,'post');
		$brandname=$ui->description('brandname','post');
		$licence=$ui->smallletters('licence',20,'post');
		$imageserver="";
		foreach (preg_split('/\r\n/', $ui->escaped('imageserver','post'),-1, PREG_SPLIT_NO_EMPTY) as $imgserver) {
			if (isurl($imgserver) or isRsync($imgserver)) {
				$imageserver.=$imgserver."\r\n";
			}
		}
		$master="N";
		$timezone=$ui->timezone('timezone','post');
        $down_checks=$ui->id('down_checks',2,'post');
		$language=$ui->smallletters('language',2,'post');
		$faillogins=$ui->id('faillogins',2,'post');
		$supportnumber=$ui->description('supportnumber','post');
		$noservertag=$ui->id('noservertag',1,'post');
		$nopassword=$ui->id('nopassword',1,'post');
		$tohighslots=$ui->id('tohighslots',1,'post');
        $lastCronWarnStatus=($ui->active('lastCronWarnStatus','post')) ? $ui->active('lastCronWarnStatus','post') : 'Y';
        $lastCronWarnReboot=($ui->active('lastCronWarnReboot','post')) ? $ui->active('lastCronWarnReboot','post') : 'Y';
        $lastCronWarnUpdates=($ui->active('lastCronWarnUpdates','post')) ? $ui->active('lastCronWarnUpdates','post') : 'Y';
        $lastCronWarnJobs=($ui->active('lastCronWarnJobs','post')) ? $ui->active('lastCronWarnJobs','post') : 'Y';
        $lastCronWarnCloud=($ui->active('lastCronWarnCloud','post')) ? $ui->active('lastCronWarnCloud','post') : 'Y';
		$query=$sql->prepare("UPDATE `settings` SET `template`=?,`voice_autobackup`=?,`voice_autobackup_intervall`=?,`voice_maxbackup`=?,`language`=?,`imageserver`=AES_ENCRYPT(?,?),`master`=?,`prefix1`=?,`prefix2`=?,`faillogins`=?,`brandname`=?,`timezone`=?,`supportnumber`=?,`noservertag`=?,`nopassword`=?,`tohighslots`=?,`down_checks`=?,`lastCronWarnStatus`=?,`lastCronWarnReboot`=?,`lastCronWarnUpdates`=?,`lastCronWarnJobs`=?,`lastCronWarnCloud`=? WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($template,$voice_autobackup,$voice_autobackup_intervall,$voice_maxbackup,$language,$imageserver,$aeskey,$master,$prefix1,$prefix2,$faillogins,$brandname,$timezone,$supportnumber,$noservertag,$nopassword,$tohighslots,$down_checks,$lastCronWarnStatus,$lastCronWarnReboot,$lastCronWarnUpdates,$lastCronWarnJobs,$lastCronWarnCloud,$reseller_id));
        if ($query->rowCount()>0) $changed=true;
        $query=$sql->prepare("SELECT `id` FROM `imprints` WHERE `language`=? AND `resellerid`=? LIMIT 1");
        $query2=$sql->prepare("UPDATE imprints SET `imprint`=? WHERE `language`=? AND `resellerid`=? LIMIT 1");
        $query3=$sql->prepare("INSERT INTO `imprints` (`language`,`imprint`,`resellerid`) VALUES (?,?,?)");
		if ($ui->escaped('languages','post')) {
            $languages=(array)$ui->escaped('languages','post');
			foreach($languages as $language) {
				if (small_letters_check($language,2)) {
					$description=$ui->escaped("description_$language",'post');
                    $query->execute(array($language,$reseller_id));
					$num=$query->rowCount();
                    if ($num==1) {
                        $query2->execute(array($description,$language,$reseller_id));
                        if ($query2->rowCount()>0) $changed=true;
                    } else {
                        $query3->execute(array($language,$description,$reseller_id));
                        if ($query3->rowCount()>0) $changed=true;
                    }
				}
			}
            $query=$sql->prepare("SELECT `language` FROM `imprints` WHERE `resellerid`=?");
            $query2=$sql->prepare("DELETE FROM `imprints` WHERE `language`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($reseller_id));
			foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
				if (!in_array($row['language'],$languages)) {
                    $query2->execute(array($row['language'],$reseller_id));
                    if ($query2->rowCount()>0) $changed=true;
				}
			}
		} else {
            $query=$sql->prepare("DELETE FROM `imprints` WHERE `resellerid`=?");
            $query->execute(array($reseller_id));
            if ($query->rowCount()>0) $changed=true;
		}
		if (isset($changed)) {
            $loguseraction="%mod% %settings%";
            $insertlog->execute();
            $template_file=$spracheResponse->table_add;
        } else {
            $template_file=$spracheResponse->error_table;
        }
	} else {
		$template_file='admin_404.tpl';
	}
} else {
	$query=$sql->prepare("SELECT *,AES_DECRYPT(`imageserver`,?) AS `decryptedimageserver` FROM `settings`  WHERE `resellerid`=? LIMIT 1");
	$query->execute(array($aeskey,$reseller_id));
	$usprache=getlanguagefile('user',$user_language,$reseller_id);
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$language_choosen=$row['language'];
		$template_choosen=$row['template'];
		$imageserver=$row['decryptedimageserver'];
		$master=$row['master'];
		$licence=$row['licence'];
		$prefix1=$row['prefix1'];
		$prefix2=$row['prefix2'];
		$brandname=$row['brandname'];
		$faillogins=$row['faillogins'];
		$supportnumber=$row['supportnumber'];
		$timezone=$row['timezone'];
		$noservertag=$row['noservertag'];
		$nopassword=$row['nopassword'];
		$tohighslots=$row['tohighslots'];
		$voice_autobackup=$row['voice_autobackup'];
		$voice_autobackup_intervall=$row['voice_autobackup_intervall'];
		$voice_maxbackup=$row['voice_maxbackup'];
        $down_checks=$row['down_checks'];
        $lastCronWarnStatus=$row['lastCronWarnStatus'];
        $lastCronWarnReboot=$row['lastCronWarnReboot'];
        $lastCronWarnUpdates=$row['lastCronWarnUpdates'];
        $lastCronWarnJobs=$row['lastCronWarnJobs'];
        $lastCronWarnCloud=$row['lastCronWarnCloud'];
		$servertime=date('Y-m-d H:i:s');
		$templates=array();
		$dir=EASYWIDIR."/template/";
		if (is_dir($dir)){
			$dirs=scandir($dir);
			foreach ($dirs as $row) {
				if (is_dir('template/'.$row) and !preg_match('/^\.(.*)$/',$row)) $templates[]=$row;
			}
		}
		$selectlanguages=getlanguages($template_choosen);
	}
	$foundlanguages=array();
	foreach (getlanguages($template_choosen) as $langrow2) {
		$imprint="";
		$lang="";
		$query=$sql->prepare("SELECT `imprint` FROM `imprints` WHERE `language`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($langrow2,$reseller_id));
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$imprint=$row['imprint'];
		}
		$foundlanguages[]=array('style'=>$query->rowCount(),'lang'=>$langrow2,'imprint'=>$imprint);
	}
	$template_file="admin_settings.tpl";
}