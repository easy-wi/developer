<?php

/**
 * File: login.php.
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

$s = preg_split('/\//',$_SERVER['SCRIPT_NAME'],-1,PREG_SPLIT_NO_EMPTY);
$ewInstallPath = '';
if (count($s)>1) {
    unset($s[(count($s) - 1)]);
    $ewInstallPath = implode('/', $s) . '/';
}
define('EASYWIDIR', dirname(__FILE__));

if (is_dir(EASYWIDIR . '/install')) {
    die('Please remove the "install" folder');
}

include(EASYWIDIR . '/stuff/vorlage.php');
include(EASYWIDIR . '/stuff/class_validator.php');
include(EASYWIDIR . '/third_party/password_compat/password.php');
include(EASYWIDIR . '/stuff/functions.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

if ((!isset($ui->get['w']) and isset($ui->post['username'])) or (isset($ui->get['w']) and $ui->get['w'] != 'pr')) {
    $logininclude = true;
}

if ($ui->ismail('email', 'post')) {
    $fullday = date('Y-m-d H:i:s', strtotime('+1 day'));

    $query = $sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? LIMIT 1");
    $query->execute(array($loguserip));
    $rowcount = $query->rowcount();

    $query = ($rowcount == 0) ? $sql->prepare("INSERT INTO `badips` (`bantime`,`failcount`,`reason`,`badip`) VALUES (?,'1','bot',?)") : $sql->prepare("UPDATE `badips` SET `bantime`=?, `failcount`=failcount+1, `reason`='bot' WHERE `badip`=? LIMIT 1");
    $query->execute(array($fullday,$loguserip));
}

$default_language = $rSA['language'];
$sprache = getlanguagefile('login', $default_language, 0);
$vosprache = getlanguagefile('voice', $default_language, 0);

if ($ui->st('w', 'get') == 'lo') {

    if (isset($ui->server['HTTP_REFERER'])) {
        $refstring = explode('/', substr(str_replace(array('http://' . $ui->domain('HTTP_HOST', 'server'), 'https://' . $ui->domain('HTTP_HOST', 'server'), '//'), array('', '', '/'), strtolower($ui->server['HTTP_REFERER'])), strlen($ewInstallPath)));
        $referrer = (isset($refstring[1])) ? explode('?',$refstring[1]) : '';
    } else {
        $referrer[0] = 'login.php';
    }

    if (isset($_SESSION['resellerid']) and isset($_SESSION['adminid']) and isset($_SESSION['oldid']) and isset($_SESSION['oldresellerid']) and !isset($_SESSION['userid']) and $_SESSION['resellerid'] != 0 and $referrer[0] == 'admin.php') {
        $_SESSION['adminid'] = $_SESSION['oldid'];
        $_SESSION['resellerid'] = $_SESSION['oldresellerid'];

        if ($_SESSION['oldresellerid'] != 0 and $_SESSION['oldid'] == $_SESSION['oldresellerid']) {
            $_SESSION['oldresellerid'] = 0;
            $_SESSION['oldid'] = $_SESSION['oldadminid'];
            unset($_SESSION['oldadminid']);
        }

        redirect('admin.php');

    } else if (isset($_SESSION['adminid']) and isset($_SESSION['userid']) and $referrer[0] == 'userpanel.php') {
        unset($_SESSION['userid']);
        redirect('admin.php');

    } else {
        session_unset();
        session_destroy();
        redirect($page_url . '/' . $ewInstallPath);
    }

} else if ($ui->st('w', 'get') == 'ba') {
    $sus = $sprache->banned;
    $include = 'login.tpl';

} else if ($ui->st('w', 'get') == 'up') {
    $sus=($ui->escaped('error', 'get')) ? 'External Auth failed: ' . htmlentities(base64_decode(urldecode($ui->escaped('error', 'get')))) : $sprache->bad_up;
    $include = 'login.tpl';

} else if ($ui->st('w', 'get') == 'pr') {

    $token = '';

    if (($ui->ismail('um', 'post') or $ui->username('um', 50, 'post')) and !$ui->w('gamestring', 32, 'get')) {

        # https://github.com/easy-wi/developer/issues/43
        $send = true;
        $text = $sprache->send;

        $query = $sql->prepare("SELECT `id`,`cname`,`logintime`,`lastlogin` FROM `userdata` WHERE `cname`=? OR `mail`=? ORDER BY `lastlogin` DESC LIMIT 1");
        $query->execute(array($ui->username('um',50, 'post'), $ui->ismail('um', 'post')));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $userid = $row['id'];
            $md5 = md5($userid . $row['logintime'] . $row['cname'] . $row['lastlogin'] . mt_rand());

            $folders = explode('/', $ui->server['SCRIPT_NAME']);
            $amount = count($folders) - 1;
            $i = 0;
            $path = '';

            while ($i < $amount) {
                $path .= $folders[$i] . '/';
                $i++;
            }

            $webhostdomain = (isset($ui->server['HTTPS'])) ? 'https://' . $ui->server['HTTP_HOST'] . $path : 'http://' . $ui->server['HTTP_HOST'] . $path;
            $link = $webhostdomain . 'login.php?w=pr&amp;gamestring=' . $md5;
            $htmllink = '<a href="' . $link . '">' . $link . '</a>';

            $query2 = $sql->prepare("UPDATE `userdata` SET `token`=? WHERE `id`=? LIMIT 1");
            $query2->execute(array($md5, $userid));

            sendmail('emailpwrecovery', $userid, $htmllink, '');
        }

    } else if ($ui->password('password1', 255, 'post') and $ui->password('password2', 255, 'post') and $ui->w('token', 32, 'get')) {

        if ($ui->password('password1', 255, 'post') == $ui->password('password2', 255, 'post')) {

            $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `token`=? LIMIT 1");
            $query->execute(array($ui->w('token',32, 'get')));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $text = $sprache->passwordreseted;
                $newHash = passwordCreate($row['cname'], $ui->password('password1', 255, 'post'));

                if (is_array($newHash)) {
                    $query2 = $sql->prepare("UPDATE `userdata` SET `token`='',`security`=?,`salt`=? WHERE `id`=? LIMIT 1");
                    $query2->execute(array($newHash['hash'], $newHash['salt'], $row['id']));
                } else {
                    $query2 = $sql->prepare("UPDATE `userdata` SET `token`='',`security`=? WHERE `id`=? LIMIT 1");
                    $query2->execute(array($newHash, $row['id']));
                }
            }

        } else if ($ui->password('password1', 255, 'post') != $ui->password('password2', 255, 'post'))  {

            # https://github.com/easy-wi/developer/issues/43
            $token = '&amp;gamestring=' . $ui->w('token',32, 'get');
            $text = $sprache->pwnomatch;
        }

    } else if ($ui->w('gamestring',32, 'get')) {

        $token = '&amp;token=' . $ui->w('gamestring',32, 'get');
        $recover = false;
        $randompass = passwordgenerate(10);

        $query = $sql->prepare("SELECT 1 FROM `userdata` WHERE `token`=? LIMIT 1");
        $query->execute(array($ui->w('gamestring', 32, 'get')));
        if ($query->rowCount() > 0) {
            $recover = true;
        }
    }

    $include = 'passwordrecovery.tpl';

} else {

    $serviceProvider = (string) $ui->w('serviceProvider', 255, 'get');

    if ($serviceProvider and file_exists(EASYWIDIR . '/third_party/hybridauth/Hybrid/Providers/' . $serviceProvider . '.php')) {
        $_SERVER = $ui->server;

        $pageUrl = '';

        $query = $sql->prepare("SELECT `pageurl`,`seo`,`registration` FROM `page_settings` WHERE `resellerid`=0 LIMIT 1");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $pageUrl = $row['pageurl'];
            $seo = $row['seo'];
            $registration = $row['registration'];
        }

        $serviceProviderConfig = array(
            'base_url' => $pageUrl . '/login.php?endpoint=1',
            'debug_mode' => (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) ? true : false,
            'debug_file' => EASYWIDIR . '/third_party/hybridauth/log/hybridauth.log',
            'providers' => array()
        );

        $query = $sql->prepare("SELECT `serviceProviderID`,`filename`,`identifier`,`token` FROM `userdata_social_providers` WHERE `resellerID`=0 AND `active`='Y'");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $serviceProviderConfig['providers'][$row['filename']] = array(
                'internalID' => $row['serviceProviderID'],
                'enabled' => true
            );

            if (strlen($row['identifier']) > 0) {
                $serviceProviderConfig['providers'][$row['filename']]['keys']['id'] = $row['identifier'];
            }

            if (strlen($row['token']) > 0) {
                $serviceProviderConfig['providers'][$row['filename']]['keys']['secret'] = $row['token'];
            }
        }
    }

    if (isset($serviceProviderConfig['providers'][$serviceProvider]) and $ui->id('loginUserId', 10, 'get')) {

        if (isset($_SESSION['loginUserAllowed'][$ui->id('loginUserId', 10, 'get')])) {

            $query = $sql->prepare("SELECT `id`,`accounttype`,`cname`,`active`,`security`,`resellerid`,`mail`,`salt`,`externalID` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($ui->id('loginUserId', 10, 'get')));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $username = $row['cname'];
                $id = $row['id'];
                $active = $row['active'];
                $mail = $row['mail'];
                $externalID = $row['externalID'];
                $resellerid = $row['resellerid'];
                $accounttype = $row['accounttype'];

                $passwordCorrect = true;
            }
        }

        unset($_SESSION['loginUserAllowed']);

    } else if (isset($serviceProviderConfig['providers'][$serviceProvider])) {

        $_SERVER = $ui->server;
        $_GET = $ui->get;
        $_POST = $ui->post;

        include(EASYWIDIR . '/third_party/hybridauth/Hybrid/Auth.php');

        try{

            $connectedUsers = array();

            // initialize Hybrid_Auth with a given file
            $hybridauth = new Hybrid_Auth($serviceProviderConfig);

            // try to authenticate with the selected provider
            $serviceProviderAdapter = $hybridauth->authenticate($serviceProvider);

            $userProfile = $serviceProviderAdapter->getUserProfile();
            $serviceProviderAdapter->logout();

            // get all user for this identifier and service provider. User should be able to select the user he is going to logon to
            $serviceProviderID = $serviceProviderConfig['providers'][$serviceProvider]['internalID'];

            if ((isset($user_id) or isset($admin_id)) and strlen($userProfile->identifier) > 0) {

                $query = $sql->prepare("INSERT INTO `userdata_social_identities` (`userID`,`serviceProviderID`,`serviceUserID`,`resellerID`) VALUES (?,?,?,?)");
                $query->execute(array((isset($admin_id)) ? $admin_id : $user_id, $serviceProviderID, $userProfile->identifier, $reseller_id));

                $redirectURL = (isset($admin_id)) ? $pageUrl . '/admin.php?w=su&added=' . $serviceProvider . '&r=su' : $pageUrl . '/userpanel.php?w=se&added=' . $serviceProvider . '&r=se';

                redirect($redirectURL);

            } else {

                $query = $sql->prepare("SELECT u.`id`,u.`cname`,`mail`,CONCAT(u.`vname`,' ',u.`name`) AS `username` FROM `userdata_social_identities` AS s INNER JOIN `userdata` AS u ON u.`id`=s.`userID` WHERE s.`serviceProviderID`=? AND s.`serviceUserID`=? AND u.`active`='Y'");
                $query->execute(array($serviceProviderID, $userProfile->identifier));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                    $username = trim($row['username']);
                    $username = (strlen($username) > 0) ? $username : $row['cname'];

                    $connectedUsers[$row['id']] = $username . ' (' . $row['mail'] . ')';

                }

                $connectedUserCount = count($connectedUsers);

                // no user has been found. Check if registration is allowed. If yes display registration form
                if ($connectedUserCount == 0) {

                    if (isset($registration) and in_array($registration, array('A', 'M', 'D'))) {

                        $page_sprache = getlanguagefile('page', $user_language, 0);

                        $_SESSION['serviceProviderData']['userProfile'] = (array) $userProfile;
                        $_SESSION['serviceProviderData']['serviceProviderID'] = (string) $serviceProviderID;

                        $redirectURL = ($seo == 'Y') ? $pageUrl . '/' . $user_language . '/' . szrp($page_sprache->register) .'/' : $pageUrl . '/index.php?site=register';

                        redirect($redirectURL);
                    }

                    // multiple active users are connected, let the user pick one
                }  else if ($connectedUserCount > 1) {

                    $sprache->multipleHelper = str_replace('%sp%', $serviceProvider, $sprache->multipleHelper);

                    $_SESSION['loginUserAllowed'] = $connectedUsers;

                    $include = 'login_mutiple.tpl';

                    // exactly one user connected, login
                } else {

                    $query = $sql->prepare("SELECT `id`,`accounttype`,`cname`,`active`,`security`,`resellerid`,`mail`,`salt`,`externalID` FROM `userdata` WHERE `id`=? LIMIT 1");
                    $query->execute(array(key($connectedUsers)));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                        $username = $row['cname'];
                        $id = $row['id'];
                        $active = $row['active'];
                        $mail = $row['mail'];
                        $externalID = $row['externalID'];
                        $resellerid = $row['resellerid'];
                        $accounttype = $row['accounttype'];

                        $passwordCorrect = true;
                    }

                }
            }
        }
        catch( Exception $e ){

            $sus = $e;

            $serviceProviders = array();
            $query = $sql->prepare("SELECT `filename` FROM `userdata_social_providers` WHERE `resellerID`=0 AND `active`='Y'");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $serviceProviders[$row['filename']] = strtolower($row['filename']);
            }

            if (count($serviceProviders) > 0) {
                $htmlExtraInformation['css'][] = '<link href="css/default/social_buttons.css" rel="stylesheet">';
            }

            $include = 'login.tpl';
        }

    } else if ($ui->escaped('endpoint', 'get')) {

        $_SERVER = $ui->server;
        $_GET = $ui->get;
        $_POST = $ui->post;
        include(EASYWIDIR . '/third_party/hybridauth/Hybrid/Auth.php');
        include(EASYWIDIR . '/third_party/hybridauth/Hybrid/Endpoint.php');

        Hybrid_Endpoint::process();
    }

    if (!isset($include) and !isset($passwordCorrect) and !$ui->username('username', 255, 'post') and !$ui->ismail('username', 255, 'post') and !$ui->password('password', 255, 'post') and !isset($_SESSION['sessionid'])) {

        $serviceProviders = array();
        $query = $sql->prepare("SELECT `filename` FROM `userdata_social_providers` WHERE `resellerID`=0 AND `active`='Y'");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $serviceProviders[$row['filename']] = strtolower($row['filename']);
        }

        if (count($serviceProviders) > 0) {
            $htmlExtraInformation['css'][] = '<link href="css/default/social_buttons.css" rel="stylesheet">';
        }

        $include = 'login.tpl';

    } else if (!isset($include) and (isset($passwordCorrect) or (($ui->username('username', 255, 'post') or $ui->ismail('username', 'post')) and $ui->password('password', 255, 'post') and !isset($_SESSION['sessionid'])))) {

        $password = $ui->password('password', 255, 'post');

        if (isset($ewCfg) and $ewCfg['captcha'] == 1) {

            if (md5($ui->w('captcha', 4, 'post')) != $_SESSION['captcha']) {
                $halfhour = date('Y-m-d H:i:s', strtotime('+30 minutes'));

                $query = $sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? LIMIT 1");
                $query->execute(array($loguserip));
                $rowcount = $query->rowCount();

                $query=($rowcount==0) ? $sql->prepare("INSERT INTO `badips` (`bantime`,`failcount`,`reason`,`badip`) VALUES (?,'1','password',?)") : $sql->prepare("UPDATE `badips` SET `bantime`=?, `failcount`=`failcount`+1, `reason`='password' WHERE `badip`=? LIMIT 1");
                $query->execute(array($halfhour, $loguserip));

                redirect('login.php?w=ca&r=lo');

            }
        }

        $salt = '';

        $query = $sql->prepare("SELECT `id`,`accounttype`,`cname`,`active`,`security`,`resellerid`,`mail`,`salt`,`externalID` FROM `userdata` WHERE `cname`=? OR `mail`=? ORDER BY `lastlogin` DESC LIMIT 1");
        $query->execute(array($ui->username('username', 255, 'post'), $ui->ismail('username', 'post')));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $username = $row['cname'];
            $id = $row['id'];
            $active = $row['active'];
            $mail = $row['mail'];
            $externalID = $row['externalID'];
            $resellerid = $row['resellerid'];
            $accounttype = $row['accounttype'];

            $passwordCorrect = passwordCheck($password, $row['security'], $row['cname'], $row['salt']);

            if ($passwordCorrect !== true and $passwordCorrect !== false) {
                if (is_array($passwordCorrect)) {
                    $query = $sql->prepare("UPDATE `userdata` SET `security`=?,`salt`=? WHERE `id`=? LIMIT 1");
                    $query->execute(array($passwordCorrect['hash'], $passwordCorrect['salt'], $id));
                } else {
                    $query = $sql->prepare("UPDATE `userdata` SET `security`=? WHERE `id`=? LIMIT 1");
                    $query->execute(array($passwordCorrect, $id));
                }
            }
        }

        # https://github.com/easy-wi/developer/issues/2
        if (!isset($active)) {
            $query = $sql->prepare("SELECT * FROM `userdata_substitutes` WHERE `loginName`=? LIMIT 1");
            $query->execute(array($ui->username('username', 255, 'post')));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $mail = '';
                $externalID = 0;
                $sID = $row['sID'];
                $id = $row['userID'];
                $username = $row['loginName'];
                $active = $row['active'];
                $resellerid = $row['resellerID'];

                $accounttype = 'v';

                $passwordCorrect = passwordCheck($password, $row['passwordHashed'], $row['loginName'], $row['salt']);

                if ($passwordCorrect !== true and $passwordCorrect !== false) {
                    if (is_array($newHash)) {
                        $query = $sql->prepare("UPDATE `userdata_substitutes` SET `passwordHashed`=?,`salt`=? WHERE `sID`=? LIMIT 1");
                        $query->execute(array($passwordCorrect['hash'], $passwordCorrect['salt'], $sID));
                    } else {
                        $query = $sql->prepare("UPDATE `userdata_substitutes` SET `passwordHashed`=? WHERE `sID`=? LIMIT 1");
                        $query->execute(array($passwordCorrect, $sID));
                    }
                }
            }
        }

        if (!isset($sID) and isset($active) and $active == 'Y' and isset($passwordCorrect) and $passwordCorrect === false) {

            $authLookupID = ($resellerid == $id) ? 0 : $resellerid;

            $query = $sql->prepare("SELECT `active`,`ssl`,`user`,`domain`,AES_DECRYPT(`pwd`,?) AS `decryptedPWD`,`file` FROM `api_external_auth` WHERE `resellerID`=? LIMIT 1");
            $query->execute(array($aeskey, $authLookupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $activeAuth = $row['active'];
                $portAuth = ($row['ssl'] == 'Y') ? 433 : 80;
                $userAuth = urlencode($row['user']);
                $pwdAuth = urlencode($row['decryptedPWD']);
                $domainAuth = $row['domain'];
                $fileAuth = $row['file'];

                $xml = new DOMDocument('1.0','utf-8');
                $element = $xml->createElement('user');

                $key = $xml->createElement('username', $username);
                $element->appendChild($key);

                $key = $xml->createElement('pwd', $password);
                $element->appendChild($key);

                $key = $xml->createElement('mail', $mail);
                $element->appendChild($key);

                $key = $xml->createElement('externalID', $externalID);
                $element->appendChild($key);

                $xml->appendChild($element);

                $postXML = urlencode(base64_encode($xml->saveXML()));
            }

            if (isset($activeAuth) and $activeAuth== 'Y') {

                $reply = webhostRequest($domainAuth, $ui->escaped('HTTP_HOST', 'server'), $fileAuth, array('authPWD' => $pwdAuth, 'userAuth' => $userAuth, 'postXML' => $postXML), $portAuth);

                $xmlReply= @simplexml_load_string($reply);

                if ($xmlReply and isset($xmlReply->success) and $xmlReply->success == 1 and $xmlReply->user == $username) {

                    $passwordCorrect = true;
                    $newHash = passwordCreate($username, $password);

                    if (is_array($newHash)) {
                        $query = $sql->prepare("UPDATE `userdata` SET `security`=?,`salt`=? WHERE `id`=? LIMIT 1");
                        $query->execute(array($newHash['hash'], $newHash['salt'], $id));
                    } else {
                        $query = $sql->prepare("UPDATE `userdata` SET `security`=? WHERE `id`=? LIMIT 1");
                        $query->execute(array($newHash, $id));
                    }


                } else if ($xmlReply and isset($xmlReply->error)) {
                    $externalAuthError = $xmlReply->error;

                } else if ($reply != null and $reply != false) {
                    $externalAuthError = $reply;
                }
            }
        }

        if (isset($active, $id, $resellerid) and $active == 'Y' and isset($passwordCorrect) and $passwordCorrect) {

            session_unset();
            session_destroy();
            session_start();

            # https://github.com/easy-wi/developer/issues/2
            if (isset($sID)) {
                $query = $sql->prepare("SELECT `logintime`,`language` FROM `userdata_substitutes` WHERE `sID`=? LIMIT 1");
                $query->execute(array($sID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $logintime = $row['logintime'];
                    $_SESSION['language'] = $row['language'];
                }

                $query = $sql->prepare("UPDATE `userdata_substitutes` SET `lastlogin`=?,`logintime`=? WHERE `sID`=? LIMIT 1");
                $query->execute(array($logintime, $logdate, $sID));

            } else if (isset($id)) {
                $query = $sql->prepare("SELECT `logintime`,`language` FROM `userdata` WHERE `id`=? LIMIT 1");
                $query->execute(array($id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $logintime = $row['logintime'];
                    $_SESSION['language'] = $row['language'];
                }

                $query = $sql->prepare("UPDATE `userdata` SET `lastlogin`=?,`logintime`=? WHERE `id`=? LIMIT 1");
                $query->execute(array($logintime, $logdate, $id));

            } else {
                redirect('login.php');
            }

            if (!isset($accounttype) or !isset($resellerid)  or ($accounttype == 'r' and $resellerid < 1)) {
                redirect('login.php');
            }

            $_SESSION['resellerid'] = $resellerid;

            $query = $sql->prepare("DELETE FROM `badips` WHERE `badip`=?");
            $query->execute(array($loguserip));

            if (isanyadmin($id) or rsellerpermisions($id)) {
                $_SESSION['adminid'] = $id;

                if (isset($_SESSION['adminid']) and is_numeric($_SESSION['adminid'])) {
                    $admin_id = $_SESSION['adminid'];
                }

            } else if (isanyuser($id)) {
                $_SESSION['userid'] = $id;

                if (isset($_SESSION['userid']) and is_numeric($_SESSION['userid'])) {
                    $user_id = $_SESSION['userid'];
                }

                if (isset($sID)) {
                    $_SESSION['sID'] = $sID;
                }
            }

            $ref = '';

            if ($ui->url('HTTP_REFERER', 'server')) {
                $ref = $ui->url('HTTP_REFERER', 'server');
            } else if ($ui->domain('HTTP_REFERER', 'server')) {
                $ref = $ui->domain('HTTP_REFERER', 'server');
            }

            $referrer = explode('/', str_replace(array('http://', 'https://'), '', strtolower($ref)));

            if (isset($referrer[1]) and $referrer[1] == 'login.php') {
                $topanel = true;
            }

            if (!isset($user_id) and !isset($admin_id)) {
                header('Location: login.php&r=lo');

            } else if (isset($user_id)) {
                redirect('userpanel.php');

            } else if (isset($admin_id)) {

                $folders = explode('/', $ui->server['SCRIPT_NAME']);
                $amount = count($folders) - 1;
                $i = 0;
                $path = '';
                while ($i < $amount) {
                    $path .= $folders[$i] . '/';
                    $i++;
                }

                $webhostdomain = (isset($ui->server['HTTPS'])) ? 'https://' . $ui->server['HTTP_HOST'] . $path : 'http://' . $ui->server['HTTP_HOST'] . $path;

                $query = $sql->prepare("UPDATE `settings` SET `paneldomain`=? WHERE `resellerid`=0 LIMIT 1");
                $query->execute(array($webhostdomain));

                $params = @json_decode(licenceRequest(true));

                if (isanyadmin($admin_id) or rsellerpermisions($admin_id)) {
                    redirect('admin.php');
                } else {
                    redirect('login.php&r=lo');
                }
            }

        } else if (!isset($passwordCorrect) or $passwordCorrect === false) {

            $halfhour = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $query = $sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? LIMIT 1");
            $query->execute(array($loguserip));
            $rowcount = $query->rowCount();

            $query = ($rowcount == 0) ? $sql->prepare("INSERT INTO `badips` (bantime,failcount,reason,badip) VALUES (?,'1','password',?)") : $sql->prepare("UPDATE `badips` SET `bantime`=?,`failcount`=`failcount`+1, `reason`='password' WHERE `badip`=? LIMIT 1");
            $query->execute(array($halfhour,$loguserip));

            if (isset($externalAuthError)) {
                redirect('login.php?w=up&error=' . urlencode(base64_encode($externalAuthError)).'&r=lo');
            } else {
                redirect('login.php?w=up&r=lo');
            }

        } else if (isset($active) and $active == 'N') {
            redirect('login.php?w=su&r=lo');

        } else {
            redirect('login.php?w=up&r=lo');
        }

    } else if (!isset($include) and $ui->escaped('username', 'post') and $ui->escaped('password', 'post')) {
        redirect('login.php?w=up&r=lo');

    } else if(!isset($include)) {
        redirect('login.php?w=lo');
    }
}

if (isset($include) and isset($template_to_use)) {
    if (is_file(EASYWIDIR . '/template/' . $template_to_use . '/' . $include)) {
        include(EASYWIDIR . '/template/' . $template_to_use . '/' . $include);
    } else if (is_file(EASYWIDIR . '/template/default/' . $include)) {
        include(EASYWIDIR . '/template/default/' . $include);
    } else {
        include(EASYWIDIR . '/template/' . $include);
    }
}

$sql = null;