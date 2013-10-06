<?php
/**
 * File: page_page.php.
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

if (!isset($page_include)) {
    header('Location: index.php');
    die;
}
if (isset($default_page_id)) {
	$page_id=$default_page_id;
} else if (isset($page_category,$page_data->pages_array['pages']) and in_array($page_category,$page_data->pages_array['pages'])) {
	$page_id=array_search($page_category,$page_data->pages_array['pages']);
} else {
	$page_id=$ui->id('id',19,'get');
}
if (isset($page_id) and is_numeric($page_id)) {
    function pre_replace($m) {
        return str_replace($m[1],htmlentities($m[1]),$m[0]);
    }
	$query = $sql->prepare("SELECT t.`title`,t.`text`,t.`id`,p.`subpage` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`id`=? AND `type`='page' AND t.`language`=? AND p.`released`='1' AND p.`resellerid`='0' LIMIT 1");
	$query->execute(array($page_id,$user_language));
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$page_title=$row['title'];
		$page_text=str_replace('%url%',$page_data->pageurl, $row['text']);
        $page_text=preg_replace_callback('/<pre.*?>(.*?)<\/pre>/imsu','pre_replace',$page_text);
		$page_keywords = array();
		$tag_tags = array();
        $breadcrumbID=$row['subpage'];
        $breadcrumbPageID=$page_id;
		$query2 = $sql->prepare("SELECT t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`language_id`=? AND u.`resellerid`='0' ORDER BY t.`name` DESC");
		$query2->execute(array($row['id']));
		foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
			$page_data->AddData('keywords', $row2['name']);
            $tag_tags[]=($seo== 'Y') ? '<a href='.$page_url. '/' . $user_language. '/' . $page_sprache->tag . '/' . strtolower(szrp($row2['name'])).'/>'.$row2['name'].'</a>' : '<a href='.$page_url.'/index.php?site=tag&amp;tag='.strtolower(szrp($row2['name'])).'/>'.$row2['name'].'</a>';
		}
	}
    $breadcrumbs = array();
    $query = $sql->prepare("SELECT p.`id`,p.`subpage`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`id`=? AND t.`language`=? AND `type`='page' AND p.`released`='1' AND p.`resellerid`='0' LIMIT 1");
    while (isset($breadcrumbID) and isid($breadcrumbID,19) and $breadcrumbID != $breadcrumbPageID) {
        $query->execute(array($breadcrumbID,$user_language));
        unset($breadcrumbID);
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $link=(isset($seo) and $seo== 'Y') ? $page_data->pageurl. '/' . $user_language . '/' . szrp($row['title']).'/' : $page_data->pageurl.'?s=page&amp;l='.$user_language.'&amp;id='.$row['id'];
            $breadcrumbs[]=array('href' => '<a href="'.$link.'">'.$row['title'].'</a>','link' => $link);
            $breadcrumbID=$row['subpage'];
            $breadcrumbPageID=$row['id'];
        }
    }
    $breadcrumbs=array_reverse($breadcrumbs);
    $template_file = (isset($page_title)) ? 'page_page.tpl' : 'page_404.tpl';
} else if ($s == 'about') {
    $query = $sql->prepare("SELECT t.`text` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE `type`='about' AND t.`language`=? AND p.`resellerid`='0' LIMIT 1");
    $query->execute(array($user_language));
    $page_text=nl2br($query->fetchColumn());
    $page_title=$page_sprache->about;
    $page_keywords = array();
    $tag_tags = array();
    $page_data->setCanonicalUrl($s);
    $template_file = 'page_page.tpl';
} else if ($s == 'search') {
    $searchStringValue=htmlentities($ui->escaped('search','post'),ENT_QUOTES,'UTF-8');
    if ($ui->escaped('search','post')) {
        $results = array();
        $searchFor=array('general' => array(),'exact' => array());
        $searchString=preg_replace("/\s+/",' ',$ui->escaped('search','post'));
        $searchFor['exact'][]=strtolower($searchString);
        if (strpos($searchString,'"')===false) {
            foreach (preg_split('/\s+/',$searchString,-1,PREG_SPLIT_NO_EMPTY) as $v) $searchFor['general'][]=strtolower($v);
        } else {
            $checkForEnd = false;
            $split=explode('"',$searchString);
            foreach ($split as $v) {
                if ($v != '' and $checkForEnd==false) {
                    foreach (preg_split('/\s+/',$v,-1,PREG_SPLIT_NO_EMPTY) as $v2) $searchFor['general'][]=strtolower($v2);
                    $checkForEnd = true;
                } else if ($v != '' and $checkForEnd==true) {
                    $searchFor['exact'][]=strtolower($v);
                    $checkForEnd = false;
                } else if ($v== '' and $checkForEnd==false) {
                    $checkForEnd = true;
                }
            }
        }
        $searchFor['exact']=array_unique($searchFor['exact']);
        $searchFor['general']=array_unique($searchFor['general']);
        function returnRating ($value,$exact=false) {
            global $sql,$newssidebar_textlength,$page_data,$results,$seo;
            $query = $sql->prepare("SELECT t.`id`,p.`id` AS `pageID`,p.`type`,t.`shortlink`,t.`title`,t.`text`,t.`language` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`released`=1 AND p.`resellerid`=0 AND (LOWER(t.`shortlink`) LIKE :search OR LOWER(t.`title`) LIKE :search OR LOWER(t.`text`) LIKE :search)");
            $query->execute(array(':search' => '%'.$value.'%'));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if(!isset($titleLanguages[$row['language']])) {
                    $titleLanguages[$row['language']]=array('page' => getlanguagefile('page', $row['language'],0),'general' => getlanguagefile('general', $row['language'],0));
                }
                if (strlen($row['text'])<=$newssidebar_textlength) {
                    $text=$row['text'];
                } else {
                    $text=substr($row['text'],0,$newssidebar_textlength).' ...';
                }
                $title=$row['title'];
                if ($row['type'] == 'news' and isset($seo) and $seo== 'Y') {
                    $type=(string)$titleLanguages[$row['language']]['general']->news;
                    $link=$page_data->pageurl. '/' . $row['language'] . '/' . szrp($titleLanguages[$row['language']]['general']->news) . '/' . szrp($row['title']).'/';
                } else if ($row['type'] == 'news') {
                    $type=(string)$titleLanguages[$row['language']]['general']->news;
                    $link=$page_data->pageurl.'?s=news&amp;l='.$row['language'].'&amp;id='.$row['pageID'];
                } else if ($row['type'] == 'page' and isset($seo) and $seo== 'Y') {
                    $type=(string)$titleLanguages[$row['language']]['general']->page;
                    $link=$page_data->pageurl. '/' . $row['language'] . '/' . szrp($row['title']).'/';
                } else if ($row['type'] == 'page') {
                    $type=(string)$titleLanguages[$row['language']]['general']->page;
                    $link=$page_data->pageurl.'?s=page&amp;l='.$row['language'].'&amp;id='.$row['pageID'];
                } else if ($row['type'] == 'about' and isset($seo) and $seo== 'Y') {
                    $type=(string)$titleLanguages[$row['language']]['page']->about;
                    $title=(string)$titleLanguages[$row['language']]['page']->about;
                    $link=$page_data->pageurl. '/' . $row['language'] . '/' . szrp($titleLanguages[$row['language']]['page']->about).'/';
                } else if ($row['type'] == 'about') {
                    $type=(string)$titleLanguages[$row['language']]['page']->about;
                    $title=(string)$titleLanguages[$row['language']]['page']->about;
                    $link=$page_data->pageurl.'/?s=news&amp;l='.$row['language'];
                }
                if(!isset($link)) {
                    $link='#';
                }
                if ($exact==true) {
                    $worth=substr_count(strtolower($row['title']),strtolower($value))*16;
                    $worth+=substr_count(strtolower($row['text']),strtolower($value))*2;
                } else {
                    $worth=substr_count(strtolower($row['title']),strtolower($value))*12;
                    $worth+=substr_count(strtolower($row['text']),strtolower($value));
                }
                $href='<a href="'.$link.'" title="'.$title.'">'.$title.'</a>';
                if(isset($results[$row['id']])) {
                    $oldWorth=$results[$row['id']]['worth'];
                    $hits=$results[$row['id']]['hits'];
                    $hits[] = $value;
                    $hits=array_unique($hits);
                    $worth+=$oldWorth;
                    unset($results[$row['id']]);
                } else {
                    $hits=array($value);
                }
                $results[$row['id']]=array('textID' => $row['id'],'pageID' => $row['pageID'],'language' => $row['language'],'type' => $type,'worth' => $worth,'href' => $href,'title' => $title,'link' => $link,'text' => str_replace('%url%',$page_data->pageurl,$text),'hits' => $hits);
            }
            return $results;
        }
        foreach ($searchFor['general'] as $v) {
            foreach (returnRating($v) as $key=>$val) {
                $results[$key] = $val;
            }
        }
        foreach ($searchFor['exact'] as $v) {
            foreach (returnRating($v) as $key=>$val) {
                $results[$key] = $val;
            }
        }
        $resultsArray = array();
        foreach ($results as $k=>$v) {
            unset($results[$k]);
            $resultsArray[$v['worth']][$v['textID']] = $v;
        }
        krsort($resultsArray);
        $results = array();
        $exists = array();
        foreach ($resultsArray as $key=>$val) {
            foreach ($val as $k=>$v) {
                if(!in_array($key,$exists)) {
                    unset($resultsArray[$key][$k]);
                    $results[] = $v;
                    $exists[] = $key;
                }
            }
        }
        unset($resultsArray,$searchFor,$searchString,$exists);
    }
    $page_data->setCanonicalUrl($s);
    $template_file = 'page_search.tpl';
} else if ($s == 'home') {
    $page_data->setCanonicalUrl();
    $template_file = 'page_home.tpl';
} else if ($s == 'sitemap') {
    $page_data->setCanonicalUrl($s);
    $template_file = 'page_sitemap.tpl';
} else if ($s == 'gallery') {
    $page_data->setCanonicalUrl($s);
    $template_file = 'page_gallery.tpl';
} else if (isset($admin_id) and $ui->smallletters('preview',4,'get') == 'true') {
	if (is_array($ui->escaped('text','post')) or is_object($ui->escaped('text','post'))) {
		foreach ($ui->escaped('text','post') as $key=>$value) {
			$page_title=$ui->htmlcode('title','post',$key);
            $page_text=str_replace('%url%',$page_data->pageurl,$value);
		}
	} else {
		$page_title=$ui->escaped('title','post');
        $page_text=str_replace('%url%',$page_data->pageurl,$ui->escaped('text','post'));
	}
	$page_keywords = array();
	$tag_tags = array();
    if (isset($page_title)) {
        $template_file = 'page_page.tpl';
    } else {
        $template_file = 'page_404.tpl';
    }
}
// https://github.com/easy-wi/developer/issues/62
$langLinks = array();
if (isset($s) and $s == 'page') {
    $query = $sql->prepare("SELECT `title`,`language` FROM `page_pages_text` WHERE `pageid`=?");
    $query->execute(array($page_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $langLinks[$row['language']]=($page_data->seo== 'Y') ? szrp($row['title'])  : '?s=page&amp;id='.$page_id;
} else if (isset($s)) {
    foreach ($languages as $l) {
        $tempLanguage = getlanguagefile('page',$l,0);
        $langLinks[$l]=($page_data->seo== 'Y') ? szrp($tempLanguage->$s)  : '?s='.$s;
    }
}
$page_data->langLinks($langLinks);