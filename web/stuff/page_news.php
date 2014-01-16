<?php

/**
 * File: page_news.php.
 * Author: Ulrich Block
 * Date: 13.05.12
 * Time: 10:31
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

if ($ui->id('id', 10, 'get')) {

    $page_id = $ui->id('id', 10, 'get');

} else if (isset($page_name) and $page_name != szrp($page_sprache->older) and isset($page_name) and $page_name != '' and $page_name != null and $page_name != false) {

    $pagesAvailable = array();

    $query = $sql->prepare("SELECT p.`id`,t.`title` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE `type`='news' AND t.`language`=? AND p.`released`=1 AND p.`resellerid`=0");
    $query->execute(array($user_language));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $pagesAvailable[szrp($row['title'])] = $row['id'];
    }

    if (array_key_exists(strtolower($page_name),$pagesAvailable)) {
        $page_id = $pagesAvailable[strtolower($page_name)];
    }
}

if ((isset($page_name) and $page_name != szrp($page_sprache->older) and isset($page_id) and is_numeric($page_id)) or $ui->id('id', 10, 'get')) {

    $query = $sql->prepare("SELECT p.`date`,p.`comments`,p.`authorname`,t.`id` AS `textID`,t.`title`,t.`text`,t.`id`,t.`language` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`id`=? AND `type`='news' AND t.`language`=? AND p.`released`='1' AND p.`resellerid`=0 LIMIT 1");
    $query2 = $sql->prepare("SELECT t.`name`,t.`type` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`language_id`=? AND u.`resellerid`='0' ORDER BY t.`name` DESC");
    $query->execute(array($page_id,$user_language));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $page_keywords = array();
        $allTags = array();
        $allCategories = array();

        $page_title = $row['title'];
        $page_text = nl2br($row['text']);
        $comments = $row['comments'];
        $authorname = $row['authorname'];
        $textID = $row['textID'];
        $query2->execute(array($textID));

        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

            $page_data->AddData('keywords', $row2['name']);

            if ($seo== 'Y' and $row2['type'] == 'tag') {
                $tagLink = $page_url. '/' . $user_language . '/' . szrp($page_sprache->tag) . '/' . szrp($row2['name']) . '/';
            } else if ($row2['type'] == 'tag') {
                $tagLink = $page_url.'/index.php?site=tag&amp;tag='.szrp($row2['name']);
            } else if ($seo== 'Y' and $row2['type'] == 'category') {
                $categoryLink = $page_url. '/' . $user_language . '/' . szrp($page_sprache->categories) . '/' . szrp($row2['name']) . '/';
            } else if ($row2['type'] == 'category') {
                $categoryLink = $page_url.'/index.php?site=categories&amp;tag='.szrp($row2['name']);
            }

            if ($row2['type'] == 'tag') {
                $allTags[] = array('name' => $row2['name'], 'link' => $tagLink,'href' => '<a href="'.$tagLink.'">'.$row2['name'].'</a>');
            } else if ($row2['type'] == 'category') {
                $allCategories[] = array('name' => $row2['name'], 'link' => $categoryLink,'href' => '<a href="'.$categoryLink.'">'.$row2['name'].'</a>');
            }
        }

        $pageLanguage = $row['language'];

        $date = ($pageLanguage == 'de') ? date('d.m.Y', strtotime($row['date'])): date('m.d.Y',strtotime($row['date']));

    }

    // https://github.com/easy-wi/developer/issues/62
    $langLinks = array();

    $query = $sql->prepare("SELECT `title`,`language` FROM `page_pages_text` WHERE `pageid`=?");
    $query->execute(array($page_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $tempLanguage = getlanguagefile('general', $row['language'], 0);
        $langLinks[$row['language']] = ($page_data->seo== 'Y') ? szrp($tempLanguage->news) . '/' . szrp($row['title'])  : '?s=news&amp;id='.$page_id;
    }

    $page_data->langLinks($langLinks);

    if (isset($textID) or isset($comments)) {

        $email = '';
        $author = '';
        $url = '';
        $comment = '';
        if (isset($comments) and $comments == 'Y') {

            if ($ui->escaped('comment', 'post')) {

                $comment = $ui->escaped('comment', 'post');

                if (strlen($ui->escaped('comment', 'post')) <= $commentMinLength) {
                    $error = true;
                }

                if (!isset($admin_id) and !isset($user_id)){

                    $email = $ui->ismail('email', 'post');
                    $author = $ui->names('author', 255, 'post');

                    if ($mailRequired == 'Y' and !$ui->ismail('email', 'post')) {
                        $error = true;
                    }

                    if (!$ui->names('author', 255, 'post')) {
                        $error = true;
                    }

                } else {

                    $query = $sql->prepare("SELECT `cname`,`mail` FROM `userdata` WHERE `id`=? LIMIT 1");
                    $query->execute(array((isset($admin_id)) ? $admin_id : $user_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $author = $row['cname'];
                        $email = $row['mail'];
                    }

                }

                if (!isset($error)) {

                    $isSpam = 'N';
                    $comment = '';
                    $posted = true;

                    $replyTo = $ui->id('replyTo', 19, 'post');

                    if ($ui->url('url', 'post')) {
                        $url = $ui->url('url', 'post');
                    }

                    if ($ui->domain('url', 'post')) {
                        $url = 'http://' . $ui->domain('url', 'post');
                    }
                    if (isset($spamFilter) and $spamFilter != 'Y') {
                        $spamArray = checkForSpam();

                        if (count($spamArray) > 0) {
                            $isSpam = 'Y';
                        } else {

                            $spamArray = checkForSpam($url);

                            if (count($spamArray) > 0) {
                                $isSpam = 'Y';
                            }
                        }

                    } else {
                        $spamArray = array();
                    }

                    $spamReason = implode(', ', $spamArray);

                    if (isset($commentsModerated) and $commentsModerated == 'Y' and (!isset($admin_id) and !isset($user_id))) {

                        $query = $sql->prepare("INSERT INTO `page_comments` (`date`,`moderateAccepted`,`pageTextID`,`replyTo`,`authorname`,`homepage`,`comment`,`ip`,`dns`,`markedSpam`,`spamReason`,`email`) VALUES (NOW(),'N',?,?,?,?,?,?,?,?,?,?)");
                        $query->execute(array($textID, $replyTo, $author, $url, $ui->escaped('comment', 'post'), $ui->ip('REMOTE_ADDR', 'server'), gethostbyaddr($ui->ip4('REMOTE_ADDR', 'server')), $isSpam, $spamReason, $email));

                        $_SESSION['toBeModerated'][] = $sql->lastInsertId();

                    } else {
                        $query = $sql->prepare("INSERT INTO `page_comments` (`date`,`moderateAccepted`,`pageTextID`,`replyTo`,`authorname`,`homepage`,`comment`,`ip`,`dns`,`markedSpam`,`spamReason`,`email`) VALUES (NOW(),'Y',?,?,?,?,?,?,?,?,?,?)");
                        $query->execute(array($textID, $replyTo, $author, $url, $ui->escaped('comment', 'post'), $ui->ip('REMOTE_ADDR', 'server'), gethostbyaddr($ui->ip4('REMOTE_ADDR', 'server')), $isSpam, $spamReason, $email));
                    }

                }
            }

            $commentArray = array();
            $token = md5(passwordgenerate(10));

            $_SESSION['news'][$textID] = $token;

            $OR = '';

            if (isset($_SESSION['toBeModerated']) and count($_SESSION['toBeModerated']) > 0) {

                foreach ($_SESSION['toBeModerated'] as $id) {
                    if (isid($id, 19)) {
                        $OR .= ' OR `commentID`=' . $id;
                    }
                }
            }

            $query = $sql->prepare("SELECT `commentID`,`replyTo`,`date`,`authorname`,`homepage`,`comment` FROM `page_comments` WHERE `pageTextID`=? AND ((`markedSpam`!='Y' AND `moderateAccepted`='Y') $OR) AND `resellerid`=0 ORDER BY `replyTo` DESC,`commentID` DESC");
            $query->execute(array($textID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $commentDate = (isset($pageLanguage) and $pageLanguage == 'de') ? date('d.m.Y H:i', strtotime($row['date'])) : date('m.d.Y H:i', strtotime($row['date']));
                $commentArray[] = array('commentID' => $row['commentID'], 'replyTo' => $row['replyTo'], 'homepage' => $row['homepage'], 'date' => $commentDate,'author' => htmlentities($row['authorname']),'comment' => htmlentities($row['comment']));
            }
        }

        $page_data->setCanonicalUrl($s, $textID);

        $template_file = 'page_news_single.tpl';

    } else {
        $template_file = 'page_404.tpl';
    }

} else if (isset($page_name) and $page_name!=szrp($page_sprache->older) and isset($page_name) and $page_name!='' and $page_name != null and $page_name != false and !isset($page_id) and $ui->smallletters('preview',4, 'get') != 'true') {

    $template_file = 'page_404.tpl';

} else if (isset($admin_id) and $ui->smallletters('preview',4, 'get') == 'true') {

    if (is_array($ui->escaped('text', 'post')) or is_object($ui->escaped('text', 'post'))) {
        foreach ($ui->escaped('text', 'post') as $key=>$value) {
            $page_title = $ui->htmlcode('title', 'post',$key);
            $page_text=nl2br($value);
        }
    } else {
        $page_title = $ui->escaped('title', 'post');
        $page_text=nl2br($ui->escaped('text', 'post'));
    }

    $allTags = array();
    $allCategories = array();
    $comments = array();
    $category_tags = array();
    $tag_tags = array();

    $template_file = (isset($page_title)) ? 'page_news_single.tpl' : 'page_404.tpl';

} else {

    $news = array();
    $category_tags = array();
    $tag_tags = array();
    $allTags = array();
    $allCategories = array();

    $query = $sql->prepare("SELECT COUNT(p.`id`) AS `amount` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`released`='1' AND p.`type`='news' AND t.`language`=? AND p.`resellerid`=0");
    $query->execute(array($user_language));
    $totalCount = $query->fetchColumn();

    $pagesCount = ceil($totalCount / $maxnews);

    if (isset($page_name) and $page_name == szrp($page_sprache->older) and (isset($page_count) and isid($page_count, 255)) or ($ui->id('start', 255, 'get'))) {

        if ($ui->id('start', 255, 'get') and $ui->id('start', 255, 'get') <= $pagesCount) {
            $pageOpen = $ui->id('start', 255, 'get');
        } else if (isset($page_count) and $page_count <= $pagesCount) {
            $pageOpen = $page_count;
        } else {
            $pageOpen = $pagesCount;
        }

        $startLooking = $pageOpen * $maxnews;
    }

    if (!isset($startLooking) or $startLooking < 0) {
        $startLooking = 0;
    }

    if (!isset($pageOpen) or $pageOpen < 0) {
        $pageOpen = 0;
    }

    $query = $sql->prepare("SELECT p.`id`,p.`date`,p.`comments`,p.`authorname`,t.`id` AS `textID`,t.`title`,t.`text`,t.`language` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`released`='1' AND p.`type`='news' AND t.`language`=? AND p.`resellerid`=0 ORDER BY `date` DESC LIMIT $startLooking,$maxnews");
    $query2 = $sql->prepare("SELECT t.`name`,t.`type` FROM `page_terms_used` u LEFT JOIN `page_terms` t ON u.`term_id`=t.`id` WHERE u.`language_id`=? AND u.`resellerid`=0 ORDER BY t.`name` DESC");
    $query3 = $sql->prepare("SELECT COUNT(`commentID`) as `amount` FROM `page_comments` WHERE `pageTextID`=? AND `resellerID`=0");

    $query->execute(array($user_language));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $page_title = $row['title'];
        $page_text = nl2br($row['text']);

        if ($seo == 'Y') {
            $link = $page_url . '/' . $user_language . '/' . szrp($gsprache->news) . '/' . szrp($row['title']) . '/';
        } else {
            $link = $page_url .'/index.php?site=news&amp;id=' . $row['id'];
        }

        $href = '<a href="' . $link . '">' . $row['title'] . '</a>';

        $query2->execute(array($row['textID']));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

            $page_data->AddData('keywords', $row2['name']);

            if ($page_data->seo == 'Y' and $row2['type'] == 'tag') {
                $tagLink = $page_url. '/' . $user_language . '/' . szrp($page_sprache->tag) . '/' . szrp($row2['name']) . '/';
            } else if ($row2['type'] == 'tag') {
                $tagLink = $page_url.'/index.php?site=tag&amp;tag='.szrp($row2['name']);
            } else if ($seo== 'Y' and $row2['type'] == 'category') {
                $categoryLink = $page_url. '/' . $user_language . '/' . szrp($page_sprache->categories) . '/' . szrp($row2['name']) . '/';
            } else if ($row2['type'] == 'category') {
                $categoryLink = $page_url.'/index.php?site=categories&amp;tag=' . szrp($row2['name']);
            }

            $page_data->AddData('keywords', $row2['name']);

            if ($row2['type'] == 'tag') {
                $allTags[] = array('name' => $row2['name'], 'link' => $tagLink, 'href' => '<a href="' . $tagLink . '">' . $row2['name'] . '</a>');
                $tag_tags[] = array('name' => $row2['name'], 'link' => $tagLink, 'href' => '<a href="' . $tagLink . '">' . $row2['name'] . '</a>');
            } else if ($row2['type'] == 'category') {
                $allCategories[] = array('name' => $row2['name'], 'link' => $categoryLink, 'href' => '<a href="' . $categoryLink . '">' . $row2['name'] . '</a>');
                $category_tags[] = array('name' => $row2['name'], 'link' => $categoryLink, 'href' => '<a href="' . $categoryLink . '">' . $row2['name'] . '</a>');
            }
        }

        $date = ($row['language'] == 'de') ? date('d.m.Y',strtotime($row['date'])) : date('m.d.Y',strtotime($row['date']));

        if ($row['comments'] == 'Y') {
            $query3->execute(array($row['textID']));
            $commentCount = $query3->fetchColumn();
        } else {
            $commentCount = 0;
        }
        $news[] = array('date' => $date,'title' => $page_title,'text' => $page_text,'href' => $href,'link' => $link,'tags' => $tag_tags,'categories' => $category_tags,'comments' => $row['comments'], 'commentCount' => $commentCount,'authorname' => $row['authorname']);
    }

    $paginationLink = ($page_data->seo == 'Y') ? $page_url. '/' . $user_language . '/' . szrp($gsprache->news) . '/' . szrp($page_sprache->older) . '/' :  $page_url.'/index.php?site=news&amp;start=';

    // https://github.com/easy-wi/developer/issues/62

    $langLinks = array();

    foreach ($languages as $l) {
        $tempLanguage = getlanguagefile('general', $l, 0);
        $langLinks[$l] = ($page_data->seo == 'Y') ? szrp($tempLanguage->news)  : '?s=news';
    }

    $page_data->langLinks($langLinks);
    $page_data->setCanonicalUrl($s);

    $template_file = 'page_news.tpl';

}