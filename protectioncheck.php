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
    $default_language = $user_language;
    $reseller_id = 0;

    $page_data->title = $page_sprache->protectioncheck;
    $protection_link = ($page_data->seo == 'N') ? 'protectioncheck.php' : $page_data->pageurl . '/' . $user_language . '/' . $page_category . '/';

} else {

    define('EASYWIDIR', dirname(__FILE__));

    if (is_dir(EASYWIDIR . '/install')) {
        die('Please remove the "install" folder');
    }

    $logininclude = true;

    include(EASYWIDIR . '/stuff/methods/vorlage.php');
    include(EASYWIDIR . '/stuff/methods/class_validator.php');
    include(EASYWIDIR . '/stuff/methods/functions.php');
    include(EASYWIDIR . '/stuff/settings.php');
}

if ($ui->escaped('email', 'post') != '') {
    $fullday=date('Y-m-d H:i:s',strtotime("+1 day"));
    $query = $sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? LIMIT 1");
    $query->execute(array($loguserip));
    if ($query->rowCount()==0) {
        $query = $sql->prepare("INSERT INTO `badips` (`bantime`,`failcount`,`reason`,`badip`) VALUES (?,'1','bot',?)");
    } else {
        $query = $sql->prepare("UPDATE `badips` SET `bantime`=?,`failcount`=`failcount`+1,`reason`='bot' WHERE `badip`=? LIMIT 1");
    }
    $query->execute(array($fullday, $loguserip));
    die('IP banned');
}

$logs = array();
$xmllogs = array();

$gsprache = getlanguagefile('general', $user_language, 0);
$sprache = getlanguagefile('gserver', $user_language, 0);
$ipvalue = '111.111.111.111:27015';

if ($ui->ipport('serveraddress', 'post') or ($ui->ip('ip', 'get') and $ui->port('po', 'get'))) {

    if ($ui->ipport('serveraddress', 'post')) {

        $serveraddress = $ui->ipport('serveraddress', 'post');
        $adresse_awk = explode(':', $serveraddress);

        $ip = $adresse_awk[0];
        $port = $adresse_awk[1];

    } else if ($ui->ip('ip', 'get') and $ui->port('po', 'get')) {

        $ip = $ui->ip('ip', 'get');
        $port = $ui->port('po', 'get');

        $serveraddress = $ip . ':' . $port;
    }

    if (isset($serveraddress)) {
        $ipvalue = $serveraddress;
    }

    if (isset($ip) and isset($port)) {

        $placeholders = array('%%', '%ad%', '%add%', '%dl%', '%del%', '%md%', '%mod%', '%ri%', '%start%', '%restart%', '%stop%', '%upd%', '%fail%', '%ok%', '%psw%', '%cfg%', '%import%', '%reinstall%', '%backup%', '%use%');
        $replace = array('', $gsprache->add, $gsprache->add, $gsprache->del, $gsprache->del, $gsprache->mod, $gsprache->mod, $gsprache->reinstall, $gsprache->start, $gsprache->start, $gsprache->stop, $gsprache->update,'','', $sprache->password, $sprache->config, $gsprache->import, $sprache->reinstall, $gsprache->backup, $gsprache->use);
        $placeholders2 = array('%modules%', '%voserver%', '%gserver%', '%user%', '%fastdl%', '%master%', '%user%', '%root%', '%addon%', '%settings%', '%vserver%', '%ticket_subject%', '%reseller%', '%virtual%', '%eac%', '%resync%', '%virtualimage%', '%template%', '%voserver%', '%emailsettings%', '%dns%', '%tsdns%', '%pmode%', '%file%', '%webmaster%', '%webvhost%');
        $replace2 = array($gsprache->modules, $gsprache->voiceserver, $gsprache->gameserver, $gsprache->user, $gsprache->fastdownload, $gsprache->master, $gsprache->user, $gsprache->root, $gsprache->addon2, $gsprache->settings, $gsprache->virtual, $gsprache->support, $gsprache->reseller, $gsprache->hostsystem,'Easy Anti Cheat', $sprache->resync, $gsprache->virtual . ' ' . $gsprache->template, $gsprache->template, $gsprache->voiceserver,'E-Mail '.$gsprache->settings, 'TSDNS', 'TSDNS', $sprache->protect, $gsprache->file, $gsprache->webspace . ' ' . $gsprache->master, $gsprache->webspace);


        $query = $sql->prepare("SELECT g.`protected`,g.`psince`,g.`queryName`,g.`queryNumplayers`,g.`queryMaxplayers`,g.`queryMap`,u.`cname`,t.`description` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`serverip`=? AND g.`port`=? LIMIT 1");
        $query->execute(array($ip, $port));

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $protected = $row['protected'];
            $customer = $row['cname'];
            $psince = $row['psince'];
            $name = $row['queryName'];
            $numplayers = $row['queryNumplayers'];
            $maxplayers = $row['queryMaxplayers'];
            $map = $row['queryMap'];
            $type = $row['description'];

            $since = (isset($default_language) and $default_language == 'de') ? date('d.m.Y H:i:s', strtotime($psince)) : $psince;

            if ($protected == 'Y') {

                $query2 = $sql->prepare("SELECT `useraction`,`logdate` FROM `userlog` WHERE `logdate` > ? AND `useraction` LIKE ? ORDER BY `logdate`");
                $query2->execute(array($row['psince'], '%' . $ipvalue . '%'));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                    $iconName = 'refresh';

                    if (strpos($row2['useraction'], '%stop%') !== false) {
                        $iconName = 'stop';
                    } else if (strpos($row2['useraction'], '%add%') !== false or strpos($row2['useraction'], '%ad%') !== false) {
                        $iconName = 'plus-circle';
                    } else if (strpos($row2['useraction'], '%del%') !== false or strpos($row2['useraction'], '%dl%') !== false) {
                        $iconName = 'trash';
                    } else if (strpos($row2['useraction'], '%start%') !== false or strpos($row2['useraction'], '%restart%') !== false) {
                        $iconName = 'play-circle';
                    }

                    $replacedUserAction = str_replace($placeholders2, $replace2, str_replace($placeholders, $replace, $row2['useraction']));

                    $logs[date('Y-m-d', strtotime($row2['logdate']))][date('H:i:s', strtotime($row2['logdate']))][$iconName] = $replacedUserAction;

                    $xmllogs[$row2['logdate']] = $replacedUserAction;
                }
            }
        }
    }
}

if ($ui->ipport('serveraddress', 'post')) {

    if (isset($page_include)) {

        $template_file = 'page_protectioncheck.tpl';

    } else {

        if (file_exists(EASYWIDIR . '/template/' . $template_to_use . '/standalone/protectioncheck.tpl')) {
            include(EASYWIDIR . '/template/' . $template_to_use . '/standalone/protectioncheck.tpl');
        } else if (file_exists(EASYWIDIR . '/template/' . $template_to_use . '/protectioncheck.tpl')) {
            include(EASYWIDIR . '/template/' . $template_to_use . '/protectioncheck.tpl');
        } else if (is_file(EASYWIDIR . '/template/default/standalone/protectioncheck.tpl')) {
            include(EASYWIDIR . '/template/default/standalone/protectioncheck.tpl');
        } else if (file_exists(EASYWIDIR . '/template/default/protectioncheck.tpl')) {
            include(EASYWIDIR . '/template/default/protectioncheck.tpl');
        } else {
            include(EASYWIDIR . '/template/protectioncheck.tpl');
        }
    }

    } else if (!isset($page_include) and $ui->ip('ip', 'get') and $ui->port('po', 'get')) {
    if ($ui->username('gamestring', 50, 'get') == 'xml') {
        if (!isset($protected)) {
            echo 'unknown';
        } else if ($protected == 'N') {
            $pstatus = 'no';
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
        } else if ($protected == 'Y') {
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
                $xml .= '
			<action>
				<time>'.$time.'</time>
				<log>'.$logentry.'</log>
			</action>';
            }
            $xml .= '
	</actions>
</status>';
            header("Content-type: text/xml; charset=UTF-8");
            echo $xml;
        }
    } else {
        if (!isset($protected)) {
            echo 'unknown';
        } else if ($protected == 'N') {
            echo 'no';
        } else if ($protected == 'Y') {
            echo 'yes';
        }
    }
} else if (isset($page_include)) {

    // https://github.com/easy-wi/developer/issues/62
    $langLinks = array();
    foreach ($languages as $l) {
        $tempLanguage = getlanguagefile('page', $l, 0);
        $langLinks[$l] = ($page_data->seo== 'Y') ? szrp($tempLanguage->$s)  : '?s='.$s;
    }
    $page_data->langLinks($langLinks);

    $template_file = 'page_protectioncheck.tpl';

} else {
    if (file_exists(EASYWIDIR . '/template/' . $template_to_use . '/standalone/protectioncheck.tpl')) {
        include(EASYWIDIR . '/template/' . $template_to_use . '/standalone/protectioncheck.tpl');
    } else if (file_exists(EASYWIDIR . '/template/' . $template_to_use . '/protectioncheck.tpl')) {
        include(EASYWIDIR . '/template/' . $template_to_use . '/protectioncheck.tpl');
    } else if (is_file(EASYWIDIR . '/template/default/standalone/protectioncheck.tpl')) {
        include(EASYWIDIR . '/template/default/standalone/protectioncheck.tpl');
    } else if (file_exists(EASYWIDIR . '/template/default/protectioncheck.tpl')) {
        include(EASYWIDIR . '/template/default/protectioncheck.tpl');
    } else {
        include(EASYWIDIR . '/template/protectioncheck.tpl');
    }
}
if (!isset($page_include)) {
    $sql = null;
}