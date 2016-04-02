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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['settings'])) {
    redirect('login.php');
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('settings', $user_language, $reseller_id);
$gssprache = getlanguagefile('gserver', $user_language, $reseller_id);
$usprache = getlanguagefile('user', $user_language, $reseller_id);

$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
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

    $query = $sql->prepare("INSERT INTO `settings_email` (`reseller_id`,`email_setting_name`,`email_setting_value`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `email_setting_value`=VALUES(`email_setting_value`)");

    $query->execute(array($reseller_id, 'email', $ui->ismail('email', 'post')));
    $changeCount += $query->rowCount();
    $query->execute(array($reseller_id, 'email_settings_host', ($ui->domain('email_settings_host', 'post') or $ui->ip4('email_settings_host', 'post')) ? $ui->post['email_settings_host'] : 'localhost'));
    $changeCount += $query->rowCount();
    $query->execute(array($reseller_id, 'email_settings_password', $ui->password('email_settings_password', 255, 'post')));
    $changeCount += $query->rowCount();
    $query->execute(array($reseller_id, 'email_settings_port', $ui->port('email_settings_port', 'post')));
    $changeCount += $query->rowCount();
    $query->execute(array($reseller_id, 'email_settings_ssl', ($ui->w('email_settings_ssl',1, 'post')) ? $ui->w('email_settings_ssl',1, 'post') : 'N'));
    $changeCount += $query->rowCount();
    $query->execute(array($reseller_id, 'email_settings_type', ($ui->w('email_settings_type',1, 'post')) ? $ui->w('email_settings_type',1, 'post') : 'P'));
    $changeCount += $query->rowCount();
    $query->execute(array($reseller_id, 'email_settings_user', ($ui->ismail('email_settings_user', 'post')) ? $ui->ismail('email_settings_user', 'post') : $ui->username('email_settings_user',255, 'post')));
    $changeCount += $query->rowCount();

    if ($changeCount == 0) {
        $template_file = $spracheResponse->error_table;
    } else {

        $loguseraction = '%mod% %emailsettings%';
        $insertlog->execute();

        $template_file = $spracheResponse->table_add;
    }

} else {
 
 if($ui->w('d', 2, 'get')=='md' && is_numeric($ui->w('id', 2, 'get'))){
  
    function getMailXML($what, $language) {

        global $sql, $reseller_id;

        $query = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='em' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($language, $what, $reseller_id));
        $xml = $query->fetchColumn();

        $style = ($query->rowCount() == 1) ? 1 : 0;

        return array('style' => $style, 'lang' => $language, 'xml' => $xml);
    }
    
    $querysettingemail = $sql->prepare("SELECT * FROM `setting_email_template` WHERE `id`=? LIMIT 1");
    $querysettingemail->execute(array($ui->w('id', 2, 'get')));
    $emaillanguage_xml = array();
    
    while ($row = $querysettingemail->fetch(PDO::FETCH_ASSOC)) {
     $email_id = $row['id'];
     $email_setting_name = $row['email_setting_name'];
     $email_body = $row['email_body'];
     $email_subject = $row['subject'];
     if (isset($template_to_use)) {
      foreach (getlanguages($template_to_use) as $language) {
       $emaillanguage_xml[] = getMailXML($row['email_setting_name'], $language);
      }
     }
     
    }
    
    $template_file = 'admin_settings_email_md.tpl';
    
}else{
 
  if($ui->w('d', 3, 'get')=='add' && is_numeric($ui->id('id',2,'post'))){
   
   function updateMailXML($what, $postarray) {
   
    global $sql, $reseller_id;
   
    $changeCount = 0;
   
    if (isset($postarray["languages-$what"])) {
   
     $query = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('em',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
     foreach($postarray["languages-$what"] as $language) {
      if (small_letters_check($language, 2)) {
   
       $xml = $postarray[$what . '_xml_' . $language];
   
       $query->execute(array($what, $language, $xml, $reseller_id));
       $changeCount += $query->rowCount();
      }
     }
   
     $query = $sql->prepare("SELECT `lang` FROM `translations` WHERE `type`='em' AND `transID`=? AND `resellerID`=?");
     $query2 = $sql->prepare("DELETE FROM `translations` WHERE `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
   
     $query->execute(array($what, $reseller_id));
     while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      if (!in_array($row['lang'],(array) $postarray["languages-$what"])) {
       $query2->execute(array($row['lang'], $what, $reseller_id));
       $changeCount += $query2->rowCount();
      }
     }
   
    } else {
     $query = $sql->prepare("DELETE FROM `translations` WHERE `lang`=? AND `resellerID`=?");
     $query->execute(array($what, $reseller_id));
     $changeCount += $query->rowCount();
    }
   
    return $changeCount;
   }
   
   $changeCount = 0;
   
   $queryupdateemailsetting = $sql->prepare("UPDATE `setting_email_template` SET `subject`= ?, `email_body`= ? WHERE `id`=?");   
   
   $queryupdateemailsetting->execute(array($ui->escaped('email_subject','post'), $ui->escaped('email_body','post'), $ui->id('id',2,'post')));
   
   $changeCount += $queryupdateemailsetting->rowCount();
   
   $changeCount += updateMailXML($ui->escaped('email_setting_name','post'), $ui->post);
   
   if ($changeCount == 0) {
    $template_file = $spracheResponse->error_table;
   } else {
   
    $loguseraction = '%mod% %emailsettings%';
    $insertlog->execute();
   
    $template_file = $spracheResponse->table_add;
   }
   
  }else{
 
    $query = $sql->prepare("SELECT `email_setting_name`,`email_setting_value` FROM `settings_email` WHERE `reseller_id`=?");
    $query->execute(array($reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
     $email_settings[$row['email_setting_name']] = $row['email_setting_value'];
    }
 
    //New
    $querycategory = $sql->prepare("SELECT * FROM `setting_email_category` WHERE `reseller_id`=?");
    $querysettingemail = $sql->prepare("SELECT * FROM `setting_email_template` WHERE `reseller_id`=? AND `category`=?");
    $querycategory->execute(array($reseller_id));
    
    while ($row = $querycategory->fetch(PDO::FETCH_ASSOC)) {
     $email_categories[$row['id']] = $row['name'];
    }
    
    //HTMLCat
    $resultHtmlCategories='';
    $i = 1;
    foreach($email_categories as $catid => $nameofcategory){
     
     if ($i % 2 != 0){
      $resultHtmlCategories.='<div class="row">';
     }
     
     $resultHtmlCategories.='
     <div class="col-md-6">
     <div class="box box-primary">
     <div class="box-header with-border">
     <h3 class="box-title">'.$nameofcategory.'</h3>
     <div class="box-tools pull-right">
     <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
     </div><!-- /.box-tools -->
     </div>
     <div class="box-body table-responsive no-padding">
     <table class="table table-hover">
     <tbody>
     <tr>
     <th width="20">Status</th>
     <th>Template Name</th>
     <th>Subject</th>
     <th>Edit</th>
     </tr>';
     
     $querysettingemail->execute(array($reseller_id,$catid));
     while ($row = $querysettingemail->fetch(PDO::FETCH_ASSOC)) {
      
      $resultHtmlCategories.='
        <tr>
        <td>';
      $resultHtmlCategories.=($row['active'] == 1)?'<span class="label label-success">OK</span>':'<span class="label label-danger">No</span>';
      $resultHtmlCategories.='</td>
        <td><a href="admin.php?w=sm&d=md&id='.$row['id'].'"><b>'.$row['email_setting_name'].'</b></a></td>
        <td><a href="admin.php?w=sm&d=md&id='.$row['id'].'"><b>'.$row['subject'].'</b></a></td>
        <td><a href="admin.php?w=sm&d=md&id='.$row['id'].'"><span style="font-size:1.2em;" class="fa fa-edit"></span></a></td>
        </tr>
        ';
     } 
     $resultHtmlCategories.='
     </tbody>
     </table>
     </div>
     </div>
     </div>
     ';
     
     if ($i % 2 == 0){
      $resultHtmlCategories.='</div>';
     }
     
     $i++;
    }
    
    $template_file = 'admin_settings_email.tpl';
 }
}
}