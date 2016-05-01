<?php
/**
 * File: panel_settings_email.php.
 * Author: Ulrich Block
 * Author: Daniel Rodriguez Baumann
 * Date: 05.01.13 v0.1
 * Date: 10.04.16 v0.2
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
$esprache = getlanguagefile('email', $user_language, $reseller_id);

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

// Add jQuery plugin chosen to the header
$htmlExtraInformation['css'][] = '<link href="css/default/summernote/summernote.css" rel="stylesheet" type="text/css">';
$htmlExtraInformation['js'][] = '<script src="js/default/plugins/summernote/summernote.min.js" type="text/javascript"></script>';

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->st('action', 'post') == 'md') {

    //Save E-Mail settings View:MD
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

    //New Teamplete Skin - Data Model
    if ($ui->w('d', 2, 'get') == 'md' and ($ui->id('id', 2, 'get') or $ui->w('id', 2, 'get') == 00)) {

        if ($ui->w('id', 2, 'get') == 00) {

            $tablename = $ui->w('tablename', 255, 'get');
            $language = $ui->w('lt', 2, 'get');
            $catid = $ui->id('cat', 2, 'get');
            $reseller_id = $ui->id('rid', 2, 'get');

            $query = $sql->prepare("SELECT MAX(id) as `id` FROM `settings_email_template`");
            $query->execute();
            $resultid = $query->fetchColumn();
            $resultid++;

            $email_id = $resultid;
            $email_setting_name = $tablename;
            $email_body = '';
            $email_subject = $tablename;
            $email_ccmailing = '';
            $email_bccmailing ='';
            $email_language = $language;
            $email_catid = $catid;

        } else {

            //Load Data from DB - View:Edit
            $query = $sql->prepare("SELECT * FROM `settings_email_template` WHERE `id`=? LIMIT 1");
            $query->execute(array($ui->w('id', 2, 'get')));
            $emaillanguage_xml = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $email_id = $row['id'];
                $email_setting_name = $row['email_setting_name'];
                $email_body = $row['email_body'];
                $email_subject = $row['subject'];
                $email_ccmailing = $row['ccmailing'];
                $email_bccmailing = $row['bccmailing'];
                $email_language = $row['language'];
                $email_catid = $row['category'];
            }
        }

        //Load Teamplate Skin MD
        $template_file = 'admin_settings_email_md.tpl';

    } else {

        //Save Data
        if ($ui->w('d', 3, 'get')=='add' and $ui->id('id', 2, 'post')){

            $query = $sql->prepare("INSERT INTO `settings_email_template` (`id`,`reseller_id`,`language`,`active`,`category`,`email_setting_name`,`subject`,`ccmailing`,`bccmailing`,`email_body`) VALUES (?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `language`=VALUES(`language`),`active`=VALUES(`active`),`category`=VALUES(`category`),`email_setting_name`=VALUES(`email_setting_name`),`reseller_id`=VALUES(`reseller_id`),`subject`=VALUES(`subject`),`ccmailing`=VALUES(`ccmailing`),`bccmailing`= VALUES(`bccmailing`),`email_body`= VALUES(`email_body`)");
            $query->execute(array($ui->id('id',2,'post'), $reseller_id, $ui->escaped('email_setting_language','post'), 1, $ui->escaped('email_setting_category','post'), $ui->escaped('email_setting_name','post'), $ui->escaped('email_subject','post'), $ui->escaped('ccmailing','post'), $ui->escaped('bccmailing','post'), $ui->escaped('email_body','post')));

            if ($query->rowCount() == 0) {
                $template_file = $spracheResponse->error_table;
            } else {
                $loguseraction = '%mod% %emailsettings%';
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;
            }

        } else {

            //Load Teamplate with language ID
            $templateLanguage = ($ui->w('tl', 2, 'get')) ? $ui->w('tl', 2, 'get') : $user_language;

            //Categorie Array
            $arrayofelements = array(
                'vServer' => array('emailvrescue','emailvinstall'),
                'Server' => array('emailbackup','emailbackuprestore','emailserverinstall','emailsecuritybreach','emaildown','emaildownrestart'),
                'Ticket' => array('emailnewticket'),
                'General' => array('emailfooter','emailregards','emailuseradd','emailpwrecovery','emailregister'),
                'VoiceServer' => array('emailvoicemasterold'),
                'GameServer' => array('emailgserverupdate')
            );

            $arraycategory = array(1 => 'vServer', 2 => 'Server' , 3 => 'Ticket', 4 => 'General', 5 => 'VoiceServer', 6 => 'GameServer');

            //Load E-Mail Setting from DB View:List
            $query = $sql->prepare("SELECT `email_setting_name`,`email_setting_value` FROM `settings_email` WHERE `reseller_id`=?");
            $query->execute(array($reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $email_settings[$row['email_setting_name']] = $row['email_setting_value'];
            }

            $query3 = $sql->prepare("SELECT * FROM `settings_email_category` WHERE `reseller_id`=?");
            $query3->execute(array($reseller_id));
            while ($row = $query3->fetch(PDO::FETCH_ASSOC)) {
                $email_categories[$row['id']] = $row['name'];
            }

            $query = $sql->prepare("SELECT * FROM `settings_email_template` WHERE `reseller_id`=? AND `category`=? AND `language`=?");
            $query2 = $sql->prepare("SELECT * FROM `settings_email_template` WHERE `reseller_id`=? AND `category`=? AND `language`=? AND `email_setting_name` =? LIMIT 1");

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

                $query->execute(array($reseller_id, $catid, $templateLanguage));

                if ($query->rowCount() > 0){

                    foreach($arrayofelements[$nameofcategory] as $tablename){

                        $query2->execute(array($reseller_id,$catid,$templateLanguage,$tablename));

                        if($query2->rowCount() > 0){
                            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                $resultHtmlCategories.='
          <tr>
          <td>';
                                $resultHtmlCategories.=($row['active'] == 1)?'<span class="label label-success">OK</span>':'<span class="label label-danger">No</span>';
                                $resultHtmlCategories.='</td>
          <td><a href="admin.php?w=sm&d=md&id='.$row['id'].'"><b>'.$row['email_setting_name'].'</b></a></td>
          <td><a href="admin.php?w=sm&d=md&id='.$row['id'].'"><b>'.$row['subject'].'</b></a></td>
          <td><a href="admin.php?w=sm&d=md&id='.$row['id'].'"><span style="font-size:1.2em;" class="fa fa-edit"></span></a> <a href="admin.php?w=sm&d=dell&id='.$row['id'].'&r=sm"><span style="font-size:1.2em;color:red;" class="fa fa-trash-o"></span></a></td>
          </tr>
          ';

                            }
                        }else{
                            $resultHtmlCategories.='
          <tr>
          <td><span class="label label-warning">Create</span>
          </td>
          <td><a href="admin.php?w=sm&d=md&id=00&tablename='.$tablename.'&lt='.$templateLanguage.'&cat='.$catid.'&rid='.$reseller_id.'"><b>'.$tablename.'</b></a></td>
          <td><a href="admin.php?w=sm&d=md&id=00&tablename='.$tablename.'&lt='.$templateLanguage.'&cat='.$catid.'&rid='.$reseller_id.'"><b>'.$tablename.'</b></a></td>
          <td><a href="admin.php?w=sm&d=md&id=00&tablename='.$tablename.'&lt='.$templateLanguage.'&cat='.$catid.'&rid='.$reseller_id.'"><span style="font-size:1.2em;" class="fa fa-edit"></span></a></td>
          </tr>
          ';
                        }
                    }
                }else{
                    foreach($arrayofelements[$nameofcategory] as $tablename){
                        $resultHtmlCategories.='
          <tr>
          <td><span class="label label-warning">Create</span>
          </td>
          <td><a href="admin.php?w=sm&d=md&id=00&tablename='.$tablename.'&lt='.$templateLanguage.'&cat='.$catid.'&rid='.$reseller_id.'"><b>'.$tablename.'</b></a></td>
          <td><a href="admin.php?w=sm&d=md&id=00&tablename='.$tablename.'&lt='.$templateLanguage.'&cat='.$catid.'&rid='.$reseller_id.'"><b>'.$tablename.'</b></a></td>
          <td><a href="admin.php?w=sm&d=md&id=00&tablename='.$tablename.'&lt='.$templateLanguage.'&cat='.$catid.'&rid='.$reseller_id.'"><span style="font-size:1.2em;" class="fa fa-edit"></span></a></td>
          </tr>
          ';
                    }
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

            if (isset($template_to_use)) {
                foreach (getlanguages($template_to_use) as $language) {
                    $emaillanguage_templates[] = array('lang' => $language);
                }
            }

            $template_file = 'admin_settings_email.tpl';

            //Delete Template
            if ($ui->w('d', 4, 'get')=='dell' && is_numeric($ui->w('id', 2, 'get'))){
                $query = $sql->prepare("DELETE FROM `settings_email_template` WHERE `id` = ?");

                if($query->execute(array($ui->id('id', 2, 'get')))){
                    $template_file = $spracheResponse->table_del;
                    $loguseraction = '%del% %emailsettings%';
                    $insertlog->execute();
                }else{
                    $template_file = $spracheResponse->error_table;
                    $loguseraction = 'ERROR: %del% %emailsettings%';
                    $insertlog->execute();
                }
            }

        }
    }
}