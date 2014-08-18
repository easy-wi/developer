<?php

/**
 * File: userpanel_substitutes_own.php.
 * Author: Ulrich Block
 * Date: 21.02.14
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

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/third_party/password_compat/password.php');

$sprache = getlanguagefile('user', $user_language, $reseller_id);

if ($ui->st('d', 'get') == 'pw') {

    if (!$ui->smallletters('action', 2, 'post')) {

        $template_file = 'userpanel_pass.tpl';

    } else if ($ui->smallletters('action', 2, 'post') == 'md'){

        $errors = array();

        if (!$ui->password('password', 255, 'post')) {
            $errors[] = $sprache->error_pass;
        }

        if (!$ui->password('pass2', 255, 'post')) {
            $errors[] = $sprache->error_pas;
        }

        if ($ui->password('password', 255, 'post') != $ui->password('pass2', 255, 'post')) {
            $errors[] = $sprache->error_passw_succ;
        }

        if (!token(true)) {
            $errors[] = $spracheResponse->token;
        }

        if (count($errors)>0) {

            $template_file = implode('<br />', $errors);

        } else {

            $salt = md5(mt_rand() . date('Y-m-d H:i:s:u'));
            $query = $sql->prepare("SELECT `loginName` FROM `userdata_substitutes` WHERE `sID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($_SESSION['sID'], $reseller_id));
            $loginName = $query->fetchColumn();

            if (strlen($loginName) > 0 and $ui->password('password', 255, 'post')) {

                $newHash = passwordCreate($loginName, (string) $ui->password('password', 255, 'post'));

                if (is_array($newHash)) {
                    $query = $sql->prepare("UPDATE `userdata_substitutes` SET `passwordHashed`=?,`salt`=? WHERE `sID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($newHash['hash'], $newHash['salt'], $_SESSION['sID'], $reseller_id));

                } else {
                    $query = $sql->prepare("UPDATE `userdata_substitutes` SET `passwordHashed`=? WHERE `sID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($newHash, $_SESSION['sID'], $reseller_id));
                }

                if ($query->rowCount() > 0) {
                    $template_file = $spracheResponse->table_add;
                } else {
                    $template_file = $spracheResponse->error_table;
                }
            } else {
                $template_file = 'userpanel_404.tpl';
            }
        }

    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else if ($ui->escaped('spUser', 'get') and $ui->id('spId', 10, 'get')) {

    $query = $sql->prepare("DELETE FROM `userdata_social_identities_substitutes` WHERE `userID`=? AND `serviceProviderID`=? AND `serviceUserID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($_SESSION['sID'], $ui->id('spId', 10, 'get'), $ui->escaped('spUser', 'get'), $reseller_id));

    if ($query->rowCount() > 0) {
        $template_file = $spracheResponse->table_del;
    } else {
        $template_file = $spracheResponse->error_table;
    }

} else {

    if ($ui->smallletters('action', 2, 'post') != 'md' and $ui->w('added', 255, 'get')) {

        $template_file = $spracheResponse->table_add;

    } else if ($ui->smallletters('action', 2, 'post') != 'md') {

        $serviceProviders = array();

        $htmlExtraInformation['css'][] = '<link href="css/default/social_buttons.css" rel="stylesheet">';

        $query = $sql->prepare("SELECT `serviceProviderID`,`filename` FROM `userdata_social_providers` WHERE `resellerID`=0 AND `active`='Y'");
        $query2 = $sql->prepare("SELECT `serviceUserID` FROM `userdata_social_identities_substitutes` WHERE `serviceProviderID`=? AND `userID`=? LIMIT 1");


        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $query2->execute(array($row['serviceProviderID'], $_SESSION['sID']));

            $serviceProviders[] = array(
                'spId' => $row['serviceProviderID'],
                'sp' => $row['filename'],
                'spUserId' => urlencode($query2->fetchColumn())
            );
        }

        $query = $sql->prepare("SELECT `name`,`vname` FROM `userdata_substitutes` WHERE `sID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($_SESSION['sID'], $reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $name = $row['name'];
            $vname = $row['vname'];
        }

        $template_file = 'userpanel_user_substitute_md.tpl';

    } else if ($ui->smallletters('action', 2, 'post') == 'md' and token(true)) {

        $query = $sql->prepare("UPDATE `userdata_substitutes` SET `name`=?,`vname`=? WHERE `sID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($ui->names('name',255, 'post'),$ui->names('vname',255, 'post'), $_SESSION['sID'], $reseller_id));

        if ($query->rowCount() > 0) {
            $template_file = $spracheResponse->table_add;
        } else {
            $template_file = $spracheResponse->error_table;
        }

    }

}