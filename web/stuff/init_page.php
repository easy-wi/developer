<?php
/**
 * File: init_page.php.
 * Author: Ulrich Block
 * Date: 30.01.13
 * Time: 11:04
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


include(EASYWIDIR . '/stuff/class_page_settings.php');

$pages = array();

if (!isset($user_language)) {
    $user_language = $default_language;
}

if (!isurl($pageurl) or (!isdomain($pageurl) and (!isurl($pageurl)))) {
    $pageurl = $page_url;
}

$page_sprache = getlanguagefile('page', $user_language, 0);
$page_data = new PageSettings($user_language, $pageurl, $seo);

$query = $sql->prepare("SELECT `active`,`activeGS`,`activeVS` FROM `lendsettings` WHERE `resellerid`=0 LIMIT 1");
$query->execute();
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $page_data->SetData('lendactive', $row['active']);
    $page_data->SetData('lendactiveGS', $row['activeGS']);
    $page_data->SetData('lendactiveVS', $row['activeVS']);
}

$page_data->SetData('protectioncheck', $protectioncheck);
$page_data->SetData('title', $title);

$query = $sql->prepare("SELECT p.`id`,p.`subpage`,p.`naviDisplay`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`released`='1' AND p.`type`='page' AND t.`language`=? AND p.`resellerid`='0' ORDER BY `subpage`,`sort`");
$query->execute(array($user_language));
if ($seo== 'Y') {
    $page_data->SetMenu($gsprache->news, $gsprache->news,'news');

    if ($protectioncheck== 'Y') {
        $page_data->SetMenu($page_sprache->protectioncheck, $page_sprache->protectioncheck,'protectioncheck');
    }

    if ($page_data->lendactive == 'Y') {
        if ($page_data->showLend((isset($admin_id)) ? true : false,(isset($user_id)) ? true : false, 'g')) {
            $page_data->SetMenu($gsprache->gameserver, array($gsprache->lendserver, $gsprache->gameserver), 'lendservergs');
        }

        if ($page_data->showLend((isset($admin_id)) ? true : false,(isset($user_id)) ? true : false, 'v')) {
            $page_data->SetMenu($gsprache->voiceserver, array($gsprache->lendserver, $gsprache->voiceserver), 'lendservervoice');
        }

        if ($page_data->lendGS !== false or $page_data->lendVS !== false) {
            $page_data->SetMenu($gsprache->lendserver, $gsprache->lendserver, 'lendserver');
        } else {
            $page_data->SetData('lendactive', 'N');
        }

    }
    $page_data->SetMenu($gsprache->downloads, $gsprache->downloads,'downloads');
    $page_data->SetMenu($page_sprache->about, $page_sprache->about,'about');
    $page_data->SetMenu($page_sprache->sitemap, $page_sprache->sitemap,'sitemap');
    $page_data->SetMenu($page_sprache->gallery, $page_sprache->gallery,'gallery');
    $page_data->SetMenu($gsprache->imprint, $gsprache->imprint,'imprint');
    $page_data->SetMenu($page_sprache->contact, $page_sprache->contact,'contact');
    $page_data->SetMenu($page_sprache->search, $page_sprache->search,'search');
    $page_data->SetMenu($page_sprache->register, $page_sprache->register,'register');

    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['naviDisplay'] == 'Y') {
            $page_data->SetMenu($row['title'], $row['title'], $row['subpage'], $row['id']);

        } else {
            $page_data->SetMenu($row['title'], $row['title'], $row['subpage'], $row['id'], false);
        }
    }

    if (isset($admin_id) and $ui->smallletters('preview',4, 'get') == 'true') {
        $preview = 1;

    } else if (count($ui->get)>0 and isset($s) and isset($page_data->pages[$s])) {
        redirect($page_data->pages[$s]['link']);

    } else if (count($ui->get)>0) {
        redirect('/');
    }

} else {
    $page_data->SetMenu($gsprache->news, array('site' => 'news'), 'news');

    if ($protectioncheck== 'Y') {
        $page_data->SetMenu($page_sprache->protectioncheck, array('site' => 'protectioncheck'), 'protectioncheck');
    }

    if ($page_data->lendactive == 'Y') {
        $page_data->SetMenu($gsprache->lendserver, array('site' => 'lendserver'), 'lendserver');
        $page_data->SetMenu($gsprache->gameserver, array('site' => 'lendserver', 'd' => 'gs'), 'lendservergs');
        $page_data->SetMenu($gsprache->voiceserver, array('site' => 'lendserver', 'd' => 'vo'), 'lendservervoice');
    }
    $page_data->SetMenu($gsprache->downloads, array('site' => 'downloads'), 'downloads');
    $page_data->SetMenu($page_sprache->about, array('site' => 'about'), 'about');
    $page_data->SetMenu($page_sprache->sitemap, array('site' => 'sitemap'), 'sitemap');
    $page_data->SetMenu($page_sprache->gallery, array('site' => 'gallery'), 'gallery');
    $page_data->SetMenu($gsprache->imprint, array('site' => 'imprint'), 'imprint');
    $page_data->SetMenu($page_sprache->contact, array('site' => 'contact'), 'contact');
    $page_data->SetMenu($page_sprache->search, array('site' => 'search'), 'search');
    $page_data->SetMenu($page_sprache->register, array('site' => 'register'), 'register');

    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['naviDisplay'] == 'Y') {
            $page_data->SetMenu($row['title'], $row['id'], $row['subpage'], $row['id']);

        } else {
            $page_data->SetMenu($row['title'], $row['id'], $row['subpage'], $row['id'], false);
        }
    }
}

$query = $sql->prepare("SELECT t.`text` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`type`='about' AND p.`resellerid`=0 LIMIT 1");
$query->execute(array($user_language));
$page_data->SetData('about', $query->fetchColumn());

$query = $sql->prepare("SELECT p.`id`,p.`subpage`,t.`title`,t.`text` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`released`='1' AND p.`type`='news' AND p.`resellerid`=0 ORDER BY `id` DESC LIMIT $maxnews_sidebar");
$query->execute(array($user_language));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $page_data->SetNewsPost($row['id'], $row['title'], $row['text'], $newssidebar_textlength);
}

if (strpos($ui->escaped('HTTP_USER_AGENT', 'server'), ' MSIE ') !== false) {
    $page_data->SetData('MSIE',(string)$page_sprache->MSIE);
}

if (isset($page_category)) {
    if ($page_category == szrp($gsprache->imprint)) {
        $s='imprint';
    } else if ($page_category == szrp($page_sprache->contact)) {
        $s='contact';
    } else if ($page_category == szrp($gsprache->downloads)) {
        $s='downloads';
    } else if ($page_category == szrp($page_sprache->protectioncheck)) {
        $s='protectioncheck';
    } else if ($page_category == szrp($page_sprache->tag)) {
        $s='tag';
    } else if ($page_category == szrp($page_sprache->categories)) {
        $s='categories';
    } else if ($page_category == szrp($page_sprache->about)) {
        $s='about';
    } else if ($page_category == szrp($gsprache->lendserver)) {
        $s='lendserver';
    } else if ($page_category == szrp($gsprache->news)) {
        $s='news';
    } else if ($page_category == szrp($page_sprache->sitemap)) {
        $s='sitemap';
    } else if ($page_category == szrp($page_sprache->search)) {
        $s='search';
    } else if ($page_category == szrp($page_sprache->gallery)) {
        $s='gallery';
    } else if ($page_category == szrp($page_sprache->sitemap)) {
        $s='sitemap';
    } else if ($page_category == szrp($page_sprache->search)) {
        $s='search';
    } else if ($page_category == szrp($page_sprache->register)) {
        $s='register';
    } else if (isset($page_data->pages_array['pages']) and in_array($page_category, $page_data->pages_array['pages'])) {
        $s='page';
    }
}

if (isset($admin_id)) {
    $page_lookupid = $admin_id;

} else if (isset($user_id)) {
    $page_lookupid = $user_id;
}

if (isset($page_lookupid)) {
    $query = $sql->prepare("SELECT `cname`,`name`,`vname`,`lastlogin` FROM `userdata` WHERE `id`=? LIMIT 1");
    $query->execute(array($page_lookupid));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $great_name = $row['name'];
        $great_vname = $row['vname'];

        $great_user = ($row['name'] != '' or $row['vname'] != '') ? trim ($row['vname'] . ' ' . $row['name']) : $row['cname'];

        if ($row['lastlogin'] != null and $row['lastlogin'] != '0000-00-00 00:00:00') {
            $great_last=($user_language == 'de') ? date('d.m.Y H:m:s', strtotime($row['lastlogin'])) : $row['lastlogin'];
        } else {
            $great_last=($user_language == 'de') ? 'Niemals' : 'Never';
        }
    }
}
if (!isset($s) and !isset($page_category) and isset($page_default) and isid($page_default,19)) {
    $s='page';
    $default_page_id = $page_default;
} else if (!isset($s) and !isset($page_category) and isset($page_default)) {
    $s = $page_default;
} else if (!isset($s) and isset($page_category) and $page_category != '' and $page_category != null) {
    $s='404';
    $throw404 = true;
}
$what_to_be_included_array=array('news' => 'page_news.php','contact' => 'page_contact.php',
    'page' => 'page_page.php','home' => 'page_page.php','about' => 'page_page.php','gallery' => 'page_page.php','sitemap' => 'page_page.php','search' => 'page_page.php',
    'tag' => 'page_tag.php','categories' => 'page_tag.php','downloads' => 'page_download.php',
    'lendserver' => 'lend.php',
    'protectioncheck' => 'protectioncheck.php',
    'imprint' => 'imprint.php',
    'register' => 'page_register.php'
);