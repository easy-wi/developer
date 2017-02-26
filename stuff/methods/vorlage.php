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
    return  (preg_match("/^[\w\-\_\.]+$/", $value) and strlen($value) <= $laeng) ? $value : false;
}

function isip($value, $ipx) {
    switch ($ipx) {
        case 'ip4':
            return (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) ? $value : false;

        case 'ip6':
            return (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) ? $value : false;

        case 'all':
            return (filter_var($value, FILTER_VALIDATE_IP) !== false) ? $value : false;
    }

    return false;
}

function isips($value) {
    return (preg_match("/^[\r\n\.\/0-9]+$/", $value) !== false) ? $value : false;
}

function ismac($value) {
    return (preg_match("/^[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}$/", $value) !== false) ? $value : false;
}

function ismail($value) {
    return (filter_var($value, FILTER_VALIDATE_EMAIL) !== false) ? $value : false;
}

function isurl($value) {
    return (filter_var($value, FILTER_VALIDATE_URL) !== false) ? $value : false;
}

function isdomain($value) {
    return (preg_match("/^[a-z0-9\-\.]+\.[a-z]{1,10}$/", $value) !== false) ? $value : false;
}

function isRsync($value) {
    return (preg_match('/^(rsync\:\/\/|)[\w\.\-\_]{1,}(::[][\w\.\-\_\/]{1,}|)$/', $value) !== false) ? $value : false;
}

function is_number($value, $max = 100, $min = 0) {
    return (is_numeric($value) && $value > $min && strlen($value) <= $max) ? $value : false;
}

function validate_int($value, $min, $max) {
    $value = str_replace(',', '.', $value);
    return (preg_match("/^[\d+(.\d+|$)]+$/", $value) !== false && is_number($value, $max, $min)) ? $value : false;
}

function isinteger($value) {
    return (is_int($value)) ? $value : false;
}

function active_check($value) {
    return (strlen($value) == 1 && preg_match("/[N,Y]/", $value) !== false) ? $value : false;
}

function small_letters_check($value, $laeng) {
    return (strlen($value) <= $laeng && preg_match("/^[a-z]+$/", $value)) ? $value : false;
}

function wpreg_check($value, $laeng) {
    return (strlen($value) <= $laeng && preg_match("/^[\w]+$/", $value)) ? $value : false;
}
function password_check($value,$laeng) {
    return (strlen($value) <= $laeng and strlen($value) > 4 and preg_match("/[A-Za-z0-9]/", $value)) ? $value : false;
}
function is_password ($value,$laeng) {
    return (preg_match("/^[\w\[\]\(\)\<\>!\"§$%&\/=\?*+#]{1,".$laeng."}$/", $value)) ? $value : false;
}
function captchac($value) {
    return (strlen($value) == "4" and preg_match("/[a-zA-Z0-9]/", $value)) ? $value : false;
}
function cores($value) {
    return (preg_match("/^[0-9\,]+$/", $value)) ? $value : false;
}
function ipport($value) {
    $adresse_awk = explode(':' , preg_replace('/\s+/', '', $value));
    return ((!isset($adresse_awk[0]) or !filter_var($adresse_awk[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) or (!isset($adresse_awk[1]) or !preg_match("/^(0|([1-9]\d{0,3}|[1-5]\d{4}|[6][0-5][0-5]([0-2]\d|[3][0-5])))$/", $adresse_awk[1]))) ? false : $value;
}
function port($value) {
    return (preg_match("/^(0|([1-9]\d{0,3}|[1-5]\d{4}|[6][0-5][0-5]([0-2]\d|[3][0-5])))$/", $value)) ? $value : false;
}
function names($value,$laeng) {
    return(strlen($value)<=$laeng and preg_match('/^[\p{L}\p{N}][\p{L}\p{N}  _.-]+$/u',$value)) ? $value : false;
}
function phone($value) {
    return (preg_match('/^[0-9\+\(\)\/\-\s]+$/', $value)) ? $value : false;
}
function isid($value,$count){
    return (!is_array($value) and strlen($value) <= $count and is_numeric($value)) ? $value : false;
}
function isDate($value) {
    return (is_string($value) and @strtotime($value)) ? $value : false;
}
function gamestring($value){
    return (preg_match('/^[\w\.\-\_]+$/', $value)) ? $value : false;
}
function isExternalID($value) {
    return (preg_match('/^[\w\:]{0,255}+$/', $value)) ? $value : '';
}
function isToken($value) {
    return (preg_match('/^[\\\w\/\+]{4,50}+$/', $value)) ? $value : '';
}