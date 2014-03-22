<?php

/**
 * File: settings.php.
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

// We cannot wait until it is removed from PHP and need to protect admins from themselves

if (ini_get('register_globals') == 'on') {
    @ini_set('register_globals', 'off');

    // check if we could turn it off. If not shut down.
    if (ini_get('register_globals') == 'on') {
        die('register_globals ist set to <b>on</b>. This setting is very dangerous. Easy-WI refuses to work in such an insecure inviroment');
    }
}

$ui = new ValidateUserinput($_GET, $_POST, $_SERVER, array(), $_ENV);
unset($_GET, $_POST, $_SERVER, $_ENV, $type, $host, $user, $pwd, $db);

include(EASYWIDIR . '/stuff/config.php');

$ewCfg['captcha'] = $captcha;
$ewCfg['title'] = $title;

$dbConnect['type'] = "mysql";
$dbConnect['host'] = $host;
$dbConnect['user'] = $user;
$dbConnect['pwd'] = $pwd;
$dbConnect['db'] = $db;

if (isset($debug) and $debug == 1) {

    $dbConnect['debug'] = 1;
    ini_set('display_errors',1);
    error_reporting(E_ALL|E_STRICT);

} else {
    $dbConnect['debug'] = 0;
}
try {
    $dbConnect['connect']="${dbConnect['type']}:host=${dbConnect['host']};dbname=${dbConnect['db']}";
    $sql = ($dbConnect['type'] == 'mysql') ? new PDO($dbConnect['connect'], $dbConnect['user'], $dbConnect['pwd'], array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8")) : new PDO($dbConnect['connect'], $dbConnect['user'], $dbConnect['pwd']);

    if ($dbConnect['debug'] == 1) {
        $sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    $insertlog = $sql->prepare("INSERT INTO userlog (`userid`,`reseller`,`subuser`,`username`,`usertype`,`useraction`,`ip`,`hostname`,`logdate`,`resellerid`) VALUES (:userid,:reseller,:subuser,:username,:usertype,:useraction,:ip,:hostname,NOW(),:reseller_id)");
    $insertlog->bindParam(':userid', $loguserid);
    $insertlog->bindParam(':reseller', $logreseller);
    $insertlog->bindParam(':subuser', $logsubuser);
    $insertlog->bindParam(':username', $logusername);
    $insertlog->bindParam(':usertype', $logusertype);
    $insertlog->bindParam(':useraction', $loguseraction);
    $insertlog->bindParam(':ip', $loguserip);
    $insertlog->bindParam(':hostname', $userHostname);
    $insertlog->bindParam(':reseller_id', $reseller_id);

    if ($ui->ip('REMOTE_ADDR', 'server')) {
        $loguserip = $ui->ip('REMOTE_ADDR', 'server');
        $userHostname = @gethostbyaddr($ui->ip('REMOTE_ADDR', 'server'));

    } else {
        $loguserip = 'localhost';
        $userHostname = 'localhost';
    }
}
catch(PDOException $error) {
    die($error->getMessage());
}

// many peaple do not know how to properly configure their servers, so we need to help them and set the timezone
$timezoneDefined = ini_get('date.timezone');

if (!isset($dbConnect['timezone'])) {

    if ($timezoneDefined == '') {

        $query = $sql->prepare("SELECT IF(@@session.time_zone = 'SYSTEM', @@system_time_zone, @@session.time_zone)");
        $query->execute();

        $dbConnect['timezone'] = $query->fetchColumn();

    } else {
        $dbConnect['timezone'] = $timezoneDefined;
    }
}

if ($dbConnect['timezone'] != $timezoneDefined) {
    date_default_timezone_set($dbConnect['timezone']);
}

$page_url = ($ui->escaped ('HTTPS', 'server')) ? 'https://' . $ui->domain('HTTP_HOST', 'server') : 'http://' . $ui->domain('HTTP_HOST', 'server');

if ($loguserip != 'localhost') {

    session_start();

    if (isset($_SESSION['userid']) and is_numeric($_SESSION['userid']) and isset($_SESSION['adminid']) and is_numeric($_SESSION['adminid'])) {
        $user_id = $_SESSION['userid'];
        $admin_id = $_SESSION['adminid'];

    } else if (isset($_SESSION['userid']) and is_numeric($_SESSION['userid'])) {
        $user_id = $_SESSION['userid'];

    } else if (isset($_SESSION['adminid']) and is_numeric($_SESSION['adminid'])) {
        $admin_id = $_SESSION['adminid'];
    }

    if (isset($_SESSION['resellerid']) and is_numeric($_SESSION['resellerid'])) {
        $reseller_id = $_SESSION['resellerid'];
    } else if ((isset($_SESSION['userid']) or isset($_SESSION['adminid'])) and (!isset($_SESSION['resellerid']) or !is_numeric($_SESSION['resellerid']))) {
        session_unset();
        session_destroy();
        redirect('login.php');
    }

    if (isset($_SESSION['HTTP_USER_AGENT']) and isset($_SESSION['REMOTE_ADDR'])){

        if ($_SESSION['HTTP_USER_AGENT'] != md5($ui->escaped('HTTP_USER_AGENT', 'server')) or $_SESSION['REMOTE_ADDR'] != md5($ui->ip('REMOTE_ADDR', 'server'))){
            session_unset();
            session_destroy();

            if (isset($page_include)) {
                redirect('/');
            } else {
                redirect('login.php');
            }
        }

    } else {
        $_SESSION['REMOTE_ADDR'] = md5($ui->ip('REMOTE_ADDR', 'server'));
        $_SESSION['HTTP_USER_AGENT'] = md5($ui->escaped('HTTP_USER_AGENT', 'server'));
    }
}

$rSA = array();

$htmlExtraInformation = array(
    'body' => array(),
    'css' => array(),
    'js' => array()
);

if (isset($reseller_id)) {

    $query = $sql->prepare("SELECT * FROM `settings` WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        foreach ($row as $k => $v) {
            $rSA[$k] = $v;
        }
    }

    $resellerstimezone = $rSA['timezone'];
    $template_to_use = $rSA['template'];
    $downChecks = $rSA['down_checks'];
    $logdate = date('Y-m-d H:i:s', strtotime($resellerstimezone .' hour'));

    if (isset($admin_id)) {
        $resellerLockupID = ($reseller_id != 0 and $reseller_id != $admin_id) ? $admin_id : $reseller_id;
    }

    if (isset($user_id) and !isset($admin_id)) {

        $lookupid = $reseller_id;

    } else {

        $check_split = preg_split("/\//", $ui->escaped('SCRIPT_NAME', 'server'), -1, PREG_SPLIT_NO_EMPTY);
        $which_file = $check_split[count($check_split) - 1];

        if ($which_file == 'userpanel.php') {

            $lookupid = $reseller_id;

        } else {
            $lookupid = ($reseller_id == $admin_id) ? 0 : $reseller_id;
        }
    }

    $query = $sql->prepare("SELECT `supportnumber` FROM `settings` WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($lookupid));
    $support_phonenumber = $query->fetchColumn();


} else {

    $query = $sql->prepare("SELECT * FROM `settings` WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        foreach ($row as $k => $v) {
            $rSA[$k] = $v;
        }
    }

    $template_to_use = $rSA['template'];
    $support_phonenumber = $rSA['supportnumber'];
    $logdate = date('Y-m-d H:i:s');
}

if ($loguserip!='localhost') {

    if (isset($_SESSION['language'])) {
        $user_language = $_SESSION['language'];
    }

    if (isset($page_include)) {

        $query = $sql->prepare("SELECT * FROM `page_settings` WHERE `resellerid`=0 LIMIT 1");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $seo = $row['seo'];
            $rssfeed = $row['rssfeed'];
            $maxnews=(isid($row['maxnews'],11)) ? $row['maxnews'] : 10;
            $page_default = $row['defaultpage'];
            $pageurl = $row['pageurl'];
            $protectioncheck = $row['protectioncheck'];
            $maxnews_sidebar = $row['maxnews_sidebar'];
            $newssidebar_textlength = $row['newssidebar_textlength'];
            $spamFilter = $row['spamFilter'];
            $languageFilter = $row['languageFilter'];
            $blockLinks = $row['blockLinks'];
            $blockWords = $row['blockWords'];
            $mailRequired = $row['mailRequired'];
            $commentMinLength = $row['commentMinLength'];
            $commentsModerated = $row['commentsModerated'];
            $honeyPotKey = $row['honeyPotKey'];

            if (!isurl($pageurl) and !isdomain($pageurl)) {
                $pageurl = $page_url;
            }
        }

        $query = $sql->prepare("SELECT `active` FROM `modules` WHERE `id`=9 LIMIT 1");
        $query->execute();
        $page_active = $query->fetchColumn();
        $page_active = (active_check($page_active)) ? $page_active : 'Y';

        $ewInstallPath = EASYWIDIR;

        $elements = (!empty($ewInstallPath) and strpos($ui->escaped('REQUEST_URI', 'server'), $ewInstallPath) === false) ? preg_split('/\//', $ui->escaped('REQUEST_URI', 'server'), -1, PREG_SPLIT_NO_EMPTY) : preg_split('/\//',substr($ui->escaped('REQUEST_URI', 'server'), strlen($ewInstallPath)), -1, PREG_SPLIT_NO_EMPTY);

        if (isset($seo) and $seo == 'Y' and isset($elements[0])) {

            $page_detect_language = $elements[0];

            if (!preg_match('/^[a-z]{2}+$/', $elements[0]) and !$ui->w('site', 50, 'get')) {
                $throw404 = true;
            }
        }

        if (isset($elements[1]) and $elements[1] != '') {
            $page_category = strtolower($elements[1]);
        }

        if (isset($elements[2]) and $elements[2] != '') {
            $page_name = strtolower($elements[2]);
        }

        if (isset($elements[3]) and $elements[3] != '') {
            $page_count = strtolower($elements[3]);
        }
    }

    if (!isset($user_language) and isset($user_id) and isset($admin_id)) {
        $user_language = language($admin_id);
    } else if (!isset($user_language) and isset($user_id) and !isset($admin_id)) {
        $user_language = language($user_id);
    } else if (!isset($user_language) and isset($admin_id)) {
        $user_language = language($admin_id);
    }

    if (isset($page_detect_language) and preg_match('/^[a-z]{2}+$/', $page_detect_language) and ((isset($_SESSION['language']) and $page_detect_language != $_SESSION['language']) or !isset($_SESSION['language']))){
        $language_changed = true;
        $user_language = $page_detect_language;
    }

    if ($ui->st('l', 'get') or isset($language_changed)) {
        if ($ui->st('l', 'get')) $user_language = $ui->st('l', 'get');
        
        # https://github.com/easy-wi/developer/issues/2
        if (isset($_SESSION['sID'])) {
            $query = $sql->prepare("UPDATE `userdata_substitutes` SET `language`=? WHERE `sID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($user_language, $_SESSION['sID'], $reseller_id));
        } else if (isset($admin_id)) {
            $query = $sql->prepare("UPDATE `userdata` SET `language`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($user_language, $admin_id, $reseller_id));
        } else if (isset($user_id)) {
            $query = $sql->prepare("UPDATE `userdata` SET `language`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($user_language, $user_id, $reseller_id));
        }
    }

    $default_language = (!empty($user_language)) ? $user_language : $rSA['language'];

    if (!isset($user_language) or empty($user_language)) {
        $user_language = $default_language;
    }

    $_SESSION['language'] = $user_language;

    $gsprache = (isset($reseller_id)) ? getlanguagefile('general', $user_language, $reseller_id) : getlanguagefile('general', $user_language, 0);
    $spracheResponse = (isset($reseller_id)) ? getlanguagefile('response', $user_language, $reseller_id) : getlanguagefile('response', $user_language, 0);

}

if (!isset($user_language)) {
    $user_language = $rSA['language'];
}

if (isset($logininclude) and $logininclude == true) {

    $query = $sql->prepare("DELETE FROM `badips` WHERE `bantime` <= ?");
    $query->execute(array($logdate));

    $query = $sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? AND reason='bot' LIMIT 1");
    $query->execute(array($loguserip));
    if ($query->rowCount() > 0) {
        die();
    }

    $query = $sql->prepare("SELECT `faillogins` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
    $allowedfails = $query->fetchColumn();

    $query = $sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? AND `reason`='password' AND `failcount`>=? LIMIT 1");
    $query->execute(array($loguserip, $allowedfails));

    if ($query->rowCount() > 0) {
        die('Your IP is banned');
    }
}

if ($ui->st('r', 'get')) {

    $redirectID = ($ui->id('id', 30, 'get')) ? '&id=' . $ui->id('id', 30, 'get') : '';
    $actionParam = ($redirectID != '' and $ui->smallletters('d', 10, 'get') == 'md') ? '&d=' . $ui->smallletters('d', 10, 'get') : '';

    $header = '<meta http-equiv="refresh" content="3; URL=' . $ui->escaped('SCRIPT_NAME', 'server') . '?w=' . $ui->st('r', 'get') . $redirectID . $actionParam . '">';

    $rsprache = getlanguagefile('redirect', $user_language, 0);
    $text = $rsprache->refresh;
}

if ($ui->w('action', 4, 'post')) {
    $action = $ui->w('action', 4, 'post');
}

if ($ui->smallletters('site', 50, 'get')) {
    $s = $ui->smallletters('site', 50, 'get');
}

$w = ($ui->st('w', 'get')) ? $ui->st('w', 'get') : 'ho';
$d = ($ui->st('d', 'get')) ? $ui->st('d', 'get') : 'md';
$start = ($ui->id('p', 19, 'get')) ? $ui->id('p', 19, 'get') : 0;

if ($ui->smallletters('t','1', 'get')) {
    $list_type = $ui->smallletters('t','1', 'get');
    if ($list_type=="m") {
        $where="WHERE `type`='map'";
    } else if ($list_type=="t") {
        $where="WHERE `type`='tool'";
    } else {
        $list_type="a";
        $where = '';
    }
} else {
    $list_type="a";
    $where = '';
}

if ($ui->pregw('g','14', 'get')) {
    $list_gtype = $ui->pregw('g','14', 'get');
    if ($where != '') {
        $where .=" AND shorten='$list_gtype'";
    } else {
        if ($w=="lo") {
            $where = $list_gtype;
        } else if ($list_gtype!='all') {
            $where="WHERE shorten='$list_gtype'";
        }
    }
} else {
    $list_gtype = '';
}

if ($ui->pregw('m','20', 'get')) {
    $list_gtype = $ui->pregw('m','20', 'get');
    if ($where != '') {
        $where .=" AND s.`shorten`='$list_gtype'";
    } else if ($list_gtype!="all") {
        $where="WHERE s.`shorten`='$list_gtype'";
    }
} else {
    $list_gtype = '';
}

if (empty($where) and $w!="lo" and $w!="rs" and ($w!="ma" and $d!="ud")) {
    $where="WHERE `resellerid`=:reseller_id";
} else if (empty($where) and $w!="lo" and $w!="rs" and ($w=="ma" and $d=="ud")) {
    $where="WHERE r.`resellerid`=:reseller_id";
} else if ($w!="lo" and ($w!="ma" and $d!="ud")) {
    $where .=" AND `resellerid`=:reseller_id";
} else if ($w!="lo" and ($w=="ma" and $d=="ud")) {
    $where .=" AND r.`resellerid`=:reseller_id";
}

if ($ui->isinteger('a', 'get')) {
    $a = (int) $ui->isinteger('a', 'get');
    $amount = $a;
    $_SESSION['amount'] = $a;
} else {
    $amount = (isset($_SESSION['amount']) and is_int($_SESSION['amount'])) ? $_SESSION['amount'] : 20;
}

$dirs = array();
$languages = array();

if (is_dir(EASYWIDIR . '/languages/' . $template_to_use . '/')) {
    $dirs = array_merge($dirs, scandir(EASYWIDIR . '/languages/' . $template_to_use . '/'));
}
if (is_dir(EASYWIDIR . '/languages/default/')) {
    $dirs = array_merge($dirs , scandir(EASYWIDIR . '/languages/default/'));
}
if (is_dir(EASYWIDIR . '/languages/')) {
    $dirs = array_merge($dirs , scandir(EASYWIDIR . '/languages/'));
}

$dirs = array_unique($dirs);

foreach ($dirs as $row) {
    if (small_letters_check($row, 2)) {
        $languages[] = $row;
    }
}

if ($ui->escaped('HTTP_REFERER', 'server')) {
    $referrer = $ui->escaped('HTTP_REFERER', 'server');
}