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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['cms_news']) or $reseller_id != 0) {
	header('Location: admin.php');
    die;
}

$sprache = getlanguagefile('page', $user_language, $resellerLockupID);
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



// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {

	unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_roots_add.tpl' : 'admin_roots_md.tpl';

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    // At this point all variables are defined that can come from the user
    $id = ($ui->id('id', 19, 'post')) ? $ui->id('id', 19, 'post') : $ui->id('id', 19, 'get');

    // Default variables
    $categories = array();
    $keywords = array();
    $categories_used = array();
    $keywords_used = array();
    $author = '';

    $lang_avail = getlanguages($template_to_use);

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        /**if (is_file(EASYWIDIR . '/css/' . $template_to_use . '/summernote.css')) {
            $htmlExtraInformation['css'][] = '<link href="css/' . $template_to_use . '/summernote.css" rel="stylesheet">' . "\n";
        } else {
            $htmlExtraInformation['css'][] = '<link href="css/default/summernote.css" rel="stylesheet">' . "\n";
        }

        if (is_file(EASYWIDIR . '/js/' . $template_to_use . '/summernote.js')) {
            $htmlExtraInformation['js'][] = '<link href="js/' . $template_to_use . '/summernote.js"  type="text/javascript">' . "\n";
        } else {
            $htmlExtraInformation['js'][] = '<link href="js/default/summernote.js"  type="text/javascript">' . "\n";
        }

        if  ($user_language == 'de') {
            if (is_file(EASYWIDIR . '/js/' . $template_to_use . '/summernote-de-DE.js')) {
                $htmlExtraInformation['js'][] = '<link href="js/' . $template_to_use . '/summernote-de-DE.js" type="text/javascript">' . "\n";
            } else {
                $htmlExtraInformation['js'][] = '<link href="js/default/summernote-de-DE.js" type="text/javascript">' . "\n";
            }
        }

        foreach ($lang_avail as $lg) {
            $htmlExtraInformation['js'][] = "<script type=\"text/javascript\"> $(document).ready(function() { $('#text[{$lg}]').summernote({height: 300});});</script>" . "\n";
        }**/

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $query = $sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='category' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `name` DESC");
            $query2 = $sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='tag' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `count` DESC LIMIT 10");

            foreach ($lang_avail as $lg) {

                $categories[$lg] = array();
                $keywords[$lg] = array();

                $query->execute(array($lg, $resellerLockupID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $categories[$lg][] = $row['name'];
                }

                $query2->execute(array($lg, $resellerLockupID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $keywords[$lg][] = $row2['name'];
                }
            }

            $template_file = 'admin_page_news_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $table = array();
            foreach ($lang_avail as $lg) {
                $table[$lg] = array('title' => false, 'keywords' => false, 'text' => false, 'categories' => array());
                $keywords_used[$lg] = array();
                $categories_used[$lg] = array();
            }

            $query = $sql->prepare("SELECT `released`,`comments` FROM `page_pages` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query2 = $sql->prepare("SELECT `id`,`language`,`title`,`text` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language`");
            $query3 = $sql->prepare("SELECT t.`name`,t.`type` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`page_id`=? AND u.`language_id`=? AND u.`resellerid`=? ORDER BY t.`name` DESC");
            $query->execute(array($id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $released = $row['released'];
                $comments = $row['comments'];

                $query2->execute(array($id, $resellerLockupID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                    $query3->execute(array($id, $row2['id'], $resellerLockupID));
                    while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
                        if ($row3['type'] == 'tag') {
                            $keywords_used[$row2['language']][] = $row3['name'];
                        } else {
                            $categories_used[$row2['language']][] = $row3['name'];
                        }
                    }

                    $table[$row2['language']] = array('title' => $row2['title'], 'keywords' => implode(', ', $keywords_used[$row2['language']]), 'categories' => (is_array($categories_used[$row2['language']])) ? $categories_used[$row2['language']] : array(), 'text' => $row2['text']);
                }
            }

            // Check if database entry exists and if not display 404 page
            if  ($query->rowCount() > 0) {

                $newsTitle = '';

                if (isset($table[$user_language]['title'])) {
                    $newsTitle = $table[$user_language]['title'];
                } else if (isset($table[$default_language]['title'])) {
                    $newsTitle = $table[$default_language]['title'];
                } else {
                    foreach ($lang_avail as $lg) {
                        if (!empty($newsTitle) and isset($table[$lg]['title'])) {
                            $newsTitle = $table[$lg]['title'];
                            break;
                        }
                    }
                }

                $query = $sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='category' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `name` DESC");
                $query2 = $sql->prepare("SELECT `name` FROM `page_terms` WHERE `type`='tag' AND `language`=? AND `resellerid`=? GROUP BY `name` ORDER BY `count` DESC LIMIT 10");
                foreach ($lang_avail as $lg) {
                    $categories[$lg] = array();
                    $keywords[$lg] = array();
                    $query->execute(array($lg, $resellerLockupID));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        if (!isset($categories_used[$lg]) or !in_array($row['name'], $categories_used[$lg])) {
                            $categories[$lg][] = $row['name'];
                        }
                    }
                    $query2->execute(array($lg, $resellerLockupID));
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                        if (!isset($keywords_used[$lg]) or !in_array($row2['name'], $keywords_used[$lg])) {
                            $keywords[$lg][] = $row2['name'];
                        }
                    }
                }

                $template_file = 'admin_page_news_md.tpl';

            } else {
                $template_file = 'admin_404.tpl';
            }

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        $posted_languages = array();
        $addterms = array();
        $countreduce = array();
        $rowCount = 0;

        if (is_object($ui->st('language', 'post'))) {
            foreach ($ui->st('language', 'post') as $k => $v) {
                $posted_languages[$k] = $v;
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            $query = $sql->prepare("SELECT `cname`,`name`,`vname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($admin_id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $author = (($row['name'] == '' or $row['name'] == null) and ($row['vname'] == '' or $row['vname'] == null)) ? $row['cname'] : $row['vname'] . ' ' . $row['name'];
            }

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {
                $query = $sql->prepare("INSERT INTO `page_pages` (`released`,`authorid`,`authorname`,`date`,`type`,`comments`,`resellerid`) VALUES (?,?,?,NOW(),'news',?,?)");
                $query->execute(array($ui->id('released', 1, 'post'), $admin_id, $author, $ui->active('comments', 'post'), $resellerLockupID));

                $rowCount = $query->rowCount();

                $id = $sql->lastInsertId();

            }

            $query = $sql->prepare("UPDATE `page_pages` SET `released`=?,`comments`=?,`subpage`=`id` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($ui->id('released', 1, 'post'), $ui->active('comments', 'post'), $id, $resellerLockupID));
            $rowCount += (int) $query->rowCount();

            if (count($posted_languages) > 0) {

                $addterms = array();
                $addCategories = array();
                $lang_exist = array();

                $query = $sql->prepare("SELECT `id`,`language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
                $query2 = $sql->prepare("UPDATE `page_pages_text` SET `title`=?,`text`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query3 = $sql->prepare("SELECT u.`term_id`,t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE t.`type`='tag' AND u.`page_id`=? AND u.`language_id`=? AND u.`resellerid`=?");
                $query4 = $sql->prepare("SELECT u.`term_id`,t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE t.`type`='category' AND u.`page_id`=? AND u.`language_id`=? AND u.`resellerid`=?");
                $query5 = $sql->prepare("DELETE FROM `page_terms_used` WHERE `term_id`=? AND `page_id`=? AND `language_id`=? AND `resellerid`=? LIMIT 1");
                $query6 = $sql->prepare("DELETE FROM `page_pages_text` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query7 = $sql->prepare("DELETE FROM `page_terms_used` WHERE `language_id`=? AND `resellerid`=? LIMIT 1");

                $query->execute(array($id, $resellerLockupID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                    $keywords = array();
                    $categories = array();

                    $lang_exist[] = $row['language'];
                    foreach (preg_split('/\,/',preg_replace("/\,\s+/", ',',preg_replace("/\s+/"," ", $ui->escaped('keywords', 'post', $row['language']))), -1, PREG_SPLIT_NO_EMPTY) as $keyword) {
                        $keywords[] = $keyword;
                    }

                    if (is_object($ui->escaped('categories', 'post', $row['language']))) {
                        foreach ($ui->escaped('categories', 'post', $row['language']) as $category) {
                            $categories[] = $category;
                        }
                    }

                    if (in_array($row['language'], $posted_languages)) {

                        $keyword_exist = array();
                        $category_exist = array();

                        $query2->execute(array($ui->htmlcode('title', 'post', $row['language']), $ui->escaped('text', 'post', $row['language']), $row['id'], $resellerLockupID));
                        $rowCount += $query2->rowCount();

                        $query3->execute(array($id, $row['id'], $resellerLockupID));
                        while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
                            $keyword_exist[] = $row3['name'];

                            if (!in_array($row3['name'], $keywords)) {

                                $countreduce[] = $row3['name'];

                                $query5->execute(array($row3['term_id'], $id, $row['id'], $resellerLockupID));
                                $rowCount += $query5->rowCount();
                            }
                        }

                        $query4->execute(array($id, $row['id'], $resellerLockupID));
                        while ($row4 = $query4->fetch(PDO::FETCH_ASSOC)) {
                            $category_exist[] = $row4['name'];

                            if (!in_array($row4['name'], $categories)) {

                                $countreduce[] = $row4['name'];

                                $query5->execute(array($row4['term_id'], $id, $row['id'], $resellerLockupID));
                                $rowCount += $query5->rowCount();
                            }
                        }

                        foreach ($keywords as $keyword) {
                            if (!in_array($keyword, $keyword_exist)) {
                                $addterms[$row['language']][] = array('lid' => $row['id'], 'word' => $keyword, 'termtype' => 'tag');
                            }
                        }

                        foreach ($categories as $category) {
                            if (!in_array($category, $category_exist)) {
                                $addterms[$row['language']][] = array('lid' => $row['id'], 'word' => $category, 'termtype' => 'category');
                            }
                        }

                    } else {
                        $query6->execute(array($row['id'], $resellerLockupID));
                        $rowCount += $query6->rowCount();

                        $query7->execute(array($row['id'], $resellerLockupID));
                        $rowCount += $query7->rowCount();

                        $countreduce = $keywords;
                    }
                }

                $query = $sql->prepare("INSERT INTO `page_pages_text` (`pageid`,`language`,`title`,`text`,`resellerid`) VALUES (?,?,?,?,?)");
                foreach ($posted_languages as $lg) {
                    if (!in_array($lg, $lang_exist)) {

                        $query->execute(array($id, $lg, $ui->htmlcode('title', 'post', $lg), $ui->escaped('text', 'post', $lg), $resellerLockupID));
                        $rowCount += (int) $query->rowCount();

                        $newpageid = $sql->lastInsertId();

                        foreach (preg_split('/\,/', $ui->escaped('keywords', 'post', $lg), -1, PREG_SPLIT_NO_EMPTY) as $keyword) {
                            $addterms[$lg][] = array('lid' => $newpageid, 'word' => $keyword, 'termtype' => 'tag');
                        }

                        $categoryArray = (array) $ui->escaped('categories', 'post', $lg);
                        foreach ($categoryArray as $category) {
                            $addterms[$lg][] = array('lid' => $newpageid, 'word' => $category, 'termtype' => 'category');
                        }
                    }
                }

                $query = $sql->prepare("SELECT `id` FROM `page_terms` WHERE `language`=? AND `type`=? AND `name`=? AND `resellerid`=? LIMIT 1");
                $query2 = $sql->prepare("UPDATE `page_terms` SET `count`=`count`+1 WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query3 = $sql->prepare("INSERT INTO `page_terms` (`language`,`name`,`search_name`,`type`,`count`,`resellerid`) VALUES (?,?,?,?, 1,?)");
                $query4 = $sql->prepare("INSERT INTO `page_terms_used` (`page_id`,`term_id`,`language_id`,`resellerid`) VALUES (?,?,?,?)");

                foreach ($addterms as $lg => $terms) {

                    foreach ($terms as $term) {

                        if (isset($term['termtype']) and isset($term['word'])) {
                            $query->execute(array($lg, $term['termtype'], $term['word'], $resellerLockupID));
                            $term_id = $query->fetchColumn();

                            if (isid($term_id, 19)) {

                                $query2->execute(array($term_id, $resellerLockupID));
                                $rowCount += $query2->rowCount();

                            } else {
                                $query3->execute(array($lg, $term['word'], strtolower(szrp($term['word'])), $term['termtype'], $resellerLockupID));
                                $rowCount += $query3->rowCount();

                                $term_id = $sql->lastInsertId();
                            }

                            if (isid($term_id, 19)) {
                                $query4->execute(array($id, $term_id, $term['lid'], $resellerLockupID));
                                $rowCount += (int) $query4->rowCount();
                            }
                        }
                    }
                }

            } else {

                $query = $sql->prepare("SELECT t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`page_id`=? AND u.`resellerid`=?");
                $query->execute(array($id, $resellerLockupID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $countreduce[] = $row['name'];
                }

                $query = $sql->prepare("DELETE FROM `page_page` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id, $resellerLockupID));
                $rowCount += (int) $query->rowCount();

                $query = $sql->prepare("DELETE FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
                $query->execute(array($id, $resellerLockupID));
                $rowCount += (int) $query->rowCount();

                $query = $sql->prepare("DELETE FROM `page_terms_used` WHERE `page_id`=? AND `resellerid`=?");
                $query->execute(array($id, $resellerLockupID));
                $rowCount += (int) $query->rowCount();

            }

            $query = $sql->prepare("UPDATE `page_terms` SET `count`=`count`-1 WHERE `name`=? AND `resellerid`=? LIMIT 1");
            foreach ($countreduce as $keyword) {
                $query->execute(array($keyword, $resellerLockupID));
                $rowCount += (int) $query->rowCount();
            }

            // Check if a row was affected during insert or update
            if ($rowCount > 0) {
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }

            // An error occurred during validation unset the redirect information and display the form again
        } else {
            unset($header, $text);
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_roots_add.tpl' : 'admin_roots_md.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and ($ui->id('id', 10, 'get') or $ui->id('id', 19, 'post'))) {

    // Define the ID variable which will be used at the form and SQLs
    $id = ($ui->id('id', 19, 'post')) ? $ui->id('id', 19, 'post') : $ui->id('id', 19, 'get');

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        $query = $sql->prepare("SELECT p.`id`,p.`released`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`id`=? AND p.`resellerid`=? LIMIT 1");
        $query->execute(array($user_language, $id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $page_active = ($row['released'] == 1) ? $gsprache->yes : $gsprache->no;

            $query2 = $sql->prepare("SELECT `language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language`");
            $query2->execute(array($id, $resellerLockupID));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                $p_languages[] = $row2['language'];
            }

            $page_title = $row['title'];

            if (($row['title'] == null or $row['title'] == '') and isset($p_languages)) {
                $query3 = $sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `language`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
                $query3->execute(array($row['id'], $p_languages[0], $resellerLockupID));
                while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
                    $page_title = $row3['title'];
                }
            } else if ($row['title'] == null or $row['title'] == '') {
                $page_title = '';
                $p_languages = array();
            }
        }

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_page_news_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl') {

        $removedCount = 0;

        $query = $sql->prepare("SELECT t.`name` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`page_id`=? AND u.`resellerid`=?");
        $query2 = $sql->prepare("UPDATE `page_terms` SET `count`=`count`-1 WHERE `name`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $query2->execute(array($row['name'], $resellerLockupID));
            $removedCount += $query2->rowCount();
        }

        $removedCount += $query->rowCount();

        $query = $sql->prepare("DELETE FROM `page_pages` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        $removedCount += $query->rowCount();

        $query = $sql->prepare("DELETE FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=?");
        $query->execute(array($id, $resellerLockupID));
        $removedCount += $query->rowCount();

        $query = $sql->prepare("DELETE FROM `page_terms_used` WHERE `page_id`=? AND `resellerid`=?");
        $query->execute(array($id, $resellerLockupID));
        $removedCount += $query->rowCount();

        $template_file = $spracheResponse->table_del;

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($removedCount > 0) {

            $template_file = $spracheResponse->table_del;

            // Nothing was deleted, display an error
        } else {
            $template_file = $spracheResponse->error_table;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

// List the available entries
} else {

    $table = array();

    $o = ($ui->st('o', 'get')) ? (string) $ui->st('o', 'get') : 'di';

    if ($o == 'at') {
        $orderby = 't.`title` ASC';
    } else if ($o == 'dt') {
        $orderby = 't.`title` DESC';
    } else if ($o == 'aa') {
        $orderby = 'p.`authorname` ASC, p.`id` ASC';
    } else if ($o == 'da') {
        $orderby = 'p.`authorname` DESC, p.`id` ASC';
    } else if ($o == 'ar') {
        $orderby = 'p.`released` ASC, p.`id` ASC';
    } else if ($o == 'dr') {
        $orderby = 'p.`released` DESC, p.`id` ASC';
    } else if ($o == 'ad') {
        $orderby = 'p.`date` ASC';
    } else if ($o == 'dd') {
        $orderby = 'p.`date` DESC';
    } else if ($o == 'ad') {
        $orderby = 'p.`id` ASC';
    } else {
        $orderby = 'p.`id` DESC';
    }

    $seo = 'N';
    $pageUrl = 'http://' . $ui->escaped('SERVER_NAME', 'server') . '/' . str_replace(array('/admin.php', 'admin.php'), '', $ui->escaped('SCRIPT_NAME', 'server'));

    $query = $sql->prepare("SELECT `seo`,`pageurl` FROM `page_settings` WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $seo = $row['seo'];
        $pageUrl = $row['pageurl'];
    }

    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `page_pages` WHERE `type`='news' AND `resellerid`=?");
    $query->execute(array($resellerLockupID));
    $colcount = $query->fetchColumn();

    $amount = (isset($amount)) ? $amount : 20;
    $start = (isset($start) and $start < $colcount) ? $start : 0;
    $next = (isset($amount) and ($start + $amount) < $colcount) ? ($start + $amount) : 20;
    $vor = ($colcount > $next) ? $start + $amount : $start;
    $zur = (($start - $amount) > -1) ? $start - $amount : $start;
    $pageamount = ceil($colcount / $amount);

    $link = '<a href="admin.php?w=pn&amp;d=md&amp;o=' . $o . '&amp;a=' . $amount;
    $link .= ($start == 0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';

    $pages[] = $link;
    $i = 2;

    while ($i <= $pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[] = ($start == $selectpage) ? '<a href="admin.php?w=pn&amp;d=md&amp;o=' . $o . '&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=pn&amp;d=md&amp;o=' . $o . '&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        $i++;
    }
    $pages = implode(', ', $pages);

    $query = $sql->prepare("SELECT p.`id`,p.`date`,p.`released`,p.`authorid`,p.`authorname`,t.`title`,t.`shortlink`,t.`language` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` AND t.`language`=? WHERE p.`type`='news' AND p.`resellerid`=? GROUP BY p.`id` ORDER BY $orderby LIMIT $start,$amount");
    $query2 = $sql->prepare("SELECT `cname`,`name`,`vname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query3 = $sql->prepare("SELECT `language` FROM `page_pages_text` WHERE `pageid`=? AND `resellerid`=? ORDER BY `language`");
    $query4 = $sql->prepare("SELECT `title` FROM `page_pages_text` WHERE `pageid`=? AND `language`=? AND `resellerid`=? ORDER BY `language` LIMIT 1");
    $query->execute(array($user_language, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {


        $author = $row['authorname'];
        $page_title = $row['title'];

        if (!isset($titleLanguages[$row['language']])) {
            $titleLanguages[$row['language']] = array('page' => getlanguagefile('page', $row['language'],0), 'general' => getlanguagefile('general', $row['language'],0));
        }

        $released = ($row['released'] == 1) ? $gsprache->yes : $gsprache->no;

        $query2->execute(array($row['authorid'], $resellerLockupID));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
            $author = (($row2['name'] == '' or $row2['name'] == null) and ($row2['vname'] == '' or $row2['vname'] == null)) ? $row2['cname'] : $row2['vname'] . ' ' . $row2['name'];
        }

        unset($p_languages);

        $query3->execute(array($row['id'], $resellerLockupID));
        while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
            $p_languages[] = $row3['language'];
        }

        if (($row['title'] == null or $row['title'] == '') and isset($p_languages[0])) {

            $query4->execute(array($row['id'], $p_languages[0], $resellerLockupID));
            $page_title = $query4->fetchColumn();

        } else if ($row['title'] == null or $row['title'] == '') {
            $page_title = '';
            $p_languages = array();
        }

        $link = ($seo == 'N') ? $page_url . '/index.php?site=page&amp;id=' . $row['id'] : $page_url. '/' . $row['language'] . '/' . szrp($titleLanguages[$row['language']]['general']->news) . '/' . strtolower(szrp($row['title'])) . '/';

        $date = ($user_language == 'de') ? date('d.m.Y H:m:s', strtotime($row['date'])) : $row['date'];

        $table[] = array('id' => $row['id'], 'author' => $author, 'date' => $date, 'released' => $released, 'title' => $page_title, 'link' => $link, 'languages' => $p_languages);

    }

    $template_file = 'admin_page_news_list.tpl';
}