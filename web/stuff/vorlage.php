<?php

/**
 * File: vorlage.php.
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
function uname_check($value, $laeng) {
	return  (preg_match("/^[\w\-\_\.]+$/", $value) and strlen($value) <= $laeng) ? true : false; 
}

function isip($value, $ipx) {
	switch ($ipx) {
		case 'ip4': 
			return (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) ? true : false; 
			
		case 'ip6': 
			return (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) ? true : false; 
			
		case 'all': 
			return (filter_var($value, FILTER_VALIDATE_IP) !== false) ? true : false; 
	}
	
	return false;
}

function isips($value) {
	return (preg_match("/^[\r\n\.\/0-9]+$/", $value) !== false) ? true : false; 
}

function ismac($value) {
	return (preg_match("/^[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}$/", $value) !== false) ? true : false; 
}

function ismail($value) {
	return (filter_var($value, FILTER_VALIDATE_EMAIL) !== false) ? true : false; 
}

function isurl($value) {
	return (filter_var($value, FILTER_VALIDATE_URL) !== false) ? true : false; 
}

function isdomain($value) {
	return (preg_match("/^[a-z0-9\-\.]+\.[a-z]{1,5}$/", $value) !== false) ? true : false; 
}

function isRsync($value) {
	return (preg_match('/^(rsync\:\/\/|)[\w\.\-\_]{1,}(::[][\w\.\-\_\/]{1,}|)$/', $value) !== false) ? true : false; 
}

function is_number($value, $max = 100, $min = 0) {
	return (is_numeric($value) && $value > $min && strlen($value) <= $max) ? true : false; 
}

function validate_int($value, $min, $max) {
	$value = str_replace(',', '.', $value);
	return (preg_match("/^[\d+(.\d+|$)]+$/", $value) !== false && is_number($value, $max, $min)) ? true : false; 
}

function isinteger($value) {
	return is_int($value); 
}

function active_check($value) {
	return (strlen($value) == 1 && preg_match("/[N,Y]/", $value) !== false) ? true : false; 
}

function small_letters_check($value, $laeng) {
	return (strlen($value) <= $laeng && preg_match("/^[a-z]+$/", $value)) ? true : false; 
}

function wpreg_check($value, $laeng) {
	return (strlen($value) <= $laeng && preg_match("/^[\w]+$/", $value)) ? true : false; 
}

/** @TODO : hier weiter überarbeiten **/
function password_check($value, $laeng) {
	if (strlen($value) <= $laeng and strlen($value) > 4 and preg_match("/[A-Za-z0-9]/", $value)) {
		return $value;
	}
}

function is_password($value, $laeng) {
	if (preg_match("/^[\w\[\]\(\)\<\>!\"§$%&\/=\?*+#]{1," . $laeng . "}$/", $value)) {
		return $value;
	}
}

function captchac($value) {
	if (strlen($value) == "4" and preg_match("/[a-zA-Z0-9]/", $value)) {
		return $value;
	}
}

function cores($value) {
	if (preg_match("/^[0-9\,]+$/", $value)) {
		return $value;
	}
}

function ipport($value) {
	$str_replace = str_replace(' ', "", $value);
	$preg_replace = preg_replace('/\s+/', '', $str_replace);
	$adresse_awk = explode(':', $preg_replace);
	$fail = 0;
	if (!isset($adresse_awk[0]) or !filter_var($adresse_awk[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		$fail = 1;
	if (!isset($adresse_awk[1]) or !preg_match("/^(0|([1-9]\d{0,3}|[1-5]\d{4}|[6][0-5][0-5]([0-2]\d|[3][0-5])))$/", $adresse_awk[1]))
		$fail = 1;
	if ($fail == 0) {
		return $adresse_awk[0] . ':' . $adresse_awk[1];
	}
}

function port($value) {
	if (preg_match("/^(0|([1-9]\d{0,3}|[1-5]\d{4}|[6][0-5][0-5]([0-2]\d|[3][0-5])))$/", $value)) {
		return $value;
	}
}

function mapname($value) {
	$a = array(" ", ".bsp");
	$b = array("", "");
	$removed = str_replace($a, $b, $value);
	if (preg_match("/^[\w-. \/]+$/", $removed)) {
		return $removed;
	}
}

function smallletters($value) {
	$value = str_replace(' ', '', $value);
	if (preg_match("/^[a-z]+$/", $value)) {
		return $value;
	}
}

function gamestring($value) {
	if (preg_match("/^[\w\.\-\_]+$/", $value)) {
		return $value;
	}
}

function folder($value) {
	if (preg_match("/^[\w\/\-\_]+$/", $value)) {
		return $value;
	}
}

function description($value) {
	$value = htmlentities($value, ENT_QUOTES, 'UTF-8');
	if (preg_match("/^[\x{0400}-\x{04FF}\w\r\n\-():;&.,% ]+/u", $value)) {
		return $value;
	}
}

function startparameter($value) {
	if (preg_match('/^[\w\r\n\(\)\[\]\{\}\~\=\?\%\:\.\,\"+-\_\|ßöÖäÄüÜ ]+$/', $value)) {
		return $value;
	}
}

function names($value, $laeng) {
	if (strlen($value) <= $laeng and preg_match('/^[\p{L}\p{N}][\p{L}\p{N}  _.-]+$/u', $value)) {
		return $value;
	}
}

function phone($value, $laeng = null) {
	if (preg_match('/^[0-9\+\(\)\/\-\s]+$/', $value)) {
		return $value;
	}
}

function isid($value, $count) {
	if (strlen($value) <= $count and is_numeric($value)) {
		return $value;
	}
}

function isconfig($value) {
	if (preg_match('/^[\w\/\-\_\.]+$/', $value)) {
		return $value;
	}
}

function istimezone($value) {
	if (preg_match('/^1?[+-][0-9]$|^[+-][1][0-9]|^[+-][2][0-4]$/', $value)) {
		return $value;
	}
}

function isDate($value) {
	if (is_string($value) and @strtotime($value)) {
		return $value;
	}
}

function st($value) {
	if (is_string($value) and preg_match('/^[a-z]{2}$/', $value)) {
		return $value;
	}
}
