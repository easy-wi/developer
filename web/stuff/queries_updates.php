<?php

/**
 * File: queries_updates.php.
 * Author: Ulrich Block
 * Date: 03.10.13
 * Time: 21:35
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

function getHttpHeaders ($url) {

    $url = str_replace('http://', '', $url);
    $splitUrl = preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
    $domain = $splitUrl[0];

    $path = '';
    $i = 1;
    while ($i < count($splitUrl)) {
        $path .= '/' . $splitUrl[$i];
        $i++;
    }
    $fp = @fsockopen($domain, 80, $errno, $errstr,5);

    $buffer = '';

    if ($fp) {

        $send = "GET $path HTTP/1.1\r\n";
        $send .= "Host: $domain\r\n";
        $send .= "User-Agent: easy-wi.com\r\n";
        $send .= "Connection: Close\r\n\r\n";

        fwrite($fp, $send);

        // small chunks and break out the loop once we have the header
        while (!feof($fp) and strpos($buffer, "\r\n\r\n") === false) {
            $buffer .= fgets($fp, 128);
            if (strpos($buffer, "\r\n\r\n") !== false) {
                break;
            }
        }

        fclose($fp);

        // finally a 200 instead of 301 or 302
        if (strpos(strtolower($buffer), 'http/1.1 200') !== false or strpos(strtolower($buffer), 'http/1.0 200') ==! false) {

            return array('code' => 200, 'url' => $url);

        } else if (strpos(strtolower($buffer), 'http/1.1 302') !== false or strpos(strtolower($buffer), 'http/1.0 302') !== false or strpos(strtolower($buffer), 'http/1.1 301') !== false or strpos(strtolower($buffer), 'http/1.0 301') !== false) {

            // Header parsing, the Location attribute is what we want
            foreach (explode("\r\n", $buffer) as $info) {
                @list($key, $value) = explode(': ', $info);
                if ($key == 'Location') {
                    return array('code' => 300, 'url' => $value);
                }
            }
        }
    }

    return array('code' => 600, 'url' => $url);
}

function getCraftBukkitVersion () {

    // CaftBukkit does not offer an API where we can retrieve the current version
    // But they have redirect in place. So lets follow them and cut out the version

    $response = getHttpHeaders('dl.bukkit.org/latest-rb/craftbukkit.jar');
    while (isset($response['code']) and $response['code'] == 300) {
        $response = getHttpHeaders($response['url']);
    }

    $split = preg_split('/\//', $response['url'], -1, PREG_SPLIT_NO_EMPTY);
    return array('version' => $split[count($split) - 2], 'downloadPath' => 'http://' . $response['url']);
}

function getMinecraftVersion($release = 'release') {

    $responseBody = webhostRequest('s3.amazonaws.com', 'https://easy-wi.com', '/Minecraft.Download/versions/versions.json');

    $json = @json_decode(cleanFsockOpenRequest($responseBody, '{', '}'));

    return ($json) ? array('version' => $json->latest->$release, 'downloadPath' => 'http://s3.amazonaws.com/Minecraft.Download/versions/' . $json->latest->$release . '/minecraft_server.' . $json->latest->$release . '.jar') : array('version' => '', 'downloadPath' => '');
}