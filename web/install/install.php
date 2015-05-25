<?php

/**
 * File: install.php.
 * Author: Ulrich Block
 * Date: 07.12.13
 * Time: 10:30
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

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
define('EASYWIDIR', dirname(dirname(__FILE__)));

require_once(EASYWIDIR . '/stuff/methods/functions.php');
require_once(EASYWIDIR . '/stuff/methods/vorlage.php');

$currentStep = (isset($_GET['step']) and $_GET['step'] > 0 and $_GET['step'] < 10) ? (int) $_GET['step'] : 0;
$progressPercent = (100 / 9) * $currentStep ;
$acceptLanguage = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0 , 2));
$fallbackLanguage = (file_exists(EASYWIDIR . '/install/' . $acceptLanguage . '.xml')) ? $acceptLanguage : 'en';
$menuLanguage = (isset($_GET['language']) and strlen($_GET['language']) == 2 and file_exists(EASYWIDIR . '/install/' . $_GET['language'] . '.xml')) ? $_GET['language'] : $fallbackLanguage;
$languageGetParameter = '&amp;language=' . $menuLanguage;
$languageObject = simplexml_load_file(EASYWIDIR . '/install/' . $menuLanguage . '.xml');

$displayToUser = '';
$systemCheckOk = array();
$systemCheckError = array();

if ($currentStep == 0) {

    $licencecode = webhostRequest('l.easy-wi.com', $_SERVER['HTTP_HOST'], '/version.php', null, 80);
    $licencecode = cleanFsockOpenRequest($licencecode, '{', '}');
    $json = @json_decode($licencecode);

    if (!$json or $json->v >= '5.00') {
        $displayToUser = "<div class='jumbotron'><h2>{$languageObject->welcome_header}</h2><p>{$languageObject->welcome_text}</p><div class='pager'><a href='?step=1${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div></div>";
    } else {
        $displayToUser = "<div class='alert alert-warning'><i class='fa fa-exclamation-triangle'></i> {$languageObject->welcome_old_version}<a href='https://easy-wi.com/uk/downloads/' target='_blank'>{$json->v}</a></div><div class='jumbotron'><h2>{$languageObject->welcome_header}</h2><p>{$languageObject->welcome_text}</p><div class='pager'><a href='?step=1${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div></div>";
    }

} else {

    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
        $systemCheckOk['php'] = $languageObject->system_ok_php_version . PHP_VERSION;
    } else {
        $systemCheckError['php'] = $languageObject->error_system_php_version . PHP_VERSION;
    }

    if (extension_loaded('openssl')) {
        $systemCheckOk['openssl'] = $languageObject->system_ok_openssl;
    } else {
        $systemCheckError['openssl'] = $languageObject->error_system_openssl;
    }

    if (extension_loaded('json')) {
        $systemCheckOk['json'] = $languageObject->system_ok_json;
    } else {
        $systemCheckError['json'] = $languageObject->error_system_json;
    }

    if (extension_loaded('hash')) {
        $systemCheckOk['hash'] = $languageObject->system_ok_hash;
    } else {
        $systemCheckError['hash'] = $languageObject->error_system_hash;
    }

    if (extension_loaded('ftp')) {
        $systemCheckOk['ftp'] = $languageObject->system_ok_ftp;
    } else {
        $systemCheckError['ftp'] = $languageObject->error_system_ftp;
    }

    if (extension_loaded('SimpleXML')) {
        $systemCheckOk['SimpleXML'] = $languageObject->system_ok_SimpleXML;
    } else {
        $systemCheckError['SimpleXML'] = $languageObject->error_system_SimpleXML;
    }

    if (extension_loaded('curl')) {
        $systemCheckOk['curl'] = $languageObject->system_ok_curl;
    } else {
        $systemCheckError['curl'] = $languageObject->error_system_curl;
    }

    if (extension_loaded('gd')) {
        $systemCheckOk['gd'] = $languageObject->system_ok_gd;
    } else {
        $systemCheckError['gd'] = $languageObject->error_system_gd;
    }

    if (extension_loaded('PDO')) {
        $systemCheckOk['PDO'] = $languageObject->system_ok_PDO;
    } else {
        $systemCheckError['PDO'] = $languageObject->error_system_PDO;
    }

    if (extension_loaded('pdo_mysql')) {
        $systemCheckOk['pdo_mysql'] = $languageObject->system_ok_pdo_mysql;
    } else {
        $systemCheckError['pdo_mysql'] = $languageObject->error_system_pdo_mysql;
    }

    if (function_exists('fopen')) {
        $systemCheckOk['fopen'] = $languageObject->system_ok_fopen;
    } else {
        $systemCheckError['fopen'] = $languageObject->error_system_fopen;
    }

    $folderArray = array(
        'css/',
        'css/default/',
        'images/',
        'images/flags',
        'images/games/',
        'images/games/icons/',
        'js/',
        'js/default/',
        'keys/',
        'languages/',
        'languages/default/',
        'languages/default/de/',
        'languages/default/dk/',
        'languages/default/uk',
        'stuff/',
        'stuff/admin/',
        'stuff/api/',
        'stuff/cms/',
        'stuff/custom_modules/',
        'stuff/jobs/',
        'stuff/methods/',
        'stuff/user/',
        'template/',
        'template/default/',
        'third_party/',
        'tmp/'
    );

    foreach ($folderArray as $folder) {
        if (is_dir(EASYWIDIR . "/${folder}")) {
            $handle = @fopen(EASYWIDIR . "/${folder}test.txt", "w+");

            if ($handle) {
                fclose($handle);
                unlink(EASYWIDIR . "/${folder}test.txt");
                $systemCheckOk['folders'][] = "Folder exists and can write to: ${folder}";

            } else {
                $systemCheckError['folders'][] = "Folder exists but cannot edit files: ${folder}";
            }
        } else {
            $systemCheckError['folders'][] = "Folder does not exist or cannot access: ${folder}";
        }
    }

}

if ($currentStep == 1) {

    if (count($systemCheckError) == 0) {
        $displayToUser .= "<div class='pager'><a href='?step=2${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";
    }

    foreach ($systemCheckError as $v) {
        if (is_array($v)) {
            foreach ($v as $v2) {
                $displayToUser .= "<div class='alert alert-danger'>${v2}</div>";
            }
        } else {
            $displayToUser .= "<div class='alert alert-danger'>${v}</div>";
        }
    }

    foreach ($systemCheckOk as $v) {
        if (is_array($v)) {
            foreach ($v as $v2) {
                $displayToUser .= "<div class='alert alert-success'>${v2}</div>";
            }
        } else {
            $displayToUser .= "<div class='alert alert-success'>${v}</div>";
        }
    }

    if (count($systemCheckError) == 0) {
        $displayToUser .= "<div class='pager'><a href='?step=2${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";
    }

} else if (count($systemCheckError) > 0) {

}

if ($currentStep == 2 and count($systemCheckError) == 0) {

    $host = '';
    $db = '';
    $user = '';
    $pwd = '';
    $aeskey = passwordgenerate(20);

    if (file_exists(EASYWIDIR . '/stuff/config.php')) {
        require_once(EASYWIDIR . '/stuff/config.php');
    }
    if (file_exists(EASYWIDIR . '/stuff/keyphrasefile.php')) {
        require_once(EASYWIDIR . '/stuff/keyphrasefile.php');
    }

    $displayToUser = "
<form class='form-horizontal' role='form' action='install.php?step=3${languageGetParameter}' method='post'>
  <div class='form-group'>
    <label for='inputHost' class='col-sm-2 control-label'>{$languageObject->host}</label>
    <div class='col-sm-10'>
      <input type='text' class='form-control' id='inputHost' name='host' value='${host}' required>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputDB' class='col-sm-2 control-label'>{$languageObject->db}</label>
    <div class='col-sm-10'>
      <input type='text' class='form-control' id='inputDB' name='db' value='${db}' required>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputUser' class='col-sm-2 control-label'>{$languageObject->user}</label>
    <div class='col-sm-10'>
      <input type='text' class='form-control' id='inputUser' name='user' value='${user}' required>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputPassword' class='col-sm-2 control-label'>{$languageObject->passw_1}</label>
    <div class='col-sm-10'>
      <input type='password' class='form-control' id='inputPassword' name='pwd' value='${pwd}' required>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputAESKey' class='col-sm-2 control-label'>{$languageObject->aeskey}</label>
    <div class='col-sm-10'>
      <input type='text' class='form-control' id='inputAESKey' name='aeskey' value='${aeskey}' required>
      <p class='help-block'>{$languageObject->aeskey2}</p>
      <p class='help-block'>{$languageObject->aeskey3}</p>
    </div>
  </div>
  <div class='form-group'>
    <div class='col-sm-offset-2 col-sm-10'>
      <button type='submit' class='btn btn-primary btn-lg pull-right'>{$languageObject->continue}</button>
    </div>
  </div>
</form>
";

} else if ($currentStep > 2 and count($systemCheckError) == 0) {

    if ($currentStep == 3 and isset($_POST['db'])) {

        try {

            $sql = new PDO("mysql:host=${_POST['host']};dbname=${_POST['db']}", $_POST['user'], $_POST['pwd'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $configFp = @fopen(EASYWIDIR . '/stuff/config.php', "w+");

            if ($configFp) {

                $configdata = "<?php

// This file was generated by the easy-wi.com installer

" . '$host' . " = '" . $_POST['host'] . "';
" . '$user' . " = '" . $_POST['user'] . "';
" . '$db' . " = '" . $_POST['db'] . "';
" . '$pwd' . " = '" . $_POST['pwd'] . "';
" . '$captcha' . " = 0;
" . '$title' . " = '';
" . '$debug' . " = 0;
" . '$timezone' . " = 'Europe/Berlin';
";

                @fwrite($configFp, $configdata);

                fclose($configFp);

            }

            $keyFp = @fopen(EASYWIDIR . '/stuff/keyphrasefile.php', "w+");

            if ($keyFp) {
                $configdata='<?php

// This file was generated by the easy-wi.com installer

$aeskey = "' . $_POST['aeskey'] . '";
';

                @fwrite($keyFp, $configdata);

                fclose($keyFp);
            }

        } catch(PDOException $error) {

            $systemCheckError['config.php'] = "<div class='alert alert-danger'>{$error->getMessage()}</div>";

        }

    }

    if (!isset($sql) ) {

        if (file_exists(EASYWIDIR . '/stuff/config.php') and file_exists(EASYWIDIR . '/stuff/keyphrasefile.php')) {

            if ($currentStep == 3 and isset($_POST['db'])) {
                $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_files_created}</div>";
            }

            require_once(EASYWIDIR . '/stuff/config.php');
            require_once(EASYWIDIR . '/stuff/keyphrasefile.php');

            if (isset($host) and isset($db) and isset($user) and isset($pwd)) {

                try {

                    $sql = new PDO("mysql:host=${host};dbname=${db}", $user, $pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                    $sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                } catch(PDOException $error) {

                    $systemCheckError['config.php'] = "<div class='alert alert-danger'>{$error->getMessage()}</div>";

                }

            } else {
                $systemCheckError['config.php'] = "<div class='alert alert-danger'>{$languageObject->error_config_php_data}</div>";
            }

        } else {

            if (isset($_POST['db'])) {
                $systemCheckError['config.php'] = "<div class='alert alert-danger'>{$languageObject->error_keyphrase_php_create}</div>";
            } else {
                $systemCheckError['config.php'] = "<div class='alert alert-danger'>{$languageObject->error_config_keyphrase_php_missing}</div>";
            }
        }
    }
}

if ($currentStep == 3 and count($systemCheckError) == 0) {
    $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_files_created}</div>";
    $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_db_connect}</div>";
    $displayToUser .= "<div class='pager'><a href='?step=4${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";
}

if ($currentStep == 4 and count($systemCheckError) == 0) {

    try {

        require_once(EASYWIDIR . '/stuff/methods/tables_add.php');

        $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_db_tables_create}</div>";
        $displayToUser .= "<div class='pager'><a href='?step=5${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";

    } catch(PDOException $error) {

        $systemCheckError['tables_add.php'] = "<div class='alert alert-danger'>{$error->getMessage()}</div>";

    }
}

if ($currentStep == 5 and count($systemCheckError) == 0) {

    try {

        class UpdateResponse {
            public $response = '';
            function __construct() {
                $this->response = '';
            }
            function add ($newtext) {
                $this->response .= $newtext;
            }
            function __destruct() {
                unset($this->response);
            }
        }

        $response = new UpdateResponse();

        require_once(EASYWIDIR . '/stuff/methods/tables_repair.php');

        if (strpos($response->response, 'Error: no such table:') !== false) {

            $systemCheckError['tables_repair.php'] = "<div class='alert alert-danger'>{$response->response}</div>";

        } else {

            if (strlen($response->response) > 0) {

                $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_db_tables_check_repair}</div>";
                $displayToUser .= "<div class='alert alert-success'>{$response->response}</div>";

            } else {
                $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_db_tables_check}</div>";
            }

            $displayToUser .= "<div class='pager'><a href='?step=6${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";

        }

    } catch(PDOException $error) {

        $systemCheckError['tables_repair.php'] = "<div class='alert alert-danger'>{$error->getMessage()}</div>";

    }

}

if ($currentStep == 6 and count($systemCheckError) == 0) {

    $cname = '';
    $email = '';

    $query = $sql->prepare("SELECT `cname`,`mail` FROM `userdata` WHERE `id`=1");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $cname = $row['cname'];
        $email = $row['mail'];
    }

    if (isset($_POST['passw1'])) {

        if ($_POST['passw1'] != $_POST['passw2']) {
            $displayToUser .= "<div class='alert alert-danger'>{$languageObject->error_password}</div>";
        }

        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $email = $_POST['email'];
        } else {
            $displayToUser .= "<div class='alert alert-danger'>{$languageObject->error_email}</div>";
        }

        if (strlen($_POST['cname']) == 0) {
            $displayToUser .= "<div class='alert alert-danger'>{$languageObject->error_cname}</div>";
        } else {
            $cname = $_POST['cname'];
        }
    }

    if (!isset($_POST['passw1']) or strlen($displayToUser) > 0) {

        $displayToUser .= "
<form class='form-horizontal' role='form' action='install.php?step=6${languageGetParameter}' method='post'>
  <div class='form-group'>
    <label for='inputUser' class='col-sm-2 control-label'>{$languageObject->user2}</label>
    <div class='col-sm-10'>
      <input type='text' class='form-control' id='inputUser' name='cname' value='${cname}' required>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputEmail' class='col-sm-2 control-label'>{$languageObject->email}</label>
    <div class='col-sm-10'>
      <input type='email' class='form-control' id='inputEmail' name='email' value='${email}' required>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputPassword1' class='col-sm-2 control-label'>{$languageObject->passw_1}</label>
    <div class='col-sm-10'>
      <input type='password' class='form-control' id='inputPassword1' name='passw1' value='' required>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputPassword2' class='col-sm-2 control-label'>{$languageObject->passw_2}</label>
    <div class='col-sm-10'>
      <input type='password' class='form-control' id='inputPassword2' name='passw2' value='' required>
    </div>
  </div>
  <div class='form-group'>
    <div class='col-sm-offset-2 col-sm-10'>
      <button type='submit' class='btn btn-primary btn-lg pull-right'>{$languageObject->continue}</button>
    </div>
  </div>
</form>
";

    } else {

        try {

            $query = $sql->prepare("INSERT INTO `userdata` (`id`,`cname`,`mail`,`security`,`accounttype`,`creationTime`,`updateTime`) VALUES (1,?,?,?,'a',NOW(),NOW()) ON DUPLICATE KEY UPDATE `cname`=VALUES(`cname`),`mail`=VALUES(`mail`)");
            $query->execute(array($cname, $email, md5($_POST['passw1'])));

            $query = $sql->prepare("INSERT INTO `usergroups` (`id`,`defaultgroup`,`name`,`grouptype`,`root`,`miniroot`) VALUES (1,'Y','Admin Default','a','Y','N') ON DUPLICATE KEY UPDATE `id`=VALUES(`id`)");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `usergroups` (`id`,`defaultgroup`,`name`,`grouptype`,`root`,`miniroot`) VALUES (2,'Y','Reseller Default','r','Y','N') ON DUPLICATE KEY UPDATE `id`=VALUES(`id`)");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `usergroups` (`id`,`defaultgroup`,`name`,`grouptype`,`root`,`miniroot`) VALUES (3,'Y','User Default','u','N','Y') ON DUPLICATE KEY UPDATE `id`=VALUES(`id`)");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`) VALUES (1,1) ON DUPLICATE KEY UPDATE `userID`=VALUES(`userID`)");
            $query->execute();

            $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_admin_user}</div>";
            $displayToUser .= "<div class='pager'><a href='?step=7${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";

        } catch(PDOException $error) {

            $systemCheckError['tables_repair.php'] = "<div class='alert alert-danger'>{$error->getMessage()}</div>";

        }

    }
}

if ($currentStep == 7 and count($systemCheckError) == 0) {

    $languages = array();
    $language = '';
    $prefix1 = "Y";
    $prefix2 = "user";
    $faillogins = 5;
    $brandname = "by myhost.com";

    $selectedCaptcha = '';
    $selectedPrefix = '';

    $query = $sql->prepare("SELECT `pageurl` FROM `page_settings` WHERE `resellerid`=0");
    $query->execute();
    $installUrl = (string) $query->fetchColumn();

    if (strlen($installUrl) == 0) {
        $installUrl = 'http://' . $_SERVER['SERVER_NAME'] . '/' . str_replace('install/install.php', '', $_SERVER['SCRIPT_NAME']);
    }


    while (substr($installUrl, -2) == '//') {
        $installUrl = substr($installUrl, 0, strlen($installUrl) -1 );
    }


    $query = $sql->prepare("SELECT `mail` FROM `userdata` WHERE `id`=1");
    $query->execute();
    $email = $query->fetchColumn();

    $query = $sql->prepare("SELECT `language`,`email`,`prefix1`,`prefix2`,`faillogins`,`brandname` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $language = $row['language'];
        $email = $row['email'];
        $prefix1 = $row['prefix1'];
        $prefix2 = $row['prefix2'];
        $faillogins = $row['faillogins'];
        $brandname = $row['brandname'];
    }

    if (isset($_POST['email'])) {

        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $email = $_POST['email'];
        } else {
            $displayToUser .= "<div class='alert alert-danger'>{$languageObject->error_email}</div>";
        }

    }

    if ($captcha == 1) {
        $selectedCaptcha = 'selected="selected"';
    }

    if ($prefix1 == 'Y') {
        $selectedPrefix = 'selected="selected"';
    }

    if (is_dir(EASYWIDIR . "/languages/default/")){
        $dirs = scandir(EASYWIDIR . "/languages/default/");
        foreach ($dirs as $row) {
            if (preg_match("/^[a-z]{2}+$/", $row)) {
                $languages[] = ($row == $menuLanguage) ? "<option value='${row}' selected='selected'>$row</option>" : "<option value='${row}'>$row</option>";
             }
        }
    }

    $languages = implode('', $languages);

    if (!isset($_POST['email']) or strlen($displayToUser) > 0) {

        $displayToUser .= "
<form class='form-horizontal' role='form' action='install.php?step=7${languageGetParameter}' method='post'>
  <div class='form-group'>
    <label for='inputInstallUrl' class='col-sm-2 control-label'>{$languageObject->installUrl}</label>
    <div class='col-sm-10'>
      <input type='text' class='form-control' id='inputInstallUrl' name='installUrl' value='${installUrl}' required>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputInstallTimezone' class='col-sm-2 control-label'>{$languageObject->timezone}</label>
    <div class='col-sm-10'>
    <select id='inputInstallTimezone' name='timezone'>";

        $timezoneDefined = ini_get('date.timezone');
        foreach (timezone_identifiers_list() as $time) {
            $displayToUser .= ($time == $timezoneDefined) ? "<option selected='selected'>{$time}</option>" : "<option>{$time}</option>";
        }

        $displayToUser .= "
    </select>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputTitle' class='col-sm-2 control-label'>{$languageObject->title}</label>
    <div class='col-sm-10'>
      <input type='text' class='form-control' id='inputTitle' name='title' value='${title}' required>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputLanguage' class='col-sm-2 control-label'>{$languageObject->language}</label>
    <div class='col-sm-10'>
      <select name='language' class='form-control' id='inputLanguage'>${languages}</select>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputEmail' class='col-sm-2 control-label'>{$languageObject->email}</label>
    <div class='col-sm-10'>
      <input type='email' class='form-control' id='inputEmail' name='email' value='${email}' required>
      <p class='help-block'>{$languageObject->email2}</p>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputCaptcha' class='col-sm-2 control-label'>{$languageObject->captcha}</label>
    <div class='col-sm-10'>
      <select name='captcha' class='form-control' id='inputCaptcha'>
      <option value='0'>{$languageObject->no}</option>
      <option value='1' ${selectedCaptcha}>{$languageObject->yes}</option>
      </select>
      <p class='help-block'>{$languageObject->captcha_2}</p>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputFaillogins' class='col-sm-2 control-label'>{$languageObject->faillogins}</label>
    <div class='col-sm-10'>
      <input type='number' class='form-control' id='inputFaillogins' name='faillogins' value='${faillogins}' required>
      <p class='help-block'>{$languageObject->faillogins2}</p>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputBrandname' class='col-sm-2 control-label'>{$languageObject->brandname}</label>
    <div class='col-sm-10'>
      <input type='text' class='form-control' id='inputBrandname' name='brandname' value='${brandname}' required>
      <p class='help-block'>{$languageObject->brandname2}</p>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputPrefix1' class='col-sm-2 control-label'>{$languageObject->prefix1}</label>
    <div class='col-sm-10'>
      <select name='prefix1' class='form-control' id='inputPrefix1'>
      <option value='N'>{$languageObject->no}</option>
      <option value='Y' ${selectedPrefix}>{$languageObject->yes}</option>
      </select>
    </div>
  </div>
  <div class='form-group'>
    <label for='inputPrefix' class='col-sm-2 control-label'>{$languageObject->prefix3}</label>
    <div class='col-sm-10'>
      <input type='text' class='form-control' id='inputPrefix' name='prefix2' value='${prefix2}' required>
      <p class='help-block'>{$languageObject->prefix2}</p>
    </div>
  </div>
  <div class='form-group'>
    <div class='col-sm-offset-2 col-sm-10'>
      <button type='submit' class='btn btn-primary btn-lg pull-right'>{$languageObject->continue}</button>
    </div>
  </div>
</form>
";

    } else {

        try {

            $query = $sql->prepare("INSERT INTO `easywi_statistics_current` (`userID`) VALUES (0) ON DUPLICATE KEY UPDATE `userID`=`userID`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `page_settings` (`id`,`pageurl`,`resellerid`) VALUES (1,?,0) ON DUPLICATE KEY UPDATE `pageurl`=VALUES(`pageurl`)");
            $query->execute(array($_POST['installUrl']));

            $query = $sql->prepare("INSERT INTO `settings` (`id`,`language`,`email`,`prefix1`,`prefix2`,`faillogins`,`brandname`,`resellerid`) VALUES (1,?,?,?,?,?,?,0) ON DUPLICATE KEY UPDATE `language`=VALUES(`language`),`email`=VALUES(`email`),`prefix1`=VALUES(`prefix1`),`prefix2`=VALUES(`prefix2`),`faillogins`=VALUES(`faillogins`),`brandname`=VALUES(`brandname`)");
            $query->execute(array($_POST['language'], $_POST['email'], $_POST['prefix1'], $_POST['prefix2'], $_POST['faillogins'], $_POST['brandname']));

            $query = $sql->prepare("INSERT INTO `eac` (`id`,`resellerid`) VALUES (1,0) ON DUPLICATE KEY UPDATE `resellerid`=`resellerid`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `lendsettings` (`id`,`resellerid`) VALUES (1,0) ON DUPLICATE KEY UPDATE `resellerid`=`resellerid`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `traffic_settings` (`id`,`type`) VALUES (1,'mysql') ON DUPLICATE KEY UPDATE `type`=`type`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `easywi_version` (`id`,`version`,`de`,`en`) VALUES (1,'5.00','','') ON DUPLICATE KEY UPDATE `id`=`id`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `page_pages` (`id`,`authorid`,`type`) VALUES (1,0,'about') ON DUPLICATE KEY UPDATE `id`=`id`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `feeds_settings` (`settingsID`,`resellerID`) VALUES (1,0) ON DUPLICATE KEY UPDATE `settingsID`=`settingsID`");
            $query->execute();

            $query = $sql->prepare("UPDATE `settings` SET `template`='default',`emailbackup`=0x78dab5534d6f9b40103d3752fec39608c991a0c621b612827d49db6b7b700f3d0eec185601962e833f52f5bf771670623b69d543c3f2356fdebe19deb2f1fb8f5fee97dfbf7e123995c5429c9fc539825c9c9fbd8b4951810b9774ad52371ef7a14d9448c07caa7dfcd1aaf5dcb9d7156145fe7257a323d23e9a3b845b1a5b5de74ea439980619fcb6fcecdf38ac138f874a71a2e5ce3ea55a0b25e74e0a465a86e0235eb19a58418a73078c82c2cbb15823a914bc351a09157804b92ec1118d7a6456e82ce2868caeb27defc26dd030993f6248c4632bbb2f512fdc068a968094ae98dd32bd82125d2f1ed70724fb3d936735d10157fc94403884a1fb72cef52bd8f4156c76826109aa3098b11dcd49aad206eb627780f6ec95d6c4ad5977d9cdcee5bdbbdd4ad8978676dd425e589fc54f5ed1124ca6aa2810d092be6360a324e5d124085c1be5a8b29c9ec21aa45455165d05f556d85b07ea4659fb228305fbb8460b26da4834be01a9da260a7b66a2b77e9383d49b68c202fbcb64098c424ff467f0219c5e766c481f32a3db4a46e26235b1e314f64bfde817aa42307e666bf1cf37f2afa712334fec2789c0f5f87d668798845dd01d62767310dcdeba2fcafa1b4c1e143d6bf7b53c51e08a04ff629e30d6209168225d7abc010a6dfc863323ae3a34707984db0e866e8e13b69ba199e30477f694f8638bffe2c2a109871efcd502fd76d265f366daff4df797dd3ffdbef90dafe98a8d,`emailbackuprestore`=0x78dab5534d6f9b40103d3752fec39608c991a0c621b612827d49db6b7b700f3d0eec185601962e833f52f5bf771670623b69d543c3f2356fdebe19deb2f1fb8f5fee97dfbf7e123995c5429c9fc539825c9c9fbd8b4951810b9774ad52371ef7a14d9448c07caa7dfcd1aaf5dcb9d7156145fe7257a323d23e9a3b845b1a5b5de74ea439980619fcb6fcecdf38ac138f874a71a2e5ce3ea55a0b25e74e0a465a86e0235eb19a58418a73078c82c2cbb15823a914bc351a09157804b92ec1118d7a6456e82ce2868caeb27defc26dd030993f6248c4632bbb2f512fdc068a968094ae98dd32bd82125d2f1ed70724fb3d936735d10157fc94403884a1fb72cef52bd8f4156c76826109aa3098b11dcd49aad206eb627780f6ec95d6c4ad5977d9cdcee5bdbbdd4ad8978676dd425e589fc54f5ed1124ca6aa2810d092be6360a324e5d124085c1be5a8b29c9ec21aa45455165d05f556d85b07ea4659fb228305fbb8460b26da4834be01a9da260a7b66a2b77e9383d49b68c202fbcb64098c424ff467f0219c5e766c481f32a3db4a46e26235b1e314f64bfde817aa42307e666bf1cf37f2afa712334fec2789c0f5f87d668798845dd01d62767310dcdeba2fcafa1b4c1e143d6bf7b53c51e08a04ff629e30d6209168225d7abc010a6dfc863323ae3a34707984db0e866e8e13b69ba199e30477f694f8638bffe2c2a109871efcd502fd76d265f366daff4df797dd3ffdbef90dafe98a8d,`emaildown`=0x78dab5534d6f9b40103d3752fec39608c991a0c6218912827d49db6b7b700f3d0eec18560196ee8ebf52e5bf771670623b6dd543c3f7bc79bc997dbb9bbefff8e57efefdeb2751525dcdc4e9495a22c8d9e9c9bb94145538f349b72af7d3711fba448d04cca736c41f4bb59a7af7ba216c289c6f5bf444de47538f704363a7ebdd89bc046391c16ff3cfe18dc73ae978a894665a6edd5baa955072eae560a463083ed205ab8905e438f5c028a88212ab1592ca2158a191d0404050ea1a3c61d523b3626f965a32ba2976bd0bdfa261320f6248a46327bb2bd1ce7c0bd59280946e98bd647a0335fa413a6ef7486e3c931735d10117fc96403884b1fffa9fcbdf60574718d6a02a83050fdd1ea51a6db0adb67b68cf5e684ddc8673929deb1cdd39d9b9ee3e2c6dbb493b739e8a9f3c7b3598423549246049fa8e81b59254269328f25d54a22a4a7a0e5b905235457211b51be11e1da8ad725625062bf66c850eccb491684203522d6d12f7cc4c6f425b82d4eb64c202bbdb14198ce240f457f421be3aefd8903f14462f1b9988b3c5c49dc77058ebc7b0520d82090b578b17da28bcbc92580462f79388fc80bfafdd292671177487b8bed90b6e6ffd5765c335660f8a5eb4fb5a81a87041829753208c3348649a48d7012ff64a9bd07266c4558706ce0f70d7c1d0cd61c27533347398e0ce9e137f6cf15f5cd83761df83bf5aa0df4ebab66fa6fddf749fdcfee9f7cd2fbc9d85ae,`emaildownrestart`=0x78dab5534d6f9b40103d3752fec39608c991a0c6218912827d49db6b7b700f3d0eec18560196ee8ebf52e5bf771670623b6dd543c3f7bc79bc997dbb9bbefff8e57efefdeb2751525dcdc4e9495a22c8d9e9c9bb94145538f349b72af7d3711fba448d04cca736c41f4bb59a7af7ba216c289c6f5bf444de47538f704363a7ebdd89bc046391c16ff3cfe18dc73ae978a894665a6edd5baa955072eae560a463083ed205ab8905e438f5c028a88212ab1592ca2158a191d0404050ea1a3c61d523b3626f965a32ba2976bd0bdfa261320f6248a46327bb2bd1ce7c0bd59280946e98bd647a0335fa413a6ef7486e3c931735d10117fc96403884b1fffa9fcbdf60574718d6a02a83050fdd1ea51a6db0adb67b68cf5e684ddc8673929deb1cdd39d9b9ee3e2c6dbb493b739e8a9f3c7b3598423549246049fa8e81b59254269328f25d54a22a4a7a0e5b905235457211b51be11e1da8ad725625062bf66c850eccb491684203522d6d12f7cc4c6f425b82d4eb64c202bbdb14198ce240f457f421be3aefd8903f14462f1b9988b3c5c49dc77058ebc7b0520d82090b578b17da28bcbc92580462f79388fc80bfafdd292671177487b8bed90b6e6ffd5765c335660f8a5eb4fb5a81a87041829753208c3348649a48d7012ff64a9bd07266c4558706ce0f70d7c1d0cd61c27533347398e0ce9e137f6cf15f5cd83761df83bf5aa0df4ebab66fa6fddf749fdcfee9f7cd2fbc9d85ae,`emailgserverupdate`=0x78dab5534d6f9b40103d3752fec39608c991a0c6218e12827d49db6b7b700f3d0eec18560196ee8ebf52f5bf771670623b6ad543c3d7326f1e6f66df2ee9fb8f5f1e16dfbf7e1225d5d55c9c9fa525829c9f9fbd4b495185739f74ab723f1df7a14bd448c07c6a43fcb152eb99f7a01bc286c2c5ae454fe47d34f308b73476badebdc84b301619fcb6f81cde7aac938e874a69a6e5ce8d52ad8592332f07231d43f0912e594d2c21c799074641159458ad91540ec11a8d8406028252d7e009ab9e98157bf3d492d14db1ef5df8160d93791243221d3bd97d8976ee5ba85604a474c3ec15d31ba8d10fd2717b4072f399bca8890eb8e25102e110c6c378edbffe767a82610daa3258f074ed49aad106db6a7780f6eca5d6c4a59d7bec56e7e2debdce69f76269d72dd485f351fce415abc114aa4922012bd2f70c6c94a432994491efa2125551d273d88294aa2992aba8dd0af7e8406d95b3273158b14f6b7460a68d44131a906a6593b867667a1bda12a4de241316d8dfa6c8601407a2bfa20ff1f4b26343fe5818bd6a64222e9613779ec261ad9fc24a3508262c5c2dde5ca3f07a2ab108c4fe2311f901bfdfb8534ce22ee80e71737b10dcddf9afca861bcc1e15bd68f7b50251e192046fa140186790c83491ae03dee09536a1e5cc88ab0e0d5c1ee1ae83a19be384eb6668e638c19d3d27fed8e2bfb87068c2a1077fb540bf9d746ddf4cfbbfe9fe72ff4fffdffc066c008334,`emailpwrecovery`=0x78dab553cb6e9c30145d3752fec125b23491a0309d264a08c3266db7ed62bae8aabae00b58033635977924eabfd786998469d5aa8bc6e6e1fbf0b9c7c776f2fafda7fbd5d7cf1f58454d9db2f3b3a44210e9f9d9ab8424d59872d2adcc79128ea60b344860f3a90df07b2f374bef5e2b4245c16adfa2c7f2d15a7a843b0a1dae77c7f20a4c87d6f965f531b8f12c4e121e2a2599167bf71772c3a4587a3918e132986d4961d15801392e3d30126abfc27a832473f037680428f0092add80c73af960b3165e9a7464b42a9fb91fec24746847e436e51dd43d0149ad38e37d87464183dc4fc27692e49631e7139f5dbcc162e9713bc172e01e2330a55bdcb7ac06b5f6d26324096102840dc8da606997d7f1d31a4a1b6cebfdc43b66175a93c5716a597506d58e6a0dcaba4147fb61632e9c6eecd1ee5063e94815470c7ad277d6b19582aa781e45dc5915cab2a227b30521a42ae3b751bb63ee333875279d2eb1c1da0ab441e7ccb41168020342f65dbc183333bd0bba0a84dec6730b707c4d99c16ce1b3f189de2cae2e876cc8d7a5d1bd1231bb28e6aeffea0e1afd10d4522198a074b5ec619a05efae04963e3b4e6211f7edf8da75365f0cc6d0d8f5cdc4b8bde5bf950db698ad253d638fb57c566341cc1e199f192710cb34916e7c7ba06b6d82ce4666b6ea81c0e589df3138b0390d38360732a701cbec29f0478affa2c25484a9067f9540bf1c74d3bd18f67fc3fde1eecf786f7e02b06c7f39,`emailregister`=0x78dab5534d6f9b40103d3752fec39608c991a0c6711325047349db6b7b700f3d55033b869561972ee3af54fdef9d053bc18e5af5d0f03d338f376fdfee266f3f7c7e987ffbf251945457a9383f4b4a04999e9fbd49485185a94fa651b99f8cfbd0156a24603c3521fe58a9f5cc7b309a505338df35e889bc8f661ee196c68ed7bb177909b6454e7e9d7f0a6f3de649c6fb4e4966e4cebda55a0b25675e0e563a84e02359309b58408e330fac822a28b15a23a91c82355a091a0282d2d4e089563d326aeaa5494bd6e8e2a05df82d5a06f320f68564ec680f2d9ad46fa15a1190329ad12b866ba8d10f92713300b9f14cfc418e5db0b89879077e4f10d8c28df27b56815e7ae973673821baf25f924f4f725883aa2c16ec477b52d2c66253ed06d91ebd3086b89fb397edec6c3ed8db4d85fb6869d7cde485335afce429ad59b6d271246045e69e131b25a98c2751e4bba8445594f4143620a5d2457c15355be11e5dd2b4caf9175bacd8c835ba6466ac441b5a906ad5c6d31e99996dd89620cd269e30c1e1b64506a36920fa2b7a37bdbeecd0902f0b6b565ac6e2623171e7693aaccd6358298d60c3c2f5e2d5370adf5f4b2c0271f849447ec0df37ee149369177487b8b91d047777fe8bb6e106b3a5a267eebe57202a5c90e0351608eb0c1299213275c03ba032366cb932e2ae7b01974779a760afe6b8e0d4ecc51c1758d953e18f12ffc585a109430ffe6a81793deaba7d35eeffc6fbcbed9f7edffc064dd78bf1,`emailsecuritybreach`=0x78dab553c1729b30103d3733f907950c33ce0cd438349984605fd2f6da1edc438f0b5a8326205169b1e374faef5d819dd8e9b4d343830069779fdeae9ea4fced87cf77cb6f5f3e8a9ada66214e4ff21a412e4e4fdee4a4a8c14548a65365984f47d3075a24603c75317eefd57a1edc194da8295e6e3b0c44395af380f081a69e37b815650dd6213bbf2e3fc5d701f3e4d35da6bc3072eb7ba9d642c9795082951e21f8c957cc265650e23c00aba0896a6cd648aa84688d56828688a0362d04c2a94746a5c12277648daef6b58bd0a165302f6217c8a79e769fa25b840e9a9e8094d18cee19aea1c530caa7dd01c8af67c6710984dc79f3e2997c74a4e1f11c6c4135162b5e927b11d2c662d76c0fbc237a650c319f5788151994da2b34a8e9078eb6c3669c79adc40fde95166ca5749608e8c9dcb263a324d5d92c49426fd5a8aa9a9ecc0ea454baca2e92ee41f8dfe0344e7909328b0d6bb146ef2c8c9568630b52f52e4b4764611e625783349b6cc604fbcf56054cd2488c6ff22ebd3c1fd050de57d6f45a66e26c35f3eda53b6ecd63dc288d60e3cae7e2033489df5f4aac22b19f249230e2f1956f62960ec6f088abeb03e3e626fc2d6dbcc1e25ed133f7982b120dae48f0318984f50289c2109936e243dc181b3b8e4c38ebae80f323bfaf6057cd71c057b32be638c0953d05fe58e2bfa87028c2a1067f95c0bc1e75eb5e8dfbbff1fef4f767bc37bf00ff327bf3,`emailnewticket`=0x78dab5534d6fd340103d53a9ff61716529956c62135ab5ae934b812b1cc281e3d83bb157b577cdee381f45fc7766eda42445200ed4df33f3fce6cdd36efefafda7fbe5d7cf1f444d6db310e767798d2017e767af7252d4e02224d3a932cca763e80b2d12309eba18bff56a3d0fee8d26d4142f771d06a21ca37940b8a5a9e70dee44598375c8c92fcb8ff14dc03cf974df292f8cdcf9b7546ba1e43c28c14a8f107ce42b66132b28711e8055d04435366b245542b4462b414344509b1602e1d423a366c12277648dae0eda45e8d0329887d817f2a9a73db4e816a183a627206534a37b866b68318cf2697704f2f3a45c9740189e96b005d558ac58b97b56d2c662d7ec8eb2237a650cb1266f040f3e1872306230cd7f38da0d9e5f784bc47736bf055b299d25027a32779cd8284975962649e8a31a5555d353d881944a57d9dba4db0aff1892c6293f6966b1e191d7e89385b1126d6c41aade65b31159986dec6a906693a54c70b86d55c0641689f14adeccae2e0734940f9535bd9699b858a5fe7c9e8e5bf318374a23d8b8f2bd789d4ce2775712ab481c7e124918f1f7b53f453a1b82e110d73747c1ed6df85bdb7883c583a25fdc63af4834b822c1ab2112d61b240a4364da88d76a636cecb832e1ae7b01972779af60afe6b4e0d5ecc59c1658d953e18f12ffc58563138e3df8ab05e6e5a85bf762dcff8df787df3fe3bef909c095742f,`emailuseradd`=0x78dab5534d6f9b40103d3752fec39608c991a0c621761d827d49db6b7b700f3d0eec185601962e633b4ed5ffde59c0a9bf5af5d00036cc7b8f37c3cc6efcf6c3e787c5b72f1f454e6531179717718e20e797176f625254e0dc255dabd48d875d68891209584fb58fdf576a3d731e74455891bfd8d6e888b48b660ee1130dadaf732fd21c4c830c7e5d7cf2a70efbc4c33e539c68b9b577a9d642c9999382915621f88897ec269690e2cc01a3a0f0722cd6482a056f8d4642051e41ae4b7044a39e59153af3b821a3ab6c57bb701b342ce68fe88978686d7729eab9db40b12220a52b56af585e4189ae170feb3d91fd9e11f31208f966c31bf754129ec16ef76d4fe9b1ad31d786fb76869d9cc1de9fc1a6471896a00a8319b7b339a22a6db02eb63ddae19d7ea93571abec7c781eed9c76f36967691f1adab64be1ca4e4afce0355182c95415050256a4ef19d8284979340a02d74639aa2ca797b006295595453741fd24ec5f0bea46d90144060b9ec41a2d986823d1f806a45a3551d82913fde4373948bd89466cb0fb992c8141e889ee0ade85e3eb560de96366f4aa9291b85a8eec790cfba57ef60b5521183fb3b978f90efcdbb1c4cc13bb9744e07afc3cb1a718856dd01e6232dd0beeeedc93b4fe06934745bfbdbb5c9e2870498217a9278c6d904834912e3dde4285367ec3cc80b3f6055c1fe0b682be9a43c256d317734870652fc41f4bfc972eec3761bf077f6d817e3debb27935efffe6fbd3ee9f6edffc02148d9b15,`emailvinstall`=0x78dab5534d6f9b40103d3752fec39608c991a0c6217613827d49db6b7b700f3d0eec185601962e831d27ea7fef2ce0c476d2aa8786ef79f39879bc61e3f79fbede2e7f7cfb2c722a8b85383d897304b9383d791793a202172ee95aa56e3cee439b289180f954fbf8b355ebb973ab2bc28afce5b64647a47d347708ef696ceb3a3722cdc134c8e0f7e517ffcae13af178e814275a6eed5daab55072eea460a46508dee21557132b4871ee8051507839166b249582b74623a1028f20d72538a2510fcc0a9d45dc90d155b6d32edc060d93f92386443cb665772dea85db40d11290d215b35ba65750a2ebc5e37a8f64bf67f25c4d74c005df25100e61e8be7ce7725fc171726a93b9366cd92bd9d92bd8c7230c4b5085c18c5d6b8e5295365817db01edf19ebfd29a588f1d03dbde8d6337866e64f6a1a16d37f1333b10f1c8a32fc164aa8a02012de91b06364a521e4d82c0b5518e2acbe929ac414a5565d14550df0b7be940dd28eb7364b060c3d768c1441b89c6372055db4461cf4cf4bddfe420f5269a7081dd69b20446a127fa23f8104ecf3b36a47799d16d252371b69ad8fd18f64bfde017aa42307e667bf15f3af22fa712334fec5e1281ebf1f3ccee62127641b789d9d55e707dedbe68eb6f30b953f45cbbefe589025724f85ff484b106894413e9d2e3955268e3379c1971d741c0f9016e150c6a0e1356cd20e630c1ca9e127f94f82f2eec9bb0efc15f2dd06f57ba6cdeacf67fabfbcbae9f7eddfc06c9e395f2,`emailvrescue`=0x78dab5534d6f9b40103d3752fec39608c991a0c621b612827d49db6b7b700f3d0eec185601962e833f52e5bf7716706a3b69d543c3f7bcf798997dbb1bbffff8e57ef9fdeb279153592cc4f9599c23c8c5f9d9bb981415b87049d72a75e3711f5aa24402d653ede38f56ade7cebdae082bf297bb1a1d91f6d1dc21dcd2d8e675ee449a836990c16fcbcffe8dc379e2f150294eb4dcd9b7546ba1e4dc49c148ab107cc42bce265690e2dc01a3a0f0722cd6482a056f8d4642051e41ae4b7044a31e59153a8bb821a3ab6cdfbb701b342ce6410c443cb669f725ea85db40d11290d215ab5b965750a2ebc5e3fa4064c733615ed5fcb0c115bf25100e61e8bed45f3367eacd2bccf4156c76826109aa3098b121cd0955698375b11bd01eeff52bad89076b1d66473ba7f70e77b3613f1ada75937961bd163f79564b3099aaa240404bfa8e818d9294479320706d94a3ca727a0e6b905255597415d45b611f1da81b652d8c0c16ece51a2d986823d1f806a46a9b28ec9589defa4d0e526fa20927d8df264b60147aa2bf820fe1f4b25343fa9019dd56321217ab893d4f61bfd48f7ea12a04e367b6162fc0917f3d95987962ff93085c8fbf67f61493b00bba43cc6e0e82db5bf745597f83c983a2dfb9fb5a9e287045829799278c3548249a48971e6f82421bbf6166c45587062e8f70dbc1d0cd3161bb199a3926b8b367e28f2dfe8b0b87261c7af0570bf4dba52e9b37cbfddff23ed9fdd3ef9b5fdf7d8aa9");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emailbackup', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Sicherung des Servers</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>die Sicherung Ihres Servers</text1>\r\n	<text2>wurde um</text2>\r\n	<text3>fertig gestellt.</text3>\r\n	<text4>Ihr Server sollte weiterhin erreichbar sein.</text4>\r\n	<text5>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text5>\r\n	<text6>Die entsprechende Nummer finden Sie im Panel.</text6>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emailbackuprestore', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Wiederherstellung Ihres Backups</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>das Backup Ihres Servers</text1>\r\n	<text2>wurde um</text2>\r\n	<text3>erfolgreich eingespielt.</text3>\r\n	<text4>Ihr Server sollte weiterhin erreichbar sein.</text4>\r\n	<text5>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text5>\r\n	<text6>Die entsprechende Nummer finden Sie im Panel.</text6>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emaildown', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server nicht erreichbar</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ihr Servers</text1>\r\n	<text2>kann seit</text2>\r\n	<text3>nicht mehr erreicht werden.</text3>\r\n	<text4>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text4>\r\n	<text5>Die entsprechende Nummer finden Sie im Panel.</text5>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emaildownrestart', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server abgest&uuml\;rzt</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ihr Servers</text1>\r\n	<text2>konnte am</text2>\r\n	<text3>nicht erreicht werden und wurde neu gstartet.</text3>\r\n	<text4>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text4>\r\n	<text5>Die entsprechende Nummer finden Sie im Panel.</text5>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emailgserverupdate', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Update eines Masterservers</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ein Gameserverupdate wurde beim Server</text1>\r\n	<text2>um</text2>\r\n	<text3>f&uuml\;r das Spiel</text3> \r\n	<text4>fertig gestellt.</text4>\r\n	<text5>Ihre Server sollten weiterhin erreichbar sein.</text5>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emailnewticket', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Ticket</topic>\r\n	<text1>schrieb am</text1>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emailpwrecovery', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Passwort Recovery</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Um ein neues Passwort anzufordern, rufen Sie bitte folgenden Best&auml\;tigungslink auf:</text1>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emailsecuritybreach', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Manipulation am Server entdeckt</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Am</text1>\r\n	<text2>wurde am Server</text2>\r\n	<text3>eine unzul&auml\;ssige Manipulation entdeckt:</text3> \r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emailuseradd', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Zugangsdaten</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ihr Account wurde um</text1>\r\n	<text2>f&uuml\;r Sie bereit gestellt.</text2>\r\n	<text3>Ihre Zugangsdaten lauten wie folgt:</text3> \r\n	<text4>Benutzername:</text4>\r\n	<text5>Passwort:</text5>\r\n	<text6>Bitte speichern Sie die Zugangsdaten aus Sicherheitsgr&uuml\;nden in einer verschl&uuml\;sselten Datei und l&ouml\;schen diese Email danach.</text6>\r\n	<text7>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text7>\r\n	<text8>Die entsprechende Nummer finden Sie im Panel.</text8>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emailregister', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Account Aktivierung</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>bitte aktivieren Sie Ihren Account indem sie auf folgenden Link klicken:</text1>\r\n	<text2>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text2>\r\n	<text3>Die entsprechende Nummer finden Sie im Panel.</text3>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'de', 'emailvrescue', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Rescue System gestartet</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Das Rescue System ihres Servers mit der IP</text1>\r\n	<text2>wurde um</text2>\r\n	<text3>f&uuml\;r Sie gestartet.</text3> \r\n	<text4>Das Passwort f&uuml\;r den Root Account lautet:</text4>\r\n	<text5>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text5>\r\n	<text6>Die entsprechende Nummer finden Sie im Panel.</text6>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emailbackup', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Your Serverbackup</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>the backup for your server</text1>\r\n	<text2>was created at</text2>\r\n	<text3>.</text3>\r\n	<text4>Your server should be still available for access.</text4>\r\n	<text5>If you have any questions feel free to use our supportsystem or give us a call.</text5>\r\n	<text6>You will find the phonenumber in our panel.</text6>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emailbackuprestore', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Your Serverbackup</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>the backup for your server</text1>\r\n	<text2>was successfully restored</text2>\r\n	<text3>.</text3>\r\n	<text4>Your server should be still available for access.</text4>\r\n	<text5>If you have any questions feel free to use our supportsystem or give us a call.</text5>\r\n	<text6>You will find the phonenumber in our panel.</text6>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emaildown', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server could not been reached</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>Your server</text1>\r\n	<text2>could not been reached since</text2>\r\n	<text3>.</text3>\r\n	<text4>If you have any questions feel free to use our supportsystem or give us a call.</text4>\r\n	<text5>You will find the phonenumber in our panel.</text5>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emaildownrestart', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server crashed</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>Your server</text1>\r\n	<text2>could not been reached at</text2>\r\n	<text3>and was restarted.</text3>\r\n	<text4>If you have any questions feel free to use our supportsystem or give us a call.</text4>\r\n	<text5>You will find the phonenumber in our panel.</text5>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emailgserverupdate', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Masterservers has been updated</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>a gameserverupdate for the server</text1>\r\n	<text2>was applied at</text2>\r\n	<text3>for the game</text3> \r\n	<text4>.</text4>\r\n	<text5>Your server should be still available for access.</text5>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emailnewticket', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Ticket</topic>\r\n	<text1>wrote at</text1>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emailpwrecovery', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Password recovery</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>To require a new password please use following activation link:</text1>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emailsecuritybreach', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Server manipulation detected</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>At</text1>\r\n	<text2>a illegal servermanipulation at the server</text2>\r\n	<text3>was detected:</text3> \r\n	<noreply>This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emailuseradd', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Zugangsdaten</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>Ihr Account wurde um</text1>\r\n	<text2>f&uuml\;r Sie bereit gestellt.</text2>\r\n	<text3>Ihre Zugangsdaten lauten wie folgt:</text3> \r\n	<text4>Benutzername:</text4>\r\n	<text5>Passwort:</text5>\r\n	<text6>Bitte speichern Sie die Zugangsdaten aus Sicherheitsgr&uuml\;nden in einer verschl&uuml\;sselten Datei und l&ouml\;schen diese Email danach.</text6>\r\n	<text7>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text7>\r\n	<text8>Die entsprechende Nummer finden Sie im Panel.</text8>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emailregister', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Account Activation</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>please activate your account by clicking following link:</text1>\r\n	<text2>If you have any questions feel free to use our supportsystem or give us a call.</text2>\r\n	<text3>You will find the phonenumber in our panel.</text3>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `translations` (`type`,`lang`,`transID`,`text`,`resellerID`) VALUES ('em', 'uk', 'emailvrescue', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Rescue system has been started</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>The rescue system for your server with the IP</text1>\r\n	<text2>was started at</text2>\r\n	<text3>.</text3> \r\n	<text4>The root password is:</text4>\r\n	<text5>If you have any questions feel free to use our supportsystem or give us a call.</text5>\r\n	<text6>You will find the phonenumber in our panel.</text6>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `resellerimages` (`id`, `distro`, `description`, `bitversion`, `pxelinux`) VALUES (1, 'other', 'Rescue 32bit', 32, 'DISPLAY boot.txt\r\nDEFAULT rescue\r\nTIMEOUT 10\r\n\r\nLABEL default\r\n        kernel /rescue/vmlinuz-rescue\r\n        append initrd=/rescue/initram.igz setkmap=de dodhcp rootpass=%rescuepass% scandelay=5 boothttp=http://1.1.1.1/rescue/32/sysrcd.dat') ON DUPLICATE KEY UPDATE `id`=`id`");
            $query->execute();
            $query = $sql->prepare("INSERT INTO `resellerimages` (`id`, `distro`, `description`, `bitversion`, `pxelinux`) VALUES (2, 'other', 'Rescue 64bit', 64, 'DISPLAY boot.txt\r\nDEFAULT rescue\r\nTIMEOUT 10\r\n\r\nLABEL default\r\n        kernel /rescue/vmlinuz-rescue\r\n        append initrd=/rescue/initram.igz setkmap=de dodhcp rootpass=%rescuepass% scandelay=5 boothttp=http://1.1.1.1/rescue/64/sysrcd.dat') ON DUPLICATE KEY UPDATE `id`=`id`");
            $query->execute();

            $configFp = @fopen(EASYWIDIR . '/stuff/config.php', "w+");

            if ($configFp) {

                $configdata = "<?php

// This file was generated by the easy-wi.com installer

" . '$host' . " = '" . $host . "';
" . '$user' . " = '" . $user . "';
" . '$db' . " = '" . $db . "';
" . '$pwd' . " = '" . $pwd . "';
" . '$captcha' . " = '" . $_POST['captcha'] . "';
" . '$title' . " = '" . $_POST['title'] . "';
" . '$debug' . " = 0;
" . '$timezone' . " = '" . $_POST['timezone'] . "';
";

                @fwrite($configFp, $configdata);

                fclose($configFp);

            }

            $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_configuration}</div>";
            $displayToUser .= "<div class='pager'><a href='?step=8${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";

        } catch(PDOException $error) {

            $displayToUser = "<div class='alert alert-danger'>{$error->getMessage()}</div>";

        }

    }
}

if ($currentStep == 8 and count($systemCheckError) == 0) {
    if (!isset($_POST['submit'])) {

        $displayToUser .= "<div class='alert alert-success'>{$languageObject->games_insert}</div>";
        $displayToUser .= "
<form class='form-horizontal' role='form' action='install.php?step=8${languageGetParameter}' method='post'>
  <div class='form-group'>
    <div class='col-sm-offset-2 col-sm-10'>
      <button type='submit' name='submit' class='btn btn-primary btn-lg pull-right'>{$languageObject->continue}</button>
    </div>
  </div>
</form>
";

    } else {

        try {

            include(EASYWIDIR . '/stuff/methods/gameslist.php');

            $displayToUser .= "<div class='pager'><a href='?step=9${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";
            $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_gameserver_data}</div>";

            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=0 LIMIT 1");
            $query2 = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`gamebinary`,`gamebinaryWin`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`gameq`,`gamemod`,`gamemod2`,`configs`,`configedit`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`useQueryPort`,`mapGroup`,`protected`,`protectedSaveCFGs`,`ramLimited`,`os`) VALUES (:steamgame,:appID,:updates,:shorten,:description,:gamebinary,:gamebinaryWin,:binarydir,:modfolder,:fps,:slots,:map,:cmd,:modcmds,:tic,:gameq,:gamemod,:gamemod2,:configs,:configedit,:portStep,:portMax,:portOne,:portTwo,:portThree,:portFour,:portFive,:useQueryPort,:mapGroup,:protected,:protectedSaveCFGs,:ramLimited,:os)");
            $query3 = $sql->prepare("UPDATE `servertypes` SET `steamgame`=:steamgame,`appID`=:appID,`updates`=:updates,`shorten`=:shorten,`description`=:description,`gamebinary`=:gamebinary,`gamebinaryWin`=:gamebinaryWin,`binarydir`=:binarydir,`modfolder`=:modfolder,`fps`=:fps,`slots`=:slots,`map`=:map,`cmd`=:cmd,`modcmds`=:modcmds,`tic`=:tic,`gameq`=:gameq,`gamemod`=:gamemod,`gamemod2`=:gamemod2,`configs`=:configs,`configedit`=:configedit,`portStep`=:portStep,`portMax`=:portMax,`portOne`=:portOne,`portTwo`=:portTwo,`portThree`=:portThree,`portFour`=:portFour,`portFive`=:portFive,`useQueryPort`=:useQueryPort,`mapGroup`=:mapGroup,`protected`=:protected,`protectedSaveCFGs`=:protectedSaveCFGs,`ramLimited`=:ramLimited,`os`=:os WHERE `shorten`=:shorten AND `resellerid`=0 LIMIT 1");

            foreach ($gameImages as $image) {

                if (count($image) == 33) {

                    $query->execute(array($image[':shorten']));
                    $imageExists = (int) $query->fetchColumn();

                    if ($imageExists == 0) {
                        $query2->execute($image);
                        $affectedRows = $query2->rowCount();
                    } else {
                        $query3->execute($image);
                        $affectedRows = $query3->rowCount();
                    }
                    if ($affectedRows == 1) {
                        $displayToUser .= "<div class='alert alert-success'>{$image[':description']}</div>";
                    }

                } else {
                    $displayToUser .= "<div class='alert alert-danger'>{$languageObject->error_game_insert} ".count($image)." ${image[':description']}</div>";
                }

            }

            require_once(EASYWIDIR . '/stuff/methods/addonslist.php');

            $query = $sql->prepare("SELECT `id` FROM `addons` WHERE `addon`=? AND `resellerid`=0 LIMIT 1");
            $query2 = $sql->prepare("INSERT INTO `addons` (`active`,`depending`,`paddon`,`addon`,`type`,`folder`,`menudescription`,`configs`,`cmd`,`rmcmd`,`resellerid`) VALUES ('Y',?,?,?,?,?,?,?,?,?,0)");
            $query3 = $sql->prepare("SELECT `id` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=0 LIMIT 1");
            $query4 = $sql->prepare("INSERT INTO `addons_allowed` (`addon_id`,`servertype_id`,`reseller_id`) VALUES (?,?,0)");

            foreach ($gameAddons as $addon) {

                if (count($addon) == 10) {

                    $query->execute(array($addon[':addon']));
                    $addonID = $query->fetchColumn();

                    if ($addonID < 1) {

                        $dependsID = 0;

                        if (strlen($addon[':depends']) > 0) {
                            $query->execute(array($addon[':depends']));
                            $dependsID = $query->fetchColumn();
                        }

                        $query2->execute(array($dependsID, $addon[':paddon'], $addon[':addon'], $addon[':type'], $addon[':folder'], $addon[':menudescription'], $addon[':configs'], $addon[':cmd'], $addon[':rmcmd']));

                        $addonID = $sql->lastInsertId();

                        foreach ($addon[':supported'] as $supported) {

                            $query3->execute(array($supported));

                            $query4->execute(array($addonID,$query3->fetchColumn()));

                        }
                        $displayToUser .= "<div class='alert alert-success'>{$addon[':menudescription']}</div>";
                    }
                }
            }

            $displayToUser .= "<div class='pager'><a href='?step=9${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";

        } catch(PDOException $error) {

            $displayToUser = "<div class='alert alert-danger'>{$error->getMessage()}</div>";

        }

    }
}

if ($currentStep == 9 and count($systemCheckError) == 0) {

    // Root module is not used at the moment and needs to be rewritten
    $query = $sql->prepare("SELECT `id` FROM `modules` WHERE `get`='ro' LIMIT 1");
    $query->execute();
    $rootModuleId = (int) $query->fetchColumn();

    if ($rootModuleId > 0) {
        $query = $sql->prepare("UPDATE `modules` SET `active`='N' WHERE `id`=? LIMIT 1");
        $query->execute(array($rootModuleId));
    } else {
        $query = $sql->prepare("INSERT INTO `modules` (`get`,`sub`,`file`,`active`,`type`) VALUES ('ro','ro','','N','C')");
        $query->execute();
    }

    function rmr($dir) {

        if (is_dir($dir)) {

            $dircontent = scandir($dir);

            foreach ($dircontent as $c) {
                if ($c != '.' and $c != '..' and is_dir($dir . '/' . $c)) {
                    rmr($dir . '/' . $c);
                } else if ($c != '.' and $c != '..') {
                    @unlink($dir . '/' . $c);
                }
            }

            @rmdir($dir);

        } else {
            @unlink($dir);
        }
    }

    rmr(EASYWIDIR . "/install");

    $query = $sql->prepare("UPDATE `settings` SET `lastCronReboot`=:futuretime,`lastCronCloud`=:futuretime,`lastCronJobs`=:futuretime,`lastCronUpdates`=:futuretime,`lastCronStatus`=:futuretime");
    $query->execute(array(':futuretime' => strtotime("+2 hours")));


    if (file_exists(EASYWIDIR . "/install")) {
        $displayToUser .= "<div class='alert alert-warning'>{$languageObject->install_done_folder}</div>";
    }

    $displayToUser .= "<div class='alert alert-success'>{$languageObject->install_done}</div>";

    $displayPHPUser = (isset($_SERVER['USER'])) ? $_SERVER['USER'] : 'changeToPHPUser';

    $displayToUser .= "<div class='alert alert-success'><h4>{$languageObject->cron_internal} (/etc/crontab)</h4>
<strong>{$languageObject->cron_internal_text}</strong><br>
0 */1 * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 300 php ./reboot.php >/dev/null 2>&1<br>
*/5 * * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 290 php ./statuscheck.php >/dev/null 2>&1<br>
*/1 * * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 290 php ./startupdates.php >/dev/null 2>&1<br>
*/5 * * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 290 php ./jobs.php >/dev/null 2>&1<br>
*/10 * * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 290 php ./cloud.php >/dev/null 2>&1</div>";

    $displayToUser .= "<div class='alert alert-success'><h4>{$languageObject->cron_internal} (crontab -e)</h4>
<strong>{$languageObject->cron_internal_text}</strong><br>
0 */1 * * * cd " . EASYWIDIR . " && timeout 300 php ./reboot.php >/dev/null 2>&1<br>
*/5 * * * * cd " . EASYWIDIR . " && timeout 290 php ./statuscheck.php >/dev/null 2>&1<br>
*/1 * * * * cd " . EASYWIDIR . " && timeout 290 php ./startupdates.php >/dev/null 2>&1<br>
*/5 * * * * cd " . EASYWIDIR . " && timeout 290 php ./jobs.php >/dev/null 2>&1<br>
*/10 * * * * cd " . EASYWIDIR . " && timeout 290 php ./cloud.php >/dev/null 2>&1</div>";

    $query = $sql->prepare("SELECT `pageurl` FROM `page_settings` WHERE `id`=1 LIMIT 1");
    $query->execute();
    $pageUrl = $query->fetchColumn();

    $displayToUser .= "<div class='alert alert-success'><h4>{$languageObject->cron_external} (/etc/crontab)</h4>
<strong>{$languageObject->cron_external_text}</strong><br>
0 */1 * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}reboot.php >/dev/null 2>&1<br>
*/5 * * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}statuscheck.php >/dev/null 2>&1<br>
*/1 * * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}startupdates.php >/dev/null 2>&1<br>
*/5 * * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}jobs.php >/dev/null 2>&1<br>
*/10 * * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}cloud.php >/dev/null 2>&1</div>";

    $displayToUser .= "<div class='alert alert-success'><h4>{$languageObject->cron_external} (crontab -e)</h4>
<strong>{$languageObject->cron_external_text}</strong><br>
0 */1 * * * wget -q --no-check-certificate -O - ${pageUrl}reboot.php >/dev/null 2>&1<br>
*/5 * * * * wget -q --no-check-certificate -O - ${pageUrl}statuscheck.php >/dev/null 2>&1<br>
*/1 * * * * wget -q --no-check-certificate -O - ${pageUrl}startupdates.php >/dev/null 2>&1<br>
*/5 * * * * wget -q --no-check-certificate -O - ${pageUrl}jobs.php >/dev/null 2>&1<br>
*/10 * * * * wget -q --no-check-certificate -O - ${pageUrl}cloud.php >/dev/null 2>&1</div>";

}

if (strlen($displayToUser) == 0 and count($systemCheckError) > 0) {
    foreach ($systemCheckError as $v) {
        if (is_array($v)) {
            foreach ($v as $v2) {
                $displayToUser .= "<div class='alert alert-danger'>${v2}</div>";
            }
        } else {
            $displayToUser .= "<div class='alert alert-danger'>${v}</div>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Easy-WI Installer">
    <meta name="author" content="Ulrich Block">

    <title>Easy-WI Installer</title>

    <!-- Bootstrap core CSS -->
    <link href="../css/default/bootstrap.min.css" rel="stylesheet">
    <link href="../css/default/font-awesome.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <style type="text/css">
            /* Space out content a bit */
        body {
            padding-top: 20px;
            padding-bottom: 20px;
        }

            /* Everything but the jumbotron gets side spacing for mobile first views */
        .header, .footer {
            padding-left: 15px;
            padding-right: 15px;
        }

            /* Custom page header */
        .header {
            border-bottom: 1px solid #e5e5e5;
        }
            /* Make the masthead heading the same height as the navigation */
        .header h3 {
            margin-top: 0;
            margin-bottom: 0;
            line-height: 40px;
            padding-bottom: 19px;
        }

            /* Custom page footer */
        .footer {
            padding-top: 19px;
            color: #777;
            border-top: 1px solid #e5e5e5;
        }

            /* Customize container */
        @media (min-width: 768px) {
            .container {
                max-width: 1024px;
            }
        }
        .container-narrow > hr {
            margin: 30px 0;
        }

            /* Responsive: Portrait tablets and up */
        @media screen and (min-width: 768px) {
            /* Remove the padding we set earlier */
            .header, .footer {
                padding-left: 0;
                padding-right: 0;
            }
            /* Space out the masthead */
            .header {
                margin-bottom: 30px;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <div class="header">
        <ul class="nav nav-pills pull-right">
            <li><a href="https://twitter.com/EasyWI" target="_blank"><i class="fa fa-twitter fa-fw"></i> Twitter</a></li>
            <li><a href="https://github.com/easy-wi/developer" target="_blank"><i class="fa fa-github fa-fw"></i> Github</a></li>
            <li><a href="https://easy-wi.com/forum/" target="_blank" title="easy-wi.com wiki"><i class="fa fa-comments fa-fw"></i> Forum</a></li>
            <li><a href="http://wiki.easy-wi.com" target="_blank" title="easy-wi.com forum"><i class="fa fa-question-circle fa-fw"></i> Wiki</a></li>
            <li><a href="?step=<?php echo $currentStep;?>&amp;language=de"><img src="../images/flags/de.png"></a></li>
            <li><a href="?step=<?php echo $currentStep;?>&amp;language=en"><img src="../images/flags/uk.png"></a></li>
            <li><a href="?step=<?php echo $currentStep;?>&amp;language=dk"><img src="../images/flags/dk.png"></a></li>
        </ul>
        <h3 class="text-muted">Easy-WI.com Installer</h3>
    </div>

    <div class="row">
        <div class="col-md-3">
            <ul class="nav nav-pills nav-stacked">
                <li <?php if ($currentStep == 0) echo 'class="active"'; ?>><a href="?step=0<?php echo $languageGetParameter;?>"><i class="fa fa-info-circle fa-fw"></i> <?php echo $languageObject->menu_welcome;?></a></li>
                <li <?php if ($currentStep == 1) echo 'class="active"'; ?>><a href="?step=1<?php echo $languageGetParameter;?>"><i class="fa fa-stethoscope fa-fw"></i> <?php echo $languageObject->menu_system;?></a></li>
                <li <?php if ($currentStep == 2) echo 'class="active"'; ?>><a href="?step=2<?php echo $languageGetParameter;?>"><i class="fa fa-key fa-fw"></i> <?php echo $languageObject->menu_db_access;?></a></li>
                <li <?php if ($currentStep == 3) echo 'class="active"'; ?>><a href="?step=3<?php echo $languageGetParameter;?>"><i class="fa fa-stethoscope fa-fw"></i> <?php echo $languageObject->menu_db_access_check;?></a></li>
                <li <?php if ($currentStep == 4) echo 'class="active"'; ?>><a href="?step=4<?php echo $languageGetParameter;?>"><i class="fa fa-tasks fa-fw"></i> <?php echo $languageObject->menu_db_add;?></a></li>
                <li <?php if ($currentStep == 5) echo 'class="active"'; ?>><a href="?step=5<?php echo $languageGetParameter;?>"><i class="fa fa-eye fa-fw"></i> <?php echo $languageObject->menu_db_check;?></a></li>
                <li <?php if ($currentStep == 6) echo 'class="active"'; ?>><a href="?step=6<?php echo $languageGetParameter;?>"><i class="fa fa-user fa-fw"></i> <?php echo $languageObject->menu_admin_add;?></a></li>
                <li <?php if ($currentStep == 7) echo 'class="active"'; ?>><a href="?step=7<?php echo $languageGetParameter;?>"><i class="fa fa-cogs fa-fw"></i> <?php echo $languageObject->menu_page_data;?></a></li>
                <li <?php if ($currentStep == 8) echo 'class="active"'; ?>><a href="?step=8<?php echo $languageGetParameter;?>"><i class="fa fa-upload fa-fw"></i> <?php echo $languageObject->menu_gamedata_add;?></a></li>
                <li <?php if ($currentStep == 9) echo 'class="active"'; ?>><a href="?step=9<?php echo $languageGetParameter;?>"><i class="fa fa-smile-o fa-fw"></i> <?php echo $languageObject->menu_finish;?></a></li>
            </ul>
        </div>

        <div class="col-md-9">
            <div class="progress progress-striped">
                <div class="progress-bar progress-bar-success"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progressPercent;?>%">
                    <span class="sr-only"><?php echo $progressPercent;?>% Complete</span>
                </div>
            </div>
            <?php echo $displayToUser;?>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?></p>
    </div>

</div> <!-- /container -->


<!-- Bootstrap core JavaScript Placed at the end of the document so the pages load faster -->
<script src="../js/default/jquery.min.js" type="text/javascript"></script>
<script src="../js/default/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>
