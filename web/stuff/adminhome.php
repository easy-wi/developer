<?php

/**
 * File: adminhome.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !isanyadmin($admin_id) and !rsellerpermisions($admin_id))) {
	header('Location: login.php');
	die('No acces');
}

$sprache_bad = getlanguagefile('home', $user_language, $reseller_id);
$resellerid = ($reseller_id != 0 and $admin_id != $reseller_id) ? $admin_id : $reseller_id;
$reseller_brandname = $rSA['brandname'];
$counttickets_open = 0;
$counttickets_unanswered = 0;
$crashedArray = array('gsCrashed' => 0,'gsPWD' => 0,'gsTag' => 0,'ticketsOpen' => 0,'tickets' => 0,'ticketsResellerOpen' => 0,'ticketsReseller' => 0,'masterserver' => 0,'ts3Master' => 0,'ts3' => 0,'virtualHosts' => 0);
$removed = array();
$tag_removed = array();
$crashed = array();

$query = $sql->prepare("SELECT `stopped`,`serverid`,CONCAT(`serverip`,':',`port`) AS `server`,`userid`,`war`,`brandname`,`queryName`,`queryPassword` FROM `gsswitch` WHERE `active`='Y' AND `resellerid`=?");
$query->execute(array($resellerid));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $war = $row['war'];
    $brandname = $row['brandname'];
    $password = $row['queryPassword'];
    $name = $row['queryName'];

	if ($name != "OFFLINE" and $row['stopped'] == 'N' and $war == "Y" and $password == "N") {
		$removed[] = array('userid' => $row['userid'], 'username' => getusername($row['userid']),'address' => $row['server']);
        $crashedArray['gsPWD']++;

	} else if ($name == "OFFLINE" and $row['stopped'] == 'N') {
		$crashed[] = array('userid' => $row['userid'], 'username' => getusername($row['userid']),'address' => $row['server']);
        $crashedArray['gsCrashed']++;
	}

	if (isset($name) and $name != '' and $name != "OFFLINE" and $row['stopped'] == 'N' and isset($reseller_brandname) and $reseller_brandname != '' and $brandname == "Y" and strpos(strtolower($name), strtolower($reseller_brandname))  === false) {
		$tag_removed[] = array('userid' => $row['userid'], 'username' => getusername($row['userid']),'address' => $row['server']);
        $crashedArray['gsTag']++;
	}
}

$query = $sql->prepare("SELECT `id`,`userid` FROM `tickets` WHERE `state` NOT IN ('C','D') AND `resellerid`=?");
$query2 = $sql->prepare("SELECT `userID` FROM `tickets_text` WHERE `ticketID`=? ORDER BY `writeDate` DESC LIMIT 1");
$query->execute(array($resellerid));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $counttickets_open++;
    $crashedArray['ticketsOpen']++;
    $query2->execute(array($row['id']));

    if ($row['userid'] == $query2->fetchColumn()) {
        $counttickets_unanswered++;
        $crashedArray['tickets']++;
    }
}

if ($reseller_id != 0) {

    $counttickets_open = 0;
    $counttickets_unanswered = 0;

    $query = $sql->prepare("SELECT `id` FROM `tickets` WHERE `userid`=? AND `state` != 'C' AND `resellerid`=?");
    $query2 = $sql->prepare("SELECT `userID` FROM `tickets_text` WHERE `ticketID`=? ORDER BY `writeDate` DESC LIMIT 1");

    $query->execute(array($admin_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $crashedArray['ticketsReseller']++;
        $query2->execute(array($row['id']));

        if ($admin_id == $query2->fetchColumn()) {
            $crashedArray['ticketsResellerOpen']++;
        }
    }
}

$crached_ts3_virtual = 0;
$crashed_ts3 = array();

$query = $sql->prepare("SELECT CONCAT(`ip`,':',`port`) AS `address` FROM `voice_server` WHERE `active`='Y' AND `uptime`='0' AND `resellerid`=?");
$query->execute(array($reseller_id));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $crashed_ts3[] = array('address' => $row['address']);
    $crached_ts3_virtual++;
}
$crashedArray['ts3'] = $crached_ts3_virtual;

$query = $sql->prepare("SELECT `id` FROM `voice_masterserver` WHERE `active`='Y' AND `notified`>=? AND `resellerid`=?");
$query->execute(array($downChecks,$resellerid));
$crached_ts3_master = $query->rowCount();
$crashedArray['ts3Master'] = $crached_ts3_master;

$query = $sql->prepare("SELECT `id` FROM `rserverdata` WHERE `active`='Y' AND `notified`>=? AND `resellerid`=?");
$query->execute(array($downChecks,$resellerid));
$crached_master = $query->rowCount();
$crashedArray['masterserver'] = $crached_master;

if ($reseller_id == 0) {
    $query = $sql->prepare("SELECT `id` FROM `virtualhosts` WHERE `active`='Y' AND `notified`>=?");
    $query->execute(array($downChecks));
} else {
    $query = $sql->prepare("SELECT `id` FROM `virtualhosts` WHERE `active`='Y' AND `notified`>=? AND `resellerid`=?");
    $query->execute(array($downChecks,$resellerid));
}
$crached_hosts = $query->rowCount();
$crashedArray['virtualHosts'] = $crached_hosts;
$feedArray = array();

if ($ui->smallletters('w',2, 'get') == 'da' or (!$ui->smallletters('w',2, 'get') and !$ui->smallletters('d',2, 'get'))) {


    if ($reseller_id == 0 and $admin_id == $reseller_id) {
        $query = $sql->prepare("SELECT * FROM `feeds_settings` WHERE `resellerID`=0 AND `active`='Y' LIMIT 1");
        $query->execute();
    } else {
        $query = $sql->prepare("SELECT * FROM `feeds_settings` WHERE `resellerID`=? AND `active`='Y' LIMIT 1");
        $query->execute(array($reseller_id));
    }

    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        if ($row['orderBy'] == 'I' and $row['merge'] == 'N'){
            $orderFeedsBy='`feedID` ASC';
        } else if ($row['orderBy'] == 'U' and $row['merge'] == 'N'){
            $orderFeedsBy='`feedUrl` ASC';
        } else {
            $orderFeedsBy='n.`pubDate` DESC';
        }

        $newsAmount = $row['newsAmount'];

        #https://github.com/easy-wi/developer/issues/80 Include CMS news in dashboards
        $query2 = $sql->prepare("SELECT p.`id`,t.`id` AS `textID`,t.`title`,t.`text` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`released`='1' AND p.`type`='news' AND t.`language`=? AND p.`resellerid`=0 ORDER BY `date` DESC LIMIT 0,$newsAmount");
        $query2->execute(array($user_language));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            if ($row['merge'] == 'N') {
                $feedArray[$page_url][] = array('title' => $row2['title'], 'link' => (isset($seo) and $seo== 'Y') ? $page_url. '/' . $user_language . '/' . szrp($gsprache->news) . '/' . szrp($row2['title']) . '/' : $page_url.'/index.php?site=news&amp;id=' . $row2['id'], 'text' => nl2br($row2['text']), 'url' => $page_url);
            } else {
                $feedArray['News'][] = array('title' => $row2['title'], 'link' => (isset($seo) and $seo== 'Y') ? $page_url. '/' . $user_language . '/' . szrp($gsprache->news) . '/' . szrp($row2['title']) . '/' : $page_url.'/index.php?site=news&amp;id=' . $row2['id'], 'text' => nl2br($row2['text']), 'url' => $page_url);
            }
        }

        if ($row['merge'] == 'N') {

            $query2 = $sql->prepare("SELECT `feedID`,`feedUrl`,`feedID`,`twitter`,`loginName` FROM `feeds_url` WHERE `resellerID`=? AND `active`='Y' ORDER BY $orderFeedsBy");
            $query2->execute(array($row['resellerID']));
            $object = $query2->fetchAll(PDO::FETCH_ASSOC);

            if ($row['steamFeeds'] == 'Y') {
                $object[] = array('feedID' => 0,'feedUrl' => 'http://store.steampowered.com/news/','twitter' => 'N','loginName' => '');
            }

            foreach ($object as $row2) {
                $query3 = $sql->prepare("SELECT `title`,`link`,`description`,`content` FROM `feeds_news` WHERE `feedID`=? AND `resellerID`=? AND `active`='Y' ORDER BY `pubDate` DESC LIMIT $newsAmount");
                $query3->execute(array($row2['feedID'], $row['resellerID']));
                foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {

                    if ($row['displayContent'] == 'Y' and $row['limitDisplay'] == 'Y' and $row2['twitter'] == 'N'){
                        $text = substr($row3['content'],0, $row['maxChars']);
                    } else if ($row['displayContent'] == 'Y' and $row['limitDisplay'] == 'N' and $row2['twitter'] == 'N'){
                        $text = $row3['content'];
                    } else if ($row['displayContent'] == 'N' and $row['limitDisplay'] == 'Y' and $row2['twitter'] == 'N'){
                        $text = substr($row3['description'],0, $row['maxChars']);
                    } else {
                        $text = $row3['description'];
                    }

                    $url = ($row2['twitter'] == 'N') ? $row2['feedUrl'] : 'https://twitter.com/' . $row2['loginName'];
                    $feedArray[$url][] = array('title' => $row3['title'], 'link' => $row3['link'], 'text' => $text, 'url' => $url);
                }
            }
            unset($object);

        } else {

            if ($row['steamFeeds'] == 'Y') {
                $query2 = $sql->prepare("SELECT u.`feedUrl`,u.`feedID`,u.`twitter`,u.`loginName`,n.`title`,n.`link`,n.`description`,n.`content` FROM `feeds_news` n LEFT JOIN `feeds_url` u ON n.`feedID`=u.`feedID` WHERE n.`resellerID`=? AND n.`active`='Y' AND (u.`active`='Y' OR u.`active` IS NULL) ORDER BY $orderFeedsBy LIMIT $newsAmount");
            } else {
                $query2 = $sql->prepare("SELECT u.`feedUrl`,u.`feedID`,u.`twitter`,u.`loginName`,n.`title`,n.`link`,n.`description`,n.`content` FROM `feeds_news` n LEFT JOIN `feeds_url` u ON n.`feedID`=u.`feedID` WHERE n.`resellerID`=? AND n.`active`='Y' AND u.`active`='Y' ORDER BY $orderFeedsBy LIMIT $newsAmount");
            }
            $query2->execute(array($row['resellerID']));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

                if ($row['displayContent'] == 'Y' and $row['limitDisplay'] == 'Y' and $row2['twitter'] == 'N'){
                    $text = substr(preg_replace('/<(.*?)>/','',preg_replace('/<*?[^<>]*?>(.*?)<\/*?>/','$1', $row2['content'],-1),-1),0, $row['maxChars']);
                } else if ($row['displayContent'] == 'Y' and $row['limitDisplay'] == 'N' and $row2['twitter'] == 'N'){
                    $text = $row2['content'];
                } else if ($row['displayContent'] == 'N' and $row['limitDisplay'] == 'Y' and $row2['twitter'] == 'N'){
                    $text = substr(preg_replace('/<(.*?)>/','',preg_replace('/<*?[^<>]*?>(.*?)<\/*?>/','$1', $row2['description'],-1),-1),0, $row['maxChars']);
                } else {
                    $text = $row2['description'];
                }

                $url = ($row2['twitter'] == 'N') ? $row2['feedUrl'] : 'https://twitter.com/'.$row2['loginName'];
                $title = $row2['title'];

                if (strlen($row2['title']) <= 1) {
                    $title = $row2['link'];
                }

                $feedArray['News'][] =  array('title' => $title,'link' => $row2['link'], 'text' => $text,'url' => $url);
            }
        }
    }
}
