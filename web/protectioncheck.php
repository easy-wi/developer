<?php
/**
 * File: protectioncheck.php.
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

if (isset($page_include)) {
    $default_language=$user_language;
    $reseller_id=0;
    if ($seo=='N') {
        $protection_link=$page_url.'/protectioncheck.php';
    } else {
        $protection_link=$page_url.'/'.$user_language.'/'.$page_category.'/';
    }
} else {
    define('EASYWIDIR',dirname(__FILE__));
    if (is_dir(EASYWIDIR."/install")) die('Please remove the "install" folder');
    $logininclude=true;
    include(EASYWIDIR.'/stuff/vorlage.php');
    include(EASYWIDIR.'/stuff/class_validator.php');
    include(EASYWIDIR.'/stuff/functions.php');
    include(EASYWIDIR.'/stuff/settings.php');
    $query="SELECT `language` FROM `settings` WHERE `resellerid`=0 LIMIT 1";
    foreach ($sql->query($query) as $row) {
        $default_language=$row['language'];
    }
    if (!isset($user_language)) $user_language=$default_language;
}
if ($ui->escaped('email','post')!='') {
    $fullday=date('Y-m-d H:i:s',strtotime("+1 day"));
    $query=$sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? LIMIT 1");
    $query->execute(array($loguserip));
    if ($query->rowCount()==0) {
        $query=$sql->prepare("INSERT INTO `badips` (`bantime`,`failcount`,`reason`,`badip`) VALUES (?,'1','bot',?)");
    } else {
        $query=$sql->prepare("UPDATE `badips` SET `bantime`=?,`failcount`=`failcount`+1,`reason`='bot' WHERE `badip`=? LIMIT 1");
    }
    $query->execute(array($fullday,$loguserip));
    die('IP banned');
}
$sprache=getlanguagefile('gserver',$user_language,0,$sql);
$ipvalue="111.111.111.111:27015";
if ($ui->ipport('serveraddress','post') or (isset($server_ip) and isset($server_port))) {
    if ($ui->ipport('serveraddress','post')) {
        $serveraddress=$ui->ipport('serveraddress','post');
        $adresse_awk=explode(":",$serveraddress);
        $ip=$adresse_awk['0'];
        $port=$adresse_awk['1'];
    } else if (isset($server_ip) and isset($server_port)) {
        $ip=$server_ip;
        $port=$server_port;
        $serveraddress=$server_ip.":".$server_port;
    }
    if (isset($serveraddress)) $ipvalue=$serveraddress;
    if(isset($ip)) {
        $query=$sql->prepare("SELECT g.`protected`,g.`psince`,g.`queryName`,g.`queryNumplayers`,g.`queryMaxplayers`,g.`queryMap`,u.`cname`,t.`description` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`serverip`=? AND g.`port`=? LIMIT 1");
        $query->execute(array($ip,$port));
        $logs=array();
        $xmllogs=array();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $protected=$row['protected'];
            $customer=$row['cname'];
            $psince=$row['psince'];
            $name=$row['queryName'];
            $numplayers=$row['queryNumplayers'];
            $maxplayers=$row['queryMaxplayers'];
            $map=$row['queryMap'];
            $type=$row['description'];
            $query=$sql->prepare("SELECT `useraction`,`logdate` FROM `userlog` WHERE `logdate`>? AND `username`=? AND `useraction` LIKE ?");
            $query->execute(array($psince,$customer,'%'.$serveraddress.'%'));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $logentry=explode(" ", $row['useraction']);
                if (($logentry['1']=="%gserver%" or $logentry['1']=="%addon%") and ($logentry['0']!='%resync%' and $logentry['0']!='%mod%')) {
                    if ($default_language=="de") {
                        $time=explode(' ', $row['logdate']);
                        $time2=explode('-', $time['0']);
                        $time3=$time2['2'].".".$time2['1'].".".$time2['0']." ".$time['1'];
                    } else {
                        $time3=$row['logdate'];
                    }
                    $placeholders1=array('%start%','%stop%'," $serveraddress",' %gserver%');
                    $placeholders2=array('%start%','%stop%','%addon%','%del%','%add%',' %ok%'," $serveraddress",' %gserver%');
                    $replace1=array('<img src="'.$page_url.'/images/16_restart.png" alt="start" />', '<img src="'.$page_url.'/images/16_stop.png" alt="stop" />','','');
                    $replace2=array('(Re)Start','Stop','Addon','Delete','Add','','','');
                    $replacedpics=str_replace($placeholders1,$replace1,$row['useraction']);
                    $replacedwords=str_replace($placeholders2,$replace2,$row['useraction']);
                    if (!empty($replacedpics)) {
                        if ($logentry['1']=="%gserver%") {
                            $logs[]="$replacedpics: $time3";
                        }
                        $xmllogs["$time3"]="$replacedwords";
                    }
                }
            }
            if ($default_language=="de") {
                $since=date('d.m.Y H:i:s',strtotime($psince));
            } else {
                $since=$psince;
            }
        }
    }
}
if (!isset($protected)) {
    $imgName='64_protected_unknown';
    $imgAlt='unknown';
} else if ($protected=="N") {
    $imgName='64_unprotected';
    $imgAlt='unprotected';
} else if ($protected=="Y") {
    $imgName='64_protected';
    $imgAlt='unprotected';
}
if ($ui->ipport('serveraddress','post')) {
    if (isset($page_include)) {
        $template_file='page_protectioncheck.tpl';
    } else {
        if (file_exists(EASYWIDIR.'/template/'.$template_to_use.'/protectioncheck.tpl')) {
            include(EASYWIDIR.'/template/'.$template_to_use.'/protectioncheck.tpl');
        } else if (file_exists(EASYWIDIR.'/template/default/protectioncheck.tpl')) {
            include(EASYWIDIR.'/template/default/protectioncheck.tpl');
        } else {
            include(EASYWIDIR.'/template/protectioncheck.tpl');
        }
    }
} else if (!isset($page_include) and isset($server_ip) and isset($server_port)) {
    if (isset($get_gamestring) and $get_gamestring == 'xml') {
        if (!isset($protected)) {
            echo "unknown";
        } else if ($protected=="N") {
            $pstatus="no";
            $xml=<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE status>
<status>
	<hostname>$name</hostname>
	<gametype>$type</gametype>
	<map>$map</map>
	<numplayers>$numplayers</numplayers>
	<maxplayers>$maxplayers</maxplayers>
	<protection>$pstatus</protection>
	<psince>0000:00:00</psince>
</status>
XML;
            header("Content-type: text/xml; charset=UTF-8");
            echo $xml;
        } else if ($protected=="Y") {
            $pstatus="yes";
            $xml='<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE status>
<status>
	<hostname>'.$name.'</hostname>
	<gametype>'.$type.'</gametype>
	<map>'.$map.'</map>
	<numplayers>'.$numplayers.'</numplayers>
	<maxplayers>'.$maxplayers.'</maxplayers>
	<protection>'.$pstatus.'</protection>
	<psince>'.$since.'</psince>
	<actions>';
            foreach ($xmllogs as $time => $logentry) {
                $xml .='
			<action>
				<time>'.$time.'</time>
				<log>'.$logentry.'</log>
			</action>';
            }
            $xml .='
	</actions>
</status>';
            header("Content-type: text/xml; charset=UTF-8");
            echo $xml;
        }
    } else {
        if (!isset($protected)) {
            echo "unknown";
        } else if ($protected=="N") {
            echo "no";
        } else if ($protected=="Y") {
            echo "yes";
        }
    }
} else if (isset($page_include)) {
    $template_file='page_protectioncheck.tpl';
} else {
    if (file_exists(EASYWIDIR.'/template/'.$template_to_use.'/protectioncheck.tpl')) {
        include(EASYWIDIR.'/template/'.$template_to_use.'/protectioncheck.tpl');
    } else if (file_exists(EASYWIDIR.'/template/default/protectioncheck.tpl')) {
        include(EASYWIDIR.'/template/default/protectioncheck.tpl');
    } else {
        include(EASYWIDIR.'/template/protectioncheck.tpl');
    }
}
if (!isset($page_include)) {
    $sql=null;
}