<?php
/**
 * File: admin_search.php.
 * Author: Ulrich Block
 * Date: 06.03.13
 * Time: 19:00
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
if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !isanyadmin($admin_id) and !rsellerpermisions($admin_id)) or (!isset($pa))) {
    header('Location: login.php');
    die('No acces');
}
$sprache = getlanguagefile('search',$user_language,$reseller_id);
$results = array();
$gs = false;
$vs = false;
$vo = false;
$ad = false;
$im = false;
$ro = false;
$us = false;
$q = array();
$i = 0;
function theName ($a,$b) {
    $name=($a == ' ' or $a == '' or $a==null) ? $b : $a.' ('.$b.')';
    return $name;
}
function notIN ($a,$c) {
    $notIN=(count($a)>0) ? 'AND '.$c.' NOT IN('.implode(',',$a).')' : '';
    return $notIN;
}
if (isset($ui->get['q'])) {
    if ($ui->st('type', 'get')) {
        foreach($ui->st('type', 'get') as $t) {
            if ($pa['addons'] and $t == 'ad') $ad = true;
            if ($pa['gserver'] and $t == 'gs') $gs = true;
            if ($pa['gimages'] and $t == 'im') $im = true;
            if ($pa['roots'] and $t == 'ro') $ro = true;
            if ($pa['voiceserver'] and $t == 'vo') $vo = true;
            if (($pa['addvserver'] or $pa['modvserver'] or $pa['delvserver'] or $pa['usevserver']) and $t == 'vs') $vs = true;
            if (($pa['user'] or $pa['user_users']) and $t == 'us') $us = true;
        }
    }
    $ips = array();
    $ports = array();
    $addresses = array();
    $words = array();
    $ids = array();
    $adIDs = array();
    $gsIDs = array();
    $imIDs = array();
    $roIDs = array();
    $usIDs = array();
    $voIDs = array();
    $vsIDs = array();
    foreach(preg_split("/\s/",$ui->get['q'],-1,PREG_SPLIT_NO_EMPTY) as $s) {
        switch($s) {
            case(isid($s,19)):
                $ids[] = $s;
                $q[] = $s;
                if (port($s)) $ports[] = $s;
                break;
            case(isip($s,'all')):
                $ips[] = $s;
                $q[] = $s;
                break;
            case(ipport($s)):
                $addresses[] = $s;
                list($ips[], $ports[]) = explode(':' , preg_replace('/\s+/', '', $s));
                $q[] = $s;
                break;
            default:
                $words[]=strtolower($s);
                $q[] = $s;
        }
    }

    $ips=array_unique($ips);
    $addresses=array_unique($addresses);
    $words=array_unique($words);
    $ids=array_unique($ids);
    foreach($ids as $id) {
        if ($ad == true) {
            $query = $sql->prepare("SELECT `menudescription` FROM `addons` WHERE `id`=? AND `resellerid`=? ".notIN($adIDs,'`id`'));
            $query->execute(array($id,$reseller_id));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $adIDs[] = $id;
                $results["ad-${id}"] = array('type' => $gsprache->addon,'id' => $id,'name' => $row['menudescription'], 'owner' => '','edit' => '?w=ad&amp;d=md&amp;id='.$id,'delete' => '?w=ad&amp;d=dl&amp;id='.$id);
            }
        }
        if ($im == true) {
            $query = $sql->prepare("SELECT `description` FROM `servertypes` WHERE `id`=? AND `resellerid`=? ".notIN($imIDs,'`id`'));
            $query->execute(array($id,$reseller_id));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $imIDs[] = $id;
                $results["im-${id}"] = array('type' => $gsprache->gameserver . ' ' . $gsprache->templates,'id' => $id,'name' => $row['description'], 'owner' => '','edit' => '?w=im&amp;d=md&amp;id='.$id,'delete' => '?w=im&amp;d=dl&amp;id='.$id);
            }
        }
        if ($us == true) {
            if ($reseller_id == 0) {
                $query = $sql->prepare("SELECT `cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `userdata` WHERE (`id`=? OR `externalID`=?) ".notIN($usIDs,'`id`'));
                $query->execute(array($id,$id));
            } else {
                $query = $sql->prepare("SELECT `cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `userdata` WHERE (`id`=? OR `externalID`=?) AND `resellerid`=? ".notIN($usIDs,'`id`'));
                if ($admin_id==$reseller_id) {
                    $query->execute(array($id,$id,$reseller_id));
                } else {
                    $query->execute(array($id,$id,$admin_id));
                }
            }
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $usIDs[] = $id;
                $results["us-${id}"] = array('type' => $gsprache->gameserver,'id' => $id,'name' => theName($row['username'], $row['cname']),'owner' => '','edit' => '?w=us&amp;d=md&amp;id='.$id,'delete' => '?w=us&amp;d=dl&amp;id='.$id);
            }
        }
        if ($gs == true) {
            $query = $sql->prepare("SELECT g.`serverip`,g.`port`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE (g.`id`=? OR g.`externalID`=?) AND g.`resellerid`=? ".notIN($gsIDs,'g.`id`'));
            $query->execute(array($id,$id,$reseller_id));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $gsIDs[] = $id;
                $results["gs-${id}"] = array('type' => $gsprache->gameserver,'id' => $id,'name' => $row['serverip'] . ':' . $row['port'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=gs&amp;d=md&amp;id='.$id,'delete' => '?w=gs&amp;d=dl&amp;id='.$id);
            }
        }
        if ($vo == true) {
            $query = $sql->prepare("SELECT v.`ip`,v.`port`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `voice_server` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE (v.`id`=? OR v.`externalID`=?) AND v.`resellerid`=? ".notIN($voIDs,'v.`id`'));
            $query->execute(array($id,$id,$reseller_id));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $voIDs[] = $id;
                $results["vo-${id}"] = array('type' => $gsprache->voiceserver,'id' => $id,'name' => $row['ip'] . ':' . $row['port'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=vo&amp;d=md&amp;id='.$id,'delete' => '?w=vo&amp;d=dl&amp;id='.$id);
            }
        }
        if ($ro == true) {
            $notIN=notIN($roIDs,'r.`id`');
            if ($reseller_id == 0) {
                $query = $sql->prepare("SELECT r.`ip`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `rserverdata` r LEFT JOIN `userdata` u ON r.`resellerid`=u.`id` WHERE (r.`id`=? OR r.`externalID`=?) AND r.`hostid`=0 $notIN");
                $query->execute(array($id,$id));
            } else {
                $query = $sql->prepare("SELECT r.`ip`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `rserverdata` r LEFT JOIN `userdata` u ON r.`resellerid`=u.`id` WHERE (r.`id`=? OR r.`externalID`=?) AND r.`resellerid`=? $notIN");
                $query->execute(array($id,$id,$reseller_id));
            }
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $roIDs[] = $id;
                $results["ro-${id}"] = array('type' => $gsprache->root,'id' => $id,'name' => $row['ip'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=ro&amp;d=md&amp;id='.$id,'delete' => '?w=ro&amp;d=dl&amp;id='.$id);
            }
        }
        if ($vs == true) {
            $notIN=notIN($vsIDs,'r.`id`');
            if ($reseller_id == 0) {
                $query = $sql->prepare("SELECT v.`ip`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `virtualcontainer` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE (v.`id`=? OR v.`externalID`=?) $notIN");
                $query->execute(array($id,$id));
            } else if ($reseller_id==$admin_id) {
                $query = $sql->prepare("SELECT v.`ip`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `virtualcontainer` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE (v.`id`=? OR v.`externalID`=?) AND v.`userid`=? $notIN");
                $query->execute(array($id,$id,$reseller_id));
            } else {
                $query = $sql->prepare("SELECT v.`ip`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `virtualcontainer` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE (v.`id`=? OR v.`externalID`=?) AND v.`userid`=? AND v.`resellerid`=? $notIN");
                $query->execute(array($id,$id,$admin_id,$reseller_id));
            }
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $vsIDs[] = $id;
                $results["vs-${id}"] = array('type' => $gsprache->virtual,'id' => $id,'name' => $row['id'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=ro&amp;d=md&amp;id='.$id,'delete' => '?w=ro&amp;d=dl&amp;id='.$id);
            }
        }
    }
    foreach($ips as $ip) {
        if ($gs == true) {
            $query = $sql->prepare("SELECT g.`id`,g.`port`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`resellerid`=? ".notIN($gsIDs,' g.`id`')." AND g.`serverip`=?");
            $query->execute(array($reseller_id,$ip));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $gsIDs[] = $row['id'];
                $results["gs-${row['id']}"] = array('type' => $gsprache->gameserver,'id' => $row['id'], 'name' => $ip . ':' . $row['port'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=gs&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=gs&amp;d=dl&amp;id='.$row['id']);
            }
        }
        if ($vo == true) {
            $query = $sql->prepare("SELECT v.`id`,v.`port`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `voice_server` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`resellerid`=? ".notIN($voIDs,'v.`id`')." AND v.`ip`=?");
            $query->execute(array($reseller_id,$ip));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $voIDs[] = $row['id'];
                $results["vo-${row['id']}"] = array('type' => $gsprache->voiceserver,'id' => $row['id'], 'name' => $ip . ':' . $row['port'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=vo&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=vo&amp;d=dl&amp;id='.$row['id']);
            }
        }
        if ($ro == true) {
            $notIN=notIN($roIDs,'r.`id`');
            if ($reseller_id == 0) {
                $query = $sql->prepare("SELECT r.`id`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `rserverdata` r LEFT JOIN `userdata` u ON r.`resellerid`=u.`id` WHERE r.`hostid`=0 $notIN AND r.`ip`=?");
                $query->execute(array($ip));
            } else {
                $query = $sql->prepare("SELECT r.`id`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `rserverdata` r LEFT JOIN `userdata` u ON r.`resellerid`=u.`id` WHERE r.`resellerid`=? $notIN AND r.`ip`=?");
                $query->execute(array($reseller_id,$ip));
            }
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $roIDs[] = $row['id'];
                $results["ro-${row['id']}"] = array('type' => $gsprache->root,'id' => $row['id'], 'name' => $ip,'owner' => theName($row['username'], $row['cname']),'edit' => '?w=ro&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=ro&amp;d=dl&amp;id='.$row['id']);
            }
        }
        if ($vs == true) {
            $notIN=notIN($vsIDs,'r.`id`');
            if ($reseller_id == 0) {
                $query = $sql->prepare("SELECT v.`id`,v.`ip`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `virtualcontainer` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`ip`=? $notIN");
                $query->execute(array($ip));
            } else if ($reseller_id==$admin_id) {
                $query = $sql->prepare("SELECT v.`id`,v.`ip`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `virtualcontainer` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`userid`=? $notIN AND v.`ip`=?");
                $query->execute(array($reseller_id,$ip));
            } else {
                $query = $sql->prepare("SELECT v.`id`,v.`ip`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `virtualcontainer` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`userid`=? AND v.`resellerid`=? $notIN AND v.`ip`=?");
                $query->execute(array($admin_id,$reseller_id,$ip));
            }
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $vsIDs[] = $row['id'];
                $results["ro-${row['id']}"] = array('type' => $gsprache->virtual,'id' => $row['id'], 'name' => $ip,'owner' => theName($row['username'], $row['cname']),'edit' => '?w=ro&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=ro&amp;d=dl&amp;id='.$row['id']);
            }
        }
    }
    foreach($ports as $port) {
        if ($gs == true) {
            $query = $sql->prepare("SELECT g.`id`,g.`serverip`,g.`port`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`resellerid`=:id ".notIN($gsIDs,'g.`id`')." AND (g.`port`=:port OR g.`port2`=:port OR g.`port3`=:port OR g.`port4`=:port OR g.`port5`=:port)");
            $query->execute(array(':id' => $reseller_id,':port' => $port));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $gsIDs[] = $row['id'];
                $results["gs-${row['id']}"] = array('type' => $gsprache->gameserver,'id' => $row['id'], 'name' => $row['serverip'] . ':' . $row['port'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=gs&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=gs&amp;d=dl&amp;id='.$row['id']);
            }
        }
        if ($vo == true) {
            $query = $sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `voice_server` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`resellerid`=? ".notIN($voIDs,'v.`id`')." AND v.`port`=?");
            $query->execute(array($reseller_id,$port));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $voIDs[] = $row['id'];
                $results["vo-${row['id']}"] = array('type' => $gsprache->voiceserver,'id' => $row['id'], 'name' => $row['ip'] . ':' . $row['port'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=vo&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=vo&amp;d=dl&amp;id='.$row['id']);
            }
        }
    }
    foreach($addresses as $address) {
        list($ip,$port)=explode(':',$address);
        if ($gs == true) {
            $query = $sql->prepare("SELECT g.`id`,g.`port`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`resellerid`=? ".notIN($gsIDs,'g.`id`')." AND g.`serverip`=? AND g.`port`=?");
            $query->execute(array($reseller_id,$ip,$port));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $gsIDs[] = $row['id'];
                $results["gs-${row['id']}"] = array('type' => $gsprache->gameserver,'id' => $row['id'], 'name' => $ip . ':' . $row['port'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=gs&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=gs&amp;d=dl&amp;id='.$row['id']);
            }
        }
        if ($vo == true) {
            $query = $sql->prepare("SELECT v.`id`,v.`port`,u.`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `voice_server` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`resellerid`=? ".notIN($voIDs,'v.`id`')." AND v.`ip`=? AND v.`port`=?");
            $query->execute(array($reseller_id,$ip,$port));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $voIDs[] = $row['id'];
                $results["vo-${row['id']}"] = array('type' => $gsprache->voiceserver,'id' => $row['id'], 'name' => $ip . ':' . $row['port'], 'owner' => theName($row['username'], $row['cname']),'edit' => '?w=vo&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=vo&amp;d=dl&amp;id='.$row['id']);
            }
        }
    }
    foreach($words as $word) {
        $word="%${word}%";
        if ($ad == true) {
            $query = $sql->prepare("SELECT `id`,`menudescription` FROM `addons` WHERE `resellerid`=? ".notIN($adIDs,'`id`')." AND LOWER(`menudescription`) LIKE ?");
            $query->execute(array($reseller_id,$word));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $adIDs[] = $row['id'];
                $results["ad-${row['id']}"] = array('type' => $gsprache->addon,'id' => $row['id'], 'name' => $row['menudescription'], 'owner' => '','edit' => '?w=ad&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=ad&amp;d=dl&amp;id='.$row['id']);
            }
        }
        if ($im == true) {
            $query = $sql->prepare("SELECT `id`,`description` FROM `servertypes` WHERE `resellerid`=? ".notIN($imIDs,'`id`')." AND (LOWER(`description`) LIKE ? OR `shorten` LIKE ?)");
            $query->execute(array($reseller_id,$word,$word));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $imIDs[] = $row['id'];
                $results["im-${row['id']}"] = array('type' => $gsprache->gameserver . ' ' . $gsprache->templates,'id' => $row['id'], 'name' => $row['description'], 'owner' => '','edit' => '?w=im&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=im&amp;d=dl&amp;id='.$row['id']);
            }
        }
        if ($us == true) {
            if ($reseller_id == 0) {
                $notIN=(count($usIDs)>0) ? '`id` NOT IN('.implode(',',$usIDs).') AND ' : '';
                $query = $sql->prepare("SELECT `id`,`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `userdata` WHERE $notIN (`cname` LIKE :word OR vname LIKE :word OR name LIKE :word)");
                $query->execute(array(':word' => $word));
            } else {
                $query = $sql->prepare("SELECT `id`,`cname`,CONCAT(`vname`,' ',`name`) AS `username` FROM `userdata` WHERE `resellerid`=? ".notIN($usIDs,'`id`')." AND (`cname` LIKE :word OR vname LIKE :word OR name LIKE :word)");
                if ($admin_id==$reseller_id) {
                    $query->execute(array(':resellerID' => $reseller_id,':word' => $word));
                } else {
                    $query->execute(array(':resellerID' => $admin_id,':word' => $word));
                }
            }
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $usIDs[] = $row['id'];
                $results["us-${row['id']}"] = array('type' => $gsprache->user,'id' => $row['id'], 'name' => theName($row['username'], $row['cname']),'owner' => '','edit' => '?w=us&amp;d=md&amp;id='.$row['id'], 'delete' => '?w=us&amp;d=dl&amp;id='.$row['id']);
            }
        }
    }
}

configureDateTables('-1', '1, "DESC"');

$q=implode(' ',$q);
$template_file = 'admin_search.tpl';