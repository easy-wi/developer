<?php

/**
 * File: queries.php.
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
function GTASAMP ($ip,$port) {
	$socket = @fsockopen('udp://' . $ip, (int) $port, $errnum, $errstr, 5);
	if ($errnum == 111) {
        return $errstr;
	} else if ($socket==false) {
		usleep(250000);
        $socket = @fsockopen('udp://' . $ip, (int) $port, $errnum, $errstr, 5);
	}
    if($socket === false) {
        return $errstr;
    } else {
		$ex = explode('.', $ip);
		$packet = 'SAMP' . chr($ex[0]) . chr($ex[1]) . chr($ex[2]) . chr($ex[3]) . chr($port & 0xFF) . chr($port >> 8 & 0xFF) . 'i';
		if (@fwrite($socket, $packet)) {
            @fread($socket, 11);
            $return = array('password'=>ord(fread($socket,1)), 'players'=>ord(fread($socket,2)), 'slots'=>ord(fread($socket,2)), 'hostname'=>htmlentities(fread($socket,ord(fread($socket,4)))), 'mode'=>htmlentities(fread($socket,ord(fread($socket,4)))), 'map'=>htmlentities(fread($socket,ord(fread($socket,4)))));
        } else {
            $return = 'Error: can not write to the socket';
        }
	}
	if (is_resource($socket)) {
		fclose($socket);
	}
    return $return;
}
function MineCraft ($ip,$port) {
	$socket=@fsockopen($ip,(int)$port,$errnum,$errstr,5);
	if ($errnum==111) {
		return $errstr;
	} else if ($socket==false) {
		usleep(250000);
		$socket=@fsockopen($ip,(int)$port,$errnum,$errstr,5);
	}
	if($socket === false) {
		return $errstr;
	} else {
		$string="\xFE";
		$length=strlen($string);
		if (@fwrite($socket,$string,$length)) {
            $mc_reply=@fread($socket,4096);
            $mc_reply=substr($mc_reply,3);
            $mc_reply=iconv('UTF-16BE', 'UTF-8',$mc_reply);
            if($mc_reply[1] === "\xA7" and $mc_reply[2] === "\x31") {
                print "New\r\n";
                $exploded=explode("\x00",$mc_reply);
                $return=array('hostname'=>$exploded[3], 'players'=>intval($exploded[4]), 'slots'=>intval($exploded[5]));
            } else {
                $exploded=explode("\xA7",$mc_reply);
                $hostname=substr($exploded[0],0,-1);
                print "Oldname: $hostname\r\n";
                $return=array('hostname'=>$hostname, 'players'=>(int)$exploded[1], 'slots'=>(int)$exploded[2]);
            }
            if (!isset($exploded[1]) and !isset($exploded[2])) {
                $return='Error: Can not retrieve data from MC Server';
            }
        } else {
            $return='Error: can not write to the socket';
        }
	}
    if (is_resource($socket)) fclose($socket);
    return $return;
}