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

    if (!$json or $json->v >= '4.40') {
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

            $query = $sql->prepare("INSERT INTO `easywi_version` (`id`,`version`,`de`,`en`) VALUES (1,'4.40','','') ON DUPLICATE KEY UPDATE `id`=`id`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `page_pages` (`id`,`authorid`,`type`) VALUES (1,0,'about') ON DUPLICATE KEY UPDATE `id`=`id`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `feeds_settings` (`settingsID`,`resellerID`) VALUES (1,0) ON DUPLICATE KEY UPDATE `settingsID`=`settingsID`");
            $query->execute();

            $query = $sql->prepare("UPDATE `settings` SET `template`='default',`emailbackuprestore`=0x78dae555c18eda30103db3d2fe83eb2a3750425852aa3a482b0ad7ee811e7a1ce221b1eac4a96368e9d777e2042da0ad96760fabaabe789ee7cdb33d7a89c59b8f9f16eb2f0f4b56b852cfd9ed8d2810e4fcf666209c721ae78133b5ca021176b04d94e880f8ae1ee1b79ddaa77c612a87951bad0f3572967528e50e7fb8b0d5fdc0b2026c83b4f679bd1acd38c988b0df486c8c3cb04d9e196d6ccadfaefce04ce3d695607355a53ce28c8e7182baf0bb92ae209c1e170a5479e13c43abea2ba9c5d3647a1f73b6bfc0d0e3851f244f672518d1984c78777fd868641b63255a2fd96f378ea2806e895ad720a5aa729f6c715343d6e35661c084b33e18300a253b1e2f3e1123a9c7bb2fc6c97dbce273eab67ca6f0fdecac7239bd9b8ee3ab2a9fd9925164cf239adb5efc6957c62f684bf4b77d999d579295a265e20db7255fb22d649872b00af4b040bd47a73218eed14aa860e8a0302570d6a89fc4221f88c65953e5c7af80050d5a22d3e7d02744d8cacec5c686decb7e0a1ad03b074e998a4a7654534189c1f082d57a6efca8c9fc424cb304873d9c04a7ecbbe00989e91925b9a01c735882d21673b0b2f90da532166b7d783adbf5fc0a774557daeb9f3655f22ea27dbba66e8d71de12afde9bffe63724c2f6d1e85e91f6ddfa05e50fe105,`emaildown`=0x78dae555c18eda30103db3d2fe83eb2a375042d850aa3a482b0ad7ee811e7a1ce221b1eac4a96368e9d777e2042da0ad96760fabaabe789e67e6d97e7a89c59b8f9f16eb2f0f4b56b852cfd9ed8d2810e4fcf666209c721ae78133b5ca021176b04d94e880ea5d3dc26f3bb54ff9c2540e2b375a1f6ae42ceb50ca1dfe7061cbfb816505d80669edf37a359a71a21161bf91d81879609b3c33dad894bf5df9c199c6ad2bc1e6aa4a79c4191de30475e177255d41383d2e14a8f2c2f90aadaaafc41627d3e43ee66c7f81a1c70b3f889ece4a30a23199f0eefeb0d1c836c64ab49eb2df6e1c4501dd12b5ae414a55e53ed9e2a686acc72dc38009677d3060144a763c5e7c4246548f775f8ca7f7f18acf496df94ce3fbd959e732b94bc6f1559dcf6cc928b2e711cdad167faacaf805b2447fabcbecbc93ac142da7de705bf225db42862907ab400f0bd47b742a83e11ead840a860e0a5302678dfa4955e403d1386baafcf815b0a0414bc5f439f40911b6b473b1b1a1f7b29f8206f4ce8153a6a2961df554506230bca86a3d377ee4647e21a65982c31e4e82279aee8253945c941c735882d21673b0b2f94d49652cd6faf074b653f90a3f45571aea9fb6d1f45d44fb76a26e8d71de04afaecd7ff3e31161fb4c74ef46fb52fd023336dd2c,`emaildownrestart`=0x78dae555c18eda30103db3d2fe83eb2a375042d850aa3a482b0ad7ee811e7a1ce221b1eac4a96368e9d777e2042da0ad96760fabaabe789e67e6d97e7a89c59b8f9f16eb2f0f4b56b852cfd9ed8d2810e4fcf666209c721ae78133b5ca021176b04d94e880ea5d3dc26f3bb54ff9c2540e2b375a1f6ae42ceb50ca1dfe7061cbfb816505d80669edf37a359a71a21161bf91d81879609b3c33dad894bf5df9c199c6ad2bc1e6aa4a79c4191de30475e177255d41383d2e14a8f2c2f90aadaaafc41627d3e43ee66c7f81a1c70b3f889ece4a30a23199f0eefeb0d1c836c64ab49eb2df6e1c4501dd12b5ae414a55e53ed9e2a686acc72dc38009677d3060144a763c5e7c4246548f775f8ca7f7f18acf496df94ce3fbd959e732b94bc6f1559dcf6cc928b2e711cdad167faacaf805b2447fabcbecbc93ac142da7de705bf225db42862907ab400f0bd47b742a83e11ead840a860e0a5302678dfa4955e403d1386baafcf815b0a0414bc5f439f40911b6b473b1b1a1f7b29f8206f4ce8153a6a2961df554506230bca86a3d377ee4647e21a65982c31e4e82279aee8253945c941c735882d21673b0b2f94d49652cd6faf074b653f90a3f45571aea9fb6d1f45d44fb76a26e8d71de04afaecd7ff3e31161fb4c74ef46fb52fd023336dd2c,`emailgserverupdate`=0x78dae5554d8fda30103db3d2fe07d7556ea084b0a15475905614aeed811e7a1c92815875e2d41968e9afefc409da05adb4b47b5855f5c5f3e6e3d99e3cc7eacdc74f8bf5d7cf4b515069e6e2f6461508f9fcf666a04893c17940b6d659a0c20eb6811209389fea117edfeb432a17b622ac68b43ed62845d6a15412fea4b0e5fd20b2025c83ecfbb25e8d66926954d82fa436363f8acd2eb3c6ba54be5df92185c12d95e076ba4a6524056fe311eacc1f3aa782717a7214a87705f90ca3ab6fcc1627d3e43e96e27081a1c70b3f989ef7ca30e23199c8eefcb0312836d6e5e83c65bfdc388a023e251a53439eeb6ae7832d6e6ac87adc320c8422e78d81603317a7edc58fc898eae1ec8bf1f43e5ec939773b7fa6f0fdecac7299dc25e3f8aaca6796146cb9738be7b6177fda95f10bda12fd6d5f66e7952ca56839f582dbb22ec516324c25380d6658a03920e90c86077439543024286c095234fa1767b10e5443ce56bbd32d1041838e93f93af40115b6b473b571a1d7b29f8206cc9e80b4adb864cf351594180c2fb25acd8d1f388577c43ce740d8c3491b2face31bd57bee822768920be729565987b5393e1dedda788560a22b15f34feb64fa2ee275032c419badb5e4bff2abf7e6bff9b3a8b07d07ba87a17d8a7e038c3ed52f,`emailpwrecovery`=0x78dae5554d8fd330103d77a5fd0fc628b756f9585a8a7022ad4a7b854339709c24d3c4c2b183e316caaf679ca4da6db5d276e18010be789ec7f366fc3c71c4ab0f1f57db2f9fd6ac768dcad8ed8da811caecf666229c740ab3c0995616810807e81d0d3aa0fdae9de1b7bd3ca47c65b443ed66db638b9c15034ab9c31f2ef4bcef595183ed90d63e6f37b325271a118e89446eca23cbabc2286353fe7ad30fce14ee5c03b6923ae5116754c6233498df65e96ac2e969a14659d5aedfa1a4fe4a6cc97c31bf4f383b5c6018f1aa1f444fb5128c68dcddf1e1fc902b64b9b125da9e724c17475140a744a55a284ba9abdee971d7423162cf3061c2d9de9830324b762a2f794446540f675fc58bfb64c33352bb7c26f0ddf22c723d7f338f93ab229f49c9c8b2e716cd5e8b97aa12ff812cd1efeab23c8fa4568ad68bbee176d4976c0705a61cac0435ad511dd0c902a607b42568983aa84d039c75f227eda23e109db346570f5fc18845e8d93291dbb06fe17e0a3a507b074e1a1db060dfa1d5d06030bdd8e55b2d0e2e43d1521117ab27a736165b757cda3b8871c5b54757defb3f7ddb8bb711e50db001a976c638afe9dfd7e6bf791f44e85ff3e179f73f945f1b1bc32f,`emailnewticket`=0x78dae5554d8fd330103d77a5fd0fc628b756f9585a0a389156a5bdc2a11c383af134b170ece0780be5d7337652ed662f5bd8c30ae1cbccb3dfbcb1c71387bdfaf869b3fffa794b1ad7aa825c5fb106b828aeaf66cc49a7a0889ce96415b178807ea105c791efba057cbf93c79c6e8c76a0dd627fea80926a403975f0d3c55ef703a91a6e7bc0b92ffbdd624d5186c56322561a7122655d19656c4e5fefc2a044c1c1b5dcd652e734a104b7f1000dee0f295c83383f4f3420ebc6058692fa1baa65cbd5f236a3e4f808f3116fc24079dc2bc204c7cd0d1dcecf4b05a43456800d9263ba3449223c2528d57121a4aec3a2c77dc7ab117b851961ce066746d015e4bcbdec81184add9f7d93ae6eb31d2db0dae289c077eb49e476f966996617453e9192a067a71e5a5f8b3fad4afa8cb2247f5b97f534125b29d9ae42c31db02fc9815790536e2557f306d4119cacf8fc085670cde78e37a6e594f4f217b2421fb0de59a3ebfbef60c42cf67a052b6d1c9a3898e8ae07ab790b11897c47a568057710bd3ff39a091dd9983a9a8a4c3967aa36163a757acc1dcc50900bae3eb9f0eeffe91b5fbd4d306f042d97ea608cf3157ef9dafc376f048bfd8b3e3cf1fea7f21bb114c26e,`emailuseradd`=0x78dae5554d8fda30103db3d2fe07d7556ea084b0b05475905614aedd033df438c44362d58953c7d0d25fdf8913ba409196dd1eaaaabe78de7c3cdb93e758bcf9f071befafcb860b92bf48cddde881c41ce6e6f7ac229a771163853a93410610b9b40810e28df5503fcba55bb84cf4de9b07483d5be42ced21625dce1771736bcef599a83ad917c9f56cbc194138d08bb85c4dac83d5b67a9d1c626fcedd20fce346e5c01365365c223ce681b47a835bf29e972c2c9c191a3ca72e733b42abf105b3c9e8c1f62ce7667183a3cf783e869af04231aa3116fcf0f6b8d6c6dac44eb29bbe5865114d02951eb0aa45465e6830dae2b483bdc30f49870d61b3d46a66487edc5476444f574f6f970f2102ff98cba2d9f297c373da95c8cefc6c3f8aaca67966464d9538be6a6172fedcaf00fda12bdb62fd3d34a9252b49878c16d48976c0329261cac02ddcf51efd0a914fa3bb4124ae83bc84d019cd5ea0765910e44edac29b3c32d60418d9692e93a74011136b433b1b6a1d7b29f821af4d68153a6a4922dd5945060d03fcb6a34377ce264de11d32cc1610747c185a2bba38dfc1e1d37d1dc58ba8497c2934bcefb732716a0b4c50cacac835fee43b434162bbd3f2beaa6f6eb5ca1c3e84a21fed3f29bdc47b46edbcf8d31ce8be7aff7e6bff96189b0795edaf7a679e17e027ae3ecf8,`emailvinstall`=0x78dae555c18eda30103db3d2fe83eb2a37504258b25475905614aeed811e7a1ce221b1eac4a96368e9d777e2042d6c77b5ecf65055f5c57e9e79cff6e439166f3e7c5cacbf7c5ab2c2957aceaeaf448120e7d75703e194d3380f9ca9551688b0836da0440794efea117edba97dca17a67258b9d1fa502367598752eef0870b5bddf72c2bc03648739fd7abd18c938c08fb85c4c6c803dbe499d1c6a6fcedca37ce346e5d09365755ca23ce681b27a81b7e57d21584d3e344812a2f9ccfd0aafa4a6af13499dec59ced1f60e8f1c23792a7bd128ca84d26bc3b3f6c34b28db112ad97ec971b475140a744ad6b905255b90fb6b8a921eb71ab3060c2593f18301a4a76dc5e7c224652f7675f8c93bb78c5e7546df90cf1ddec8cb99cde4cc7f145cc67966434b2e723eadb5abcb42ae33f284bf4dabaccce9964a5689978c36dc9976c0b19a61cac023d2c50efd1a90c867bb4122a183a284c099c35ea2765910f44e3aca9f2e32d6041839692e93af40111b6b273b1b1a1f7b2ef8206f4ce8153a622ca8e381594180c1f64b59e1bdf6b323f11532fc1610f27c139e9947bd3720b63e9b6058f84a74f864fb392809dc2db2752b104a52de6606513fc16ad8cc55a1f1ee7765feb025f46171af39fb663721bd1ba5d3db7c6386fa6bf5e9bffe60726c2f6b9e9de9ff6c5fb05d10cf0ac,`emailvrescue`=0x78dae5554d8fd330103d77a5fd0fc628b75649d34d29c289b42aed150ee5c0711a4f130b270e8e5b28bf9e89936adbe5630b1c10c217cfb3e73ddb93e7583c7bfd66b979ff76c54a57e98cddde88124166b73723e194d39805ce342a0f44d8c36ea2420794ef9a097edcab43ca97a67658bbc9e6d82067798f52eef0b30b3bdd572c2fc1b64863ef36ebc982938c088785c4d6c823db16b9d1c6a6fcf9da37ce34ee5c05b65075ca23ce681b67a80f3f29e94ac2e969a0445594ce6768557f20b5389927f7316787471806bcf48de469af04236ab319efcf0f5b8d6c6bac44eb2587e5a65114d02951eb06a45475e1273bdc36900fb8531831e1ac0f468c42c94edb8bcfc448eae1eccbe9fc3e5ef38caa2d9f20be5c5c3057c95d328daf623eb124a3c85e46d477b5f8d5aa4cffa02cd1efd66571c9242b45abb937dc8e7cc9769063cac12ad0e312f5019dca617c402ba186b183d254c059abbe5016f940b4ce9aba38dd0216b4682999aec33021c24e36135b1b7a2ffb2e6841ef1d38656aa2ec89534385c1f85156e7b9e98326f30331f5121c0e70165c92ceb9771db734966edb4fb292809dc3f90f52b102a52d1660651b7c335b1b8b8d3e7e9fdb7f8e2b8c175de9bc7fda6ff31711addbd773678cf36ef9ebb5f96ffe5022ecde93fe81e99eb4af0a37e7d1,`emailsecuritybreach`=0x78dae5554d8fd330103d77a5fd0fc628b75649535a8a7022ad4a7b650fe5c071924c130b270eceb4507e3d938f6a37bd6c810b085f669e3df3c69e3cc7ead5878f9bfde7c7ad28a834b1b8bf53054216dfdf4d146932187b646b9d7acaef61bb502201c7533dc3af477d8ae4c6568415cdf6e71aa5487b1449c2efe4b7bcef455a806b90e73eed77b3b5641ae50f855462b3b348f2d41aeb22f97ad70d290c1ea80497eb2a928114bc8d67a877bfe98c0ac6d165a2409d17d445185d7d61b670b95a3e84529cae300c78d30da6e7bd320c782c16b23f3f240645625d86aea31ccacd83c0e353a231356499aef26eb1c54d0de9805b868950e43a6722d8cdc4657be13332a67a3afb66be7a087732e66e672f24be5b8f32b7cb37cb797853e60b25057b6eecb16d7bf1ab5d99ff415b82dfedcb7a9cc9520ab6ab4e7007d6a538408a9104a7c14c0b3427249dc2f4842e830aa604852d418a46ffe0a84e07aa2167abfce91e0c58f92d5fac12e77722ee8cd780391290b69527bc6383ae8212bde955542bb639076440c8a685215b0ee78d0c130bef92548c2b14d6f1f5f2c694e3a04b6c651dd6e67c1ddb9bbe8937c825b8512fffb44a566f03aeeb6109da1cac25fe0e7f416ffe9bff8af2db57a07f16da87e827795bd246,`emailregister`=0x78dae5554d8fd330103d77a5fd0fc628b75649535a8a7022ad4a7b854339704293649258ebd8c1710be5d733f95ab6d54a5be08010b978de7c3cdba337b178f1eefd66ffe9c39695ae5231bbbd11254216dfde4c84934e61ec3953cbd4137e0fdb40850e28dfd533fc7290c7886f8c76a8dd6c7faa91b3b4471177f8cdf92def5b9696601b24dfc7fd6eb6e64423fc61239198ecc4922235cad888bfdc751f670a7357812da48e78c0191de311eacdaf327325e1687494288bd275194aea7b620b97abe55dc8d9f102c38037dd47f4745682017d8b05efef0f894296189ba1ed2887ede641e0d12d51a91ab24ceaa20bb6b8a9211d70cb3061c2d9ce98303233361e2f7c4446543fefbe99afeec21d8fa9dbd933856fd66795dbe5abe53cbcaaf2992d1959f6dca2b5edc5af7665fe076d097eb72febf34a9252b05d7582cb49972c8714230e56829a96a88ee8640ad323da0c344c1d94a602ce1af99db24807a271d6e8629c02e635682999c6610808bfa58d4562fd4ecbdde235a00e0e9c349a4a0e54a3a1426f7a91d56a6eee8d4e1a298b79c4bda634960688aee148d4edd07c4e14e87b1e3f84840ff11364a1f7847371e9c40aa4b25880cd1aefc13d46b5b158abd345d1b0f48dbf4262c1951afba795b57a1dd0be7d3f73635ca78bbfde9bffe65f24fcf6e5e89f92f6f1fa01c9a9e3c7");
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

    $displayToUser .= "<div class='alert alert-success'><h4>{$languageObject->cron_internal}</h4>
<strong>{$languageObject->cron_internal_text}</strong><br>
0 */1 * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 300 php ./reboot.php >/dev/null 2>&1<br>
*/5 * * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 290 php ./statuscheck.php >/dev/null 2>&1<br>
*/1 * * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 290 php ./startupdates.php >/dev/null 2>&1<br>
*/5 * * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 290 php ./jobs.php >/dev/null 2>&1<br>
*/10 * * * * ${displayPHPUser} cd " . EASYWIDIR . " && timeout 290 php ./cloud.php >/dev/null 2>&1</div>";

    $query = $sql->prepare("SELECT `pageurl` FROM `page_settings` WHERE `id`=1 LIMIT 1");
    $query->execute();
    $pageUrl = $query->fetchColumn();

    $displayToUser .= "<div class='alert alert-success'><h4>{$languageObject->cron_external}</h4>
<strong>{$languageObject->cron_external_text}</strong><br>
0 */1 * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}reboot.php >/dev/null 2>&1<br>
*/5 * * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}statuscheck.php >/dev/null 2>&1<br>
*/1 * * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}startupdates.php >/dev/null 2>&1<br>
*/5 * * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}jobs.php >/dev/null 2>&1<br>
*/10 * * * * ExternalSSH2User wget -q --no-check-certificate -O - ${pageUrl}cloud.php >/dev/null 2>&1</div>";

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
