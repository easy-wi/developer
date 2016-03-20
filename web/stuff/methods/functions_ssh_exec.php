<?php

/**
 * File: functions_ssh_exec.php.
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

// Include PHPSeclib if not already included
if (!class_exists('SSH2')) {
    include(EASYWIDIR . '/third_party/phpseclib/autoloader.php');
}

if (!function_exists('ssh2_execute')) {

    function ssh2_execute($type, $id, $cmds) {

        global $sql, $rSA, $aeskey;

        $return = '';

        if ($type == 'eac') {
            $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass` FROM `eac` WHERE resellerid=:serverID LIMIT 1");

        } else if ($type == 'gs') {
            $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `decryptedsteamAccount`,AES_DECRYPT(`steamPassword`,:aeskey) AS `decryptedsteamPassword` FROM `rserverdata` WHERE `id`=:serverID LIMIT 1");

        } else if ($type == 'vh') {
            $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass` FROM `virtualhosts` WHERE `id`=:serverID LIMIT 1");

        } else if ($type == 'vd') {
            $query = $sql->prepare("SELECT *,`ssh2ip` AS `ip`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedpass` FROM `voice_tsdns` WHERE `id`=:serverID LIMIT 1");

        } else if ($type == 'vm') {
            $query = $sql->prepare("SELECT *,`ssh2ip` AS `ip`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedpass` FROM `voice_masterserver` WHERE `id`=:serverID LIMIT 1");
        }

        if (isset($query)) {

            $query->execute(array(':serverID' => $id,':aeskey' => $aeskey));

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $serverID = (int) $row['id'];
                $resellerID = (int) $row['resellerid'];
                $notified = (int) $row['notified'];
                $ssh2IP = (string) $row['ip'];
                $ssh2Port = (int) $row['decryptedport'];
                $ssh2User = (string) $row['decrypteduser'];
                $ssh2Publickey = (string) $row['publickey'];
                $ssh2DecryptedPass = (string) $row['decryptedpass'];
                $ssh2KeyName = (string) $row['keyname'];

                # https://github.com/easy-wi/developer/issues/70
                $privateKey = EASYWIDIR . '/keys/' . removePub($ssh2KeyName);

                $sshObject = new phpseclib\Net\SSH2($ssh2IP, $ssh2Port);

                if ($ssh2Publickey != 'N') {

                    $ssh2Pass = new phpseclib\Crypt\RSA();

                    if ($ssh2Publickey == 'B') {
                        $ssh2Pass->setPassword($ssh2DecryptedPass);
                    }

                    $ssh2Pass->loadKey(file_get_contents($privateKey));

                } else {
                    $ssh2Pass = $ssh2DecryptedPass;
                }

                if ($sshObject->login($ssh2User, $ssh2Pass)) {

                    $notified = 0;

                    if (!is_array($cmds)) {
                        $cmds = array($cmds);
                    }

                    foreach ($cmds as $c) {
                        if (is_string($c) and $c != '') {
                            $return .= $sshObject->exec($c);
                        }
                    }

                } else {
                    $notified++;
                }

                if ($notified == $rSA['down_checks']) {
                    $query = ($resellerID == 0) ? $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`=0 AND `accounttype`='a'") : $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=${resellerID} AND `id`=`resellerid`) OR `resellerid`=0 AND `accounttype`='a'");
                    $query->execute();
                    while ($row2 = $query->fetch(PDO::FETCH_ASSOC)) {
                        if ($row2['mail_serverdown'] == 'Y') {
                            sendmail('emaildown', $row2['id'], $ssh2IP, '');
                        }
                    }
                }

                if ($type == 'gs') {
                    $query = $sql->prepare("UPDATE `rserverdata` SET `notified`=? WHERE `id`=? LIMIT 1");
                } else if ($type == 'eac') {
                    $query = $sql->prepare("UPDATE `eac` SET `notified`=? WHERE `id`=? LIMIT 1");
                } else if ($type == 'vh') {
                    $query = $sql->prepare("UPDATE `virtualhosts` SET `notified`=? WHERE `id`=? LIMIT 1");
                } else if ($type == 'vd') {
                    $query = $sql->prepare("UPDATE `voice_tsdns` SET `notified`=? WHERE `id`=? LIMIT 1");
                } else if ($type == 'vm') {
                    $query = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=? WHERE `id`=? LIMIT 1");
                }
                $query->execute(array($notified, $serverID));

                if (strlen($return) == 0) {
                    $return = true;
                }

                return ($notified == 0) ? $return : false;
            }
        }

        return false;
    }

    function ssh_check ($ssh2IP, $ssh2Port, $ssh2User, $sshPublickey, $sshKey, $ssh2Pass) {

        $privateKey = EASYWIDIR . '/keys/' . removePub($sshKey);

        $sshObject = new phpseclib\Net\SSH2($ssh2IP, $ssh2Port);

        if ($sshPublickey != 'N') {

            $key = new phpseclib\Crypt\RSA();

            if ($sshPublickey == 'B') {
                $key->setPassword($ssh2Pass);
            }

            $key->loadKey(file_get_contents($privateKey));

            $ssh2Pass = $key;
        }

        return ($sshObject->login($ssh2User, $ssh2Pass)) ?  true : 'login';
    }
}