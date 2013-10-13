<?php
/**
 * File: feeds_function.php.
 * Author: Ulrich Block
 * Date: 17.06.12
 * Time: 14:35
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
if (isset($newsInclude) and $newsInclude == true) {
    $update = $sql->prepare("UPDATE `feeds_settings` SET `lastUpdate`=NOW() WHERE `resellerID`=? LIMIT 1");
    $update2 = $sql->prepare("UPDATE `feeds_url` SET `modified`=NOW() WHERE `feedID`=? AND `resellerID`=? LIMIT 1");
    $insert = $sql->prepare("INSERT INTO `feeds_news` (`feedID`,`title`,`link`,`pubDate`,`description`,`content`,`author`,`resellerID`) VALUES (?,?,?,?,?,?,?,?)");
    $count = $sql->prepare("SELECT COUNT(`newsID`) AS `amount` FROM `feeds_news` WHERE `pubDate`=? AND `resellerID`=? AND `feedID`=? LIMIT 1");
    $total = $sql->prepare("SELECT COUNT(`newsID`) AS `amount` FROM `feeds_news` WHERE `resellerID`=?");
    $delete = $sql->prepare("DELETE FROM `feeds_news` WHERE `resellerID`=? AND `pubDate`<=?");
    @ini_set('user_agent','easy-wi.com');
    if(isset($lookUpID)) {
        $query = $sql->prepare("SELECT * FROM `feeds_settings` WHERE `resellerID`=? AND `active`='Y' LIMIT 1");
        $query->execute(array($lookUpID));
    } else {
        $steamNews = array();
        $query = $sql->prepare("SELECT `newsAmount` FROM `feeds_settings` WHERE `active`='Y' AND `steamFeeds`='Y' ORDER BY `newsAmount` DESC LIMIT 1");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $newsAmount = $row['newsAmount'];
            $query2 = $sql->prepare("SELECT t.* FROM `servertypes` t LEFT JOIN `rservermasterg` r ON t.`id`=r.`servertypeid` WHERE r.`id` IS NOT NULL AND t.`appID` IS NOT NULL AND t.`steamgame`!='N' GROUP BY t.`appID` ORDER BY t.`appID`");
            $query2->execute();
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if (!in_array($row2['appID'], array(null,'', false))) {
                    $lookUpAppID=workAroundForValveChaos($row2['appID'], $row2['shorten']);
                    $json=webhostRequest('api.steampowered.com','easy-wi.com','/ISteamNews/GetNewsForApp/v0002/?appid='.$lookUpAppID.'&format=json&count='.$newsAmount);
                    $json=cleanFsockOpenRequest($json,'{','}');
                    $json=@json_decode($json);
                    if ($json and isset($json->appnews->newsitems) and $json->appnews->appid==$lookUpAppID) {
                        if (isset($printToConsole)) print "Getting Feed Updates for Steamgame with AppID ${lookUpAppID}\r\n";
                        $theCount = 0;
                        foreach ($json->appnews->newsitems as $item) {
                            if ($item->is_external_url == false and $theCount<$newsAmount) {
                                $steamNews[$lookUpAppID][] = array('title' => $item->title,'description' => $item->contents,'link' => $item->url,'pubDate' => date('Y-m-d H:i:s',$item->date),'content' => $lookUpAppID,'author' => $item->author,'creator' => $item->author);
                                $theCount++;
                            }
                        }
                    } else {
                        if (isset($printToConsole)) print "Failed getting Feed Updates for Steamgame with AppID ${lookUpAppID}\r\n";
                    }
                }
            }
        }
        $query = $sql->prepare("SELECT * FROM `feeds_settings` WHERE `active`='Y' ORDER BY `resellerID`");
        $query->execute();
    }
    $checkedFeeds = 0;
    $skippedFeeds = 0;
    $skipEntries = 0;
    $newEntries = 0;
    $removed = 0;
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $feedsArray = array();
        $lookUpID = $row['resellerID'];
        $feedsActive = $row['active'];
        $newsAmount = $row['newsAmount'];
        $diff=round((time()-strtotime($row['lastUpdate']))/60);
        if (isset($steamNews) and $row['steamFeeds'] == 'Y') {
            foreach ($steamNews as $news) {
                $i = 0;
                while ($i<count($news) and $i<=$newsAmount) {
                    $feedsArray[0][] = $news[$i];
                    $i++;
                }
            }
        } else if (isset($lookUpID) and $row['steamFeeds'] == 'Y') {
            $query2 = $sql->prepare("SELECT t.* FROM `servertypes` t LEFT JOIN `rservermasterg` r ON t.`id`=r.`servertypeid` WHERE r.`id` IS NOT NULL AND t.`appID` IS NOT NULL AND t.`resellerID`=? AND t.`steamgame`!='N' GROUP BY t.`appID` ORDER BY t.`appID`");
            $query2->execute(array($lookUpID));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if (!in_array($row2['appID'], array(null,'', false))) {
                    $lookUpAppID=workAroundForValveChaos($row2['appID'], $row2['shorten']);
                    $json=webhostRequest('api.steampowered.com','easy-wi.com','/ISteamNews/GetNewsForApp/v0002/?appid='.$lookUpAppID.'&format=json&count='.$newsAmount);
                    $json=cleanFsockOpenRequest($json,'{','}');
                    $json=@json_decode($json);
                    if ($json and isset($json->appnews->newsitems) and $json->appnews->appid==$lookUpAppID) {
                        $theCount = 0;
                        foreach ($json->appnews->newsitems as $item) {
                            if ($item->is_external_url == false and $theCount<$newsAmount) {
                                $feedsArray[0][] = array('title' => $item->title,'description' => $item->contents,'link' => $item->url,'pubDate' => date('Y-m-d H:i:s',$item->date),'content' => $lookUpAppID,'author' => $item->author,'creator' => $item->author);
                                $theCount++;
                            }
                        }
                    }
                }
            }
        }
        $query2 = $sql->prepare("SELECT * FROM `feeds_url` WHERE `resellerID`=?");
        $query2->execute(array($lookUpID));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            unset($gZipped);
            if ($feedsActive == 'Y' and $row2['active'] == 'Y' and ($diff>$row['updateMinutes'] or !isset($jobUpdating))) {
                $modified=date('D, d M Y H:i:s T',strtotime($row2['modified']));
                $twitter = $row2['twitter'];
                $feedID = $row2['feedID'];
                if ($twitter== 'Y') {
                    if (isset($printToConsole)) print "Getting Updates for Twitter Feed ${row2['loginName']}\r\n";
                    $json=webhostRequest('api.twitter.com','easy-wi.com','/1/statuses/user_timeline.json?include_rts=false&exclude_replies=true&screen_name='.$row2['loginName'].'&count='.$newsAmount,443);
                    $json=cleanFsockOpenRequest($json,'[',']');
                    foreach (json_decode($json) as $tweet) {
                        if (isset($tweet->text)) {
                            $feedTitle=substr($tweet->text,0,50).'...';
                            $description = $tweet->text;
                            $link='https://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str;
                            $pubDate=date('Y-m-d H:i:s',strtotime($tweet->created_at));
                            $content = '';
                            $author = $tweet->user->name;
                            $creator = $tweet->user->name;
                            $feedsArray[$feedID][] = array('title' => $feedTitle,'description' => $description,'link' => $link,'pubDate' => $pubDate,'content' => $content,'author' => $author,'creator' => $creator);
                        }
                    }
                } else {
                    if (isset($printToConsole)) print "Getting Feed Updates for Feed ${row2['feedUrl']}\r\n";
                    $port=80;
                    if (strpos($row2['feedUrl'], 'https://')) $port=443;
                    $domain=str_replace(array('https://','http://'),'', $row2['feedUrl']);
                    $ex=explode('/',$domain);
                    $domain = $ex[0];
                    $params='/';
                    $i = 1;
                    while ($i<count($ex)) {
                        $params .= '/' . $ex[$i];
                        $i++;
                    }
                   # $xml=webhostRequest($domain,'easy-wi.com',$params,$port);
                   # $xml=cleanFsockOpenRequest($xml,'<','>');
                    $feed = false;
                    if (!empty($row2['feedUrl']) and extension_loaded('zlib')) {
                        $opts=array('http' => array('header' => "Accept-Encoding: gzip\r\n"));
                        $context=stream_context_create($opts);
                        $feed=fopen($row2['feedUrl'], 'r', false,$context);
                    } else if (!empty($row2['feedUrl'])) {
                        $feed=fopen($row2['feedUrl'], 'r');
                    }
                    if ($feed) {
                        $lastModified = true;
                        stream_set_timeout($feed,10);
                        $meta=stream_get_meta_data($feed);
                        foreach ($meta['wrapper_data'] as $mrow) {
                            if(is_string($mrow) and $mrow == 'Content-Encoding: gzip') {
                                $gZipped = true;
                            } else if (is_string($mrow) and substr($mrow,0,13) == 'Last-Modified' and !isset($lastModified)) {
                                $lastModified=substr($mrow,16);
                            } else if (is_string($mrow) and substr($mrow,-12) == 'Not Modified') {
                                $lastModified = false;
                            }
                        }
                        if (isset($lastModified) and $lastModified != false) {
                            $buffer = '';
                            while (!feof($feed)){
                                $buffer .= fgets($feed,4096);
                            }
                            fclose($feed);
                            if (isset($gZipped) and $gZipped == true){
                                $content=gzinflate(substr($buffer,10));
                            } else {
                                $content = $buffer;
                            }
                            $cdata=explode('<![CDATA[',$content);
                            $buffer = '';
                            $base64Buffer = '';
                            $cdataStarted = false;
                            foreach ($cdata as $block) {
                                if ($cdataStarted == false) {
                                    if (strpos($block,']]>') !== false) {
                                        $end=explode(']]>',$block);
                                        $base64Buffer .= $end[0];
                                        if (isset($end[1])) {
                                            if (strlen($base64Buffer)>1) {
                                                $buffer.=base64_encode(preg_replace('/<tr[^<>]*?>(.*?)<\/tr>/','$1',preg_replace('/<td[^<>]*?>(.*?)<\/td>/','$1',preg_replace('/<a[^<>]*?>(.*?)<\/a>/','$1',urldecode($base64Buffer),-1),-1),-1));
                                                $base64Buffer = '';
                                            }
                                            $buffer .= $end[1];
                                            $cdataStarted = false;
                                        }
                                    } else {
                                        $buffer .= $block;
                                        $cdataStarted = true;
                                    }
                                } else if ($cdataStarted == true) {
                                    $end=explode(']]>',$block);
                                    $base64Buffer .= $end[0];
                                    if (isset($end[1])) {
                                        if (strlen($base64Buffer)>1) {
                                            $buffer.=base64_encode(preg_replace('/<tr[^<>]*?>(.*?)<\/tr>/','$1',preg_replace('/<td[^<>]*?>(.*?)<\/td>/','$1',preg_replace('/<a[^<>]*?>(.*?)<\/a>/','$1',$base64Buffer,-1),-1),-1));
                                            $base64Buffer = '';
                                        }
                                        $buffer .= $end[1];
                                        $cdataStarted = false;
                                    }
                                }
                            }
                            $doc=new SimpleXmlElement($buffer);
                            $theCount = 0;
                            if (isset($doc->channel->item)) {
                                foreach ($doc->channel->item as $item) {
                                    $namespaces = $item->getNameSpaces(true);
                                    if (isset($namespaces['content'])) {
                                        $content=(string)$item->children($namespaces['content']);
                                        if ((bool)preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/',$content)) {
                                            $content=base64_decode($content);
                                        }
                                    } else {
                                        $content = '';
                                    }
                                    if (isset($namespaces['dc'])) {
                                        $dc = $item->children($namespaces['dc']);
                                        $author=(string)$dc->publisher;
                                        $creator=(string)$dc->creator;
                                    } else {
                                        $author = '';
                                        $creator = '';
                                    }
                                    $feedTitle=(string)$item->title;
                                    if ((bool)preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/',$feedTitle)) {
                                        $feedTitle=base64_decode($feedTitle);
                                    }
                                    $pubDate=date('Y-m-d H:i:s',strtotime((string)$item->pubDate));
                                    $link=(string)$item->link;
                                    if ((bool)preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/',$link)) {
                                        $link=base64_decode($link);
                                    }
                                    $description=(string)$item->description;
                                    if ((bool)preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/',$description)) {
                                        $description=base64_decode($description);
                                    }
                                    if($theCount<$newsAmount) {
                                        $feedsArray[$feedID][] = array('title' => $feedTitle,'description' => $description,'link' => $link,'pubDate' => $pubDate,'content' => $content,'author' => $author,'creator' => $creator);
                                    }
                                    $theCount++;
                                }

                            }
                        }
                    }
                }
                $update2->execute(array($feedID,$lookUpID));
                $checkedFeeds++;
            } else {
                $skippedFeeds++;
            }
        }
        foreach ($feedsArray as $feedID=>$feeds) {
            foreach ($feeds as $singleFeed) {
                $count->execute(array($singleFeed['pubDate'],$lookUpID,$feedID));
                $exists = $count->fetchColumn();
                if($exists>0) {
                    $skipEntries++;
                } else {
                    $newEntries++;
                    $insert->execute(array($feedID,$singleFeed['title'],$singleFeed['link'],$singleFeed['pubDate'],$singleFeed['description'],$singleFeed['content'],$singleFeed['author'],$lookUpID));
                }
            }
        }
        $update->execute(array($lookUpID));
        $total->execute(array($lookUpID));
        $totalNews = $total->fetchColumn();
        $maxKeep = $row['maxKeep'];
        if ($totalNews>$maxKeep) {
            $removed = $removed+$totalNews-$maxKeep;
            $getLastID = $sql->prepare("SELECT `pubDate` FROM `feeds_news` WHERE `resellerID`=? ORDER BY `pubDate` DESC LIMIT $maxKeep,1");
            $getLastID->execute(array($lookUpID));
            $lastPubDate = $getLastID->fetchColumn();
            $delete->execute(array($lookUpID,$lastPubDate));
        }
    }
    $template_file = 'Skipped Feeds:'.$skippedFeeds.' Checked Feeds: '.$checkedFeeds.' Skipped News:'.$skipEntries.' New Entries:'.$newEntries.' Removed Entries:'.$removed;
}