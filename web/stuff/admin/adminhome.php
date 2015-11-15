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
	die('No Access');
}

include(EASYWIDIR . '/third_party/Decoda/autoloader.php');
use Decoda\Decoda;

$sprache_bad = getlanguagefile('home', $user_language, $reseller_id);

$statsArray = array(
    'gameMasterInstalled' => 0,
    'gameMasterActive' => 0,
    'gameMasterSlotsAvailable' => 0,
    'gameMasterCrashed' => 0,
    'gameserverInstalled' => 0,
    'gameserverActive' => 0,
    'gameserverSlotsInstalled' => 0,
    'gameserverSlotsActive' => 0,
    'gameserverSlotsUsed' => 0,
    'gameserverNoPassword' => 0,
    'gameserverNoTag' => 0,
    'gameserverNotRunning' => 0,
    'mysqlMasterInstalled' => 0,
    'mysqlMasterActive' => 0,
    'mysqlMasterDBAvailable' => 0,
    'mysqlMasterCrashed' => 0,
    'mysqlDBInstalled' => 0,
    'mysqlDBActive' => 0,
    'mysqlDBSpaceUsed' => 0,
    'ticketsCompleted' => 0,
    'ticketsInProcess' => 0,
    'ticketsNew' => 0,
    'userAmount' => 0,
    'userAmountActive' => 0,
    'virtualMasterInstalled' => 0,
    'virtualMasterActive' => 0,
    'virtualMasterVserverAvailable' => 0,
    'virtualInstalled' => 0,
    'virtualActive' => 0,
    'voiceMasterInstalled' => 0,
    'voiceMasterActive' => 0,
    'voiceMasterSlotsAvailable' => 0,
    'voiceMasterCrashed' => 0,
    'voiceserverInstalled' => 0,
    'voiceserverActive' => 0,
    'voiceserverSlotsInstalled' => 0,
    'voiceserverSlotsActive' => 0,
    'voiceserverSlotsUsed' => 0,
    'voiceserverTrafficAllowed' => 0,
    'voiceserverTrafficUsed' => 0,
    'voiceserverCrashed' => 0,
    'webMasterInstalled' => 0,
    'webMasterActive' => 0,
    'webMasterCrashed' => 0,
    'webMasterSpaceAvailable' => 0,
    'webMasterVhostAvailable' => 0,
    'webspaceInstalled' => 0,
    'webspaceActive' => 0,
    'webspaceSpaceGiven' => 0,
    'webspaceSpaceGivenActive' => 0,
    'webspaceSpaceUsed' => 0
);

$query = $sql->prepare("SELECT * FROM `easywi_statistics_current` WHERE `userID`=? LIMIT 1");
$query->execute(array($resellerLockupID));
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $statsArray = $row;
}

$statsArray['ticketsTotal'] = $statsArray['ticketsInProcess'] + $statsArray['ticketsNew'];
$statsArray['warningTotal'] = $statsArray['gameserverNoPassword'] + $statsArray['gameserverNoTag'] + $statsArray['gameserverNotRunning'] + $statsArray['voiceserverCrashed'];

if ($ui->smallletters('w', 2, 'get') == 'da' or (!$ui->smallletters('w', 2, 'get') and !$ui->smallletters('d', 2, 'get'))) {

    $statsArray['ticketsPercent'] = (($statsArray['ticketsCompleted'] + $statsArray['ticketsInProcess'] + $statsArray['ticketsNew']) > 0) ? round( ($statsArray['ticketsInProcess'] + $statsArray['ticketsNew']) / ( ($statsArray['ticketsCompleted'] + $statsArray['ticketsInProcess'] + $statsArray['ticketsNew']) / 100), 2) : 0;
    $statsArray['ticketsNewPercent'] = (($statsArray['ticketsInProcess'] + $statsArray['ticketsNew']) > 0) ? round($statsArray['ticketsNew'] / ( ($statsArray['ticketsInProcess'] + $statsArray['ticketsNew']) / 100), 2) : 0;

    $statsArray['gameMasterActivePercent'] = ($statsArray['gameMasterInstalled'] > 0) ? round($statsArray['gameMasterActive'] / ($statsArray['gameMasterInstalled'] / 100), 2) : 0;
    $statsArray['gameMasterCrashedPercent'] = ($statsArray['gameMasterActive'] > 0) ? round($statsArray['gameMasterCrashed'] / ($statsArray['gameMasterActive'] / 100), 2) : 0;
    $statsArray['gameMasterServerPercent'] = ($statsArray['gameMasterServerAvailable'] > 0) ? round($statsArray['gameserverActive'] / ($statsArray['gameMasterServerAvailable'] / 100), 2) : 0;
    $statsArray['gameMasterSlotsPercent'] = ($statsArray['gameMasterSlotsAvailable'] > 0) ? round($statsArray['gameserverSlotsInstalled'] / ($statsArray['gameMasterSlotsAvailable'] / 100), 2) : 0;

    $statsArray['gameserverActivePercent'] = ($statsArray['gameserverInstalled'] > 0) ? round($statsArray['gameserverActive'] / ($statsArray['gameserverInstalled'] / 100), 2) : 0;
    $statsArray['gameserverSlotsUsedPercent'] = ($statsArray['gameserverSlotsActive'] > 0) ? round($statsArray['gameserverSlotsUsed'] / ($statsArray['gameserverSlotsActive'] / 100), 2) : 0;
    $statsArray['gameserverCrashedPercent'] = ($statsArray['gameserverSlotsActive'] > 0) ? round($statsArray['gameserverNotRunning'] / ($statsArray['gameserverSlotsActive'] / 100), 2) : 0;
    $statsArray['gameserverTagPercent'] = ($statsArray['gameserverActive'] > 0) ? round($statsArray['gameserverNoTag'] / ($statsArray['gameserverActive'] / 100), 2) : 0;
    $statsArray['gameserverPasswordPercent'] = ($statsArray['gameserverActive'] > 0) ? round($statsArray['gameserverNoPassword'] / ($statsArray['gameserverActive'] / 100), 2) : 0;
    $statsArray['gameserverRuleBreakPercent'] = ($statsArray['gameserverActive'] > 0) ? round(($statsArray['gameserverNoTag'] + $statsArray['gameserverNoPassword']) / ($statsArray['gameserverActive'] / 100), 2) : 0;

    $statsArray['voiceMasterActivePercent'] = ($statsArray['voiceMasterInstalled'] > 0) ? round($statsArray['voiceMasterActive'] / ($statsArray['voiceMasterInstalled'] / 100), 2) : 0;
    $statsArray['voiceMasterCrashedPercent'] = ($statsArray['voiceMasterActive'] > 0) ? round($statsArray['voiceMasterCrashed'] / ($statsArray['voiceMasterActive'] / 100), 2) : 0;
    $statsArray['voiceMasterServerPercent'] = ($statsArray['voiceMasterServerAvailable'] > 0) ? round($statsArray['voiceserverInstalled'] / ($statsArray['voiceMasterServerAvailable'] / 100), 2) : 0;
    $statsArray['voiceMasterSlotsPercent'] = ($statsArray['voiceMasterSlotsAvailable'] > 0) ? round($statsArray['voiceserverSlotsInstalled'] / ($statsArray['voiceMasterSlotsAvailable'] / 100), 2) : 0;

    $statsArray['voiceserverActivePercent'] = ($statsArray['voiceserverInstalled'] > 0) ? round($statsArray['voiceserverActive'] / ($statsArray['voiceserverInstalled'] / 100), 2) : 0;
    $statsArray['voiceserverSlotsUsedPercent'] = ($statsArray['voiceserverSlotsActive'] > 0) ? round($statsArray['voiceserverSlotsUsed'] / ($statsArray['voiceserverSlotsActive'] / 100), 2) : 0;
    $statsArray['voiceserverCrashedPercent'] = ($statsArray['voiceserverSlotsActive'] > 0) ? round($statsArray['voiceserverCrashed'] / ($statsArray['voiceserverActive'] / 100), 2) : 0;
    $statsArray['voiceserverTrafficPercent'] = ($statsArray['voiceserverTrafficAllowed'] > 0) ? round($statsArray['voiceserverTrafficUsed'] / ($statsArray['voiceserverTrafficAllowed'] / 100), 2) : 0;

    $statsArray['webMasterActivePercent'] = ($statsArray['webMasterInstalled'] > 0) ? round($statsArray['webMasterActive'] / ($statsArray['webMasterInstalled'] / 100), 2) : 0;
    $statsArray['webMasterCrashedPercent'] = ($statsArray['webMasterCrashed'] > 0) ? round($statsArray['webMasterCrashed'] / ($statsArray['webMasterActive'] / 100), 2) : 0;
    $statsArray['webMasterVhostPercent'] = ($statsArray['webMasterVhostAvailable'] > 0) ? round($statsArray['webspaceInstalled'] / ($statsArray['webMasterVhostAvailable'] / 100), 2) : 0;
    $statsArray['webMasterSpaceUsedPercent'] = ($statsArray['webMasterSpaceAvailable'] > 0) ? round($statsArray['webspaceSpaceGiven'] / ($statsArray['webMasterSpaceAvailable'] / 100), 2) : 0;

    $statsArray['webspaceActivePercent'] = ($statsArray['webspaceInstalled'] > 0) ? round($statsArray['webspaceActive'] / ($statsArray['webspaceInstalled'] / 100), 2) : 0;
    $statsArray['webspaceSpaceUsedPercent'] = ($statsArray['webspaceSpaceGiven'] > 0) ? round($statsArray['webspaceSpaceUsed'] / ($statsArray['webspaceSpaceGiven'] / 100), 2) : 0;

    $statsArray['mysqlMasterActivePercent'] = ($statsArray['mysqlMasterInstalled'] > 0) ? round($statsArray['mysqlMasterActive'] / ($statsArray['mysqlMasterInstalled'] / 100), 2) : 0;
    $statsArray['mysqlMasterCrashedPercent'] = ($statsArray['mysqlMasterCrashed'] > 0) ? round($statsArray['mysqlMasterCrashed'] / ($statsArray['mysqlMasterActive'] / 100), 2) : 0;
    $statsArray['mysqlMasterDBPercent'] = ($statsArray['mysqlMasterDBAvailable'] > 0) ? round($statsArray['mysqlDBInstalled'] / ($statsArray['mysqlMasterDBAvailable'] / 100), 2) : 0;

    $statsArray['mysqlActivePercent'] = ($statsArray['mysqlDBInstalled'] > 0) ? round($statsArray['mysqlDBActive'] / ($statsArray['mysqlDBInstalled'] / 100), 2) : 0;

    $feedArray = array();

    if ($reseller_id == 0 and $admin_id == $reseller_id) {
        $query = $sql->prepare("SELECT * FROM `feeds_settings` WHERE `resellerID`=0 AND `active`='Y' LIMIT 1");
        $query->execute();
    } else {
        $query = $sql->prepare("SELECT * FROM `feeds_settings` WHERE `resellerID`=? AND `active`='Y' LIMIT 1");
        $query->execute(array($reseller_id));
    }

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        if ($row['orderBy'] == 'I' and $row['merge'] == 'N'){
            $orderFeedsBy='`feedID` ASC';
        } else if ($row['orderBy'] == 'U' and $row['merge'] == 'N'){
            $orderFeedsBy='`feedUrl` ASC';
        } else {
            $orderFeedsBy='n.`pubDate` DESC';
        }

        $newsAmount = $row['newsAmount'];

        if ($row['merge'] == 'N') {

            $query2 = $sql->prepare("SELECT p.`id`,p.`date`,t.`id` AS `textID`,t.`title`,t.`text` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`released`='1' AND p.`type`='news' AND t.`language`=? AND p.`resellerid`=0 ORDER BY `date` DESC LIMIT 0,$newsAmount");
            $query2->execute(array($user_language));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                $strtotime = strtotime($row2['date']);
                $feedArray[$page_url][] = array('title' => $row2['title'], 'link' => (isset($seo) and $seo == 'Y') ? $page_url. '/' . $user_language . '/' . szrp($gsprache->news) . '/' . szrp($row2['title']) . '/' : $page_url.'/index.php?site=news&amp;id=' . $row2['id'], 'text' => nl2br($row2['text']), 'url' => $page_url, 'date' => date('Y-m-d', $strtotime), 'time' => date('H:i', $strtotime));
            }

            $query2 = $sql->prepare("SELECT `feedID`,`feedUrl`,`feedID`,`twitter`,`loginName` FROM `feeds_url` WHERE `resellerID`=? AND `active`='Y' ORDER BY $orderFeedsBy");
            $query2->execute(array($row['resellerID']));
            $object = $query2->fetchAll(PDO::FETCH_ASSOC);

            if ($row['steamFeeds'] == 'Y') {
                $object[] = array('feedID' => 0,'feedUrl' => 'http://store.steampowered.com/news/','twitter' => 'N','loginName' => '');
            }

            foreach ($object as $row2) {
                $query3 = $sql->prepare("SELECT `title`,`link`,`description`,`content`,`pubDate` FROM `feeds_news` WHERE `feedID`=? AND `resellerID`=? AND `active`='Y' ORDER BY `pubDate` DESC LIMIT $newsAmount");
                $query3->execute(array($row2['feedID'], $row['resellerID']));
                while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {

                    if ($row['displayContent'] == 'Y' and $row['limitDisplay'] == 'Y' and $row2['twitter'] == 'N'){
                        $text = substr($row3['content'], 0, $row['maxChars']);
                    } else if ($row['displayContent'] == 'Y' and $row['limitDisplay'] == 'N' and $row2['twitter'] == 'N'){
                        $text = $row3['content'];
                    } else if ($row['displayContent'] == 'N' and $row['limitDisplay'] == 'Y' and $row2['twitter'] == 'N'){
                        $text = substr($row3['description'], 0, $row['maxChars']);
                    } else {
                        $text = $row3['description'];
                    }

                    $url = ($row2['twitter'] == 'N') ? $row2['feedUrl'] : 'https://twitter.com/' . $row2['loginName'];
                    $strtotime = strtotime($row3['pubDate']);
                    $feedArray[$url][] = array('title' => $row3['title'], 'link' => $row3['link'], 'text' => $text, 'url' => $url, 'date' => date('Y-m-d', $strtotime), 'time' => date('H:i', $strtotime));
                }
            }
            unset($object);

        } else {

            $steamAppIDs = '';

            if ($row['steamFeeds'] == 'Y') {

                $steamAppIDsArray = array();

                $query2 = $sql->prepare("SELECT t.`appID`,t.`shorten` FROM `servertypes` AS t WHERE t.`resellerid`=? AND t.`steamgame`='S' AND EXISTS (SELECT 1 FROM `rservermasterg` WHERE `servertypeid`=t.`id`)");
                $query2->execute(array($reseller_id));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $steamAppIDsArray[] = workAroundForValveChaos($row2['appID'], $row2['shorten']);
                }

                $steamAppIDs = (count($steamAppIDsArray) > 0) ? ' OR (n.`feedID`=0  AND n.`content` IN (' . implode(',', $steamAppIDsArray) . '))' : '';
            }

            $query2 = $sql->prepare("(SELECT 'cms' AS `newsType`,p.`id` AS `newsID`,p.`date` AS `newsDate`,t.`id` AS `textID`,t.`title` AS `newsTitle`,t.`text` AS `newsText`,'' AS `twitterText`,'' AS `feedUrl`,'N' AS `twitter`,'' AS `loginName`,'' AS `link` FROM `page_pages` p LEFT JOIN `page_pages_text` t ON p.`id`=t.`pageid` WHERE p.`released`='1' AND p.`type`='news' AND t.`language`=:lang AND p.`resellerid`=0) UNION (SELECT 'feed' AS `newsType`,u.`feedID` AS `newsID`,n.`pubDate` AS `newsDate`,0 AS `textID`,n.`title` AS `newsTitle`,n.`content` AS `newsText`,n.`description` AS `twitterText`,u.`feedUrl`,u.`twitter`,u.`loginName`,n.`link` FROM `feeds_news` n LEFT JOIN `feeds_url` u ON n.`feedID`=u.`feedID` WHERE n.`resellerID`=:resellerID AND n.`active`='Y' AND (u.`active`='Y'  $steamAppIDs)) ORDER BY `newsDate` DESC LIMIT 0,$newsAmount");
            $query2->execute(array(':lang' => $user_language,':resellerID' => $row['resellerID']));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                if ($row2['newsType'] == 'cms') {

                    $url = $page_url;
                    $link = (isset($seo) and $seo == 'Y') ? $page_url . '/' . $user_language . '/' . szrp($gsprache->news) . '/' . szrp($row2['newsTitle']) . '/' : $page_url . '/index.php?site=news&amp;id=' . $row2['newsID'];
                    $text = nl2br($row2['newsText']);

                } else {

                    $url = ($row2['twitter'] == 'N') ? $row2['feedUrl'] : 'https://twitter.com/' . $row2['loginName'];
                    $link = $row2['link'];

                    if ($row['displayContent'] == 'Y' and $row['limitDisplay'] == 'Y' and $row2['twitter'] == 'N'){
                        $text = substr(preg_replace('/<(.*?)>/', '', preg_replace('/<*?[^<>]*?>(.*?)<\/*?>/', '$1', $row2['newsText'], -1), -1), 0, $row['maxChars']);
                    } else if ($row['displayContent'] == 'Y' and $row['limitDisplay'] == 'N' and $row2['twitter'] == 'N'){
                        $text = $row2['newsText'];
                    } else if ($row['displayContent'] == 'N' and $row['limitDisplay'] == 'Y' and $row2['twitter'] == 'N'){
                        $text = substr(preg_replace('/<(.*?)>/', '', preg_replace('/<*?[^<>]*?>(.*?)<\/*?>/', '$1', $row2['twitterText'], -1), -1), 0, $row['maxChars']);
                    } else {
                        $text = $row2['twitterText'];
                    }
                }

                if (preg_match('/(\[\/img\]|\[\/url\]|\[\/b\]|\[\/h1\])/i', $text)) {
                    $code = new \Decoda\Decoda($text);
                    $code->addFilter(new \Decoda\Filter\DefaultFilter());
                    $code->addFilter(new \Decoda\Filter\ImageFilter());
                    $code->addFilter(new \Decoda\Filter\BlockFilter());
                    $code->addFilter(new \Decoda\Filter\EmailFilter());
                    $code->addFilter(new \Decoda\Filter\UrlFilter());
                    $code->addFilter(new \Decoda\Filter\VideoFilter());
                    $code->addFilter(new \Decoda\Filter\HeadLineFilter());
                    $code->addHook(new \Decoda\Hook\ClickableHook());
                    $text = $code->parse();
                }

                $title = $row2['newsTitle'];

                if (strlen($row2['newsTitle']) <= 1) {
                    $title = $link;
                }

                $strtotime = strtotime($row2['newsDate']);

                $feedArray['News'][] = array('title' => $title, 'link' => $link, 'text' => preg_replace('/<img[^>]+\>/i', '', $text), 'url' => $url, 'date' => date('Y-m-d', $strtotime), 'time' => date('H:i', $strtotime));
            }
        }
    }
}
