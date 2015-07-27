<?php

/**
 * File: versioncheck.php.
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

if ((!isset($admin_id) or $main != 1) or !isset($reseller_id) or $reseller_id != 0 or !$pa['updateEW']) {
    header('Location: admin.php');
    die;
}

$loguserid = $admin_id;
$logusername = getusername($admin_id);
$sprache = getlanguagefile('licence', $user_language, $reseller_id);
$logusertype = 'admin';
$logreseller = 0;
$logsubuser = 0;

if ($ui->st('d', 'get') == 'ud' and $reseller_id == 0 and $pa['updateEW'] and ($ewVersions['cVersion'] < $ewVersions['version'] or $ewVersions['files'] < $ewVersions['version'])) {

    if ($ui->w('action', 4, 'post') == 'ud') {

        $updateinclude = true;

        class UpdateResponse {
            public $response=array(),$errors = array();
            function __construct() {
                $this->response = array();
            }
            function add ($newtext) {
                $this->response[] = $newtext;
            }
            function addError ($newtext) {
                $this->errors[] = $newtext;
            }
            function printresponse () {
                return $this->response;
            }
            function printErrors () {
                return $this->errors;
            }
            function __destruct() {
                unset($this->response, $this->errors);
            }
        }

        function rmr($dir) {
            if (is_dir($dir)) {
                $dircontent = scandir($dir);
                foreach ($dircontent as $c) {
                    if ($c != '.' and $c != '..' and is_dir($dir . '/' . $c)) {
                        rmr($dir . '/' . $c);
                    } else if ($c != '.' and $c != '..') {
                        unlink($dir . '/' . $c);
                    }
                }
                rmdir($dir);
            } else {
                @unlink($dir);
            }
        }

        $response = new UpdateResponse();

        if (!is_dir(EASYWIDIR . '/tmp')) {
            @mkdir(EASYWIDIR . '/tmp');
        }

        if (is_dir(EASYWIDIR . '/tmp')) {
            $response->add('Creating tempfolder <b>tmp/</b>');

            $opts = stream_context_create(array('http' => array('method' => 'GET','header' => "Accept-language: en\r\nUser-Agent: ".$ui->server['HTTP_HOST']."\r\n")));

            $response->add('Downloading: '. $licenceDetails['v'] . '.zip');

            $fp = @fopen('http://update.easy-wi.com/ew/' . $licenceDetails['v'] . '.zip', 'rb', false, $opts);
            $zip = @fopen(EASYWIDIR . '/tmp/' . $licenceDetails['v'] . '.zip', 'wb');

            if ($fp == true and $zip == true) {

                while (!feof($fp)){
                    fwrite($zip, fread($fp, 8192));
                }

                fclose($fp);
                fclose($zip);

                $response->add('Unpacking archive: '. $licenceDetails['v'] . '.zip');

                $zo = @zip_open(EASYWIDIR . '/tmp/'. $licenceDetails['v'] . '.zip');

                if (is_resource($zo)) {

                    while ($ze = zip_read($zo)) {

                        $name = zip_entry_name($ze);
                        $zeo = zip_entry_open($zo, $ze, 'r');

                        if (preg_match('/^(.*)\.[\w]{1,}$/', $name)) {

                            $folders = preg_split('/\//',$name,-1,PREG_SPLIT_NO_EMPTY);
                            $count = count($folders) - 1;
                            $i = 0;

                            unset($checkfolder);

                            while ($i < $count) {

                                if (isset($checkfolder)) {
                                    $checkfolder .= '/' . $folders[$i];
                                } else {
                                    $checkfolder = $folders[$i];
                                }

                                $i++;
                            }

                            if (isset($checkfolder) and $checkfolder!='' and !is_dir(EASYWIDIR . '/' . $checkfolder) and !is_file(EASYWIDIR . '/' . $checkfolder)) {

                                @mkdir($checkfolder);

                                if (is_dir(EASYWIDIR . '/' . $checkfolder)) {
                                    $response->add('Creating new folder: '.$checkfolder);
                                } else {
                                    $response->addError('Cannot create the folder <b>'.EASYWIDIR . '/' . $checkfolder . '</b>');
                                }

                            }

                        } else if (!is_dir(EASYWIDIR . '/' . $name) and !is_file(EASYWIDIR . '/' . $name)) {

                            @mkdir(EASYWIDIR . '/' . $name);

                            if (is_dir(EASYWIDIR . '/' . $name)) {
                                $response->add('Creating new folder: '.$name);
                            } else {
                                $response->addError('Cannot create the folder <b>'.EASYWIDIR . '/' . $name . '</b>');
                            }

                        }

                        if (preg_match('/^(.*)\.[\w]{1,}$/', $name) and $zeo) {

                            $nf = @fopen($name,'w');
                            if ($nf) {
                                $fz = zip_entry_filesize($ze);

                                if ($fz > 0) {
                                    if (@fwrite($nf, zip_entry_read($ze, $fz), $fz)) {
                                        $response->add('Unpacking: '. $name);
                                    } else {
                                        $response->addError('Unpacking: '. $name);
                                    }
                                } else {
                                    $response->addError('Unpacking: '. $name);
                                }

                                zip_entry_close($ze);
                                if (is_resource($nf)) {
                                    fclose($nf);
                                }
                            } else {
                                $response->addError('Unpacking: '. $name);
                            }
                        }
                    }

                    zip_close($zo);

                } else {
                    $response->addError('Cannot open the update archive <b>' . $licenceDetails['v'] . '.zip</b>');
                }

                $sql->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);

                if (!isset($alreadyRepaired)) {
                    $response->add('Adding tables if needed.');
                    include(EASYWIDIR . '/stuff/methods/tables_add.php');
                }

                if ($ewVersions['cVersion'] < $ewVersions['version'] and is_file(EASYWIDIR . '/install/update.php')) {
                    include(EASYWIDIR . '/install/update.php');
                } else {
                    $response->addError('Can not open: '. EASYWIDIR . '/install/update.php');
                }

                if (!isset($alreadyRepaired)) {
                    $response->add('Repairing tables if needed.');
                    include(EASYWIDIR . '/stuff/methods/tables_repair.php');
                }

            }
            if ($fp != true) {
                $response->add('Error: could not retrieve the update');
            }
            if ($zip != true) {
                $response->add('Error: could not create the temporary zip file');
            }

            foreach (scandir(EASYWIDIR . '/tmp/') as $c) {
                if ($c != '.' and $c != '..') {
                    rmr(EASYWIDIR . '/tmp/' . $c);
                }
            }

            rmr(EASYWIDIR . '/install/');

            if (is_file(EASYWIDIR . '/tmp/' . $licenceDetails['v'] . '.zip')) {
                $response->addError('Cannot remove the content from tempfolder <b>tmp/</b>');
            } else {
                $response->add('Removed temporary files from tempfolder');
            }

        } else {
            $response->addError('Cannot create the tempfolder <b>tmp/</b>');
        }

        if (count($response->errors)>0) {
            $template_file = 'Errors: '.implode('<br />',$response->errors);
        }

        if (isset($template_file)) {
            $template_file .= ' <br/>'.implode('<br />',$response->printresponse());
        } else {
            $template_file = $response->printresponse();
        }

    } else if (isset($ewVersions)) {
        $template_file = 'admin_versioncheck_ud.tpl';
    }

} else {

    $table = array();

    if ($user_language == 'de') {
        $column = 'de';
        $release = 'releasenotesDE';
    } else {
        $column = 'en';
        $release = 'releasenotesEN';
    }

    $release = '<div class="right"><a href="https://easy-wi.com/forum/showthread.php?tid='.$ewVersions[$release] . '" target="_blank">'.$vcsprache->releaseNotes . '</a></div>';

    $query = $sql->prepare("SELECT `version`,`$column` FROM `easywi_version` ORDER BY `id` DESC");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        if ($row[$column] != null and $row[$column] != '') {
            $table[] = array('version' => $row['version'], 'text' => $row[$column]);
        }
    }

    $update = ($reseller_id == 0 and isset($pa['updateEW']) and $pa['updateEW'] == true) ? '<div class="right"><a href="admin.php?w=vc&amp;d=ud">Update</a></div>' : '';

    if ($ewVersions['cVersion'] < $ewVersions['version']) {
        $state = 1;
        $class = 'versioncheckbad';
        $isok = $vcsprache->outdated . ' ' . $ewVersions['cVersion'] . ' ' . $vcsprache->latestversion . ' ' . $ewVersions['version'] . '.' . $release . ' ' . $update;

    } else if ($ewVersions['files'] < $ewVersions['version']) {
        $state = 1;
        $class = 'versioncheckbad';
        $isok = $vcsprache->filesoutdated . ' ' . $ewVersions['cVersion'] . '. '.$vcsprache->latestversion . ' ' . $ewVersions['version'] . '.' . $release . ' ' . $update;

    } else {
        $state = 2;
        $class = 'versioncheckok';
        $isok = $vcsprache->ok . ' ' . $ewVersions['cVersion'] . '.';
    }

    if ($reseller_id == 0){
        $rowspan = 8;
        $contract = $licenceDetails['c'];

    } else {
        $rowspan = 4;
    }

    $template_file = 'admin_versioncheck.tpl';
}