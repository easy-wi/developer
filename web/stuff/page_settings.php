<?php
/**
 * File: page_settings.php.
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['cms_settings']) or $reseller_id!=0) {
    header('Location: admin.php');
    die('No acces');
}
$sprache=getlanguagefile('page',$user_language,$reseller_id,$sql);
$loguserid=$admin_id;
$logusername=getusername($admin_id);
$logusertype="admin";
$logreseller=0;
$logsubuser=0;
$logsubuser=0;
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->smallletters('action',2,'post')=='md' and $ui->id('maxnews',19,'post')) {
    if ($ui->smallletters('defaultpage','255','post')) {
        $defaultpage=$ui->smallletters('defaultpage','255','post');
    } else if ($ui->id('defaultpage','30','post')) {
        $defaultpage=$ui->id('defaultpage','30','post');
    } else {
        $defaultpage='home';
    }
    $queryAffected=0;
    $registerBlockMails='';
    foreach(explode("\r\n",$ui->escaped('registrationBadEmail','post')) as $row)if (preg_match("/^[a-z0-9@\_\-\.]+$/",strtolower($row))) $registerBlockMails.=strtolower($row)."\r\n";
    $registrationBadIP='';
    foreach(explode("\r\n",$ui->escaped('registrationBadIP','post')) as $row) if (isips($row)) $registrationBadIP.=strtolower($row)."\r\n";
    $registration=(in_array($ui->escaped('registration','post'),array('N','A','M','D'))) ? $ui->escaped('registration','post') : 'N';
    $query=$sql->prepare("UPDATE `page_settings` SET `registration`=?,`registrationBadIP`=?,`registrationBadEmail`=?,`active`=?,`seo`=?,`rssfeed`=?,`rssfeed_fulltext`=?,`rssfeed_textlength`=?,`maxnews`=?,`defaultpage`=?,`protectioncheck`=?,`maxnews_sidebar`=?,`newssidebar_textlength`=?,`spamFilter`=?,`languageFilter`=?,`blockLinks`=?,`blockWords`=?,`mailRequired`=?,`commentMinLength`=?,`commentsModerated`=?,`honeyPotKey`=?,`dnsbl`=?,`pageurl`=? WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($registration,$registrationBadIP,$registerBlockMails,$ui->active('active','post'),$ui->active('seo','post'),$ui->active('rssfeed','post'),$ui->active('rssfeed_fulltext','post'),$ui->id('rssfeed_textlength',11,'post'),$ui->id('maxnews',30,'post'),$defaultpage,$ui->active('protectioncheck','post'),$ui->id('maxnews_sidebar',11,'post'),$ui->id('newssidebar_textlength',11,'post'),$ui->active('spamFilter','post'),$ui->active('languageFilter','post'),$ui->active('blockLinks','post'),$ui->escaped('blockWords','post'),$ui->active('mailRequired','post'),$ui->id('commentMinLength',11,'post'),$ui->active('commentsModerated','post'),$ui->w('honeyPotKey',255,'post'),$ui->active('dnsbl','post'),$ui->url('pageurl','post'),$reseller_id));
    $queryAffected+=$query->rowCount();
    $posted_languages=array();
    if (is_object($ui->st('language','post'))) {
        foreach ($ui->st('language','post') as $key=>$lg) $posted_languages[$key]=$lg;
    }
    $query=$sql->prepare("SELECT `id` FROM `page_pages` WHERE `type`='about' AND `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    $about_id=$query->fetchColumn();
    if (count($posted_languages)>0) {
        $query=$sql->prepare("SELECT `language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
        $query2=$sql->prepare("UPDATE `page_pages_text` SET `text`=? WHERE `pageid`=? AND `language`=? AND `resellerid`=? LIMIT 1");
        $query3=$sql->prepare("DELETE FROM `page_pages_text` WHERE `pageid`=? AND `language`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($about_id,$reseller_id));
        $lang_exist=array();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $lang_exist[]=$row['language'];
            if (in_array($row['language'],$posted_languages)) {
                $query2->execute(array($ui->escaped('about','post',$row['language']),$about_id,$row['language'],$reseller_id));
            } else {
                $query3->execute(array($about_id,$row['language'],$reseller_id));
            }
        }
        $queryAffected+=$query->rowCount();
        $query=$sql->prepare("INSERT INTO `page_pages_text` (`pageid`,`language`,`text`,`resellerid`) VALUES (?,?,?,?)");
        $queryAffected+=$query->rowCount();
        foreach ($posted_languages as $lg) {
            if (!in_array($lg,$lang_exist)) $query->execute(array($about_id,$lg,nl2br($ui->escaped('about','post',$lg)),$reseller_id));
        }
    } else {
        $query=$sql->prepare("DELETE FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
        $query->execute(array($about_id,$reseller_id));
        $queryAffected+=$query->rowCount();
    }
    $posted_touLanguages=array();
    if (is_object($ui->st('touLanguages','post'))) {
        foreach ($ui->st('touLanguages','post') as $key=>$lg) $posted_touLanguages[$key]=$lg;
    }
    if (count($posted_touLanguages)>0) {
        $query=$sql->prepare("SELECT `lang` FROM `translations` WHERE `type`='to' AND `resellerID`=?");
        $query2=$sql->prepare("UPDATE `translations` SET `text`=? WHERE `type`='to' AND `lang`=? AND `resellerID`=? LIMIT 1");
        $query3=$sql->prepare("DELETE FROM `translations` WHERE `type`='to' AND `lang`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($reseller_id));
        $lang_exist=array();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $lang_exist[]=$row['lang'];
            if (in_array($row['lang'],$posted_touLanguages)) {
                $query2->execute(array($ui->escaped('tou','post',$row['lang']),$row['lang'],$reseller_id));
            } else {
                $query3->execute(array($row['lang'],$reseller_id));
            }
        }
        $queryAffected+=$query->rowCount();
        $query=$sql->prepare("INSERT INTO `translations` (`transID`,`type`,`lang`,`text`,`resellerID`) VALUES (1,'to',?,?,?)");
        $queryAffected+=$query->rowCount();
        foreach ($posted_touLanguages as $lg) {
            if (!in_array($lg,$lang_exist)) $query->execute(array($lg,$ui->escaped('tou','post',$lg),$reseller_id));
        }
    } else {
        $query=$sql->prepare("DELETE FROM `translations` WHERE `type`='to' AND `resellerID`=?");
        $query->execute(array($reseller_id));
        $queryAffected+=$query->rowCount();
    }
    $loguseraction="%mod% CMS Settings";
    $insertlog->execute();
    if($queryAffected>0) {
        $template_file=$spracheResponse->table_add;
    } else {
        $template_file=$spracheResponse->error_table;
    }
} else {
    $lang_avail=getlanguages($template_to_use);
    $about_text=array();
    foreach ($lang_avail as $lg) $about_text[$lg]=false;
    $query=$sql->prepare("SELECT * FROM `page_settings` WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $active=$row['active'];
        $seo=$row['seo'];
        $rssfeed=$row['rssfeed'];
        $rssfeed_fulltext=$row['rssfeed_fulltext'];
        $rssfeed_textlength=$row['rssfeed_textlength'];
        $maxnews=$row['maxnews'];
        $maxnews_sidebar=$row['maxnews_sidebar'];
        $newssidebar_textlength=$row['newssidebar_textlength'];
        $defaultpage=$row['defaultpage'];
        $protectioncheck=$row['protectioncheck'];
        $spamFilter=$row['spamFilter'];
        $languageFilter=$row['languageFilter'];
        $blockLinks=$row['blockLinks'];
        $blockWords=$row['blockWords'];
        $mailRequired=$row['mailRequired'];
        $commentMinLength=$row['commentMinLength'];
        $commentsModerated=$row['commentsModerated'];
        $honeyPotKey=$row['honeyPotKey'];
        $dnsbl=$row['dnsbl'];
        $pageurl=$row['pageurl'];
        $registration=$row['registration'];
        $registrationQuestion=$row['registrationQuestion'];
        $registrationBadEmail=$row['registrationBadEmail'];
        $registrationBadIP=$row['registrationBadIP'];
    }
    $query=$sql->prepare("SELECT `id` FROM `page_pages` WHERE `type`='about' AND `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    $about_id=$query->fetchColumn();
    $query=$sql->prepare("SELECT `language`,`text` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
    $query->execute(array($about_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $about_text[$row['language']]=$row['text'];
    $subpage=array();
    $query=$sql->prepare("SELECT p.`id`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`resellerid`=? AND p.`type`='page' ORDER BY t.`title`");
    $query2=$sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
    $query->execute(array($user_language,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $page_title=$row['title'];
        if ($row['title']==null or $row['title']=='') {
            $query2->execute(array($row['id'],$reseller_id));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) $page_title=$row2['title'];
        }
        $subpage[$row['id']]=$page_title;
    }
    $default_language=$rSA['language'];
    $tous=array();
    $query=$sql->prepare("SELECT `text` FROM `translations` WHERE `type`='to' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
    foreach ($languages as $row) {
        if (small_letters_check($row,2)) {
            $query->execute(array(1,$row,$reseller_id));
            $tous[$row]=$query->fetchColumn();
        }
    }
    $template_file="admin_page_settings.tpl";
}