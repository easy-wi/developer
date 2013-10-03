<?php
/**
 * File: page_tag.php.
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

if ($seo== 'Y') {
	$search_tag=strtolower($page_name);
	if (!isset($page_count)) {
		$page_count = 1;
	}
	$current_page_link='/'. $user_language . '/' . szrp($page_sprache->$s). '/' . $page_name. '/' . $page_count.'/';
} else {
	$search_tag=strtolower($ui->username('tag','255','get'));
	if ($ui->id('older','30','get')) {
		$page_count=$ui->id('older','30','get');
	}
	if (!isset($page_count)) {
		$page_count = 1;
	}
	$current_page_link='/index.php?site='.$s.'&amp;tag='.strtolower(szrp($row2['name'])).'&amp;older='.$page_count;
}
$page_data->SetCanUrl($current_page_link);
if ($s == 'categories') $lookUp='category';
else $lookUp='tag';
if (isset($search_tag) and $search_tag != '' and $search_tag != null) {
	$table = array();
	$query = $sql->prepare("SELECT `id`,`name` FROM `page_terms` WHERE `language`=? AND `search_name`=? AND `type`=? AND `resellerid`='0' LIMIT 1");
	$query->execute(array($user_language,$search_tag,$lookUp));
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$tag_name=$row['name'];
		$limit=$maxnews;
        $query = $sql->prepare("SELECT COUNT(p.`id`) AS `amount` FROM `page_terms_used` u LEFT JOIN `page_pages_text` t ON u.`language_id`=t.`id` AND t.`language`=? LEFT JOIN `page_pages` p ON u.`page_id`=p.`id` AND (p.`type`='page' OR p.`type`='news') WHERE t.`title` IS NOT NULL AND t.`text` IS NOT NULL AND t.`title`!='' AND t.`text`!='' AND u.`term_id`=? AND u.`resellerid`='0'");
        $query->execute(array($user_language, $row['id']));
        $page_row_count=$query->fetchColumn();
		$max_old=ceil($page_row_count/$maxnews);
		if (isset($page_count) and $max_old<$page_count) {
			$page_count=$max_old;
		}
		if ($page_row_count>$maxnews and isset($page_count)) {
			if ($page_count==1) {
				$limit=$maxnews;
			} else {
				$limit=($maxnews*$page_count)-$maxnews.','.($maxnews*$page_count);
			}
			if ($max_old>$page_count and $seo== 'Y') {
				$older=$page_url. '/' . $user_language. '/' . $page_sprache->$s. '/' . $search_tag . '/' . ($page_count+1).'/';
			} else if ($max_old>$page_count and $seo== 'N') {
				$older=$page_url.'/index.php?site='.$s.'&amp;tag='.strtolower(szrp($row2['name'])).'&amp;older='.($page_count+1);
			}
		} else if ($page_row_count>$maxnews and !isset($page_count)) {
			if ($seo== 'Y') {
				$older=$page_url. '/' . $user_language. '/' . $page_sprache->$s. '/' . $search_tag.'/2/';
			} else {
				$older=$page_url.'/index.php?site='.$s.'&amp;tag='.strtolower(szrp($row2['name'])).'&amp;older=2';
			}
		}
		if (isset($page_count) and $page_count>1 and $seo== 'Y') {
			$newer=$page_url. '/' . $user_language. '/' . $page_sprache->$s. '/' . $search_tag . '/' . ($page_count-1).'/';
		} else if (isset($page_count) and $page_count>1 and $seo== 'N') {
			$newer=$page_url.'/index.php?site='.$s.'&amp;tag='.strtolower(szrp($row2['name'])).'&amp;older='.($page_count-1);
		}
        $query = $sql->prepare("SELECT p.`id`,p.`date`,p.`type`,t.`title`,t.`text` FROM `page_terms_used` u LEFT JOIN `page_pages_text` t ON u.`language_id`=t.`id` AND t.`language`=? LEFT JOIN `page_pages` p ON u.`page_id`=p.`id` AND (p.`type`='page' OR p.`type`='news') WHERE t.`title` IS NOT NULL AND t.`text` IS NOT NULL AND t.`title`!='' AND t.`text`!='' AND u.`term_id`=? AND u.`resellerid`='0' ORDER BY p.`id` DESC LIMIT $limit");
        $query->execute(array($user_language, $row['id']));
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			if ($seo== 'Y') {
                $type = '';
                if ($row['type'] == 'news') $type=szrp($gsprache->news).'/';
				$row_link=$page_url. '/' . $user_language. '/' . $type.szrp($row['title']).'/';
			} else {
				$row_link=$page_url.'/index.php?site='.$row['type'].'&amp;id='.$row['id'];
			}
			$table[]=array('title'=>$row['title'],'text'=>nl2br($row['text']),'link'=>$row_link,'date'=>$row['date']);
		}
		$template_file = 'page_tag.tpl';
	}
} else {
	redirect($page_url);
}