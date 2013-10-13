<?php

/**
 * File: ssh_exec.php.
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
        }

        if (isset($query)) {

            $query->execute(array(':serverID' => $id,':aeskey' => $aeskey));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $serverID = $row['id'];
                $resellerID = $row['resellerid'];
                $notified = $row['notified'];
                $ssh2IP = $row['ip'];
                $ssh2Port = $row['decryptedport'];
                $ssh2User = $row['decrypteduser'];
                $ssh2Pass = $row['decryptedpass'];
                $ssh2Publickey = $row['publickey'];

                # https://github.com/easy-wi/developer/issues/70
                $sshkey = removePub($row['keyname']);
                $pubkey = EASYWIDIR . '/keys/' . $sshkey . '.pub';
                $key = EASYWIDIR . '/keys/' . $sshkey;

                $ssh2Socket=($ssh2Publickey == 'Y') ? (file_exists($pubkey) and file_exists($key)) ? @ssh2_connect($ssh2IP, $ssh2Port, array('hostkey' => 'ssh-rsa')) : false : @ssh2_connect($ssh2IP, $ssh2Port);

                if ($ssh2Socket == true) {
                    $ssh2Connect=($ssh2Publickey == 'Y') ? @ssh2_auth_pubkey_file($ssh2Socket, $ssh2User, $pubkey, $key) : @ssh2_auth_password($ssh2Socket, $ssh2User, $ssh2Pass);

                    if ($ssh2Connect == true) {

                        if (!is_array($cmds)) {
                            $cmds = array($cmds);
                        }

                        foreach ($cmds as $c) {

                            if (is_string($c) and $c != '') {
                                $ssh2Stream = @ssh2_exec($ssh2Socket, $c);
                                stream_set_blocking($ssh2Stream, true);

                                if (!$ssh2Stream) {
                                    $error = true;
                                }

                                $return .= stream_get_contents($ssh2Stream);

                                fclose($ssh2Stream);
                            }
                        }

                    } else {
                        $error = true;
                    }

                    $ssh2Socket = null;

                } else {
                    $error = true;
                }

                if (isset($error)) {
                    $notified++;
                } else {
                    $notified = 0;
                }

                if ($notified == $rSA['down_checks']) {
                    $query=($resellerID==0) ? $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`=0 AND `accounttype`='a'") : $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=${resellerID} AND `id`=`resellerid`) OR `resellerid`=0 AND `accounttype`='a'");
                    $query->execute();
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        if ($row2['mail_serverdown'] == 'Y') {
                            sendmail('emaildown', $row2['id'], $ssh2IP,'');
                        }
                    }
                }

                if ($type == 'gs') {
                    $query = $sql->prepare("UPDATE `rserverdata` SET `notified`=? WHERE `id`=? LIMIT 1");
                } else if ($type == 'eac') {
                    $query = $sql->prepare("UPDATE `eac` SET `notified`=? WHERE `id`=? LIMIT 1");
                } else if ($type == 'vh') {
                    $query = $sql->prepare("UPDATE `virtualhosts` SET `notified`=? WHERE `id`=? LIMIT 1");
                }
                $query->execute(array($notified, $serverID));

                return $return;
            }
        }
        return false;
    }

    function ssh_check ($ssh2IP, $ssh2Port, $ssh2User, $sshPublickey, $sshKey, $ssh2Pass) {

        $sshKey = removePub($sshKey);
        $pubkey = EASYWIDIR . '/keys/' . $sshKey . '.pub';
        $key = EASYWIDIR . '/keys/' . $sshKey;

        $ssh2Socket=($sshPublickey == 'Y') ? (file_exists($pubkey) and file_exists($key)) ? @ssh2_connect($ssh2IP, $ssh2Port, array('hostkey' => 'ssh-rsa')) : false : @ssh2_connect($ssh2IP, $ssh2Port);

        if ($ssh2Socket == true) {
            $ssh2Connect=($sshPublickey == 'Y') ? @ssh2_auth_pubkey_file($ssh2Socket, $ssh2User, $pubkey, $key) : @ssh2_auth_password($ssh2Socket, $ssh2User, $ssh2Pass);

            $ssh2Socket = null;

            return ($ssh2Connect == true) ?  true : 'login';

        } else {
            return 'ipport';
        }
    }
}