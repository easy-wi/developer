<?php

/**
 * File: userpanel_restartcalendar.php.
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

if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['restart']) or !$ui->id('id', 10, 'get')) {
    redirect('userpanel.php');
}

$id = (int) $ui->id('id', 10, 'get');
$sprache = getlanguagefile('gserver', $user_language, $reseller_id);
$pallowed = 'N';

if (isset($admin_id) and $reseller_id != 0 and $admin_id != $reseller_id) {
    $reseller_id = $admin_id;
}
if (!isset($_SESSION['sID']) or in_array($id, $substituteAccess['gs'])) {
    $query = $sql->prepare("SELECT g.`serverip`,g.`port`,g.`protected`,s.`anticheat`,g.`pallowed`,g.`eacallowed`,s.`map`,s.`mapGroup`,t.`shorten`,t.`mapGroup` AS `defaultMapGroup` FROM `gsswitch` g LEFT JOIN `serverlist` s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($id, $user_id, $reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $serverip = $row['serverip'];
        $port = $row['port'];
        $map = $row['map'];
        $mapGroup = $row['mapGroup'];
        $defaultMapGroup = $row['defaultMapGroup'];
        $protected = $row['protected'];
        $anticheat = $row['anticheat'];
        $gsswitch = $row['shorten'];
        $pallowed = $row['pallowed'];
        $eacallowed = $row['eacallowed'];
    }
    $uploadallowed = array();
    $rowcount = $query->rowcount();

}

if (!isset($rowcount) or $rowcount == 0) {
    redirect('userpanel.php');
}

if ($ui->smallletters('edit',4, 'post') == 'edit' and isset($serverip) and isset($port)) {

    $template = '';
    $anticheat = '';
    $gsswitch = '';
    $pro = '';
    $restart = 'Y';
    $backup = '';
    $worldsafe = 'N';
    $upload = '';
    $binary = '';
    $eacallowed = 'N';

    $gameqArray = array();
    $binaryArray = array();
    $table = array();

    $date2 = $ui->gamestring('date', 'post');

    @list($day, $hour) = explode('_', $date2);

    $hour = $hour . ':00';

    if ($day == 'mon') {
        $day = $sprache->monday;
    } else if ($day == 'tue') {
        $day = $sprache->tuesday;
    } else if ($day == 'wed') {
        $day = $sprache->wednesday;
    } else if ($day == 'thu') {
        $day = $sprache->thursday;
    } else if ($day == 'fri') {
        $day = $sprache->friday;
    } else if ($day == 'sat') {
        $day = $sprache->saturday;
    } else if ($day == 'sun') {
        $day = $sprache->sunday;
    }

    $query = $sql->prepare("SELECT `id`,`normal_3`,`normal_4`,`hlds_3`,`hlds_4`,`hlds_5`,`hlds_6` FROM `eac` WHERE `active`='Y' AND `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));

    $rowcount = $query->rowCount();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $normal_3 = $row['normal_3'];
        $normal_4 = $row['normal_4'];
        $hlds_3 = $row['hlds_3'];
        $hlds_4 = $row['hlds_4'];
        $hlds_5 = $row['hlds_5'];
        $hlds_6 = $row['hlds_6'];
    }

    $query = $sql->prepare("SELECT s.`upload`,t.`shorten`,t.`description`,t.`gameq`,t.`gamebinary`,t.`mapGroup`,t.`protected` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=? GROUP BY t.`shorten`");
    $query->execute(array($id, $reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $shorten = $row['shorten'];
        $uploadallowed[] = $row['upload'];
        $gameqArray[$shorten] = $row['gameq'];
        $binaryArray[$shorten] = $row['gamebinary'];

        if (!isset($gameq)) {
            $gameq = $row['gameq'];
        }

        $table[$row['shorten']] = array('shorten' => $shorten,'description' => $row['description'], 'defaultMapGroup' => $row['mapGroup'], 'protected' => $row['protected'], 'gameq' => $row['gameq'], 'gamebinary' => $row['gamebinary']);
    }

    $query = $sql->prepare("SELECT * FROM `gserver_restarts` WHERE `restarttime`=? AND `switchID`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($date2, $id, $reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $template = $row['template'];
        $anticheat = $row['anticheat'];
        $gsswitch = $row['gsswitch'];
        $defaultMapGroup = $table[$row['gsswitch']]['defaultMapGroup'];
        $map = $row['map'];
        $pro = $row['protected'];
        $restart = $row['restart'];
        $backup = $row['backup'];
        $worldsafe = $row['worldsafe'];
        $binary = $binaryArray[$gsswitch];

        if ($defaultMapGroup != null) {
            $mapGroup = $row['mapGroup'];
        }

        if (in_array('srcds_run', $binaryArray) and (in_array(2, $uploadallowed) or in_array(3, $uploadallowed))) {
            $upload = $row['upload'];
        }
    }

    $style = ($restart == 'Y') ? 'style="width:690px;"' : 'style="display:none;border-spacing:0px;"';

    if (!isset($eac) and $eacallowed == 'Y' and $rowcount > 0 and ($gsswitch == 'css' or $gsswitch == 'cod4' or $gsswitch == 'cstrike' or $gsswitch == 'czero' or $gsswitch == 'tf')) {

        if ($gsswitch == 'cstrike' or $gsswitch == 'czero') {

            if ($anticheat == 3 and $hlds_3 == 'Y') {
                $eac[] = '<option value="3" selected="selected">Easy Anti Cheat</option>';
            } else if ($hlds_3 == 'Y') {
                $eac[] = '<option value="3">Easy Anti Cheat</option>';
            }

            if ($anticheat == 4 and $hlds_4 == 'Y') {
                $eac[] = '<option value="4" selected="selected">Easy Anti Cheat Public</option>';
            } else if ($hlds_4 == 'Y') {
                $eac[] = '<option value="4">Easy Anti Cheat Public</option>';
            }

            if ($anticheat == 5 and $hlds_5 == 'Y') {
                $eac[] = '<option value="5" selected="selected">Easy Anti Cheat 32Bit</option>';
            } else if ($hlds_5 == 'Y') {
                $eac[] = '<option value="5">Easy Anti Cheat 32Bit</option>';
            }

            if ($anticheat == 6 and $hlds_6 == 'Y') {
                $eac[] = '<option value="6" selected="selected">Easy Anti Cheat Public 32Bit</option>';
            } else if ($hlds_6 == 'Y') {
                $eac[] = '<option value="6">Easy Anti Cheat Public 32Bit</option>';
            }
        } else {

            if ($anticheat == 3 and $normal_3 == 'Y') {
                $eac[] = '<option value="3" selected="selected">Easy Anti Cheat</option>';
            } else if ($normal_3 == 'Y') {
                $eac[] = '<option value="3">Easy Anti Cheat</option>';
            }

            if ($anticheat == 4 and $normal_4 == 'Y') {
                $eac[] = '<option value="4" selected="selected">Easy Anti Cheat Public</option>';
            } else if ($normal_4 == 'Y') {
                $eac[] = '<option value="4">Easy Anti Cheat Public</option>';
            }
        }

    } else if (!isset($eac)) {
        $eac = array();
    }

    if ($binary == 'srcds_run' or $binary == 'hlds_run') {
        $anticheatsoft = 'Valve Anti Cheat';
    } else if ($binary == 'cod4_lnxded') {
        $anticheatsoft = 'Punkbuster';
    } else {
        $anticheatsoft = '';
    }

    $template_file = 'userpanel_gserver_calendar_md.tpl';

} else if ($ui->smallletters('edit2',4, 'post') == 'edit' and $ui->gamestring('date', 'post') and $ui->id('template',1, 'post') and $ui->id('anticheat',1, 'post') and $ui->gamestring('shorten', 'post') and $ui->active('backup', 'post') and $ui->active('restart', 'post') and isset($serverip) and isset($port)) {

    $gameqArray = array();

    $date = $ui->gamestring('date', 'post');
    $template = $ui->id('template',1, 'post');
    $anticheat = $ui->id('anticheat',1, 'post');
    $gsswitch = $ui->gamestring('shorten', 'post');

    $query = $sql->prepare("SELECT t.`protected`,t.`gameq` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE t.`shorten`=? AND s.`resellerid`=?");
    $query->execute(array($gsswitch, $reseller_id));
    $serverlist = $query->fetch(PDO::FETCH_ASSOC);
    $query->closeCursor();

    $query = $sql->prepare("SELECT `normal_3`,`normal_4`,`hlds_3`,`hlds_4`,`hlds_5`,`hlds_6` FROM `eac` WHERE `active`='Y' AND `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    $rowcount = $query->rowCount();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $normal_3 = $row['normal_3'];
        $normal_4 = $row['normal_4'];
        $hlds_3 = $row['hlds_3'];
        $hlds_4 = $row['hlds_4'];
        $hlds_5 = $row['hlds_5'];
        $hlds_6 = $row['hlds_6'];
    }

    $worldsafe = ($ui->active('worldsafe', 'post')) ? $ui->active('worldsafe', 'post') : 'N';

    if ($anticheat > 2) {

        if ($gsswitch == 'cstrike' or $gsswitch == 'czero') {

            if ($anticheat==3 and $hlds_3== 'N' and $hlds_5 == 'Y') {
                $anticheat = 5;
            } else if ($anticheat==3 and $hlds_3== 'N' and $hlds_5== 'N') {
                $anticheat = 1;
            } else {
                $anticheat = 1;
            }

            if ($anticheat==4 and $hlds_4== 'N' and $hlds_6 == 'Y') {
                $anticheat = 6;
            } else if ($anticheat==4 and $hlds_4== 'N' and $hlds_6== 'N') {
                $anticheat = 1;
            } else {
                $anticheat = 1;
            }

            if ($anticheat==5 and $hlds_5== 'N') {
                $anticheat = 1;
            }

            if ($anticheat==6 and $hlds_6== 'N') {
                $anticheat = 1;
            }
        } else {

            if ($anticheat==3 and $normal_3== 'N') {
                $anticheat = 1;
            }

            if ($anticheat==4 and $normal_4== 'N') {
                $anticheat = 1;
            }
        }
    }

    $restart = $ui->active('restart', 'post');
    $backup = $ui->active('backup', 'post');

    $map = ($ui->mapname('map', 'post') and $serverlist['gameq'] != 'minecraft') ? $ui->mapname('map', 'post') : '';
    $protected = ($ui->active('protected', 'post') and $serverlist['protected'] == 'Y') ? $ui->active('protected', 'post') : 'N';
    $stvupload = ($ui->active('upload', 'post')) ? $ui->active('upload', 'post') : 'N';

    $query = $sql->prepare("SELECT `id` FROM `gserver_restarts` WHERE `restarttime`=? AND `switchID`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($date, $id, $reseller_id));
    $rowcount = $query->rowCount();

    if ($rowcount == 0) {
        $query = $sql->prepare("INSERT INTO `gserver_restarts` (`template`,`anticheat`,`protected`,`restarttime`,`gsswitch`,`map`,`mapGroup`,`restart`,`backup`,`worldsafe`,`upload`,`switchID`,`userid`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $query->execute(array($template, $anticheat, $protected, $date, $gsswitch, $map, $ui->mapname('mapGroup', 'post'), $restart, $backup, $worldsafe, $stvupload, $id, $user_id, $reseller_id));
    } else {
        $query = $sql->prepare("UPDATE `gserver_restarts` SET `template`=?,`anticheat`=?,`protected`=?,`gsswitch`=?,`map`=?,`mapGroup`=?,`restart`=?,`backup`=?,`worldsafe`=?,`upload`=? WHERE `restarttime`=? AND `switchID`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($template, $anticheat, $protected, $gsswitch, $map, $ui->mapname('mapGroup', 'post'), $restart, $backup, $worldsafe, $stvupload, $date, $id, $user_id, $reseller_id));
    }

    $template_file = $spracheResponse->table_add;

} else if ($ui->smallletters('delete', 6, 'post') == 'delete' and $ui->gamestring('date', 'post') and isset($serverip) and isset($port)) {

    $date = $ui->gamestring('date', 'post');

    $query = $sql->prepare("DELETE FROM `gserver_restarts` WHERE `restarttime`=? AND `switchID`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($date, $id, $reseller_id));

    $template_file = $spracheResponse->table_del;;

} else if (isset($serverip) and isset($port)){

    $backup = 'N';
    $i = 0;
    $gameqArray = array();

    while ($i < 24) {
        $restarts[$i] = array('mon' => '','tue' => '','wed' => '','thu' => '','fri' => '','sat' => '','sun' => '');
        $i++;
    }

    $query = $sql->prepare("SELECT t.`shorten`,t.`gameq`,t.`gamebinary` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=? GROUP BY t.`shorten`");
    $query->execute(array($id, $reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $shorten = $row['shorten'];
        $gameqArray[$shorten] = array('gameq' => $row['gameq'], 'gamebinary' => $row['gamebinary']);
    }

    $query = $sql->prepare("SELECT `template`,`restarttime`,`gsswitch`,`anticheat`,`protected`,`map`,`restart`,`backup`,`worldsafe`,`upload` FROM `gserver_restarts` WHERE `switchID`=? AND `resellerid`=?");
    $query->execute(array($id, $reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $shorten = $row['gsswitch'];
        $template = $row['template'];
        $restart = $row['restart'];
        $backup = $row['backup'];
        $worldsafe = $row['worldsafe'];
        $upload = $row['upload'];
        $protected = $row['protected'];
        $anticheat = $row['anticheat'];

        $gamebinary = (isset($gameqArray[$shorten])) ? $gameqArray[$shorten]['gamebinary'] : '';

        $restarttime = explode('_', $row['restarttime']);
        $day = $restarttime[0];
        $hour = $restarttime[1];

        if ($gamebinary == 'srcds_run' or $gamebinary == 'hlds_run') {
            $anticheatsoft = 'VAC';
        } else if ($gamebinary == 'cod4_lnxded') {
            $anticheatsoft = 'PBuster';
        } else {
            $anticheatsoft = '';
        }

        if ($anticheat == 1) {
            $restarts[$hour][$day]['anti'] = $anticheatsoft . ' ' . $sprache->on;
        } else if ($anticheat == 2) {
            $restarts[$hour][$day]['anti'] = $anticheatsoft . ' ' . $sprache->off2;
        } else if ($anticheat == 3 or $anticheat == 4 or $anticheat == 5 or $anticheat == 6) {
            $restarts[$hour][$day]['anti'] = 'EAC';
        }

        if ($template == 1) {
            $template = '';
        } else {
            $template = '-' . $template;
        }

        if ($backup == 'Y') {
            $restarts[$hour][$day]['backup'] = $gsprache->yes;
        } else {
            $restarts[$hour][$day]['backup'] = $gsprache->no;
        }

        if ($gameqArray[$shorten]['gameq'] == 'minecraft' and $restart == 'N' and $worldsafe == 'Y') {
            $restarts[$hour][$day]['worldsave'] = $gsprache->yes;
        } else if ($gameqArray[$shorten]['gameq'] == 'minecraft' and $restart == 'N') {
            $restarts[$hour][$day]['worldsave'] = $gsprache->no;
        }

        if ($gamebinary == 'srcds_run' and $restart == 'N' and $upload== 'Y') {
            $restarts[$hour][$day]['sourcetvdemo'] = $gsprache->yes;
        } else if ($gamebinary == 'srcds_run' and $restart == 'N') {
            $restarts[$hour][$day]['sourcetvdemo'] = $gsprache->no;
        }

        if ($restart == 'Y') {
            $restarts[$hour][$day]['restart'] = $gsprache->yes;
            $restarts[$hour][$day]['template'] = $shorten.$template;
            $restarts[$hour][$day]['map'] = $row['map'];

            if ($pallowed == 'Y') {
                if ($protected=='N') {
                    $restarts[$hour][$day]['protected'] = $gsprache->no;
                } else if ($protected=='Y') {
                    $restarts[$hour][$day]['protected'] = $gsprache->yes;
                }
            }

            else {
                $restarts[$hour][$day]['protected'] = '';
            }
        }
        else {
            $restarts[$hour][$day]['restart'] = $gsprache->no;
            $restarts[$hour][$day]['template'] = '';
            $restarts[$hour][$day]['map'] = '';
            $restarts[$hour][$day]['protected'] = '';
        }

    }

    $template_file = 'userpanel_gserver_calendar_list.tpl';
}