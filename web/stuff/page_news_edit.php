<?php
/**
 * File: page_news_edit.php.
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['cms_news']) or $reseller_id!=0) {
	header('Location: admin.php');
    die;
}
$sprache=getlanguagefile('page',$user_language,$reseller_id);
$loguserid=$admin_id;
$logusername=getusername($admin_id);
$logusertype="admin";
$logreseller=0;
$logsubuser=0;
$logsubuser=0;
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->st('d','get')=="ad") {
	if (!$ui->smallletters('action',2,'post')) {
		$lang_avail=getlanguages($template_to_use);
		$categories=array();
		$keywords=array();
		foreach ($lang_avail as $lg) {
			$categories[$lg]=array();
			$keywords[$lg]=array();
			$query=$sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='category' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `name` DESC");
			$query->execute(array($lg,$reseller_id));
			foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$categories[$lg][]=$row['name'];
			}
			$query2=$sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='tag' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `count` DESC LIMIT 10");
			$query2->execute(array($lg,$reseller_id));
			foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
				$keywords[$lg][]=$row2['name'];
			}
		}
		$template_file="admin_page_news_add.tpl";
	} else if ($ui->smallletters('action',2,'post')=="ad" and $ui->id('released','1','post') and ($ui->id('subpage',19,'post') or $ui->id('subpage',19,'post')==0)) {
		if (is_object($ui->st('language','post'))) {
			foreach ($ui->st('language','post') as $key=>$lg) {
				$posted_languages[$key]=$lg;
			}
		} else {
			$posted_languages=array();
		}
		if (count($posted_languages)>0) {
			$addterms=array();
			$query=$sql->prepare("SELECT `cname`,`name`,`vname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
			$query->execute(array($admin_id,$reseller_id));
			foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $author=(($row['name']=='' or $row['name']==null) and ($row['vname']=='' or $row['vname']==null)) ? $row['cname'] : $row['vname'].' '.$row['name'];
			}
            $query=$sql->prepare("INSERT INTO `page_pages` (`released`,`subpage`,`authorid`,`authorname`,`date`,`type`,`comments`,`resellerid`) VALUES (?,?,?,?,NOW(),'news',?,?)");
            $query->execute(array($ui->id('released','1','post'),$ui->id('subpage',19,'post'),$admin_id,$author,$ui->active('comments','post'),$reseller_id));
            $query=$sql->prepare("SELECT `id` FROM `page_pages` WHERE `resellerid`=? ORDER BY `id` DESC LIMIT 1");
            $query->execute(array($reseller_id));
            $pageid=$query->fetchColumn();
            $query=$sql->prepare("UPDATE `page_pages` SET `subpage`=`id` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query2=$sql->prepare("INSERT INTO `page_pages_text` (`pageid`,`language`,`title`,`text`,`resellerid`) VALUES (?,?,?,?,?)");
            $query->execute(array($pageid,$reseller_id));
			foreach ($posted_languages as $lg) {
                $query2->execute(array($pageid,$lg,$ui->htmlcode('title','post',$lg),$ui->escaped('text','post',$lg),$reseller_id));
                $newpageid=$sql->lastInsertId();
				foreach (preg_split('/\,/',$ui->escaped('keywords','post',$lg),-1,PREG_SPLIT_NO_EMPTY) as $keyword) {
					$addterms[$lg][]=array('lid'=>$newpageid,'keyword'=>$keyword,'termtype'=>'tag');
				}
				if (is_object($ui->escaped('categories','post',$lg))) {
					foreach ($ui->escaped('categories','post',$lg) as $category) {
						$addterms[$lg][]=array('lid'=>$newpageid,'keyword'=>$category,'termtype'=>'category');
					}
				}
			}
            $query=$sql->prepare("SELECT `id` FROM `page_terms` WHERE `language`=? AND `type`=? AND `name`=? AND `resellerid`=? LIMIT 1");
            $query2=$sql->prepare("UPDATE `page_terms` SET `count`=`count`+1 WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query3=$sql->prepare("INSERT INTO `page_terms` (`language`,`name`,`search_name`,`type`,`count`,`resellerid`) VALUES (?,?,?,?,'1',?)");
            $query4=$sql->prepare("SELECT `id` FROM `page_terms` WHERE `language`=? AND `type`=? AND `name`=? AND `resellerid`=? LIMIT 1");
            $query5=$sql->prepare("INSERT INTO `page_terms_used` (`page_id`,`term_id`,`language_id`,`resellerid`) VALUES (?,?,?,?)");
			foreach ($addterms as $lg => $terms) {
				foreach ($terms as $term) {
					unset($term_id);
                    $query->execute(array($lg,$term['termtype'],$term['keyword'],$reseller_id));
					foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
						$term_id=$row['id'];
					}
					if (isset($term_id)) {
                        $query2->execute(array($term_id,$reseller_id));
					} else {
                        $query3->execute(array($lg,$term['keyword'],strtolower(szrp($term['keyword'])),$term['termtype'],$reseller_id));
                        $query4->execute(array($lg,$term['termtype'],$term['keyword'],$reseller_id));
						foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row) {
							$term_id=$row['id'];
						}
					}
					if (isset($term_id)) {
                        $query5->execute(array($pageid,$term_id,$term['lid'],$reseller_id));
					}
				}
			}
			$template_file=$spracheResponse->table_add;
		} else {
			$template_file="Error: No language selected";
		}
	} else {
		$template_file="Unknown Error";
	}
} else if ($ui->st('d','get')=='dl') {
	if (!$ui->st('action','post') and $ui->id('id',19,'get')) {
		$id=$ui->id('id',19,'get');
		$pselect=$sql->prepare("SELECT p.`id`,p.`released`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`id`=? AND p.`resellerid`=? LIMIT 1");
		$pselect->execute(array($user_language,$id,$reseller_id));
		foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
			if ($row['released']=='1') {
				$page_active=$gsprache->yes;
			} else {
				$page_active=$gsprache->no;
			}
			$query2=$sql->prepare("SELECT `language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language`");
			$query2->execute(array($id,$reseller_id));
			foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
				$p_languages[]=$row2['language'];
			}
			$page_title=$row['title'];
			if (($row['title']==null or $row['title']=='') and isset($p_languages)) {
				$query3=$sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `language`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
				$query3->execute(array($row['id'],$p_languages[0],$reseller_id));
				foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
					$page_title=$row3['title'];
				}
			} else if ($row['title']==null or $row['title']=='') {
				$page_title='';
				$p_languages=array();
			}
		}
		if (isset($page_active)) {
			$template_file="admin_page_news_dl.tpl";
		} else {
			$template_file="Error: No ID";
		}
	} else if ($ui->st('action','post')=='dl' and ($ui->id('id',19,'post') or $ui->id('id',19,'get'))) {
        $id=($ui->id('id',19,'post')) ? $ui->id('id',19,'post') : $ui->id('id',19,'get');
		$query=$sql->prepare("SELECT t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`page_id`=? AND u.`resellerid`=?");
		$query->execute(array($id,$reseller_id));
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$updateterms=$sql->prepare("UPDATE `page_terms` SET `count`=`count`-1 WHERE `name`=? AND `resellerid`=? LIMIT 1");
			$updateterms->execute(array($row['name'],$reseller_id));
		}
		$update=$sql->prepare("UPDATE `page_pages` SET `subpage`=`id` WHERE `subpage`=? AND `resellerid`=?");
		$update->execute(array($id,$reseller_id));
		$delete1=$sql->prepare("DELETE FROM `page_pages` WHERE `id`=? AND `resellerid`=? LIMIT 1");
		$delete1->execute(array($id,$reseller_id));
		$delete2=$sql->prepare("DELETE FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
		$delete2->execute(array($id,$reseller_id));
		$delete3=$sql->prepare("DELETE FROM `page_terms_used` WHERE `page_id`=? AND `resellerid`=?");
		$delete3->execute(array($id,$reseller_id));
		$template_file=$spracheResponse->table_del;
	} else {
		$template_file="Error: No ID";
	}
} else if ($ui->st('d','get')=="md" and ($ui->id('id',19,'get') or $ui->id('id',19,'post'))) {
	if (!$ui->st('action','post') and $ui->id('id',19,'get')) {
		$lang_avail=getlanguages($template_to_use);
		$id=$ui->id('id',19,'get');
		$table=array();
		foreach ($lang_avail as $lg) {
			$table[$lg]=array('title'=>false,'keywords'=>false,'text'=>false,'categories'=>array());
			$keywords_used[$lg]=array();
			$categories_used[$lg]=array();
		}
		$query=$sql->prepare("SELECT `released`,`subpage`,`comments` FROM `page_pages` WHERE `id`=? AND `resellerid`=? LIMIT 1");
		$query->execute(array($id,$reseller_id));
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$released=$row['released'];
			$subpage=$row['subpage'];
            $comments=$row['comments'];
			$query2=$sql->prepare("SELECT `id`,`language`,`title`,`text` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language`");
			$query2->execute(array($id,$reseller_id));
			foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
				$query3=$sql->prepare("SELECT t.`name`,t.`type` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`page_id`=? AND u.`language_id`=? AND u.`resellerid`=? ORDER BY t.`name` DESC");
				$query3->execute(array($id,$row2['id'],$reseller_id));
				foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
					if ($row3['type']=='tag') {
						$keywords_used[$row2['language']][]=$row3['name'];
					} else {
						$categories_used[$row2['language']][]=$row3['name'];
					}
				}
				$table[$row2['language']]=array('title'=>$row2['title'],'keywords'=>implode(', ',$keywords_used[$row2['language']]),'categories'=>(is_array($categories_used[$row2['language']])) ? $categories_used[$row2['language']] : array(),'text'=>$row2['text']);
			}
		}
		$subpages=array();
		$query4=$sql->prepare("SELECT p.`id`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`resellerid`=? AND p.`type`='news' ORDER BY t.`title`");
		$query4->execute(array($user_language,$reseller_id));
		foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row4) {
			if ($row4['id']!=$id) {
				$page_title=$row4['title'];
				if ($row4['title']==null or $row4['title']=='') {
					$query5=$sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
					$query5->execute(array($row4['id'],$reseller_id));
					foreach ($query5->fetchAll(PDO::FETCH_ASSOC) as $row5) {
						$page_title=$row5['title'];
					}
				}
				$subpages[$row4['id']]=$page_title;
			}
		}
		$categories=array();
		$keywords=array();
		foreach ($lang_avail as $lg) {
			$categories[$lg]=array();
			$keywords[$lg]=array();
			$query=$sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='category' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `name` DESC");
			$query->execute(array($lg,$reseller_id));
			foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
				if (!in_array($row['name'],$categories_used[$lg])) {
					$categories[$lg][]=$row['name'];
				}
			}
			$query2=$sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='tag' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `count` DESC LIMIT 10");
			$query2->execute(array($lg,$reseller_id));
			foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
				if (!in_array($row2['name'],$keywords_used[$lg])) {
					$keywords[$lg][]=$row2['name'];
				}
			}
		}
		$template_file="admin_page_news_md.tpl";
	} else if ($ui->st('action','post')=='md' and ($ui->id('id',19,'post') or $ui->id('id',19,'get'))) {
		$id=($ui->id('id',19,'post')) ? $ui->id('id',19,'post') : $ui->id('id',19,'get');
		if (is_object($ui->st('language','post'))) {
			foreach ($ui->st('language','post') as $key=>$lg) {
				$posted_languages[$key]=$lg;
			}
		} else {
			$posted_languages=array();
		}
		$countreduce=array();
		if (count($posted_languages)>0) {
            $query=$sql->prepare("UPDATE `page_pages` SET `released`=?,`comments`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($ui->id('released','1','post'),$ui->active('comments','post'),$id,$reseller_id));
			$addterms=array();
            $addCategories=array();
			$query=$sql->prepare("SELECT `id`,`language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
            $query2=$sql->prepare("UPDATE `page_pages_text` SET `title`=?,`text`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query3=$sql->prepare("SELECT u.`term_id`,t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE t.`type`='tag' AND u.`page_id`=? AND u.`language_id`=? AND u.`resellerid`=?");
            $query4=$sql->prepare("SELECT u.`term_id`,t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE t.`type`='category' AND u.`page_id`=? AND u.`language_id`=? AND u.`resellerid`=?");
            $query5=$sql->prepare("DELETE FROM `page_terms_used` WHERE `term_id`=? AND `page_id`=? AND `language_id`=? AND `resellerid`=? LIMIT 1");
            $query6=$sql->prepare("DELETE FROM `page_pages_text` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query7=$sql->prepare("DELETE FROM `page_terms_used` WHERE `language_id`=? AND `resellerid`=? LIMIT 1");
			$query->execute(array($id,$reseller_id));
			$lang_exist=array();
			foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$lang_exist[]=$row['language'];
				$keywords=array();
				foreach (preg_split('/\,/',preg_replace("/\,\s+/",',',preg_replace("/\s+/"," ",$ui->escaped('keywords','post',$row['language']))),-1,PREG_SPLIT_NO_EMPTY) as $keyword) {
					$keywords[]=$keyword;
				}
				$categories=array();
				if (is_object($ui->escaped('categories','post',$row['language']))) {
					foreach ($ui->escaped('categories','post',$row['language']) as $category) {
						$categories[]=$category;
					}
				}
				if (in_array($row['language'],$posted_languages)) {
                    $query2->execute(array($ui->htmlcode('title','post',$row['language']),$ui->escaped('text','post',$row['language']),$row['id'],$reseller_id));
					$keyword_exist=array();
					$query3->execute(array($id,$row['id'],$reseller_id));
					foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
						$keyword_exist[]=$row3['name'];
						if (!in_array($row3['name'],$keywords)) {
                            $query5->execute(array($row3['term_id'],$id,$row['id'],$reseller_id));
							$countreduce[]=$row3['name'];
						}
					}
					$category_exist=array();
					$query4->execute(array($id,$row['id'],$reseller_id));
					foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row4) {
						$category_exist[]=$row4['name'];
						if (!in_array($row4['name'],$categories)) {
                            $query5->execute(array($row4['term_id'],$id,$row['id'],$reseller_id));
							$countreduce[]=$row4['name'];
						}
					}
					foreach ($keywords as $keyword) {
						if (!in_array($keyword,$keyword_exist)) {
							$addterms[$row['language']][]=array('lid'=>$row['id'],'keyword'=>$keyword,'termtype'=>'tag');
						}
					}
					foreach ($categories as $category) {
						if (!in_array($category,$category_exist)) {
							$addterms[$row['language']][]=array('lid'=>$row['id'],'keyword'=>$category,'termtype'=>'category');
						}
					}
				} else {
                    $query6->execute(array($row['id'],$reseller_id));
                    $query7->execute(array($row['id'],$reseller_id));
					$countreduce=$keywords;
				}
			}
            $query=$sql->prepare("INSERT INTO `page_pages_text` (`pageid`,`language`,`title`,`text`,`resellerid`) VALUES (?,?,?,?,?)");
            $query2=$sql->prepare("SELECT `id` FROM `page_pages_text` WHERE `pageid`=? AND `language`=? AND `resellerid`=? LIMIT 1");
			foreach ($posted_languages as $lg) {
				if (!in_array($lg,$lang_exist)) {
                    $query->execute(array($id,$lg,$ui->htmlcode('title','post',$lg),$ui->escaped('text','post',$lg),$reseller_id));
					$query2->execute(array($id,$lg,$reseller_id));
                    $newpageid=$query6->fetchColumn();
					foreach (preg_split('/\,/',$ui->escaped('keywords','post',$lg),-1,PREG_SPLIT_NO_EMPTY) as $keyword) {
						$addterms[$lg][]=array('lid'=>$newpageid,'keyword'=>$keyword,'termtype'=>'tag');
					}
					foreach ($ui->escaped('categories','post',$lg) as $category) {
						$addterms[$lg][]=array('lid'=>$newpageid,'category'=>$category,'termtype'=>'category');
					}
				}
			}
            $query=$sql->prepare("SELECT `id` FROM `page_terms` WHERE `language`=? AND `type`=? AND `name`=? AND `resellerid`=? LIMIT 1");
            $query2=$sql->prepare("UPDATE `page_terms` SET `count`=`count`+1 WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query3=$sql->prepare("INSERT INTO `page_terms` (`language`,`name`,`search_name`,`type`,`count`,`resellerid`) VALUES (?,?,?,?,'1',?)");
            $query4=$sql->prepare("SELECT `id` FROM `page_terms` WHERE `language`=? AND `type`=? AND `name`=? AND `resellerid`=? LIMIT 1");
			foreach ($addterms as $lg => $terms) {
				foreach ($terms as $term) {
					unset($term_id);
					$query->execute(array($lg,$term['termtype'],$term['keyword'],$reseller_id));
					foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
						$term_id=$row['id'];
					}
					if (isset($term_id)) {
                        $query2->execute(array($term_id,$reseller_id));
					} else {
                        $query3->execute(array($lg,$term['keyword'],strtolower(szrp($term['keyword'])),$term['termtype'],$reseller_id));
                        $query4->execute(array($lg,$term['termtype'],$term['keyword'],$reseller_id));
						foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row4) {
							$term_id=$row4['id'];
						}
					}
					if (isset($term_id)) {
						$inserttermsused=$sql->prepare("INSERT INTO `page_terms_used` (`page_id`,`term_id`,`language_id`,`resellerid`) VALUES (?,?,?,?)");
						$inserttermsused->execute(array($id,$term_id,$term['lid'],$reseller_id));
					}
				}
			}
		} else {
			$query=$sql->prepare("SELECT t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`page_id`=? AND u.`resellerid`=?");
			$query->execute(array($id,$reseller_id));
			foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$countreduce[]=$row['name'];
			}
            $query=$sql->prepare("DELETE FROM `page_page` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id,$reseller_id));
            $query=$sql->prepare("DELETE FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
            $query=$sql->prepare("DELETE FROM `page_terms_used` WHERE `page_id`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
		}
        $query=$sql->prepare("UPDATE `page_terms` SET `count`=`count`-1 WHERE `name`=? AND `resellerid`=? LIMIT 1");
		foreach ($countreduce as $keyword) {
            $query->execute(array($keyword,$reseller_id));
		}
		$template_file=$spracheResponse->table_add;
    } else {
		$template_file="Unknown Error";
	}
} else {
    $query=$sql->prepare("SELECT `seo` FROM `page_settings` WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    $seo=$query->fetchColumn();
    $o=($ui->st('o','get')) ? $ui->st('o','get') : 'di';
    if ($o=='at') {
        $orderby='t.`title` ASC';
    } else if ($o=='dt') {
        $orderby='t.`title` DESC';
    } else if ($o=='aa') {
        $orderby='p.`authorname` ASC, p.`id` ASC, p.`subpage` ASC';
    } else if ($o=='da') {
        $orderby='p.`authorname` DESC, p.`id` ASC, p.`subpage` ASC';
    } else if ($o=='ar') {
        $orderby='p.`released` ASC, p.`id` ASC, p.`subpage` ASC';
    } else if ($o=='dr') {
        $orderby='p.`released` DESC, p.`id` ASC, p.`subpage` ASC';
    } else if ($o=='ad') {
        $orderby='p.`date` ASC';
    } else if ($o=='dd') {
        $orderby='p.`date` DESC';
    } else if ($o=='ad') {
        $orderby='p.`subpage`, p.`id` ASC';
    } else if ($o=='di') {
        $orderby='p.`id` DESC';
    } else {
        $orderby='p.`id` ASC';
    }
    $query=$sql->prepare("SELECT p.`id`,p.`date`,p.`released`,p.`subpage`,p.`authorid`,p.`authorname`,t.`title`,t.`shortlink`,t.`language` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`type`='news' AND p.`resellerid`=? GROUP BY p.`id` ORDER BY $orderby LIMIT $start,$amount");
    $query2=$sql->prepare("SELECT `cname`,`name`,`vname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query3=$sql->prepare("SELECT `language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language`");
    $query4=$sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `language`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
    $query->execute(array($user_language,$reseller_id));
    $table = array();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if(!isset($titleLanguages[$row['language']])) {
            $titleLanguages[$row['language']]=array('page'=>getlanguagefile('page',$row['language'],0),'general'=>getlanguagefile('general',$row['language'],0));
        }
        if ($row['released']=='1') {
            $released=$gsprache->yes;
        } else {
            $released=$gsprache->no;
        }
        $author=$row['authorname'];
        $query2->execute(array($row['authorid'],$reseller_id));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            if (($row2['name']=='' or $row2['name']==null) and ($row2['vname']=='' or $row2['vname']==null)) {
                $author=$row2['cname'];
            } else {
                $author=$row2['vname'].' '.$row2['name'];
            }
        }
        unset($p_languages);
        $query3->execute(array($row['id'],$reseller_id));
        foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
            $p_languages[]=$row3['language'];
        }
        $page_title=$row['title'];
        if (($row['title']==null or $row['title']=='') and isset($p_languages[0])) {
            $query4->execute(array($row['id'],$p_languages[0],$reseller_id));
            foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row4) {
                $page_title=$row4['title'];
            }
        } else if ($row['title']==null or $row['title']=='') {
            $page_title='';
            $p_languages=array();
        }
        if ($row['subpage']!=$row['id']) {
            $page_title=' - '.$page_title;
        }
        if ($seo=='N') {
            $link=$page_url.'/index.php?site=page&amp;id='.$row['id'];
        } else {
            if ($row['language']==$user_language) {
                $link=$page_url.'/'. $user_language.'/'.szrp($titleLanguages[$row['language']]['general']->news).'/'.strtolower(szrp($row['title'])).'/';
            } else {
                $link=$page_url.'/'. $row['language'].'/'.szrp($titleLanguages[$row['language']]['general']->news).'/'.strtolower(szrp($row['title'])).'/';
            }
        }
        $explodedtime=explode(' ', $row['date']);
        $explodedtime2=explode('-', $explodedtime[0]);
        if ($user_language=='de') {
            $date=$explodedtime2[2].".".$explodedtime2[1].".".$explodedtime2[0]." ".$explodedtime[1];
        } else {
            $date=$explodedtime2[1].".".$explodedtime2[2].".".$explodedtime2[0]." ".$explodedtime[1];
        }
        $table[]=array('id'=>$row['id'],'author'=>$author,'date'=>$date,'released'=>$released,'title'=>$page_title,'link'=>$link,'languages'=>$p_languages);
    }
    $next=$start+$amount;
    $query=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `page_pages` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    $colcount=$query->fetchColumn();
    if ($colcount>$next) {
        $vor=$start+$amount;
    } else {
        $vor=$start;
    }
    $back=$start-$amount;
    if ($back>=0){
        $zur=$start-$amount;
    } else {
        $zur=$start;
    }
    $pageamount=ceil($colcount/$amount);
    $link='<a href="admin.php?w=pn&amp;d=md&amp;o='.$o.'&amp;a=';
    if(!isset($amount)) {
        $link .="20";
    } else {
        $link .=$amount;
    }
    if ($start==0) {
        $link .='&p=0" class="bold">1</a>';
    } else {
        $link .='&p=0">1</a>';
    }
    $pages[]=$link;
    $i=2;
    while ($i<=$pageamount) {
        $selectpage=($i-1)*$amount;
        if ($start==$selectpage) {
            $pages[]='<a href="admin.php?w=pn&amp;d=md&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'" class="bold">'.$i.'</a>';
        } else {
            $pages[]='<a href="admin.php?w=pn&amp;d=md&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'">'.$i.'</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file="admin_page_news_list.tpl";
}