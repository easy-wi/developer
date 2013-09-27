<?php
/**
 * File: userpanel_mysql.php.
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
if ((!isset($user_id) or $main!=1) or (isset($user_id) and !$pa['mysql'])) {
    header('Location: userpanel.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache=getlanguagefile('mysql',$user_language,$reseller_id);
$loguserid=$user_id;
$logusername=getusername($user_id);
$logusertype="user";
$logreseller=0;
if (isset($admin_id)) {
	$logsubuser=$admin_id;
} else if (isset($subuser_id)) {
	$logsubuser=$subuser_id;
} else {
	$logsubuser=0;
}
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->id('id',10,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['db']))) {
    $id=$ui->id('id',10,'get');
    if (!$ui->smallletters('action',2,'post')) {

        #https://github.com/easy-wi/developer/issues/42 column description added
        $query=$sql->prepare("SELECT e.`dbname`,e.`description`,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,e.`ips`,s.`ip`,s.`port`,s.`interface`,u.`cname` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`id`=? AND e.`active`='Y' AND s.`active` AND e.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey,$id,$reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ip'];
            $ips=$row['ips'];
            $port=$row['port'];
            $interface=$row['interface'];
            $dbname=$row['dbname'];
            $cname=$row['cname'];
            $description=$row['description'];
            $password=$row['decryptedpassword'];
        }
        $template_file=(isset($dbname)) ? 'userpanel_mysql_db_md.tpl' : 'userpanel_404.tpl';
    } else if ($ui->smallletters('action',2,'post')=='md'){
        if ($ui->password('password',40,'post')) {
            include(EASYWIDIR . '/stuff/mysql_functions.php');
            $password=$ui->password('password',40,'post');
            $ips=$ui->ips('ips','post');
            $query=$sql->prepare("SELECT e.`dbname`,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,e.`ips`,e.`max_queries_per_hour`,e.`max_connections_per_hour`,e.`max_updates_per_hour`,e.`max_userconnections_per_hour`,s.`ip`,AES_DECRYPT(s.`password`,?) AS `decryptedpassword2`,s.`port`,s.`user`,u.`cname` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`id`=? AND e.`active`='Y' AND s.`active`='Y' AND e.`uid`=? AND e.`resellerid`=? LIMIT 1");
            $query->execute(array($aeskey,$aeskey,$id,$user_id,$reseller_id));
            foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                $cname=$row['cname'];
                $dbname=$row['dbname'];
                $ip=$row['ip'];
                $port=$row['port'];
                $user=$row['user'];
                $pwd=$row['decryptedpassword2'];
                $max_queries_per_hour=$row['max_queries_per_hour'];
                $max_connections_per_hour=$row['max_connections_per_hour'];
                $max_updates_per_hour=$row['max_updates_per_hour'];
                $max_userconnections_per_hour=$row['max_userconnections_per_hour'];
                $old_password=$row['decryptedpassword'];
                $old_ips=$row['ips'];
                if ($old_password!=$password or $old_ips!=$ips) {
                    $remotesql=new ExternalSQL ($ip,$port,$user,$pwd);
                    if ($remotesql->error=='ok') {

                        #https://github.com/easy-wi/developer/issues/42 column description added
                        $query=$sql->prepare("UPDATE `mysql_external_dbs` SET `description`=?,`password`=AES_ENCRYPT(?,?),`ips`=? WHERE `id`=? AND `uid`=? AND `resellerid`=? LIMIT 1");
                        $query->execute(array($ui->names('description',255,'post'),$password,$aeskey,$ips,$id,$user_id,$reseller_id));
                        $remotesql->ModDB($dbname,$password,$ips,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour);
                        $template_file=$spracheResponse->table_add;
                        $loguseraction="%mod% MYSQL DB $dbname ($ip)";
                        $insertlog->execute();
                    } else {
                        $template_file=$remotesql->error;
                    }
                } else {
                    $template_file=$spracheResponse->table_add;
                }
            }
        } else {
            $template_file='Error: '.$sprache->password;
        }
    } else {
        $template_file='userpanel_404.tpl';
    }
} else {
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='ap') {
        $orderby='s.`ip` ASC';
    } else if ($ui->st('o','get')=='dp') {
        $orderby='s.`ip` DESC';
    } else if ($ui->st('o','get')=='dn') {
        $orderby='e.`dbname` DESC';
    } else if ($ui->st('o','get')=='an') {
        $orderby='e.`dbname` ASC';
    } else if ($ui->st('o','get')=='di') {
        $orderby='e.`id` DESC';
    } else{
        $o='ai';
        $orderby='e.`id` ASC';
    }
    $table=array();
    $query=$sql->prepare("SELECT e.`id`,e.`dbname`,e.`gsid`,s.`ip`,s.`interface`,u.`cname` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`active`='Y' AND s.`active`='Y' AND e.`uid`=? AND e.`resellerid`=? ORDER BY $orderby");
    $query->execute(array($user_id,$reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        if (!isset($_SESSION['sID']) or in_array($row['id'],$substituteAccess['db'])) {
            $dbname=$row['dbname'];
            if ($row['gsid']>0) {
                $query2=$sql->prepare("SELECT CONCAT(`serverip`,':',`port`) AS `server` FROM `gsswitch` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
                $query2->execute(array($row['gsid'],$user_id,$reseller_id));
                foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                    $dbname .=' ( '.$row2['server'].' ) ';
                }
            }
            $table[]=array('id'=>$row['id'],'dbname'=>$dbname,'ip'=>$row['ip'],'interface'=>$row['interface']);
        }
    }
    $template_file="userpanel_mysql_db_list.tpl";
}