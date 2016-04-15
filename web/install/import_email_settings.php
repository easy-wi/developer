<?php
/**
 * File: import_email_settings.php
 * Author: Daniel Rodriguez Baumann
 * Date: 01.04.2016
 * Time: 16:02:54
 * Contact: info@triopsi.com
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
ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
define('EASYWIDIR', dirname(dirname(__FILE__)));

include_once(EASYWIDIR . '/stuff/methods/vorlage.php');
include_once(EASYWIDIR . '/stuff/methods/class_validator.php');
include_once(EASYWIDIR . '/stuff/methods/functions.php');
include_once(EASYWIDIR . '/stuff/settings.php');

$arrayofelements = array('vServer' => array('emailvrescue','emailvinstall'),
'Server' => array('emailbackup','emailbackuprestore','emailserverinstall','emailsecuritybreach','emaildown','emaildownrestart'),
'Ticket' => array('emailnewticket'),
'General' => array('emailfooter','emailregards','emailuseradd','emailpwrecovery','emailregister'),
'VoiceServer' => array('emailvoicemasterold'),
'GameServer' => array('emailgserverupdate'));
$arraycategory=array(1 => 'vServer', 2 => 'Server' , 3 => 'Ticket', 4 => 'General', 5 => 'VoiceServer', 6 => 'GameServer');
$queryselect = $sql->prepare("SELECT `email_setting_name`,`email_setting_value` FROM `settings_email` WHERE `reseller_id`=? AND `email_setting_name`=? ");
$queryinsert = $sql->prepare("INSERT INTO `setting_email_template` (`reseller_id`,`active`,`category`,`email_setting_name`,`subject`,`email_body`) VALUES (?,?,?,?,?,?)");
foreach($arrayofelements as $key => $value){
  echo '<b>'.$key.'</b><br>';
  foreach($value as $tablename){
   //echo '<i>'.$tablename.'</i> ';
   $queryselect->execute(array('0',$tablename));
   while ($row = $queryselect->fetch(PDO::FETCH_ASSOC)) {
    $category = array_search($key, $arraycategory);
    $queryinsert->execute(array(0,1,$category,$row['email_setting_name'],$row['email_setting_name'],$row['email_setting_value']));    
    if($queryinsert){
     //echo 'OK, ';
    }else{
     //echo 'Fail, ';
    }
   }
  }
  //echo '<br>';
}
