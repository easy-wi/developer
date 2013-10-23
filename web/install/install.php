<?php

/**
 * File: install.php.
 * Author: Ulrich Block
 * Date: 03.10.12
 * Time: 14:09
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

ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
define('EASYWIDIR', dirname(dirname(__FILE__)));

?>
<!DOCTYPE html>
<head>
    <title>Installer</title>
    <link rel="shortcut icon" href="images/favicon.png" type="image/png" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <script src="../js/default/main.js" type="text/javascript"></script>
    <style type="text/css">
        * {margin:0;padding:0;}
        html, body {height:100%;background:none;font-family:"Lucida Sans Unicode", "Bitstream Vera Sans", "Trebuchet Unicode MS", "Lucida Grande", Verdana, Helvetica, sans-serif;font-size:14px;text-align:left;}
        p, h1 {padding-bottom:10px;}
        img {border:0;}
        h2 {background-color:#2565A2;margin:2px;}
        img {border:0;}
        form {display:inline;}
        a:link{color:#CCCCCC;text-decoration:none;}
        a:visited{color:#CCCCCC;text-decoration:none;}
        a:hover{color:#FFFFFF;text-decoration:none;}
        a:active{color:#F00000;text-decoration:none;}
        td{padding-top:5px;padding-left:5px;padding-right:5px;}
        select {min-width:200px;background-color:#F8F8FF;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;}
        input[type=text] {min-width:200px;background-color:#F8F8FF;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        input[type=tel] {min-width:200px;background-color:#F8F8FF;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        input[type=email] {min-width:200px;background-color:#F8F8FF;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        input[type=password] {min-width:200px;background-color:#F8F8FF;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        input[type=number] {min-width:20px;max-width:50px;background-color:#F8F8FF;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        input[type=url] {min-width:200px;background-color:#F8F8FF;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        textarea {background-color:#F8F8FF;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        select.flagmenu option {background-repeat:no-repeat;background-position:bottom left;padding-left:20px;}
        #screenlog {padding:1em 1em 1em 1em;background-color:#F8F8FF;color:#000033;}
        #wrapper {background-color:#F8F8FF;position:relative;min-height:100%;}
        * html #wrapper {height:100%;}
        #header{background-color:#2565A2;height:66px;}
        #headerwrapper {margin:auto;width:1000px;}
        #navigation1{height:24px;background-color:#101010;}
        #languages{float:left;margin-top:2px;padding:0px;padding-left:6px;}
        #support{float:right;padding-right:6px;color:#CCCCCC;}
        #navigation2{clear:both;height:40px;margin-left:auto;margin-right:auto;width:1000px;line-height:1.3;}
        #welcome{float:left;color:#CCCCCC;}
        #logout{float:right;padding-right:6px;}
        #content {clear:both;color:	#000033;padding:1em 1em 4em 1em;width:1000px;margin-left:auto;margin-right:auto;}
        #leftmenu {float:left;list-style:none;background-color:#FFFFFF;width:200px;padding:0px;text-align:left;border-spacing:0px;border-radius:4px;box-shadow:0 0 4px 2px #999;}
        ul#leftmenu {padding:0px;}
        ul#leftmenu li{background-color:#2565A2;border-radius:4px;}
        ul#leftmenu li li{list-style:none;background-color:#FFFFFF;padding-left:10px;border-bottom:1px dotted #000033;border-radius:0px;}
        ul#leftmenu li a{font-size:16px;color:#EEEEEE;}
        ul#leftmenu li a:hover{font-size:16px;color:#FFFFFF;}
        ul#leftmenu li li a{font-size:15px;color:#000033;line-height:1.0em;}
        ul#leftmenu li li a:hover{font-size:15px;color:#BBBBBB;line-height:1.0em;}
        #datapage {float:right;color:	#000033;width:750px;}
        #spacer {clear:both;padding:1em 1em 4em 1em;}
        #footer {position:absolute;bottom:0;width:100%;line-height:3em;text-align:center;background-color:#101010;}
        #footerwrapper {margin:auto;width:1000px;}
        #legalinfo {margin-left:500px;float:left;}
        #easywi {float:right;}
        #redirect{background-color:#FFF95D;color:#000000;margin-left:auto;margin-right:auto;margin-top:auto; margin-bottom:auto;text-align:center;vertical-align:top;height:40px;}
        .ticket {margin-left:10px;width:695px;padding:10px;background-color:#F8F8FF;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        .menu {width:800px;border:none;border:0px;margin:0px;padding:0px;font:67.5% "Lucida Sans Unicode", "Bitstream Vera Sans", "Trebuchet Unicode MS", "Lucida Grande", Verdana, Helvetica, sans-serif;font-size:14px;font-weight:bold;margin-left:0px;float:right;}
        .blueline {height:1px;background-color:#2590F2;}
        .error {color:#FF0000;}
        .iplist {height:350px;width:200px;overflow:auto;border:0px;padding:2px;background:#FFFFFF;}
        .shortiplist {height:100px;width:200px;overflow:auto;border:0px;padding:2px;background:#FFFFFF;}
        .small_edit {background-image:url(images/16_edit.png); background-repeat:no-repeat;width:16px;height:16px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .small_delete {background-image:url(images/16_delete.png); background-repeat:no-repeat;width:16px;height:16px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .restart {background-image:url(images/16_restart.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .stop {background-image:url(images/16_stop.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .small_restart {background-image:url(images/16_restart.png); background-repeat:no-repeat;width:16px;height:16px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .small_stop {background-image:url(images/16_stop.png); background-repeat:no-repeat;width:16px;height:16px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .edit {background-image:url(images/16_edit.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#F8F8FF;}
        .edit_table {background-image:url(images/16_edit.png); background-repeat:no-repeat;border:0;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .details_table {background-image:url(images/16_details.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .install {background-image:url(images/16_install.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .delete {background-image:url(images/16_delete.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .save {background-image:url(images/16_check.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#F8F8FF;}
        .config {background-image:url(images/16_config.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#F8F8FF;}
        .sourcetv {background-image:url(images/movie.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .backwards {background-image:url(images/16_backwards.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .lock {background-image:url(images/16_lock.png); background-repeat:no-repeat;width:32px;height:32px;padding:0;margin:0;border:0; background-position:0px 0px;background-color:#FFFFFF;}
        .innertable {width:740px;}
        .usertables {width:750px;min-height:750px;padding:5px;background-color:#FFFFFF;text-align:left;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:10px;border-radius:10px;-moz-box-shadow:0 0 8px 4px #999;-webkit-box-shadow:0 0 8px 4px #999;box-shadow:0 0 8px 4px #999;}
        .usertables th{background-color:#2565A2;color:#EEEEEE;text-decoration:none;font-size:15px;font-weight:bold;text-align:left;padding:4px;-moz-border-radius:5px;border-radius:5px;}
        .usertables a:link{color:#2565A2;text-decoration:none;}
        .usertables a:visited{color:#2565A2;text-decoration:none;}
        .usertables a:hover{color:#CCCCCC;text-decoration:none;}
        .usertables a:active{color:#CCCCCC;text-decoration:none;}
        .usertables th a:link{color:#EEEEEE;}
        .usertables th a:visited{color:#EEEEEE;}
        .usertables th a:hover{color:#FFFFFF;}
        .usertables th a:active{color:#FFFFFF;}
        .userlog {height:600px;width:720px;overflow:auto;border:0px;padding:2px;background:#FFFFFF;}
        .logtable {width:700px;border:0;border-spacing:0px;padding:0;}
        .logtable tr:nth-child(odd) {background-color:#FFFFFF;}
        .logtable tr:nth-child(even) {background-color:#FAFAD2;}
        .calendar {height:100%;background:none;font-size:12px;font-weight:bold;border:0;border-spacing:0px;padding:0;}
        .calendar td {border-right:1px solid #A2ADBC;border-bottom:1px solid #A2ADBC;width:100px;background-color:#FFFFFF;}
        .cleantable  td{border-right:0px;border-bottom:0px;border-spacing:0px;padding:0;text-align:right;}
        .right {text-align:right;}
        .middle {vertical-align:middle;text-align:center;}
        .bold {font-size:15px;font-weight:bold;}
        .bold a{font-size:15px;font-weight:bold;}
        .centeralign {vertical-align:center;}
        .tablebackground {background-color:#B0E2FF;width:750px;}
        .infotext{width:400px;margin:100px auto;background-color:#FFFFFF;padding:15px;text-align:justify;}
        .tooltip {vertical-align:bottom;}
        .tooltip span {display:none;padding:2px 3px;margin-top:-32px;margin-left:4px;min-width:100px;max-width:300px;}
        .tooltip:hover span{display:inline;position:absolute;background:#FFFFFF;border:1px solid #CCCCCC;color:#000033;}
        .versioncheckbad, .versioncheckok {padding:10px;}
        .versioncheckbad {border:#FFCC60 1px solid;background:#FFFFCA;color:#CC0000;}
        .versioncheckok {border:#008700 1px solid;background:#E8FCDB;color:#008700;}
        .versioncheckbad a:link {color:#008700;text-decoration:none;}
        .versioncheckbad a:visited {color:#008700;}
        .versioncheckbad a:hover {color:#008700;}
        .versioncheckbad a:active {color:#008700;}
        .changelog {padding:4px;border:#808080 1px solid;background:#F7F7F7;color:#0C0C0C;}
        .changelog ul{padding-left:16px;}
        .protected {padding:10px;text-align:right;}
        .login {color:#FFFFFF;background-color:#2565A2;font:67.5% "Lucida Sans Unicode", "Bitstream Vera Sans", "Trebuchet Unicode MS", "Lucida Grande", Verdana, Helvetica, sans-serif;font-size:18px;width:80px;height:30px;vertical-align:center;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        .email {visibility:hidden;}
        .lendbutton {color:#FFFFFF;background-color:#2565A2;font:67.5% "Lucida Sans Unicode", "Bitstream Vera Sans", "Trebuchet Unicode MS", "Lucida Grande", Verdana, Helvetica, sans-serif;font-size:18px;width:120px;height:30px;vertical-align:center;border-spacing:0px;border-style:ridge;border-color:#000000;border-collapse:separate;border-width:1px;-moz-border-radius:4px;border-radius:4px;}
        .page_edit {width:725px;border:1px;border-style:ridge;background-color:#CCC;}
        .page_edit input[type=text]{width:600px;height:20px;}
        .page_edit input[type=button] {height:25px;}
    </style>
</head>
<body>
<div id="wrapper">
    <div id="header">
        <div class="navigation1">
            <div class="headerwrapper">
                <div class="logo"></div>
                <div class="welcome"></div>
            </div>
        </div>
        <div class="blueline"></div>
        <div class="navigation2"></div>
        <div class="blueline"></div>
    </div>
    <div id="content">
        <?php
        function small_letters_check($value,$laeng) {
            if (strlen($value) <= $laeng and preg_match("/^[a-z]+$/", $value)) {
                return $value;
            }
        }
        function active_check($value) {
            if (strlen($value) == 1 and preg_match("/[N,Y]/", $value)) {
                return $value;
            }
        }
        function isid($value,$count){
            if (isset($value)) {
                if (strlen($value)<=$count and is_numeric($value)) {
                    return $value;
                }
            }
        }
        function uname_check($value,$laeng) {
            if (preg_match("/^[\w\-\.]+$/", $value) and strlen($value) <= $laeng) {
                return $value;
            }
        }
        function password_check($value,$laeng) {
            if (strlen($value) <= $laeng and strlen($value) > 4 and preg_match("/[A-Za-z0-9]/", $value)) {
                return $value;
            }
        }
        function ismail($value) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return $value;
            }
        }
        function wpreg_check($value,$laeng) {
            if (strlen($value) <= $laeng and preg_match("/^[\w]+$/", $value)) {
                return $value;
            }
        }
        function description($value){
            if (isset($value)) {
                $value=htmlentities($value, ENT_QUOTES, 'UTF-8');
                if (preg_match("/^[\x{0400}-\x{04FF}\w\r\n\-():;&.,% ]+/u", $value)) {
                    return $value;
                }
            }
        }
        $lang_detect=strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));
        if (file_exists("$lang_detect.xml")) {
            $sprache=simplexml_load_file("$lang_detect.xml");
        } else {
            $sprache=simplexml_load_file("en.xml");
        }

        function remove_magic_quotes ($value) {
            if (function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc()==1) {
                $value=stripcslashes($value);
            }
            return $value;
        }
        function inputsecurity($validate=null) {
            if ($validate == null) {
                foreach ($_REQUEST as $key => $val) {
                    if (is_string($val)) {
                        $_REQUEST[$key] = remove_magic_quotes($val);
                    } else if (is_array($val)) {
                        $_REQUEST[$key] = inputsecurity($val);
                    }
                }
                foreach ($_GET as $key => $val) {
                    if (is_string($val)) {
                        $_GET[$key] = remove_magic_quotes($val);
                    } else if (is_array($val)) {
                        $_GET[$key] = inputsecurity($val);
                    }
                }
                foreach ($_POST as $key => $val) {
                    if (is_string($val)) {
                        $_POST[$key] = remove_magic_quotes($val);
                    } else if (is_array($val)) {
                        $_POST[$key] = inputsecurity($val);
                    }
                }
                if (isset($_SESSION) and is_array($_SESSION)) {
                    foreach ($_SESSION as $key => $val) {
                        if (is_string($val)) {
                            $_SESSION[$key] = remove_magic_quotes($val);
                        } else if (is_array($val)) {
                            $_SESSION[$key] = inputsecurity($val);
                        }
                    }
                }
            } else {
                foreach ($validate as $key => $val) {
                    if (is_string($val)) {
                        $validate[$key] = addslashes($val);
                    } else if (is_array($val)) {
                        $validate[$key] = inputsecurity($val);
                    }
                    return $validate;
                }
            }
        }
        inputsecurity();
        if (isset($_POST['host']) and isset($_POST['db'])) {
        ?>
        <ul id="leftmenu">
            <li>
                <ul>
                    <li><a href="install.php"><?php echo $sprache->step1a;?></a></li>
                    <li><div class="error"><?php echo $sprache->step2a;?></div></li>
                    <li><?php echo $sprache->step3a;?></li>
                </ul>
            </li>
        </ul>
        <div id="datapage">
            <form action="install.php" method="post">
                <table class="usertables">
                    <tr>
                        <th colspan="2"><?php echo $sprache->step2a.": ".$sprache->step2b;?></th>
                    </tr>
                    <?php
                    $config= @fopen("../stuff/config.php",'w') or die("Error opening the config file for writing.");
                    $configdata='<?php
// This file was generated by the installer
$host="'.$_POST['host'].'";
$user="'.$_POST['user'].'";
$db="'.$_POST['db'].'";
$pwd="'.$_POST['pwd'].'";
$databanktype="'.$_POST['databanktype'].'";
$captcha="'.$_POST['captcha'].'";
$title="'.$_POST['title'].'";
$debug = 0;
?>';
                    @fwrite($config, $configdata) or die("Can not write the configdata");
                    fclose($config);
                    $config= @fopen("../stuff/keyphrasefile.php",'w') or die("Error opening the config file for writing.");
                    $configdata='<?php
// This file was generated by the installer
$aeskey="'.$_POST['aeskey'].'";
?>';
                    @fwrite($config, $configdata) or die("Can not write the configdata");
                    fclose($config);
                    include(EASYWIDIR . '/stuff/config.php');
                    try {
                        $sql=new PDO("$databanktype:host=$host;dbname=$db", $user, $pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
                    }
                    catch(PDOException $error) {
                    ?>
                    <tr>
                        <td colspan="2"><?php echo $sprache->error_database."<br />".$error->getMessage();?></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <div id="spacer"></div>
    <div id="footer">
        <div class="blueline"></div>
        <a href="http://easy-wi.com" target="_blank">easy-wi.com</a>
    </div>
</div>
</body>
    </html>
<?php
die();
}
class UpdateResponse {
    public $response = '';
    function __construct() {
        $this->response = '';
    }
    function add ($newtext) {
        $this->response .= $newtext;
    }
    function printresponse () {
        return $this->response;
    }
    function __destruct() {
        unset($this->response);
    }
}
$response=new UpdateResponse();
$admin_id = 1;
$main = 1;
$reseller_id = 0;
include('../stuff/tables_add.php');
include('../stuff/tables_repair.php');
if (!isset($sql) or !is_object($sql)) die('Error: Could not establish database connection');
$selectlanguages = array();
if (is_dir('../languages/default/')){
    $dirs=scandir('../languages/default/');
    foreach ($dirs as $row) {
        if (small_letters_check($row, '2')) {
            if ($row==$lang_detect) {
                $selectlanguages[]="<option value=\"$row\" selected=\"selected\" style=\"background-image:url(../images/flags/${row}.png);\">$row</option>";
            } else {
                $selectlanguages[]="<option value=\"$row\" style=\"background-image:url(../images/flags/${row}.png);\">$row</option>";
            }
        }
    }
}
$zeichen = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9');
$anzahl=count($zeichen);
$anzahlcorrect = $anzahl-1;
$randompass = '';
for($i = 1; $i<=10; $i++){
    $wuerfeln = mt_rand(0,$anzahlcorrect);
    $randompass .= $zeichen[$wuerfeln];
}
?>
    <tr>
        <td>
            <br />
            <?php echo $sprache->language;?>
            <br /><br />
        </td>
        <td>
            <br />
            <select name="language" class="flagmenu">
                <?php foreach ($selectlanguages as $selectlanguage) { echo $selectlanguage; echo "\n"; } ?>
            </select>
            <br /><br />
        </td>
    </tr>
    <tr>
        <td style="width:200px;"><?php echo $sprache->email;?></td>
        <td><input type="text" name="email" value="yourmail@mail.com" required="required"/></td>
    </tr>
    <tr>
        <td colspan="2"><?php echo $sprache->email2;?><br /><br /></td>
    </tr>
    <tr>
        <td><?php echo $sprache->faillogins;?><br /><br /></td>
        <td><input type="text" name="faillogins" value="5" required="required"/><br /><br /></td>
    </tr>
    <tr>
        <td><?php echo $sprache->brandname;?><br /><br /></td>
        <td><input type="text" name="brandname" value="by myhost.com"/><br /><br /></td>
    </tr>
    <tr>
        <td>
            <?php echo $sprache->prefix1;?>
        </td>
        <td>
            <select name="prefix1" onchange="textdrop('prefix');">
                <option value="Y"><?php echo $sprache->yes;?></option>
                <option value="N"><?php echo $sprache->no;?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo $sprache->prefix2;?></td>
    </tr>
    <tr>
        <td colspan="2">
            <table id="prefix">
                <tr>
                    <td style="width:202px;"><?php echo $sprache->prefix3;?></td>
                    <td>
                        <input type="text" name="prefix2" value="user" />
                    </td>
                </tr>
            </table>
            <br />
        </td>
    </tr>
    <tr>
        <td><?php echo $sprache->user2;?></td>
        <td><input type="text" name="cname" value="myusername" required="required"/></td>
    </tr>
    <tr>
        <td><?php echo $sprache->passw_1;?></td>
        <td><input type="password" name="passw1" value="" placeholder="<?php echo $randompass;?>" required="required"/></td>
    </tr>
    <tr>
        <td><?php echo $sprache->passw_2;?></td>
        <td><input type="password" name="passw2" value="" placeholder="<?php echo $randompass;?>" required="required"/></td>
    </tr>
    <tr>
        <td colspan="2">
            <div align="right"><input type="submit" name="submit" value="<?php echo $sprache->step3a;?>" /></div>
        </td>
    </tr>
<?php
} else if (isset($_POST['prefix1']) and isset($_POST['email'])) {
$fail = 0;
$emessage = '';
if (!active_check($_POST['prefix1'])) {
    $fail = 1;
    $emessage .="</br>Prefix";
}
if (!isid($_POST['faillogins'], '2')) {
    $fail = 1;
    $emessage .="</br>Faillogins";
}
if (!small_letters_check($_POST['language'], '2')) {
    $fail = 1;
    $emessage .="</br>Language";
}
if ($_POST['passw1'] != $_POST['passw2']) {
    $fail = 1;
    $emessage .="</br>Pasword";
}
if (!uname_check($_POST['cname'],20)) {
    $fail = 1;
    $emessage .="</br>Adminname";
}
    if ($fail!="1") {
include(EASYWIDIR . '/stuff/config.php');
    try {
        $sql=new PDO("$databanktype:host=$host;dbname=$db", $user, $pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
        $sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $error) {
        ?>
        <tr>
            <td colspan="2"><?php echo $sprache->error_database."<br />".$error->getMessage();?></td>
        </tr>
        </table>
        </form>
        </div>
        </div>
        <div id="spacer"></div>
        <div id="footer">
            <div class="blueline"></div>
            <a href="http://easy-wi.com" target="_blank">easy-wi.com</a>
        </div>
        </div>
        </body>
        </html>
        <?php
        die();
    }
$cname=uname_check($_POST['cname'],20);
$password=password_check($_POST['passw1'],50);
if (ismail($_POST['email'])) $email=ismail($_POST['email']);
else $email='changeme@mail.de';
$prefix1=(active_check($_POST['prefix1'])) ? $_POST['prefix1'] : 'Y';
$prefix2=(wpreg_check($_POST['prefix2'], '15')) ? $_POST['prefix2'] : '';
$brandname=description($_POST['brandname']);
if (isset($_POST['licence'])) $licence=small_letters_check($_POST['licence'], '20');
else $licence = '';
$master="N";
$imageserver = 0;
$language=small_letters_check($_POST['language'], '2');
$faillogins=isid($_POST['faillogins'], '2');
include(EASYWIDIR . '/stuff/keyphrasefile.php');
$insert_usergroups = $sql->prepare("INSERT INTO `usergroups` (`defaultgroup`,`name`,`grouptype`,`root`,`miniroot`) VALUES
('Y','Admin Default','a','Y','N'),
('Y','Reseller Default','r','Y','N'),
('Y','User Default','u','N','Y')");
$insert_usergroups->execute();
$query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `grouptype`='a' LIMIT 1");
$query->execute();
$groupID = $query->fetchColumn();
$query = $sql->prepare("INSERT INTO `settings` (`language`,`imageserver`,`master`,`email`,`prefix1`,`prefix2`,`faillogins`,`brandname`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,'0')");
$query->execute(array($language,$imageserver,$master,$email,$prefix1,$prefix2,$faillogins,$brandname));
$query = $sql->prepare("INSERT INTO `userdata` (`active`,`cname`,`security`,`mail`,`accounttype`,`creationTime`,`updateTime`) VALUES ('Y',?,?,?,'a',NOW(),NOW())");
$query->execute(array($cname,md5($password),$email));
$userID = $sql->lastInsertId();
$query = $sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`) VALUES (?,?)");
$query->execute(array($userID,$groupID));
$query = $sql->prepare("INSERT INTO `eac` (`resellerid`) VALUES (0)");
$query->execute();
$query = $sql->prepare("INSERT INTO `qstatshorten` (`qstat`,`description`) VALUES
		('a2s', 'Half-Life 2'),(
		'ams', 'Americas Army v2.x'),
		('bfs', 'BFRIS'),
		('cods', 'Call of Duty'),
		('crs', 'C & C: Renegade'),
		('d3p', 'Descent3 PXO protocol'),
		('d3s', 'Descent3'),
		('dm3s', 'Doom 3'),
		('efs', 'Star Trek: Elite Force'),
		('fcs', 'FarCry'),
		('grs', 'Ghost Recon'),
		('gtasamp', 'San Andreas Multiplayer'),
		('h2s', 'Hexen II'),
		('hla2s', 'Half-Life'),
		('hrs', 'Heretic II'),
		('hws', 'HexenWorld'),
		('jk3s', 'Jedi Knight: Jedi Academy'),
		('kps', 'Kingpin'),
		('maqs', 'MoH: Allied Assault (Q/maqs)'),
		('mas', 'MoH: Allied Assault (mas)'),
		('mhs', 'MoH: Allied Assault (mhs)'),
		('mtasa', 'Multi Theft Auto San Andreas'),
		('minecraft', 'Minecraft'),
		('netp', 'NetPanzer'),
		('nexuizs', 'Nexuiz'),
		('other', 'Other'),
		('preys', 'PREY'),
		('prs', 'Pariah'),
		('q2s', 'Quake II'),
		('q3s', 'Quake III: Arena'),
		('q4s', 'Quake 4'),
		('qs', 'Quake'),
		('qws', 'QuakeWorld'),
		('rss', 'Ravenshield'),
		('rws', 'Return to Castle Wolfenstein'),
		('sas', 'Savage'),
		('sfs', 'Soldier of Fortune'),
		('sgs', 'Shogo: Mobile Armor Division'),
		('sms', 'Serious Sam'),
		('sns', 'Sin'),
		('sof2s', 'Soldier of Fortune 2'),
		('t2s', 'Tribes 2'),
		('tbs', 'Tribes'),
		('teeworlds', 'Teeworlds'),
		('tm', 'TrackMania'),
		('tremulous', 'Tremulous'),
		('uns', 'Unreal'),
		('ut2004s', 'UT2004'),
		('ut2s', 'Unreal Tournament 2003'),
		('warsows', 'Warsow'),
		('woets', 'Enemy Territory')");
$query->execute();
$query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`qstat`,`gamemod`,`gamemod2`,`configs`,`configedit`,`qstatpassparam`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`resellerid`,`mapGroup`) VALUES
('S',232330,1,'css','Counter-Strike: Source','game','srcds_run','','cstrike','67',12,'de_dust2','./%binary% -game cstrike -ip %ip% -port %port% -hostport %port% +maxplayers %slots% +map %map% +tv_port %tvport% +tv_maxclients 1 +clientport %port3%',NULL,'66','a2s','N','','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full','[cfg/server.cfg] cfg\r\ntestslots \"%slots%\"\r\ntestslots2 %slots%\r\n[cfg/server.ini] ini\r\ninistile=\"%slots%\"\r\ninistile2=\"%slots%\"\r\n[cfg/server.xml] xml\r\n<inistile>%slots%</inistile>\r\n','password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',90,1,'cstrike','Counter-Strike 1.6','game','hlds_run',NULL,'cstrike','100',12,'de_dust','./%binary% -game cstrike -ip %ip% -port %port% -sys_ticrate %tic% +maxplayers %slots% +map %map% +fps_max %fps%','[Classic Casual = default]\r\n+game_type 0 +game_mode 0 +mapgroup mg_bomb\r\n\r\n[Classic Competitive]\r\n+game_type 0 +game_mode 1 +mapgroup mg_bomb\r\n\r\n[Arms Race]\r\n+game_type 1 +game_mode 0 +mapgroup mg_armsrace\r\n\r\n[Demolition]\r\n+game_type 1 +game_mode 1 +mapgroup mg_demolition\r\n\r\n[Deathmathch]\r\n+game_type 1 +game_mode 2 +mapgroup mg_allclassic','100','hla2s','N','css','server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,2,27015,27016,NULL,NULL,27019,0,NULL),
('S',90,1,'czero','Counter-Strike: Condition Zero','game','hlds_run',NULL,'czero','500',12,'de_dust','./%binary% -game cstrike -ip %ip% -port %port% -pingboost 2 +sys_ticrate %tic% +maxplayers %slots% +map %map% +fps_max %fps%','czero','500','hla2s','Y','css','server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',232290,1,'dods','Day of Defeat: Source','game','srcds_run','','dod','66',12,'dod_anzio','./%binary% -game dod -tickrate %tic% -ip %ip% -port %port% -autoupdate -verify_all +maxplayers %slots% +map %map% +fps_max %fps% +tv_port %tvport% +replay_port %port3% +alias plugin_load \"test\"',NULL,'100','a2s','N','css','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('N',NULL,1,'cod4','Call of Duty 4','game','cod4_lnxded',NULL,NULL,'',12,NULL,'./%binary% +set dedicated 2 +exec dedicated.cfg +set fs_basepath %absolutepath%/ +set fs_homepath %absolutepath%/.callofduty4 +set net_ip %ip% +set net_port %port% +set sv_maxclients %slots% +map %map% ','[PAM402 = default]\r\n+set fs_game mods/pam402\r\n\r\n[Punkbuster]\r\n+set sv_punkbuster 1','','cods','N','css','main/dedicated.cfg\r\nmain/server.cfg',NULL,'pswrd:1',100,4,27015,27016,27017,27018,NULL,0,NULL),
('S',550,1,'left4dead','Left 4 Dead','game','srcds_run','l4d','left4dead',NULL,0,'l4d_airport01_greenhouse','./%binary% -game left4dead -ip %ip% -port %port% -autoupdate  +map %map% ',NULL,NULL,'a2s','N','css','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',222860,1,'left4dead2','Left 4 Dead 2','game','srcds_run','left4dead2','left4dead2',NULL,0,'c2m1_highway','./%binary% -game left4dead2 -ip %ip% -port %port% -autoupdate +map %map% ',NULL,NULL,'a2s','N','css','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('Y',NULL,1,'pvkii','Pirates,Vikings and Knights II','game','srcds_run','orangebox','pvkii','100',12,'tw_temple','./%binary% -game pvkii -ip %ip% -port %port% -autoupdate +maxplayers %slots% +map %map% +fps_max %fps% +tv_port %tvport%',NULL,NULL,'a2s','N','css','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',17515,1,'ageofchivalry','Age Of Chivalry','game','srcds_run','orangebox','ageofchivalry','75',12,'aoc_battleground','./%binary% -game ageofchivalry -ip %ip% -port %port% -autoupdate +maxplayers %slots% +map %map% +fps_max %fps% +tv_port %tvport%',NULL,'66','a2s','N','css','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',90,1,'dod','Day of Defeat','game','hlds_run',NULL,'dod','100',12,'dod_anzio','./%binary% -game dod -ip %ip% -port %port% +sys_ticrate %tic% +maxplayers %slots% +map %map% +fps_max %fps%','dod','100','hla2s','Y','css','server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',232370,1,'hl2mp','HL2 Deathmatch','game','srcds_run','','hl2mp','100',12,'dm_lockdown','./%binary% -game hl2mp -ip %ip% -port %port% -autoupdate +maxplayers %slots% +map %map% +fps_max %fps% +tv_port %tvport%',NULL,NULL,'a2s','N','css','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',17705,1,'insurgency','Insurgency','game','srcds_run',NULL,'insurgency','100',12,'ins_abdallah','./%binary% -game insurgency -ip %ip% -port %port% -autoupdate +maxplayers %slots% +map %map% +fps_max %fps%',NULL,'100','a2s','N','css','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',232250,1,'tf','Team Fortress 2','game','srcds_run','orangebox','tf','100',12,'cp_5gorge','./%binary% -game tf -ip %ip% -port %port% -autoupdate +maxplayers %slots% +map %map% +fps_max %fps% +tv_port %tvport%',NULL,NULL,'a2s','N','css','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',90,1,'tfc','Team Fortress Classic','game','hlds_run',NULL,'tfc','100',12,'2fort','./%binary% -game tfc -ip %ip% -port %port% +sys_ticrate %tic% +maxplayers %slots% +map %map% +fps_max %fps%','tfc','100','hla2s','Y','css','server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',17505,1,'zps','Zombie Panic Source','game','srcds_run','orangebox','zps','100',12,'zpa_badbayou','./%binary% -game zps -ip %ip% -port %port% -autoupdate +maxplayers %slots% +map %map% +fps_max %fps%',NULL,'100','a2s','N','css','cfg/server.cfg both\r\nmaplist.txt full\r\nmapcycle.txt full',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,NULL),
('S',740,1,'csgo','Counter-Strike: Global Offensive','gserver','srcds_run',NULL,'csgo',NULL,0,'de_dust','./%binary% -game csgo -console -usercon +ip %ip% +port %port% -maxplayers_override %slots% +map %map%  +mapgroup %mapgroup%','[Classic Casual = default]\r\n+game_type 0 +game_mode 0\r\n\r\n[Classic Competitive]\r\n+game_type 0 +game_mode 1\r\n\r\n[Arms Race]\r\n+game_type 1 +game_mode 0\r\n\r\n[Demolition]\r\n+game_type 1 +game_mode 1',NULL,'a2s','N','css','cfg/server.cfg both\r\ncfg/autoexec.cfg both\r\ngamemodes.txt\r\ngamemodes_server.txt',NULL,'password:1',100,4,27015,27016,27017,27018,27019,0,'mg_bomb'),
('N',NULL,1,'mc','Minecraft','gserver','minecraft_server.jar',NULL,NULL,NULL,0,NULL,'java -Xmx%maxram%M -Xms%minram%M -XX:+UseConcMarkSweepGC -XX:+CMSIncrementalPacing -XX:ParallelGCThreads=%maxcores% -XX:+AggressiveOpts -jar %binary% nogui',NULL,NULL,'minecraft','N','css','server.properties','[server.properties] ini\r\nserver-port=%port%\r\nquery.port=%port%\r\nrcon.port=%port2%\r\nserver-ip=%ip%\r\nmax-players=%slots%',NULL,100,2,25565,25566,NULL,NULL,NULL,0,NULL),
('N',NULL,1,'bukkit','MC Bukkit','gserver','craftbukkit.jar',NULL,NULL,NULL,0,NULL,'java -Xincgc -Xmx%maxram%M -Xms%minram%M -jar %binary% -o true -h %ip% -p %port% -s %slots% --log-append false --log-limit 50000',NULL,NULL,'minecraft','N','','server.properties','[server.properties] ini\r\nserver-port=%port%\r\nquery.port=%port%\r\nrcon.port=%port2%\r\nserver-ip=%ip%\r\nmax-players=%slots%',NULL,100,2,25565,25566,NULL,NULL,NULL,0,NULL),
('N',NULL,1,'samp','GTA San Andreas','gserver','samp03svr',NULL,NULL,NULL,0,NULL,'./%binary%',NULL,NULL,'gtasamp','N','','server.cfg','[server.cfg] cfg\r\nmaxplayers %slots%\r\nport %port%','',10,1,7777,NULL,NULL,NULL,NULL,0,NULL),
('N',NULL,1,'mtasa','Multi Theft Auto San Andreas','gserver','mta-server',NULL,NULL,NULL,0,NULL,'./%binary%',NULL,NULL,'mtasa','N','','','[mods/deathmatch/mtaserver.conf] xml\r\n<serverip>%ip%</serverip>\r\n<serverport>%port%</serverport> \r\n<httpport>%port2%</httpport>\r\n<maxplayers>%slots%</maxplayers>\r\n<httpserver>0</httpserver>','',10,3,22003,22005,22126,NULL,NULL,0,NULL),
('N',NULL,1,'teeworlds','Teeworlds','gserver','teeworlds_srv',NULL,NULL,NULL,0,NULL,'./%binary%','[Capture the Flag = default]\r\n-f config_ctf.cfg\r\n\r\n[Deathmatch]\r\n-f config_dm.cfg\r\n\r\n[Team Deathmatch]\r\n-f config_tdm.cfg',NULL,'teeworlds','N','','config_ctf.cfg\r\nconfig_dm.cfg\r\nconfig_tdm.cfg', '[autoexec.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_ctf.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_dm.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_tdm.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%','',10,1,8303,NULL,NULL,NULL,NULL,0,NULL)
");
$query->execute();
$query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES
('em', 'de', 'emailbackup', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Sicherung des Servers</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>die Sicherung Ihres Servers</text1>\r\n	<text2>wurde um</text2>\r\n	<text3>fertig gestellt.</text3>\r\n	<text4>Ihr Server sollte weiterhin erreichbar sein.</text4>\r\n	<text5>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text5>\r\n	<text6>Die entsprechende Nummer finden Sie im Panel.</text6>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'de', 'emailbackuprestore', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Wiederherstellung Ihres Backups</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>das Backup Ihres Servers</text1>\r\n	<text2>wurde um</text2>\r\n	<text3>erfolgreich eingespielt.</text3>\r\n	<text4>Ihr Server sollte weiterhin erreichbar sein.</text4>\r\n	<text5>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text5>\r\n	<text6>Die entsprechende Nummer finden Sie im Panel.</text6>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'de', 'emaildown', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server nicht erreichbar</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ihr Servers</text1>\r\n	<text2>kann seit</text2>\r\n	<text3>nicht mehr erreicht werden.</text3>\r\n	<text4>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text4>\r\n	<text5>Die entsprechende Nummer finden Sie im Panel.</text5>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'de', 'emaildownrestart', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server abgest&uuml\;rzt</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ihr Servers</text1>\r\n	<text2>konnte am</text2>\r\n	<text3>nicht erreicht werden und wurde neu gstartet.</text3>\r\n	<text4>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text4>\r\n	<text5>Die entsprechende Nummer finden Sie im Panel.</text5>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'de', 'emailgserverupdate', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Update eines Masterservers</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ein Gameserverupdate wurde beim Server</text1>\r\n	<text2>um</text2>\r\n	<text3>f&uuml\;r das Spiel</text3> \r\n	<text4>fertig gestellt.</text4>\r\n	<text5>Ihre Server sollten weiterhin erreichbar sein.</text5>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'de', 'emailnewticket', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Ticket</topic>\r\n	<text1>schrieb am</text1>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'de', 'emailpwrecovery', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Passwort Recovery</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Um ein neues Passwort anzufordern, rufen Sie bitte folgenden Best&auml\;tigungslink auf:</text1>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'de', 'emailsecuritybreach', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Manipulation am Server entdeckt</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Am</text1>\r\n	<text2>wurde am Server</text2>\r\n	<text3>eine unzul&auml\;ssige Manipulation entdeckt:</text3> \r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'de', 'emailuseradd', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Zugangsdaten</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ihr Account wurde um</text1>\r\n	<text2>f&uuml\;r Sie bereit gestellt.</text2>\r\n	<text3>Ihre Zugangsdaten lauten wie folgt:</text3> \r\n	<text4>Benutzername:</text4>\r\n	<text5>Passwort:</text5>\r\n	<text6>Bitte speichern Sie die Zugangsdaten aus Sicherheitsgr&uuml\;nden in einer verschl&uuml\;sselten Datei und l&ouml\;schen diese Email danach.</text6>\r\n	<text7>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text7>\r\n	<text8>Die entsprechende Nummer finden Sie im Panel.</text8>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'de', 'emailvrescue', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Rescue System gestartet</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Das Rescue System ihres Servers mit der IP</text1>\r\n	<text2>wurde um</text2>\r\n	<text3>f&uuml\;r Sie gestartet.</text3> \r\n	<text4>Das Passwort f&uuml\;r den Root Account lautet:</text4>\r\n	<text5>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text5>\r\n	<text6>Die entsprechende Nummer finden Sie im Panel.</text6>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emailbackup', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Your Serverbackup</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>the backup for your server</text1>\r\n	<text2>was created at</text2>\r\n	<text3>.</text3>\r\n	<text4>Your server should be still available for access.</text4>\r\n	<text5>If you have any questions feel free to use our supportsystem or give us a call.</text5>\r\n	<text6>You will find the phonenumber in our panel.</text6>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emailbackuprestore', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Your Serverbackup</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>the backup for your server</text1>\r\n	<text2>was successfully restored</text2>\r\n	<text3>.</text3>\r\n	<text4>Your server should be still available for access.</text4>\r\n	<text5>If you have any questions feel free to use our supportsystem or give us a call.</text5>\r\n	<text6>You will find the phonenumber in our panel.</text6>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emaildown', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server could not been reached</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>Your server</text1>\r\n	<text2>could not been reached since</text2>\r\n	<text3>.</text3>\r\n	<text4>If you have any questions feel free to use our supportsystem or give us a call.</text4>\r\n	<text5>You will find the phonenumber in our panel.</text5>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emaildownrestart', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server crashed</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>Your server</text1>\r\n	<text2>could not been reached at</text2>\r\n	<text3>and was restarted.</text3>\r\n	<text4>If you have any questions feel free to use our supportsystem or give us a call.</text4>\r\n	<text5>You will find the phonenumber in our panel.</text5>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emailgserverupdate', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Masterservers has been updated</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>a gameserverupdate for the server</text1>\r\n	<text2>was applied at</text2>\r\n	<text3>for the game</text3> \r\n	<text4>.</text4>\r\n	<text5>Your server should be still available for access.</text5>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emailnewticket', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Ticket</topic>\r\n	<text1>wrote at</text1>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emailpwrecovery', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Password recovery</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>To require a new password please use following activation link:</text1>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emailsecuritybreach', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server manipulation detected</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>At</text1>\r\n	<text2>a illegal servermanipulation at the server</text2>\r\n	<text3>was detected:</text3> \r\n	<noreply>This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emailuseradd', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Zugangsdaten</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ihr Account wurde um</text1>\r\n	<text2>f&uuml\;r Sie bereit gestellt.</text2>\r\n	<text3>Ihre Zugangsdaten lauten wie folgt:</text3> \r\n	<text4>Benutzername:</text4>\r\n	<text5>Passwort:</text5>\r\n	<text6>Bitte speichern Sie die Zugangsdaten aus Sicherheitsgr&uuml\;nden in einer verschl&uuml\;sselten Datei und l&ouml\;schen diese Email danach.</text6>\r\n	<text7>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text7>\r\n	<text8>Die entsprechende Nummer finden Sie im Panel.</text8>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
('em', 'uk', 'emailvrescue', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Rescue system has been started</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>The rescue system for your server with the IP</text1>\r\n	<text2>was started at</text2>\r\n	<text3>.</text3> \r\n	<text4>The root password is:</text4>\r\n	<text5>If you have any questions feel free to use our supportsystem or give us a call.</text5>\r\n	<text6>You will find the phonenumber in our panel.</text6>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0)
");
$query->execute();
$query = $sql->prepare("INSERT INTO `lendsettings` (`resellerid`) VALUES (0)");
$query->execute();
$query = $sql->prepare("INSERT INTO `traffic_settings` (`type`) VALUES ('mysql')");
$query->execute();
$query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES ('4.11','','')");
$query->execute();
$query = $sql->prepare("INSERT INTO `voice_stats_settings` (`resellerid`) VALUES (0)");
$query->execute();
$query = $sql->prepare("INSERT INTO `page_settings` (`resellerid`) VALUES ('0')");
$query->execute();
$query = $sql->prepare("INSERT INTO `page_pages` (`authorid`,`type`) VALUES ('0','about')");
$query->execute();
$query = $sql->prepare("INSERT INTO `feeds_settings` (`resellerID`) VALUES (0)");
$query->execute();
$query = $sql->prepare("UPDATE `settings` SET `template`='default',`emailbackuprestore`=0x78dae555c18eda30103db3d2fe83eb2a3750425852aa3a482b0ad7ee811e7a1ce221b1eac4a96368e9d777e2042da0ad96760fabaabe789ee7cdb33d7a89c59b8f9f16eb2f0f4b56b852cfd9ed8d2810e4fcf666209c721ae78133b5ca021176b04d94e880f8ae1ee1b79ddaa77c612a87951bad0f3572967528e50e7fb8b0d5fdc0b2026c83b4f679bd1acd38c988b0df486c8c3cb04d9e196d6ccadfaefce04ce3d695607355a53ce28c8e7182baf0bb92ae209c1e170a5479e13c43abea2ba9c5d3647a1f73b6bfc0d0e3851f244f672518d1984c78777fd868641b63255a2fd96f378ea2806e895ad720a5aa729f6c715343d6e35661c084b33e18300a253b1e2f3e1123a9c7bb2fc6c97dbce273eab67ca6f0fdecac7239bd9b8ee3ab2a9fd9925164cf239adb5efc6957c62f684bf4b77d999d579295a265e20db7255fb22d649872b00af4b040bd47a73218eed14aa860e8a0302570d6a89fc4221f88c65953e5c7af80050d5a22d3e7d02744d8cacec5c686decb7e0a1ad03b074e998a4a7654534189c1f082d57a6efca8c9fc424cb304873d9c04a7ecbbe00989e91925b9a01c735882d21673b0b2f90da532166b7d783adbf5fc0a774557daeb9f3655f22ea27dbba66e8d71de12afde9bffe63724c2f6d1e85e91f6ddfa05e50fe105,`emaildown`=0x78dae555c18eda30103db3d2fe83eb2a375042d850aa3a482b0ad7ee811e7a1ce221b1eac4a96368e9d777e2042da0ad96760fabaabe789e67e6d97e7a89c59b8f9f16eb2f0f4b56b852cfd9ed8d2810e4fcf666209c721ae78133b5ca021176b04d94e880ea5d3dc26f3bb54ff9c2540e2b375a1f6ae42ceb50ca1dfe7061cbfb816505d80669edf37a359a71a21161bf91d81879609b3c33dad894bf5df9c199c6ad2bc1e6aa4a79c4191de30475e177255d41383d2e14a8f2c2f90aadaaafc41627d3e43ee66c7f81a1c70b3f889ece4a30a23199f0eefeb0d1c836c64ab49eb2df6e1c4501dd12b5ae414a55e53ed9e2a686acc72dc38009677d3060144a763c5e7c4246548f775f8ca7f7f18acf496df94ce3fbd959e732b94bc6f1559dcf6cc928b2e711cdad167faacaf805b2447fabcbecbc93ac142da7de705bf225db42862907ab400f0bd47b742a83e11ead840a860e0a5302678dfa4955e403d1386baafcf815b0a0414bc5f439f40911b6b473b1b1a1f7b29f8206f4ce8153a6a2961df554506230bca86a3d377ee4647e21a65982c31e4e82279aee8253945c941c735882d21673b0b2f94d49652cd6faf074b653f90a3f45571aea9fb6d1f45d44fb76a26e8d71de04afaecd7ff3e31161fb4c74ef46fb52fd023336dd2c,`emaildownrestart`=0x78dae555c18eda30103db3d2fe83eb2a375042d850aa3a482b0ad7ee811e7a1ce221b1eac4a96368e9d777e2042da0ad96760fabaabe789e67e6d97e7a89c59b8f9f16eb2f0f4b56b852cfd9ed8d2810e4fcf666209c721ae78133b5ca021176b04d94e880ea5d3dc26f3bb54ff9c2540e2b375a1f6ae42ceb50ca1dfe7061cbfb816505d80669edf37a359a71a21161bf91d81879609b3c33dad894bf5df9c199c6ad2bc1e6aa4a79c4191de30475e177255d41383d2e14a8f2c2f90aadaaafc41627d3e43ee66c7f81a1c70b3f889ece4a30a23199f0eefeb0d1c836c64ab49eb2df6e1c4501dd12b5ae414a55e53ed9e2a686acc72dc38009677d3060144a763c5e7c4246548f775f8ca7f7f18acf496df94ce3fbd959e732b94bc6f1559dcf6cc928b2e711cdad167faacaf805b2447fabcbecbc93ac142da7de705bf225db42862907ab400f0bd47b742a83e11ead840a860e0a5302678dfa4955e403d1386baafcf815b0a0414bc5f439f40911b6b473b1b1a1f7b29f8206f4ce8153a6a2961df554506230bca86a3d377ee4647e21a65982c31e4e82279aee8253945c941c735882d21673b0b2f94d49652cd6faf074b653f90a3f45571aea9fb6d1f45d44fb76a26e8d71de04afaecd7ff3e31161fb4c74ef46fb52fd023336dd2c,`emailgserverupdate`=0x78dae5554d8fda30103db3d2fe07d7556ea084b0a15475905614aeed811e7a1c92815875e2d41968e9afefc409da05adb4b47b5855f5c5f3e6e3d99e3cc7eacdc74f8bf5d7cf4b515069e6e2f6461508f9fcf666a04893c17940b6d659a0c20eb6811209389fea117edfeb432a17b622ac68b43ed62845d6a15412fea4b0e5fd20b2025c83ecfbb25e8d66926954d82fa436363f8acd2eb3c6ba54be5df92185c12d95e076ba4a6524056fe311eacc1f3aa782717a7214a87705f90ca3ab6fcc1627d3e43e96e27081a1c70b3f989ef7ca30e23199c8eefcb0312836d6e5e83c65bfdc388a023e251a53439eeb6ae7832d6e6ac87adc320c8422e78d81603317a7edc58fc898eae1ec8bf1f43e5ec939773b7fa6f0fdecac7299dc25e3f8aaca6796146cb9738be7b6177fda95f10bda12fd6d5f66e7952ca56839f582dbb22ec516324c25380d6658a03920e90c86077439543024286c095234fa1767b10e5443ce56bbd32d1041838e93f93af40115b6b473b571a1d7b29f8206cc9e80b4adb864cf351594180c2fb25acd8d1f388577c43ce740d8c3491b2face31bd57bee822768920be729565987b5393e1dedda788560a22b15f34feb64fa2ee275032c419badb5e4bff2abf7e6bff9b3a8b07d07ba87a17d8a7e038c3ed52f,`emailpwrecovery`=0x78dae5554d8fd330103d77a5fd0fc628b756f9585a8a7022ad4a7b854339709c24d3c4c2b183e316caaf679ca4da6db5d276e18010be789ec7f366fc3c71c4ab0f1f57db2f9fd6ac768dcad8ed8da811caecf666229c740ab3c0995616810807e81d0d3aa0fdae9de1b7bd3ca47c65b443ed66db638b9c15034ab9c31f2ef4bcef595183ed90d63e6f37b325271a118e89446eca23cbabc2286353fe7ad30fce14ee5c03b6923ae5116754c6233498df65e96ac2e969a14659d5aedfa1a4fe4a6cc97c31bf4f383b5c6018f1aa1f444fb5128c68dcddf1e1fc902b64b9b125da9e724c17475140a744a55a284ba9abdee971d7423162cf3061c2d9de9830324b762a2f794446540f675fc58bfb64c33352bb7c26f0ddf22c723d7f338f93ab229f49c9c8b2e716cd5e8b97aa12ff812cd1efeab23c8fa4568ad68bbee176d4976c0705a61cac0435ad511dd0c902a607b42568983aa84d039c75f227eda23e109db346570f5fc18845e8d93291dbb06fe17e0a3a507b074e1a1db060dfa1d5d06030bdd8e55b2d0e2e43d1521117ab27a736165b757cda3b8871c5b54757defb3f7ddb8bb711e50db001a976c638afe9dfd7e6bf791f44e85ff3e179f73f945f1b1bc32f,`emailnewticket`=0x78dae5554d8fd330103d77a5fd0fc628b756f9585a0a389156a5bdc2a11c383af134b170ece0780be5d7337652ed662f5bd8c30ae1cbccb3dfbcb1c71387bdfaf869b3fffa794b1ad7aa825c5fb106b828aeaf66cc49a7a0889ce96415b178807ea105c791efba057cbf93c79c6e8c76a0dd627fea80926a403975f0d3c55ef703a91a6e7bc0b92ffbdd624d5186c56322561a7122655d19656c4e5fefc2a044c1c1b5dcd652e734a104b7f1000dee0f295c83383f4f3420ebc6058692fa1baa65cbd5f236a3e4f808f3116fc24079dc2bc204c7cd0d1dcecf4b05a43456800d9263ba3449223c2528d57121a4aec3a2c77dc7ab117b851961ce066746d015e4bcbdec81184add9f7d93ae6eb31d2db0dae289c077eb49e476f966996617453e9192a067a71e5a5f8b3fad4afa8cb2247f5b97f534125b29d9ae42c31db02fc9815790536e2557f306d4119cacf8fc085670cde78e37a6e594f4f217b2421fb0de59a3ebfbef60c42cf67a052b6d1c9a3898e8ae07ab790b11897c47a568057710bd3ff39a091dd9983a9a8a4c3967aa36163a757acc1dcc50900bae3eb9f0eeffe91b5fbd4d306f042d97ea608cf3157ef9dafc376f048bfd8b3e3cf1fea7f21bb114c26e,`emailuseradd`=0x78dae5554d8fda30103db3d2fe07d7556ea084b0b05475905614aedd033df438c44362d58953c7d0d25fdf8913ba409196dd1eaaaabe78de7c3cdb93e758bcf9f071befafcb860b92bf48cddde881c41ce6e6f7ac229a771163853a93410610b9b40810e28df5503fcba55bb84cf4de9b07483d5be42ced21625dce1771736bcef599a83ad917c9f56cbc194138d08bb85c4dac83d5b67a9d1c626fcedd20fce346e5c01365365c223ce681b47a835bf29e972c2c9c191a3ca72e733b42abf105b3c9e8c1f62ce7667183a3cf783e869af04231aa3116fcf0f6b8d6c6dac44eb29bbe5865114d02951eb0aa45465e6830dae2b483bdc30f49870d61b3d46a66487edc5476444f574f6f970f2102ff98cba2d9f297c373da95c8cefc6c3f8aaca67966464d9538be6a6172fedcaf00fda12bdb62fd3d34a9252b49878c16d48976c0329261cac02ddcf51efd0a914fa3bb4124ae83bc84d019cd5ea0765910e44edac29b3c32d60418d9692e93a74011136b433b1b6a1d7b29f821af4d68153a6a4922dd5945060d03fcb6a34377ce264de11d32cc1610747c185a2bba38dfc1e1d37d1dc58ba8497c2934bcefb732716a0b4c50cacac835fee43b434162bbd3f2beaa6f6eb5ca1c3e84a21fed3f29bdc47b46edbcf8d31ce8be7aff7e6bff96189b0795edaf7a679e17e027ae3ecf8,`emailvinstall`=0x78dae555c18eda30103db3d2fe83eb2a37504258b25475905614aeed811e7a1ce221b1eac4a96368e9d777e2042d6c77b5ecf65055f5c57e9e79cff6e439166f3e7c5cacbf7c5ab2c2957aceaeaf448120e7d75703e194d3380f9ca9551688b0836da0440794efea117edba97dca17a67258b9d1fa502367598752eef0870b5bddf72c2bc03648739fd7abd18c938c08fb85c4c6c803dbe499d1c6a6fcedca37ce346e5d09365755ca23ce681b27a81b7e57d21584d3e344812a2f9ccfd0aafa4a6af13499dec59ced1f60e8f1c23792a7bd128ca84d26bc3b3f6c34b28db112ad97ec971b475140a744ad6b905255b90fb6b8a921eb71ab3060c2593f18301a4a76dc5e7c224652f7675f8c93bb78c5e7546df90cf1ddec8cb99cde4cc7f145cc67966434b2e723eadb5abcb42ae33f284bf4dabaccce9964a5689978c36dc9976c0b19a61cac023d2c50efd1a90c867bb4122a183a284c099c35ea2765910f44e3aca9f2e32d6041839692e93af40111b6b273b1b1a1f7b2ef8206f4ce8153a622ca8e381594180c1f64b59e1bdf6b323f11532fc1610f27c139e9947bd3720b63e9b6058f84a74f864fb392809dc2db2752b104a52de6606513fc16ad8cc55a1f1ee7765feb025f46171af39fb663721bd1ba5d3db7c6386fa6bf5e9bffe60726c2f6b9e9de9ff6c5fb05d10cf0ac,`emailvrescue`=0x78dae5554d8fd330103d77a5fd0fc628b75649d34d29c289b42aed150ee5c0711a4f130b270e8e5b28bf9e89936adbe5630b1c10c217cfb3e73ddb93e7583c7bfd66b979ff76c54a57e98cddde88124166b73723e194d39805ce342a0f44d8c36ea2420794ef9a097edcab43ca97a67658bbc9e6d82067798f52eef0b30b3bdd572c2fc1b64863ef36ebc982938c088785c4d6c823db16b9d1c6a6fcf9da37ce34ee5c05b65075ca23ce681b67a80f3f29e94ac2e969a0445594ce6768557f20b5389927f7316787471806bcf48de469af04236ab319efcf0f5b8d6c6bac44eb2587e5a65114d02951eb06a45475e1273bdc36900fb8531831e1ac0f468c42c94edb8bcfc448eae1eccbe9fc3e5ef38caa2d9f20be5c5c3057c95d328daf623eb124a3c85e46d477b5f8d5aa4cffa02cd1efd66571c9242b45abb937dc8e7cc9769063cac12ad0e312f5019dca617c402ba186b183d254c059abbe5016f940b4ce9aba38dd0216b4682999aec33021c24e36135b1b7a2ffb2e6841ef1d38656aa2ec89534385c1f85156e7b9e98326f30331f5121c0e70165c92ceb9771db734966edb4fb292809dc3f90f52b102a52d1660651b7c335b1b8b8d3e7e9fdb7f8e2b8c175de9bc7fda6ff31711addbd773678cf36ef9ebb5f96ffe5022ecde93fe81e99eb4af0a37e7d1,`emailsecuritybreach`=0x78dae5554d8fd330103d77a5fd0fc628b75649535a8a7022ad4a7b650fe5c071924c130b270eceb4507e3d938f6a37bd6c810b085f669e3df3c69e3cc7ead5878f9bfde7c7ad28a834b1b8bf53054216dfdf4d146932187b646b9d7acaef61bb502201c7533dc3af477d8ae4c6568415cdf6e71aa5487b1449c2efe4b7bcef455a806b90e73eed77b3b5641ae50f855462b3b348f2d41aeb22f97ad70d290c1ea80497eb2a928114bc8d67a877bfe98c0ac6d165a2409d17d445185d7d61b670b95a3e84529cae300c78d30da6e7bd320c782c16b23f3f240645625d86aea31ccacd83c0e353a231356499aef26eb1c54d0de9805b868950e43a6722d8cdc4657be13332a67a3afb66be7a087732e66e672f24be5b8f32b7cb37cb797853e60b25057b6eecb16d7bf1ab5d99ff415b82dfedcb7a9cc9520ab6ab4e7007d6a538408a9104a7c14c0b3427249dc2f4842e830aa604852d418a46ffe0a84e07aa2167abfce91e0c58f92d5fac12e77722ee8cd780391290b69527bc6383ae8212bde955542bb639076440c8a685215b0ee78d0c130bef92548c2b14d6f1f5f2c694e3a04b6c651dd6e67c1ddb9bbe8937c825b8512fffb44a566f03aeeb6109da1cac25fe0e7f416ffe9bff8af2db57a07f16da87e827795bd246");
$query->execute();
$query = $sql->prepare("INSERT INTO `resellerimages` (`distro`, `description`, `bitversion`, `pxelinux`) VALUES
('other', 'Rescue 64bit', 64, 'DISPLAY boot.txt\r\nDEFAULT rescue\r\nTIMEOUT 10\r\n\r\nLABEL default\r\n        kernel /rescue/vmlinuz-rescue\r\n        append initrd=/rescue/initram.igz setkmap=de dodhcp rootpass=%rescuepass% scandelay=5 boothttp=http://1.1.1.1/rescue/64/sysrcd.dat'),
('other', 'Rescue 32bit', 32, 'DISPLAY boot.txt\r\nDEFAULT rescue\r\nTIMEOUT 10\r\n\r\nLABEL default\r\n        kernel /rescue/vmlinuz-rescue\r\n        append initrd=/rescue/initram.igz setkmap=de dodhcp rootpass=%rescuepass% scandelay=5 boothttp=http://1.1.1.1/rescue/32/sysrcd.dat')");
$query->execute();
?>
    <ul id="leftmenu">
        <li>
            <ul>
                <li><?php echo $sprache->step1a;?></li>
                <li><?php echo $sprache->step2a;?></li>
                <li><div class="error"><?php echo $sprache->step3a;?></div></li>
            </ul>
        </li>
    </ul>
<div id="datapage">
    <form action="install.php" method="post">
        <table class="usertables">
            <tr>
                <th colspan="2"><?php echo $sprache->step3a.": ".$sprache->step3b;?></th>
            </tr>
            <tr>
                <td colspan="2"><?php echo $sprache->finished;?></td>
            </tr>
            <?php
            } else {
                echo "Error".$emessage;
            }
            } else {
            ?>
            <ul id="leftmenu">
                <li>
                    <ul>
                        <li><div class="error"><?php echo $sprache->step1a;?></div></li>
                        <li><?php echo $sprache->step2a;?></li>
                        <li><?php echo $sprache->step3a;?></li>
                    </ul>
                </li>
            </ul>
            <div id="datapage">
                <form action="install.php" method="post">
                    <table class="usertables">
                        <tr>
                            <th colspan="2"><?php echo $sprache->step1a.": ".$sprache->step1b;?></th>
                        </tr>
                        <?php
                        if (is_dir("../stuff")){
                            if (!is_writable("../stuff")) {
                                $error = 1;
                                ?>
                                <tr>
                                    <td colspan="2"><?php echo $sprache->error_stuff1;?></td>
                                </tr>
                            <?php
                            }
                        } else {
                            $error = 1;
                            ?>
                            <tr>
                                <td colspan="2"><?php echo $sprache->error_stuff2;?></td>
                            </tr>
                        <?php
                        }
                        if (!is_dir("../keys")){
                            mkdir("../keys", 0700);
                        }
                        if (is_dir("../keys")){
                            if (!is_readable("../keys")) {
                                $error = 1;
                                ?>
                                <tr>
                                    <td colspan="2"><?php echo $sprache->error_keys1;?></td>
                                </tr>
                            <?php
                            }
                        } else {
                            $error = 1;
                            ?>
                            <tr>
                                <td colspan="2"><?php echo $sprache->error_keys2;?></td>
                            </tr>
                        <?php
                        }
                        if (! function_exists('ssh2_connect') or ! function_exists('ssh2_exec')) {
                            $error = 1;
                            ?>
                            <tr>
                                <td colspan="2"><?php echo $sprache->error_ssh2;?></td>
                            </tr>
                        <?php
                        }
                        if (!file_exists("../.htaccess")){
                            ?>
                            <tr>
                                <td colspan="2"><?php echo $sprache->error_htaccess;?></td>
                            </tr>
                        <?php
                        }
                        if (!isset($error)) {
                            $zeichen = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                                'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                                '1','2','3','4','5','6','7','8','9');
                            $anzahl=count($zeichen);
                            $anzahlcorrect = $anzahl-1;
                            $randompass = '';
                            for($i = 1; $i<=30; $i++){
                                $wuerfeln = mt_rand(0,$anzahlcorrect);
                                $randompass .= $zeichen[$wuerfeln];
                            }
                            ?>
                            <tr>
                                <td colspan="2"><br /><?php echo $sprache->data;?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php echo $sprache->aeskey2;?><br /><?php echo $sprache->aeskey3;?></td>
                            </tr>
                            <tr>
                                <td><?php echo $sprache->aeskey;?></td>
                                <td><input type="text" name="aeskey" size="50" value="<?php echo $randompass;?>" required="required"/></td>
                            </tr>
                            <tr>
                                <td><?php echo $sprache->host;?></td>
                                <td><input type="text" name="host" size="50" value="localhost" required="required"/></td>
                            </tr>
                            <tr>
                                <td><?php echo $sprache->db;?></td>
                                <td><input type="text" name="db" size="50" value="" required="required"/></td>
                            </tr>
                            <tr>
                                <td><?php echo $sprache->user;?></td>
                                <td><input type="text" name="user" size="50" value="" required="required"/></td>
                            </tr>
                            <tr>
                                <td><?php echo $sprache->pwd;?></td>
                                <td><input type="text" name="pwd" size="50" value="" required="required"/></td>
                            </tr>
                            <tr>
                                <td><?php echo $sprache->databanktype;?></td>
                                <td><input type="text" name="databanktype" size="50" value="mysql" required="required"/></td>
                            </tr>
                            <tr>
                                <td><?php echo $sprache->title;?></td>
                                <td><input type="text" name="title" size="50" value="My Homepagename" required="required"/></td>
                            </tr>
                            <tr>
                                <td><?php echo $sprache->captcha;?></td>
                                <td>
                                    <select name="captcha">
                                        <option>0</option>
                                        <option>1</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="right">
                                    <input type="submit" value="<?php echo $sprache->step2a;?>">
                                </td>
                            </tr>
                        <?php
                        }
                        }
                        ?>
                    </table>
                </form>
            </div>
</div>
<div id="spacer"></div>
<div id="footer">
    <div class="blueline"></div>
    <a href="http://easy-wi.com" target="_blank">easy-wi.com</a>
</div>
</div>
</body>
</html>
<?php
?>
