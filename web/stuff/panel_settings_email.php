<?php
/**
 * File: panel_settings_email.php.
 * Author: Ulrich Block
 * Date: 05.01.13
 * Time: 10:29
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['settings'])) {
    redirect('login.php');
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('settings',$user_language,$reseller_id);
$gssprache = getlanguagefile('gserver',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id==0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller = 0;
}
if ($reseller_id != 0 and $admin_id != $reseller_id) {
    $reseller_id = $admin_id;
}
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('action', 'post') == 'md') {
    $changeCount = 0;
    $emailbackup=gzcompress($ui->escaped('emailbackup', 'post'),9);
    $emailbackuprestore=gzcompress($ui->escaped('emailbackuprestore', 'post'),9);
    $emaildown=gzcompress($ui->escaped('emaildown', 'post'),9);
    $emaildownrestart=gzcompress($ui->escaped('emaildownrestart', 'post'),9);
    $emailgserverupdate=gzcompress($ui->escaped('emailgserverupdate', 'post'),9);
    $emailpwrecovery=gzcompress($ui->escaped('emailpwrecovery', 'post'),9);
    $emailsecuritybreach=gzcompress($ui->escaped('emailsecuritybreach', 'post'),9);
    $emailnewticket=gzcompress($ui->escaped('emailnewticket', 'post'),9);
    $emailuseradd=gzcompress($ui->escaped('emailuseradd', 'post'),9);
    $emailvinstall=gzcompress($ui->escaped('emailvinstall', 'post'),9);
    $emailvrescue=gzcompress($ui->escaped('emailvrescue', 'post'),9);
    $emailregister=gzcompress($ui->escaped('emailregister', 'post'),9);
    $email_settings_host=($ui->domain('email_settings_host', 'post') or $ui->ip4('email_settings_host', 'post')) ? $ui->post['email_settings_host'] : 'localhost';
    $email_settings_password = $ui->password('email_settings_password',255, 'post');
    $email_settings_port = $ui->port('email_settings_port', 'post');
    $email_settings_ssl=($ui->w('email_settings_ssl',1, 'post')) ? $ui->w('email_settings_ssl',1, 'post') : 'N';
    $email_settings_type=($ui->w('email_settings_type',1, 'post')) ? $ui->w('email_settings_type',1, 'post') : 'P';
    $email_settings_user=($ui->ismail('email_settings_user', 'post')) ? $ui->ismail('email_settings_user', 'post') : $ui->username('email_settings_user',255, 'post');
    $query = $sql->prepare("UPDATE `settings` SET `emailregister`=?,`emailbackup`=?,`emailbackuprestore`=?,`emaildown`=?,`emaildownrestart`=?,`emailgserverupdate`=?,`emailpwrecovery`=?,`emailsecuritybreach`=?,`emailnewticket`=?,`emailuseradd`=?,`emailvinstall`=?,`emailvrescue`=?,`email_settings_host`=?,`email_settings_password`=AES_ENCRYPT(?,?),`email_settings_port`=?,`email_settings_ssl`=?,`email_settings_type`=?,`email_settings_user`=? WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($emailregister,$emailbackup,$emailbackuprestore,$emaildown,$emaildownrestart,$emailgserverupdate,$emailpwrecovery,$emailsecuritybreach,$emailnewticket,$emailuseradd,$emailvinstall,$emailvrescue,$email_settings_host,$email_settings_password,$aeskey,$email_settings_port,$email_settings_ssl,$email_settings_type,$email_settings_user,$reseller_id));
    $changeCount+=$query->rowCount();
    function updateemailxml($what,$postarray) {
        $changeCount = 0;
        global $sql,$reseller_id;
        if (isset($postarray["languages-$what"])) {
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('em',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
            foreach($postarray["languages-$what"] as $language) {
                if (small_letters_check($language, '2')) {
                    $xml = $postarray[$what.'_xml_'.$language];
                    $query->execute(array($what,$language,$xml,$reseller_id));
                    $changeCount+=$query->rowCount();
                }
            }
            $query = $sql->prepare("SELECT `lang` FROM `translations` WHERE `type`='em' AND `transID`=? AND `resellerID`=?");
            $query->execute(array($what,$reseller_id));
            $query2 = $sql->prepare("DELETE FROM `translations` WHERE `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (!in_array($row['lang'],(array)$postarray["languages-$what"])) {
                    $query2->execute(array($row['lang'],$what,$reseller_id));
                    $changeCount+=$query2->rowCount();
                }
            }
        } else {
            $query = $sql->prepare("DELETE FROM `translations` WHERE `lang`=? AND `resellerID`=?");
            $query->execute(array($what,$reseller_id));
            $changeCount+=$query->rowCount();
        }
        return $changeCount;
    }
    $changeCount+=updateemailxml('emailbackup',$ui->post);
    $changeCount+=updateemailxml('emailbackuprestore',$ui->post);
    $changeCount+=updateemailxml('emaildown',$ui->post);
    $changeCount+=updateemailxml('emaildownrestart',$ui->post);
    $changeCount+=updateemailxml('emailgserverupdate',$ui->post);
    $changeCount+=updateemailxml('emailpwrecovery',$ui->post);
    $changeCount+=updateemailxml('emailsecuritybreach',$ui->post);
    $changeCount+=updateemailxml('emailnewticket',$ui->post);
    $changeCount+=updateemailxml('emailuseradd',$ui->post);
    $changeCount+=updateemailxml('emailvinstall',$ui->post);
    $changeCount+=updateemailxml('emailvrescue',$ui->post);
    $changeCount+=updateemailxml('emailregister',$ui->post);
    $email = $ui->ismail('email', 'post');
    $emailregards = $ui->escaped('emailregards', 'post');
    $emailfooter = $ui->escaped('emailfooter', 'post');
    $query = $sql->prepare("UPDATE `settings` SET `email`=?,`emailregards`=?,`emailfooter`=? WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($email,$emailregards,$emailfooter,$reseller_id));
    $changeCount+=$query->rowCount();
    if ($changeCount==0) {
        $template_file = $spracheResponse->error_table;
    } else {
        $loguseraction="%mod% %emailsettings%";
        $insertlog->execute();
        $template_file = $spracheResponse->table_add;
    }
} else {
    $query = $sql->prepare("SELECT *,AES_DECRYPT(`email_settings_password`,?) AS `decryptedpassword` FROM `settings` WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($aeskey,$reseller_id));
    $usprache = getlanguagefile('user',$user_language,$reseller_id);
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $emailbackup=@gzuncompress($row['emailbackup']);
        $emailbackuprestore=@gzuncompress($row['emailbackuprestore']);
        $emaildown=@gzuncompress($row['emaildown']);
        $emaildownrestart=@gzuncompress($row['emaildownrestart']);
        $emailgserverupdate=@gzuncompress($row['emailgserverupdate']);
        $emailpwrecovery=@gzuncompress($row['emailpwrecovery']);
        $emailsecuritybreach=@gzuncompress($row['emailsecuritybreach']);
        $emailnewticket=@gzuncompress($row['emailnewticket']);
        $emailuseradd=@gzuncompress($row['emailuseradd']);
        $emailvinstall=@gzuncompress($row['emailvinstall']);
        $emailvrescue=@gzuncompress($row['emailvrescue']);
        $emailregister=@gzuncompress($row['emailregister']);
        $email = $row['email'];
        $emailregards = $row['emailregards'];
        $emailfooter = $row['emailfooter'];
        $email_settings_host = $row['email_settings_host'];
        $email_settings_password = $row['decryptedpassword'];
        $email_settings_port = $row['email_settings_port'];
        $email_settings_ssl = $row['email_settings_ssl'];
        $email_settings_type = $row['email_settings_type'];
        $email_settings_user = $row['email_settings_user'];
    }
    function getemailxml($what,$language) {
        global $sql,$reseller_id;
        $query = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='em' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($language,$what,$reseller_id));
        $xml = $query->fetchColumn();
        $style=($query->rowCount()==1) ? 1 : 0;
        return array('style' => $style,'lang' => $language,'xml' => $xml);
    }
    $emailbackup_xml = array();
    $emailbackuprestore_xml = array();
    $emaildown_xml = array();
    $emaildownrestart_xml = array();
    $emailgserverupdate_xml = array();
    $emailpwrecovery_xml = array();
    $emailsecuritybreach_xml = array();
    $emailnewticket_xml = array();
    $emailuseradd_xml = array();
    $emailvinstall_xml = array();
    $emailvrescue_xml = array();
    if (isset($template_to_use)) {
        foreach (getlanguages($template_to_use) as $row) {
            $emailbackup_xml[]=getemailxml('emailbackup', $row);
            $emailbackuprestore_xml[]=getemailxml('emailbackuprestore', $row);
            $emaildown_xml[]=getemailxml('emaildown', $row);
            $emaildownrestart_xml[]=getemailxml('emaildownrestart', $row);
            $emailgserverupdate_xml[]=getemailxml('emailgserverupdate', $row);
            $emailpwrecovery_xml[]=getemailxml('emailpwrecovery', $row);
            $emailsecuritybreach_xml[]=getemailxml('emailsecuritybreach', $row);
            $emailnewticket_xml[]=getemailxml('emailnewticket', $row);
            $emailuseradd_xml[]=getemailxml('emailuseradd', $row);
            $emailvinstall_xml[]=getemailxml('emailvinstall', $row);
            $emailvrescue_xml[]=getemailxml('emailvrescue', $row);
            $emailregister_xml[]=getemailxml('emailregister', $row);
        }
    }
    $template_file = "admin_settings_email.tpl";
}