<?php
/**
 * File: cloud.php.
 * Author: Ulrich Block
 * Date: 21.10.12
 * Time: 10:24
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

if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip=$_SERVER['REMOTE_ADDR'];
    if (isset($_GET['timeout']) and is_numeric($_GET['timeout'])) {
        $timelimit=$_GET['timeout'];
    } else {
        $timelimit=ini_get('max_execution_time')-10;
    }
} else {
    $timelimit=600;
}
set_time_limit($timelimit);
if (!isset($ip) or $_SERVER['SERVER_ADDR']==$ip) {
    define('EASYWIDIR',dirname(__FILE__));
    function printText ($text) {
        echo $text."\r\n";
    }
    function getParam ($v) {
        global $value;
        if (isset($value->$v)) {
            return $value->$v;
        }
        return '';
    }
    printText('Cloud jobs started');
    include('stuff/vorlage.php');
    include('stuff/functions.php');
    include('stuff/class_validator.php');
    include('stuff/settings.php');
    printText('File include and parameters fetched. Start connecting to external systems.');
    $query=$sql->prepare("SELECT * FROM `api_import` WHERE `active`='Y'");
    $query2=$sql->prepare("UPDATE `userdata` SET `salutation`=?,`mail`=?,`cname`=?,`name`=?,`vname`=?,`birthday`=?,`country`=?,`phone`=?,`fax`=?,`handy`=?,`city`=?,`cityn`=?,`street`=?,`streetn`=? WHERE `sourceSystemID`=? AND `externalID`=? AND `resellerid`=? LIMIT 1");
    $query3=$sql->prepare("INSERT INTO `userdata` (`salutation`,`mail`,`cname`,`name`,`vname`,`birthday`,`country`,`phone`,`fax`,`handy`,`city`,`cityn`,`street`,`streetn`,`usergroup`,`sourceSystemID`,`externalID`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $query4=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE `sourceSystemID`=? AND `externalID`=? LIMIT 1");
    $query5=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE LOWER(`mail`)=? AND LOWER(`cname`)=? LIMIT 1");
    $query6=$sql->prepare("UPDATE `userdata` SET `salutation`=?,`mail`=?,`cname`=?,`name`=?,`vname`=?,`birthday`=?,`country`=?,`phone`=?,`fax`=?,`handy`=?,`city`=?,`cityn`=?,`street`=?,`streetn`=? WHERE LOWER(`mail`)=? AND LOWER(`cname`)=? AND `resellerid`=? LIMIT 1");
    $query7=$sql->prepare("UPDATE `api_import` SET `lastCheck`=?,`lastID`=? WHERE `importID`=? LIMIT 1");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $resellerID=$row['resellerID'];
        if($row['ssl']=='Y') {
            $ssl='https://';
            $port=443;
        } else {
            $ssl='http://';
            $port=80;
        }
        $start=0;
        printText('Connect to: '.$ssl.$row['domain']);
        while (!isset($left) or $left>0) {
            $getRequest='/'.$row['file'].'?passwordToken='.urlencode($row['token']).'&start='.urlencode($start).'&chunkSize='.urlencode($row['chunkSize']).'&lastID='.urlencode($row['lastID']).'&updateTime='.urlencode($row['lastCheck']);
            $rawResponse=webhostRequest($row['domain'],'http://easy-wi.com user importer',$getRequest,null,$port);
            $response=cleanFsockOpenRequest($rawResponse,'{','}');
            $decoded=json_decode($response);
            unset($response);
            if ($decoded and isset($decoded->error)) {
                $left=0;
                if (is_array($decoded->error)) {
                    printText('Error: '.implode(', ',$decoded->error));
                } else {
                    printText('Error: '.$decoded->error);
                }
            } else if ($decoded and isset($decoded->total)) {
                if (isset($left)) {
                    $left-=$row['chunkSize'];
                } else {
                    $left=$decoded->total-$row['chunkSize'];
                }
                $start+=$row['chunkSize'];
                unset($lastID);
                foreach ($decoded->users as $value) {
                    if (isset($value->externalID)) {
                        $query4->execute(array(json_encode(array('I'=>$row['importID'])),$value->externalID));
                        $checkAmount=$query4->fetchColumn();
                        if($checkAmount>0 and $row['fetchUpdates']=='Y') {
                            $query2->execute(array(getParam('salutation'),strtolower(getParam('email')),getParam('loginName'),getParam('firstName'),getParam('lastName'),getParam('birthday'),getParam('country'),getParam('phone'),getParam('fax'),getParam('handy'),getParam('city'),getParam('cityn'),getParam('street'),getParam('streetn'),json_encode(array('I'=>$row['importID'])),getParam('externalID'),$row['resellerID']));
                            printText('User updated. Loginname: '.$value->loginName.' e-mail: '.strtolower($value->email));
                        } else if ($checkAmount>0) {
                            printText('User update skipped. Loginname: '.$value->loginName.' e-mail: '.strtolower($value->email));
                        } else {
                            $query5->execute(array(strtolower($value->email),strtolower($value->loginName)));
                            if ($query5->fetchColumn()>0 and $row['fetchUpdates']=='Y') {
                                $query6->execute(array(getParam('salutation'),strtolower(getParam('email')),getParam('loginName'),getParam('firstName'),getParam('lastName'),getParam('birthday'),getParam('country'),getParam('phone'),getParam('fax'),getParam('handy'),getParam('city'),getParam('cityn'),getParam('street'),getParam('streetn'),strtolower($value->email),strtolower($value->loginName),$row['resellerID']));
                                printText('User updated. Loginname: '.$value->loginName.' e-mail: '.strtolower($value->email));
                            } else if ($checkAmount>0) {
                                printText('User update skipped because source system differ. Loginname: '.$value->loginName.' e-mail: '.strtolower($value->email));
                            } else {
                                printText('Import user. Loginname: '.$value->loginName.' e-mail: '.strtolower($value->email));
                                $query3->execute(array(getParam('salutation'),strtolower(getParam('email')),getParam('loginName'),getParam('firstName'),getParam('lastName'),getParam('birthday'),getParam('country'),getParam('phone'),getParam('fax'),getParam('handy'),getParam('city'),getParam('cityn'),getParam('street'),getParam('streetn'),$row['groupID'],json_encode(array('I'=>$row['importID'])),getParam('externalID'),$row['resellerID']));
                            }
                        }
                        if (getParam('updatetime')!='' and (isset($lastCheck) and strtotime(getParam('updatetime'))>strtotime($lastCheck)) or !isset($lastCheck)) {
                            $lastCheck=getParam('updatetime');
                        }
                        $lastID=$value->externalID;
                    }
                }
                if (isset($lastID)) {
                    if (!isset($lastCheck)) {
                        $lastCheck=date('Y-m-d H:i:s');
                    }
                    $query7->execute(array($lastCheck,$lastID,$row['importID']));
                }
                if ($left>0){
                    printText('Total amount is: '.$decoded->total.' User left: '.$left.' need to make another run');
                    sleep(1);
                } else {
                    printText('Total amount is: '.$decoded->total.' No user left.');
                }
            } else if ($decoded) {
                printText('JSON Response does not contain expected values');
                $left=0;
            } else {
                if (strpos(strtolower($rawResponse),'file not found')===false) {
                    printText('No Json Response. Will retry.');
                } else {
                    $left=0;
                    printText('404: File not found');
                }
            }
        }
    }
    $query=$sql->prepare("UPDATE `settings` SET `lastCronCloud`=UNIX_TIMESTAMP() WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
} else {
    header('Location: login.php');
    die('Cloud can only be run via console and or a cronjob');
}