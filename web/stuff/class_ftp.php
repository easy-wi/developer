<?php

/**
 * File: class_ftp.php.
 * Author: Ulrich Block
 * Date: 14.12.13
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

class EasyWiFTP {


    // define vars
    public $ftpConnection = null;
    private $tempHandle = null;

    function __construct($ip, $port, $user, $pwd) {

        @ini_set('default_socket_timeout', 5);

        $this->ftpConnection = @ftp_connect($ip, $port);

        if ($this->ftpConnection) {
            $ftpLogin = @ftp_login($this->ftpConnection, $user, $pwd);

            if ($ftpLogin) {
                return true;
            }
        }

        return false;

    }

    public function downloadToTemp ($pathAndFile, $startAt = 0) {

        // Check if file exists by getting size
        $fileSize = @ftp_size($this->ftpConnection, $pathAndFile);

        if ($fileSize != -1) {

            $startAtSize = ($startAt != 0 and $fileSize > $startAt) ? ($fileSize - $startAt) : 0;

            // now we have a connection and filesize so we can create a local temp file and start downloading
            $this->tempHandle = tmpfile();

            $download = @ftp_fget($this->ftpConnection, $this->tempHandle, $pathAndFile, FTP_BINARY, $startAtSize);

            if ($download) {
                return true;
            }

        }

        return false;
    }

    public function getTempFileContent () {

        fseek($this->tempHandle, 0);

        $fstats = fstat($this->tempHandle);

        return ($fstats['size'] > 0) ? fread($this->tempHandle, $fstats['size']) : '';
    }

    public function uploadFileFromTemp () {

    }

    function __destruct() {

        if ($this->ftpConnection) {
            ftp_close($this->ftpConnection);
        }

        if ($this->tempHandle) {
            fclose($this->tempHandle);
        }
    }
}