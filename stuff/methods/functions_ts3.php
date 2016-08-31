<?php
/**
 * File: functions_ts3.php.
 * Author: Ulrich Block
 * Date: 29.12.13
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
    include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
}

function tsbackup ($action, $sshuser, $path, $ts3MasterID, $virtualserver_id, $backupid, $move = array()) {

    $split_config = preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);
    $folderfilecount = count($split_config) - 1;
    $i = 0;
    
    if(!isset($path) || empty($path)){
     $folders = '/home/'.$sshuser . '/';
    }else{
     $folders = (substr($path, 0, 1) == '/') ? '/' : '/home/'.$sshuser . '/';
    }
    

    while ($i <= $folderfilecount) {
        $folders .= $split_config[$i] . '/';
        $i++;
    }

    if ($folders == '') {
        $folders='.';
    }

    if (substr($folders, -1) != '/') {
        $folders = $folders . '/';
    }

    $filefolder = $folders . 'files/virtualserver_' . $virtualserver_id . '/';

    $backupfolder = $folders . 'backups/virtualserver_' . $virtualserver_id . '/';

    if ($action == 'create') {

        $function = 'function backup () { mkdir -p ' . $backupfolder . ' && nice -n +19 tar cfPj ' . $backupfolder . $backupid . '.tar.bz2 ' . $filefolder . '; }';

    } else if ($action == 'delete') {

        $function = 'function backup () { nice -n +19 rm -f ' . $backupfolder . $backupid . '.tar.bz2; }';

    } else if ($action == 'deploy') {

        $function = 'function backup () { nice -n +19 rm -rf ' . $filefolder . '* && nice -n +19 tar xfPj ' . $backupfolder . $backupid . '.tar.bz2 -C /';

        if (count($move) > 0) {
            foreach ($move as $o => $n) {
                $function .= ' && mv ' . $o . ' ' . $n;
            }
        }

        $function .= '; }';

    }

    if (isset($function)) {

        $ssh2cmd = 'cd ' . $folders . ' && ' . $function . '; backup& ';
        if (ssh2_execute('vm', $ts3MasterID, $ssh2cmd) !== false) {
            return 'ok';
        } else {
            return 'The login data does not work';
        }

    }

    return 'Incorrect action';

}

function tsdns ($action, $sship, $sshport, $sshuser, $keyuse, $sshkey, $sshpw, $notified, $path, $bitversion, $tsip, $tsport, $tsdns, $reseller_id, $maxnotified = 2) {

    global $sql;

    $sshSftpObject = new phpseclib\Net\SFTP($sship, $sshport);

    if ($keyuse != 'N') {

        $privateKey = EASYWIDIR . '/keys/' . removePub($sshkey);

        $key = new phpseclib\Crypt\RSA();

        if ($keyuse == 'B') {
            $key->setPassword($sshpw);
        }

        $keyContent = @file_get_contents($privateKey);

        if ($keyContent) {
            $key->loadKey($keyContent);
        }
        
        $sshpw = $key;
    }

    if ($sshSftpObject->login($sshuser, $sshpw)) {


        $split_config = preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);
        $folderfilecount = count($split_config) - 1;

        $i = 0;

        $folders = (substr($path,0,1) == '/') ? '/' : '';
        $lastFolder = '';

        while ($i <= $folderfilecount) {
            $folders .= $split_config[$i] . '/';
            $lastFolder = $split_config[$i];
            $i++;
        }

        if ($lastFolder != 'tsdns' or substr($path, 0, 1) != '/') {
            $folders .= 'tsdns/';
        }

        if ($action == 'md' or $action == 'dl') {

            $newip = $tsip[0];
            $oldip = (isset($tsip[1])) ? $tsip[1] : '';

            $newport = $tsport[0];
            $oldport = (isset($tsport[1])) ? $tsport[1] : '';

            $newdns = $tsdns[0];
            $olddns = (isset($tsdns[1])) ? $tsdns[1] : '';

        } else {
            $dnsarray = array();
        }

        $file = (substr($path,0,1) == '/') ? $folders . 'tsdns_settings.ini' : '/home/' . $sshuser . '/' . $folders . 'tsdns_settings.ini';

        if ($action != 'rs') {
            $data = $sshSftpObject->get($file);
            $data = str_replace(array("\0", "\b", "\r", "\Z"), '', $data);
        }

        if ($action != 'rs' and $action != 'mw') {

            $edited = false;
            $ca = array();

            foreach (preg_split('/\n/', $data, -1, PREG_SPLIT_NO_EMPTY) as $configLine) {

                if ($action != 'li' and $configLine != $olddns . '=' . $oldip . ':' . $oldport and $configLine != $newdns . '=' . $newip . ':' . $newport) {
                    $ca[] = $configLine . "\r\n";
                } else if ($action == 'md' and $edited == false and ($configLine == $olddns . '=' . $oldip . ':' . $oldport or $configLine == $newdns . '=' . $newip . ':' . $newport)) {
                    $edited = true;
                    $ca[] = $newdns . '=' . $newip . ':' . $newport . "\r\n";
                }

                if ($action == 'li' and $configLine != '' and !preg_match('/^#(|\s+)(.*)$/', $configLine)) {
                    $dnsconfig = explode('=', $configLine);
                    if (isset($dnsconfig[1])) {
                        $linedns = $dnsconfig[0];
                        $lineserver = $dnsconfig[1];
                        $dnsarray[$lineserver] = $linedns;
                    }
                }
            }

            if ($action == 'md' and $edited == false) {
                $ca[] = $newdns . '=' . $newip . ':' . $newport . "\r\n";
            }

            if ($action != 'li') {

                $ca = array_unique($ca);
                sort($ca);

                $newcfg = '';

                foreach ($ca as $line) {
                    $newcfg .= $line;
                }

                if ($newcfg == '') {
                    $newcfg = '# No TSDNS data entered';
                }

                $sshSftpObject->put($file, $newcfg);

            }
        }

        if ($action == 'mw' and isset($data)) {

            $usedIPs = array();

            foreach (preg_split('/\n/', $data,-1,PREG_SPLIT_NO_EMPTY) as $configLine) {

                if ($configLine != '' and !preg_match('/^#(|\s+)(.*)$/', $configLine)) {

                    $splittedLine = preg_split('/\=/', $configLine, -1, PREG_SPLIT_NO_EMPTY);

                    $usedIPs[] = (isset($splittedLine[1])) ? array('dns' => $splittedLine[0], 'address' => $splittedLine[1]) : $configLine;

                } else {
                    $usedIPs[] = $configLine;
                }
            }

            foreach ($tsip as $newLine) {

                $splittedLine = preg_split('/\=/', strtolower($newLine), -1, PREG_SPLIT_NO_EMPTY);

                if (isset($splittedLine[1]) and !array_key_exists($splittedLine[1], $usedIPs)) {
                    $usedIPs[] = array('dns' => $splittedLine[0], 'address' => $splittedLine[1]);
                }
            }

            function array_multi_dimensional_unique($multi){

                $unique = array();

                foreach($multi as $sub){
                    if (!in_array($sub, $unique)){
                        $unique[] = $sub;
                    }
                }

                return $unique;

            }

            $newCfg = '';

            $usedIPs = array_multi_dimensional_unique($usedIPs);
            sort($usedIPs);

            foreach ($usedIPs as $value) {
                $newCfg .= (isset($value['dns']) and isset($value['address']) and !preg_match('/^#(|\s+)(.*)$/', $value['dns'])) ? $value['dns'] . '=' . $value['address'] . "\r\n" : $value . "\r\n";
            }

            if ($newCfg== '') {

                $bad = 'Nothing to write';

            } else {

                $sshSftpObject->put($file, $newCfg);

            }
        }

        if (!isset($bad) and $action != 'li') {

            $sshObject = new phpseclib\Net\SSH2($sship, $sshport);

            if ($sshObject->login($sshuser, $sshpw)) {

                $bin = ($bitversion == 32) ? 'tsdnsserver_linux_x86' : 'tsdnsserver_linux_amd64';

                $ssh2cmd = 'cd ' . $folders . ' && function restart () { if [ "`ps fx | grep ' . $bin . ' | grep -v grep`" == "" ]; then ./' . $bin . ' > /dev/null & else ./' . $bin . ' --update > /dev/null & fi }; restart& ';

                $sshObject->exec($ssh2cmd);

                if ($notified > 0) {
                    $query = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=0 WHERE `ssh2ip`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($sship, $reseller_id));
                }

            } else {
                $bad = 'The login data does not work';
                $notified++;
            }
        }

    } else {
        $bad = 'Could not connect to Server';
    }

    if (isset($bad) and $notified == $maxnotified) {

        if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`=0 AND `accounttype`='a'");
            $query->execute();
        } else {
            $query = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=? AND `id`=`resellerid`) OR (`resellerid`=0 AND `accounttype`='a')");
            $query->execute(array($reseller_id));
        }
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            if ($row['mail_serverdown'] == 'Y') {
                sendmail('emaildown', $row['id'], 'TS3 Master ' . $sship . ' ( ' . $bad . ' )', '');
            }
        }

        $query = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=`notified`+1 WHERE `ssh2ip`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($sship, $reseller_id));

        return $bad;

    } else if (isset($bad)) {
        return $bad;

    } else if ($action == 'li' and isset($dnsarray)) {
        return $dnsarray;

    }

    return 'ok';

}

function checkDNS ($dns, $id = null, $user_id = null, $type = '') {

    global $sql, $reseller_id;

    if ($type == 'server') {

        $query = $sql->prepare("SELECT `masterserver` FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        $masterID = $query->fetchColumn();

        $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dns`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($dns, $reseller_id));

        $query2 = $sql->prepare("SELECT `masterserver` FROM `voice_server` WHERE `id`!=? AND `dns`=? AND `resellerid`=? LIMIT 1");
        $query2->execute(array($id, $dns, $reseller_id));

    } else if ($type == 'dns') {

        $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dnsID`!=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        $masterID = $query->fetchColumn();

        $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dnsID`!=? AND `dns`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id, $dns, $reseller_id));

        $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
        $query2->execute(array($dns, $reseller_id));

    } else {

        $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dns`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($dns, $reseller_id));

        $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
        $query2->execute(array($dns, $reseller_id));

    }

    if ($query->rowCount() > 0 or $query2->rowCount() > 0) {
        return false;
    }

    if ($user_id != null) {

        $serverdnsArray = array();

        $query = $sql->prepare("SELECT `id`,`defaultdns`,`externalDefaultDNS`,`tsdnsServerID` FROM `voice_masterserver` WHERE `resellerid`=?");
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            if ($row['externalDefaultDNS'] == 'N') {

                unset($temp);

                if ($type == 'server' and $id != null and $row['id'] == $masterID) {
                    $defaultdns = strtolower($id . '.' . $row['defaultdns']);
                    $partCount = count(explode('.', $defaultdns));
                }

                $ex = explode('.', $row['defaultdns']);
                $i = count($ex) - 1;

                while ($i >= 0) {

                    $serverdnsArray[] = (isset($temp)) ? $ex[$i] . '.' . $temp : $ex[$i];

                    $i--;
                }

            } else if ($type == 'server' and $row['externalDefaultDNS'] == 'Y' and $id != null and $row['id'] == $masterID) {
                $tsdnsServerID = $row['tsdnsServerID'];
            }
        }

        $query = $sql->prepare("SELECT `id`,`defaultdns` FROM `voice_tsdns` WHERE `resellerid`=?");
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            unset($temp);

            if ((isset($tsdnsServerID) and $id != null and $row['id'] == $tsdnsServerID) or ($type == 'dns' and $id != null and $row['id'] == $masterID)) {
                $defaultdns = strtolower($id . '-' . getusername($user_id) . '.' . $row['defaultdns']);
                $partCount = count(explode('.', $defaultdns));
            }

            $ex = explode('.', $row['defaultdns']);
            $i = count($ex) - 1;

            while ($i >= 0) {

                $serverdnsArray[] = (isset($temp)) ? $ex[$i] . '.' . $temp : $ex[$i];

                $i--;
            }
        }

        if (isset($defaultdns) and $dns == $defaultdns) {
            return true;
        }

        $ex = explode('.', $dns);
        $dnsPartCount = count($ex);
        $first = $ex[0];

        if (isset($partCount) and $partCount == $dnsPartCount and isid($first, 10) and ($type == 'dns' or ($type == 'server' and $first != $id))) {
            return false;
        }

        $ex = explode('-', $first);

        if ($type == 'dns' and isset($partCount) and $partCount == $dnsPartCount and isid($ex[0], 10) and $ex[0] != $id) {
            return false;
        }

        $serverdnsArray = array_unique($serverdnsArray);

        if (((isset($defaultdns) and $dns != $defaultdns) or !isset($defaultdns)) and in_array($dns, $serverdnsArray)) {
            return false;
        }
    }

    return true;

}

/*
 * They probably do not expect scripts like this on the mirror side
 * But so what as long as it works...
 */
function getTS3Version ($type = 'server', $os = 'linux', $bit = 64, $url = null) {

    // check if entered parameters are correct
    if ($type != 'server' and $type != 'client') {
        return false;
    }

    if ($os != 'linux' and $os != 'win') {
        return false;
    }

    if ($bit != 64 and $bit != 32) {
        return false;
    }

    if ($url === null) {

        // Currently there are two mirrors.
        $urls = array('http://teamspeak.gameserver.gamed.de/ts3/releases/', 'http://dl.4players.de/ts/releases/');

        // Use random value from urls array. Autocheck how many entries and fit the mt_rand parameters accordingly
        $url = $urls[mt_rand(0, (count($urls) -1))];
    }

    $subfolders = array();

    $doc = new DOMDocument();
    $doc->loadHTMLFile($url);
    $links = $doc->getElementsByTagName('a');


    foreach ($links as $link) {

        $href = $link->getAttribute('href');

        // Filter for downloadfolders. Known old versions will be left out
        if (!preg_match('/^0\.[0-9](\.[0-9\/]|\/)+$/', substr($href, 2, 6)) and preg_match('/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(\.[0-9]{1,2}|)\/+$/', $href)) {

            // As versioning does not follow the same pattern we need to normalize in order to be able to sort.
            $normalizedVersion = str_replace(array('.', '/'), '', $href);

            while (strlen($normalizedVersion) < 6) {
                $normalizedVersion = $normalizedVersion . 0;
            }

            $subfolders[$normalizedVersion] = $href;
        }
    }

    // As the array key is normalized based on version we can sort by key in descending order.
    krsort($subfolders, SORT_NUMERIC);

    // Now loop though all version subfolders and search for server files.
    foreach ($subfolders as $sub) {

        // Use Dom to download html page into an object
        $doc = new DOMDocument();
        $doc->loadHTMLFile($url . $sub);

        // Get all links from page
        $links = $doc->getElementsByTagName('a');

        // loop through the links
        foreach ($links as $link) {

            // Build regular expression on the fly
            $clientServer = ($type == 'server') ? 'teamspeak3\-server\_' : 'TeamSpeak3\-Client\-';

            if ($os == 'linux' and $type == 'server') {

                $bitAndOS = ($bit == 64) ? 'linux(\-|\_)amd64' : 'linux(\-|\_)x86';
                $extension = 'tar\.(gz|bz2)';

            } else if ($os == 'linux' and $type == 'client') {

                $bitAndOS = ($bit == 64) ? 'linux\_amd64' : 'linux\_x86';
                $extension = 'run';

            } else {
                $bitAndOS = ($bit == 64) ? 'win64' : 'win32';
                $extension = ($type == 'server') ? 'zip' : 'run';
            }

            // get the href value from html anchor
            $href = $link->getAttribute('href');

            // check if we have a valid version and file. As we sorted and normalized some steps ago the first hit is the latest version.
            if (preg_match('/^' . $clientServer . $bitAndOS . '\-[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(\.[0-9]{1,2}|)\.' . $extension . '+$/', $href)) {

                $downloadLink = $url . $sub . $href;
                $currentVersion = str_replace('/', '', $sub);

                return array('version' => $currentVersion, 'link' => $downloadLink);
            }
        }
    }

    return false;
}

function getVoiceMasterList($resellerID, $adminID) {

    $table = array();

    global $sql;

    $query = $sql->prepare("SELECT m.`id`,m.`ssh2ip`,m.`description`,m.`maxserver`,m.`maxslots`,m.`active`,m.`resellerid`,m.`managedForID`,COUNT(v.`id`)*(100/m.`maxserver`) AS `serverpercent`,SUM(v.`slots`)*(100/m.`maxslots`) AS `slotpercent`,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`,SUM(v.`usedslots`) AS `uslots`,r.`ip`  FROM `voice_masterserver` m LEFT JOIN `rserverdata` r ON m.`rootid`=r.`id` LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` GROUP BY m.`id` HAVING (`installedserver`<`maxserver` AND (`installedslots`<`maxslots` OR `installedslots` IS NULL) AND `active`='Y' AND (`resellerid`=? OR m.`managedForID`=?)) ORDER BY `slotpercent`,`serverpercent` ASC");
    $query->execute(array($resellerID, $adminID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $table[$row['id']] = ($row['description'] != null and $row['description'] != '') ? $row['ssh2ip'] . ' ' . $row['description'] : $row['ssh2ip'];
    }

    return $table;
}
