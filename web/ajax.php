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
if (is_dir(EASYWIDIR . '/install')) die('Please remove the "install" folder');
include(EASYWIDIR . '/stuff/functions.php');
include(EASYWIDIR . '/stuff/class_validator.php');
include(EASYWIDIR . '/stuff/vorlage.php');
include(EASYWIDIR . '/stuff/config.php');
include(EASYWIDIR . '/stuff/settings.php');
if (isset($admin_id)) {
    $permissionid=(isset($_SESSION['oldid']))  ? (isset($_SESSION['oldadminid'])) ? $_SESSION['oldadminid'] : $_SESSION['oldid'] : $admin_id;
	$userpermissionquery = $sql->prepare("SELECT * FROM `userpermissions` WHERE `userid`=? LIMIT 1");
	$userpermissionquery->execute(array($permissionid));
	foreach ($userpermissionquery->fetchall() as $userpermissionrow) {
		if ($userpermissionrow['root']=="Y") {
			foreach ($userpermissionrow as $key => $value) {
				$pa[$key] = true;
			}
		} else {
			foreach ($userpermissionrow as $key => $value) {
				if ($value=="Y") {
					$pa[$key] = true;
				} else {
					$pa[$key] = false;
				}
			}
		}
	}
}
if (isset($user_id)) {
	$userpermissionquery = $sql->prepare("SELECT * FROM `userpermissions` WHERE `userid`=? LIMIT 1");
	$userpermissionquery->execute(array($user_id));
	foreach ($userpermissionquery->fetchall() as $userpermissionrow) {
		if ($userpermissionrow['miniroot']=="Y") {
			foreach ($userpermissionrow as $key => $value) {
				$pau[$key] = true;
			}
		} else {
			foreach ($userpermissionrow as $key => $value) {
				if (isset($admin_id)) {
					$pau[$key] = true;
				} else {
					if ($value=="Y") {
						$pau[$key] = true;
					} else {
						$pau[$key] = false;
					}
				}
			}
		}
	}
}
if (((!isset($admin_id) and !isset($user_id)) or (((!$pa['gserver']) and !$pa['voiceserver'] and !$pa['voicemasterserver'] and !$pa['traffic'] and !$pa['user'] and !rsellerpermisions($admin_id) and !$pa['usertickets']) and (!$pau['restart'] and !$pau['usertickets'])))) {
	die('No acces');
}