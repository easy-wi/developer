<?php

/**
 * File: ajax.php.
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

define('EASYWIDIR', dirname(__FILE__));

if (is_dir(EASYWIDIR . '/install')) {
    die('Please remove the "install" folder');
}

include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/config.php');
include(EASYWIDIR . '/stuff/settings.php');

if (!isset($admin_id) and !isset($user_id)) {
    redirect('login.php');
} else if (isset($admin_id)) {
    $pa = User_Permissions($admin_id);
} else if (isset($user_id)) {
    $pa = User_Permissions($user_id);
}

if (isset($admin_id) and $pa['dedicatedServer'] and $ui->smallletters('d', 7, 'get') == 'freeips' and $reseller_id == 0) {

    if ($ui->id('userID', 10, 'get')) {

        $query = $sql->prepare("SELECT `resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($ui->id('userID', 10, 'get')));

        $ipsAvailable = freeips(($query->fetchColumn()));

    } else {
        $ipsAvailable = array();
    }

    $template_file = 'ajax_admin_roots_ips.tpl';
}

if (isset($template_file)) {

    require_once IncludeTemplate($template_to_use, $template_file, 'ajax');

} else {

    die('No Access');

}