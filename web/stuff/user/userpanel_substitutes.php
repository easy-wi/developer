<?php

/**
 * File: userpanel_substitutes.php.
 * Author: Ulrich Block
 * Date: 18.08.13
 * Time: 13:25
 * Contact: <ulrich.block@easy-wi.com>
 * Ticket: https://github.com/easy-wi/developer/issues/2
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

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/third_party/password_compat/password.php');

if (!isset($main) or $main != 1 or !isset($user_id) or !isset($reseller_id) or isset($_SESSION['substitute'])) {
    header('Location: userpanel.php');
    die;
}

$sprache = getlanguagefile('user', $user_language, $reseller_id);

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->id('id', 10, 'get') or $ui->st('d', 'get') == 'ad') {

    $template_file = 'userpanel_404.tpl';
    $id = $ui->id('id', 10, 'get');

    if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

        $db = array();
        $gs = array();
        $wv = array();
        $vo = array();
        $vd = array();
        $vs = array();
        $ro = array();

        $query = $sql->prepare("SELECT `id`,`dbname` FROM `mysql_external_dbs` WHERE `uid`=? AND `resellerid`=? AND `active`='Y'");
        $query->execute(array($user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $db[$row['id']] = $row['dbname'];
        }

        $query = $sql->prepare("SELECT `id`,CONCAT(`serverip`,':',`port`) AS `address` FROM `gsswitch` WHERE `userid`=? AND `resellerid`=? AND `active`='Y'");
        $query->execute(array($user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $gs[$row['id']] = $row['address'];
        }

        $query = $sql->prepare("SELECT `webVhostID`,`dns` FROM `webVhost` WHERE `userID`=? AND `resellerID`=? AND `active`='Y'");
        $query->execute(array($user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $wv[$row['webVhostID']] = $row['dns'];
        }

        $query = $sql->prepare("SELECT `id`,CONCAT(`ip`,':',`port`) AS `address` FROM `voice_server` WHERE `userid`=? AND `resellerid`=? AND `active`='Y'");
        $query->execute(array($user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $vo[$row['id']] = $row['address'];
        }

        $query = $sql->prepare("SELECT `dnsID`,`dns` FROM `voice_dns` WHERE `userID`=? AND `resellerID`=? AND `active`='Y'");
        $query->execute(array($user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $vd[$row['dnsID']] = $row['dns'];
        }

        $query = $sql->prepare("SELECT `id`,`ip` FROM `virtualcontainer` WHERE `userid`=? AND `resellerid`=? AND `active`='Y'");
        $query->execute(array($user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $vs[$row['id']] = $row['ip'];
        }

        $query = $sql->prepare("SELECT `dedicatedID`,`ip` FROM `rootsDedicated` WHERE `userID`=? AND `resellerID`=? AND `active`='Y'");
        $query->execute(array($user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ro[$row['dedicatedID']] = $row['ip'];
        }
    }

    if (!$ui->st('action', 'post') and $ui->st('d', 'get') == 'ad') {

        $randompass = passwordgenerate(10);

        $template_file = 'userpanel_substitutes_add.tpl';

    } else if (!$ui->st('action', 'post') and $ui->id('id', 10, 'get') and ($ui->st('d', 'get') == 'md' or  $ui->st('d', 'get') == 'dl')) {

        $query = $sql->prepare("SELECT `loginName`,`active`,`name`,`vname` FROM `userdata_substitutes` WHERE `sID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $loginName = $row['loginName'];
            $active = $row['active'];
            $name = $row['name'];
            $vname = $row['vname'];
            $template_file = ($ui->st('d', 'get') == 'md') ? 'userpanel_substitutes_mod.tpl' : 'userpanel_substitutes_del.tpl';
        }

        if ($ui->st('d', 'get') == 'md') {

            $as = array();

            $query = $sql->prepare("SELECT `oID`,`oType` FROM `userdata_substitutes_servers` WHERE `sID`=? AND `resellerID`=?");
            $query->execute(array($id,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $as[$row['oType']][$row['oID']] = true;
            }
        }

    } else if ($ui->st('action', 'post') == 'ad' or ($ui->st('action', 'post') == 'md' and $ui->id('id', 10, 'get'))) {

        $id = $ui->id('id', 10, 'get');

        if ($ui->st('action', 'post') == 'ad') {

            if (!$ui->names('loginName',255, 'post') or ($rSA['prefix1'] == 'Y' and $rSA['prefix2'] != '' and preg_match('/^'.$rSA['prefix2'].'[0-9]{0,}+$/',$ui->names('loginName',255, 'post')))) {
                $template_file = $spracheResponse->errorUsername;
            }

            $query = $sql->prepare("SELECT 1 FROM `userdata_substitutes` WHERE `loginName`=? LIMIT 1");
            $query->execute(array($ui->names('loginName',255, 'post')));
            if ($query->rowCount() > 0) {
                $userError = $spracheResponse->error_username;
            }

            $query = $sql->prepare("SELECT 1 FROM `userdata` WHERE `cname`=? LIMIT 1");
            $query->execute(array($ui->names('loginName',255, 'post')));
            if ($query->rowCount() > 0) {
                $userError = $spracheResponse->error_username;
            }

            if (isset($userError)) {
                $template_file = $userError;

            } else {
                $newHash = passwordCreate($ui->names('loginName',255, 'post'), $ui->password('security',255, 'post'));

                if (is_array($newHash)) {
                    $query = $sql->prepare("INSERT INTO `userdata_substitutes` (`userID`,`active`,`loginName`,`name`,`vname`,`passwordHashed`,`salt`,`resellerID`) VALUES (?,?,?,?,?,?,?,?)");
                    $query->execute(array($user_id, $ui->active('active', 'post'), $ui->names('loginName',255, 'post'), $ui->names('name',255, 'post'), $ui->names('vname',255, 'post'), $newHash['hash'], $newHash['salt'], $reseller_id));

                } else {
                    $query = $sql->prepare("INSERT INTO `userdata_substitutes` (`userID`,`active`,`loginName`,`name`,`vname`,`passwordHashed`,`resellerID`) VALUES (?,?,?,?,?,?,?)");
                    $query->execute(array($user_id, $ui->active('active', 'post'), $ui->names('loginName',255, 'post'), $ui->names('name',255, 'post'), $ui->names('vname',255, 'post'), $newHash, $reseller_id));
                }



                if ($query->rowCount() > 0) {
                    $changed = true;
                    $id = $sql->lastInsertId();
                }

            }

        } else if ($ui->st('action', 'post') == 'md' and $ui->id('id', 10, 'get')) {

            if ($ui->password('security',255, 'post') != '(encrypted)') {

                $salt = md5(mt_rand().date('Y-m-d H:i:s:u'));
                $query = $sql->prepare("SELECT `loginName` FROM `userdata_substitutes` WHERE `sID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($id,$reseller_id));
                $loginName = $query->fetchColumn();

                $newHash = passwordCreate($loginName, $ui->password('security',255, 'post'));

                if (is_array($newHash)) {
                    $query = $sql->prepare("UPDATE `userdata_substitutes` SET `active`=?,`name`=?,`vname`=?,`passwordHashed`=?,`salt`=? WHERE `sID`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($ui->active('active', 'post'), $ui->names('name',255, 'post'), $ui->names('vname',255, 'post'), $newHash['hash'], $newHash['salt'], $id, $user_id, $reseller_id));

                } else {
                    $query = $sql->prepare("UPDATE `userdata_substitutes` SET `active`=?,`name`=?,`vname`=?,`passwordHashed`=? WHERE `sID`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($ui->active('active', 'post'), $ui->names('name',255, 'post'), $ui->names('vname',255, 'post'), $newHash, $id, $user_id, $reseller_id));
                }


            } else {
                $query = $sql->prepare("UPDATE `userdata_substitutes` SET `active`=?,`name`=?,`vname`=? WHERE `sID`=? AND `userID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($ui->active('active', 'post'),$ui->names('name',255, 'post'),$ui->names('vname',255, 'post'), $id, $user_id, $reseller_id));
            }

            if ($query->rowCount() > 0) {
                $changed = true;
            }
        }

        if ($id) {

            $query = $sql->prepare("SELECT `oID`,`oType` FROM `userdata_substitutes_servers` WHERE `sID`=? AND `resellerID`=?");
            $query2 = $sql->prepare("DELETE FROM `userdata_substitutes_servers` WHERE `oType`=? AND `oID`=? AND `sID`=? AND `resellerID`=?");

            $query->execute(array($id,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (!$ui->id($row['oType'],10, 'post') or !in_array($row['oID'],(array)$ui->id($row['oType'],10, 'post'))) {
                    $query2->execute(array($row['oType'], $row['oID'],$id,$reseller_id));
                    if ($query2->rowCount() > 0) {
                        $changed = true;
                    }
                }
            }

            foreach (array('gs','wv','db','vo','vd','vs','ro') as $v) {

                if ($ui->id($v, 10, 'post')) {

                    $query = $sql->prepare("INSERT INTO `userdata_substitutes_servers` (`sID`,`oType`,`oID`,`resellerID`) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE `sID`=`sID`");

                    foreach ($ui->id($v, 10, 'post') as $oID) {

                        $query->execute(array($id, $v, $oID, $reseller_id));

                        if ($query->rowCount() > 0) {
                            $changed = true;
                        }
                    }
                }
            }

            $template_file = (isset($changed)) ? $spracheResponse->table_add : $spracheResponse->error_table;
        }

    } else if ($ui->st('action', 'post') == 'dl' and $ui->id('id', 10, 'get')) {

        $query = $sql->prepare("DELETE FROM `userdata_substitutes` WHERE `sID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));

        $template_file = ($query->rowCount() > 0) ? $spracheResponse->table_del : 'userpanel_404.tpl';

        $query = $sql->prepare("DELETE o.* FROM `userdata_substitutes_servers` o LEFT JOIN `userdata_substitutes` s ON o.`sID`=s.`sID` WHERE s.`sID` IS NULL");
        $query->execute();
    }

} else {

    $table = array();

    $query = $sql->prepare("SELECT `sID`,`loginName`,`active` FROM `userdata_substitutes` WHERE `userID`=? AND `resellerID`=?");
    $query->execute(array($user_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[] = array('id' => $row['sID'], 'loginName' => $row['loginName'], 'active' => ($row['active'] == 'Y') ? $gsprache->yes : $gsprache->no);
    }

    configureDateTables('-1, -2', '0, "asc"');

    $template_file = 'userpanel_substitutes_list.tpl';
}