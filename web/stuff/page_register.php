<?php

/**
 * File: page_register.php.
 * Author: Ulrich Block
 * Date: 27.07.13
 * Time: 13:19
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

include(EASYWIDIR . '/third_party/password_compat/password.php');

if (!isset($page_include) or (isset($user_id)) or isset($admin_id) or isset($reseller_id)) {
    if (isset($page_data->canurl)) header('Location: '.$page_data->canurl);
    else header('Location: index.php');
    die;
}
$query = $sql->prepare("SELECT `registration`,`registrationQuestion`,`registrationBadEmail`,`registrationBadIP` FROM `page_settings` WHERE `resellerid`=0 LIMIT 1");
$query->execute();
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $registration = $row['registration'];
    $registrationQuestion = $row['registrationQuestion'];
    $registrationBadEmail = $row['registrationBadEmail'];
    $registrationBadIP = $row['registrationBadIP'];
}
$langObject = getlanguagefile('user',(isset($user_language)) ? $user_language : $default_language,0);
if (isset($registration) and in_array($registration, array('A','M','D'))) {
    if (isset($page_name) and isset($page_count) and $page_name == 'activate' and wpreg_check($page_count,100)) {

        // Check if a user to the activation ID exists
        $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `token`=? LIMIT 1");
        $query->execute(array($page_count));
        $userID = $query->fetchColumn();
        if (isid($userID,10)) {
            $query = $sql->prepare("UPDATE `userdata` SET `active`='Y',`token`=null,`updateTime`=NOW() WHERE `id`=? LIMIT 1");
            $query->execute(array($userID));
            $_SESSION['userid'] = $userID;
            $_SESSION['resellerid'] = 0;
            $template_file = $page_sprache->registerActivated;
            $langObjectTemp = getlanguagefile('redirect',(isset($user_language)) ? $user_language : $default_language,0);
            $text = $langObjectTemp->refresh;
            $langObjectTemp = null;
            if (isset($page_data->canurl)) $header='<meta http-equiv="refresh" content="3; URL='.$page_data->canurl.'">';
            else $header='<meta http-equiv="refresh" content="3; URL=/">';
        } else $template_file = $page_sprache->registerErrorActivatedFailed;
    } else {
        $selectlanguages=getlanguages($template_to_use);

        // default values in case an input error appears so that the user only needs to enter false data
        $loginname = $ui->username('loginname',255, 'post');
        $mail = $ui->ismail('mail', 'post');
        $password = $ui->password('password',100, 'post');
        $passwordsecond = $ui->password('passwordsecond',100, 'post');
        $name = $ui->names('name',255, 'post');
        $vname = $ui->names('vname',255, 'post');
        $vname = $ui->names('vname',255, 'post');
        $bday=date('Y-m-d',strtotime($ui->isDate('birthday', 'post')));
        $bdayShow=(isset($user_language) and $user_language == 'de') ? date('d.m.Y',strtotime($ui->isDate('birthday', 'post'))) : date('Y-m-d',strtotime($ui->isDate('birthday', 'post')));
        $error = array();
        $alert = array();
        $tous = array();
        $query = $sql->prepare("SELECT `lang`,`text` FROM `translations` WHERE `type`='to'");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $tous[$row['lang']] = $row['text'];
        if (isset($user_language) and isset($tous[$user_language])) $tou = $tous[$user_language];
        else if (isset($default_language) and isset($tous[$default_language])) $tou = $tous[$default_language];
        else if (count($tous)>0) $tou=key($tous);
        // Check if any Input was entered
        if (($ui->escaped('mail', 'post') or $ui->escaped('password', 'post')) and !$ui->escaped('email', 'post')) {

            // Captcha match?
            if (!isset($_SESSION['registerToken']) or $ui->w('token',32, 'post') != $_SESSION['registerToken']) $error[] = $page_sprache->registerErrorCookies;

            // E-Mail in use?
            if ($ui->ismail('mail', 'post')) {
                $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE `mail`=? LIMIT 1");
                $query->execute(array($ui->ismail('mail', 'post')));
                if ($query->fetchColumn()>0) {
                    $error[] = $page_sprache->registerErrorMail;
                    $alert['email'] = true;
                } else {
                    foreach (explode("\r\n",$registrationBadEmail) as $row) {
                        if (strlen($row)>0 and substr($ui->ismail('mail', 'post'), -1*strlen($row))===$row and !in_array($page_sprache->registerErrorMail,$error)) {
                            $error[] = $page_sprache->registerErrorMail;
                            $alert['email'] = true;
                        }
                    }
                }
            } else {
                $error[] = $page_sprache->registerErrorMail;
                $alert['email'] = true;
            }

            // TOU?
            if (isset($tou) and $ui->active('tou', 'post') != 'Y') {
                $error[] = $page_sprache->registerErrorTou;
                $alert['tou'] = true;
            }

            // Password entered and stronger one?
            if ($ui->password('password',100, 'post') and $ui->password('passwordsecond',100, 'post') and $ui->password('password',100, 'post') != $ui->password('passwordsecond',100, 'post')) {
                $error[] = $page_sprache->registerErrorPassword;
                $alert['password2'] = true;
            } else if ($ui->escaped('password', 'post') and !$ui->password('password',100, 'post')) {
                $error[] = $page_sprache->registerErrorPassword2;
                $alert['password'] = true;
            } else if (!$ui->escaped('password', 'post')) {
                $error[] = $page_sprache->registerErrorPassword3;
                $alert['password'] = true;
            }

            // IP blocked?
            if (count($error)>0) {
                foreach (explode("\r\n",$registrationBadIP) as $row) {
                    if (strlen($row)>0 and substr($ui->ip('REMOTE_ADDR', 'server'),0,strlen($row))===$row and !in_array($page_sprache->registerErrorIP,$error)) $error[] = $page_sprache->registerErrorBot;
                }
            }

            // If no error occurred go on otherwise display form again
            if (count($error)>0) {
                $token=md5(date('Y-d-m H:i:s u').md5(mt_rand()));
                $_SESSION['registerToken'] = $token;
                $template_file = 'page_register.tpl';
            } else {
                // personal Salt and activation md5
                $userSalt=md5(date('Y-d-m H:i:s u').md5(mt_rand()));
                $activeHash=uniqid();
				include(EASYWIDIR . '/stuff/keyphrasefile.php');

                // insert data
                $query = $sql->prepare("INSERT INTO `userdata` (`accounttype`,`active`,`mail`,`token`,`creationTime`,`updateTime`,`salutation`,`country`,`name`,`vname`,`birthday`,`phone`,`fax`,`handy`,`city`,`cityn`,`street`,`streetn`) VALUES ('u','R',?,?,NOW(),NOW(),?,?,?,?,?,?,?,?,?,?,?,?)");
                $query->execute(array($mail,$activeHash,$salutation = $ui->id('salutation',1, 'post'),$ui->st('country', 'post'),$name,$vname,$bday,$ui->phone('phone',50, 'post'),$ui->phone('fax',50, 'post'),$ui->phone('handy',50, 'post'),$ui->names('city',50, 'post'),$ui->id('cityn',6, 'post'),$ui->names('street',50, 'post'),$ui->w('streetn',6, 'post')));
                $userID = $sql->lastInsertId();
                $cname = $rSA['prefix2'] . $userID;

                $newHash = passwordCreate($cname, $ui->password('password', 255, 'post'));

                if (is_array($newHash)) {
                    $query = $sql->prepare("UPDATE `userdata` SET `cname`=?,`security`=?,`salt`=? WHERE `id`=? LIMIT 1");
                    $query->execute(array($cname, $newHash['hash'], $newHash['salt'], $userID));

                } else {
                    $query = $sql->prepare("UPDATE `userdata` SET `cname`=?,`security`=? WHERE `id`=? LIMIT 1");
                    $query->execute(array($cname, $newHash, $userID));
                }

                // Setup default Group
                $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `grouptype`='u' AND `active`='Y' AND `defaultgroup`='Y' AND `resellerid`=0 LIMIT 1");
                $query->execute();
                $groupID = $query->fetchColumn();

                $query = $sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`,`resellerID`) VALUES (?,?,0)");
                $query->execute(array($userID,$groupID));

                // If is is in DB and mail could be send
                if ($query->rowCount() > 0) {
                    if ($registration == 'A') {
                        $template_file = $page_sprache->registerAdmin;
                    } else if ($registration == 'M') {
                        $template_file = $page_sprache->registerMailSend;

                        // send Mail
                        sendmail('emailregister',$userID,'',$page_data->pages['register']['link'].'activate/'.$activeHash.'/');
                    } else {
                        $_SESSION['userid'] = $userID;
                        $_SESSION['resellerid'] = 0;
                        $template_file = $page_sprache->registerAccountOK;
                    }
                } else {
                    $error[] = $page_sprache->registerErrorUnknown;
                    $token=md5(date('Y-d-m H:i:s u').md5(mt_rand()));
                    $_SESSION['registerToken'] = $token;
                    $template_file = 'page_register.tpl';
                }
            }
        } else if ($ui->escaped('email', 'post')) {
            $template_file = $page_sprache->registerErrorBot;
        } else {
            $token=md5(date('Y-d-m H:i:s u').md5(mt_rand()));
            $_SESSION['registerToken'] = $token;
            $template_file = 'page_register.tpl';
        }
    }
}