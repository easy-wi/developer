<?php

//##############################################
// MONSTA FTP v1.4.3 by MONSTA APPS
//##############################################
//
// Monsta FTP is proud to be open source.
//
// Please consider a donation and support this product's ongoing
// development: http://www.monstaapps.com/donations/
//
//##############################################
// COPYRIGHT NOTICE
//##############################################
//
// Copyright 2013 Internet Services Group Limited of New Zealand
//
// Monsta FTP is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.
//
// Monsta FTP is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// A copy of the GNU General Public License can be viewed at:
// < http://www.gnu.org/licenses/ >
//
//##############################################
// SUPPORT, BUG REPORTS, FEATURE REQUESTS
//##############################################
//
// Please visit http://www.monstaftp.com/support/
//
//##############################################
// INSTALL NOTES **IMPORTANT**
//##############################################
//
// 1. While this application is able to connect to FTP servers on both
//	  Windows and Linux, this script must run on a Linux server with PHP.
// 2. The server running this script must allow external FTP connections
//	  if you intend to allow connection to external servers.
// 3. The script can be uploaded anywhere on your website, and you can
//	  rename index.php to any name you prefer.
// 4. Please check the configurable variables below before running.
//
//##############################################
// Rewritten and adapted to easy-wi.com by Ulrich Block
// Contact ulrich.block@easy-wi.com


if (!isset($userPanelInclude)) {
    header('Location: userpanel.php');
    die('No acces');
}

// Include the language file
if (is_file(EASYWIDIR . '/third_party/monstaftp/languages/' . $user_language . '.php')) {
    include(EASYWIDIR . '/third_party/monstaftp/languages/' . $user_language . '.php');
} else {
    include(EASYWIDIR . '/third_party/monstaftp/languages/' . $default_language . '.php');
}

###############################################
# SET VARS
###############################################

$ftpAction = '';

// Check for file download
if ($ui->escaped('dl', 'get')) {
    $ftpAction = "download";
}

// Check for iFrame upload
if ($ui->escaped('ftpAction', 'get') == "iframe_upload") {
    $ftpAction = "iframe_upload";
}

// Check for iFrame edit
if ($ui->escaped('ftpAction', 'get') == "editProcess") {
    $ftpAction = "editProcess";
}

// Check for iFrame edit
if ($ui->escaped('ftpAction', 'get') == "templateProcess") {
    $ftpAction = "templateProcess";
}

// Check for AJAX post
if ($ui->escaped('ftpAction', 'post') != "" || $ui->escaped('ftpAction', 'get') != "") {
    $ajaxRequest=1;
} else {
    $ajaxRequest=0;
}

// Check resetting upload erreor array
if ($ui->id('resetErrorArray', 1, 'post') == 1 || $ajaxRequest == 0 ) {
    $_SESSION["monstaftp"][$ui->id('id', 10, 'get')]["errors"] = array();
}

$monstaDisplay = '';

$monsta = new Monsta($ui->id('id', 10, 'get'), $ftpIP, $ftpPort, $ftpUser, $ftpPass, $user_language, $gsFolder);

$template_file = 'userpanel_gserver_monstaftp.tpl';

if ($monsta->loggedIn === true and in_array($ftpAction, array("download", "iframe_upload", "editProcess", "templateProcess"))) {

    if ($ftpAction == "download") {
        $monstaDisplay .= $monsta->downloadFile();
        $monstaDisplay .= $monsta->parentOpenFolder();
    }

    if ($ftpAction == "iframe_upload") {
        $monstaDisplay .= $monsta->iframeUpload();
        $monstaDisplay .= $monsta->parentOpenFolder();
    }

    if ($ftpAction == "editProcess") {
        $monstaDisplay .= $monsta->editProcess();
    }

    if ($ftpAction == "templateProcess") {
        $monstaDisplay .= $monsta->editProcessTemplate();
    }


    if ($ajaxRequest == 1) {
        die($monstaDisplay);
    }

} else if ($monsta->loggedIn === true) {

    if (is_file(EASYWIDIR . '/css/' . $template_to_use . '/monstaftp.css')) {
        $htmlExtraInformation['css'][] = '<link href="css/' . $template_to_use . '/monstaftp.css" rel="stylesheet">';
    } else {
        $htmlExtraInformation['css'][] = '<link href="css/default/monstaftp.css" rel="stylesheet">';
    }

    $htmlExtraInformation['body'][] = 'onresize="setFileWindowSize(\'ajaxContentWindow\',0,0);"';

    // Process any FTP actions
    $monstaDisplay .= $monsta->processActions();

    if ($ajaxRequest == 0) {
        $monstaDisplay .= $monsta->displayFormStart();
        #$monstaDisplay .= $monsta->displayFtpActions();
        $monstaDisplay .= $monsta->displayAjaxDivOpen();
    }

    // Display FTP folder history
    $monstaDisplay .= $monsta->displayFtpHistory();

    // Display folder/file listing
    $monstaDisplay .= $monsta->displayFiles();

    // Load error window
    $monstaDisplay .= $monsta->displayErrors();

    if ($ajaxRequest == 0) {
        $monstaDisplay .= $monsta->divClose() . "\n";
        $monstaDisplay .= $monsta->displayAjaxIframe() . "\n";
        $monstaDisplay .= $monsta->displayUploadProgress() . "\n";
        $monstaDisplay .= $monsta->displayAjaxFooter() . "\n";
        $monstaDisplay .= $monsta->loadJsLangVars() . "\n";
        $monstaDisplay .= $monsta->loadAjax() . "\n";
        $monstaDisplay .= $monsta->writeHiddenDivs() . "\n";
        $monstaDisplay .= $monsta->divClose() . "\n";
        $monstaDisplay .= $monsta->displayFormEnd() . "\n";
    }

    if ($ajaxRequest == 1) {
        die($monstaDisplay);
    }

} else {

    if ($ajaxRequest == 1) {
        die($monsta->errorResponse);
    }

    $template_file = $monsta->errorResponse;
}