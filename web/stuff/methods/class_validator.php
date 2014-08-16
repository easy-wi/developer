<?php
/**
 * File: class_validator.php.
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

class ValidateUserinput {

    public $get = array(), $post = array(), $server = array(), $request = array(), $env = array();

    private function magic_quotes ($value) {

        if (function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc() == 1) {
            $value = stripcslashes($value);
        }

        return $value;
    }

    private function ArrayToObject($array) {

        if (is_string($array)) {

            return $this->magic_quotes($array);

        } else if (is_array($array)) {

            $stdClass = new stdClass();

            foreach ($array as $key => $value) {
                $stdClass->$key = $this->ArrayToObject($value);
            }

            return $stdClass;

        }

        return false;

    }

    function __construct($get, $post, $server, $request, $env) {

        foreach ($get as $key => $value) {
            if (is_string($value)) {
                $this->get[$key] = $this->magic_quotes($value);
            } else if (is_array($value)) {
                $this->get[$key] = $this->ArrayToObject($value);
            }
        }

        foreach ($post as $key => $value) {
            if (is_string($value)) {
                $this->post[$key] = $this->magic_quotes($value);
            } else if (is_array($value)) {
                $this->post[$key] = $this->ArrayToObject($value);
            }
        }

        foreach ($server as $key => $value) {
            if (is_string($value)) {
                $this->server[$key] = $this->magic_quotes($value);
            } else if (is_array($value)) {
                $this->server[$key] = $this->ArrayToObject($value);
            }
        }

        foreach ($request as $key => $value) {
            if (is_string($value)) {
                $this->request[$key] = $this->magic_quotes($value);
            } else if (is_array($value)) {
                $this->request[$key] = $this->ArrayToObject($value);
            }
        }

        foreach ($env as $key => $value) {
            if (is_string($value)) {
                $this->env[$key] = $this->magic_quotes($value);
            } else if (is_array($value)) {
                $this->env[$key] = $this->ArrayToObject($value);
            }
        }
    }

    function __destruct() {
        unset($this->get, $this->post, $this->server, $this->request, $this->env);
    }

    private function loop ($check, $function, $type, $length = null) {

        if (is_string($check) and $length == null and $this->$function($check, $type)) {

            return $this->$function($check, $type);

        } else if (is_string($check) and $this->$function($check, $length, $type)) {

            return $this->$function($check, $length, $type);

        } else if (is_array($check) or is_object($check)) {

            $stdClass = new stdClass();

            foreach ($check as $key => $value) {
                $stdClass->$key = (is_string($value)) ? $value : $this->loop($value, $function, $type, $length);
            }

            return $stdClass;
        }

        return false;
    }

    private function if_obj_or_str ($value, $type, $object) {

        if ($object == false and is_string($value) and !isset($this->$type)) {

            return $value;

        } else if ($object == false and isset($this->$type)) {

            $check = $this->$type;

            if (isset($check[$value])) {
                return $check[$value];
            }

        } else if ($object != false and isset($this->$type)) {

            $check = $this->$type;

            if (isset($check[$value]->$object)) {
                return $check[$value]->$object;
            }
        }

        return false;
    }

    function url ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and filter_var($check,FILTER_VALIDATE_URL)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'url', $type);
        }

        return false;
    }

    function domain ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^[\w\d+\-\.]+\.[a-z]{1,5}$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'domain', $type);
        }

        return false;
    }

    function domainPath ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^[\w\d+\-\.]+\.[a-zA-Z]{1,5}(|\:[0-9]{1,5})(|\/[\w\.\/\-\_]{0,})$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'domain', $type);
        }

        return false;
    }

    function ismail ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if (is_string($check)) {
            $check = trim($check);
        }

        if ($check and is_string($check) and filter_var($check, FILTER_VALIDATE_EMAIL)) {

            $exploded = explode('@', $check);

            if (!checkdnsrr($exploded[1], 'MX') and !checkdnsrr($exploded[1], 'A')) {
                return false;
            }

            return strtolower($check);

        }  else if ($check) {
            return $this->loop($check, 'ismail', $type);
        }

        return false;
    }

    function ip4 ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and filter_var($check, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
            return $check;
        } else if ($check) {
            return $this->loop($check, 'ip4', $type);
        }

        return false;
    }

    function ip6 ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and filter_var($check, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
            return $check;
        } else if ($check) {
            return $this->loop($check, 'ip6', $type);
        }

        return false;
    }

    function ip ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and filter_var($check, FILTER_VALIDATE_IP)){
            return $check;
        } else if ($check) {
            return $this->loop($check, 'ip', $type);
        }

        return false;
    }

    function ips ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^[\r\n\.\/\d+]+$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'ips', $type);
        }

        return false;
    }

    function subnetFirstThree ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^[\d]{1,3}.[\d]{1,3}.[\d]{1,3}+$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'subnetFirstThree', $type);
        }

        return false;
    }

    function mac ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'mac', $type);
        }

        return false;
    }

    function isDate ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and @strtotime($check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'isDate', $type);
        }

        return false;
    }

    function port ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^(0|([1-9]\d{0,3}|[1-5]\d{4}|[6][0-5][0-5]([0-2]\d|[3][0-5])))$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'port', $type);
        }

        return false;
    }

    function path ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^[\w\-\_\/]{1,}[\/]{0,1}$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'path', $type);
        }

        return false;
    }

    function anyPath ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^([\/]{1}|[\/]{0,1}[\w\-\_\/\.]{0,}[\/]{0,1})$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'anyPath', $type);
        }

        return false;
    }

    function pregw ($value, $length, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w]{1,' . $length . '}$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'pregw', $type, $length);
        }

        return false;
    }

    function isinteger ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check)) {

            $value = (int) str_replace(',', '.', $check);

            if (preg_match("/^[\d+(.\d+|$)]+$/", $value)) {
                return $value;
            }

        } else if ($check) {
            return $this->loop($check, 'isinteger', $type);
        }

        return false;
    }

    function active ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^[N,Y]{1}$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'active', $type);
        }

        return false;
    }

    function password ($value, $length, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w\[\]\-\@\(\)\<\>\!\"\.$%&\/=\?*+#]{1,' . $length . '}$/', trim($check))) {
            return trim($check);
        } else if ($check) {
            return $this->loop($check, 'password', $type, $length);
        }

        return false;
    }

    function captcha ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match("/^[a-zA-Z\d+]{4}$/", $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'catpcha', $type);
        }

        return false;
    }

    function ipport ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check)) {

            $awk = explode(":", preg_replace('/\s+/', '', str_replace(' ', "", $check)));

            if (isset($awk[1]) and filter_var($awk[0],FILTER_VALIDATE_IP,FILTER_FLAG_IPV4) and preg_match("/^(0|([1-9]\d{0,3}|[1-5]\d{4}|[6][0-5][0-5]([0-2]\d|[3][0-5])))$/", $awk[1])) {
                return $awk[0] . ':' . $awk[1];
            }

        } else if ($check) {
            return $this->loop($check, 'ipport', $type);
        }

        return false;
    }

    function mapname ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check)) {

            $replaced = str_replace(array(' ', '.bsp'), '', $check);

            if (preg_match("/^[\w\-\.\_ \/]+$/", $replaced)) {
                return $replaced;
            }

        } else if ($check) {
            return $this->loop($check, 'mapname', $type);
        }

        return false;
    }

    function gamestring ($value, $type, $object = false){

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w\.\-\_]+$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'gamestring', $type);
        }

        return false;
    }

    function folder ($value, $type, $object = false){

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w\/\-\_\.]+$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'folder', $type);
        }

        return false;
    }

    function config($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w\/\-\_\.]+$/', $check)) {
            return $check;
        } else if ($check and is_string($check) and preg_match('/^[\w\/\-\_\.]+$/',urldecode($check))) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'config', $type);
        }

        return false;
    }

    function description ($value, $type, $object = false){

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check)) {

            $value = htmlentities($check, ENT_QUOTES, 'UTF-8');

            if (preg_match("/^[\x{0400}-\x{04FF}\w\r\n\-():;&.,% ]+/u", $value)) {
                return $value;
            }

        } else if ($check) {
            return $this->loop($check, 'description', $type);
        }

        return false;
    }

    // https://github.com/easy-wi/developer/issues/76 forking l4d(2) servers '#' needed
    function startparameter ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w\r\n\#\(\)\[\]\{\}\~\=\?\%\$\:\.\,\"+-\_\|ßöÖäÄüÜ ]+$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'startparameter', $type);
        }

        return false;
    }

    function externalID ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w\:]{0,255}+$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'externalID', $type);
        }

        return '';
    }

    function phone ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\d+\+\(\)\/\-\s]+$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'phone', $type);
        }

        return false;
    }

    # https://github.com/easy-wi/developer/issues/73
    function streetNumber ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w\.\-\/\ ]+$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'streetNumber', $type);
        }

        return false;
    }

    function id ($value, $length, $type, $object = false){

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\d+]{1,' . $length . '}$/', $check)) {
            return (int) $check;
        } else if ($check) {
            return $this->loop($check, 'id', $type, $length);
        }

        return false;
    }

    function timezone ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^1?[+-][\d+]$|^[+-][1][\d+]|^[+-][2][0-4]$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'timezone', $type);
        }

        return false;
    }

    function username ($value, $length, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w\-\.\@]{1,' . $length . '}$/', trim($check))) {
            return trim($check);
        } else if ($check) {
            return $this->loop($check, 'username', $type, $length);
        }

        return false;
    }

    function names ($value, $length, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check)) {

            if (strlen($check) <= $length and preg_match('/^[\p{L}\p{N}][\p{L}\p{N}  _.-]+$/u', $check)) {
                return $check;
            }

        } else if ($check) {
            return $this->loop($check, 'names', $type, $length);
        }

        return false;
    }

    function st ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[a-z]{2}$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'st', $type);
        }

        return false;
    }

    function smallletters ($value, $length, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[a-z]{1,' . $length . '}$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'smallletters', $type, $length);
        }

        return false;
    }

    function htmlcode ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check)) {
            return htmlentities($check, ENT_QUOTES, 'UTF-8');
        } else if ($check) {
            return $this->loop($check, 'htmlcode', $type);
        }

        return false;
    }

    function w ($value, $length, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w ]{1,' . $length . '}$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'w', $type, $length);
        }

        return false;
    }

    function date ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and @strtotime($check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'date', $type);
        }

        return false;
    }

    function base64 ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check) and preg_match('/^[\w]{1,}[=]{0,}+$/', $check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'base64', $type);
        }

        return false;
    }

    function escaped ($value, $type, $object = false) {

        $check = $this->if_obj_or_str($value, $type, $object);

        if ($check and is_string($check)) {
            return $check;
        } else if ($check) {
            return $this->loop($check, 'escaped', $type);
        }

        return false;
    }
}