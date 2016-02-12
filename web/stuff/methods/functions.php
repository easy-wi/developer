<?php

/**
 * File: functions.php.
 * Author: Ulrich Block
 * Date: 03.10.12
 * Time: 17:09
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

if (!defined('EASYWIDIR')) {
    define('EASYWIDIR', '');
}

if (!function_exists('passwordgenerate')) {

    function passwordgenerate ($length) {
        $zeichen = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $anzahl = count($zeichen) - 1;
        $password = '';
        for($i = 1; $i <= $length; $i++){
            $wuerfeln = mt_rand(0, $anzahl);
            $password .= $zeichen[$wuerfeln];
        }
        return $password;
    }

    function passwordhash($username, $password, $salt=false){
        $passworda = str_split($password, (strlen($password) / 2) + 1);
        $usernamea = str_split($username, (strlen($username) / 2) + 1);
        return ($salt == false) ? hash('sha512', sha1($usernamea[0] . md5($passworda[0] . $usernamea[1]) . $passworda[1])): hash('sha512', sha1($usernamea[0] . md5($passworda[0] . $salt . $usernamea[1]) . $passworda[1]));
    }

    function createHash ($name, $pwd, $saltOne, $saltTwo = 'ZPZw$[pkJF!;SHdl', $iterate = 1000) {
        $pwdSplit = str_split($pwd,(strlen($pwd) / 2) + 1);
        $nameSplit = str_split($name, (strlen($name) / 2) + 1);
        $hash = '';

        if (!isset($nameSplit[1]) and strlen($nameSplit[0]) > 0) {
            $nameSplit[1] = $nameSplit[0];
        }

        if (!isset($pwdSplit[1]) and strlen($pwdSplit[0]) > 0) {
            $pwdSplit[1] = $pwdSplit[0];
        }

        if (isset($nameSplit[1]) and isset($pwdSplit[1])) {
            for ($i = 0;$i<=$iterate;$i++) {
                $hash = hash('sha512', $nameSplit[0] . $saltOne . $pwdSplit[0] . $hash . $nameSplit[1] . $saltTwo . $pwdSplit[1]);
            }

            return $hash;
        }

        return false;
    }

    function passwordCheck ($password, $storedHash, $username = '', $salt = '') {

        // Easy-WI uses the PHP hash API introduced with version 5.5. Fallbacks in place for older versions.

        global $aeskey;

        // First check if crypt works properly. With old PHP versions like Debian 6 with 5.3.3 we will run into an error
        if (crypt('password', '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG') == '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG') {

            // Return true in case the password is ok
            if (password_verify($password, $storedHash)) {
                return true;
            }

            // Password is correctly but stored in an old or insecure format. We need to hash it with a secure implementation.
            // Insecure implementations like md5 or sha1 are imported from other systems with the cloud.php job.
            if (preg_match('/^[a-z0-9]{32}+$/', $storedHash) and md5($password) == $storedHash) {
                return password_hash($password, PASSWORD_DEFAULT);
            } else if (preg_match('/^[a-z0-9]{40}+$/', $storedHash) and sha1($password) == $storedHash) {
                return password_hash($password, PASSWORD_DEFAULT);
            } else if (preg_match('/^[a-z0-9]{128}+$/', $storedHash) and createHash($username, $password, $salt, $aeskey) == $storedHash) {
                return password_hash($password, PASSWORD_DEFAULT);
            } else if (preg_match('/^[a-z0-9]{128}+$/', $storedHash) and passwordhash($username, $password) == $storedHash) {
                return password_hash($password, PASSWORD_DEFAULT);
            }

        // Fallback to sha512 since some Admins are either lazy or forced to stick to old PHP.
        } else {

            $newSalt = md5(mt_rand() . date('Y-m-d H:i:s:u'));

            if (createHash($username, $password, $salt, $aeskey) == $storedHash) {
                return true;
            } else if (preg_match('/^[a-z0-9]{32}+$/', $storedHash) and md5($password) == $storedHash) {
                return array('hash' => createHash($username, $password, $newSalt, $aeskey), 'salt' => $newSalt);
            } else if (preg_match('/^[a-z0-9]{40}+$/', $storedHash) and sha1($password) == $storedHash) {
                return array('hash' => createHash($username, $password, $newSalt, $aeskey), 'salt' => $newSalt);
            } else if (preg_match('/^[a-z0-9]{128}+$/', $storedHash) and passwordhash($username, $password) == $storedHash) {
                return createHash($username, $password, $salt, $aeskey);
            }
        }

        // Password Is Not Correct
        return false;
    }

    function passwordCreate ($username, $password) {

        global $aeskey;

        // First check if crypt works properly. Old PHP versions like Debian 6 with 5.3.3 will run into an error
        if (crypt('password', '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG') == '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG') {
            return password_hash($password, PASSWORD_DEFAULT);
        } else {
            $newSalt = md5(mt_rand() . strtotime('now'));
            return array('hash' => createHash($username, $password, $newSalt, $aeskey), 'salt' => $newSalt);
        }
    }

    function szrp ($value) {
        $szrm = array('ä' => 'ae','ö' => 'oe','ü' => 'ue','Ä' => 'Ae','Ö' => 'Oe','Ü' => 'Ue','ß' => 'ss','á' => 'a','à' => 'a','Á' => 'A','À' => 'A','é' => 'e','è' => 'e','É' => 'E','È' => 'E','ó' => 'o','ò' => 'o','Ó' => 'O','Ò' => 'O','ú' => 'u','ù' => 'u','Ú' => 'U','Ù' => 'U');
        return strtolower(preg_replace('/[^a-zA-Z0-9]{1}/', '-', strtr($value, $szrm)));
    }

    function removeDoubleSlashes ($value) {
        return preg_replace('/([^:])(\/{2,})/', '$1/', $value);
    }

    function redirect($value, $sendHTTP301 = false) {

        $value = removeDoubleSlashes($value);

        if ($value == 'login.php') {
            session_unset();
            session_destroy();
        }

        if ($sendHTTP301 == true) {
            header('HTTP/1.1 301 Moved Permanently');
        }

        header ('Location: ' . $value);
        die('Please allow redirection settings');
    }

    function listDirs ($dir) {

        $selectLanguages = array();

        if (is_dir($dir)){
            $dirs=scandir($dir);

            foreach ($dirs as $row) {
                if (small_letters_check($row,2)) {
                    $selectLanguages[] = $row;
                }
            }
        }

        return $selectLanguages;
    }

    function getlanguages ($value) {

        $selectLanguages=listDirs('languages/' . $value . '/');

        if (count($selectLanguages)<1) {
            $selectLanguages = listDirs('languages/default/');
        }
        if (count($selectLanguages)<1) {
            $selectLanguages = listDirs('languages/');
        }

        return $selectLanguages;
    }

    function cleanFsockOpenRequest ($string, $start, $stop) {

        while(substr($string, 0,1) != $start and strlen($string) > 0) {
            $string = substr($string,1);
        }

        while(substr($string, -1) != $stop and strlen($string) > 0) {
            $string = substr($string, 0, -1);
        }

        return $string;
    }

    function serverdata($type, $serverID, $aeskey) {

        global $sql;
        $serverdata = array();

        if ($type == 'root') {
            $query = $sql->prepare("SELECT `os`,`ip`,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `decryptedsteamAccount`,AES_DECRYPT(`steamPassword`,:aeskey) AS `decryptedsteamPassword`,`publickey`,`keyname`,`ftpport`,`notified`,`cores`,`hyperthreading`,`resellerid`,`install_paths` FROM `rserverdata` WHERE `id`=:serverID LIMIT 1");

        } else if ($type == 'virtualhost') {
            $query = $sql->prepare("SELECT `ip`,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,`publickey`,`keyname`,`notified`,`resellerid` FROM `virtualhosts` WHERE `id`=:serverID LIMIT 1");

        } else if ($type == 'dhcp') {
            $query = $sql->prepare("SELECT `ip`,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,`publickey`,`keyname`,`notified`,`resellerid` FROM `dhcpdata` WHERE `id`=:serverID LIMIT 1");

        } else {
            $query = $sql->prepare("SELECT `ip`,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,`publickey`,`keyname`,`cfgdir`,`notified`,`resellerid` FROM `eac` WHERE `resellerid`=:serverID LIMIT 1");
        }

        $query->execute(array(':serverID' => $serverID, ':aeskey' => $aeskey));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $cores = '';
            $hyperthreading = '';
            $steamAccount = '';
            $steamPassword = '';
            $installPaths = '';

            if ($type == 'root') {
                $ftpport = $row['ftpport'];
                $cores = $row['cores'];
                $hyperthreading = $row['hyperthreading'];
                $steamAccount = $row['decryptedsteamAccount'];
                $steamPassword = $row['decryptedsteamPassword'];
                $installPaths = $row['install_paths'];

            } else if ($type == 'eac') {

                $ftpport = $row['cfgdir'];

            } else {
                $ftpport = '';
            }

            $os = (isset($row['os'])) ? $row['os'] : '';

            $serverdata = array('os' => $os, 'ip' => $row['ip'], 'port' => $row['decryptedport'], 'user' => $row['decrypteduser'], 'pass' => $row['decryptedpass'], 'publickey' => $row['publickey'], 'keyname' => $row['keyname'], 'notified' => $row['notified'], 'resellerid' => $row['resellerid'], 'hyperthreading' => $hyperthreading,'cores' => $cores,'ftpport' => $ftpport,'steamAccount' => $steamAccount,'steamPassword' => $steamPassword, 'install_paths' => $installPaths);
        }

        return $serverdata;

    }

    function serverAmount($resellerid) {

        global $sql, $user_language;

        $query = $sql->prepare("SELECT `licence` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
        $query->execute();
        $json = @json_decode($query->fetchColumn());

        $query = $sql->prepare("SELECT  COUNT(g.`id`) AS `amount` FROM `gsswitch` g LEFT JOIN `userdata` u ON g.`userid`=u.`id` LEFT JOIN `userdata` r ON g.`resellerid`= r.`id` WHERE g.`active`='Y' AND u.`active`='Y' AND (r.`active`='Y' OR r.`active` IS NULL)");
        $query->execute();
        $gsCount = (int) $query->fetchColumn();

        $query = $sql->prepare("SELECT COUNT(v.`id`) AS `amount` FROM `virtualcontainer` v LEFT JOIN `userdata` u ON v.`userid`=u.`id` LEFT JOIN `userdata` r ON v.`resellerid`= r.`id` WHERE v.`active`='Y' AND u.`active`='Y' AND (r.`active`='Y' OR r.`active` IS NULL)");
        $query->execute();
        $vCount = (int) $query->fetchColumn();

        $query = $sql->prepare("SELECT COUNT(v.`id`) AS `amount` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` LEFT JOIN `userdata` u ON v.`userid`=u.`id` LEFT JOIN `userdata` r ON v.`resellerid`= r.`id` WHERE v.`active`='Y' AND m.`active`='Y' AND u.`active`='Y' AND (r.`active`='Y' OR r.`active` IS NULL)");
        $query->execute();
        $voCount = (int) $query->fetchColumn();

        $query = $sql->prepare("SELECT `dedicatedID` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userID`=u.`id` LEFT JOIN `userdata` r ON d.`resellerID`= r.`id` WHERE r.`active`!='N' AND u.`active`!='N' AND d.`active`!='N'");
        $query->execute();
        $dCount = (int) $query->fetchColumn();

        $count = $gsCount + $vCount + $voCount + $dCount;

        $sprache = getlanguagefile('licence', $user_language, $resellerid);
        $s = $sprache->unlimited;
        $mG = $s;
        $mVs = $s;
        $mVo = $s;
        $mD = $s;
        $lG = 10;
        $lVs = 10;
        $lVo = 10;
        $lD = 10;
        $left = $s;

        if ($resellerid != 0) {

            $query = $sql->prepare("SELECT `maxgserver`,`maxvserver`,`maxvoserver`,`maxdedis` FROM `resellerdata` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($resellerid));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $mG = $row['maxgserver'];
                $mVs = $row['maxvserver'];
                $mVo = $row['maxvoserver'];
                $mD = $row['maxdedis'];
            }

            $query = $sql->prepare("SELECT COUNT(g.`id`) AS `amount` FROM `gsswitch` g LEFT JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`resellerid`=? AND g.`active`='Y' AND u.`active`='Y'");
            $query->execute(array($resellerid));
            $gsCount = (int) $query->fetchColumn();

            $query = $sql->prepare("SELECT COUNT(v.`id`) AS `amount` FROM `virtualcontainer` v LEFT JOIN `userdata` u ON v.`userid`=u.`id` WHERE (v.`userid`=:resellerid OR v.`resellerid`=:resellerid) AND v.`active`='Y' AND u.`active`='Y'");
            $query->execute(array(':resellerid' => $resellerid));
            $vCount = (int) $query->fetchColumn();

            $query = $sql->prepare("SELECT COUNT(v.`id`) AS `amount` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` LEFT JOIN `userdata` u ON v.`userid`=u.`id` LEFT JOIN `userdata` r ON v.`resellerid`= r.`id` WHERE v.`resellerid`=? AND v.`active`='Y' AND m.`active`='Y' AND u.`active`='Y'");
            $query->execute(array($resellerid));
            $voCount = (int) $query->fetchColumn();

            $query = $sql->prepare("SELECT COUNT(`dedicatedID`) AS `amount` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userid`=u.`id` WHERE (d.`userID`=:resellerid OR d.`resellerID`=:resellerid) AND d.`active`!='N'");
            $query->execute(array(':resellerid' => $resellerid));
            $dCount = (int) $query->fetchColumn();
        }

        return array('left' => $left, 'count' => $count, 'gsCount' => $gsCount, 'vCount' => $vCount, 'voCount' => $voCount, 'dCount' => $dCount, 'mG' => $mG, 'mVs' => $mVs, 'mVo' => $mVo, 'mD' => $mD, 'lG' => $lG, 'lVs' => $lVs, 'lVo' => $lVo, 'lD' => $lD, 'p' => $json->p, 'b' => $json->b, 't' => $json->t, 'u' => $json->u, 'c' => $json->c, 'v' => $json->v);
    }

    function getusername($userid) {

        global $sql;

        $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($userid));
        $cname = ($query->rowCount() == 0) ? 'User deleted' : $query->fetchColumn();

        return $cname;
    }

    function rsellerpermisions($userid) {

        global $sql;
        $query = $sql->prepare("SELECT `userid` FROM `userpermissions` WHERE `userid`=? AND (`addvserver`='Y' OR `modvserver`='Y' OR `delvserver`='Y' OR `vserversettings`='Y' OR `vserverhost`='Y' OR `resellertemplates`='Y' OR `usevserver`='Y' OR `root`='Y' OR `traffic`='Y') LIMIT 1");
        $query->execute(array($userid));
        $colcount = $query->rowCount();

        if ($colcount == 0) {
            $query = $sql->prepare("SELECT g.`id` FROM `userdata_groups` u LEFT JOIN `usergroups` g ON u.`groupID`=g.`id` WHERE u.`userID`=? AND (`addvserver`='Y' OR `modvserver`='Y' OR `delvserver`='Y' OR `vserversettings`='Y' OR `vserverhost`='Y' OR `resellertemplates`='Y' OR `usevserver`='Y' OR `root`='Y' OR `traffic`='Y') LIMIT 1");
            $query->execute(array($userid));
            $colcount = $query->fetchAll(PDO::FETCH_ASSOC);
        }

        return $colcount;
    }

    function isanyadmin($userid) {

        global $sql;

        $query = $sql->prepare("SELECT `accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($userid));
        $accountType = $query->fetchColumn();

        return ($accountType == 'a' or $accountType == 'r') ?  true : false;
    }

    function isanyuser($userid) {

        global $sql;

        $query = $sql->prepare("SELECT `accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($userid));

        return ($query->fetchColumn() == 'u') ? true : false;
    }

    function language($user_id) {

        global $sql, $ui;

        if (!isset($_SESSION['language'])) {

            $query = $sql->prepare("SELECT `language` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($user_id));
            $language = $query->fetchColumn();

            if ($language == '') {
                $lang_detect = (isset($ui->server['HTTP_ACCEPT_LANGUAGE'])) ? small_letters_check(substr($ui->server['HTTP_ACCEPT_LANGUAGE'], 0, 2), 2) : 'uk';

                if (is_dir(EASYWIDIR . '/languages/' . $lang_detect)) {
                    $language = $lang_detect;

                } else {
                    $query = $sql->prepare("SELECT `language` FROM `settings` LIMIT 1");
                    $query->execute();
                    $language = $query->fetchColumn();
                }

            } else if (!is_dir(EASYWIDIR . '/languages/' . $language)) {
                $query = $sql->prepare("SELECT `language` FROM `settings` LIMIT 1");
                $query->execute();
                $language = $query->fetchColumn();
            }

            $query = $sql->prepare("UPDATE `userdata` SET `language`=? WHERE `id`=? LIMIT 1");
            $query->execute(array($language, $user_id));
            $_SESSION['language'] = $language;

        } else {
            $language = $_SESSION['language'];
        }

        return $language;
    }

    function getlanguagefile($filename, $user_language, $reseller_id) {

        global $sql;

        $sprache = new stdClass;
        $query = $sql->prepare("SELECT `language`,`template` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $default_language = $row['language'];
            $template = $row['template'];

            if (file_exists(EASYWIDIR . '/languages/' . $template. '/' . $user_language. '/' . $filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR . '/languages/' . $template. '/' . $user_language. '/' . $filename.'.xml');

            } else if (file_exists(EASYWIDIR . '/languages/' . $template. '/' . $default_language. '/' . $filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR . '/languages/' . $template. '/' . $default_language. '/' . $filename.'.xml');

            } else if (file_exists(EASYWIDIR . '/languages/default/'.$user_language. '/' . $filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR . '/languages/default/'.$user_language. '/' . $filename.'.xml');

            } else if (file_exists(EASYWIDIR . '/languages/default/'.$default_language. '/' . $filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR . '/languages/default/'.$default_language. '/' . $filename.'.xml');

            } else if (file_exists(EASYWIDIR . '/languages/' . $user_language. '/' . $filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR . '/languages/' . $user_language. '/' . $filename.'.xml');

            } else if (file_exists(EASYWIDIR . '/languages/' . $default_language. '/' . $filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR . '/languages/' . $default_language. '/' . $filename.'.xml');
            }
        }
        return $sprache;
    }

    function ipstoarray($value) {

        $ips_array = array();

        if (isips($value)) {
            foreach (explode("\r\n", $value) as $exip) {

                if (isips($exip)) {
                    $exploded_ip = explode('.', $exip);

                    if (isset($exploded_ip[3]) and is_numeric($exploded_ip[3])){
                        $ips_array[] = $exip;

                    } else if (isset($exploded_ip[3])) {
                        $range = explode('/', $exploded_ip[3]);
                        $i = $range[0];

                        while (isset($range[1]) and $i <= $range[1]) {
                            $ips_array[] = $exploded_ip[0] . '.' . $exploded_ip[1] . '.' . $exploded_ip[2] . '.' . $i;
                            $i++;
                        }
                    }
                }
            }
        }

        natsort($ips_array);

        return $ips_array;
    }

    function freeips($value) {

        global $sql;

        $userips = array();

        if ($value == 0) {

            $query = $sql->prepare("SELECT `ip` FROM `rootsIP4` WHERE `resellerID`=0");
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $userips[] = $row['ip'];
            }

            $query = $sql->prepare("SELECT `ip`,`ips` FROM `virtualcontainer`");
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $key = array_search($row['ip'], $userips);
                if (false !== $key) {
                    unset($userips[$key]);
                }

                foreach (ipstoarray($row['ips']) as $usedip) {
                    $key = array_search($usedip, $userips);
                    if (false !== $key) {
                        unset($userips[$key]);
                    }
                }
            }

            $query = $sql->prepare("SELECT `ip`,`ips` FROM `rootsDedicated`");
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $key = array_search($row['ip'], $userips);
                if (false !== $key) {
                    unset($userips[$key]);
                }

                foreach (ipstoarray($row['ips']) as $usedip) {
                    $key = array_search($usedip, $userips);
                    if (false !== $key) {
                        unset($userips[$key]);
                    }
                }
            }

        } else {

            $query = $sql->prepare("SELECT `resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($value));
            $resellerid = $query->fetchColumn();


            $query = $sql->prepare("SELECT `ip` FROM `rootsIP4` WHERE `resellerID`=?");
            $query->execute(array($resellerid));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $userips[] = $row['ip'];
            }

            $query = $sql->prepare("SELECT `ip`,`ips` FROM `virtualcontainer` WHERE `resellerid`=?");
            $query->execute(array($resellerid));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $key = array_search($row['ip'], $userips);
                if (false !== $key) {
                    unset($userips[$key]);
                }

                foreach (ipstoarray($row['ips']) as $usedip) {
                    $key = array_search($usedip, $userips);
                    if (false !== $key) {
                        unset($userips[$key]);
                    }
                }
            }

            $query = $sql->prepare("SELECT `ip`,`ips` FROM `rootsDedicated` WHERE `resellerid`=?");
            $query->execute(array($resellerid));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $key = array_search($row['ip'], $userips);
                if (false !== $key) {
                    unset($userips[$key]);
                }

                foreach (ipstoarray($row['ips']) as $usedip) {
                    $key = array_search($usedip, $userips);
                    if (false !== $key) {
                        unset($userips[$key]);
                    }
                }
            }

            $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE accounttype='r' AND `resellerid`=:id AND `id`!=:id");
            $query2 = $sql->prepare("SELECT `ip` FROM `rootsIP4` WHERE `resellerID`=? AND `ownerID`!=`resellerID`");
            $query->execute(array(':id' => $resellerid));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $query2->execute(array($row['id']));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $key = array_search($row2['ip'], $userips);
                    if (false !== $key) {
                        unset($userips[$key]);
                    }
                }
            }
        }

        $userips = array_unique($userips);
        natsort($userips);

        return $userips;
    }

    function webhostdomain($resellerid) {

        global $sql;

        $paneldomain = '';

        $query = $sql->prepare("SELECT `paneldomain` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($resellerid));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $paneldomain = $row['paneldomain'];
        }

        if (!filter_var($paneldomain, FILTER_VALIDATE_URL)) {
            $query = $sql->prepare("SELECT `paneldomain` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $paneldomain = $row['paneldomain'];
            }
        }

        return $paneldomain;
    }

    function sendmail($template, $userid, $server, $shorten, $connectInfo = array()) {

        global $sql, $rSA;

        if (!isset($aeskey)) {
            include(EASYWIDIR . '/stuff/keyphrasefile.php');
        }
        if (!class_exists('PHPMailer')) {
            include(EASYWIDIR . '/third_party/phpmailer/PHPMailerAutoload.php');
        }

        if ($template == 'emailnewticket') {
            $writerid = $shorten[1];
            $shorten = $shorten[0];
        }

        $userLanguage = $rSA['language'];
        $resellerLanguage = $rSA['language'];

        $query = $sql->prepare("SELECT `mail`,`vname`,`name`,`cname`,`language`,`resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($userid));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $usermail = $row['mail'];
            $username = $row['vname'] . '  ' . $row['name'];

            if ($username == ' ' or $username == '') {
                $username = $row['cname'];
            }

            $userLanguage = $row['language'];
            $resellerid = $row['resellerid'];

        }

        if ($template == 'emailnewticket' and isset($writerid)) {

            $query = $sql->prepare("SELECT `vname`,`name`,`cname` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($writerid));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $username = ($row['vname'] . ' ' . $row['name'] == ' ') ? $row['cname'] : $row['vname'] . ' ' . $row['name'];
            }
        }

        if (!isset($resellerid) or $resellerid == $userid) {

            $resellersid = 0;

            if (!isset($resellerid)) {
                $resellerid = 0;
            }

        } else {
            $resellersid = $resellerid;
        }

        $query = $sql->prepare("SELECT `email_setting_value` FROM `settings_email` WHERE `reseller_id`=? AND `email_setting_name`=? LIMIT 1");
        $query->execute(array($resellersid, 'email_settings_type'));
        $email_settings_type = $query->fetchColumn();

        if ($email_settings_type and $email_settings_type != 'N') {

            $query->execute(array($resellersid, 'emailregards'));
            $emailregards = nl2br($query->fetchColumn());

            $query->execute(array($resellersid, 'emailfooter'));
            $emailfooter = nl2br($query->fetchColumn());

            $query->execute(array($resellersid, 'email'));
            $resellersmail = $query->fetchColumn();

            $query->execute(array($resellersid, 'email'));
            $resellermail = $query->fetchColumn();

            $query = $sql->prepare("SELECT `timezone`,`language` FROM `settings` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($resellerid));
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $resellerLanguage = $row['language'];
                $resellerstimezone = $row['timezone'];
            }

            if (!isset($resellerstimezone) or $resellerstimezone == null) {
                $resellerstimezone = 0;
            }

            $maildate = date('Y-m-d H:i:s', strtotime("$resellerstimezone hour"));

            if ($template == 'contact') {

                $startMail = true;

                $topic = 'You\'ve been contacted by ' . $userid .'.';

                $mailBody = $server;

                $usermail = $resellermail;

            } else {

                if ($resellerid == $userid) {
                    $resellermail = $resellersmail;
                    $lookupID = $resellersid;
                } else {
                    $lookupID = $resellerid;
                }

                $query = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='em' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($userLanguage, $template, $lookupID));
                $sprache = @simplexml_load_string(strtr($query->fetchColumn(), array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES))));

                if (!$sprache) {
                    $query->execute(array($resellerLanguage, $template, $lookupID));
                    $sprache = @simplexml_load_string(strtr($query->fetchColumn(), array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES))));
                }

                if (!$sprache) {
                    $query = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='em' AND `transID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($template, $lookupID));
                    $sprache = @simplexml_load_string(strtr($query->fetchColumn(), array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES))));
                }

                $query = $sql->prepare("SELECT `email_setting_value` FROM `settings_email` WHERE `reseller_id`=? AND `email_setting_name`=? LIMIT 1");
                $query->execute(array($lookupID, $template));
                $mailtext = $query->fetchColumn();

                $keys = array('%server%', '%username%', '%date%', '%shorten%', '%emailregards%', '%emailfooter%', '%ip%', '%port%', '%port2%', '%port3%', '%port4%', '%port5%', '%ports%');
                $replacements = array($server, $username, $maildate, $shorten, $emailregards, $emailfooter);

                if (is_array($connectInfo) and count($connectInfo) > 0 and isset($connectInfo['ip'])) {

                    $replacements[] = $connectInfo['ip'];

                    $ports = array();

                    if ((isset($connectInfo['port']))) {

                        $ports[] = $connectInfo['port'];

                        $replacements[] = $connectInfo['port'];

                    } else {
                        $replacements[] = '';
                    }

                    for ($i = 2; $i < 6; $i++) {

                        if (isset($connectInfo["port{$i}"])) {

                            $ports[] = $connectInfo["port{$i}"];

                            $replacements[] = $connectInfo["port{$i}"];

                        } else {
                            $replacements[] = '';
                        }
                    }

                    $replacements[] = implode(', ', $ports);

                } else {
                    for ($i = 0; $i < 7; $i++) {
                        $replacements[] = '';
                    }
                }

                if ($sprache) {

                    $topic = $sprache->topic;

                    $sprache = (array) $sprache;

                    foreach ($sprache as $key => $value) {
                        if ($key != 'server' and $key != 'title' and $key != 'username' and $key != 'shorten' and $key != 'date' and $key != 'emailregards' and $key != 'emailfooter') {

                            if ($template == 'emailnewticket' and $key == 'topic') {
                                $value = $sprache['topic'] . ' #' . $shorten;
                                $topic = $value;
                            }

                            $keys[] = '%' . $key . '%';
                            $replacements[] = htmlentities($value, null, 'UTF-8');
                        }
                    }
                }

                $mailBody = str_replace($keys, $replacements, $mailtext);

                if (isset($usermail) and $usermail != 'ts3@import.mail' and ismail($usermail)) {
                    $startMail = true;
                }

            }

            if (isset($startMail) and isset($topic)) {

                $mail = new PHPMailer();
                $mail->CharSet = 'UTF-8';

                $mail->setFrom($resellermail);

                $mail->addAddress($usermail);

                $mail->Subject = $topic;

                $mail->msgHTML($mailBody);

                if ($email_settings_type == 'S') {

                    $mail->isSMTP();

                    $query = $sql->prepare("SELECT `email_setting_value` FROM `settings_email` WHERE `reseller_id`=? AND `email_setting_name`=? LIMIT 1");

                    $query->execute(array($resellersid, 'email_settings_host'));
                    $mail->Host = $query->fetchColumn();

                    $query->execute(array($resellersid, 'email_settings_port'));
                    $mail->Port = $query->fetchColumn();

                    $query->execute(array($resellersid, 'email_settings_ssl'));
                    $email_settings_ssl = $query->fetchColumn();

                    if ($email_settings_ssl == 'T') {
                        $mail->SMTPSecure = 'tls';
                    } else if ($email_settings_ssl == 'S') {
                        $mail->SMTPSecure = 'ssl';
                    }

                    $mail->SMTPAuth = true;

                    $query->execute(array($resellersid, 'email_settings_user'));
                    $mail->Username = $query->fetchColumn();

                    $query->execute(array($resellersid, 'email_settings_password'));
                    $mail->Password = $query->fetchColumn();
                }

                if ($mail->send()) {
                    $query = $sql->prepare("INSERT INTO `mail_log` (`uid`,`topic`,`date`,`resellerid`) VALUES (?,?,NOW(),?)");

                    if ($resellerid == $userid) {
                        $query->execute(array($userid, $topic, $resellersid));

                    } else {
                        $query->execute(array($userid, $topic, $resellerid));
                    }

                    return true;
                }
            }

            return false;
        }

        return true;
    }

    function IncludeTemplate($use, $file, $location = 'admin') {

        if (is_file(EASYWIDIR . '/template/' . $use. '/' . $location. '/' . $file) and preg_match('/^(.*)\.[\w]{1,}$/', $file)) {
            return EASYWIDIR . '/template/' . $use. '/' . $location. '/' . $file;

        } else if (is_file(EASYWIDIR . '/template/' . $use. '/' . $file) and preg_match('/^(.*)\.[\w]{1,}$/', $file)) {
            return EASYWIDIR . '/template/' . $use. '/' . $file;

        } else if (is_file(EASYWIDIR . '/template/default/' . $location. '/' . $file) and preg_match('/^(.*)\.[\w]{1,}$/', $file)) {
            return EASYWIDIR . '/template/default/' . $location. '/' . $file;

        } else if (is_file(EASYWIDIR . '/template/default/'.$file) and preg_match('/^(.*)\.[\w]{1,}$/', $file)) {
            return EASYWIDIR . '/template/default/'.$file;

        } else if (preg_match('/^(.*)\.[\w]{1,}$/', $file)) {
            return EASYWIDIR . '/template/' . $file;
        }
        return false;
    }

    function returnButton ($templateToUse, $template, $what, $do, $id, $description = '') {
        ob_start();
        include(IncludeTemplate($templateToUse, $template, 'ajax'));
        return ob_get_clean();
    }

    function User_Permissions($id) {

        global $sql;

        $pa = array('defaultgroup' => false, 'active' => false, 'root' => false, 'miniroot' => false, 'settings' => false, 'log' => false, 'ipBans' => false, 'updateEW' => false, 'feeds' => false, 'jobs' => false, 'apiSettings' => false, 'cms_settings' => false, 'cms_pages' => false, 'cms_news' => false, 'cms_comments' => false, 'mysql_settings' => false, 'mysql' => false, 'user' => false, 'user_users' => false, 'userGroups' => false, 'userPassword' => false, 'roots' => false, 'masterServer' => false, 'gserver' => false, 'eac' => false, 'gimages' => false, 'addons' => false, 'restart' => false, 'gsResetting' => false, 'modfastdl' => false, 'fastdl' => false, 'useraddons' => false, 'usersettings' => false, 'ftpaccess' => false, 'tickets' => false, 'usertickets' => false, 'addvserver' => false, 'modvserver' => false, 'delvserver' => false, 'usevserver' => false, 'vserversettings' => false, 'dhcpServer' => false, 'pxeServer' => false, 'dedicatedServer' => false, 'resellertemplates' => false, 'vserverhost' => false, 'lendserver' => false, 'lendserverSettings' => false, 'voicemasterserver' => false, 'voiceserver' => false, 'voiceserverStats' => false, 'voiceserverSettings' => false, 'ftpbackup' => false, 'traffic' => false, 'trafficsettings' => false);

        $query = $sql->prepare("SELECT `accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        $accounttype = $query->fetchColumn();

        $query = $sql->prepare("SELECT g.* FROM `userdata_groups` a INNER JOIN `usergroups` g ON g.`id`=a.`groupID` WHERE a.`userID`=?");
        $query->execute(array($id));
        $array = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($array as $row) {

            if (($accounttype == 'u' and $row['miniroot'] == 'Y')) {
                foreach ($row as $key => $value) {
                    $pa[$key] = true;
                }

            } else if (($accounttype != 'u' and $row['root'] == 'Y')) {
                foreach ($row as $key => $value) {
                    $pa[$key] = true;
                }

            } else {
                foreach ($row as $key => $value) {
                    if ((isset($pa[$key]) and $pa[$key] === false) or !isset($pa[$key])) {
                        $pa[$key] = ($value == 'Y') ? true : false;
                    }
                }
            }
        }
        return $pa;
    }

    function array_value_exists($key, $value, $array) {
        return (array_key_exists($key, $array) and $array[$key] == $value)  ? true : false;
    }

    function updateJobs($localID, $resellerID, $jobPending = 'Y') {

        global $sql;

        $update = $sql->prepare("UPDATE `gsswitch` SET `jobPending`=? WHERE `userid`=? AND `resellerid`=?");
        $update->execute(array($jobPending, $localID, $resellerID));

        $update = $sql->prepare("UPDATE `mysql_external_dbs` SET `jobPending`=? WHERE `uid`=? AND `resellerid`=?");
        $update->execute(array($jobPending, $localID, $resellerID));

        $update = $sql->prepare("UPDATE `virtualcontainer` SET `jobPending`=? WHERE `userid`=? AND `resellerid`=?");
        $update->execute(array($jobPending, $localID, $resellerID));

        $update = $sql->prepare("UPDATE `voice_server` SET `jobPending`=? WHERE `userid`=? AND `resellerid`=?");
        $update->execute(array($jobPending, $localID, $resellerID));

        $update = $sql->prepare("UPDATE `voice_dns` SET `jobPending`=? WHERE `userID`=? AND `resellerID`=?");
        $update->execute(array($jobPending, $localID, $resellerID));
    }

    function updateStates($action, $type=null) {

        global $sql;

        $typeQuery=($type != null) ? " AND `type`='${type}'" : '';

        $query = $sql->prepare("SELECT `type`,`affectedID` FROM `jobs` WHERE (`status` IS NULL OR `status`=1) AND `action`=? $typeQuery GROUP BY `type`,`affectedID`");
        $query->execute(array($action));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $query2 = $sql->prepare("SELECT `jobID` FROM `jobs` WHERE `type`=? AND `affectedID`=? AND `action`=? $typeQuery ORDER BY `jobID` DESC LIMIT 1");
            $query2->execute(array($row['type'], $row['affectedID'], $action));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                if ($type==null) {
                    $update = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE (`status` IS NULL OR `status`=1) AND `type`=? AND `affectedID`=? AND `jobID`!=?");
                    $update->execute(array($row['type'], $row['affectedID'], $row2['jobID']));

                } else {
                    $update = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE (`status` IS NULL OR `status`=1) AND `userID`=? AND `jobID`!=?");
                    $update->execute(array($row['affectedID'], $row2['jobID']));
                }
            }
        }
    }

    function CopyAdminTable ($tablename, $id, $reseller_id, $limit, $where='') {

        global $sql;

        $query = $sql->prepare("SELECT * FROM `$tablename` WHERE `resellerid`=? " . $where . " " .$limit);
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $keys = array();
            $questionmarks = array();
            $intos = array();

            foreach ($row as $key=>$value) {
                if ($key != 'id' and $key != 'resellerid'){
                    $keys[]="`".$key."`";
                    $questionmarks[] = '?';
                    $intos[] = $value;
                }
            }

            $keys[] = "`resellerid`";
            $intos[] = $id;
            $questionmarks[] = '?';
            $into = 'INSERT INTO `' . $tablename . '` (' . implode(',', $keys) . ') VALUES (' . implode(',', $questionmarks) . ')';
            $query2 = $sql->prepare("$into");
            $query2->execute($intos);
        }
    }

    function dataExist ($value, $array) {
        return (isset($array[$value]) and isset($array[$array[$value]]) and !in_array($array[$array[$value]], array(false, null,''))) ? true : false;
    }

    function webhostRequest ($domain, $useragent, $file, $postParams = '', $port = 80) {

        $domain = str_replace(array('https://', 'http://'),'', $domain);

        if (isdomain($domain)) {
            $fp = @fsockopen($domain, $port, $errno, $errstr, 10);
        } else {
            $errstr = $domain . ' is no domain';
        }

        if (isset($fp) and $fp) {

            if (is_array($postParams) and count($postParams) > 0) {
                $postData = '';
                $i = 0;

                foreach ($postParams as $key=>$value) {
                    if ($i == 0){
                        $postData .= $key . '=' . $value;
                    } else {
                        $postData .= '&' . $key . '=' . $value;
                    }
                    $i++;
                }

                $send = "POST /${file} HTTP/1.1\r\n";

            } else {

                if (strlen($file) == 0) {
                    $file = '/';
                }
                $send = "GET ${file} HTTP/1.1\r\n";
            }

            $send .= "Host: ${domain}\r\n";
            $send .= "User-Agent: ${useragent}\r\n";
            $send .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";

            if (isset($postData) and is_array($postParams) and count($postParams) > 0) {
                $send .= "Content-Length: " . strlen($postData) . "\r\n";
            }
            $send .= "Connection: Close\r\n\r\n";

            if (isset($postData) and is_array($postParams) and count($postParams)>0) {
                $send .= $postData;
            }

            fwrite($fp, $send);

            $buffer = '';
            while (!feof($fp)) {
                $buffer .= fgets($fp, 4096);
            }

            fclose($fp);

            $ex = explode("\r\n\r\n", $buffer);

            if (strpos($ex[0], '404') !== false) {
                return 'file not found: ' . $domain . '/' . $file;

            } else if (isset($ex[1])) {
                return $ex[1];

            } else {
                $errstr = 'Error: no response. Header is: ' . $ex[0];
            }
        }
        return 'Error: Could not connect to host ' . $domain . ' and port ' . $port . ' (' . $errstr . ')';
    }

    function checkPorts ($send, $used) {

        foreach ($send as $port) {
            if (!port($port) or in_array($port, $used)) {
                return false;
            }
        }

        return true;
    }

    function usedPorts ($ips) {

        global $sql;

        if (!is_array($ips)) {
            $ips = array($ips);
        }

        $portsArray = array();

        foreach ($ips as $serverIP) {

            $ports = array();

            $query = $sql->prepare("SELECT `port`,`port2`,`port3`,`port4`,`port5` FROM `gsswitch` WHERE `serverip`=? ORDER BY `port`");
            $query->execute(array($serverIP));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                if (port($row['port'])){
                    $ports[] = $row['port'];
                }
                if (port($row['port2'])){
                    $ports[] = $row['port2'];
                }
                if (port($row['port3'])){
                    $ports[] = $row['port3'];
                }
                if (port($row['port4'])){
                    $ports[] = $row['port4'];
                }
                if (port($row['port5'])){
                    $ports[] = $row['port5'];
                }
            }

            $query = $sql->prepare("SELECT `port` FROM `voice_server` WHERE `ip`=?");
            $query->execute(array($serverIP));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                if (port($row['port'])){
                    $ports[] = $row['port'];
                }
            }

            $ports = array_unique($ports);
            asort($ports);

            $portsArray[count($ports)] = array('ip' => $serverIP, 'ports' => $ports);
        }

        $bestIP = current($portsArray);

        return array('ip' => $bestIP['ip'], 'ports' => $bestIP['ports']);
    }

    function array2xml($array, $xml){

        foreach($array as $key => $value){

            if (is_numeric($key)) {
                $key = 'key' . $key;
            }

            if (is_array($value)){
                array2xml($value, $xml->addChild($key));

            } else {
                $xml->$key = $value;
            }
        }
        return $xml->asXML();
    }

    function yesNo ($check) {

        global $ui;

        if ($ui->active($check, 'post') == 'Y') {
            return 'Y';
        }
        return 'N';
    }

    function returnPlainArray ($arr, $key) {
        $return = array();

        if (is_array($arr) and !is_array($key)) {
            foreach ($arr as $v) {
                $return[] = $v[$key];
            }
        }

        return $return;
    }

    function licenceRequest($return = false) {

        global $sql, $ui;

        $licencecode = webhostRequest('l.easy-wi.com', $ui->server['HTTP_HOST'], '/licence.php', null, 80);
        $licencecode = cleanFsockOpenRequest($licencecode, '{', '}');
        $json = @json_decode($licencecode);

        if ($json and isset($json->v)) {
            $licencecode = array();

            foreach($json as $k => $v) {
                $licencecode[$k] = $v;
            }

            $licencecode['lt'] = time();
            $licencecode = json_encode($licencecode);

            $query2 = $sql->prepare("UPDATE `settings` SET `licence`=?,`version`=?,`releasenotesDE`=?,`releasenotesEN`=? WHERE `resellerid`=0 LIMIT 1");
            $query2->execute(array($licencecode, $json->v, $json->de, $json->en));
        }
        return ($return == true) ? $licencecode : false;
    }

    function token ($check = false) {

        global $ui, $_SESSION;

        if ($check == false) {
            $token = md5(mt_rand());
            $tokenLifeTime = '+40 minutes';

            if ($ui->id('id', 10, 'get') and $ui->smallletters('d', 10, 'get')) {
                $_SESSION[$ui->smallletters('w', 10, 'get')][$ui->smallletters('d', 10, 'get')][$ui->id('id', 10, 'get')] = array('t' => $token,'d' => strtotime($tokenLifeTime));

            } else if (!$ui->id('id', 10, 'get') and $ui->smallletters('d', 10, 'get')) {
                $_SESSION[$ui->smallletters('w', 10, 'get')][$ui->smallletters('d', 10, 'get')] = array('t' => $token,'d' => strtotime($tokenLifeTime));

            } else if ($ui->id('id', 10, 'get') and !$ui->smallletters('d', 10, 'get')) {
                $_SESSION[$ui->smallletters('w', 10, 'get')][$ui->id('id', 10, 'get')] = array('t' => $token,'d' => strtotime($tokenLifeTime));

            } else {
                $_SESSION[$ui->smallletters('w', 10, 'get')] = array('t' => $token,'d' => strtotime($tokenLifeTime));
            }

            return $token;

        } else {

            if (isset($_SESSION[$ui->smallletters('w', 10, 'get')][$ui->smallletters('d', 10, 'get')][$ui->id('id', 10, 'get')]['t']) and $_SESSION[$ui->smallletters('w', 10, 'get')][$ui->smallletters('d', 10, 'get')][$ui->id('id', 10, 'get')]['t'] == $ui->w('token', 32, 'post') and $_SESSION[$ui->smallletters('w', 10, 'get')][$ui->smallletters('d', 10, 'get')][$ui->id('id', 10, 'get')]['d'] >= strtotime('now')) {
                deleteOldToken($ui->smallletters('w', 10, 'get'), $ui->smallletters('d', 10, 'get'), $ui->id('id', 10, 'get'));
                return true;

            } else if (isset($_SESSION[$ui->smallletters('w', 10, 'get')][$ui->smallletters('d', 10, 'get')]['t']) and $_SESSION[$ui->smallletters('w', 10, 'get')][$ui->smallletters('d', 10, 'get')]['t'] == $ui->w('token', 32, 'post') and $_SESSION[$ui->smallletters('w', 10, 'get')][$ui->smallletters('d', 10, 'get')]['d'] >= strtotime('now')) {
                deleteOldToken($ui->smallletters('w', 10, 'get'), $ui->smallletters('d', 10, 'get'));
                return true;

            } else if (isset($_SESSION[$ui->smallletters('w', 10, 'get')][$ui->id('id', 10, 'get')]['t']) and $_SESSION[$ui->smallletters('w', 10, 'get')][$ui->id('id', 10, 'get')]['t'] == $ui->w('token', 32, 'post') and $_SESSION[$ui->smallletters('w', 10, 'get')][$ui->id('id', 10, 'get')]['d'] >= strtotime('now')) {
                deleteOldToken($ui->smallletters('w', 10, 'get'),'', $ui->id('id', 10, 'get'));
                return true;

            } else if (isset($_SESSION[$ui->smallletters('w', 10, 'get')]['t']) and $_SESSION[$ui->smallletters('w', 10, 'get')]['t'] == $ui->w('token', 32, 'post') and $_SESSION[$ui->smallletters('w', 10, 'get')]['d'] >= strtotime('now')) {
                deleteOldToken($ui->smallletters('w', 10, 'get'));
                return true;
            }

            deleteOldToken();

            return false;
        }
    }

    function deleteOldToken ($w = '', $d = '', $id = '') {

        global $_SESSION;

        if ($w != 'sID') {
            if ($id != '' and $d != '') {
                unset($_SESSION[$w][$d][$id]);

            } else if ($id == '' and $d != '') {
                unset($_SESSION[$w][$d]);

            } else if ($id != '' and $d == '') {
                unset($_SESSION[$w][$id]);

            } else if ($id == '' and $d == '') {
                unset($_SESSION[$w]);
            }
        }

        foreach ($_SESSION as $k => $v) {

            if (wpreg_check($k, 4) and $k != 'sID' and ((isset($_SESSION[$k]['t']) and $_SESSION[$k]['d'] < strtotime('now')) or (is_array($_SESSION[$k]) and count($_SESSION[$k]) == 0))) {
                unset($_SESSION[$k]);

            } else if (wpreg_check($k, 4) and is_array($_SESSION[$k]) and count($_SESSION[$k]) > 0) {

                foreach ($_SESSION[$k] as $k2=>$v2) {
                    if (wpreg_check($k2, 4) and ((isset($_SESSION[$k][$k2]['t']) and $_SESSION[$k][$k2]['d'] < strtotime('now')) or (is_array($_SESSION[$k][$k2]) and count($_SESSION[$k][$k2]) == 0))) {
                        unset($_SESSION[$k][$k2]);

                    } else if (wpreg_check($k2, 4) and is_array($_SESSION[$k][$k2]) and count($_SESSION[$k][$k2]) > 0) {
                        foreach ($_SESSION[$k][$k2] as $k3 => $v3) {

                            if (isid($k3, 4) and ((isset($_SESSION[$k][$k2][$k3]['t']) and $_SESSION[$k][$k2][$k3]['d'] < strtotime('now')) or (is_array($_SESSION[$k][$k2][$k3]) and count($_SESSION[$k][$k2][$k3]) == 0))) {
                                unset($_SESSION[$k][$k2][$k3]);
                            }
                        }
                    }
                }
            }
        }
    }

    function customColumns($item, $id = 0, $action = false, $api = false) {

        global $sql, $user_language, $default_language;

        $return = array();

        if ($id !== null) {

            $query = $sql->prepare("SELECT * FROM `custom_columns_settings` WHERE `item`=? AND `active`='Y'");
            $query->execute(array($item));

            if ($action == false) {
                $query2 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='cc' AND `transID`=? AND `lang`=? LIMIT 1");
                $query3 = $sql->prepare("SELECT `var` FROM `custom_columns` WHERE `customID`=? AND `itemID`=? LIMIT 1");
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                    $text = '';

                    $query2->execute(array($row['customID'], $user_language));
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                        $text = $row2['text'];
                    }

                    if (empty($text)) {

                        $query2->execute(array($row['customID'], $default_language));
                        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                            $text = $row2['text'];
                        }
                    }

                    $type = ($row['type'] == 'I') ? 'number' : 'text';
                    $query3->execute(array($row['customID'], $id));
                    $value = ($id == 0) ? '' : $query3->fetchColumn();

                   $return[] = array('customID' => $row['customID'], 'menu' => $text, 'name' => $row['name'], 'length' => $row['length'], 'type' => $row['type'], 'input' => "<input id='inputCustom-${row['customID']}' type='${type}' name='${row['name']}' maxlength='${row['length']}' value='${value}' >", 'value' => $value);
                }

            } else if ($action == 'save') {

                $return = 0;

                $query2 = $sql->prepare("INSERT INTO `custom_columns` (`customID`,`itemID`,`var`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `var`=VALUES(`var`)");

                if ($api == false) {

                    global $ui;

                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                        $var = '';

                        if ($row['type'] == 'I' and $ui->id($row['name'], $row['length'], 'post')) {
                            $var = $ui->id($row['name'], $row['length'], 'post');
                        } else if ($ui->names($row['name'], $row['length'], 'post')) {
                            $var = $ui->names($row['name'], $row['length'], 'post');
                        }

                        $query2->execute(array($row['customID'], $id, $var));

                        $return += $query2->rowCount();
                    }

                } else {

                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                        $var = '';

                        if (isset($api[$row['name']])) {

                            if ($row['type'] == 'I') {
                                $var = isid($api[$row['name']], $row['length']);
                            } else if (names($api[$row['name']], $row['length'])) {
                                $var = names($api[$row['name']], $row['length']);
                            }

                            $query2->execute(array($row['customID'], $id, $var));

                            $return += $query2->rowCount();
                        }
                    }
                }

            } else if ($action == 'del') {

                $return = 0;

                $query2 = $sql->prepare("DELETE FROM `custom_columns` WHERE `customID`=? AND `itemID`=? LIMIT 1");

                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                    $query2->execute(array($row['customID'], $id));

                    $return += $query2->rowCount();
                }
            }

        }

        return $return;
    }

    function workAroundForValveChaos ($appID, $shorten, $toApi = true) {

        // Server to client ID mapping
        if ($toApi == true) {
            if ($appID == 90) {

                $mapping = array('cstrike' => 10, 'czero' => 80, 'dmc' => 40, 'dod' => 30, 'gearbox' => 50, 'ricochet' => 60, 'tfc' => 20);

                if (isset($mapping[$shorten])) {
                    return $mapping[$shorten];
                }
            } else {

                $mapping = array(510 => 500, 740 => 730, 4020 => 4000, 4940 => 4920, 17505 => 17500, 17510 => 17515, 17570 => 17575, 111710 => 17710, 215350 => 1250, 215360 => 1250, 222860 => 550, 229830 => 440, 232250 => 440, 232290 => 300, 232330 => 240, 232370 => 320, 258550 => 252490, 259080 => 261140, 295230 => 265630, 317670 => 224260, 332670 => 234630, 376030 => 346110);

                if (isset($mapping[$appID])) {
                    return $mapping[$appID];
                }
            }

        // Client to server mapping
        } else {

            if (in_array($appID, array(10, 20, 30, 40, 50, 60, 80))) {

                return 90;

            } else {

                $mapping = array(240 => 232330, 300 => 232290, 320 => 232370, 440 => 232250, 500 => 510, 730 => 740, 550 => 222860, 1250 => 215360, 4000 => 4020, 4920 => 4940, 17500 => 17505, 17515 => 17510, 17575 => 17570, 17710 => 111710, 215350 => 215360, 224260 => 317670, 234630 => 332670, 252490 => 258550, 261140 => 259080, 265630 => 295230, 346110 => 376030);

                if (isset($mapping[$appID])) {
                    return $mapping[$appID];
                }
            }
        }

        return $appID;
    }

# https://github.com/easy-wi/developer/issues/70
    function removePub ($string) {
        if (substr(strtolower($string), -4) == '.pub') {
            return substr($string, 0, -4);
        }
        return $string;
    }

# https://github.com/easy-wi/developer/issues/57
    function checkFtpData ($ip, $port, $user, $pwd) {

        $ftpConnection = @ftp_connect($ip, $port);

        if ($ftpConnection) {

            $ftpLogin = @ftp_login($ftpConnection, $user, $pwd);
            ftp_close($ftpConnection);

            return ($ftpLogin === true) ? true : 'login';
        }

        return 'ipport';
    }

    function ftpStringToData ($fptConnect) {

        $server = null;
        $port = null;
        $path = null;
        $user = '';
        $pwd = '';

        $fptConnect = str_replace(array('ftp://', 'ftps://'), '', $fptConnect);

        $splittedConnectionString = preg_split('/\@/', $fptConnect, -1, PREG_SPLIT_NO_EMPTY);

        $splittedConnectionStringArrayCount = count($splittedConnectionString) -1;


        if ($splittedConnectionStringArrayCount > 0) {

            $serverData = $splittedConnectionString[$splittedConnectionStringArrayCount];

            unset($splittedConnectionString[$splittedConnectionStringArrayCount]);

            @list($user, $pwd) = explode(':', implode('@', $splittedConnectionString));

            $ex = preg_split('/\//', $serverData, -1, PREG_SPLIT_NO_EMPTY);
            $portServer = $ex[0];

            $path = '';
            $i = 1;

            while ($i < count($ex)) {
                $path .= '/' . $ex[$i];
                $i++;
            }

            if ($path == '') {
                $path = '/';
            }

            @list($server, $port) = explode(':', $portServer);

            if (!$port) {
                $port = 21;
            }

        }

        return array('server' => $server, 'port' => $port, 'user' => $user, 'pwd' => $pwd, 'path' => $path);
    }

    function configureDateTables ($doNotShow = '', $defaultSorting = '0, "asc"', $ajaxSource = '') {

        global $htmlExtraInformation, $gsprache;

        if ($ajaxSource != '') {
            $ajaxSource = '"bServerSide" : true,"sAjaxSource": "' . $ajaxSource. '",';
        }

        $htmlExtraInformation['css'][] = '<link href="css/default/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css">';
        $htmlExtraInformation['js'][] = '<script src="js/default/plugins/datatables/jquery.datatables.js" type="text/javascript"></script>';
        $htmlExtraInformation['js'][] = '<script src="js/default/plugins/datatables/datatables.bootstrap.js" type="text/javascript"></script>';
        $htmlExtraInformation['js'][] = "<script type='text/javascript'>
$(function() {
    $('#dataTable').dataTable({
        'bPaginate': true,
        'bLengthChange': true,
        'bFilter': true,
        'bSort': true,
        'aoColumnDefs': [{
            'bSortable': false,
            'aTargets': [{$doNotShow}]
        }],
        'bInfo': true,
        'bAutoWidth': false,
        'iDisplayLength' : 10,
        'aaSorting': [[{$defaultSorting}]],
        'oLanguage': {
            'oPaginate': {
                'sFirst': '{$gsprache->dataTablesFirst}',
                'sLast': '{$gsprache->dataTablesLast}',
                'sNext': '{$gsprache->dataTablesNext}',
                'sPrevious': '{$gsprache->dataTablesPrevious}'
            },
            'sEmptyTable': '{$gsprache->dataTablesEmptyTable}',
            'sInfo': '{$gsprache->dataTablesInfo}',
            'sInfoEmpty': '{$gsprache->dataTablesEmpty}',
            'sInfoFiltered': '{$gsprache->dataTablesFiltered}',
            'sLengthMenu': '{$gsprache->dataTablesMenu}',
            'sSearch': '{$gsprache->dataTablesSearch}',
            'sZeroRecords': '{$gsprache->dataTablesNoRecords}'
        },
        $ajaxSource
    });
});
</script>";

    }

    function getUserList ($resellerID) {

        $table = array();

        global $sql;

        $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
        $query->execute(array($resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }

        return $table;
    }
}