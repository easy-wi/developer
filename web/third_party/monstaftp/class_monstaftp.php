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

//##############################################
// SET UPLOAD LIMIT
//##############################################

class Monsta {

    private $upload_limit, $ftpConnection, $dateFormatUsa, $lang_size_kb, $lang_size_mb, $lang_size_gb, $ftpIP, $ftpPort, $ftpUser, $ftpPass;
    private $win_lin, $serverID;
    private $actionTarget = 'userpanel.php?w=gs&amp;d=wf&amp;id=', $platformTestCount = 0, $trCount = 0;
    public $loggedIn = false, $errorResponse = false;

    public function __construct ($serverID, $ftpIP, $ftpPort, $ftpUser, $ftpPass, $language, $startDir = '') {

        $this->ftpIP = $ftpIP;
        $this->ftpPort = $ftpPort;
        $this->ftpUser = $ftpUser;
        $this->ftpPass = $ftpPass;
        $this->serverID = $serverID;

        $this->setDateFormatUsa($language);

        $this->setUploadLimit();

        $this->defineActionTarget();

        $this->errorResponse = $this->connectFTP();

        $this->getPlatform();

        $this->setInitialDir($startDir);
    }

    public function __destruct () {
        $upload_limit = null;
    }

    private function setDateFormatUsa ($language) {
        $this->dateFormatUsa = ($language == 'de') ? 0 : 1;
    }

    private function setUploadLimit() {

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

        $this->upload_limit = $upload_limit;
    }

    private function defineActionTarget () {

        global $ui;

        $this->actionTarget .= $ui->id('id', 10, 'get');

        foreach (array_keys($ui->get) as $k) {
            if (!in_array($k, array('w', 'd', 'id')) and $ui->w($k, 255, 'get')) {
                $this->actionTarget .= '&amp;' .$k . '=' . $ui->w($k, 255, 'get');
            }
        }
    }

###############################################
# CONNECT TO FTP
###############################################
    private function connectFTP() {

        $this->ftpConnection = @ftp_connect($this->ftpIP, $this->ftpPort, 3);

        if ($this->ftpConnection) {

            if (@ftp_login ($this->ftpConnection, $this->ftpUser, $this->ftpPass)) {

                @ftp_pasv($this->ftpConnection, true);

                $this->loggedIn = true;

                return true;

            } else {

                global $lang_cant_authenticate;
                return $lang_cant_authenticate;
            }
        }

        global $lang_cant_connect;
        return $lang_cant_connect;
    }

    private function setInitialDir ($ftpDir) {

        // Change dir if one set
        if (!isset($_SESSION["monstaftp"][$this->serverID]["dir_current"])) {
            if ($ftpDir != "") {
                if (@ftp_chdir($this->ftpConnection, $ftpDir)) {
                    $_SESSION["monstaftp"][$this->serverID]["dir_current"] = $ftpDir;
                } else if (@ftp_chdir($this->ftpConnection, "~".$ftpDir)) {
                    $_SESSION["monstaftp"][$this->serverID]["dir_current"] = "~".$ftpDir;
                }
            } else {
                $_SESSION["monstaftp"][$this->serverID]["dir_current"] = "";
            }
        }

        if (!isset($_SESSION["monstaftp"][$this->serverID]["dir_history"])) {
            $_SESSION["monstaftp"][$this->serverID]["dir_history"] = array();
        }

        if (!isset($_SESSION["monstaftp"][$this->serverID]["errors"])) {
            $_SESSION["monstaftp"][$this->serverID]["errors"] = array();
        }
    }

    private function getPlatform() {

        if ($this->loggedIn === true and $this->win_lin == "") {
            $ftp_rawlist = ftp_rawlist($this->ftpConnection, ".");

            // Check for content in array
            if (sizeof($ftp_rawlist) == 0) {

                $this->platformTestCount++;

                // Create a test folder
                if (@ftp_mkdir($this->ftpConnection, "test")) {

                    if ($this->platformTestCount < 2) {
                        $this->getPlatform();
                        @ftp_rmdir($this->ftpConnection, "test");
                    }
                }

            } else {

                $win_lin = '';

                // Get first item in array
                $ff = $ftp_rawlist[0];

                // Split up array into values
                $ff = preg_split("/[\s]+/",$ff,9);

                // First item in Linux rawlist is permissions. In Windows it's date.
                // If length of first item in array line is 8 chars, without a-z, it's a date.
                if (strlen($ff[0]) == 8 && !preg_match("/[a-z]/i", $ff[0], $matches)) {
                    $win_lin = "win";
                }

                if (strlen($ff[0]) == 10 && !preg_match("/[0-9]/i", $ff[0], $matches)) {
                    $win_lin = "lin";
                }

                $this->win_lin = $win_lin;
            }
        }
    }

    public function displayFormStart () {
        return '<form method="post" action="' . $this->actionTarget . '" enctype="multipart/form-data" name="ftpActionForm" id="ftpActionForm">';
    }

    public function displayFtpActions () {

        global $lang_btn_refresh, $lang_btn_cut, $lang_btn_copy, $lang_btn_paste, $lang_btn_rename, $lang_btn_delete;

        $return = '<div id="ftpActionButtonsDiv" class="alert alert-info">
            <input type="button" value="' . $lang_btn_refresh . '" onClick="refreshListing()" class="btn btn-primary">
            <input type="button" id="actionButtonCut" value="' . $lang_btn_cut . '" onClick="actionFunctionCut(\'\',\'\');" disabled class="btn btn-primary">
            <input type="button" id="actionButtonCopy" value="' . $lang_btn_copy . '" onClick="actionFunctionCopy(\'\',\'\');" disabled class="btn btn-primary">
            <input type="button" id="actionButtonPaste" value="' . $lang_btn_paste . '" onClick="actionFunctionPaste(\'\');" disabled class="btn btn-primary">
            <input type="button" id="actionButtonRename" value="' . $lang_btn_rename . '" onClick="actionFunctionRename(\'\',\'\');" disabled class="btn btn-primary">
            <input type="button" id="actionButtonDelete" value="' . $lang_btn_delete . '" onClick="actionFunctionDelete(\'\',\'\');" disabled class="btn btn-danger">
            ';

        $return .= '</div>';

        return $return;
    }

    private function assignWinLinNum() {

        if ($this->win_lin == "lin") {
            return 1;
        }

        if ($this->win_lin == "win") {
            return 0;
        }

        return false;

    }

    public function displayAjaxDivOpen () {
        return '<div id="ajaxContentWindow" onContextMenu="displayContextMenu(event,\'\',\'\',' . $this->assignWinLinNum() . ')" onClick="unselectFiles()">';
    }

    private function sanitizeStr($str) {

        $str = trim($str);
        $str = str_replace("&","&amp;",$str);
        $str = str_replace('"','&quot;',$str);
        $str = str_replace("<","&lt;",$str);
        $str = str_replace(">","&gt;",$str);

        return $str;
    }


//##############################################
// GET MAX STR LENGTH FROM ARRAY
//##############################################

    private function getMaxStrLen($array) {

        $maxLen = 0;

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

    private function getFileFromPath($str) {

        $str = preg_replace("/^(.)+\//","",$str);
        $str = preg_replace("/^~/","",$str);

        return $str;
    }

//##############################################
// PARENT OPEN FOLDER
//##############################################

    public function parentOpenFolder() {
        return "<html><body><script type=\"text/javascript\">parent.processForm('&ftpAction=openFolder');</script></body></html>";
    }

    private function replaceTilde($str) {

        $str = str_replace("~","/",$str);
        $str = str_replace("//","/",$str);

        return $str;
    }

    public function displayFtpHistory() {

        $return = '<select onChange="openThisFolder(this.options[this.selectedIndex].value,1)" id="ftpHistorySelect">';

        if (isset($_SESSION["monstaftp"][$this->serverID]["dir_history"]) and is_array($_SESSION["monstaftp"][$this->serverID]["dir_history"])) {

            foreach ($_SESSION["monstaftp"][$this->serverID]["dir_history"] as $dir) {

                $dir_display = $this->sanitizeStr($dir);
                $dir_display = $this->replaceTilde($dir_display);

                $return .= "<option value=\"".rawurlencode($dir)."\"";

                // Check if this is current directory
                if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] == $dir) {
                    $return .= " selected";
                }

                $return .= ">" . $dir_display . "</option>";
            }
        }

        $return .= '</select>';

        return $return;
    }

    private function getFtpRawList($folder_path) {

        global $lang_folder_cant_access;


        if ($this->loggedIn === true) {

            $isError=0;

            if (!@ftp_chdir($this->ftpConnection, $folder_path)) {
                if ($this->checkFirstCharTilde($folder_path) == 1) {
                    if (!@ftp_chdir($this->ftpConnection, $this->replaceTilde($folder_path))) {
                        $this->recordFileError("folder",$folder_path,$lang_folder_cant_access);
                        $isError=1;
                    }
                } else {
                    $this->recordFileError("folder",$folder_path,$lang_folder_cant_access);
                    $isError=1;
                }
            }

            if ($isError == 0) {
                return ftp_rawlist($this->ftpConnection, ".");
            }

        }

        return false;

    }

//##############################################
// CHECK FIRST CHAR IS TILDE
//##############################################

    private function checkFirstCharTilde($str) {
        return (substr($str,0,1) == "~") ? 1 : 0;
    }

//##############################################
// RECORD FILE/FOLDER ERROR
//##############################################

    private function recordFileError($str,$file_name,$error) {

        $_SESSION["monstaftp"][$this->serverID]["errors"][] = str_replace("[".$str."]","<strong>".$file_name."</strong>",$error);
    }

    private function getFtpColumnSpan($sort,$name) {

        global $ui;

        // Check current column
        $ord = ($ui->w('sort', 1, 'post') == $sort and $ui->w('ord', 4, 'post') == 'desc') ? 'asc' : 'desc';

        return "<span onclick=\"processForm('&amp;ftpAction=openFolder&amp;openFolder=".rawurlencode($_SESSION["monstaftp"][$this->serverID]["dir_current"])."&amp;sort=".$sort."&amp;ord=".$ord."')\" class=\"cursorPointer\">".$name."</span>";
    }

    public function displayFiles() {

        global $lang_table_name, $lang_table_size, $lang_table_date, $lang_table_time;

        $ftp_rawlist = $this->getFtpRawList($_SESSION["monstaftp"][$this->serverID]["dir_current"]);

        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        # FOLDER/FILES TABLE HEADER
        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $return = "<table width=\"100%\" cellpadding=\"7\" cellspacing=\"0\" id=\"ftpTable\">";
        $return .= "<tr>"."\n";
        #$return .= "<td width=\"16\" class=\"ftpTableHeadingNf\"><input type=\"checkbox\" id=\"checkboxSelector\" onClick=\"checkboxSelectAll()\"></td>"."\n";
        $return .= "<td width=\"16\" class=\"ftpTableHeadingNf\"></td>"."\n";
        $return .= "<td class=\"ftpTableHeading\">".$this->getFtpColumnSpan("n",$lang_table_name)."</td>"."\n";
        $return .= "<td width=\"10%\" class=\"ftpTableHeading\">".$this->getFtpColumnSpan("s",$lang_table_size)."</td>"."\n";
        $return .= "<td width=\"10%\" class=\"ftpTableHeading\">".$this->getFtpColumnSpan("d",$lang_table_date)."</td>"."\n";
        $return .= "<td width=\"10%\" class=\"ftpTableHeading\">".$this->getFtpColumnSpan("t",$lang_table_time)."</td>"."\n";

        $return .= "</tr>"."\n";

        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        # FOLDER UP BUTTON
        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] != "/" && $_SESSION["monstaftp"][$this->serverID]["dir_current"] != "~") {

            $return .= "<tr>"."\n";
            #$return .= "<td width=\"16\"></td>"."\n";
            $return .= "<td width=\"16\"><i class='fa fa-folder-o'></i></td>"."\n";

            $return .= "<td colspan=\"7\">"."\n";

            // Get the parent directory
            $parent = $this->getParentDir();

            $return .= "<div class=\"width100pc\" onDragOver=\"dragFile(event); selectFile('folder0',0);\" onDragLeave=\"unselectFolder('folder0')\" onDrop=\"dropFile('".rawurlencode($parent)."')\"><a href=\"#\" id=\"folder0\" draggable=\"false\" onClick=\"openThisFolder('".rawurlencode($parent)."',1)\">...</a></div>";

            $return .= "</td>"."\n";
            $return .= "</tr>"."\n";
        }

        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        # FOLDERS & FILES
        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        if (sizeof($ftp_rawlist) > 0) {

            // Linux
            if ($this->win_lin == "lin") {
                $return .= $this->createFileFolderArrayLin($ftp_rawlist,"folders");
                $return .= $this->createFileFolderArrayLin($ftp_rawlist,"links");
                $return .= $this->createFileFolderArrayLin($ftp_rawlist,"files");
            }

            // Windows
            if ($this->win_lin == "win") {
                $return .= $this->createFileFolderArrayWin($ftp_rawlist,"folders");
                $return .= $this->createFileFolderArrayWin($ftp_rawlist,"files");
            }
        }

        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        # CLOSE TABLE
        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $return .= "</table>";

        return $return;

    }


###############################################
# CREATE FILE/FOLDER ARRAY FOR LINUX
###############################################

    private function createFileFolderArrayLin($ftp_rawlist, $type) {

        global $ui;

        // set and correct to avoid php notice
        $foldAllAr = false;
        $linkAllAr = false;
        $fileAllAr = false;

        if (!is_array($ftp_rawlist)) {
            $ftp_rawlist = (array) $ftp_rawlist;
        }

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
            if (preg_match("/^\.+/",$file) && $_SESSION["monstaftp"][$this->serverID]["interface"] == "bas")
                $dot_prefix=1;

            if ($file != "." && $file != ".." && $dot_prefix == 0) {

                // Where the last mod date is the previous year, the year will be displayed in place of the time
                if (preg_match("/:/",$ff[7]))
                    $time = $ff[7];
                else
                    $year = $ff[7];

                // Set date
                $date = $this->formatFtpDate($day,$month,$year);

                // Reset user and group
                if ($user == "0")
                    $user = "-";
                if ($group == "0")
                    $group = "-";

                // Add folder to array
                if ($this->getFileType($perms) == "d") {
                    $foldAllAr[] = $file."|d|".$date."|".$time."|".$user."|".$group."|".$perms;
                    $foldNameAr[] = $file;
                    $foldDateAr[] = $date;
                    $foldTimeAr[] = $time;
                    $foldUserAr[] = $user;
                    $foldGroupAr[] = $group;
                    $foldPermsAr[] = $perms;
                }

                // Add link to array
                if ($this->getFileType($perms) == "l") {
                    $linkAllAr[] = $file."|l|".$date."|".$time."|".$user."|".$group."|".$perms;
                    $linkNameAr[] = $file;
                    $linkDateAr[] = $date;
                    $linkTimeAr[] = $time;
                    $linkUserAr[] = $user;
                    $linkGroupAr[] = $group;
                    $linkPermsAr[] = $perms;
                }

                // Add file to array
                if ($this->getFileType($perms) == "f") {
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
            if ($ui->w('sort', 1, 'post') == "")
                $sort = "n";
            else
                $sort = $ui->w('sort', 1, 'post');

            if ($ui->w('ord', 4, 'post') == "")
                $ord = "asc";
            else
                $ord = $ui->w('ord', 4, 'post');

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
                    return $this->getFileListHtml($foldAllAr, "<i class='fa fa-folder-o'></i>");
                }

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
                    return $this->getFileListHtml($linkAllAr, "<i class='fa fa-link'></i>");
                }

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
                    return $this->getFileListHtml($fileAllAr, "<i class='fa fa-file-text-o'></i>");
                }

            }
        }

        return '';
    }


###############################################
# CREATE FILE/FOLDER ARRAY FOR WINDOWS
###############################################

    private function createFileFolderArrayWin($ftp_rawlist,$type) {

        global $ui;

        $foldAllAr = false;
        $fileAllAr = false;

        if (!is_array($ftp_rawlist)) {
            $ftp_rawlist = (array) $ftp_rawlist;
        }

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
            $date = $this->formatFtpDate($day,$month,$year);

            // Format time
            $time = $this->formatWinFtpTime($time);

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
            if ($ui->w('sort', 1, 'post') == "")
                $sort = "n";
            else
                $sort = $ui->w('sort', 1, 'post');

            if ($ui->w('ord', 4, 'post') == "")
                $ord = "asc";
            else
                $ord = $ui->w('ord', 4, 'post');

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
                    return $this->getFileListHtml($foldAllAr, "<i class='fa fa-folder-o'></i>");
                }

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
                    return $this->getFileListHtml($fileAllAr, "<i class='fa fa-file-text-o'></i>");
                }
            }
        }

        return '';
    }


###############################################
# FORMAT FTP DATE
###############################################

    private function formatFtpDate($day,$month,$year) {

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

    private function formatWinFtpTime($time) {

        $h = substr($time,0,2);
        $m = substr($time,3,2);
        $am_pm = substr($time,5,2);

        if ($am_pm == "PM")
            $h = $h + 12;

        $time = $h.":".$m;

        return $time;
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

        return '';
    }

###############################################
# GET FTP COLUMN SPAN
###############################################

    private function getFileListHtml($array,$image) {

        global $ui;

        $html = '';

        if ($this->trCount == 1)
            $this->trCount=1;
        else
            $this->trCount=0;

        $i=1;
        foreach ($array AS $file) {

            list($file,$size,$date,$time,$user,$group,$perms) = explode("|",$file);

            $action = '';

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

                $file_path = $this->getPathFromLink($file);
                $file = preg_replace("/ -> .*/","",$file);

            } else {

                if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] == "/")
                    $file_path = "/".$file;
                else
                    $file_path = $_SESSION["monstaftp"][$this->serverID]["dir_current"]."/".$file;
            }

            if ($this->trCount == 0) {
                $trClass = "trBg0";
                $this->trCount=1;
            } else {
                $trClass = "trBg1";
                $this->trCount=0;
            }
/**
            // Check for checkbox check (only if action button clicked"
            if ($ui->w('ftpAction', 255, 'post') != "") {
                if (isset($_SESSION["monstaftp"][$this->serverID]["clipboard_rename"]) and sizeof($_SESSION["monstaftp"][$this->serverID]["clipboard_rename"]) > 1 and in_array($file,$_SESSION["monstaftp"][$this->serverID]["clipboard_rename"]))
                    $checked = "checked";
                else
                    $checked = "";

            } else {
                $checked = "";
            }
**/
            // Set the date
            if ($this->dateFormatUsa == 1)
                $date = substr($date,4,2)."/".substr($date,6,2)."/".substr($date,2,2);
            else
                $date = substr($date,6,2)."/".substr($date,4,2)."/".substr($date,2,2);

            $html .= "<tr class=\"".$trClass."\">"."\n";
/**            $html .= "<td>"."\n";

            if ($action != "linkAction")
                $html .= "<input type=\"checkbox\" name=\"".$action."[]\" value=\"".rawurlencode($file_path)."\" onclick=\"checkFileChecked()\" ".$checked.">"."\n";

            $html .= "</td>"."\n";**/
            $html .= "<td>".$image."</td>"."\n";
            $html .= "<td>"."\n";

            // Display Folders
            if ($action == "folderAction")
                $html .= "<div class=\"width100pc\" onDragOver=\"dragFile(event); selectFile('folder".$i."',0);\" onDragLeave=\"unselectFolder('folder".$i."')\" onDrop=\"dropFile('".rawurlencode($file_path)."')\"><a href=\"#\" id=\"folder".$i."\" onClick=\"openThisFolder('".rawurlencode($file_path)."',1)\" onContextMenu=\"selectFile(this.id,1); displayContextMenu(event,'','".rawurlencode($file_path)."',".$this->assignWinLinNum().")\" draggable=\"true\" onDragStart=\"selectFile(this.id,1); setDragFile('','".rawurlencode($file_path)."')\">".$this->sanitizeStr($file)."</a></div>"."\n";

            // Display Links
            if ($action == "linkAction")
                $html .= "<div class=\"width100pc\"><a href=\"#\" id=\"link".$i."\" onContextMenu=\"\" draggable=\"false\">".$this->sanitizeStr($file)."</a></div>"."\n";

            // Display files
            if ($action == "fileAction")
                $html .= "<a href=\"".$this->actionTarget."&amp;dl=".rawurlencode($file_path)."\" id=\"file".$i."\" target=\"ajaxIframe\" onContextMenu=\"selectFile(this.id,1); displayContextMenu(event,'".rawurlencode($file_path)."','',".$this->assignWinLinNum().")\" draggable=\"true\" onDragStart=\"selectFile(this.id,1); setDragFile('".rawurlencode($file_path)."','')\">".$this->sanitizeStr($file)."</a>"."\n";

            $html .= "</td>"."\n";
            $html .= "<td>".$this->formatFileSize($size)."</td>"."\n";
            $html .= "<td>".$date."</td>"."\n";
            $html .= "<td>".$time."</td>"."\n";

            $html .= "</tr>"."\n";

            $i++;
        }

        return $html;
    }


###############################################
# GET PATH FROM LINK
###############################################

    private function getPathFromLink($file) {

        $file_path = preg_replace("/.* -> /","",$file);

        // Check if path is not absolute
        if (substr($file_path,0,1) != "/") {

            // Count occurances of ../
            $i=0;
            while (substr($file_path,0,3) == "../") {
                $i++;
                $file_path = substr($file_path,3,strlen($file_path));
            }

            $dir_current = $_SESSION["monstaftp"][$this->serverID]["dir_current"];

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

//##############################################
// GET PARENT DIRECTORY
//##############################################

    private function getParentDir() {

        if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] == "/") {

            $parent = "/";

        } else {

            $path_parts = pathinfo($_SESSION["monstaftp"][$this->serverID]["dir_current"]);
            $parent = $path_parts['dirname'];
        }

        return $parent;
    }

###############################################
# FORMAT FILE SIZES
###############################################

    private function formatFileSize($size) {

        global $lang_size_b;
        global $lang_size_kb;
        global $lang_size_mb;
        global $lang_size_gb;

        if ($size == "d" || $size == "l") {

            $size="";

        } else {

            if ($size < 1024) {
                $size = round($size,2).' '. $lang_size_b;
            } elseif ($size < (1024*1024)) {
                $size = round(($size/1024),0).' '.$lang_size_kb;
            } elseif ($size < (1024*1024*1024)) {
                $size = round((($size/1024)/1024),0).' '.$lang_size_mb;
            } elseif ($size < (1024*1024*1024*1024)) {
                $size = round(((($size/1024)/1024)/1024),0).' '.$lang_size_gb;
            }
        }

        return $size;
    }

###############################################
# DISPLAY ERRORS
###############################################

    public function displayErrors() {

        global $lang_title_errors;

        $sizeAr = sizeof($_SESSION["monstaftp"][$this->serverID]["errors"]);

        $return = '';

        if ($sizeAr > 0) {

            $width = ($this->getMaxStrLen($_SESSION["monstaftp"][$this->serverID]["errors"]) * 10) + 30;
            $height = sizeof($_SESSION["monstaftp"][$this->serverID]["errors"]) * 25;

            $title = $lang_title_errors;

            // Display pop-up
            $return .= $this->displayPopupOpen(1,$width,$height,1,$title);

            $errors = array_reverse($_SESSION["monstaftp"][$this->serverID]["errors"]);

            foreach($errors AS $error) {
                $return .= $error."<br>";
            }

            $vars = "&amp;ftpAction=openFolder&amp;resetErrorArray=1";

            $return .=$this->displayPopupClose(1,$vars,0);
        }
        $_SESSION["monstaftp"][$this->serverID]["errors"] = array();
        return $return;

    }

//##############################################
// DISPLAY POP-UP FRAME OPEN
//##############################################

    private function displayPopupOpen($resize,$width,$height,$isError,$title) {

        global $ui;

        // Set default sizes of exceeded
        if ($resize == 1) {

            if ($width < 400)
                $width = 400;

            if ($height > 400)
                $height = 400;
        }

        // Center window
        if ($ui->id('windowWidth', 255, 'post') > 0)
            $left = round(($ui->id('windowWidth', 255, 'post') - $width) / 2 - 15); // -15 for H padding
        else
            $left = 250;

        if ($ui->id('windowHeight', 255, 'post') > 0)
            $top = round(($ui->id('windowHeight', 255, 'post') - $height) / 2 - 50);
        else
            $top = 250;

        $return = "<div id=\"blackOutDiv\">";
        $return .= "<div id=\"popupFrame\" style=\"left: ".$left."px; top: ".$top."px; width: ".$width."px;\">";

        if ($isError == 1)
            $divId = "popupHeaderError";
        else
            $divId = "popupHeaderAction";

        $return .= "<div id=\"".$divId."\">";
        $return .= $title;
        $return .= "</div>";

        if ($isError == 1)
            $divId = "popupBodyError";
        else
            $divId = "popupBodyAction";

        $return .= "<div id=\"".$divId."\" style=\"height: ".$height."px;\">";

        return $return;

    }

//##############################################
// DISPLAY POP-UP FRAME CLOSE
//##############################################

    function displayPopupClose($isError,$vars,$btnCancel) {

        global $lang_btn_ok;
        global $lang_btn_cancel;

        $return = "</div>";

        if ($isError == 1)
            $divId = "popupFooterError";
        else
            $divId = "popupFooterAction";

        $return .= "<div id=\"".$divId."\">";

        // OK button
        if ($vars != "")
            $return .= "<input type=\"button\" class=\"btn btn-primary\" value=\"".$lang_btn_ok."\" onClick=\"processForm('".$vars."'); activateActionButtons(0,0);\"> ";

        // Cancel button
        if ($btnCancel == 1)
            $return .= "<input type=\"button\" class=\"btn btn-danger\" value=\"".$lang_btn_cancel."\" onClick=\"processForm('&amp;ftpAction=openFolder');\"> ";

        $return .= "</div>";

        $return .= "</div>";
        $return .= "</div>";

        return $return;

    }

    public function divClose() {
        return '</div>';
    }

###############################################
# DISPLAY IFRAME
###############################################

    public function displayAjaxIframe() {
        return '<iframe name="ajaxIframe" id="ajaxIframe" width="0" height="0" frameborder="0" style="visibility: hidden; display: none;"></iframe>';
    }

###############################################
# DISPLAY UPLOAD PROGRESS
###############################################

    public function displayUploadProgress() {

        global $lang_xfer_file;
        global $lang_xfer_size;
        global $lang_xfer_progress;
        global $lang_xfer_elapsed;
        global $lang_xfer_uploaded;
        global $lang_xfer_rate;
        global $lang_xfer_remain;
        return '<div id="uploadProgressDiv" style="visibility:hidden; display:none">
            <table width="100%" cellpadding="7" cellspacing="0" id="uploadProgressTable">
                <tr>
                    <td class="ftpTableHeadingNf" width="1%"></td>
                    <td class="ftpTableHeading" size="35%">' . $lang_xfer_file . '</td>
                    <td class="ftpTableHeading" width="7%">' . $lang_xfer_size . '</td>
                    <td class="ftpTableHeading" width="21%">' . $lang_xfer_progress . '</td>
                    <td class="ftpTableHeading" width="9%">' . $lang_xfer_elapsed . '</td>
                    <td class="ftpTableHeading" width="7%">' . $lang_xfer_uploaded . '</td>
                    <td class="ftpTableHeading" width="9%">' . $lang_xfer_rate . '</td>
                    <td class="ftpTableHeading" width="10%">' . $lang_xfer_remain . '</td>
                    <td class="ftpTableHeading" width="1%"></td>
                </tr>
            </table>
        </div>';
    }

###############################################
# WINDOW FOOTER
###############################################

    public function displayAjaxFooter() {

        global $lang_btn_new_folder;
        global $lang_btn_new_file;
        global $lang_btn_refresh;
        global $lang_info_upload_limit;

        return '<div id="footerDiv">
        <div id="hostInfoDiv">
            <span>' . $lang_info_upload_limit . ':</span> ' . round(($this->upload_limit /(1024 * 1024) ) * 0.9) . ' MB' . '
        </div>
        <div class="floatLeft10">
            <input type="button" value="' . $lang_btn_refresh . '" onClick="refreshListing()" class="btn btn-primary btn-sm">
        </div>
        <div class="floatLeft10">
            <input type="button" value="' . $lang_btn_new_folder . '" onClick="processForm(\'&amp;ftpAction=newFolder\')" class="btn btn-primary btn-sm">
        </div>

        <div class="floatLeft10">
            <input type="button" value="' .  $lang_btn_new_file . '" onClick="processForm(\'&amp;ftpAction=newFile\')" class="btn btn-primary btn-sm">
        </div>

        <div id="uploadButtonsDiv"></div>';
    }

//##############################################
// LOAD JAVASCRIPT LANGUAGE VARS
//##############################################

    public function loadJsLangVars() {

        global $lang_no_xmlhttp;
        global $lang_support_drop;
        global $lang_no_support_drop;
        global $lang_transfer_pending;
        global $lang_transferring_to_ftp;
        global $lang_no_file_selected;
        global $lang_none_selected;
        global $lang_context_open;
        global $lang_context_download;
        global $lang_context_edit;
        global $lang_context_cut;
        global $lang_context_copy;
        global $lang_context_paste;
        global $lang_context_rename;
        global $lang_context_delete;
        global $lang_context_chmod;
        global $lang_size_b;
        global $lang_size_kb;
        global $lang_size_mb;
        global $lang_size_gb;
        global $lang_btn_upload_file;
        global $lang_btn_upload_files;
        global $lang_btn_upload_repeat;
        global $lang_btn_upload_folder;
        global $lang_file_size_error;
        global $lang_context_template;

        return "<script type=\"text/javascript\">
        var lang_no_xmlhttp = '" . $this->quotesEscape($lang_no_xmlhttp,"s") . "';
        var lang_support_drop = '" . $this->quotesEscape($lang_support_drop,"s") . "';
        var lang_no_support_drop = '" . $this->quotesEscape($lang_no_support_drop,"s") . "';
        var lang_transfer_pending = '" . $this->quotesEscape($lang_transfer_pending,"s") . "';
        var lang_transferring_to_ftp = '" . $this->quotesEscape($lang_transferring_to_ftp,"s") . "';
        var lang_no_file_selected = '" . $this->quotesEscape($lang_no_file_selected,"s") . "';
        var lang_none_selected = '" . $this->quotesEscape($lang_none_selected,"s") . "';
        var lang_context_open = '" . $this->quotesEscape($lang_context_open,"s") . "';
        var lang_context_download = '" . $this->quotesEscape($lang_context_download,"s") . "';
        var lang_context_edit = '" . $this->quotesEscape($lang_context_edit,"s") . "';
        var lang_context_cut = '" . $this->quotesEscape($lang_context_cut,"s") . "';
        var lang_context_copy = '" . $this->quotesEscape($lang_context_copy,"s") . "';
        var lang_context_paste = '" . $this->quotesEscape($lang_context_paste,"s") . "';
        var lang_context_rename = '" . $this->quotesEscape($lang_context_rename,"s") . "';
        var lang_context_delete = '" . $this->quotesEscape($lang_context_delete,"s") . "';
        var lang_context_chmod = '" . $this->quotesEscape($lang_context_chmod,"s") . "';
        var lang_size_b = '" . $this->quotesEscape($lang_size_b,"s") . "';
        var lang_size_kb = '" . $this->quotesEscape($lang_size_kb,"s") . "';
        var lang_size_mb = '" . $this->quotesEscape($lang_size_mb,"s") . "';
        var lang_size_gb = '" . $this->quotesEscape($lang_size_gb,"s") . "';
        var lang_btn_upload_file = '" . $this->quotesEscape($lang_btn_upload_file,"s") . "';
        var lang_btn_upload_files = '" . $this->quotesEscape($lang_btn_upload_files,"s") . "';
        var lang_btn_upload_repeat = '" . $this->quotesEscape($lang_btn_upload_repeat,"s") . "';
        var lang_btn_upload_folder = '" . $this->quotesEscape($lang_btn_upload_folder,"s") . "';
        var lang_file_size_error = '" . $this->quotesEscape($lang_file_size_error,"s") . "';
        var lang_context_template = '" . $this->quotesEscape($lang_context_template,"s") . "';

        var upload_limit = '" . $this->upload_limit . "';
    </script>";
    }

###############################################
# ESCAPE QUOTES
###############################################

    private function quotesEscape($str,$type) {

        if ($type == "s" || $type == "")
            $str = str_replace("'","\'",$str);
        if ($type == "d" || $type == "")
            $str = str_replace('"','\"',$str);

        return $str;
    }

###############################################
# UNESCAPE QUOTES
###############################################

    private function quotesUnescape($str) {

        $str = str_replace("\'","'",$str);
        $str = str_replace('\"','"',$str);

        return $str;
    }

###############################################
# REPLACE QUOTES
###############################################

    private function quotesReplace($str,$type) {

        $str = $this->quotesUnescape($str);

        if ($type == "s")
            $str = str_replace("'","&acute;",$str);
        if ($type == "d")
            $str = str_replace('"','&quot;',$str);

        return $str;
    }

###############################################
# LOAD AJAX
###############################################

    public function loadAjax() {

        global $template_to_use;

        $javascript =  (is_file(EASYWIDIR . '/js/' . $template_to_use . '/monstaftp_ajax.js')) ? 'js/' . $template_to_use . '/monstaftp_ajax.js' : 'js/default/monstaftp_ajax.js';

        return '<script type="text/javascript" src="' . $javascript . '"></script>';

    }

###############################################
# WRITE HIDDEN DIVS
###############################################

    public function writeHiddenDivs() {
        return '<div id="contextMenu" style="visibility: hidden; display: none;"></div>
        <div id="indicatorDiv" style="z-index: 1; visibility: hidden; display: none"><i class="fa fa-spinner fa-spin fa-5x"></i></div>';
    }

###############################################
# END FORM
###############################################

    function displayFormEnd() {
        return '</form>';
    }

###############################################
# PROCESS ACTIONS
###############################################

    public function processActions() {

        global $ui;

        $ftpAction = $ui->w('ftpAction', 255, 'post');

        if ($ftpAction == "")
            $ftpAction = $ui->w('ftpAction', 255, 'get');

        // Open folder (always called)
        if ($this->openFolder() == 1) {

            // New file
            if ($ftpAction == "newFile")
                return $this->newFile();

            // New folder
            if ($ftpAction == "newFolder")
                return $this->newFolder();

            // Upload file
            if ($ftpAction == "upload")
                return $this->uploadFile();

            // Cut
            if ($ftpAction == "cut")
                return $this->cutFilesPre();

            // Copy
            if ($ftpAction == "copy")
                return $this->copyFilesPre();

            // Paste
            if ($ftpAction == "paste")
                return $this->pasteFiles();

            // Delete
            if ($ftpAction == "delete")
                return $this->deleteFiles();

            // Rename
            if ($ftpAction == "rename")
                return $this->renameFiles();

            // Drag & Drop
            if ($ftpAction == "dragDrop")
                return $this->dragDropFiles();

            // Edit
            if ($ftpAction == "edit")
                return $this->editFile();

            // Template create
            if ($ftpAction == "template")
                return $this->editTemplate();
        }

        return '';
    }

###############################################
# CHANGE FTP DIRECTORY (OPEN FOLDER)
###############################################

    private function openFolder() {

        global $ui;
        global $lang_folder_doesnt_exist;

        $isError=0;

        if ($this->loggedIn === true) {

            // Set the folder to open
            if (isset($_SESSION["monstaftp"][$this->serverID]["dir_current"]) and $_SESSION["monstaftp"][$this->serverID]["dir_current"] != "")
                $dir = $_SESSION["monstaftp"][$this->serverID]["dir_current"];
            if (isset($ui->post['openFolder']) and $ui->post['openFolder'] != "")
                $dir = $this->quotesUnescape($ui->post['openFolder']);

            // Check dir is set
            if (!isset($dir)) {

                $dir = "";

                // No folder set (must be first login), so set home dir
                if ($this->win_lin == "lin")
                    $dir = "~";
                if ($this->win_lin == "win")
                    $dir = "/";
            }

            // Attempt to change directory
            if (!@ftp_chdir($this->ftpConnection, $dir)) {
                if ($this->checkFirstCharTilde($dir) == 1) {
                    if (!@ftp_chdir($this->ftpConnection, $this->replaceTilde($dir))) {
                        $this->recordFileError("folder",$dir,$lang_folder_doesnt_exist);
                        $isError=1;
                    }
                } else {
                    $this->recordFileError("folder",$dir,$lang_folder_doesnt_exist);
                    $isError=1;
                }
            }

            if ($isError == 0) {

                // Set new directory
                $_SESSION["monstaftp"][$this->serverID]["dir_current"] = $dir;

                // Record new directory to history
                if (!is_array($_SESSION["monstaftp"][$this->serverID]["dir_history"])) // array check
                    $_SESSION["monstaftp"][$this->serverID]["dir_history"] = array();
                if (!in_array($dir,$_SESSION["monstaftp"][$this->serverID]["dir_history"])) {
                    $_SESSION["monstaftp"][$this->serverID]["dir_history"][] = $dir;
                    asort($_SESSION["monstaftp"][$this->serverID]["dir_history"]); // sort array
                }

                return 1;

            } else {

                // Delete item from history
                $this->deleteFtpHistory($dir);

                // Change to previous directory (if folder to open is currently open)
                if ((isset($ui->post['openFolder']) and $ui->post['openFolder'] == $_SESSION["monstaftp"][$this->serverID]["dir_current"]) or !isset($ui->post['openFolder']) or $ui->post['openFolder'] == "")
                    $_SESSION["monstaftp"][$this->serverID]["dir_current"] = $this->getParentDir();

                return 0;
            }
        }

        return 0;
    }

###############################################
# DELETE FTP HISTORY
###############################################

    private function deleteFtpHistory($dirDelete) {

        $dirDelete = str_replace("/","\/",$dirDelete);

        // Check each item in the history
        if (is_array($_SESSION["monstaftp"][$this->serverID]["dir_history"])) {
            foreach($_SESSION["monstaftp"][$this->serverID]["dir_history"] AS $dir) {

                if (!@preg_match("/^".$dirDelete."/", $dir))
                    $dir_history[] = $dir;
            }

            // Set new array
            if (isset($dir_history)) {

                $_SESSION["monstaftp"][$this->serverID]["dir_history"] = $dir_history;

                // Sort array
                if (is_array($_SESSION["monstaftp"][$this->serverID]["dir_history"]))
                    asort($_SESSION["monstaftp"][$this->serverID]["dir_history"]);

            }
        }
    }

###############################################
# NEW FILE
###############################################

    private function newFile() {

        global $ui;
        global $sql;
        global $user_id;
        global $resellerLockupID;
        global $shorten;

        global $lang_title_new_file;
        global $lang_new_file_name;
        global $lang_template;
        global $lang_no_template;
        global $lang_file_exists;
        global $lang_file_cant_make;

        $isError=0;

        $return = '';

        // Set vars
        $vars = "&ftpAction=newFile";

        $file_name = trim($this->quotesUnescape($ui->escaped("newFile","post")));
        $file_names = array();

        $langs = '';


        if ($file_name == "") {

            $title = $lang_title_new_file;
            $width = 400;
            $height = 95;

            // Display pop-up
            $return .= $this->displayPopupOpen(0,$width,$height,0,$title);

            $return .= "<input type=\"text\" name=\"newFile\" id=\"newFile\" placeholder=\"".$lang_new_file_name."\" onkeypress=\"if (event.keyCode==13){ processForm('".$vars."'); return false;}\">";


            $return .= "<p>".$lang_template.": ";
            $return .= "<select name=\"template\">";
            $return .= "<option value=\"\">".$lang_no_template."</option>";

            $query = $sql->prepare("SELECT `templateID`,`name` FROM `gserver_file_templates` WHERE `servertype`=? AND  (`userID` IS NULL OR `userID`=?) AND `resellerID`=?");
            $query->execute(array($shorten, $user_id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $return .= "<option value=\"".$row['templateID']."\">".$row['name']."</option>";
            }

            $return .= $langs;
            $return .= "</select>";

            $return .= $this->displayPopupClose(0,$vars,1);

        } else {

            // md5 to unify and catch any uncaught path traversal attacks
            $fp1 = EASYWIDIR . "/tmp/" . md5($_SESSION['userid'] . $file_name) . '.tmp';

            if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] == "/")
                $fp2 = "/".$file_name;
            else
                $fp2 = $_SESSION["monstaftp"][$this->serverID]["dir_current"]."/".$file_name;

            // Check if file already exists
            if ($this->checkFileExists("f",$file_name,$_SESSION["monstaftp"][$this->serverID]["dir_current"]) == 1) {
                $this->recordFileError("file",$file_name,$lang_file_exists);
            } else {

                $content = '';

                // Get template
                if ($ui->id('template', 10, 'post') and $ui->config('template', 'post') != $lang_no_template) {
                    $query = $sql->prepare("SELECT `content` FROM `gserver_file_templates` WHERE `templateID`=? AND `servertype`=? AND (`userID` IS NULL OR `userID`=?) AND `resellerID`=?");
                    $query->execute(array($ui->id('template', 10, 'post'), $shorten, $user_id, $resellerLockupID));
                    $content = $query->fetchColumn();
                }

                // Write file to server
                $tmpFile = @fopen($fp1,"w+");
                @fputs($tmpFile,$content);
                @fclose($tmpFile);

                // Upload the file
                if (!@ftp_put($this->ftpConnection, $fp2, $fp1, FTP_BINARY)) {
                    if ($this->checkFirstCharTilde($fp2) == 1) {
                        if (!@ftp_put($this->ftpConnection, $this->replaceTilde($fp2), $fp1, FTP_BINARY)) {
                            $this->recordFileError("file",$file_name,$lang_file_cant_make);
                            $isError=1;
                        }
                    } else {
                        $this->recordFileError("file",$file_name,$lang_file_cant_make);
                        $isError=1;
                    }
                }

                if ($isError == 0) {

                    // Open editor
                    $file = $fp2;
                    $return .= $this->displayEditFileForm($file,$content);
                }

                // Delete tmp file
                unlink($fp1);
            }

        }

        return $return;

    }

###############################################
# CHECK IF FILE EXISTS
###############################################

    function checkFileExists($type,$file_name,$folder_path) {

        $ftp_rawlist = $this->getFtpRawList($folder_path);

        if (is_array($ftp_rawlist)) {

            $fileNameAr = array();

            // Go through array of files/folders
            foreach($ftp_rawlist AS $ff) {

                // Lin
                if ($this->win_lin == "lin") {

                    // Split up array into values
                    $ff = preg_split("/[\s]+/",$ff,9);

                    $perms = $ff[0];
                    $file = $ff[8];

                    if ($file != "." && $file != "..") {

                        if ($type == "f" && $this->getFileType($perms) == "f")
                            $fileNameAr[] = $file;

                        if ($type == "d" && $this->getFileType($perms) == "d")
                            $fileNameAr[] = $file;
                    }
                }

                // Win
                if ($this->win_lin == "win") {

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

        return false;
    }

###############################################
# EDIT FILE PROCESS (EDIT FILE)
###############################################

    function displayEditFileForm($file,$content) {

        global $ui;

        global $lang_title_edit_file;
        global $lang_btn_save;
        global $lang_btn_close;

        $return = '';

        $width = ($ui->id('windowWidth',255,'post') > 250) ? $ui->id('windowWidth',255,'post') - 250 : 250;
        $height = ($ui->id('windowHeight',255,'post') > 220) ? $ui->id('windowHeight',255,'post') - 220 : 220;
        $editorHeight = (int) $height - 85;

        $file_display = $this->sanitizeStr($file);
        $file_display = $this->replaceTilde($file_display);
        $title = $lang_title_edit_file.": ".$file_display;

        // Display pop-up
        $return .= $this->displayPopupOpen(0,$width,$height,0,$title);

        $return .= "<input type=\"hidden\" name=\"file\" value=\"".$this->sanitizeStr($file)."\">";
        $return .= "<textarea name=\"editContent\" id=\"editContent\" style=\"height: ".$editorHeight."px;\">".$this->sanitizeStr($content)."</textarea>";

        // Save button
        $return .= "<input type=\"button\" value=\"".$lang_btn_save."\" class=\"btn btn-primary\" onClick=\"submitToIframe('ftpAction=editProcess');\"> ";

        // Close button
        $return .= "<input type=\"button\" value=\"".$lang_btn_close."\" class=\"btn btn-danger\" onClick=\"processForm('ftpAction=openFolder');\"> ";

        $return .=$this->displayPopupClose(0,"",0);

        return $return;
    }

###############################################
# EDIT FILE PROCESS (SAVE FILE)
###############################################

// Saving the file to the iframe preserves the cursor position in the edit div.

    public function editProcess() {

        global $ui;
        global $lang_server_error_up;

        if ($this->loggedIn) {
            // Get file contents
            $file = $this->quotesUnescape($ui->escaped('file','post'));
            $file_name = $this->getFileFromPath($file);

            // user md5 to mask file and also to catch any uncaught path traversal attacks
            $fp1 = EASYWIDIR . "/tmp/" . md5($_SESSION['userid'] . $file_name) . '.tmp';
            $fp2 = $file;

            // Write content to a file
            $tmpFile = @fopen($fp1,"w+");
            @fputs($tmpFile,$ui->escaped('editContent','post'));
            @fclose($tmpFile);

            if (!@ftp_put($this->ftpConnection, $fp2, $fp1, FTP_BINARY)) {
                if ($this->checkFirstCharTilde($fp2) == 1) {
                    if (!@ftp_put($this->ftpConnection, $this->replaceTilde($fp2), $fp1, FTP_BINARY)) {
                        $this->recordFileError("file",$file_name,$lang_server_error_up);
                    }
                } else {
                    $this->recordFileError("file",$file_name,$lang_server_error_up);
                }
            }

            // Delete tmp file
            unlink($fp1);
        }

        return '';
    }

###############################################
# EDIT TEMPLATE PROCESS (SAVE FILE)
###############################################

// Saving the file to the iframe preserves the cursor position in the edit div.

    public function editProcessTemplate() {

        global $ui;
        global $sql;
        global $user_id;
        global $shorten;
        global $resellerLockupID;
        global $lang_server_error_up;


        $file = $this->quotesUnescape($ui->escaped('templateName','post'));
        $file_name = $this->getFileFromPath($file);

        if ($this->loggedIn) {

            $query = $sql->prepare("INSERT INTO `gserver_file_templates` (`userID`,`servertype`,`name`,`content`,`resellerID`) VALUES (?,?,?,?,?)");
            $query->execute(array($user_id, $shorten, $file_name, $ui->escaped('editContent','post'), $resellerLockupID));

        } else {
            $this->recordFileError("file",$file_name,$lang_server_error_up);
        }

        return '';
    }

###############################################
# DOWNLOAD FILE
###############################################

    public function downloadFile() {

        global $ui;
        global $lang_server_error_down;

        if ($this->loggedIn) {

            $isError=0;

            $file = $this->quotesUnescape($ui->escaped("dl","get"));
            $file_name = $this->getFileFromPath($file);
            $fp1 = EASYWIDIR . "/tmp/" . md5($_SESSION['userid'] . $file_name) . '.tmp';
            $fp2 = $file;

            // Download the file
            if (!@ftp_get($this->ftpConnection, $fp1, $fp2, FTP_BINARY)) {
                if ($this->checkFirstCharTilde($fp2) == 1) {
                    if (!@ftp_get($this->ftpConnection, $fp1, $this->replaceTilde($fp2), FTP_BINARY)) {
                        $this->recordFileError("file", $this->quotesEscape($file,"s"), $lang_server_error_down);
                        $isError=1;
                    }
                } else {
                    $this->recordFileError("file", $this->quotesEscape($file,"s"), $lang_server_error_down);
                    $isError=1;
                }
            }

            if ($isError == 0) {

                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"" . $this->quotesEscape($file_name, "d") . "\""); // quotes required for spacing in filename
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");
                header("Content-Description: File Transfer");
                header("Content-Length: " . filesize($fp1));

                flush();

                $fp = fopen($fp1, "rb");
                while (!feof($fp)) {
                    echo fread($fp, 1024);
                    flush();
                }
                fclose($fp);
            }

            // Delete tmp file
            unlink($fp1);
        }

        return '';
    }

###############################################
# NEW FOLDER
###############################################

    private function newFolder() {

        global $ui;
        global $lang_title_new_folder;
        global $lang_new_folder_name;
        global $lang_folder_exists;
        global $lang_folder_cant_make;

        // Set vars
        $vars = "&ftpAction=newFolder";

        $return = '';

        $folder = trim($this->quotesUnescape($ui->escaped("newFolder","post")));

        if ($folder == "") {

            $title = $lang_title_new_folder;
            $width = 400;
            $height = 40;

            // Display pop-up
            $return .= $this->displayPopupOpen(0,$width,$height,0,$title);

            $return .= "<input type=\"text\" name=\"newFolder\" id=\"newFolder\" placeholder=\"".$lang_new_folder_name."\" onkeypress=\"if (event.keyCode==13){ processForm('".$vars."'); return false;}\">";

            $return .= $this->displayPopupClose(0,$vars,1);

        } else {

            // Check if folder exists
            if ($this->checkFileExists("d",$folder,$_SESSION["monstaftp"][$this->serverID]["dir_current"]) == 1 || $folder == "..") {
                $this->recordFileError("folder",$folder,$lang_folder_exists);
            } else {

                if (!@ftp_mkdir($this->ftpConnection, $folder))
                    $this->recordFileError("folder",$folder,$lang_folder_cant_make);
            }
        }
        return $return;
    }

###############################################
# DELETE FILES & FOLDERS
###############################################

    private function deleteFiles () {

        global $lang_file_doesnt_exist;
        global $lang_cant_delete;

        $folderArray = $this->recreateFileFolderArrays("folder");
        $fileArray = $this->recreateFileFolderArrays("file");

        // folders
        foreach($folderArray AS $folder) {

            $folder = $this->getFileFromPath($folder);

            $this->deleteFolder($folder,$_SESSION["monstaftp"][$this->serverID]["dir_current"]);
        }

        // files
        foreach($fileArray AS $file) {

            $isError=0;
            $file_decoded = urldecode($file);

            if ($file != "") {

                // Check if file exists
                if ($this->checkFileExists("f",$file,$_SESSION["monstaftp"][$this->serverID]["dir_current"]) == 1) {
                    $this->recordFileError("file",$file,$lang_file_doesnt_exist);
                } else {

                    if (!@ftp_delete($this->ftpConnection, $file_decoded)) {
                        if ($this->checkFirstCharTilde($file_decoded) == 1) {
                            if (!@ftp_delete($this->ftpConnection, $this->replaceTilde($file_decoded))) {
                                $isError=1;
                            }
                        } else {
                            $isError=1;
                        }
                    }

                    // If deleting decoded file fails, try original file name
                    if ($isError == 1) {

                        if (!@ftp_delete($this->ftpConnection, "".$file."")) {
                            if ($this->checkFirstCharTilde($file) == 1) {
                                if (!@ftp_delete($this->ftpConnection, "".$this->replaceTilde($file)."")) {
                                    $this->recordFileError("file",$this->getFileFromPath($file),$lang_cant_delete);
                                }
                            } else {
                                $this->recordFileError("file",$this->getFileFromPath($file),$lang_cant_delete);
                            }
                        }
                    }
                }
            }
        }
        return '';
    }

###############################################
# DELETE FOLDER
###############################################

    private function deleteFolder($folder,$path) {

        global $lang_cant_delete;
        global $lang_folder_doesnt_exist;
        global $lang_folder_cant_delete;

        $isError=0;
        $folder_path = '';

        // List contents of folder
        if ($path != "/" && $path != "~") {

            $folder_path = $path."/".$folder;

        } else {

            if ($this->win_lin == "lin")

                if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] == "/")
                    $folder_path = "/".$folder;
            if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] == "~")
                $folder_path = "~/".$folder;

            if ($this->win_lin == "win")
                $folder_path = "/".$folder;
        }

        $ftp_rawlist = $this->getFtpRawList($folder_path);

        // Go through array of files/folders
        if (sizeof($ftp_rawlist) > 0) {

            foreach($ftp_rawlist AS $ff) {

                $isFolder=0;
                $file = '';

                // Split up array into values (Lin)
                if ($this->win_lin == "lin") {

                    $ff = preg_split("/[\s]+/",$ff,9);
                    $perms = $ff[0];
                    $file = $ff[8];

                    if ($this->getFileType($perms) == "d")
                        $isFolder=1;
                }

                // Split up array into values (Win)
                if ($this->win_lin == "win") {

                    $ff = preg_split("/[\s]+/",$ff,4);
                    $size = $ff[2];
                    $file = $ff[3];

                    if ($size == "<DIR>")
                        $isFolder=1;
                }

                if ($file != "." && $file != "..") {

                    // Check for sub folders and then perform this function
                    if ($isFolder == 1) {
                        $this->deleteFolder($file,$folder_path);
                    } else {
                        // otherwise delete file
                        $file_path = $folder_path."/".$file;
                        if (!@ftp_delete($this->ftpConnection, "".$file_path.""))  {
                            if ($this->checkFirstCharTilde($file_path) == 1) {
                                if (!@ftp_delete($this->ftpConnection, "".$this->replaceTilde($file_path)."")) {
                                    $this->recordFileError("file",$file_path,$lang_cant_delete);
                                }
                            } else {
                                $this->recordFileError("file",$file_path,$lang_cant_delete);
                            }
                        }
                    }
                }
            }
        }

        // Check if file exists
        if ($this->checkFileExists("d",$folder,$folder_path) == 1) {

            $_SESSION["monstaftp"][$this->serverID]["errors"][] = str_replace("[file]","<strong>".$this->tidyFolderPath($folder_path,$folder)."</strong>",$lang_folder_doesnt_exist);

        } else {

            // Chage dir up before deleting
            ftp_cdup($this->ftpConnection);

            // Delete the empty folder
            if (!@ftp_rmdir($this->ftpConnection, "".$folder_path."")) {
                if ($this->checkFirstCharTilde($folder_path) == 1) {
                    if (!@ftp_rmdir($this->ftpConnection, "".$this->replaceTilde($folder_path)."")) {
                        $this->recordFileError("folder",$folder_path,$lang_folder_cant_delete);
                        $isError=1;
                    }
                } else {
                    $this->recordFileError("folder",$folder_path,$lang_folder_cant_delete);
                    $isError=1;
                }
            }

            // Remove directory from history
            if ($isError == 0)
                $this->deleteFtpHistory($folder_path);
        }
    }

###############################################
# RECREATE FOLDER & FILE ARRAYS
###############################################

    private function recreateFileFolderArrays($type) {

        global $ui;

        $array = false;
        $arrayNew = array();

        if ($ui->escaped("fileSingle","post") != "" || $ui->escaped("folderSingle","post") != "") {

            // Single file/folder
            if ($type == "file" && $ui->escaped("fileSingle","post") != "") {
                $file = $this->quotesUnescape($ui->escaped("fileSingle","post"));
                $arrayNew[] = $file;
            }
            if ($type == "folder" && $ui->escaped("folderSingle","post") != "")
                $arrayNew[] = $this->quotesUnescape($ui->escaped("folderSingle","post"));

        } else {

            // Array file/folder
            if ($type == "file")
                $array = (array) $ui->escaped("fileAction","post");
            if ($type == "folder")
                $array = (array) $ui->escaped("folderAction","post");

            if (is_array($array) and count($array) > 0) {

                foreach($array AS $file) {

                    $file = $this->quotesUnescape($file);

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

    private function renameFiles() {

        global $ui;
        global $lang_file_exists;
        global $lang_folder_exists;
        global $lang_cant_rename;
        global $lang_title_rename;

        $return = '';

        // Check for processing of form
        if ($ui->id('processAction',1,'post') == 1) {

            $i=0;

            // Go through array of saved names
            foreach ($_SESSION["monstaftp"][$this->serverID]["clipboard_rename"] AS $file) {

                $isError=0;

                $file_name = trim($ui->escaped('file'.$i,'post'));
                $file_name = $this->quotesUnescape($file_name);
                $file = $this->quotesUnescape($file);
                $fileExists=0;

                // Check for a different name
                if ($file_name != $file) {

                    if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] == "/")
                        $file_to_move = "/".$file;
                    if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] == "~")
                        $file_to_move = "~/".$file;
                    if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] != "/" && $_SESSION["monstaftp"][$this->serverID]["dir_current"] != "~")
                        $file_to_move = $_SESSION["monstaftp"][$this->serverID]["dir_current"]."/".$file;

                    $file_destination = $_SESSION["monstaftp"][$this->serverID]["dir_current"]."/".$file_name;

                    // Check if file exists
                    if ($this->checkFileExists("f",$file_name,$_SESSION["monstaftp"][$this->serverID]["dir_current"]) == 1) {
                        $this->recordFileError("file",$this->sanitizeStr($file_name),$lang_file_exists);
                        $fileExists=1;
                    }

                    // Check if folder exists
                    if ($this->checkFileExists("d",$file_name,$_SESSION["monstaftp"][$this->serverID]["dir_current"]) == 1) {
                        $this->recordFileError("folder",$this->sanitizeStr($file_name),$lang_folder_exists);
                        $fileExists=1;
                    }

                    if ($fileExists == 0 and isset($file_to_move)) {

                        if (!@ftp_rename($this->ftpConnection, $file_to_move, $file_destination)) {
                            if ($this->checkFirstCharTilde($file_to_move) == 1) {
                                if (!@ftp_rename($this->ftpConnection, $this->replaceTilde($file_to_move), $this->replaceTilde($file_destination))) {
                                    $this->recordFileError("file",$this->sanitizeStr($file),$lang_cant_rename);
                                    $isError=1;
                                }
                            } else {
                                $this->recordFileError("file",$this->sanitizeStr($file),$lang_cant_rename);
                                $isError=1;
                            }
                        }

                        if ($isError == 0) {

                            // Delete item from history
                            $this->deleteFtpHistory($file_to_move);
                        }
                    }
                }

                $i++;
            }

            // Reset var
            $_SESSION["monstaftp"][$this->serverID]["clipboard_rename"] = array();

        } else {

            // Recreate arrays
            $fileArray = $this->recreateFileFolderArrays("file");
            $folderArray = $this->recreateFileFolderArrays("folder");
            $_SESSION["monstaftp"][$this->serverID]["clipboard_rename"] = array();

            $n = sizeof($fileArray) + sizeof($folderArray);
            $height = $n * 35;

            $width = 565;
            $title = $lang_title_rename;

            // Display pop-up
            $return .= $this->displayPopupOpen(1,$width,$height,0,$title);

            $i=0;

            // Set vars
            $vars = "&ftpAction=rename&processAction=1";
            $onKeyPress = "onkeypress=\"if (event.keyCode==13){ processForm('".$vars."'); activateActionButtons(0,0); return false; }\"";

            // Display folders
            foreach($folderArray AS $folder) {

                $folder = $this->getFileFromPath($folder);

                $return .= "<i class='fa fa-folder-o'></i> ";
                $return .= "<input type=\"text\" name=\"file".$i."\" class=\"inputRename\" value=\"".$this->quotesReplace($folder,"d")."\" ".$onKeyPress."><br>";
                $_SESSION["monstaftp"][$this->serverID]["clipboard_rename"][] = $folder;
                $i++;
            }

            // Display files
            foreach($fileArray AS $file) {

                $file = $this->getFileFromPath($file);

                $return .= "<i class='fa fa-file'></i> ";
                $return .= "<input type=\"text\" name=\"file".$i."\" class=\"inputRename\" value=\"".$this->quotesReplace($file,"d")."\" ".$onKeyPress."><br>";
                $_SESSION["monstaftp"][$this->serverID]["clipboard_rename"][] = $file;
                $i++;
            }

            $return .= $this->displayPopupClose(0,$vars,1);
        }
        return $return;
    }


###############################################
# CLIPBOARD FILES
###############################################

    private function clipboard_files () {

        // Recreate arrays
        $folderArray = $this->recreateFileFolderArrays("folder");
        $fileArray = $this->recreateFileFolderArrays("file");

        // Reset cut session var
        $_SESSION["monstaftp"][$this->serverID]["clipboard_folders"] = array();
        $_SESSION["monstaftp"][$this->serverID]["clipboard_files"] = array();

        // Folders
        foreach($folderArray AS $folder) {
            $_SESSION["monstaftp"][$this->serverID]["clipboard_folders"][] = $this->quotesUnescape($folder);
        }

        // Files
        foreach($fileArray AS $file) {
            $_SESSION["monstaftp"][$this->serverID]["clipboard_files"][] = $this->quotesUnescape($file);
        }

        return '';

    }

###############################################
# CUT FILES & FOLDERS
###############################################

    private function cutFilesPre() {

        $_SESSION["monstaftp"][$this->serverID]["copy"] = 0;
        return $this->clipboard_files();
    }

###############################################
# COPY FILES & FOLDERS
###############################################

    private function copyFilesPre() {

        $_SESSION["monstaftp"][$this->serverID]["copy"] = 1;
        return $this->clipboard_files();
    }

###############################################
# PASTE FILES
###############################################

    private function pasteFiles() {

        if ($_SESSION["monstaftp"][$this->serverID]["copy"] == 1)
            return $this->copyFiles();

        return $this->moveFiles();
    }

###############################################
# COPY FILES
###############################################

    private function copyFiles() {

        // As there is no PHP function to copy files by FTP on a remote server, the files
        // need to be downloaded to the client server and then uploaded to the copy location.

        global $ui;
        global $lang_folder_exists;
        global $lang_file_exists;
        global $lang_server_error_down;
        global $lang_server_error_up;

        // Check for a right-clicked folder (else it's current)
        if ($ui->escaped('rightClickFolder', 'post'))
            $folderMoveTo = $this->quotesUnescape($ui->escaped('rightClickFolder', 'post'));
        else
            $folderMoveTo = $_SESSION["monstaftp"][$this->serverID]["dir_current"];

        // Folders
        foreach ($_SESSION["monstaftp"][$this->serverID]["clipboard_folders"] as $folder) {

            $folder_name = $this->getFileFromPath($folder);

            $path_parts = pathinfo($folder);
            $dir_source = $path_parts['dirname'];

            // Check if folder exists
            if ($this->checkFileExists("f",$folder_name,$folderMoveTo) == 1) {
                $this->recordFileError("folder",$this->tidyFolderPath($folderMoveTo,$folder_name),$lang_folder_exists);
            } else {
                $this->copyFolder($folder_name,$folderMoveTo,$dir_source);
            }
        }

        // Files
        foreach ($_SESSION["monstaftp"][$this->serverID]["clipboard_files"] as $file) {

            $isError=0;

            $file_name = $this->getFileFromPath($file);
            $fp1 = EASYWIDIR . "/tmp/" . md5($_SESSION['userid'] . $file_name) . '.tmp';
            $fp2 = $file;
            $fp3 = $folderMoveTo."/".$file_name;

            // Check if file exists
            if ($this->checkFileExists("f",$file_name,$folderMoveTo) == 1) {
                $this->recordFileError("file",$this->tidyFolderPath($folderMoveTo,$file_name),$lang_file_exists);
            } else {

                // Download file to client server
                if (!@ftp_get($this->ftpConnection, $fp1, $fp2, FTP_BINARY)) {
                    if ($this->checkFirstCharTilde($fp2) == 1) {
                        if (!@ftp_get($this->ftpConnection, $fp1, $this->replaceTilde($fp2), FTP_BINARY)) {
                            $this->recordFileError("file",$file_name,$lang_server_error_down);
                            $isError=1;
                        }
                    } else {
                        $this->recordFileError("file",$file_name,$lang_server_error_down);
                        $isError=1;
                    }
                }

                if ($isError == 0) {

                    // Upload file to remote server
                    if (!@ftp_put($this->ftpConnection, $fp3, $fp1, FTP_BINARY)) {
                        if ($this->checkFirstCharTilde($fp3) == 1) {
                            if (!@ftp_put($this->ftpConnection, $this->replaceTilde($fp3), $fp1, FTP_BINARY))
                                $this->recordFileError("file",$file_name,$lang_server_error_up);
                        } else {
                            $this->recordFileError("file",$file_name,$lang_server_error_up);
                        }
                    }
                }

                // Delete tmp file
                unlink($fp1);
            }
        }
        return '';
    }

###############################################
# MOVE FILES (CUT)
###############################################

    private function moveFiles() {

        global $ui;
        global $lang_move_conflict;
        global $lang_folder_exists;
        global $lang_folder_cant_move;
        global $lang_file_exists;
        global $lang_file_cant_move;

        $moveError = 0;
        // Check for a right-clicked folder (else it's current)
        if ($ui->escaped('rightClickFolder', 'post'))
            $folderMoveTo = $this->quotesUnescape($ui->escaped('rightClickFolder', 'post'));
        else
            $folderMoveTo = $_SESSION["monstaftp"][$this->serverID]["dir_current"];

        // Check if destination folder is a sub-folder
        if (sizeof($_SESSION["monstaftp"][$this->serverID]["clipboard_folders"]) > 0) {

            $sourceFolder = str_replace("/","\/",$_SESSION["monstaftp"][$this->serverID]["clipboard_folders"][0]);

            if (preg_match("/".$sourceFolder."/", $folderMoveTo)) {

                $_SESSION["monstaftp"][$this->serverID]["errors"][] = $lang_move_conflict;

                $moveError=1;
            }
        }

        if ($moveError != 1) {

            // Folders
            foreach ($_SESSION["monstaftp"][$this->serverID]["clipboard_folders"] as $folder_to_move) {

                $isError=0;

                // Create the new filename and path
                $file_destination = $this->getFileFromPath($folder_to_move);
                $folder = $this->getFileFromPath($folder_to_move);

                // Check if folder exists
                if ($this->checkFileExists("d",$folder,$folderMoveTo) == 1) {
                    $this->recordFileError("folder",$this->tidyFolderPath($folderMoveTo,$folder),$lang_folder_exists);
                } else {

                    if (!@ftp_rename($this->ftpConnection, $folder_to_move, $file_destination)) {
                        if ($this->checkFirstCharTilde($folder_to_move) == 1) {
                            if (!@ftp_rename($this->ftpConnection, $this->replaceTilde($folder_to_move), $this->replaceTilde($file_destination))) {
                                $this->recordFileError("folder",$this->tidyFolderPath($file_destination,$folder_to_move),$lang_folder_cant_move);
                                $isError=1;
                            }
                        } else {
                            $this->recordFileError("folder",$this->tidyFolderPath($file_destination,$folder_to_move),$lang_folder_cant_move);
                            $isError=1;
                        }
                    }

                    if ($isError == 0)
                        $this->deleteFtpHistory($folder_to_move);
                }
            }

            // Files
            foreach ($_SESSION["monstaftp"][$this->serverID]["clipboard_files"] as $file_to_move) {

                // Create the new filename and path
                $file_destination = $folderMoveTo."/".$this->getFileFromPath($file_to_move);
                $file = $this->getFileFromPath($file_to_move);

                // Check if file exists
                if ($this->checkFileExists("f",$file,$folderMoveTo) == 1) {
                    $this->recordFileError("file",$file,$lang_file_exists);
                } else {

                    if (!@ftp_rename($this->ftpConnection, $file_to_move, $file_destination)) {
                        if ($this->checkFirstCharTilde($file_to_move) == 1) {
                            if (!@ftp_rename($this->ftpConnection, $this->replaceTilde($file_to_move), $this->replaceTilde($file_destination))) {
                                $this->recordFileError("file",$file_to_move,$lang_file_cant_move);
                            }
                        } else {
                            $this->recordFileError("file",$file_to_move,$lang_file_cant_move);
                        }
                    }
                }
            }
        }

        $_SESSION["monstaftp"][$this->serverID]["clipboard_folders"] = array();
        $_SESSION["monstaftp"][$this->serverID]["clipboard_files"] = array();

        return '';
    }

###############################################
# COPY FOLDERS
###############################################

    private function copyFolder($folder,$dir_destin,$dir_source) {

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
        if (!@ftp_chdir($this->ftpConnection, $dir_source."/".$folder)) {
            if ($this->checkFirstCharTilde($dir_source) == 1) {
                if (!@ftp_chdir($this->ftpConnection, $this->replaceTilde($dir_source)."/".$folder)) {
                    $this->recordFileError("folder",$this->tidyFolderPath($dir_destin,$folder),$lang_folder_cant_access);
                    $isError=1;
                }
            } else {
                $this->recordFileError("folder",$this->tidyFolderPath($dir_destin,$folder),$lang_folder_cant_access);
                $isError=1;
            }
        }

        if ($isError == 0) {

            // Check if destination folder exists
            if ($this->checkFileExists("d",$folder,$dir_destin) == 1) {
                $this->recordFileError("folder",$this->tidyFolderPath($dir_destin,$folder),$lang_folder_exists);
            } else {

                // Create the new folder
                if (!@ftp_mkdir($this->ftpConnection, $dir_destin."/".$folder)) {
                    if ($this->checkFirstCharTilde($dir_destin) == 1) {
                        if (!@ftp_mkdir($this->ftpConnection, $this->replaceTilde($dir_destin)."/".$folder)) {
                            $this->recordFileError("folder",$this->tidyFolderPath($dir_destin,$folder),$lang_folder_cant_make);
                            $isError=1;
                        }
                    } else {
                        $this->recordFileError("folder",$this->tidyFolderPath($dir_destin,$folder),$lang_folder_cant_make);
                        $isError=1;
                    }
                }
            }
        }

        if ($isError == 0) {

            // Copy permissions (Lin)
            if ($this->win_lin == "lin") {

                $mode = $this->getPerms($dir_source,$folder);
                $lang_folder_cant_chmod = str_replace("[perms]",$mode,$lang_folder_cant_chmod);

                if (function_exists('ftp_chmod')) {
                    if (!ftp_chmod($this->ftpConnection, $mode, $dir_destin."/".$folder)) {
                        if ($this->checkFirstCharTilde($dir_destin) == 1) {
                            if (!@ftp_chmod($this->ftpConnection, $mode, $this->replaceTilde($dir_destin)."/".$folder)) {
                                $this->recordFileError("folder",$folder,$lang_folder_cant_chmod);
                            }
                        } else {
                            $this->recordFileError("folder",$folder,$lang_folder_cant_chmod);
                        }
                    }
                }
            }

            // Go through array of files/folders
            $ftp_rawlist = $this->getFtpRawList($dir_source."/".$folder);

            if (is_array($ftp_rawlist)) {

                foreach($ftp_rawlist AS $ff) {

                    $isError=0;
                    $perms = false;
                    $file = false;
                    $isDir = 0;

                    // Split up array into values (Lin)
                    if ($this->win_lin == "lin") {

                        $ff = preg_split("/[\s]+/",$ff,9);
                        $perms = $ff[0];
                        $file = $ff[8];

                        if ($this->getFileType($perms) == "d")
                            $isDir=1;
                    }

                    // Split up array into values (Win)
                    if ($this->win_lin == "win") {

                        $ff = preg_split("/[\s]+/",$ff,4);
                        $size = $ff[2];
                        $file = $ff[3];

                        if ($size == "<DIR>")
                            $isDir=1;
                    }

                    if ($file != "." && $file != "..") {

                        // Check for sub folders and then perform this function
                        if ($isDir == 1) {
                            if ($file != $folder) {
                                $this->copyFolder($file,$dir_destin."/".$folder,$dir_source."/".$folder);
                            }
                        } else {

                            $fp1 = EASYWIDIR . "/tmp/" . md5($_SESSION['userid'] . $file) . '.tmp';
                            $fp2 = $dir_source."/".$folder."/".$file;
                            $fp3 = $dir_destin."/".$folder."/".$file;

                            // Download
                            if (!@ftp_get($this->ftpConnection, $fp1, $fp2, FTP_BINARY)) {
                                if ($this->checkFirstCharTilde($fp2) == 1) {
                                    if (!@ftp_get($this->ftpConnection, $fp1, $this->replaceTilde($fp2), FTP_BINARY)) {
                                        $this->recordFileError("file",$file,$lang_server_error_down);
                                        $isError=1;
                                    }
                                } else {
                                    $this->recordFileError("file",$file,$lang_server_error_down);
                                    $isError=1;
                                }
                            }

                            // Upload
                            if ($isError == 0) {

                                if (!@ftp_put($this->ftpConnection, $fp3, $fp1, FTP_BINARY)) {
                                    if ($this->checkFirstCharTilde($fp3) == 1) {
                                        if (!@ftp_put($this->ftpConnection, $this->replaceTilde($fp3), $fp1, FTP_BINARY)) {
                                            $this->recordFileError("file",$file,$lang_server_error_down);
                                            $isError=1;
                                        }
                                    } else {
                                        $this->recordFileError("file",$file,$lang_server_error_down);
                                        $isError=1;
                                    }
                                }
                            }

                            if ($isError == 0) {

                                // Chmod files (Lin)
                                if ($this->win_lin == "lin") {

                                    $perms = $this->getChmodNumber($perms);
                                    $mode = $this->formatChmodNumber($perms);

                                    $lang_file_cant_chmod = str_replace("[perms]",$perms,$lang_file_cant_chmod);

                                    if (function_exists('ftp_chmod')) {
                                        if (!@ftp_chmod($this->ftpConnection, $mode, $fp3)) {
                                            if ($this->checkFirstCharTilde($fp3) == 1) {
                                                if (!@ftp_chmod($this->ftpConnection, $mode, $this->replaceTilde($fp3))) {
                                                    $this->recordFileError("file",$file,$lang_server_error_down);
                                                }
                                            } else {
                                                $this->recordFileError("file",$file,$lang_server_error_down);
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
            unset($_SESSION["monstaftp"][$this->serverID]["ftp_rawlist"]);
        }
        return '';
    }

###############################################
# GET PERMISSIONS OF FILE/FOLDER
###############################################

    private function getPerms($folder,$file_name) {

        $ftp_rawlist = $this->getFtpRawList($folder);

        if (is_array($ftp_rawlist)) {

            foreach($ftp_rawlist AS $ff) {

                // Split up array into values
                $ff = preg_split("/[\s]+/",$ff,9);

                $perms = $ff[0];
                $file = $ff[8];

                if ($file == $file_name) {
                    $perms = $this->getChmodNumber($perms);
                    $perms = $this->formatChmodNumber($perms);
                    return $perms;
                }
            }
        }
        return false;
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

            $m = 0;

            if ($i>=1&&$i<=3)
                $m=100;
            if ($i>=4&&$i<=6)
                $m=10;
            if ($i>=7&&$i<=9)
                $m=1;

            $l = substr($str,$i,1);

            if ($l != "d" && $l != "-") {

                $n = 0;

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

//##############################################
// TIDY FOLDER PATH
//##############################################
    function tidyFolderPath($str1,$str2) {
        $str1 = $this->replaceTilde($str1);
        return ($str1 == "/") ? "/".$str2 : $str1."/".$str2;
    }

###############################################
# EDIT FILE
###############################################

    private function editFile() {

        global $ui;
        global $lang_server_error_down;

        $return = '';
        $isError=0;

        $file = $this->quotesUnescape($ui->escaped("file","post"));
        $file_name = $this->getFileFromPath($file);
        $fp1 = EASYWIDIR."/tmp/".md5($_SESSION["userid"].$file_name).'.tmp';
        $fp2 = $file;

        // Download the file
        if (!@ftp_get($this->ftpConnection, $fp1, $fp2, FTP_BINARY)) {

            if ($this->checkFirstCharTilde($fp2) == 1) {
                if (!@ftp_get($this->ftpConnection, $fp1, $this->replaceTilde($fp2), FTP_BINARY)) {
                    $this->recordFileError("file",$this->quotesEscape($file,"s"),$lang_server_error_down);
                    $isError=1;
                }
            } else {
                $this->recordFileError("file",$this->quotesEscape($file,"s"),$lang_server_error_down);
                $isError=1;
            }
        }

        if ($isError == 0) {

            $content = '';

            // Check file has contents
            if (filesize($fp1) > 0) {

                $fd = @fopen($fp1,"r");
                $content = @fread($fd, filesize($fp1));
                @fclose($fd);
            }

            $return = $this->displayEditFileForm($file,$content);
        }

        // Delete tmp file
        unlink($fp1);

        return $return;
    }

###############################################
# CREATE TEMPLATE
###############################################

    private function editTemplate() {

        global $ui;
        global $lang_server_error_down;

        $return = '';
        $isError=0;

        $file = $this->quotesUnescape($ui->escaped("file","post"));
        $file_name = $this->getFileFromPath($file);
        $fp1 = EASYWIDIR."/tmp/".md5($_SESSION["userid"].$file_name).'.tmp';
        $fp2 = $file;

        // Download the file
        if (!@ftp_get($this->ftpConnection, $fp1, $fp2, FTP_BINARY)) {

            if ($this->checkFirstCharTilde($fp2) == 1) {
                if (!@ftp_get($this->ftpConnection, $fp1, $this->replaceTilde($fp2), FTP_BINARY)) {
                    $this->recordFileError("file",$this->quotesEscape($file,"s"),$lang_server_error_down);
                    $isError=1;
                }
            } else {
                $this->recordFileError("file",$this->quotesEscape($file,"s"),$lang_server_error_down);
                $isError=1;
            }
        }

        if ($isError == 0) {

            $content = '';

            // Check file has contents
            if (filesize($fp1) > 0) {

                $fd = @fopen($fp1,"r");
                $content = @fread($fd, filesize($fp1));
                @fclose($fd);
            }

            $return = $this->displayTemplateFileFileForm($file,$content);
        }

        // Delete tmp file
        unlink($fp1);

        return $return;
    }

###############################################
# EDIT FILE PROCESS (EDIT FILE)
###############################################

    function displayTemplateFileFileForm($file,$content) {

        global $ui;

        global $lang_btn_save;
        global $lang_btn_close;
        global $lang_context_template;

        $return = '';


        $width = ($ui->id('windowWidth',255,'post') > 250) ? $ui->id('windowWidth',255,'post') - 250 : 250;
        $height = ($ui->id('windowHeight',255,'post') > 220) ? $ui->id('windowHeight',255,'post') - 220 : 220;
        $editorHeight = (int) $height - 105;

        $fileExplode = preg_split('/\//', $file, -1, PREG_SPLIT_NO_EMPTY);
        $file_display = $this->sanitizeStr($fileExplode[(count($fileExplode)-1)]);
        $file_display = $this->replaceTilde($file_display);
        $title = $lang_context_template.": ".$file_display;

        // Display pop-up
        $return .= $this->displayPopupOpen(0,$width,$height,0,$title);

        $return .= "<input type=\"text\" class=\"span12\" name=\"templateName\" value=\"".$this->sanitizeStr($file_display)."\">";
        $return .= "<textarea name=\"editContent\" id=\"editContent\" style=\"height: ".$editorHeight."px;\">".$this->sanitizeStr($content)."</textarea>";

        // Save button
        $return .= "<input type=\"button\" value=\"".$lang_btn_save."\" class=\"btn btn-primary\" onClick=\"submitToIframe('ftpAction=templateProcess');\"> ";

        // Close button
        $return .= "<input type=\"button\" value=\"".$lang_btn_close."\" class=\"btn btn-danger\" onClick=\"processForm('ftpAction=openFolder');\"> ";

        $return .=$this->displayPopupClose(0,"",0);

        return $return;
    }
###############################################
# UPLOAD FILE
###############################################

    private function uploadFile() {

        global $ui;
        global $lang_server_error_up;
        global $lang_browser_error_up;

        $file_name = (string) $ui->escaped('HTTP_X_FILENAME','server');
        $path = (string) $ui->escaped('filePath','get');

        if ($file_name) {

            $fp1 = EASYWIDIR."/tmp/".md5($_SESSION["userid"].$file_name).'.tmp';

            // Check if a folder is being uploaded
            if ($path != "") {

                // Check to see folder path exists (and create)
                $this->createFolderHeirarchy($path);
                $fp2 = $_SESSION["monstaftp"][$this->serverID]["dir_current"]."/".$path.$file_name;

            } else {

                if ($_SESSION["monstaftp"][$this->serverID]["dir_current"] == "/")
                    $fp2 = "/".$file_name;
                else
                    $fp2 = $_SESSION["monstaftp"][$this->serverID]["dir_current"]."/".$file_name;
            }

            // Check if file reached server
            if (file_put_contents($fp1,file_get_contents('php://input'))) {

                if (!@ftp_put($this->ftpConnection, $fp2, $fp1, FTP_BINARY)) {
                    if ($this->checkFirstCharTilde($fp2) == 1) {
                        if (!@ftp_put($this->ftpConnection, $this->replaceTilde($fp2), $fp1, FTP_BINARY)) {
                            $this->recordFileError("file",$file_name,$lang_server_error_up);
                        }
                    } else {
                        $this->recordFileError("file",$file_name,$lang_server_error_up);
                    }
                }
            } else {
                $this->recordFileError("file",$file_name,$lang_browser_error_up);
            }

            // Delete tmp file
            unlink($fp1);
        }
        return '';
    }

###############################################
# CREATE FOLDER HEIRARCHY
###############################################

    function createFolderHeirarchy($path) {

        $folderAr = explode("/",$path);

        $n = sizeof($folderAr);
        for ($i=0;$i<$n;$i++) {

            if (isset($folder))
                $folder .= "/".$folderAr[$i];
            else
                $folder = $folderAr[$i];

            if (!@ftp_mkdir($this->ftpConnection, $folder)) {
                if ($this->checkFirstCharTilde($folder) == 1)
                    @ftp_mkdir($this->ftpConnection, $this->replaceTilde($folder));
            }
        }
    }

###############################################
# DRAG & DROP FILES
###############################################

    private function dragDropFiles() {

        global $ui;
        global $lang_file_exists;
        global $lang_folder_exists;
        global $lang_file_cant_move;

        $fileExists=0;
        $dragFile = $this->quotesUnescape($ui->escaped('dragFile','post'));
        $dropFolder = $this->quotesUnescape($ui->escaped('dropFolder','post'));
        $file_name = $this->getFileFromPath($dragFile);

        // Check if file exists
        if ($this->checkFileExists("f",$file_name,$dropFolder) == 1) {
            $this->recordFileError("file",$this->tidyFolderPath($dropFolder,$file_name),$lang_file_exists);
            $fileExists=1;
        }

        // Check if folder exists
        if ($this->checkFileExists("d",$file_name,$dropFolder) == 1) {
            $this->recordFileError("folder",$this->tidyFolderPath($dropFolder,$file_name),$lang_folder_exists);
            $fileExists=1;
        }

        if ($fileExists == 0) {

            $isError=0;

            if (!@ftp_rename($this->ftpConnection, $dragFile, $dropFolder."/".$file_name)) {
                if ($this->checkFirstCharTilde($dragFile) == 1) {
                    if (!@ftp_rename($this->ftpConnection, $this->replaceTilde($dragFile), $this->replaceTilde($dropFolder)."/".$file_name)) {
                        $this->recordFileError("file",$this->getFileFromPath($dragFile),$lang_file_cant_move);
                        $isError=1;
                    }
                } else {
                    $this->recordFileError("file",$this->getFileFromPath($dragFile),$lang_file_cant_move);
                    $isError=1;
                }
            }

            if ($isError == 0) {

                // Delete item from history
                $this->deleteFtpHistory($dragFile);
            }
        }
        return '';
    }

###############################################
# UPLOAD FILE (IFRAME)
###############################################

    public function iframeUpload() {

        global $lang_server_error_up;
        global $lang_browser_error_up;

        $fp1 = $_FILES["uploadFile"]["tmp_name"];
        $fp2 = $_SESSION["dir_current"]."/".$_FILES["uploadFile"]["name"];

        if ($fp1 != "") {

            if (!@ftp_put($this->ftpConnection, $fp2, $fp1, FTP_BINARY)) {
                if ($this->checkFirstCharTilde($fp2) == 1) {
                    if (!@ftp_put($this->ftpConnection, $this->replaceTilde($fp2), $fp1, FTP_BINARY)) {
                        $this->recordFileError("file",$_FILES["uploadFile"]["name"],$lang_server_error_up);
                    }
                } else {
                    $this->recordFileError("file",$_FILES["uploadFile"]["name"],$lang_server_error_up);
                }
            }

        } else {
            $this->recordFileError("file",$_FILES["uploadFile"]["name"],$lang_browser_error_up);
        }
        return '';
    }
}
