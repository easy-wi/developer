<?php
/**
 * File: ajaxfunctions.php
 * Author: Daniel Rodriguez Baumann
 * Contact: <daniel@triopsi.com>
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

define('EASYWIDIR', dirname(__FILE__));
include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/third_party/phpmailer/class.phpmailer.php');
if (!class_exists('SSH2')) {
    include(EASYWIDIR . '/third_party/phpseclib/autoloader.php');
}
$data = array();
$errors = array();

//Mail Test
if($ui->w('d', 7, 'get')=='smttest')
{

 try{ 
  $mail = new PHPMailer();
  $mail->CharSet = 'UTF-8';
  $mail->isSMTP();
  $mail->SMTPAuth = true;                                 
  $mail->Host = $ui->escaped('email_settings_host','post'); 
  $mail->Username = $ui->escaped('email_settings_user','post');
  $mail->Password = $ui->escaped('email_settings_password','post');
  
  if ($ui->escaped('email_settings_ssl','post') == 'T') {
   $mail->SMTPSecure = 'tls';
  } else if ($ui->escaped('email_settings_ssl','post') == 'S') {
   $mail->SMTPSecure = 'ssl';
  }
  
  $mail->Port = $ui->id('email_settings_port',5,'post');
  $mail->setFrom('noreply@easy-wi');
 
  if(!$mail->smtpConnect()) {
   $errors[]= 'Mailer Error: Invalide Data';
  } else {
   $mail->smtpClose();
  }

 }catch (phpmailerException $e) {
   $errors[]= 'Mailer Error EXP: ' . $e->errorMessage(); //Pretty error messages from PHPMailer
 } catch (Exception $e) {
   $errors[]= 'Mailer Error EXP: ' . $e->getMessage(); //Boring error messages from anything else!
 }
 
 
 if(empty($errors)){
  $data=array('success' => 'OK');
 }else{
  $data=array('error' => $errors);
 }
}
echo json_encode($data);