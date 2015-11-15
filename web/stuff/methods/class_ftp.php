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
    public $ftpConnection = false, $ftpSecondConnection = false, $loggedIn = false, $secondLoggedIn = false, $tempHandle = null, $lastFileSize = 0;

    function __construct($ip, $port, $user, $pwd, $ssl = 'N') {

        $this->ftpConnection = ($ssl == 'N') ? @ftp_connect($ip, $port, 5) :  @ftp_ssl_connect($ip, $port, 5);

        if ($this->ftpConnection) {
            $ftpLogin = @ftp_login($this->ftpConnection, $user, $pwd);

            if ($ftpLogin) {

                $this->loggedIn = true;

                return true;
            }
        }

        return false;
    }

    public function removeSlashes ($string) {
        return str_replace(array('//', '///', '////'), '/', $string);
    }

    public function createSecondFTPConnect ($ip, $port, $user, $pwd, $ssl = 'N') {

        $this->ftpSecondConnection = ($ssl == 'N') ? @ftp_connect($ip, $port, 5) :  @ftp_ssl_connect($ip, $port, 5);

        if ($this->ftpSecondConnection) {
            $ftpLogin = @ftp_login($this->ftpSecondConnection, $user, $pwd);

            if ($ftpLogin) {

                $this->secondLoggedIn = true;

                return true;
            }
        }

        return false;
    }

    public function downloadToTemp ($pathAndFile, $lastBytes = 0, $files = false, $startAt = false) {

        $pathAndFile = $this->removeSlashes($pathAndFile);

        if (is_array($files)) {

            $this->tempHandle = array();

            foreach ($files as $file) {

                $arrayCombined = $this->removeSlashes($pathAndFile . '/' . $file);

                $fileSize = @ftp_size($this->ftpConnection, $arrayCombined);
                $this->lastFileSize = $fileSize;

                if ($fileSize != -1) {
                    $this->tempHandle[$file] = tmpfile();
                    @ftp_fget($this->ftpConnection, $this->tempHandle[$file], $arrayCombined, FTP_BINARY, 0);

                    fseek($this->tempHandle[$file], 0);
                }
            }

        }  else {

            $fileSize = @ftp_size($this->ftpConnection, $pathAndFile);
            $this->lastFileSize = $fileSize;

            if ($fileSize != -1) {

                if ($fileSize === $startAt) {
                    return true;
                }

                $startAtSize = ($lastBytes != 0 and $fileSize > $lastBytes) ? ($fileSize - $lastBytes) : 0;

                if ($startAt !== false and $startAt !== null and $fileSize > $startAt and ($fileSize - $startAt) > 0 and ($fileSize - $startAt) <= $lastBytes) {
                    $startAtSize = $startAt;
                }

                $this->tempHandle = tmpfile();

                $download = @ftp_fget($this->ftpConnection, $this->tempHandle, $pathAndFile, FTP_BINARY, $startAtSize);

                fseek($this->tempHandle, 0);

                if ($download) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getLastFileSize() {
        return $this->lastFileSize;
    }

    public function getTempFileContent () {

        if (is_array($this->tempHandle)) {

            $fileContentArray = array();

            foreach (array_keys($this->tempHandle) as $k) {

                fseek($this->tempHandle[$k], 0);

                $fstats = fstat($this->tempHandle[$k]);

                $fileContentArray[$k] = ($fstats['size'] > 0) ? fread($this->tempHandle[$k], $fstats['size']) : '';
            }

            return $fileContentArray;

        } else if (is_resource($this->tempHandle)) {

            $fstats = fstat($this->tempHandle);

            $startAtSize = ($fstats['size'] > 32768) ? ($fstats['size'] - 32768) : 0;
            fseek($this->tempHandle, $startAtSize);
            fseek($this->tempHandle, $startAtSize);


            return ($fstats['size'] > 0) ? fread($this->tempHandle, 32768) : '';

        }

        return false;
    }

    public function writeContentToTemp ($content) {

        if (is_array($this->tempHandle)) {
            return false;
        }

        if ($this->tempHandle === null) {
            $this->tempHandle = tmpfile();
        }

        $contentLength = strlen($content);

        if ($this->tempHandle and $contentLength > 0) {

            fwrite($this->tempHandle, $content, $contentLength);

            return true;

        }

        return false;
    }

    public function displayTemp () {
        return $this->tempHandle;
    }

    public function uploadFileFromTemp ($folders, $file = '', $secondConnection = true) {

        $useConnection = ($secondConnection === true) ? 'ftpSecondConnection' : 'ftpConnection';

        if (($secondConnection === false and $this->loggedIn) or ($secondConnection === true and $this->secondLoggedIn)) {

            if (is_array($this->tempHandle)) {

                $returns = array();

                $fileNames = array_keys($this->tempHandle);

                foreach ($fileNames as $k) {

                    $combinedFolders = $this->combineFolderFile($folders, $k);

                    $this->arrayToChDir($combinedFolders, $secondConnection);

                    fseek($this->tempHandle[$k], 0);

                    // only upload in case we have downloaded some data.
                    $fstats = fstat($this->tempHandle[$k]);

                    if ($fstats['size'] > 0) {

                        if (@ftp_fput($this->$useConnection, $this->fileNameFromPath($k), $this->tempHandle[$k], FTP_BINARY, 0)) {
                            $returns[] = true;
                        } else {
                            $returns[] = false;
                        }
                    }

                }

                if (!in_array(false, $returns)) {
                    return true;
                }

            } else {

                $combinedFolders = $this->combineFolderFile($folders, $file);
                $this->arrayToChDir($combinedFolders, $secondConnection);

                fseek($this->tempHandle, 0);

                // only upload in case we have downloaded some data.
                $fstats = fstat($this->tempHandle);

                if ($fstats['size'] > 0) {
                    if (@ftp_fput($this->$useConnection, $this->fileNameFromPath($file), $this->tempHandle, FTP_BINARY, 0)) {
                        return true;
                    }
                }

            }

        }

        return false;

    }

    private function fileNameFromPath ($fileWithPath) {

        $splitConfig = preg_split('/\//', $this->removeSlashes($fileWithPath), -1, PREG_SPLIT_NO_EMPTY);

        return $splitConfig[count($splitConfig) - 1];
    }

    private function combineFolderFile ($folders, $file) {

        $i = 0;

        $splitConfig = preg_split('/\//', $this->cleanSlashes($file), -1, PREG_SPLIT_NO_EMPTY);
        $folderFileCount = count($splitConfig) - 1;

        while ($i < $folderFileCount) {
            $folders .= '/' . $splitConfig[$i];
            $i++;
        }

        return $this->cleanSlashes($folders);

    }

    public function cleanSlashes ($folders) {
        while (substr($folders, -2) == '//') {
            $folders = substr($folders, 0, strlen($folders) -1 );
        }
        return $folders;
    }

    private function arrayToChDir ($folders, $secondConnection = true) {

        $useConnection = ($secondConnection === true) ? 'ftpSecondConnection' : 'ftpConnection';

        @ftp_chdir($this->$useConnection, '/');

        // only in case we cannot access the folder directly, loop and create
        if (!@ftp_chdir($this->$useConnection, $folders)) {

            // go back to home dir. otherwise we might create subfolders in wrong places
            @ftp_chdir($this->$useConnection, '/');

            foreach (preg_split('/\//', $folders, -1, PREG_SPLIT_NO_EMPTY) as $dir) {

                if (!@ftp_chdir($this->$useConnection, $dir)) {
                    @ftp_mkdir($this->$useConnection, $dir);
                    @ftp_chdir($this->$useConnection, $dir);
                }

            }
        }
    }

    public function checkPath ($ftpPath, $searchFor) {


        if ($ftpPath != '') {

            if (substr($ftpPath, 0, 1) != '/') {
                $ftpPath = '/' . $ftpPath;
            }

            @ftp_chdir($this->ftpConnection, $ftpPath);
        }

        $currentPath = @ftp_pwd($this->ftpConnection);

        if (strlen($searchFor) == 0 and $currentPath) {
            return $ftpPath;
        }

        if (substr($currentPath, strlen($searchFor) * (-1)) == $searchFor) {
            return $currentPath;
        }

        return false;
    }

    public function checkFolders ($dir, $searchFor, $maxDepth = false, $currentDepth = 0) {

        $folders = array();
        $donotsearch = array('bin', 'cfg', 'cl_dlls', 'dlls', 'gfx', 'hl2', 'manual', 'maps', 'materials', 'models', 'particles', 'recource', 'scenes', 'scripts', 'sound', 'sounds', 'textures', 'valve', 'reslists');

        $spl = strlen($searchFor) * (-1);

        if ($dir != '/') {

            if (substr($dir, 0, 1) != '/') {
                $dir = '/' . $dir;
            }

            $dir = $dir . '/';
        }

        $rawList = @ftp_rawlist($this->ftpConnection, $dir);

        if ($rawList) {
            foreach ($rawList as $d) {

                $list = preg_split('/(\s|\s+)/', $d, -1, PREG_SPLIT_NO_EMPTY);

                if (preg_match('/^d[rwx\-]{9}+$/', $list[0]) and !preg_match('/^[\.\/]{0,}Steam[\/]{0,}+$/', $list[count($list) - 1]) and !in_array($list[count($list) - 1], $donotsearch)) {

                    if (substr($dir . $list[count($list) - 1], $spl) == $searchFor) {
                        return $this->cleanSlashes($dir . $list[count($list) - 1]);
                    }

                    $folders[] = $dir . $list[count($list) - 1];

                    if (is_numeric($maxDepth) and $currentDepth < ($maxDepth + 1)) {

                        $array = $this->checkFolders($this->cleanSlashes($dir . $list[count($list) - 1]), $searchFor, $maxDepth, $currentDepth + 1);

                        if (is_array($array)) {
                            foreach ($array as $f){
                                if (substr($f, $spl) == $searchFor) {
                                    return $this->cleanSlashes($f);
                                }
                                $folders[] = $f;
                            }

                        } else if (substr($array, $spl) == $searchFor) {
                            return $this->cleanSlashes($array);
                        }
                    }
                }
            }
            return $folders;
        }

        return $this->cleanSlashes($dir);
    }

    public function getMapGroups () {

        $mapGroups = array();

        $contents = $this->getTempFileContent();
        if (!is_array($contents)) {
            $contents = array($contents);
        }

        foreach ($contents as $content) {

            @list($buffer, $mapgroupsRaw) = explode('"mapgroups"', $content);

            if ($mapgroupsRaw) {

                $mapGroupStarted = false;
                $mapGroupBlockStarted = false;
                $mapGroupMapsBlockStarted = false;

                $splitConfig = preg_split('/\n/',str_replace("\r", '', str_replace(array("\0", "\b", "\r", "\Z"), '', $mapgroupsRaw)) , -1, PREG_SPLIT_NO_EMPTY);

                foreach ($splitConfig as $line) {
                    if (isset($mapGroupStarted) and $mapGroupStarted) {

                        if ($mapGroupBlockStarted) {

                            if ($mapGroupMapsBlockStarted and preg_match('/^[\s+]{0,}[\}][\s+]{0,}$/', $line)) {
                                $mapGroupMapsBlockStarted = false;
                            } else if (!$mapGroupMapsBlockStarted and preg_match('/^[\s+]{0,}[\{][\s+]{0,}$/', $line)) {
                                $mapGroupMapsBlockStarted = true;
                            } else if (!$mapGroupMapsBlockStarted and preg_match('/^[\s+]{0,}[\}][\s+]{0,}$/', $line)) {
                                $mapGroupBlockStarted = false;
                            }

                        } else if (!$mapGroupBlockStarted and preg_match('/^[\s+]{0,}[\{][\s+]{0,}$/', $line)) {
                            $mapGroupBlockStarted = true;

                        } else if (substr_count($line, '"') == 2 and !$mapGroupBlockStarted and !$mapGroupMapsBlockStarted) {
                            $mapGroups[] = preg_replace('/\s/', '', str_replace('"', '', $line));

                        } else if (!$mapGroupBlockStarted and !$mapGroupMapsBlockStarted and preg_match('/^[\s+]{0,}[\}][\s+]{0,}$/', $line)) {
                            unset($mapGroupStarted);
                        }
                    } else if (isset($mapGroupStarted) and !$mapGroupStarted and preg_match('/^[\s+]{0,}[\{][\s+]{0,}$/', $line)) {
                        $mapGroupStarted = true;
                    }
                }
            }
        }

        natsort($mapGroups);

        return $mapGroups;

    }

    public function createDirs ($dirs) {

        if (!is_array($dirs)) {
            $dirs = (array) $dirs;
        }

        if ($this->loggedIn == true) {

            foreach ($dirs as $dir) {
                @ftp_mkdir($this->ftpConnection, $dir);
            }
        }

    }

    public function removeTempFiles () {
        if (is_array($this->tempHandle)) {
            foreach (array_keys($this->tempHandle) as $k) {
                if (is_resource($this->tempHandle[$k])) {
                    fclose($this->tempHandle[$k]);
                }
            }
        } else if (is_resource($this->tempHandle)) {
            fclose($this->tempHandle);
        }
    }

    public function logOut () {

        if (is_resource($this->ftpConnection)) {
            ftp_close($this->ftpConnection);
        }

        if (is_resource($this->ftpSecondConnection)) {
            ftp_close($this->ftpSecondConnection);
        }

        $this->removeTempFiles();
    }

    function __destruct() {

        if (is_resource($this->ftpConnection)) {
            ftp_close($this->ftpConnection);
        }

        if (is_resource($this->ftpSecondConnection)) {
            ftp_close($this->ftpSecondConnection);
        }

        $this->removeTempFiles();
    }
}