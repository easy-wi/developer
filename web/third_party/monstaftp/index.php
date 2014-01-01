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

###############################################
# CONFIGURABLE VARIABLES
###############################################

// MonstaFTP version number
$version = "1.4.3";

// Enter the host address/port/mode if it should always be a fixed host address.
// Leave this blank if you want the user to input their host address.
$ftpHost = "";
$ftpPort = "21"; // 21 is the default
$ftpMode = "1"; // 1 for passive, 0 for not passive (passive is default)
$ftpSSL = "0"; // 1 for SSL, 0 for not SSL - your (server must support ftp_ssl_connect() to use this)
$ftpDir = ""; // Enter an FTP server path for the default login (or leave blank for home folder)

// This is a folder on the client server for uploading files to
// By default it's /tmp, which is the server's folder for temporary
// files, however you can set any folder, provided it has 777 chmod
// permissions, so it can be written to from the web.
$serverTmp = "../../tmp";

// Include a list of file types that can be edited in the text editor
$editableExts = "asp,ashx,asmx,aspx,asx,axd,cfm,cgi,css,html,htm,jhtml,js,php,phtml,pl,txt,xhtml";

// USA date format - 1 for mm/dd/yy, 0 for dd/mm/yy
$dateFormatUsa = 0;

// The number of minutes to lockout invalid logins
$lockOutTime = 5;

###############################################
# SANITZE VARS (DEPRECATED)
###############################################

//sanitizeVars();

###############################################
# SAVE DETAILS TO COOKIE
###############################################

saveFtpDetailsCookie();

###############################################
# START SESSION
###############################################

startSession();

###############################################
# INCLUDE LANGUAGE FILE
###############################################

if ($_SESSION["lang"] == "" || isset($_POST["lang"]))
    setLangFile();

$langFileArray = getFileArray("languages");

if (in_array($_SESSION["lang"],$langFileArray))
    include("languages/".$_SESSION["lang"].".php");
else
    include("languages/uk.php");

###############################################
# SET VARS
###############################################

// Check for file download
if (isset($_GET["dl"]))
    $ftpAction = "download";

// Check for iFrame upload
if ($_GET["ftpAction"] == "iframe_upload")
    $ftpAction = "iframe_upload";

// Check for iFrame edit
if ($_GET["ftpAction"] == "editProcess")
    $ftpAction = "editProcess";

// Check for AJAX post
if ($_POST["ftpAction"] != "" || $_GET["ftpAction"] != "")
    $ajaxRequest=1;
else
    $ajaxRequest=0;

// Check resetting upload erreor array
if ($_POST["resetErrorArray"] == 1 || $ajaxRequest == 0 ) {
    $_SESSION["errors"] = array();
}

// Set file upload limit
setUploadLimit();

###############################################
# LOAD CONTENT
###############################################

// These check vars are set in the "SET VARS" section
if ($ftpAction == "download" || $ftpAction == "iframe_upload" || $ftpAction == "editProcess") {

    // Login
    attemptLogin();

    // Check referer
    if (checkReferer() == 1) {

        // Display content when logged in
        if ($_SESSION["loggedin"] == 1) {

            if ($ftpAction == "download") {
                downloadFile();
                parentOpenFolder();
            }
            if ($ftpAction == "iframe_upload") {
                iframeUpload();
                parentOpenFolder();
            }
            if ($ftpAction == "editProcess") {
                editProcess();
            }
        }
    }

} else {

    if ($ajaxRequest == 0) {

        // Check if logout link has been clicked
        checkLogOut();

        // Include the header
        displayHeader();
    }

    // Attempt to login with session or post vars
    attemptLogin();

    // Check referer
    if (checkReferer() == 1) {

        // Process any FTP actions
        processActions();

        // Display content when logged in
        if ($_SESSION["loggedin"] == 1) {

            if ($ajaxRequest == 0) {
                displayFormStart();
                displayFtpActions();
                displayAjaxDivOpen();
            }

            // Display FTP folder history
            displayFtpHistory();

            // Display folder/file listing
            displayFiles();

            // Load error window
            displayErrors();

            if ($ajaxRequest == 0) {
                displayAjaxDivClose();
                displayAjaxIframe();
                displayUploadProgress();
                displayAjaxFooter();
                loadJsLangVars();
                loadAjax();
                writeHiddenDivs();
                displayFormEnd();
                //displayAjaxIframe();
                loadEditableExts();
            }
        }

        if ($ajaxRequest ==  0) {

            // Include the footer
            displayFooter();
        }
    }
}

// Close FTP connection
@ftp_close($conn_id);

###############################################
# START SESSION
###############################################

function startSession() {

    @session_start();

    $sessionAr[] = "user_ip";
    $sessionAr[] = "loggedin";
    $sessionAr[] = "skin";
    $sessionAr[] = "lang";
    $sessionAr[] = "win_lin";
    $sessionAr[] = "ip_check";
    $sessionAr[] = "login_error";
    $sessionAr[] = "login_fails";
    $sessionAr[] = "login_lockout";
    $sessionAr[] = "ftp_ssl";
    $sessionAr[] = "ftp_host";
    $sessionAr[] = "ftp_user";
    $sessionAr[] = "ftp_pass";
    $sessionAr[] = "ftp_port";
    $sessionAr[] = "ftp_pasv";
    $sessionAr[] = "interface";
    $sessionAr[] = "dir_current";
    $sessionAr[] = "dir_history";
    $sessionAr[] = "clipboard_chmod";
    $sessionAr[] = "clipboard_files";
    $sessionAr[] = "clipboard_folders";
    $sessionAr[] = "clipboard_rename";
    $sessionAr[] = "copy";
    $sessionAr[] = "errors";
    $sessionAr[] = "upload_limit";
    $sessionAr[] = "domain";

    // Register each variable in the array
    $n = sizeof($sessionAr);
    for ($i=0;$i<$n;$i++) {

        if (!isset($sessionAr[$i]))
            session_register($sessionAr[$i]);
    }
}

###############################################
# SET COOKIE BEFORE HEADER
###############################################

function saveFtpDetailsCookie() {

    if ($_POST["login"] == 1) {

        if ($_POST["login_save"] == 1) {

            $s = 31536000; // seconds in a year
            setcookie("ftp_ssl", $_POST["ftp_ssl"], time()+$s);
            setcookie("ftp_host", trim($_POST["ftp_host"]), time()+$s);
            setcookie("ftp_user", trim($_POST["ftp_user"]), time()+$s);
            setcookie("ftp_pass", trim($_POST["ftp_pass"]), time()+$s);
            setcookie("ftp_port", trim($_POST["ftp_port"]), time()+$s);
            setcookie("ftp_pasv", $_POST["ftp_pasv"], time()+$s);
            setcookie("interface", $_POST["interface"], time()+$s);
            setcookie("login_save", $_POST["login_save"], time()+$s);
            setcookie("skin", $_POST["skin"], time()+$s);
            setcookie("lang", $_POST["lang"], time()+$s);
            setcookie("ip_check", $_POST["ip_check"], time()+$s);

        } else {

            setcookie("ftp_ssl", "", time()-3600);
            setcookie("ftp_host", "", time()-3600);
            setcookie("ftp_user", "", time()-3600);
            setcookie("ftp_pass", "", time()-3600);
            setcookie("ftp_port", "", time()-3600);
            setcookie("ftp_pasv", "", time()-3600);
            setcookie("interface", "", time()-3600);
            setcookie("login_save", "", time()-3600);
            setcookie("skin", "", time()-3600);
            setcookie("lang", "", time()-3600);
            setcookie("ip_check", "", time()-3600);
        }
    }
}

###############################################
# CHECK IF LOGGED IN
###############################################

function attemptLogin() {

    global $conn_id;
    global $ftpHost;
    global $ftpPort;
    global $ftpMode;
    global $ftpSSL;
    global $ftpDir;
    global $lang_missing_fields;
    global $lang_ip_conflict;

    if (connectFTP(0) == 1 && $_POST["login"] != 1) {

        // Check for hijacked session
        if ($_SESSION["ip_check"] == 1) {

            if ($_SERVER['REMOTE_ADDR'] == $_SESSION["user_ip"]) {
                $_SESSION["loggedin"]=1;
            } else {
                $_SESSION["errors"] = $lang_ip_conflict;
                sessionExpired($lang_ip_conflict);
                logOut();
            }

        } else {
            $_SESSION["loggedin"]=1;
        }

    } else {

        if ($_POST["login"] == 1) {

            // Check for login errors
            if (checkLoginErrors() == 1) {

                $_SESSION["login_error"] = $lang_missing_fields;
                displayLoginForm(1);

            } else {

                // Set POST vars to SESSION
                if ($ftpHost == "") {

                    $_SESSION["ftp_host"] = trim($_POST["ftp_host"]);
                    $_SESSION["ftp_port"] = trim($_POST["ftp_port"]);
                    $_SESSION["ftp_pasv"] = $_POST["ftp_pasv"];
                    $_SESSION["ftp_ssl"] = $_POST["ftp_ssl"];

                } else {

                    $_SESSION["ftp_host"] = $ftpHost;
                    $_SESSION["ftp_port"] = $ftpPort;
                    $_SESSION["ftp_pasv"] = $ftpMode;
                    $_SESSION["ftp_ssl"] = $ftpSSL;
                }

                $_SESSION["ftp_user"] = trim($_POST["ftp_user"]);
                $_SESSION["ftp_pass"] = trim($_POST["ftp_pass"]);
                $_SESSION["interface"] = $_POST["interface"];
                $_SESSION["skin"] = $_POST["skin"];
                $_SESSION["lang"] = $_POST["lang"];
                $_SESSION["ip_check"] = $_POST["ip_check"];

                if (connectFTP(1) == 1) {

                    $_SESSION["loggedin"]=1;

                    // Save user's IP address
                    $_SESSION["user_ip"] = $_SERVER['REMOTE_ADDR'];

                    // Set platform
                    getPlatform();

                    // Change dir if one set
                    if ($ftpDir != "") {
                        if (@ftp_chdir($conn_id, $ftpDir)) {
                            $_SESSION["dir_current"] = $ftpDir;
                        } else {
                            if (@ftp_chdir($conn_id, "~".$ftpDir))
                                $_SESSION["dir_current"] = "~".$ftpDir;
                        }
                    }

                } else {
                    displayLoginForm(1);
                }
            }

        } else {
            displayLoginForm(0);
        }
    }
}

###############################################
# DISPLAY HEADER
###############################################

function displayHeader() {

    global $version;

    // The order of these determines the proper display
    if ($_COOKIE["skin"] != "")
        $skin = $_COOKIE["skin"];
    if ($_SESSION["skin"] != "")
        $skin = $_SESSION["skin"];
    if (isset($_POST["skin"]))
        $skin = $_POST["skin"];
    if ($skin == "")
        $skin = "monsta";
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html>
    <head>
        <title>Monsta FTP v<?php echo $version; ?></title>
        <link href="style.css" rel="stylesheet" type="text/css">
        <link href="skins/<?php echo sanitizeStr($skin); ?>.css" rel="stylesheet" type="text/css">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body <?php if ($_POST["login"] == 1) { ?>onresize="setFileWindowSize('ajaxContentWindow',0,0);"<?php }?>>
<?php
}

###############################################
# DISPLAY FOOTER
###############################################

function displayFooter() {
    ?>
    </body>
    </html>
<?php
}

###############################################
# DISPLAY LOGIN FORM
###############################################

function displayLoginForm($posted) {

    global $version;
    global $ftpHost;
    global $ajaxRequest;
    global $lang_max_logins;
    global $lang_btn_login;
    global $lang_ftp_host;
    global $lang_port;
    global $lang_passive_mode;
    global $lang_username;
    global $lang_password;
    global $lang_ftp_ssl;
    global $lang_adv_interface;
    global $lang_save_login;
    global $lang_ip_check;
    global $lang_session_expired;

    // $posted - 1 for form has been posted to, 0 for hasn't

    // Check for lockout
    $date_now = date("YmdHis");
    if ($_SESSION["login_lockout"] > 0 && $date_now < $_SESSION["login_lockout"]) {

        $n = ceil(($_SESSION["login_lockout"] - $date_now) / 60);

        $_SESSION["login_error"] = str_replace("[n]",$n,$lang_max_logins);
    }

    // Check for posted form
    if ($posted == 1) {

        // Set vars
        $ftp_ssl = $_POST["ftp_ssl"];
        $ftp_host = trim($_POST["ftp_host"]);
        $ftp_user = trim($_POST["ftp_user"]);
        $ftp_pass = trim($_POST["ftp_pass"]);
        $ftp_port = trim($_POST["ftp_port"]);
        $ftp_pasv = $_POST["ftp_pasv"];
        $interface = $_POST["interface"];
        $lang = $_POST["lang"];
        $skin = $_POST["skin"];
        $login_save = $_POST["login_save"];
        $ip_check = $_POST["ip_check"];

        $_SESSION["domain"] = $_SERVER["SERVER_NAME"];

    } else {

        // Set values from cookies
        if ($_COOKIE["login_save"] == 1) {

            $ftp_ssl = $_COOKIE["ftp_ssl"];
            $ftp_host = $_COOKIE["ftp_host"];
            $ftp_user = $_COOKIE["ftp_user"];
            $ftp_pass = $_COOKIE["ftp_pass"];
            $ftp_port = $_COOKIE["ftp_port"];
            $ftp_pasv = $_COOKIE["ftp_pasv"];
            $interface = $_COOKIE["interface"];
            $lang = $_COOKIE["lang"];
            $skin = $_COOKIE["skin"];
            $login_save = $_COOKIE["login_save"];
            $ip_check = $_COOKIE["ip_check"];

        } else {

            $ftp_port = 21;
            $ftp_pasv = 1;
        }
    }

    if ($ajaxRequest == 1) {

        sessionExpired($lang_session_expired);
        logOut();

    } else {

        // Check for errors
        if ($_SESSION["login_error"] != "") {
            $height = 522;
        } else {
            $height = 458;
        }
        ?>

        <form method="post" action="?">

            <div align="center">
                <div id="loginForm" align="left">
                    <div id="loginFormTitle">Monsta FTP</div>
                    <div id="loginFormContent">

                        <?php if ($_SESSION["login_error"] != "") { ?>
                            <div id="loginFormError">
                                <?php echo $_SESSION["login_error"]; ?>
                            </div>
                        <?php } ?>

                        <input type="hidden" name="login" value="1">
                        <input type="hidden" name="openFolder" value="<?php echo $_GET["openFolder"]; ?>">

                        <?php if ($ftpHost == "") { ?>
                    <?php echo $lang_ftp_host; ?>:
                    <br><input type="text" name="ftp_host" value="<?php echo sanitizeStr($ftp_host); ?>" size="30" class="<?php if ($posted==1 && $ftp_host=="") echo "bgFormError"; ?>">
                    <?php echo $lang_port; ?>: <input type="text" name="ftp_port" value="<?php echo sanitizeStr($ftp_port) ?>" size="3" class="<?php if ($posted==1 && $ftp_port=="") echo "bgFormError"; ?>" tabindex="-1">
                        <p>
                            <?php } ?>

                            <?php echo $lang_username; ?>:
                            <br><input type="text" name="ftp_user" value="<?php echo sanitizeStr($ftp_user); ?>" class="<?php if ($posted==1 && $ftp_user=="") echo "bgFormError"; ?>">

                        <p><?php echo $lang_password; ?>:
                            <br><input type="password" name="ftp_pass" value="<?php echo sanitizeStr($ftp_pass); ?>" class="<?php if ($posted==1 && $ftp_pass=="") echo "bgFormError"; ?>">

                        <div class="floatLeft">
                            <input type="submit" value="<?php echo $lang_btn_login; ?>" id="btnLogin">
                        </div>
                        <div class="floatRight">
                            <a href="http://www.monstaftp.com">version <?php echo $version; ?></a>
                        </div>

                        <br><br>

                        <p><hr noshade>

                        <?php if ($ftpHost == "") { ?>
                        <p><input type="checkbox" name="ftp_pasv" value="1" <?php if ($ftp_pasv == 1) echo "checked"; ?> tabindex="-1"> <?php echo $lang_passive_mode; ?>
                        <p><input type="checkbox" name="ftp_ssl" value="1" <?php if ($ftp_ssl == 1) echo "checked"; ?> tabindex="-1"> <?php echo $lang_ftp_ssl; ?>
                            <?php }?>

                        <p><input type="checkbox" name="ip_check" value="1" <?php if ($ip_check == 1) echo "checked"; ?> tabindex="-1"> <?php echo $lang_ip_check; ?>
                        <p><input type="checkbox" name="interface" value="adv" <?php if ($interface == "adv" || $interface == "") echo "checked"; ?> tabindex="-1"> <?php echo $lang_adv_interface; ?>
                        <p><input type="checkbox" name="login_save" value="1" <?php if ($login_save == 1) echo "checked"; ?> tabindex="-1"> <?php echo $lang_save_login; ?>

                        <p><hr noshade>

                        <?php echo displayLangSelect($_SESSION["lang"]); ?>
                        <?php echo displaySkinSelect($skin); ?>

                    </div>
                </div>
            </div>

        </form>

        <?php
        // Reset error
        $_SESSION["login_error"] = "";
    }
}

###############################################
# CHECK LOGIN ERRORS
###############################################

function checkLoginErrors() {

    global $ftpHost;

    // Check for blank fields
    if ($ftpHost == "") {
        if ($_POST["ftp_host"] == "" || trim($_POST["ftp_user"]) == "" || trim($_POST["ftp_pass"]) == "" || trim($_POST["ftp_port"]) == "")
            return 1;
        else
            return 0;
    }

    if ($ftpHost != "") {
        if (trim($_POST["ftp_user"]) == "" || trim($_POST["ftp_pass"]) == "")
            return 1;
        else
            return 0;
    }
}

###############################################
# CONNECT TO FTP
###############################################

function connectFTP($posted) {

    global $conn_id;
    global $lockOutTime;
    global $lang_cant_connect;
    global $lang_cant_authenticate;

    if ($_SESSION["ftp_host"] != "" && $_SESSION["ftp_port"] != "" && $_SESSION["ftp_user"] != "" && $_SESSION["ftp_pass"] != "") {

        // Connect
        if ($_SESSION["ftp_ssl"] == 1)
            $conn_id = @ftp_ssl_connect($_SESSION["ftp_host"],$_SESSION["ftp_port"]) or $connectFail = 1;
        else
            $conn_id = @ftp_connect($_SESSION["ftp_host"],$_SESSION["ftp_port"]) or $connectFail = 1;

        if ($connectFail == 1) {
            $_SESSION["login_error"] = $lang_cant_connect;
            return 0;
        } else {

            // Check for lockout
            $date_now = date("YmdHis");
            if ($_SESSION["login_lockout"] == "" || ($_SESSION["login_lockout"] > 0 && $date_now > $_SESSION["login_lockout"])) {

                // Authenticate
                if (@ftp_login ($conn_id, $_SESSION["ftp_user"], $_SESSION["ftp_pass"])) {

                    if ($_SESSION["ftp_pasv"] == 1)
                        @ftp_pasv($conn_id, true);

                    $_SESSION["loggedin"] = 1;
                    $_SESSION["login_fails"] = 0;

                    return 1;

                } else {

                    $_SESSION["login_error"] = $lang_cant_authenticate;

                    // Count the failed login attempts (if form posted)
                    if ($posted == 1) {

                        $_SESSION["login_fails"]++;

                        // Lock user for 5 minutes if 3 failed attempts
                        if ($_SESSION["login_fails"] >= 3)
                            $_SESSION["login_lockout"] = date("YmdHis") + ($lockOutTime * 60);
                    }

                    return 0;
                }
            }
        }
    } else {
        return 0;
    }
}

###############################################
# WRITE HIDDEN DIVS
###############################################

function writeHiddenDivs() {
    ?>
    <div id="contextMenu" style="visibility: hidden; display: none;"></div>
    <div id="indicatorDiv" style="z-index: 1; visibility: hidden; display: none"><img src="images/indicator.gif" width="32" height="32" border="0"></div>
<?php
}

###############################################
# START FORM
###############################################

function displayFormStart() {
    ?>
    <form method="post" action="?" enctype="multipart/form-data" name="ftpActionForm" id="ftpActionForm">
<?php
}

###############################################
# END FORM
###############################################

function displayFormEnd() {
    ?>
    </form>
<?php
}

###############################################
# DISPLAY IFRAME
###############################################

function displayAjaxIframe() {
    ?>
    <iframe name="ajaxIframe" id="ajaxIframe" width="0" height="0" frameborder="0" style="visibility: hidden; display: none;"></iframe>
<?php
}

###############################################
# LOAD AJAX
###############################################

function loadAjax() {
    ?>
    <script type="text/javascript" src="ajax.js"></script>
<?php
}

###############################################
# GET FTP RAWLIST
###############################################

// Because ftp_rawlist() doesn't support folders with spaces in
// their names, it is neccessary to first change into the directory.

function getFtpRawList($folder_path) {

    global $conn_id;
    global $lang_folder_cant_access;

    $isError=0;

    if (!@ftp_chdir($conn_id, $folder_path)) {
        if (checkFirstCharTilde($folder_path) == 1) {
            if (!@ftp_chdir($conn_id, replaceTilde($folder_path))) {
                recordFileError("folder",$folder_path,$lang_folder_cant_access);
                $isError=1;
            }
        } else {
            recordFileError("folder",$folder_path,$lang_folder_cant_access);
            $isError=1;
        }
    }

    if ($isError == 0)
        return ftp_rawlist($conn_id, ".");
}

###############################################
# DISPLAY FILES & FOLDERS
###############################################

function displayFiles() {

    global $conn_id;
    global $lang_table_name;
    global $lang_table_size;
    global $lang_table_date;
    global $lang_table_time;
    global $lang_table_user;
    global $lang_table_group;
    global $lang_table_perms;

    $ftp_rawlist = getFtpRawList($_SESSION["dir_current"]);

    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    # FOLDER/FILES TABLE HEADER
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    echo "<table width=\"100%\" cellpadding=\"7\" cellspacing=\"0\" id=\"ftpTable\">";
    echo "<tr>";
    echo "<td width=\"16\" class=\"ftpTableHeadingNf\"><input type=\"checkbox\" id=\"checkboxSelector\" onClick=\"checkboxSelectAll()\"></td>";
    echo "<td width=\"16\" class=\"ftpTableHeadingNf\"></td>";
    echo "<td class=\"ftpTableHeading\">".getFtpColumnSpan("n",$lang_table_name)."</td>";
    echo "<td width=\"10%\" class=\"ftpTableHeading\">".getFtpColumnSpan("s",$lang_table_size)."</td>";
    echo "<td width=\"10%\" class=\"ftpTableHeading\">".getFtpColumnSpan("d",$lang_table_date)."</td>";
    echo "<td width=\"10%\" class=\"ftpTableHeading\">".getFtpColumnSpan("t",$lang_table_time)."</td>";

    // Only display permissions/user/group for Linux advanced
    if ($_SESSION["interface"] == "adv" && $_SESSION["win_lin"] != "win") {
        echo "<td width=\"10%\" class=\"ftpTableHeading\">".$lang_table_user."</td>";
        echo "<td width=\"10%\" class=\"ftpTableHeading\">".$lang_table_group."</td>";
        echo "<td width=\"10%\" class=\"ftpTableHeading\">".$lang_table_perms."</td>";
    }

    echo "</tr>";

    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    # FOLDER UP BUTTON
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    if ($_SESSION["dir_current"] != "/" && $_SESSION["dir_current"] != "~") {

        echo "<tr>";
        echo "<td width=\"16\"></td>";
        echo "<td width=\"16\"><img src=\"images/icon_16_folder.gif\" width=\"16\" height=\"16\"></td>";

        if ($_SESSION["interface"] == "adv")
            echo "<td colspan=\"7\">";
        else
            echo "<td colspan=\"4\">";

        // Get the parent directory
        $parent = getParentDir();

        echo "<div class=\"width100pc\" onDragOver=\"dragFile(event); selectFile('folder0',0);\" onDragLeave=\"unselectFolder('folder0')\" onDrop=\"dropFile('".rawurlencode($parent)."')\"><a href=\"#\" id=\"folder0\" draggable=\"false\" onClick=\"openThisFolder('".rawurlencode($parent)."',1)\">...</a></div>";

        echo "</td>";
        echo "</tr>";
    }

    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    # FOLDERS & FILES
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    if (sizeof($ftp_rawlist) > 0) {

        // Linux
        if ($_SESSION["win_lin"] == "lin") {
            echo createFileFolderArrayLin($ftp_rawlist,"folders");
            echo createFileFolderArrayLin($ftp_rawlist,"links");
            echo createFileFolderArrayLin($ftp_rawlist,"files");
        }

        // Windows
        if ($_SESSION["win_lin"] == "win") {
            echo createFileFolderArrayWin($ftp_rawlist,"folders");
            echo createFileFolderArrayWin($ftp_rawlist,"files");
        }
    }

    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    # CLOSE TABLE
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    echo "</table>";
}

###############################################
# GET PLATFORM
###############################################

function getPlatform() {

    global $conn_id;
    global $platformTestCount;

    if ($_SESSION["win_lin"] == "") {

        $ftp_rawlist = ftp_rawlist($conn_id, ".");

        // Check for content in array
        if (sizeof($ftp_rawlist) == 0) {

            $platformTestCount++;

            // Create a test folder
            if (@ftp_mkdir($conn_id, "test")) {

                if ($platformTestCount < 2) {
                    getPlatform();
                    @ftp_rmdir($conn_id, "test");
                }
            }

        } else {

            // Get first item in array
            $ff = $ftp_rawlist[0];

            // Split up array into values
            $ff = preg_split("/[\s]+/",$ff,9);

            // First item in Linux rawlist is permissions. In Windows it's date.
            // If length of first item in array line is 8 chars, without a-z, it's a date.
            if (strlen($ff[0]) == 8 && !preg_match("/[a-z]/i", $ff[0], $matches))
                $win_lin = "win";

            if (strlen($ff[0]) == 10 && !preg_match("/[0-9]/i", $ff[0], $matches))
                $win_lin = "lin";

            $_SESSION["win_lin"] = $win_lin;
        }
    }
}

###############################################
# CREATE FILE/FOLDER ARRAY FOR LINUX
###############################################

function createFileFolderArrayLin($ftp_rawlist,$type) {

    // Go through array of files/folders
    foreach($ftp_rawlist AS $ff) {

        // Reset values
        $time="";
        $year="";

        // Split up array into values
        $ff = preg_split("/[\s]+/",$ff,9);

        $perms = $ff[0];
        $user = $ff[2];
        $group = $ff[3];
        $size = $ff[4];
        $month = $ff[5];
        $day = $ff[6];
        $file = $ff[8];

        // Check if file starts with a dot
        $dot_prefix=0;
        if (preg_match("/^\.+/",$file) && $_SESSION["interface"] == "bas")
            $dot_prefix=1;

        if ($file != "." && $file != ".." && $dot_prefix == 0) {

            // Where the last mod date is the previous year, the year will be displayed in place of the time
            if (preg_match("/:/",$ff[7]))
                $time = $ff[7];
            else
                $year = $ff[7];

            // Set date
            $date = formatFtpDate($day,$month,$year);

            // Reset user and group
            if ($user == "0")
                $user = "-";
            if ($group == "0")
                $group = "-";

            // Add folder to array
            if (getFileType($perms) == "d") {
                $foldAllAr[] = $file."|d|".$date."|".$time."|".$user."|".$group."|".$perms;
                $foldNameAr[] = $file;
                $foldDateAr[] = $date;
                $foldTimeAr[] = $time;
                $foldUserAr[] = $user;
                $foldGroupAr[] = $group;
                $foldPermsAr[] = $perms;
            }

            // Add link to array
            if (getFileType($perms) == "l") {
                $linkAllAr[] = $file."|l|".$date."|".$time."|".$user."|".$group."|".$perms;
                $linkNameAr[] = $file;
                $linkDateAr[] = $date;
                $linkTimeAr[] = $time;
                $linkUserAr[] = $user;
                $linkGroupAr[] = $group;
                $linkPermsAr[] = $perms;
            }

            // Add file to array
            if (getFileType($perms) == "f") {
                $fileAllAr[] = $file."|".$size."|".$date."|".$time."|".$user."|".$group."|".$perms;
                $fileNameAr[] = $file;
                $fileSizeAr[] = $size;
                $fileDateAr[] = $date;
                $fileTimeAr[] = $time;
                $fileUserAr[] = $user;
                $fileGroupAr[] = $group;
                $filePermsAr[] = $perms;
            }
        }
    }

    // Check there are files and/or folders to display
    if (is_array($foldAllAr) || is_array($linkAllAr) || is_array($fileAllAr)) {

        // Set sorting order
        if ($_POST["sort"] == "")
            $sort = "n";
        else
            $sort = $_POST["sort"];

        if ($_POST["ord"] == "")
            $ord = "asc";
        else
            $ord = $_POST["ord"];

        // Return folders
        if ($type == "folders") {

            if (is_array($foldAllAr)) {

                // Set the folder arrays to sort
                if ($sort == "n") $sortAr = $foldNameAr;
                if ($sort == "d") $sortAr = $foldDateAr;
                if ($sort == "t") $sortAr = $foldTimeAr;
                if ($sort == "u") $sortAr = $foldUserAr;
                if ($sort == "g") $sortAr = $foldGroupAr;
                if ($sort == "p") $sortAr = $foldPermsAr;

                // Multisort array
                if (is_array($sortAr)) {
                    if ($ord == "asc")
                        array_multisort($sortAr, SORT_ASC, $foldAllAr);
                    else
                        array_multisort($sortAr, SORT_DESC, $foldAllAr);
                }

                // Format and display folder content
                $folders = getFileListHtml($foldAllAr, "icon_16_folder.gif");
            }

            return $folders;
        }

        // Return links
        if ($type == "links") {

            if (is_array($linkAllAr)) {

                // Set the folder arrays to sort
                if ($sort == "n") $sortAr = $linkNameAr;
                if ($sort == "d") $sortAr = $linkDateAr;
                if ($sort == "t") $sortAr = $linkTimeAr;
                if ($sort == "u") $sortAr = $linkUserAr;
                if ($sort == "g") $sortAr = $linkGroupAr;
                if ($sort == "p") $sortAr = $linkPermsAr;

                // Multisort array
                if (is_array($sortAr)) {
                    if ($ord == "asc")
                        array_multisort($sortAr, SORT_ASC, $linkAllAr);
                    else
                        array_multisort($sortAr, SORT_DESC, $linkAllAr);
                }

                // Format and display folder content
                $links = getFileListHtml($linkAllAr, "icon_16_link.gif");
            }

            return $links;
        }

        // Return files
        if ($type == "files") {

            if (is_array($fileAllAr)) {

                // Set the folder arrays to sort
                if ($sort == "n") $sortAr = $fileNameAr;
                if ($sort == "s") $sortAr = $fileSizeAr;
                if ($sort == "d") $sortAr = $fileDateAr;
                if ($sort == "t") $sortAr = $fileTimeAr;
                if ($sort == "u") $sortAr = $fileUserAr;
                if ($sort == "g") $sortAr = $fileGroupAr;
                if ($sort == "p") $sortAr = $filePermsAr;

                // Multisort folders
                if ($ord == "asc")
                    array_multisort($sortAr, SORT_ASC, $fileAllAr);
                else
                    array_multisort($sortAr, SORT_DESC, $fileAllAr);

                // Format and display file content
                $files = getFileListHtml($fileAllAr, "icon_16_file.gif");
            }

            return $files;
        }
    }
}

###############################################
# CREATE FILE/FOLDER ARRAY FOR WINDOWS
###############################################

function createFileFolderArrayWin($ftp_rawlist,$type) {

    // Go through array of files/folders
    foreach($ftp_rawlist AS $ff) {

        // Split up array into values
        $ff = preg_split("/[\s]+/",$ff,4);

        $date = $ff[0];
        $time = $ff[1];
        $size = $ff[2];
        $file = $ff[3];

        if ($size == "<DIR>") $size = "d";

        // Format date
        $day = substr($date,3,2);
        $month = substr($date,0,2);
        $year = substr($date,6,2);
        $date = formatFtpDate($day,$month,$year);

        // Format time
        $time = formatWinFtpTime($time);

        // Add folder to array
        if ($size == "d") {
            $foldAllAr[] = $file."|d|".$date."|".$time."|||";
            $foldNameAr[] = $file;
            $foldDateAr[] = $date;
            $foldTimeAr[] = $time;
        }

        // Add file to array
        if ($size != "d") {
            $fileAllAr[] = $file."|".$size."|".$date."|".$time."|||";
            $fileNameAr[] = $file;
            $fileSizeAr[] = $size;
            $fileDateAr[] = $date;
            $fileTimeAr[] = $time;
        }
    }

    // Check there are files and/or folders to display
    if (is_array($foldAllAr) || is_array($fileAllAr)) {

        // Set sorting order
        if ($_POST["sort"] == "")
            $sort = "n";
        else
            $sort = $_POST["sort"];

        if ($_POST["ord"] == "")
            $ord = "asc";
        else
            $ord = $_POST["ord"];

        // Return folders
        if ($type == "folders") {

            if (is_array($foldAllAr)) {

                // Set the folder arrays to sort
                if ($sort == "n") $sortAr = $foldNameAr;
                if ($sort == "d") $sortAr = $foldDateAr;
                if ($sort == "t") $sortAr = $foldTimeAr;

                // Multisort array
                if (is_array($sortAr)) {
                    if ($ord == "asc")
                        array_multisort($sortAr, SORT_ASC, $foldAllAr);
                    else
                        array_multisort($sortAr, SORT_DESC, $foldAllAr);
                }

                // Format and display folder content
                $folders = getFileListHtml($foldAllAr, "icon_16_folder.gif");
            }

            return $folders;
        }

        // Return files
        if ($type == "files") {

            if (is_array($fileAllAr)) {

                // Set the folder arrays to sort
                if ($sort == "n") $sortAr = $fileNameAr;
                if ($sort == "s") $sortAr = $fileSizeAr;
                if ($sort == "d") $sortAr = $fileDateAr;
                if ($sort == "t") $sortAr = $fileTimeAr;

                // Multisort folders
                if ($ord == "asc")
                    array_multisort($sortAr, SORT_ASC, $fileAllAr);
                else
                    array_multisort($sortAr, SORT_DESC, $fileAllAr);

                // Format and display file content
                $files = getFileListHtml($fileAllAr, "icon_16_file.gif");
            }

            return $files;
        }
    }
}

###############################################
# GET FTP COLUMN SPAN
###############################################

function getFileListHtml($array,$image) {

    global $trCount;
    global $dateFormatUsa;

    if ($trCount == 1)
        $trCount=1;
    else
        $trCount=0;

    $i=1;
    foreach ($array AS $file) {

        list($file,$size,$date,$time,$user,$group,$perms) = explode("|",$file);

        // Folder check (lin/win)
        if ($size == "d")
            $action = "folderAction";
        // Link check (lin/win)
        if ( $size == "l")
            $action = "linkAction";
        // File check (lin/win)
        if ($size != "d" && $size != "l")
            $action = "fileAction";

        // Set file path
        if ($size == "l") {

            $file_path = getPathFromLink($file);
            $file = preg_replace("/ -> .*/","",$file);

        } else {

            if ($_SESSION["dir_current"] == "/")
                $file_path = "/".$file;
            else
                $file_path = $_SESSION["dir_current"]."/".$file;
        }

        if ($trCount == 0) {
            $trClass = "trBg0";
            $trCount=1;
        } else {
            $trClass = "trBg1";
            $trCount=0;
        }

        // Check for checkbox check (only if action button clicked"
        if ($_POST["ftpAction"] != "") {
            if (
                (sizeof($_SESSION["clipboard_rename"]) > 1 && in_array($file,$_SESSION["clipboard_rename"]))
                ||
                (sizeof($_SESSION["clipboard_chmod"]) > 1 && in_array($file_path,$_SESSION["clipboard_chmod"])))
                $checked = "checked";
            else
                $checked = "";

        } else {
            $checked = "";
        }

        // Set the date
        if ($dateFormatUsa == 1)
            $date = substr($date,4,2)."/".substr($date,6,2)."/".substr($date,2,2);
        else
            $date = substr($date,6,2)."/".substr($date,4,2)."/".substr($date,2,2);

        $html .= "<tr class=\"".$trClass."\">";
        $html .= "<td>";
        $html .= "<input type=\"checkbox\" name=\"".$action."[]\" value=\"".rawurlencode($file_path)."\" onclick=\"checkFileChecked()\" ".$checked.">";
        $html .= "</td>";
        $html .= "<td><img src=\"images/".$image."\" width=\"16\" height=\"16\"></td>";
        $html .= "<td>";

        // Display Folders
        if ($action == "folderAction")
            $html .= "<div class=\"width100pc\" onDragOver=\"dragFile(event); selectFile('folder".$i."',0);\" onDragLeave=\"unselectFolder('folder".$i."')\" onDrop=\"dropFile('".rawurlencode($file_path)."')\"><a href=\"#\" id=\"folder".$i."\" onClick=\"openThisFolder('".rawurlencode($file_path)."',1)\" onContextMenu=\"selectFile(this.id,1); displayContextMenu(event,'','".rawurlencode($file_path)."',".assignWinLinNum().")\" draggable=\"true\" onDragStart=\"selectFile(this.id,1); setDragFile('','".rawurlencode($file_path)."')\">".sanitizeStr($file)."</a></div>";

        // Display Links
        if ($action == "linkAction")
            $html .= "<div class=\"width100pc\"><a href=\"#\" id=\"folder".$i."\" onClick=\"openThisFolder('".rawurlencode($file_path)."',1)\" onContextMenu=\"\" draggable=\"false\">".sanitizeStr($file)."</a></div>";

        // Display files
        if ($action == "fileAction")
            $html .= "<a href=\"?dl=".rawurlencode($file_path)."\" id=\"file".$i."\" target=\"ajaxIframe\" onContextMenu=\"selectFile(this.id,1); displayContextMenu(event,'".rawurlencode($file_path)."','',".assignWinLinNum().")\" draggable=\"true\" onDragStart=\"selectFile(this.id,1); setDragFile('".rawurlencode($file_path)."','')\">".sanitizeStr($file)."</a>";

        $html .= "</td>";
        $html .= "<td>".formatFileSize($size)."</td>";
        $html .= "<td>".$date."</td>";
        $html .= "<td>".$time."</td>";

        if ($_SESSION["interface"] == "adv" && $_SESSION["win_lin"] == "lin") {
            $html .= "<td>".$user."</td>";
            $html .= "<td>".$group."</td>";
            $html .= "<td>".$perms."</td>";
        }

        $html .= "</tr>";

        $i++;
    }

    return $html;
}

###############################################
# GET PATH FROM LINK
###############################################

function getPathFromLink($file) {

    $file_path = preg_replace("/.* -> /","",$file);

    // Check if path is not absolute
    if (substr($file_path,0,1) != "/") {

        // Count occurances of ../
        $i=0;
        while (substr($file_path,0,3) == "../") {
            $i++;
            $file_path = substr($file_path,3,strlen($file_path));
        }

        $dir_current = $_SESSION["dir_current"];

        // Get the real parent
        for ($j=0;$j<$i;$j++) {

            $path_parts = pathinfo($dir_current);
            $dir_current = $path_parts['dirname'];
        }

        // Set the path
        if ($dir_current == "/")
            $file_path = "/".$file_path;
        else
            $file_path = $dir_current."/".$file_path;
    }

    if ($file_path == "~/")
        $file_path = "~";

    return $file_path;
}

###############################################
# FORMAT FTP DATE
###############################################

function formatFtpDate($day,$month,$year) {

    if (strlen($day) == 1)
        $day = "0".$day;

    if ($year == "")
        $year = date("Y");

    if (strlen($year) == 2) {

        // To avoid a future Y2K problem, check the first two digits of year on Windows
        if ($year > 00 && $year < 99)
            $year = substr(date("Y"),0,2).$year;
        else
            $year = (substr(date("Y"),0,2)-1).$year;
    }

    if ($month == "Jan") $month = "01";
    if ($month == "Feb") $month = "02";
    if ($month == "Mar") $month = "03";
    if ($month == "Apr") $month = "04";
    if ($month == "May") $month = "05";
    if ($month == "Jun") $month = "06";
    if ($month == "Jul") $month = "07";
    if ($month == "Aug") $month = "08";
    if ($month == "Sep") $month = "09";
    if ($month == "Oct") $month = "10";
    if ($month == "Nov") $month = "11";
    if ($month == "Dec") $month = "12";

    $date = $year.$month.$day;

    return $date;
}

###############################################
# FORMAT WINDOWS FTP TIME
###############################################

function formatWinFtpTime($time) {

    $h = substr($time,0,2);
    $m = substr($time,3,2);
    $am_pm = substr($time,5,2);

    if ($am_pm == "PM")
        $h = $h + 12;

    $time = $h.":".$m;

    return $time;
}

###############################################
# CHANGE FTP DIRECTORY (OPEN FOLDER)
###############################################

function openFolder() {

    global $conn_id;
    global $lang_folder_doesnt_exist;

    $isError=0;

    if ($_SESSION["loggedin"] == 1) {

        // Set the folder to open
        if ($_SESSION["dir_current"] != "")
            $dir = $_SESSION["dir_current"];
        if ($_POST["openFolder"] != "")
            $dir = quotesUnescape($_POST["openFolder"]);

        // Check dir is set
        if ($dir == "") {

            // No folder set (must be first login), so set home dir
            if ($_SESSION["win_lin"] == "lin")
                $dir = "~";
            if ($_SESSION["win_lin"] == "win")
                $dir = "/";
        }

        // Attempt to change directory
        if (!@ftp_chdir($conn_id, $dir)) {
            if (checkFirstCharTilde($dir) == 1) {
                if (!@ftp_chdir($conn_id, replaceTilde($dir))) {
                    recordFileError("folder",$dir,$lang_folder_doesnt_exist);
                    $isError=1;
                }
            } else {
                recordFileError("folder",$dir,$lang_folder_doesnt_exist);
                $isError=1;
            }
        }

        if ($isError == 0) {

            // Set new directory
            $_SESSION["dir_current"] = $dir;

            // Record new directory to history
            if (!is_array($_SESSION["dir_history"])) // array check
                $_SESSION["dir_history"] = array();
            if (!in_array($dir,$_SESSION["dir_history"])) {
                $_SESSION["dir_history"][] = $dir;
                asort($_SESSION["dir_history"]); // sort array
            }

            return 1;

        } else {

            // Delete item from history
            deleteFtpHistory($dir);

            // Change to previous directory (if folder to open is currently open)
            if ($_POST["openFolder"] == $_SESSION["dir_current"] || $_POST["openFolder"] == "")
                $_SESSION["dir_current"] = getParentDir();

            return 0;
        }
    }
}

###############################################
# CHECK LOG OUT
###############################################

function checkLogOut() {

    if ($_GET["logout"] == 1)
        logOut();
}

###############################################
# LOG OUT
###############################################

function logOut() {

    $_SESSION["user_ip"] = "";
    $_SESSION["loggedin"] = "";
    $_SESSION["win_lin"] = "";
    $_SESSION["login_error"] = "";
    $_SESSION["login_fails"] = "";
    $_SESSION["login_lockout"] = "";
    $_SESSION["ftp_host"] = "";
    $_SESSION["ftp_user"] = "";
    $_SESSION["ftp_pass"] = "";
    $_SESSION["ftp_port"] = "";
    $_SESSION["ftp_pasv"] = "";
    $_SESSION["interface"] = "";
    $_SESSION["dir_current"] = "";
    $_SESSION["dir_history"] = "";
    $_SESSION["clipboard_chmod"] = "";
    $_SESSION["clipboard_files"] = "";
    $_SESSION["clipboard_folders"] = "";
    $_SESSION["clipboard_rename"] = "";
    $_SESSION["copy"] = "";
    $_SESSION["errors"] = "";
    $_SESSION["upload_limit"] = "";

    session_destroy();
}

###############################################
# FORMAT FILE SIZES
###############################################

function formatFileSize($size) {

    global $lang_size_b;
    global $lang_size_kb;
    global $lang_size_mb;
    global $lang_size_gb;

    if ($size == "d" || $size == "l") {

        $size="";

    } else {

        if ($size < 1024) {
            $size = round($size,2);
            //$size = round($size,2).$lang_size_b;
        } elseif ($size < (1024*1024)) {
            $size = round(($size/1024),0).$lang_size_kb;
        } elseif ($size < (1024*1024*1024)) {
            $size = round((($size/1024)/1024),0).$lang_size_mb;
        } elseif ($size < (1024*1024*1024*1024)) {
            $size = round(((($size/1024)/1024)/1024),0).$lang_size_gb;
        }
    }

    return $size;
}

###############################################
# GET FTP COLUMN SPAN
###############################################

function getFtpColumnSpan($sort,$name) {

    // Check current column
    if ($_POST["sort"] == $sort && $_POST["ord"] == "desc") {
        $ord = "asc";
    } else {
        $ord = "desc";
    }

    return "<span onclick=\"processForm('&ftpAction=openFolder&openFolder=".rawurlencode($_SESSION["dir_current"])."&sort=".$sort."&ord=".$ord."')\" class=\"cursorPointer\">".$name."</span>";
}

###############################################
# DISPLAY FTP BUTTONS
###############################################

function displayFtpActions() {

    global $lang_btn_refresh;
    global $lang_btn_cut;
    global $lang_btn_copy;
    global $lang_btn_paste;
    global $lang_btn_rename;
    global $lang_btn_delete;
    global $lang_btn_chmod;
    global $lang_btn_logout;
    ?>
    <div id="ftpActionButtonsDiv">
        <input type="button" value="<?php echo $lang_btn_refresh; ?>" onClick="refreshListing()" class="<?php echo adjustButtonWidth($lang_btn_refresh); ?>">
        <input type="button" id="actionButtonCut" value="<?php echo $lang_btn_cut; ?>" onClick="actionFunctionCut('','');" disabled class="<?php echo adjustButtonWidth($lang_btn_cut); ?>">
        <input type="button" id="actionButtonCopy" value="<?php echo $lang_btn_copy; ?>" onClick="actionFunctionCopy('','');" disabled class="<?php echo adjustButtonWidth($lang_btn_copy); ?>">
        <input type="button" id="actionButtonPaste" value="<?php echo $lang_btn_paste; ?>" onClick="actionFunctionPaste('');" disabled class="<?php echo adjustButtonWidth($lang_btn_paste); ?>">
        <input type="button" id="actionButtonRename" value="<?php echo $lang_btn_rename; ?>" onClick="actionFunctionRename('','');" disabled class="<?php echo adjustButtonWidth($lang_btn_rename); ?>">
        <input type="button" id="actionButtonDelete" value="<?php echo $lang_btn_delete; ?>" onClick="actionFunctionDelete('','');" disabled class="<?php echo adjustButtonWidth($lang_btn_delete); ?>">
        <?php if ($_SESSION["interface"] == "adv" && $_SESSION["win_lin"] == "lin") { ?>
            <input type="button" id="actionButtonChmod" value="<?php echo $lang_btn_chmod; ?>" onClick="actionFunctionChmod('','');" disabled class="<?php echo adjustButtonWidth($lang_btn_chmod); ?>">
        <?php } ?>
        <div class="floatRight">
            <input type="button" value="<?php echo $lang_btn_logout; ?>" onClick="actionFunctionLogout();" class="<?php echo adjustButtonWidth($lang_btn_logout); ?>">
        </div>
    </div>
<?php
}

###############################################
# DISPLAY UPLOAD PROGRESS
###############################################

function displayUploadProgress() {

    global $lang_xfer_file;
    global $lang_xfer_size;
    global $lang_xfer_progress;
    global $lang_xfer_elapsed;
    global $lang_xfer_uploaded;
    global $lang_xfer_rate;
    global $lang_xfer_remain;
    ?>
    <div id="uploadProgressDiv" style="visibility:hidden; display:none">
        <table width="100%" cellpadding="7" cellspacing="0" id="uploadProgressTable">
            <tr>
                <td class="ftpTableHeadingNf" width="1%"></td>
                <td class="ftpTableHeading" size="35%"><?php echo $lang_xfer_file; ?></td>
                <td class="ftpTableHeading" width="7%"><?php echo $lang_xfer_size; ?></td>
                <td class="ftpTableHeading" width="21%"><?php echo $lang_xfer_progress; ?></td>
                <td class="ftpTableHeading" width="9%"><?php echo $lang_xfer_elapsed; ?></td>
                <td class="ftpTableHeading" width="7%"><?php echo $lang_xfer_uploaded; ?></td>
                <td class="ftpTableHeading" width="9%"><?php echo $lang_xfer_rate; ?></td>
                <td class="ftpTableHeading" width="10%"><?php echo $lang_xfer_remain; ?></td>
                <td class="ftpTableHeading" width="1%"></td>
            </tr>
        </table>
    </div>
<?php
}

###############################################
# WINDOW FOOTER
###############################################

function displayAjaxFooter() {

    global $lang_btn_new_folder;
    global $lang_btn_new_file;
    global $lang_info_host;
    global $lang_info_user;
    global $lang_info_upload_limit;
    global $lang_info_drag_drop;

    ?>
    <div id="footerDiv">

    <div id="hostInfoDiv">
        <span><?php echo $lang_info_host; ?>:</span> <?php echo $_SESSION["ftp_host"]; ?>
        <span><?php echo $lang_info_user; ?>:</span> <?php echo $_SESSION["ftp_user"]; ?>
        <span><?php echo $lang_info_upload_limit; ?>:</span> <?php echo formatFileSize($_SESSION["upload_limit"]); ?>
        <!-- <span><?php echo $lang_info_drag_drop; ?>:</span> <div id="dropFilesCheckDiv"></div> --> <!-- Drag & Drop check commented out as considered redundant -->
    </div>

    <div class="floatLeft10">
        <input type="button" value="<?php echo $lang_btn_new_folder; ?>" onClick="processForm('&ftpAction=newFolder')" class="<?php echo adjustButtonWidth($lang_btn_new_folder); ?>">
    </div>

    <div class="floatLeft10">
        <input type="button" value="<?php echo $lang_btn_new_file; ?>" onClick="processForm('&ftpAction=newFile')" class="<?php echo adjustButtonWidth($lang_btn_new_file); ?>">
    </div>

    <div id="uploadButtonsDiv"><div>

    </div>
<?php
}

###############################################
# DISPLAY FTP HISTORY
###############################################

function displayFtpHistory() {
    ?>
    <select onChange="openThisFolder(this.options[this.selectedIndex].value,1)" id="ftpHistorySelect">
        <?php
        if (is_array($_SESSION["dir_history"])) {

            foreach ($_SESSION["dir_history"] AS $dir) {

                $dir_display = $dir;
                $dir_display = sanitizeStr($dir_display);
                $dir_display = replaceTilde($dir_display);

                echo "<option value=\"".rawurlencode($dir)."\"";

                // Check if this is current directory
                if ($_SESSION["dir_current"] == $dir)
                    echo " selected";

                echo ">";
                echo $dir_display;
                echo "</option>";
            }
        }
        ?>
    </select>
<?php
}

###############################################
# PROCESS ACTIONS
###############################################

function processActions() {

    $ftpAction = $_POST["ftpAction"];

    if ($ftpAction == "")
        $ftpAction = $_GET["ftpAction"];

    // Open folder (always called)
    if (openFolder() == 1) {

        // New file
        if ($ftpAction == "newFile")
            newFile();

        // New folder
        if ($ftpAction == "newFolder")
            newFolder();

        // Upload file
        if ($ftpAction == "upload")
            uploadFile();

        // Cut
        if ($ftpAction == "cut")
            cutFilesPre();

        // Copy
        if ($ftpAction == "copy")
            copyFilesPre();

        // Paste
        if ($ftpAction == "paste")
            pasteFiles();

        // Delete
        if ($ftpAction == "delete")
            deleteFiles();

        // Rename
        if ($ftpAction == "rename")
            renameFiles();

        // Chmod
        if ($ftpAction == "chmod")
            chmodFiles();

        // Drag & Drop
        if ($ftpAction == "dragDrop")
            dragDropFiles();

        // Edit
        if ($ftpAction == "edit")
            editFile();
    }
}

###############################################
# CLIPBOARD FILES
###############################################

function clipboard_files () {

    // Recreate arrays
    $folderArray = recreateFileFolderArrays("folder");
    $fileArray = recreateFileFolderArrays("file");

    // Reset cut session var
    $_SESSION["clipboard_folders"] = array();
    $_SESSION["clipboard_files"] = array();

    // Folders
    foreach($folderArray AS $folder) {
        $_SESSION["clipboard_folders"][] = quotesUnescape($folder);
    }

    // Files
    foreach($fileArray AS $file) {
        $_SESSION["clipboard_files"][] = quotesUnescape($file);
    }
}

###############################################
# CUT FILES & FOLDERS
###############################################

function cutFilesPre() {

    $_SESSION["copy"] = 0;
    clipboard_files();
}

###############################################
# COPY FILES & FOLDERS
###############################################

function copyFilesPre() {

    $_SESSION["copy"] = 1;
    clipboard_files();
}

###############################################
# PASTE FILES
###############################################

function pasteFiles() {

    if ($_SESSION["copy"] == 1)
        copyFiles();
    else
        moveFiles();
}

###############################################
# MOVE FILES (CUT)
###############################################

function moveFiles() {

    global $conn_id;
    global $lang_move_conflict;
    global $lang_folder_exists;
    global $lang_folder_cant_move;
    global $lang_file_exists;
    global $lang_file_cant_move;

    // Check for a right-clicked folder (else it's current)
    if (isset($_POST["rightClickFolder"]))
        $folderMoveTo = quotesUnescape($_POST["rightClickFolder"]);
    else
        $folderMoveTo = $_SESSION["dir_current"];

    // Check if destination folder is a sub-folder
    if (sizeof($_SESSION["clipboard_folders"]) > 0) {

        $sourceFolder = str_replace("/","\/",$_SESSION["clipboard_folders"][0]);

        if (preg_match("/".$sourceFolder."/", $folderMoveTo)) {

            $_SESSION["errors"][] = $lang_move_conflict;

            $moveError=1;
        }
    }

    if ($moveError != 1) {

        // Folders
        foreach ($_SESSION["clipboard_folders"] as $folder_to_move) {

            $isError=0;

            // Create the new filename and path
            $file_destination = getFileFromPath($folder_to_move);
            $folder = getFileFromPath($folder_to_move);

            // Check if folder exists
            if (checkFileExists("d",$folder,$folderMoveTo) == 1) {
                recordFileError("folder",tidyFolderPath($folderMoveTo,$folder),$lang_folder_exists);
            } else {

                if (!@ftp_rename($conn_id, $folder_to_move, $file_destination)) {
                    if (checkFirstCharTilde($folder_to_move) == 1) {
                        if (!@ftp_rename($conn_id, replaceTilde($folder_to_move), replaceTilde($file_destination))) {
                            recordFileError("folder",tidyFolderPath($file_destination,$folder_to_move),$lang_folder_cant_move);
                            $isError=1;
                        }
                    } else {
                        recordFileError("folder",tidyFolderPath($file_destination,$folder_to_move),$lang_folder_cant_move);
                        $isError=1;
                    }
                }

                if ($isError == 0)
                    deleteFtpHistory($folder_to_move);
            }
        }

        // Files
        foreach ($_SESSION["clipboard_files"] as $file_to_move) {

            $isError=0;

            // Create the new filename and path
            $file_destination = $folderMoveTo."/".getFileFromPath($file_to_move);
            $file = getFileFromPath($file_to_move);

            // Check if file exists
            if (checkFileExists("f",$file,$folderMoveTo) == 1) {
                recordFileError("file",$file,$lang_file_exists);
            } else {

                if (!@ftp_rename($conn_id, $file_to_move, $file_destination)) {
                    if (checkFirstCharTilde($file_to_move) == 1) {
                        if (!@ftp_rename($conn_id, replaceTilde($file_to_move), replaceTilde($file_destination))) {
                            recordFileError("file",$file_to_move,$lang_file_cant_move);
                        }
                    } else {
                        recordFileError("file",$file_to_move,$lang_file_cant_move);
                    }
                }
            }
        }
    }

    $_SESSION["clipboard_folders"] = array();
    $_SESSION["clipboard_files"] = array();
}

###############################################
# DRAG & DROP FILES
###############################################

function dragDropFiles() {

    global $conn_id;
    global $lang_file_exists;
    global $lang_folder_exists;
    global $lang_file_cant_move;

    $fileExists=0;
    $dragFile = quotesUnescape($_POST["dragFile"]);
    $dropFolder = quotesUnescape($_POST["dropFolder"]);
    $file_name = getFileFromPath($dragFile);

    // Check if file exists
    if (checkFileExists("f",$file_name,$dropFolder) == 1) {
        recordFileError("file",tidyFolderPath($dropFolder,$file_name),$lang_file_exists);
        $fileExists=1;
    }

    // Check if folder exists
    if (checkFileExists("d",$file_name,$dropFolder) == 1) {
        recordFileError("folder",tidyFolderPath($dropFolder,$file_name),$lang_folder_exists);
        $fileExists=1;
    }

    if ($fileExists == 0) {

        $isError=0;

        if (!@ftp_rename($conn_id, $dragFile, $dropFolder."/".$file_name)) {
            if (checkFirstCharTilde($dragFile) == 1) {
                if (!@ftp_rename($conn_id, replaceTilde($dragFile), replaceTilde($dropFolder)."/".$file_name)) {
                    recordFileError("file",getFileFromPath($dragFile),$lang_file_cant_move);
                    $isError=1;
                }
            } else {
                recordFileError("file",getFileFromPath($dragFile),$lang_file_cant_move);
                $isError=1;
            }
        }

        if ($isError == 0) {

            // Delete item from history
            deleteFtpHistory($dragFile);
        }
    }
}

###############################################
# COPY FILES
###############################################

function copyFiles() {

    // As there is no PHP function to copy files by FTP on a remote server, the files
    // need to be downloaded to the client server and then uploaded to the copy location.

    global $conn_id;
    global $serverTmp;
    global $lang_folder_exists;
    global $lang_file_exists;
    global $lang_server_error_down;
    global $lang_server_error_up;

    // Check for a right-clicked folder (else it's current)
    if (isset($_POST["rightClickFolder"]))
        $folderMoveTo = quotesUnescape($_POST["rightClickFolder"]);
    else
        $folderMoveTo = $_SESSION["dir_current"];

    // Folders
    foreach ($_SESSION["clipboard_folders"] as $folder) {

        $folder_name = getFileFromPath($folder);

        $path_parts = pathinfo($folder);
        $dir_source = $path_parts['dirname'];

        // Check if folder exists
        if (checkFileExists("f",$folder_name,$folderMoveTo) == 1) {
            recordFileError("folder",tidyFolderPath($folderMoveTo,$folder_name),$lang_folder_exists);
        } else {
            copyFolder($folder_name,$folderMoveTo,$dir_source);
        }
    }

    // Files
    foreach ($_SESSION["clipboard_files"] as $file) {

        $isError=0;

        $file_name = getFileFromPath($file);
        $fp1 = $serverTmp."/".$file_name;
        $fp2 = $file;
        $fp3 = $folderMoveTo."/".$file_name;

        // Check if file exists
        if (checkFileExists("f",$file_name,$folderMoveTo) == 1) {
            recordFileError("file",tidyFolderPath($folderMoveTo,$file_name),$lang_file_exists);
        } else {

            // Download file to client server
            if (!@ftp_get($conn_id, $fp1, $fp2, FTP_BINARY)) {
                if (checkFirstCharTilde($fp2) == 1) {
                    if (!@ftp_get($conn_id, $fp1, replaceTilde($fp2), FTP_BINARY)) {
                        recordFileError("file",$file_name,$lang_server_error_down);
                        $isError=1;
                    }
                } else {
                    recordFileError("file",$file_name,$lang_server_error_down);
                    $isError=1;
                }
            }

            if ($isError == 0) {

                // Upload file to remote server
                if (!@ftp_put($conn_id, $fp3, $fp1, FTP_BINARY)) {
                    if (checkFirstCharTilde($fp3) == 1) {
                        if (!@ftp_put($conn_id, replaceTilde($fp3), $fp1, FTP_BINARY))
                            recordFileError("file",$file_name,$lang_server_error_up);
                    } else {
                        recordFileError("file",$file_name,$lang_server_error_up);
                    }
                }
            }
        }

        // Delete tmp file
        unlink($fp1);
    }
}

###############################################
# GET PERMISSIONS OF FILE/FOLDER
###############################################

function getPerms($folder,$file_name) {

    global $conn_id;

    $ftp_rawlist = getFtpRawList($folder);

    if (is_array($ftp_rawlist)) {

        foreach($ftp_rawlist AS $ff) {

            // Split up array into values
            $ff = preg_split("/[\s]+/",$ff,9);

            $perms = $ff[0];
            $file = $ff[8];

            if ($file == $file_name) {
                $perms = getChmodNumber($perms);
                $perms = formatChmodNumber($perms);
                return $perms;
            }
        }
    }
}

###############################################
# COPY FOLDERS
###############################################

function copyFolder($folder,$dir_destin,$dir_source) {

    global $conn_id;
    global $serverTmp;
    global $lang_folder_cant_access;
    global $lang_folder_exists;
    global $lang_folder_cant_chmod;
    global $lang_folder_cant_make;
    global $lang_server_error_down;
    global $lang_file_cant_chmod;
    global $lang_chmod_no_support;

    $isError=0;

    // Check if ftp_chmod() exists
    if (!function_exists('ftp_chmod')) {
        $_SESSION["errors"][] = $lang_chmod_no_support;
    }

    // Check source folder exists
    if (!@ftp_chdir($conn_id, $dir_source."/".$folder)) {
        if (checkFirstCharTilde($dir_source) == 1) {
            if (!@ftp_chdir($conn_id, replaceTilde($dir_source)."/".$folder)) {
                recordFileError("folder",tidyFolderPath($dir_destin,$folder),$lang_folder_cant_access);
                $isError=1;
            }
        } else {
            recordFileError("folder",tidyFolderPath($dir_destin,$folder),$lang_folder_cant_access);
            $isError=1;
        }
    }

    if ($isError == 0) {

        // Check if destination folder exists
        if (checkFileExists("d",$folder,$dir_destin) == 1) {
            recordFileError("folder",tidyFolderPath($dir_destin,$folder),$lang_folder_exists);
        } else {

            // Create the new folder
            if (!@ftp_mkdir($conn_id, $dir_destin."/".$folder)) {
                if (checkFirstCharTilde($dir_destin) == 1) {
                    if (!@ftp_mkdir($conn_id, replaceTilde($dir_destin)."/".$folder)) {
                        recordFileError("folder",tidyFolderPath($dir_destin,$folder),$lang_folder_cant_make);
                        $isError=1;
                    }
                } else {
                    recordFileError("folder",tidyFolderPath($dir_destin,$folder),$lang_folder_cant_make);
                    $isError=1;
                }
            }
        }
    }

    if ($isError == 0) {

        // Copy permissions (Lin)
        if ($_SESSION["win_lin"] == "lin") {

            $mode = getPerms($dir_source,$folder);
            $lang_folder_cant_chmod = str_replace("[perms]",$mode,$lang_folder_cant_chmod);

            if (function_exists('ftp_chmod')) {
                if (!ftp_chmod($conn_id, $mode, $dir_destin."/".$folder)) {
                    if (checkFirstCharTilde($dir_destin) == 1) {
                        if (!@ftp_chmod($conn_id, $mode, replaceTilde($dir_destin)."/".$folder)) {
                            recordFileError("folder",$folder,$lang_folder_cant_chmod);
                        }
                    } else {
                        recordFileError("folder",$folder,$lang_folder_cant_chmod);
                    }
                }
            }
        }

        // Go through array of files/folders
        $ftp_rawlist = getFtpRawList($dir_source."/".$folder);

        if (is_array($ftp_rawlist)) {

            foreach($ftp_rawlist AS $ff) {

                $isDir=0;
                $isError=0;

                // Split up array into values (Lin)
                if ($_SESSION["win_lin"] == "lin") {

                    $ff = preg_split("/[\s]+/",$ff,9);
                    $perms = $ff[0];
                    $file = $ff[8];

                    if (getFileType($perms) == "d")
                        $isDir=1;
                }

                // Split up array into values (Win)
                if ($_SESSION["win_lin"] == "win") {

                    $ff = preg_split("/[\s]+/",$ff,4);
                    $size = $ff[2];
                    $file = $ff[3];

                    if ($size == "<DIR>")
                        $isDir=1;
                }

                if ($file != "." && $file != "..") {

                    // Check for sub folders and then perform this function
                    if (getFileType($perms) == "d") {
                        copyFolder($file,$dir_destin."/".$folder,$dir_source."/".$folder);
                    } else {

                        $fp1 = $serverTmp."/".$file;
                        $fp2 = $dir_source."/".$folder."/".$file;
                        $fp3 = $dir_destin."/".$folder."/".$file;

                        // Download
                        if (!@ftp_get($conn_id, $fp1, $fp2, FTP_BINARY)) {
                            if (checkFirstCharTilde($fp2) == 1) {
                                if (!@ftp_get($conn_id, $fp1, replaceTilde($fp2), FTP_BINARY)) {
                                    recordFileError("file",$file,$lang_server_error_down);
                                    $isError=1;
                                }
                            } else {
                                recordFileError("file",$file,$lang_server_error_down);
                                $isError=1;
                            }
                        }

                        // Upload
                        if ($isError == 0) {

                            if (!@ftp_put($conn_id, $fp3, $fp1, FTP_BINARY)) {
                                if (checkFirstCharTilde($fp3) == 1) {
                                    if (!@ftp_put($conn_id, replaceTilde($fp3), $fp1, FTP_BINARY)) {
                                        recordFileError("file",$file,$lang_server_error_down);
                                        $isError=1;
                                    }
                                } else {
                                    recordFileError("file",$file,$lang_server_error_down);
                                    $isError=1;
                                }
                            }
                        }

                        if ($isError == 0) {

                            // Chmod files (Lin)
                            if ($_SESSION["win_lin"] == "lin") {

                                $perms = getChmodNumber($perms);
                                $mode = formatChmodNumber($perms);

                                $lang_file_cant_chmod = str_replace("[perms]",$perms,$lang_file_cant_chmod);

                                if (function_exists('ftp_chmod')) {
                                    if (!@ftp_chmod($conn_id, $mode, $fp3)) {
                                        if (checkFirstCharTilde($fp3) == 1) {
                                            if (!@ftp_chmod($conn_id, $mode, replaceTilde($fp3))) {
                                                recordFileError("file",$file,$lang_server_error_down);
                                            }
                                        } else {
                                            recordFileError("file",$file,$lang_server_error_down);
                                        }
                                    }
                                }
                            }
                        }

                        // Delete tmp file
                        unlink($fp1);
                    }
                }
            }
        }
    }
}

###############################################
# RECREATE FOLDER & FILE ARRAYS
###############################################

function recreateFileFolderArrays($type) {

    $arrayNew = array();

    if ($_POST["fileSingle"] != "" || $_POST["folderSingle"] != "") {

        // Single file/folder
        if ($type == "file" && $_POST["fileSingle"] != "") {
            $file = quotesUnescape($_POST["fileSingle"]);
            $arrayNew[] = $file;
        }
        if ($type == "folder" && $_POST["folderSingle"] != "")
            $arrayNew[] = quotesUnescape($_POST["folderSingle"]);

    } else {

        // Array file/folder
        if ($type == "file")
            $array = $_POST["fileAction"];
        if ($type == "folder")
            $array = $_POST["folderAction"];

        if (is_array($array)) {

            foreach($array AS $file) {

                $file = quotesUnescape($file);

                if ($file != "")
                    $arrayNew[] = $file;
            }
        }
    }

    return $arrayNew;
}

###############################################
# RENAME FILES
###############################################

function renameFiles() {

    global $conn_id;
    global $lang_file_exists;
    global $lang_folder_exists;
    global $lang_cant_rename;
    global $lang_title_rename;

    // Check for processing of form
    if ($_POST["processAction"] == 1) {

        $i=0;

        // Go through array of saved names
        foreach ($_SESSION["clipboard_rename"] AS $file) {

            $isError=0;

            $file_name = trim($_POST["file".$i]);
            $file_name = quotesUnescape($file_name);
            $file = quotesUnescape($file);
            $fileExists=0;

            // Check for a different name
            if ($file_name != $file) {

                if ($_SESSION["dir_current"] == "/")
                    $file_to_move = "/".$file;
                if ($_SESSION["dir_current"] == "~")
                    $file_to_move = "~/".$file;
                if ($_SESSION["dir_current"] != "/" && $_SESSION["dir_current"] != "~")
                    $file_to_move = $_SESSION["dir_current"]."/".$file;

                $file_destination = $_SESSION["dir_current"]."/".$file_name;

                // Check if file exists
                if (checkFileExists("f",$file_name,$_SESSION["dir_current"]) == 1) {
                    recordFileError("file",sanitizeStr($file_name),$lang_file_exists);
                    $fileExists=1;
                }

                // Check if folder exists
                if (checkFileExists("d",$file_name,$_SESSION["dir_current"]) == 1) {
                    recordFileError("folder",sanitizeStr($file_name),$lang_folder_exists);
                    $fileExists=1;
                }

                if ($fileExists == 0) {

                    if (!@ftp_rename($conn_id, $file_to_move, $file_destination)) {
                        if (checkFirstCharTilde($file_to_move) == 1) {
                            if (!@ftp_rename($conn_id, replaceTilde($file_to_move), replaceTilde($file_destination))) {
                                recordFileError("file",sanitizeStr($file),$lang_cant_rename);
                                $isError=1;
                            }
                        } else {
                            recordFileError("file",sanitizeStr($file),$lang_cant_rename);
                            $isError=1;
                        }
                    }

                    if ($isError == 0) {

                        // Delete item from history
                        deleteFtpHistory($file_to_move);
                    }
                }
            }

            $i++;
        }

        // Reset var
        $_SESSION["clipboard_rename"] = array();

    } else {

        // Recreate arrays
        $fileArray = recreateFileFolderArrays("file");
        $folderArray = recreateFileFolderArrays("folder");
        $_SESSION["clipboard_rename"] = array();

        $n = sizeof($fileArray) + sizeof($folderArray);
        $height = $n * 35;

        $width = 565;
        $title = $lang_title_rename;

        // Display pop-up
        displayPopupOpen(1,$width,$height,0,$title);

        $i=0;

        // Set vars
        $vars = "&ftpAction=rename&processAction=1";
        $onKeyPress = "onkeypress=\"if (event.keyCode==13){ processForm('".$vars."'); activateActionButtons(0,0); return false; }\"";

        // Display folders
        foreach($folderArray AS $folder) {

            $folder = getFileFromPath($folder);

            echo "<img src=\"images/icon_16_folder.gif\" width=\"16\" height=\"16\"> ";
            echo "<input type=\"text\" name=\"file".$i."\" class=\"inputRename\" value=\"".quotesReplace($folder,"d")."\" ".$onKeyPress."><br>";
            $_SESSION["clipboard_rename"][] = $folder;
            $i++;
        }

        // Display files
        foreach($fileArray AS $file) {

            $file = getFileFromPath($file);

            echo "<img src=\"images/icon_16_file.gif\" width=\"16\" height=\"16\"> ";
            echo "<input type=\"text\" name=\"file".$i."\" class=\"inputRename\" value=\"".quotesReplace($file,"d")."\" ".$onKeyPress."><br>";
            $_SESSION["clipboard_rename"][] = $file;
            $i++;
        }

        displayPopupClose(0,$vars,1);
    }
}

###############################################
# CHMOD FILES
###############################################

function chmodFiles() {

    global $conn_id;
    global $lang_chmod_max_777;
    global $lang_file_cant_chmod;
    global $lang_chmod_owner;
    global $lang_chmod_group;
    global $lang_chmod_public;
    global $lang_chmod_manual;
    global $lang_title_chmod;
    global $lang_chmod_no_support;

    if (!function_exists('ftp_chmod')) {

        $_SESSION["errors"][] = $lang_chmod_no_support;

    } else {

        // Check for a posted form
        if ($_POST["processForm"] == 1) {

            if (trim($_POST["chmodNum"]) > 777) {

                $_SESSION["errors"][] = $lang_chmod_max_777;

            } else {

                $mode = formatChmodNumber($_POST["chmodNum"]);
                $lang_file_cant_chmod = str_replace("[perms]",$mode,$lang_file_cant_chmod);

                foreach($_SESSION["clipboard_chmod"] AS $file) {

                    if (!@ftp_chmod($conn_id, $mode, $file)) {
                        if (checkFirstCharTilde($file) == 1) {
                            if (!@ftp_chmod($conn_id, $mode, replaceTilde($file))) {
                                recordFileError("file",$file,$lang_file_cant_chmod);
                            }
                        } else {
                            recordFileError("file",$file,$lang_file_cant_chmod);
                        }
                    }
                }
            }

            // Reset var
            $_SESSION["clipboard_chmod"] = array();

        } else {

            // Recreate arrays
            $fileArray = recreateFileFolderArrays("file");
            $folderArray = recreateFileFolderArrays("folder");
            $_SESSION["clipboard_chmod"] = array();

            // Count items checked
            $n = sizeof($fileArray) + sizeof($folderArray);

            // Get attributes if 1 item selected
            if ($n == 1) {

                if ($theFile == "")
                    $theFile = $fileArray[0];
                if ($theFile == "")
                    $theFile = $folderArray[0];

                $theFile = getFileFromPath($theFile);

                $ftp_rawlist = getFtpRawList($_SESSION["dir_current"]);

                // Go through array of files/folders
                foreach($ftp_rawlist AS $ff) {

                    // Split up array into values
                    $ff = preg_split("/[\s]+/",$ff,9);

                    $perms = $ff[0];
                    $file = $ff[8];

                    // Check for a match
                    if ($file == $theFile) {
                        $chmod = getChmodNumber($perms);
                        $o_wrx = substr($perms,1,3);
                        $g_wrx = substr($perms,4,3);
                        $p_wrx = substr($perms,7,3);
                    }
                }
            }

            // Save folders
            foreach($folderArray AS $folder) {
                $_SESSION["clipboard_chmod"][] = $folder;
            }

            // Save files
            foreach($fileArray AS $file) {
                $_SESSION["clipboard_chmod"][] = $file;
            }

            $height = 335;
            $width = 420;
            $title = $lang_title_chmod;

            // Display pop-up
            displayPopupOpen(1,$width,$height,0,$title);

            $vars = "&ftpAction=chmod&processForm=1";

            displayChmodFieldset($lang_chmod_owner,"owner",$o_wrx,$vars);
            displayChmodFieldset($lang_chmod_group,"group",$g_wrx,$vars);
            displayChmodFieldset($lang_chmod_public,"public",$p_wrx,$vars);
            displayChmodFieldset($lang_chmod_manual,"manual",$chmod,$vars);

            displayPopupClose(0,$vars,1);
        }
    }
}

###############################################
# FORMAT CHMOD NUMBER
###############################################

function formatChmodNumber($str) {

    $str = trim($str);
    $str = octdec ( str_pad ( $str, 4, '0', STR_PAD_LEFT ) );
    $str = (int) $str;

    return $str;
}

###############################################
# GET CHMOD NUMBER
###############################################

function getChmodNumber($str) {

    $j=0;
    $strlen = strlen($str);
    for ($i=0;$i<$strlen;$i++) {

        if ($i>=1&&$i<=3)
            $m=100;
        if ($i>=4&&$i<=6)
            $m=10;
        if ($i>=7&&$i<=9)
            $m=1;

        $l = substr($str,$i,1);

        if ($l != "d" && $l != "-") {

            if ($l=="r")
                $n=4;
            if ($l=="w")
                $n=2;
            if ($l=="x")
                $n=1;

            $j = $j+($n*$m);
        }
    }

    return $j;
}

###############################################
# CHMOD FIELDSET
###############################################

function displayChmodFieldset($title,$type,$chmod,$vars) {

    global $lang_chmod_read;
    global $lang_chmod_write;
    global $lang_chmod_exe;
    ?>
    <fieldset class="fieldsetChmod">
        <legend><?php echo $title; ?></legend>
        <?php if ($type == "manual") { ?>
            <input type="text" size="4" name="chmodNum" id="chmodNum" value="<?php echo $chmod; ?>" onkeypress="if (event.keyCode==13){ processForm('<?php echo $vars; ?>'); activateActionButtons(0,0); return false;}">
        <?php }else{ ?>
            <?php
            if ($type == "owner")
                $n = 100;
            if ($type == "group")
                $n = 10;
            if ($type == "public")
                $n = 1;

            $n_r = $n * 4;
            $n_w = $n * 2;
            $n_e = $n * 1;
            ?>
            <div class="checkboxChmod"><input type="checkbox" id="<?php echo $type; ?>_r" value="1" <?php if (substr($chmod,0,1) == "r") echo "checked"; ?> onclick="updateChmodNum(this.id,<?php echo $n_r; ?>)"> <?php echo $lang_chmod_read; ?></div>
            <div class="checkboxChmod"><input type="checkbox" id="<?php echo $type; ?>_w" value="1" <?php if (substr($chmod,1,1) == "w") echo "checked"; ?> onclick="updateChmodNum(this.id,<?php echo $n_w; ?>)"> <?php echo $lang_chmod_write; ?></div>
            <div class="checkboxChmod"><input type="checkbox" id="<?php echo $type; ?>_e" value="1" <?php if (substr($chmod,2,1) == "x") echo "checked"; ?> onclick="updateChmodNum(this.id,<?php echo $n_e; ?>)"> <?php echo $lang_chmod_exe; ?></div>
        <?php } ?>
    </fieldset>
<?php
}

###############################################
# EDIT FILE
###############################################

function editFile() {

    global $conn_id;
    global $serverTmp;
    global $lang_server_error_down;

    $isError=0;

    $file = quotesUnescape($_POST["file"]);
    $file_name = getFileFromPath($file);
    $fp1 = $serverTmp."/".$file_name;
    $fp2 = $file;

    // Download the file
    if (!@ftp_get($conn_id, $fp1, $fp2, FTP_BINARY)) {

        if (checkFirstCharTilde($fp2) == 1) {
            if (!@ftp_get($conn_id, $fp1, replaceTilde($fp2), FTP_BINARY)) {
                recordFileError("file",quotesEscape($file,"s"),$lang_server_error_down);
                $isError=1;
            }
        } else {
            recordFileError("file",quotesEscape($file,"s"),$lang_server_error_down);
            $isError=1;
        }
    }

    if ($isError == 0) {

        // Check file has contents
        if (filesize($fp1) > 0) {

            $fd = @fopen($fp1,"r");
            $content = @fread($fd, filesize($fp1));
            @fclose($fd);
        }

        displayEditFileForm($file,$content);
    }

    // Delete tmp file
    unlink($fp1);
}

###############################################
# EDIT FILE PROCESS (EDIT FILE)
###############################################

function displayEditFileForm($file,$content) {

    global $lang_title_edit_file;
    global $lang_btn_save;
    global $lang_btn_close;

    $width = $_POST["windowWidth"] - 250;
    $height = $_POST["windowHeight"] - 220;
    $editorHeight = $height - 85;

    $file_display = $file;
    $file_display = sanitizeStr($file_display);
    $file_display = replaceTilde($file_display);
    $title = $lang_title_edit_file.": ".$file_display;

    // Display pop-up
    displayPopupOpen(0,$width,$height,0,$title);

    echo "<input type=\"hidden\" name=\"file\" value=\"".sanitizeStr($file)."\">";
    echo "<textarea name=\"editContent\" id=\"editContent\" style=\"height: ".$editorHeight."px;\">".sanitizeStr($content)."</textarea>";

    // Save button
    echo "<input type=\"button\" value=\"".$lang_btn_save."\" class=\"popUpBtn\" onClick=\"submitToIframe('&ftpAction=editProcess');\"> ";

    // Close button
    echo "<input type=\"button\" value=\"".$lang_btn_close."\" class=\"popUpBtn\" onClick=\"processForm('&ftpAction=openFolder')\"> ";

    displayPopupClose(0,"",0);
}

###############################################
# EDIT FILE PROCESS (SAVE FILE)
###############################################

// Saving the file to the iframe preserves the cursor position in the edit div.

function editProcess() {

    global $conn_id;
    global $serverTmp;
    global $lang_server_error_up;

    $isError=0;

    // Get file contents
    $file = quotesUnescape($_POST["file"]);
    $file_name = getFileFromPath($file);
    $fp1 = $serverTmp."/".$file_name;
    $fp2 = $file;

    $editContent = $_POST["editContent"];

    // Write content to a file
    $tmpFile = @fopen($fp1,"w+");
    @fputs($tmpFile,$editContent);
    @fclose($tmpFile);

    if (!@ftp_put($conn_id, $fp2, $fp1, FTP_BINARY)) {
        if (checkFirstCharTilde($fp2) == 1) {
            if (!@ftp_put($conn_id, replaceTilde($fp2), $fp1, FTP_BINARY)) {
                recordFileError("file",$file_name,$lang_server_error_up);
            }
        } else {
            recordFileError("file",$file_name,$lang_server_error_up);
        }
    }

    // Delete tmp file
    unlink($fp1);
}

###############################################
# DOWNLOAD FILE
###############################################

function downloadFile() {

    global $conn_id;
    global $serverTmp;
    global $lang_server_error_down;

    $isError=0;

    $file = quotesUnescape($_GET["dl"]);
    $file_name = getFileFromPath($file);
    $fp1 = $serverTmp."/".$file_name;
    $fp2 = $file;

    // Download the file
    if (!@ftp_get($conn_id, $fp1, $fp2, FTP_BINARY)) {
        if (checkFirstCharTilde($fp2) == 1) {
            if (!@ftp_get($conn_id, $fp1, replaceTilde($fp2), FTP_BINARY)) {
                recordFileError("file",quotesEscape($file,"s"),$lang_server_error_down);
                $isError=1;
            }
        } else {
            recordFileError("file",quotesEscape($file,"s"),$lang_server_error_down);
            $isError=1;
        }
    }

    if ($isError == 0) {

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"".quotesEscape($file_name,"d")."\""); // quotes required for spacing in filename
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Description: File Transfer");
        header("Content-Length: ".filesize($fp1));

        flush();

        $fp = @fopen($fp1, "r");
        while (!feof($fp)) {
            echo @fread($fp, 65536);
            @flush();
        }
        @fclose($fp);
    }

    // Delete tmp file
    unlink($fp1);
}

###############################################
# UNESCAPE QUOTES
###############################################

function quotesUnescape($str) {

    $str = str_replace("\'","'",$str);
    $str = str_replace('\"','"',$str);

    return $str;
}

###############################################
# ESCAPE QUOTES
###############################################

function quotesEscape($str,$type) {

    if ($type == "s" || $type == "")
        $str = str_replace("'","\'",$str);
    if ($type == "d" || $type == "")
        $str = str_replace('"','\"',$str);

    return $str;
}

###############################################
# REPLACE QUOTES
###############################################

function quotesReplace($str,$type) {

    $str = quotesUnescape($str);

    if ($type == "s")
        $str = str_replace("'","&acute;",$str);
    if ($type == "d")
        $str = str_replace('"','&quot;',$str);

    return $str;
}

###############################################
# SANITIZE VARS (DEPRECATED)
###############################################
/*
function sanitizeVars() {

	foreach($_POST AS $key => $value) {

		if ($key != "editContent") {
			$value = str_replace("<","&lt;",$value);
			$value = str_replace(">","&gt;",$value);
			$_POST[$key] = $value;
		}
	}

	foreach($_GET AS $key => $value) {

		if ($key != "editContent") {
			$value = str_replace("<","&lt;",$value);
			$value = str_replace(">","&gt;",$value);
			$_POST[$key] = $value;
		}
	}
}
*/

###############################################
# DELETE FILES & FOLDERS
###############################################

function deleteFiles () {

    global $conn_id;
    global $lang_file_doesnt_exist;
    global $lang_cant_delete;

    $folderArray = recreateFileFolderArrays("folder");
    $fileArray = recreateFileFolderArrays("file");

    // folders
    foreach($folderArray AS $folder) {

        $folder = getFileFromPath($folder);

        deleteFolder($folder,$_SESSION["dir_current"]);
    }

    // files
    foreach($fileArray AS $file) {

        $isError=0;
        $file_decoded = urldecode($file);

        if ($file != "") {

            // Check if file exists
            if (checkFileExists("f",$file,$_SESSION["dir_current"]) == 1) {
                recordFileError("file",$file,$lang_file_doesnt_exist);
            } else {

                if (!@ftp_delete($conn_id, $file_decoded)) {
                    if (checkFirstCharTilde($file_decoded) == 1) {
                        if (!@ftp_delete($conn_id, replaceTilde($file_decoded))) {
                            $isError=1;
                        }
                    } else {
                        $isError=1;
                    }
                }

                // If deleting decoded file fails, try original file name
                if ($isError == 1) {

                    if (!@ftp_delete($conn_id, "".$file."")) {
                        if (checkFirstCharTilde($file) == 1) {
                            if (!@ftp_delete($conn_id, "".replaceTilde($file)."")) {
                                recordFileError("file",getFileFromPath($file),$lang_cant_delete);
                            }
                        } else {
                            recordFileError("file",getFileFromPath($file),$lang_cant_delete);
                        }
                    }
                }
            }
        }
    }
}

###############################################
# DELETE FOLDER
###############################################

function deleteFolder($folder,$path) {

    global $conn_id;
    global $lang_cant_delete;
    global $lang_folder_doesnt_exist;
    global $lang_folder_cant_delete;

    $isError=0;

    // List contents of folder
    if ($path != "/" && $path != "~") {

        $folder_path = $path."/".$folder;

    } else {

        if ($_SESSION["win_lin"] == "lin")

            if ($_SESSION["dir_current"] == "/")
                $folder_path = "/".$folder;
        if ($_SESSION["dir_current"] == "~")
            $folder_path = "~/".$folder;

        if ($_SESSION["win_lin"] == "win")
            $folder_path = "/".$folder;
    }

    $ftp_rawlist = getFtpRawList($folder_path);

    // Go through array of files/folders
    if (sizeof($ftp_rawlist) > 0) {

        foreach($ftp_rawlist AS $ff) {

            // Split up array into values (Lin)
            if ($_SESSION["win_lin"] == "lin") {

                $ff = preg_split("/[\s]+/",$ff,9);
                $perms = $ff[0];
                $file = $ff[8];

                if (getFileType($perms) == "d")
                    $isFolder=1;
                else
                    $isFolder=0;
            }

            // Split up array into values (Win)
            if ($_SESSION["win_lin"] == "win") {

                $ff = preg_split("/[\s]+/",$ff,4);
                $size = $ff[2];
                $file = $ff[3];

                if ($size == "<DIR>")
                    $isFolder=1;
                else
                    $isFolder=0;
            }

            if ($file != "." && $file != "..") {

                // Check for sub folders and then perform this function
                if ($isFolder == 1) {
                    deleteFolder($file,$folder_path);
                } else {
                    // otherwise delete file
                    $file_path = $folder_path."/".$file;
                    if (!@ftp_delete($conn_id, "".$file_path.""))  {
                        if (checkFirstCharTilde($file_path) == 1) {
                            if (!@ftp_delete($conn_id, "".replaceTilde($file_path)."")) {
                                recordFileError("file",$file_path,$lang_cant_delete);
                            }
                        } else {
                            recordFileError("file",$file_path,$lang_cant_delete);
                        }
                    }
                }
            }
        }
    }

    // Check if file exists
    if (checkFileExists("d",$folder,$folder_path) == 1) {

        $_SESSION["errors"][] = str_replace("[file]","<strong>".tidyFolderPath($folder_path,$folder)."</strong>",$lang_folder_doesnt_exist);

    } else {

        // Chage dir up before deleting
        ftp_cdup($conn_id);

        // Delete the empty folder
        if (!@ftp_rmdir($conn_id, "".$folder_path."")) {
            if (checkFirstCharTilde($folder_path) == 1) {
                if (!@ftp_rmdir($conn_id, "".replaceTilde($folder_path)."")) {
                    recordFileError("folder",$folder_path,$lang_folder_cant_delete);
                    $isError=1;
                }
            } else {
                recordFileError("folder",$folder_path,$lang_folder_cant_delete);
                $isError=1;
            }
        }

        // Remove directory from history
        if ($isError == 0)
            deleteFtpHistory($folder_path);
    }
}

###############################################
# NEW FILE
###############################################

function newFile() {

    global $conn_id;
    global $serverTmp;
    global $lang_title_new_file;
    global $lang_new_file_name;
    global $lang_template;
    global $lang_no_template;
    global $lang_file_exists;
    global $lang_file_cant_make;

    $isError=0;

    // Set vars
    $vars = "&ftpAction=newFile";

    // Display templates
    $templates_dir = "templates";

    $file_name = trim(quotesUnescape($_POST["newFile"]));

    if ($file_name == "") {

        $title = $lang_title_new_file;
        $width = 400;
        $height = 95;

        // Display pop-up
        displayPopupOpen(0,$width,$height,0,$title);

        echo "<input type=\"text\" name=\"newFile\" id=\"newFile\" placeholder=\"".$lang_new_file_name."\" onkeypress=\"if (event.keyCode==13){ processForm('".$vars."'); return false;}\">";

        if (is_dir($templates_dir)) {

            if ($dh = opendir($templates_dir)) {

                $i=0;
                while (($file = readdir($dh)) !== false) {

                    if ($file != "" && $file != "." && $file != ".." && $file != "index.html") {

                        $file_name = $file;

                        $template_found=1;

                        $langs .= "<option value=\"".$file_name."\">".$file_name."</option>";
                    }
                }
                closedir($dh);
            }
        }

        echo "<p>".$lang_template.": ";
        echo "<select name=\"template\">";
        echo "<option value=\"\">".$lang_no_template."</option>";
        echo $langs;
        echo "</select>";

        displayPopupClose(0,$vars,1);

    } else {

        $fp1 = $serverTmp."/".$file_name;

        if ($_SESSION["dir_current"] == "/")
            $fp2 = "/".$file_name;
        else
            $fp2 = $_SESSION["dir_current"]."/".$file_name;

        // Check if file already exists
        if (checkFileExists("f",$file_name,$_SESSION["dir_current"]) == 1) {
            recordFileError("file",$file_name,$lang_file_exists);
        } else {

            // Get template
            if ($_POST["template"] != $lang_no_template) {

                $file_name = $templates_dir."/".$_POST["template"];
                $fd = @fopen($file_name,"r");
                $content = @fread($fd,filesize($file_name));
                @fclose($fd);
            }

            // Write file to server
            $tmpFile = @fopen($fp1,"w+");
            @fputs($tmpFile,$content);
            @fclose($tmpFile);

            // Upload the file
            if (!@ftp_put($conn_id, $fp2, $fp1, FTP_BINARY)) {
                if (checkFirstCharTilde($fp2) == 1) {
                    if (!@ftp_put($conn_id, replaceTilde($fp2), $fp1, FTP_BINARY)) {
                        recordFileError("file",$file_name,$lang_file_cant_make);
                        $isError=1;
                    }
                } else {
                    recordFileError("file",$file_name,$lang_file_cant_make);
                    $isError=1;
                }
            }

            if ($isError == 0) {

                // Open editor
                $file = $fp2;
                displayEditFileForm($file,$content);
            }
        }

        // Delete tmp file
        unlink($fp1);
    }
}

###############################################
# CHECK IF FILE EXISTS
###############################################

function checkFileExists($type,$file_name,$folder_path) {

    $ftp_rawlist = getFtpRawList($folder_path);

    if (is_array($ftp_rawlist)) {

        $fileNameAr = array();

        // Go through array of files/folders
        foreach($ftp_rawlist AS $ff) {

            // Lin
            if ($_SESSION["win_lin"] == "lin") {

                // Split up array into values
                $ff = preg_split("/[\s]+/",$ff,9);

                $perms = $ff[0];
                $file = $ff[8];

                if ($file != "." && $file != "..") {

                    if ($type == "f" && getFileType($perms) == "f")
                        $fileNameAr[] = $file;

                    if ($type == "d" && getFileType($perms) == "d")
                        $fileNameAr[] = $file;
                }
            }

            // Win
            if ($_SESSION["win_lin"] == "win") {

                // Split up array into values
                $ff = preg_split("/[\s]+/",$ff,4);

                $size = $ff[2];
                $file = $ff[3];

                if ($size == "<DIR>")
                    $size = "d";

                if ($type == "d" && $size == "d")
                    $fileNameAr[] = $file;

                if ($type == "f" && $size != "d")
                    $fileNameAr[] = $file;
            }
        }

        // Check if file is in array
        if (in_array($file_name,$fileNameAr))
            return 1;

    } else {
        return 0;
    }
}

###############################################
# NEW FOLDER
###############################################

function newFolder() {

    global $conn_id;
    global $lang_title_new_folder;
    global $lang_new_folder_name;
    global $lang_folder_exists;
    global $lang_folder_cant_make;

    // Set vars
    $vars = "&ftpAction=newFolder";

    $folder = trim(quotesUnescape($_POST["newFolder"]));

    if ($folder == "") {

        $title = $lang_title_new_folder;
        $width = 400;
        $height = 40;

        // Display pop-up
        displayPopupOpen(0,$width,$height,0,$title);

        echo "<input type=\"text\" name=\"newFolder\" id=\"newFolder\" placeholder=\"".$lang_new_folder_name."\" onkeypress=\"if (event.keyCode==13){ processForm('".$vars."'); return false;}\">";

        displayPopupClose(0,$vars,1);

    } else {

        // Check if folder exists
        if (checkFileExists("d",$folder,$_SESSION["dir_current"]) == 1 || $folder == "..") {
            recordFileError("folder",$folder,$lang_folder_exists);
        } else {

            if (!@ftp_mkdir($conn_id, $folder))
                recordFileError("folder",$folder,$lang_folder_cant_make);
        }
    }
}

###############################################
# UPLOAD FILE
###############################################

function uploadFile() {

    global $conn_id;
    global $serverTmp;
    global $lang_server_error_up;
    global $lang_browser_error_up;

    $file_name = $_SERVER['HTTP_X_FILENAME'];
    $path = $_GET["filePath"];

    if ($file_name) {

        $fp1 = $serverTmp."/".$file_name;

        // Check if a folder is being uploaded
        if ($path != "") {

            // Check to see folder path exists (and create)
            createFolderHeirarchy($path);
            $fp2 = $_SESSION["dir_current"]."/".$path.$file_name;

        } else {

            if ($_SESSION["dir_current"] == "/")
                $fp2 = "/".$file_name;
            else
                $fp2 = $_SESSION["dir_current"]."/".$file_name;
        }

        // Check if file reached server
        if (file_put_contents($fp1,file_get_contents('php://input'))) {

            if (!@ftp_put($conn_id, $fp2, $fp1, FTP_BINARY)) {
                if (checkFirstCharTilde($fp2) == 1) {
                    if (!@ftp_put($conn_id, replaceTilde($fp2), $fp1, FTP_BINARY)) {
                        recordFileError("file",$file_name,$lang_server_error_up);
                    }
                } else {
                    recordFileError("file",$file_name,$lang_server_error_up);
                }
            }
        } else {
            recordFileError("file",$file_name,$lang_browser_error_up);
        }

        // Delete tmp file
        unlink($fp1);
    }
}

###############################################
# CREATE FOLDER HEIRARCHY
###############################################

function createFolderHeirarchy($path) {

    global $conn_id;
    global $lang_folder_cant_make;

    $folderAr = explode("/",$path);

    $n = sizeof($folderAr);
    for ($i=0;$i<$n;$i++) {

        if ($folder == "")
            $folder = $folderAr[$i];
        else
            $folder = $folder."/".$folderAr[$i];

        if (!@ftp_mkdir($conn_id, $folder)) {
            if (checkFirstCharTilde($folder) == 1)
                @ftp_mkdir($conn_id, replaceTilde($folder));
        }
    }
}

###############################################
# UPLOAD FILE (IFRAME)
###############################################

function iframeUpload() {

    global $conn_id;
    global $lang_server_error_up;
    global $lang_browser_error_up;

    $fp1 = $_FILES["uploadFile"]["tmp_name"];
    $fp2 = $_SESSION["dir_current"]."/".$_FILES["uploadFile"]["name"];

    if ($fp1 != "") {

        if (!@ftp_put($conn_id, $fp2, $fp1, FTP_BINARY)) {
            if (checkFirstCharTilde($fp2) == 1) {
                if (!@ftp_put($conn_id, replaceTilde($fp2), $fp1, FTP_BINARY)) {
                    recordFileError("file",$file_name,$lang_server_error_up);
                }
            } else {
                recordFileError("file",$file_name,$lang_server_error_up);
            }
        }

    } else {
        recordFileError("file",$file_name,$lang_browser_error_up);
    }
}

###############################################
# DELETE FTP HISTORY
###############################################

function deleteFtpHistory($dirDelete) {

    $dirDelete = str_replace("/","\/",$dirDelete);

    // Check each item in the history
    if (is_array($_SESSION["dir_history"])) {
        foreach($_SESSION["dir_history"] AS $dir) {

            if (!@preg_match("/^".$dirDelete."/", $dir))
                $dir_history[] = $dir;
        }

        // Set new array
        $_SESSION["dir_history"] = $dir_history;

        // Sort array
        if (is_array($_SESSION["dir_history"]))
            asort($_SESSION["dir_history"]);
    }
}

###############################################
# SINGLE QUOTE ESCAPE
###############################################

function singleQuoteEscape($str) {
    return str_replace("'","\'",$str);
}

###############################################
# GET FILE TYPE
###############################################

function getFileType($perms) {

    if (substr($perms,0,1) == "d")
        return "d"; // directory
    if (substr($perms,0,1) == "l")
        return "l"; // link
    if (substr($perms,0,1) == "-")
        return "f"; // file
}

###############################################
# DISPLAY AJAX DIV OPEN
###############################################

function displayAjaxDivOpen() {
    ?>
<div id="ajaxContentWindow" onContextMenu="displayContextMenu(event,'','',<?php echo assignWinLinNum(); ?>)" onClick="unselectFiles()">
<?php
}

###############################################
# DISPLAY AJAX DIV CLOSE
###############################################

function displayAjaxDivClose() {
    ?>
</div>
<?php
}

###############################################
# DISPLAY ERRORS
###############################################

function displayErrors() {

    global $lang_title_errors;

    $sizeAr = sizeof($_SESSION["errors"]);

    if ($sizeAr > 0) {

        $width = (getMaxStrLen($_SESSION["errors"]) * 10) + 30;
        $height = sizeof($_SESSION["errors"]) * 25;

        $title = $lang_title_errors;

        // Display pop-up
        displayPopupOpen(1,$width,$height,1,$title);

        $errors = array_reverse($_SESSION["errors"]);

        foreach($errors AS $error) {
            echo $error."<br>";
        }

        $vars = "&ftpAction=openFolder&resetErrorArray=1";

        displayPopupClose(1,$vars,0);
    }
}

//##############################################
// DISPLAY POP-UP FRAME OPEN
//##############################################

function displayPopupOpen($resize,$width,$height,$isError,$title) {

    // Set default sizes of exceeded
    if ($resize == 1) {

        if ($width < 400)
            $width = 400;

        if ($height > 400)
            $height = 400;
    }

    $windowWidth = $_POST["windowWidth"];
    $windowHeight = $_POST["windowHeight"];

    // Center window
    if ($windowWidth > 0)
        $left = round(($windowWidth - $width) / 2 - 15); // -15 for H padding
    else
        $left = 250;

    if ($windowHeight > 0)
        $top = round(($_POST["windowHeight"] - $height) / 2 - 50);
    else
        $top = 250;

    echo "<div id=\"blackOutDiv\">";
    echo "<div id=\"popupFrame\" style=\"left: ".$left."px; top: ".$top."px; width: ".$width."px;\">";

    if ($isError == 1)
        $divId = "popupHeaderError";
    else
        $divId = "popupHeaderAction";

    echo "<div id=\"".$divId."\">";
    echo $title;
    echo "</div>";

    if ($isError == 1)
        $divId = "popupBodyError";
    else
        $divId = "popupBodyAction";

    echo "<div id=\"".$divId."\" style=\"height: ".$height."px;\">";
}

//##############################################
// DISPLAY POP-UP FRAME CLOSE
//##############################################

function displayPopupClose($isError,$vars,$btnCancel) {

    global $lang_btn_ok;
    global $lang_btn_cancel;

    echo "</div>";

    if ($isError == 1)
        $divId = "popupFooterError";
    else
        $divId = "popupFooterAction";

    echo "<div id=\"".$divId."\">";

    // OK button
    if ($vars != "")
        echo "<input type=\"button\" class=\"popUpBtn\" value=\"".$lang_btn_ok."\" onClick=\"processForm('".$vars."'); activateActionButtons(0,0);\"> ";

    // Cancel button
    if ($btnCancel == 1)
        echo "<input type=\"button\" class=\"popUpBtn\" value=\"".$lang_btn_cancel."\" onClick=\"processForm('&ftpAction=openFolder');\"> ";

    echo "</div>";

    echo "</div>";
    echo "</div>";
}

//##############################################
// GET MAX STR LENGTH FROM ARRAY
//##############################################

function getMaxStrLen($array) {

    foreach ($array AS $str) {

        $thisLen = strlen($str);

        if ($thisLen > $maxLen)
            $maxLen = $thisLen;
    }

    return $maxLen;
}

//##############################################
// GET FILE/FOLDER NAME
//##############################################

function getFileFromPath($str) {

    $str = preg_replace("/^(.)+\//","",$str);
    $str = preg_replace("/^~/","",$str);

    return $str;
}

//##############################################
// PARENT OPEN FOLDER
//##############################################

function parentOpenFolder() {
    ?>
    <html>
    <body>
    <script type="text/javascript">
        parent.processForm('&ftpAction=openFolder');
    </script>
    </body>
    </html>
<?php
}

//##############################################
// LOAD EDITABLE EXTENSIONS
//##############################################

function loadEditableExts() {

    global $editableExts;

if ($editableExts != "") {
    ?>
    <script type="text/javascript">
        <?php
                echo "var editableExts = new Array();"."\n";
                $extAr = explode(",",$editableExts);
                $n = sizeof($extAr);
                for ($i=0;$i<$n;$i++) {
                    echo "editableExts[".$i."] = '".$extAr[$i]."';\n";
                }
            }
         ?>
    </script>
<?php
}

//##############################################
// REPLACE TILDE FROM PATH
//##############################################

function replaceTilde($str) {

    $str = str_replace("~","/",$str);
    $str = str_replace("//","/",$str);

    return $str;
}

//##############################################
// ASSIGN NUMBER TO WIN/LIN VAR
//##############################################

function assignWinLinNum() {

    if ($_SESSION["win_lin"] == "lin")
        return 1;
    if ($_SESSION["win_lin"] == "win")
        return 0;
}

//##############################################
// GET PARENT DIRECTORY
//##############################################

function getParentDir() {

    if ($_SESSION["dir_current"] == "/") {

        $parent = "/";

    } else {

        $path_parts = pathinfo($_SESSION["dir_current"]);
        $parent = $path_parts['dirname'];
    }

    return $parent;
}

//##############################################
// DISPLAY SKIN SELECT
//##############################################

function displaySkinSelect($skin) {

    global $lang_skin;
    global $lang_skins_empty;
    global $lang_skins_locked;
    global $lang_skins_missing;

    $dir = "skins";
    $skin_found=0;

    if ($skin == "")
        $skin = "monsta.css";

    if (is_dir($dir)) {

        if ($dh = opendir($dir)) {

            $i=0;
            while (($file = readdir($dh)) !== false) {

                if ($file != "" && $file != "." && $file != ".." && $file != "index.html") {

                    $i++;

                    $file_name = $file;

                    $skin_found=1;

                    // Strip extension
                    $file_name = preg_replace("/\..*$/","",$file_name);

                    $skins = "<option value=\"".$file_name."\"";

                    if ($file_name == $skin)
                        $skins .= " selected";

                    $skins .= ">";

                    $skins .= preg_replace("/\..*$/","",$file_name);

                    $skins .= "</option>";

                    $skinsAr[] = $skins;
                }
            }
            closedir($dh);

            if ($skin_found == 0) {

                echo "<p>".$lang_skin.": ";
                echo str_replace("[skins]","<strong>skins</strong>", $lang_skins_empty);

            } else {

                if ($i > 1) {

                    sort($skinsAr);

                    echo "<p>".$lang_skin.": ";
                    echo "<select name=\"skin\" tabindex=\"-1\">";

                    foreach ($skinsAr AS $skin) {
                        echo $skin;
                    }

                    echo "</select>";

                } else {
                    echo "<input type=\"hidden\" name=\"skin\" value=\"".$file_name."\">";
                }
            }

        } else {

            echo "<p>".$lang_skin.": ";
            echo str_replace("[skins]","<strong>skins</strong>", $lang_skins_locked);
        }

    } else {
        echo "<p>".$lang_skin.": ";
        echo str_replace("[skins]","<strong>skins</strong>", $lang_skins_missing);
    }
}

//##############################################
// DISPLAY LANGUAGE SELECT
//##############################################

function displayLangSelect($lang) {

    global $lang_language;

    $dir = "languages";
    $lang_found=0;

    if (is_dir($dir)) {

        if ($dh = opendir($dir)) {

            $i=0;
            while (($file = readdir($dh)) !== false) {

                if ($file != "" && $file != "." && $file != ".." && $file != "index.html") {

                    $i++;

                    $file_name = $file;

                    // Open file to get language name
                    include($dir."/".$file_name);

                    $lang_found=1;

                    // Strip extension
                    $file_name = preg_replace("/\..*$/","",$file_name);

                    $langs = "<option value=\"".$file_name."\"";

                    if ($file_name == $lang)
                        $langs .= " selected";

                    $langs .= ">";

                    $langs .= $file_lang_name;

                    $langs .= "</option>";

                    $langsAr[] = $langs;

                    // Restore session language file
                    include($dir."/".$lang.".php");
                }
            }
            closedir($dh);

            if ($lang_found == 0) {

                echo "Language: <strong>languages</strong> folder empty!";

            } else {

                if ($i > 1) {

                    sort($langsAr);

                    echo $lang_language.": ";
                    echo "<select name=\"lang\" tabindex=\"-1\">";

                    foreach($langsAr AS $lang) {
                        echo $lang;
                    }

                    echo "</select>";

                } else {
                    echo "<input type=\"hidden\" name=\"lang\" value=\"".$file_name."\">";
                }
            }

        } else {

            echo "Language: <strong>languages</strong> folder locked!";
        }

    } else {
        echo "Language: <strong>languages</strong> folder missing!";
    }
}

//##############################################
// TIDY FOLDER PATH
//##############################################

function tidyFolderPath($str1,$str2) {

    $str1 = replaceTilde($str1);

    if ($str1 == "/")
        return "/".$str2;
    else
        return $str1."/".$str2;
}

//##############################################
// LOAD JAVASCRIPT LANGUAGE VARS
//##############################################

function loadJsLangVars() {

    // Include language file again to save listing globals
    $langFileArray = getFileArray("languages");

    if (in_array($_SESSION["lang"],$langFileArray))
        include("languages/".$_SESSION["lang"].".php");
    else
        include("languages/uk.php");
    ?>
    <script type="text/javascript">
        var lang_no_xmlhttp = '<?php echo quotesEscape($lang_no_xmlhttp,"s"); ?>';
        var lang_support_drop = '<?php echo quotesEscape($lang_support_drop,"s"); ?>';
        var lang_no_support_drop = '<?php echo quotesEscape($lang_no_support_drop,"s"); ?>';
        var lang_transfer_pending = '<?php echo quotesEscape($lang_transfer_pending,"s"); ?>';
        var lang_transferring_to_ftp = '<?php echo quotesEscape($lang_transferring_to_ftp,"s"); ?>';
        var lang_no_file_selected = '<?php echo quotesEscape($lang_no_file_selected,"s"); ?>';
        var lang_none_selected = '<?php echo quotesEscape($lang_none_selected,"s"); ?>';
        var lang_context_open = '<?php echo quotesEscape($lang_context_open,"s"); ?>';
        var lang_context_download = '<?php echo quotesEscape($lang_context_download,"s"); ?>';
        var lang_context_edit = '<?php echo quotesEscape($lang_context_edit,"s"); ?>';
        var lang_context_cut = '<?php echo quotesEscape($lang_context_cut,"s"); ?>';
        var lang_context_copy = '<?php echo quotesEscape($lang_context_copy,"s"); ?>';
        var lang_context_paste = '<?php echo quotesEscape($lang_context_paste,"s"); ?>';
        var lang_context_rename = '<?php echo quotesEscape($lang_context_rename,"s"); ?>';
        var lang_context_delete = '<?php echo quotesEscape($lang_context_delete,"s"); ?>';
        var lang_context_chmod = '<?php echo quotesEscape($lang_context_chmod,"s"); ?>';
        var lang_size_b = '<?php echo quotesEscape($lang_size_b,"s"); ?>';
        var lang_size_kb = '<?php echo quotesEscape($lang_size_kb,"s"); ?>';
        var lang_size_mb = '<?php echo quotesEscape($lang_size_mb,"s"); ?>';
        var lang_size_gb = '<?php echo quotesEscape($lang_size_gb,"s"); ?>';
        var lang_btn_upload_file = '<?php echo quotesEscape($lang_btn_upload_file,"s"); ?>';
        var lang_btn_upload_files = '<?php echo quotesEscape($lang_btn_upload_files,"s"); ?>';
        var lang_btn_upload_repeat = '<?php echo quotesEscape($lang_btn_upload_repeat,"s"); ?>';
        var lang_btn_upload_folder = '<?php echo quotesEscape($lang_btn_upload_folder,"s"); ?>';
        var lang_file_size_error = '<?php echo quotesEscape($lang_file_size_error,"s"); ?>';

        var upload_limit = '<?php echo $_SESSION["upload_limit"]; ?>';
    </script>
<?php
}

//##############################################
// LOAD JAVASCRIPT LANGUAGE VARS
//##############################################

function setLangFile() {

    // The order of these determines the proper display
    if ($_COOKIE["lang"] != "")
        $lang = $_COOKIE["lang"];
    if ($_SESSION["lang"] != "")
        $lang = $_SESSION["lang"];
    if (isset($_POST["lang"]))
        $lang = $_POST["lang"];

    if ($lang == "") {

        $dir = "languages";

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != "..") {

                        include("languages/".$file);

                        if ($file_lang_default == 1)
                            $lang = str_replace(".php","",$file);
                    }
                }
                closedir($dh);
            }
        }
    }

    $_SESSION["lang"] = $lang;
}

//##############################################
// SESSION ENDED
//##############################################

function sessionExpired($message) {

    global $lang_title_ended;
    global $lang_btn_login;

    $title = $lang_title_ended;

    // Display pop-up
    displayPopupOpen(1,200,90,1,$title);

    echo $message;

    echo "<p><input type=\"button\" id=\"btnLogin\" value=\"".$lang_btn_login."\" onClick=\"document.location.href='?openFolder=".$_POST["openFolder"]."'\">";

    displayPopupClose(1,"",0);
}

//##############################################
// SET UPLOAD LIMIT
//##############################################

function setUploadLimit() {

    global $lang_size_kb;
    global $lang_size_mb;
    global $lang_size_gb;
    global $lang_size_tb;

    if ($_SESSION["upload_limit"] == "") {

        // Get the server's memory limit
        //if (preg_match('/msie [1-8]/i',$_SERVER['HTTP_USER_AGENT']))
        //	$upload_limit = ini_get('upload_max_filesize');
        //else
        $upload_limit = ini_get('memory_limit');

        $ll = substr($upload_limit,strlen($upload_limit)-1,1);

        if ($ll == "B") {
            $upload_limit = str_replace("B","",$upload_limit);
            $upload_limit = $upload_limit * 1;
        }
        if ($ll == "K") {
            $upload_limit = str_replace("K","",$upload_limit);
            $upload_limit = $upload_limit * 1024;
        }
        if ($ll == "M") {
            $upload_limit = str_replace("M","",$upload_limit);
            $upload_limit = $upload_limit * 1024 * 1024;
        }
        if ($ll == "G") {
            $upload_limit = str_replace("G","",$upload_limit);
            $upload_limit = $upload_limit * 1024 * 1024 * 1024;
        }
        if ($ll == "T") {
            $upload_limit = str_replace("T","",$upload_limit);
            $upload_limit = $upload_limit * 1024 * 1024 * 1024 * 1024;
        }

        $_SESSION["upload_limit"] = $upload_limit;
    }
}

//##############################################
// ADJUST BUTTON WIDTH (FOR LANGUAGES)
//##############################################

function adjustButtonWidth($str) {

    if (strlen(utf8_decode($str)) > 12)
        return "inputButtonNf";
    else
        return "inputButton";
}

//##############################################
// CHECK REFERER MATCHES DOMAIN
//##############################################

function checkReferer() {

    global $lang_session_expired;

    $domain = $_SESSION["domain"];
    $domain = str_replace(".","\.",$domain);

    if (preg_match("/".$domain."/",$_SERVER["HTTP_REFERER"])) {
        return 1;
    } else {
        sessionExpired($lang_session_expired);
        logOut();
        return 0;
    }
}

//##############################################
// CHECK FIRST CHAR IS TILDE
//##############################################

function checkFirstCharTilde($str) {

    if (substr($str,0,1) == "~")
        return 1;
    else
        return 0;
}

//##############################################
// RECORD FILE/FOLDER ERROR
//##############################################

function recordFileError($str,$file_name,$error) {

    $_SESSION["errors"][] = str_replace("[".$str."]","<strong>".$file_name."</strong>",$error);
}

//##############################################
// RECORD FILE/FOLDER ERROR
//##############################################

function getFileArray($dir) {

    $langFileArray = array();

    if (is_dir($dir)) {

        if ($dh = opendir($dir)) {

            $i=0;
            while (($file = readdir($dh)) !== false) {

                if ($file != "" && $file != "." && $file != ".." && $file != "index.html") {
                    $file = str_replace(".php","",$file);
                    $langFileArray[] = $file;
                }
            }
            closedir($dh);
        }
    }

    return $langFileArray;
}

###############################################
# SANITIZE VARS
###############################################

function sanitizeStr($str) {

    $str = trim($str);
    $str = str_replace("&","&amp;",$str);
    $str = str_replace('"','&quot;',$str);
    $str = str_replace("<","&lt;",$str);
    $str = str_replace(">","&gt;",$str);

    return $str;
}

?>