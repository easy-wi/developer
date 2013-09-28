<?php
/**
 * File: page_pages.php.
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['cms_pages']) or $reseller_id!=0) {
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
		$query=$sql->prepare("SELECT p.`id`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`resellerid`=? AND p.`type`='page' ORDER BY t.`title`");
		$query->execute(array($user_language,$reseller_id));
		$subpage=array();
		foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
			$page_title=$row['title'];
			if ($row['title']==null or $row['title']=='') {
				$query2=$sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
				$query2->execute(array($row['id'],$reseller_id));
				foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
					$page_title=$row2['title'];
				}
			}
			$subpage[$row['id']]=$page_title;
		}
		$keywords=array();
		foreach ($lang_avail as $lg) {
			$keywords[$lg]=array();
			$query3=$sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='tag' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `count` DESC LIMIT 10");
			$query3->execute(array($lg,$reseller_id));
			foreach ($query3->fetchall(PDO::FETCH_ASSOC) as $row3) {
				$keywords[$lg][]=$row3['name'];
			}
		}
		$template_file="admin_page_pages_add.tpl";
	} else if ($ui->smallletters('action',2,'post')=="ad" and $ui->id('released','1','post') and ($ui->id('subpage',19,'post') or $ui->id('subpage',19,'post')==0)) {
		if (is_object($ui->st('language','post'))) {
			foreach ($ui->st('language','post') as $key=>$lg) {
				$posted_languages[$key]=$lg;
			}
		} else {
			$posted_languages=array();
		}
		if (count($posted_languages)>0) {
			$addkeywords=array();
			$query=$sql->prepare("SELECT `cname`,`name`,`vname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
			$query->execute(array($admin_id,$reseller_id));
			foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
				if (($row['name']=='' or $row['name']==null) and ($row['vname']=='' or $row['vname']==null)) {
					$author=$row['cname'];
				} else {
					$author=$row['vname'].' '.$row['name'];
				}
			}
            $query=$sql->prepare("INSERT INTO `page_pages` (`released`,`subpage`,`authorid`,`authorname`,`date`,`type`,`naviDisplay`,`resellerid`) VALUES (?,?,?,?,NOW(),'page',?,?)");
            $query->execute(array($ui->id('released','1','post'),$ui->id('subpage',19,'post'),$admin_id,$author,$ui->active('naviDisplay','post'),$reseller_id));
            $query=$sql->prepare("SELECT `id` FROM `page_pages` WHERE `resellerid`=? ORDER BY `id` DESC LIMIT 1");
            $query->execute(array($reseller_id));
            $pageid=$query->fetchColumn();
            if (!$ui->id('subpage',19,'post')) {
                $query=$sql->prepare("UPDATE `page_pages` SET `subpage`=`id` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($pageid,$reseller_id));
            }
            $query2=$sql->prepare("INSERT INTO `page_pages_text` (`pageid`,`language`,`title`,`text`,`resellerid`) VALUES (?,?,?,?,?)");
            $query3=$sql->prepare("SELECT `id` FROM `page_pages_text` WHERE `pageid`=? AND `language`=? AND `resellerid`=? LIMIT 1");
			foreach ($posted_languages as $lg) {
                $query2->execute(array($pageid,$lg,$ui->htmlcode('title','post',$lg),$ui->escaped('text','post',$lg),$reseller_id));
				$query3->execute(array($pageid,$lg,$reseller_id));
				foreach ($query3->fetchall(PDO::FETCH_ASSOC) as $row3) {
					$newpageid=$row3['id'];
				}
				foreach (preg_split('/\,/',$ui->escaped('keywords','post',$lg),-1,PREG_SPLIT_NO_EMPTY) as $keyword) {
					$addkeywords[]=array('lid'=>$newpageid,'keyword'=>$keyword);
				}
			}
            $query=$sql->prepare("SELECT `id` FROM `page_terms` WHERE `type`='tag' AND `name`=? AND `resellerid`=? LIMIT 1");
            $query2=$sql->prepare("UPDATE `page_terms` SET `count`=`count`+1 WHERE `name`=? AND `resellerid`=? LIMIT 1");
            $query3=$sql->prepare("INSERT INTO `page_terms` (`name`,`search_name`,`type`,`count`,`resellerid`) VALUES (?,?,'tag','1',?)");
            $query4=$sql->prepare("SELECT `id` FROM `page_terms` WHERE `type`='tag' AND `name`=? AND `resellerid`=? LIMIT 1");
            $query5=$sql->prepare("INSERT INTO `page_terms_used` (`page_id`,`term_id`,`language_id`,`resellerid`) VALUES (?,?,?,?)");
			foreach ($addkeywords as $keyword) {
				unset($term_id);
                $query->execute(array($keyword['keyword'],$reseller_id));
				foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
					$term_id=$row['id'];
				}
				if (isset($term_id)) {
                    $query2->execute(array($keyword['keyword'],$reseller_id));
				} else {
                    $query3->execute(array($keyword['keyword'],strtolower(szrp($keyword['keyword'])),$reseller_id));
                    $query4->execute(array($keyword['keyword'],$reseller_id));
					foreach ($query4->fetchall(PDO::FETCH_ASSOC) as $row) {
						$term_id=$row['id'];
					}
				}
                $query5->execute(array($pageid,$term_id,$keyword['lid'],$reseller_id));
			}
            $template_file=$spracheResponse->table_add;
		} else {
			$template_file="Error: No language selected";
		}
	} else {
		$template_file="Unknown Error";
	}
} else if ($ui->st('d','get')=='dl' and $ui->id('id',19,'get')) {
    $id=$ui->id('id',19,'get');
	if (!isset($action)) {
        $query=$sql->prepare("SELECT p.`id`,p.`released`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`id`=? AND p.`resellerid`=? LIMIT 1");
        $query->execute(array($user_language,$id,$reseller_id));
		foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $page_title=$row['title'];
			if ($row['released']=='1') {
				$page_active=$gsprache->yes;
			} else {
				$page_active=$gsprache->no;
			}
            $query=$sql->prepare("SELECT `language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language`");
            $query->execute(array($id,$reseller_id));
			foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
				$p_languages[]=$row['language'];
			}
			if (($page_title==null or $page_title=='') and isset($p_languages)) {
                $query=$sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `language`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
                $query->execute(array($id,$p_languages[0],$reseller_id));
				foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
					$page_title=$row['title'];
				}
			} else if ($page_title==null or $page_title=='') {
				$page_title='';
				$p_languages=array();
			}
		}
		if (isset($page_active)) {
			$template_file="admin_page_pages_dl.tpl";
		} else {
			$template_file="Error: No ID";
		}
	} else if (isset($action) and $action=='dl') {
		$query=$sql->prepare("SELECT t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`page_id`=? AND u.`resellerid`=?");
        $query2=$sql->prepare("UPDATE `page_terms` SET `count`=`count`-1 WHERE `name`=? AND `resellerid`=? LIMIT 1");
		$query->execute(array($id,$reseller_id));
		foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $query2->execute(array($row['name'],$reseller_id));
		}
        $query=$sql->prepare("UPDATE `page_pages` SET `subpage`=`id` WHERE `subpage`=? AND `resellerid`=?");
        $query->execute(array($id,$reseller_id));
        $query=$sql->prepare("DELETE FROM `page_pages` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $query=$sql->prepare("DELETE FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
        $query->execute(array($id,$reseller_id));
        $query=$sql->prepare("DELETE FROM `page_terms_used` WHERE `page_id`=? AND `resellerid`=?");
        $query->execute(array($id,$reseller_id));
        $template_file=$spracheResponse->table_del;
	} else {
		$template_file="Error: No ID";
	}
} else if ($ui->st('d','get')=="md" and $ui->id('id',19,'get')) {
    $id=$ui->id('id',19,'get');
	if (!isset($action)) {
		$lang_avail=getlanguages($template_to_use);
		$table=array();
		foreach ($lang_avail as $lg) {
			$table[$lg]=array('title'=>false,'keywords'=>false,'text'=>false);
			$keywords_used[$lg]=array();
		}
		$query=$sql->prepare("SELECT `released`,`subpage`,`naviDisplay` FROM `page_pages` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query2=$sql->prepare("SELECT `id`,`language`,`title`,`text` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language`");
        $query3=$sql->prepare("SELECT t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE t.`type`='tag' AND u.`page_id`=? AND u.`language_id`=? AND u.`resellerid`=? ORDER BY t.`name` DESC");
		$query->execute(array($id,$reseller_id));
		foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
			$released=$row['released'];
			$subpage=$row['subpage'];
            $naviDisplay=$row['naviDisplay'];
			$query2->execute(array($id,$reseller_id));
			foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
				$keywords=array();
				$query3->execute(array($id,$row2['id'],$reseller_id));
				foreach ($query3->fetchall(PDO::FETCH_ASSOC) as $row3) {
					$keywords[]=$row3['name'];
					$keywords_used[$row2['language']][]=$row3['name'];
				}
				$table[$row2['language']]=array('title'=>$row2['title'],'keywords'=>implode(', ',$keywords),'text'=>$row2['text']);
			}
		}
		$subpages=array();
        $query=$sql->prepare("SELECT p.`id`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`resellerid`=? AND p.`type`='page' ORDER BY t.`title`");
        $query2=$sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
        $query->execute(array($user_language,$reseller_id));
		foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
			if ($row['id']!=$id) {
				$page_title=$row['title'];
				if ($row['title']==null or $row['title']=='') {
                    $query2->execute(array($row['id'],$reseller_id));
					foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
						$page_title=$row2['title'];
					}
				}
				$subpages[$row['id']]=$page_title;
			}
		}
		$keywords=array();
        $query=$sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='tag' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `count` DESC LIMIT 10");
		foreach ($lang_avail as $lg) {
			$keywords[$lg]=array();
			$query->execute(array($lg,$reseller_id));
			foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
				if (!in_array($row['name'],$keywords_used[$lg])) {
					$keywords[$lg][]=$row['name'];
				}
			}
		}
		$template_file="admin_page_pages_md.tpl";
	} else if (isset($action) and $action=='md' and ($ui->id('subpage',19,'post') or $ui->id('subpage','30','post')==0)) {
		if (is_object($ui->st('language','post'))) {
			foreach ($ui->st('language','post') as $key=>$lg) {
				$posted_languages[$key]=$lg;
			}
		} else {
			$posted_languages=array();
		}
		$countreduce=array();
		if (count($posted_languages)>0) {
			$addkeywords=array();
			if ($ui->id('subpage','30','post')==0) {
				$subpage=$id;
			} else {
				$subpage=$ui->id('subpage','30','post');
			}
            $query=$sql->prepare("UPDATE `page_pages` SET `released`=?,`subpage`=?,`naviDisplay`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($ui->id('released','1','post'),$subpage,$ui->active('naviDisplay','post'),$id,$reseller_id));
			$query=$sql->prepare("SELECT `id`,`language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
			$query->execute(array($id,$reseller_id));
			$lang_exist=array();
			foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
				$lang_exist[]=$row['language'];
				$keywords=array();
				foreach (preg_split('/\,/',preg_replace("/\,\s+/",',',preg_replace("/\s+/"," ",$ui->escaped('keywords','post',$row['language']))),-1,PREG_SPLIT_NO_EMPTY) as $keyword) {
					$keywords[]=$keyword;
				}
				if (in_array($row['language'],$posted_languages)) {
                    $query2=$sql->prepare("UPDATE `page_pages_text` SET `title`=?,`text`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($ui->htmlcode('title','post',$row['language']),$ui->escaped('text','post',$row['language']),$row['id'],$reseller_id));
					$keyword_exist=array();
					$query2=$sql->prepare("SELECT u.`term_id`,t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`page_id`=? AND u.`language_id`=? AND u.`resellerid`=?");
					$query2->execute(array($id,$row['id'],$reseller_id));
					foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
						$keyword_exist[]=$row2['name'];
						if (!in_array($row2['name'],$keywords)) {
                            $query2=$sql->prepare("DELETE FROM `page_terms_used` WHERE `term_id`=? AND `page_id`=? AND `language_id`=? AND `resellerid`=? LIMIT 1");
                            $query2->execute(array($row2['term_id'],$id,$row['id'],$reseller_id));
							$countreduce[]=$row2['name'];
						}
					}
					foreach ($keywords as $keyword) {
						if (!in_array($keyword,$keyword_exist)) {
							$addkeywords[]=array('lid'=>$row['id'],'keyword'=>$keyword);
						}
					}
				} else {
                    $query2=$sql->prepare("DELETE FROM `page_pages_text` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($row['id'],$reseller_id));
                    $query2=$sql->prepare("DELETE FROM `page_terms_used` WHERE `language_id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($row['id'],$reseller_id));
					$countreduce=$keywords;
				}
			}
			foreach ($posted_languages as $lg) {
				if (!in_array($lg,$lang_exist)) {
                    $query=$sql->prepare("INSERT INTO `page_pages_text` (`pageid`,`language`,`title`,`text`,`resellerid`) VALUES (?,?,?,?,?)");
                    $query->execute(array($id,$lg,$ui->htmlcode('title','post',$lg),$ui->escaped('text','post',$lg),$reseller_id));
                    $query=$sql->prepare("SELECT `id` FROM `page_pages_text` WHERE `pageid`=? `language`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($id,$lg,$reseller_id));
					foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
						$newpageid=$row['id'];
					}
					foreach (preg_split('/\,/',$ui->escaped('keywords','post',$lg),-1,PREG_SPLIT_NO_EMPTY) as $keyword) {
						$addkeywords[]=array('lid'=>$newpageid,'keyword'=>$keyword);
					}
				}
			}
			foreach ($addkeywords as $keyword) {
				unset($term_id);
				$query=$sql->prepare("SELECT `id` FROM `page_terms` WHERE `type`='tag' AND `name`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($keyword['keyword'],$reseller_id));
				foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
					$term_id=$row['id'];
				}
				if (isset($term_id)) {
                    $query=$sql->prepare("UPDATE `page_terms` SET `count`=`count`+1 WHERE `name`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($keyword['keyword'],$reseller_id));
				} else {
                    $query=$sql->prepare("INSERT INTO `page_terms` (`name`,`search_name`,`type`,`count`,`resellerid`) VALUES (?,?,'tag','1',?)");
                    $query->execute(array($keyword['keyword'],strtolower(szrp($keyword['keyword'])),$reseller_id));
                    $query=$sql->prepare("SELECT `id` FROM `page_terms` WHERE `type`='tag' AND `name`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($keyword['keyword'],$reseller_id));
					foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
						$term_id=$row['id'];
					}
				}
				$inserttermsused=$sql->prepare("INSERT INTO `page_terms_used` (`page_id`,`term_id`,`language_id`,`resellerid`) VALUES (?,?,?,?)");
				$inserttermsused->execute(array($id,$term_id,$keyword['lid'],$reseller_id));
			}
		} else {
			$query=$sql->prepare("SELECT t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`page_id`=? AND u.`resellerid`=?");
			$query->execute(array($id,$reseller_id));
			foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
				$countreduce[]=$row['name'];
			}
            $query=$sql->prepare("DELETE FROM `page_page` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id,$reseller_id));
            $query=$sql->prepare("DELETE FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
            $query=$sql->prepare("DELETE FROM `page_terms_used` WHERE `page_id`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
		}
		foreach ($countreduce as $keyword) {
			$updateterms=$sql->prepare("UPDATE `page_terms` SET `count`=`count`-1 WHERE `name`=? AND `resellerid`=? LIMIT 1");
			$updateterms->execute(array($keyword,$reseller_id));
		}
        $template_file=$spracheResponse->table_add;
	}
} else {
    if ($ui->smallletters('pageorder',4,'post')=='true') {
        foreach ($ui->id('pageid','30','post') as $pageid => $pageorder) {
            $pupdate=$sql->prepare("UPDATE `page_pages` SET `sort`=? WHERE `id`=? LIMIT 1");
            $pupdate->execute(array($pageorder,$pageid));
        }
    }
    $settings=$sql->prepare("SELECT `seo` FROM `page_settings` WHERE `resellerid`=? LIMIT 1");
    $settings->execute(array($reseller_id));
    foreach ($settings->fetchall(PDO::FETCH_ASSOC) as $row) {
        $seo=$row['seo'];
    }
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='at') {
        $orderby='t.`title` ASC';
    } else if ($ui->st('o','get')=='dt') {
        $orderby='t.`title` DESC';
    } else if ($ui->st('o','get')=='aa') {
        $orderby='p.`authorname` ASC, p.`id` ASC, p.`subpage` ASC';
    } else if ($ui->st('o','get')=='da') {
        $orderby='p.`authorname` DESC, p.`id` ASC, p.`subpage` ASC';
    } else if ($ui->st('o','get')=='ar') {
        $orderby='p.`released` ASC, p.`id` ASC, p.`subpage` ASC';
    } else if ($ui->st('o','get')=='dr') {
        $orderby='p.`released` DESC, p.`id` ASC, p.`subpage` ASC';
    } else if ($ui->st('o','get')=='as') {
        $orderby='p.`sort` ASC';
    } else if ($ui->st('o','get')=='ds') {
        $orderby='p.`date` DESC';
    } else if ($ui->st('o','get')=='ad') {
        $orderby='p.`date` ASC';
    } else if ($ui->st('o','get')=='dd') {
        $orderby='p.`date` DESC';
    } else if ($ui->st('o','get')=='ad') {
        $orderby='p.`subpage`, p.`id` ASC';
    } else if ($ui->st('o','get')=='di') {
        $orderby='p.`id` DESC';
    } else {
        $orderby='p.`id` ASC';
        $o='ai';
    }
    $query=$sql->prepare("SELECT p.`id`,p.`date`,p.`released`,p.`subpage`,p.`authorid`,p.`authorname`,p.`sort`,t.`title`,t.`shortlink`,t.`language` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`type`='page' AND p.`resellerid`=? GROUP BY p.`id` ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($user_language,$reseller_id));
    $table = array();
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        if ($row['released']=='1') {
            $released=$gsprache->yes;
        } else {
            $released=$gsprache->no;
        }
        $author=$row['authorname'];
        $query2=$sql->prepare("SELECT `cname`,`name`,`vname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query2->execute(array($row['authorid'],$reseller_id));
        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
            if (($row2['name']=='' or $row2['name']==null) and ($row2['vname']=='' or $row2['vname']==null)) {
                $author=$row2['cname'];
            } else {
                $author=$row2['vname'].' '.$row2['name'];
            }
        }
        unset($p_languages);
        $query3=$sql->prepare("SELECT `language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language`");
        $query3->execute(array($row['id'],$reseller_id));
        foreach ($query3->fetchall(PDO::FETCH_ASSOC) as $row3) {
            $p_languages[]=$row3['language'];
        }
        $page_title=$row['title'];
        if (($row['title']==null or $row['title']=='') and isset($p_languages[0])) {
            $query4=$sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `language`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
            $query4->execute(array($row['id'],$p_languages[0],$reseller_id));
            foreach ($query4->fetchall(PDO::FETCH_ASSOC) as $row4) {
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
                $link=$page_url.'/'. $user_language.'/'.strtolower(szrp($row['title'])).'/';
            } else {
                $link=$page_url.'/'. $row['language'].'/'.strtolower(szrp($row['title'])).'/';
            }
        }
        $explodedtime=explode(' ', $row['date']);
        $explodedtime2=explode('-', $explodedtime[0]);
        if ($user_language=='de') {
            $date=$explodedtime2[2].".".$explodedtime2[1].".".$explodedtime2[0]." ".$explodedtime[1];
        } else {
            $date=$explodedtime2[1].".".$explodedtime2[2].".".$explodedtime2[0]." ".$explodedtime[1];
        }
        array_push($table,array('id'=>$row['id'],'author'=>$author,'date'=>$date,'released'=>$released,'title'=>$page_title,'link'=>$link,'languages'=>$p_languages,'sort'=>$row['sort']));
    }
    $next=$start+$amount;
    $countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `page_pages` WHERE `resellerid`=?");
    $countp->execute(array($reseller_id));
    foreach ($countp->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $colcount=$row['amount'];
    }
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
    $link='<a href="admin.php?w=pp&amp;d=md&amp;o='.$o.'&amp;a=';
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
            $pages[]='<a href="admin.php?w=pp&amp;d=md&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'" class="bold">'.$i.'</a>';
        } else {
            $pages[]='<a href="admin.php?w=pp&amp;d=md&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'">'.$i.'</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file="admin_page_pages_list.tpl";
}