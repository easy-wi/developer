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


function serverQuery ($ip, $port, $type) {
	$socket = @fsockopen('udp://' . $ip, (int) $port, $errnum, $errstr, 5);
	if ($errnum == 111) {
        return $errstr;
        
	} else if ($socket == false) {
		usleep(250000);
        $socket = @fsockopen('udp://' . $ip, (int) $port, $errnum, $errstr, 5);
	}
    
    if($socket === false) {
        return $errstr;
        
    } else {

        //5 seconds read timeout
        stream_set_timeout($socket, 5);

        stream_set_blocking($socket, true);

        if ($type == 'gtasamp') {
            $ex = explode('.', $ip);
            $packet = 'SAMP' . chr($ex[0]) . chr($ex[1]) . chr($ex[2]) . chr($ex[3]) . chr($port & 0xFF) . chr($port >> 8 & 0xFF) . 'i';
            
            if (@fwrite($socket, $packet)) {
                @fread($socket, 11);
                return array(
                    'password' => ord(fread($socket, 1)),
                    'players' => ord(fread($socket, 2)),
                    'slots' => ord(fread($socket, 2)),
                    'hostname' => htmlentities(fread($socket, ord(fread($socket, 4)))),
                    'mode' => htmlentities(fread($socket, ord(fread($socket, 4)))),
                    'map' => htmlentities(fread($socket, ord(fread($socket, 4))))
                );
            } else {
                return 'Error: can not write to the socket';
            }
            
        } else if ($type == 'mtasa') {


            fwrite($socket, 's');

            $reply = fread($socket, 4096);

            if (substr($reply, 0, 4) == 'EYE1') {

                // Reply will have format like (without spaces):
                // EYE1 EOT mta ACK 22003 DC3 Default MTA Server BEL MTA:SA ENQ None EOT 1.3 STX 0 STX 0 STX 12 SOH

                // Remove EYE1
                $reply = substr($reply,4);

                // We do not need player details
                @list($reply) = explode(chr(1), $reply);

                $i = 0;
                $parts = array();
                while($reply != '') {
                    $length = ord(substr($reply, 0, 1));
                    $parts[$i] = substr($reply, 1, $length - 1);
                    $reply = substr($reply, $length);
                    $i++;
                }

                return array(
                    'password' => $parts[6],
                    'players' => $parts[7],
                    'slots' => $parts[8],
                    'hostname' => htmlentities($parts[2]),
                    'mode' => htmlentities($parts[3]),
                    'map' => htmlentities($parts[4])
                );
            }


        } else if ($type == 'minecraft') {

            $string="\xFE";
            $length=strlen($string);
            if (@fwrite($socket, $string, $length)) {
                
                $reply = @fread($socket, 4096);
                
                if (is_resource($socket)) {
                    fclose($socket);
                }
                
                $reply = substr($reply,3);
                $reply = iconv('UTF-16BE', 'UTF-8', $reply);
                
                if ($reply[1] === "\xA7" and $reply[2] === "\x31") {
                    
                    $exploded = explode("\x00", $reply);

                    if (!isset($exploded[3]) and !isset($exploded[4])) {
                        return 'Error: Can not retrieve data from MC Server';
                    }

                    return array('hostname' => $exploded[3], 'players' => intval($exploded[4]), 'slots' => intval($exploded[5]));
                    
                } else {

                    $exploded = explode("\xA7", $reply);
                    if (!isset($exploded[1]) and !isset($exploded[2])) {
                        return 'Error: Can not retrieve data from MC Server';
                    }

                    return array('hostname' => substr($exploded[0], 0, -1), 'players' => (int) $exploded[1], 'slots' => (int) $exploded[2], 'password' => 1, 'map' => '');
                }

            } else {
                return 'Error: can not write to the socket';
            }

        } else if ($type == 'teeworlds') {

            if (@fwrite($socket, "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\x67\x69\x65\x33\x05")) {

                $reply = @fread($socket, 2048);

                if (is_resource($socket)) {
                    fclose($socket);
                }

                $exploded = explode("\x00", $reply);

                if (!isset($exploded[2]) and !isset($exploded[3])) {
                    return 'Error: Can not retrieve data from Teeworlds Server';
                }

                return array('hostname' => $exploded[2], 'players' => (int) $exploded[8], 'slots' => (int) $exploded[9], 'password' => 1, 'map' => $exploded[3]);

            } else {
                return 'Error: can not write to the socket';
            }
        }
	}
    return '';
}