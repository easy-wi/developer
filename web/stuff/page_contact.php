<?php
/**
 * File: page_contact.php.
 * Author: Ulrich Block
 * Date: 28.10.12
 * Time: 18:07
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


$name = $ui->names('name',255, 'post');
$email = $ui->ismail('email', 'post');
$comments=nl2br(htmlentities(trim($ui->escaped('comments', 'post')),ENT_QUOTES,'UTF-8'));
if ($ui->escaped('email', 'post')) {
    $error = array();
    if (!$ui->ismail('email', 'post'))$error[] = 'Mail';
    if (!$ui->names('name',255, 'post')) $error[] = 'Name';
    if (!isset($_SESSION['token'])) $error[] = 'No Token';
    else if ($_SESSION['token'] != $ui->escaped('token', 'post')) $error[] = 'Spamprotect';
    if (isset($_SESSION['token'])) unset($_SESSION['token']);
    if (count($error)>0) {
        $token=md5(passwordgenerate(32));
        $_SESSION['token'] = $token;
        $comments=str_replace('<br />','',$comments);
    } else {
        unset($error);
        $success = true;
        $comments = $name.' ('.$email.'):<br />'.$comments;
        sendmail('contact',$name,$comments,$rSA['email']);
    }
} else {
    $token=md5(passwordgenerate(32));
    $_SESSION['token'] = $token;
}
$page_data->setCanonicalUrl($s);

// https://github.com/easy-wi/developer/issues/62
$langLinks = array();
foreach ($languages as $l) {
    $tempLanguage = getlanguagefile('page',$l,0);
    $langLinks[$l]=($page_data->seo== 'Y') ? szrp($tempLanguage->$s)  : '?s='.$s;
}
$page_data->langLinks($langLinks);

$template_file = 'contact.tpl';