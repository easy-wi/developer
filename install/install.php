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

require_once(EASYWIDIR . '/stuff/methods/functions.php');
require_once(EASYWIDIR . '/stuff/methods/vorlage.php');

$currentStep = (isset($_GET['step']) and $_GET['step'] > 0 and $_GET['step'] < 10) ? (int) $_GET['step'] : 0;
$developer = (isset($_GET['developer']) and $_GET['developer'] == 'Y') ? 'Y' : 'N';
$developerGetParameter = '&amp;developer=' . $developer;
$progressPercent = (100 / 9) * $currentStep ;
$acceptLanguage = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0 , 2));
$fallbackLanguage = (file_exists(EASYWIDIR . '/install/' . $acceptLanguage . '.xml')) ? $acceptLanguage : 'en';
$menuLanguage = (isset($_GET['language']) and strlen($_GET['language']) == 2 and file_exists(EASYWIDIR . '/install/' . $_GET['language'] . '.xml')) ? $_GET['language'] : $fallbackLanguage;
$languageGetParameter = '&amp;language=' . $menuLanguage;
$languageObject = simplexml_load_file(EASYWIDIR . '/install/' . $menuLanguage . '.xml');

$displayToUser = '';
$systemCheckOk = array();
$systemCheckError = array();
$easyWiVersion = ($developer == 'Y') ? '6.0.4' : '6.0.4';

if ($currentStep == 0) {

    $selectedDeveloper = ($developer == 'Y') ? "selected='selected'" : '';

    $apiResponse = webhostRequest('api.github.com', $_SERVER['HTTP_HOST'], '/repos/easy-wi/developer/' . (($developer == 'Y') ? 'tags' : 'releases/latest'), null, 443);
    $json = @json_decode($apiResponse);

    $displayToUser = "
<div class='col-md-9'>
<form class='form-horizontal' role='form' action='install.php' method='get'>
  <input type='hidden' name='step' value='0' />
  <input type='hidden' name='language' value='${menuLanguage}' />
  <div class='form-group'>
    <label for='inputVersion' class='col-sm-2 control-label'>Version</label>
    <div class='col-sm-10'>
      <select class='form-control' name='developer' onChange='this.form.submit()'>
        <option value='N'>Stable</option>
        <option value='Y' ${selectedDeveloper}>Developer</option>
      </select>
    </div>
  </div>
</form>
</div>
";

    if (!$json or ($developer == 'N' and !is_object($json)) or ($developer == 'Y' and !is_array($json)) or ($developer == 'N' and property_exists($json, 'tag_name') and $easyWiVersion == $json->tag_name) or ($developer == 'Y' and is_object($json[0]) and property_exists($json[0], 'name') and $easyWiVersion == $json[0]->name )) {
        $displayToUser .= "<div class='col-md-12'><div class='jumbotron'><h2>{$languageObject->welcome_header}</h2><p>{$languageObject->welcome_text}</p><div class='pager'><a href='?step=1${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div></div></div>";
    } else {
        $apiVersion = ($developer == 'Y') ? $json[0]->name : $json->tag_name;
        $displayToUser .= "<div class='col-md-12'><div class='alert alert-warning'><i class='fa fa-exclamation-triangle'></i> {$languageObject->welcome_old_version}<a href='https://github.com/easy-wi/developer/releases/tag/{$apiVersion}' target='_blank'>{$apiVersion}</a></div><div class='jumbotron'><h2>{$languageObject->welcome_header}</h2><p>{$languageObject->welcome_text}</p><div class='pager'><a href='?step=1${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div></div></div>";
    }

} else {

    if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
        $systemCheckOk['php'] = $languageObject->system_ok_php_version . PHP_VERSION;
    } else {
        $systemCheckError['php'] = $languageObject->error_system_php_version . PHP_VERSION;
    }

    $modules = array('openssl', 'json', 'hash', 'ftp', 'SimpleXML', 'curl', 'gd', 'PDO', 'pdo_mysql', 'zip');

    foreach ($modules as $module) {
        if (extension_loaded($module)) {
            $okField = "system_ok_$module";
            $systemCheckOk[$module] = $sprache->$okField;
        } else {
            $errorField = "error_system_$module";
            $systemCheckError[$module] = $sprache->$errorField;
        }
    }

    $functions = array('fopen');

    foreach ($functions as $function) {
        if (function_exists($function)) {
            $okField = "system_ok_$function";
            $systemCheckOk[$function] = $function->$okField;
        } else {
            $errorField = "error_system_$function";
            $systemCheckError[$function] = $sprache->$errorField;
        }
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

}

if ($currentStep == 2 and count($systemCheckError) == 0) {

    $host = 'localhost';
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

" . '$host' . " = '" . addcslashes($_POST['host'], "'") . "';
" . '$user' . " = '" . addcslashes($_POST['user'], "'") . "';
" . '$db' . " = '" . addcslashes($_POST['db'], "'") . "';
" . '$pwd' . " = '" . addcslashes($_POST['pwd'], "'") . "';
" . '$captcha' . " = 0;
" . '$title' . " = '';
" . '$debug' . " = 0;
" . '$timezone' . " = 'Europe/Berlin';
";

                @fwrite($configFp, $configdata);

                fclose($configFp);

            } else {
                $systemCheckError['config.php'] = "<div class='alert alert-danger'>{$languageObject->error_config_php_data}</div>";
            }

            $keyFp = @fopen(EASYWIDIR . '/stuff/keyphrasefile.php', "w+");

            if ($keyFp) {
                $configdata='<?php

// This file was generated by the easy-wi.com installer

$aeskey = "' . addcslashes($_POST['aeskey'], "'") . '";
';

                @fwrite($keyFp, $configdata);

                fclose($keyFp);
            } else {
                $systemCheckError['keyphrasefile.php'] = "<div class='alert alert-danger'>{$languageObject->error_keyphrase_php_create}</div>";
            }

        } catch(PDOException $error) {

            $systemCheckError['db_connect'] = "<div class='alert alert-danger'>{$error->getMessage()}</div>";

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

                    $systemCheckError['db_connect'] = "<div class='alert alert-danger'>{$error->getMessage()}</div>";

                }

            } else {
                $systemCheckError['config.php'] = "<div class='alert alert-danger'>{$languageObject->error_config_php_data}</div>";
            }

        } else {

            if (isset($_POST['db'])) {
                $systemCheckError['keyphrasefile.php'] = "<div class='alert alert-danger'>{$languageObject->error_keyphrase_php_create}</div>";
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

        $response = new UpdateResponse();

        require_once(EASYWIDIR . '/stuff/methods/class_tables.php');

        $tables = new Tables($db);
        $tables->createMissingTables();

        foreach($tables->getExecutedSql() as $change){
            if ($change) {
                $response->add($change . '<br>');
            }
        }

        if (strlen($response->response) > 0) {

            $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_db_tables_create}</div>";
            $displayToUser .= "<div class='alert alert-success'>{$response->response}</div>";

        } else {
            $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_db_tables_create}</div>";
        }

        $displayToUser .= "<div class='pager'><a href='?step=5${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";

    } catch(PDOException $error) {
        $systemCheckError['tables_add.php'] = "<div class='alert alert-danger'>{$error->getMessage()}" . implode("\r\n", $tables->getExecutedSql()) . "</div>";
    }
}

if ($currentStep == 5 and count($systemCheckError) == 0) {

    try {

        $response = new UpdateResponse();

        require_once(EASYWIDIR . '/stuff/methods/class_tables.php');

        $tables = new Tables($db);
        $response->add('Repairing tables if needed.<br>');
        $tables->correctTablesStatus();
        $tables->correctExistingTables();

        foreach($tables->getExecutedSql() as $change){
            $response->add($change . '<br>');
        }

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

        if ($_POST['passw1'] != $_POST['passw2'] or !preg_match('/^[\w\S]{6,120}$/', $_POST['passw1'])) {
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
        $installUrl = 'http://' . $_SERVER['SERVER_NAME'] . '/';
    }

    if (strlen($_SERVER['SERVER_NAME']) == 0 and isset($_SERVER['HTTP_REFERER'])) {
        $installUrl = str_replace(array('&language=de', '&language=en', '&language=dk'), '', str_replace('install/install.php?step=6', '', $_SERVER['HTTP_REFERER']));
    }

    while (substr($installUrl, -2) == '//') {
        $installUrl = substr($installUrl, 0, strlen($installUrl) -1 );
    }

    $defaultTimeZone = (@ini_get('date.timezone') != "") ? ini_get('date.timezone') : 'Europe/Berlin';

    $query = $sql->prepare("SELECT `mail` FROM `userdata` WHERE `id`=1");
    $query->execute();
    $email = $query->fetchColumn();

    $query = $sql->prepare("SELECT `language`,`prefix1`,`prefix2`,`faillogins`,`brandname` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $language = $row['language'];
        $prefix1 = $row['prefix1'];
        $prefix2 = $row['prefix2'];
        $faillogins = $row['faillogins'];
        $brandname = $row['brandname'];
    }

    $query = $sql->prepare("SELECT `email_setting_value` FROM `settings_email` WHERE `reseller_id`=0 AND `email_setting_name`='email' LIMIT 1");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $email = $row['email_setting_value'];
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
    <select id='inputInstallTimezone' class='form-control' name='timezone'>";

        foreach (timezone_identifiers_list() as $time) {
            $displayToUser .= ($time == $defaultTimeZone) ? "<option selected='selected'>{$time}</option>" : "<option>{$time}</option>";
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

            $query = $sql->prepare("INSERT INTO `settings` (`id`,`template`,`language`,`prefix1`,`prefix2`,`faillogins`,`brandname`,`developer`,`cronjob_ips`,`imageserver`,`resellerid`) VALUES (1,'default',?,?,?,?,?,?,'','images.easy-wi.com::easy-wi',0) ON DUPLICATE KEY UPDATE `language`=VALUES(`language`),`prefix1`=VALUES(`prefix1`),`prefix2`=VALUES(`prefix2`),`faillogins`=VALUES(`faillogins`),`brandname`=VALUES(`brandname`),`developer`=VALUES(`developer`),`imageserver`=VALUES(`imageserver`)");
            $query->execute(array($_POST['language'], $_POST['prefix1'], $_POST['prefix2'], $_POST['faillogins'], $_POST['brandname'], $developer));

            $query = $sql->prepare("INSERT INTO `settings_email` (`reseller_id`,`email_setting_name`,`email_setting_value`) VALUES (0,'email',?) ON DUPLICATE KEY UPDATE `email_setting_value`=VALUES(`email_setting_value`)");
            $query->execute(array($_POST['email']));

            $query = $sql->prepare("INSERT INTO `eac` (`id`,`resellerid`) VALUES (1,0) ON DUPLICATE KEY UPDATE `resellerid`=`resellerid`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `lendsettings` (`id`,`resellerid`) VALUES (1,0) ON DUPLICATE KEY UPDATE `resellerid`=`resellerid`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `traffic_settings` (`id`,`type`) VALUES (1,'mysql') ON DUPLICATE KEY UPDATE `type`=`type`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `easywi_version` (`id`,`version`,`de`,`en`) VALUES (1,'${easyWiVersion}','','') ON DUPLICATE KEY UPDATE `id`=`id`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `page_pages` (`id`,`authorid`,`type`) VALUES (1,0,'about') ON DUPLICATE KEY UPDATE `id`=`id`");
            $query->execute();

            $query = $sql->prepare("INSERT INTO `feeds_settings` (`settingsID`,`resellerID`) VALUES (1,0) ON DUPLICATE KEY UPDATE `settingsID`=`settingsID`");
            $query->execute();
            
            $query = $sql->prepare("INSERT INTO `settings_email_category` (`id`, `name`, `reseller_id`) VALUES (1, 'vServer', 0), (2, 'Server', 0), (3, 'Ticket', 0), (4, 'General', 0), (5, 'VoiceServer', 0), (6, 'GameServer', 0) ON DUPLICATE KEY UPDATE `id`=`id`");
            $query->execute();
           
            //Install E-Mail template
            include(EASYWIDIR . '/stuff/data/email_templates.php');
            foreach ($emailTemplates as $template) {
                $query = $sql->prepare($template['query']);
                $query->execute(array(0));
            }


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

            include(EASYWIDIR . '/stuff/data/gameslist.php');

            $displayToUser .= "<div class='pager'><a href='?step=9${languageGetParameter}' class='pull-right'><span class='btn btn-primary btn-lg'>{$languageObject->continue}</span></a></div>";
            $displayToUser .= "<div class='alert alert-success'>{$languageObject->ok_gameserver_data}</div>";

            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=0 LIMIT 1");
            $query2 = $sql->prepare("INSERT INTO `servertypes` (`type`,`steamgame`,`appID`,`serverID`,`updates`,`shorten`,`description`,`gamebinary`,`gamebinaryWin`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`gameq`,`gamemod`,`gamemod2`,`configs`,`configedit`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`useQueryPort`,`mapGroup`,`protected`,`protectedSaveCFGs`,`ramLimited`,`os`,`copyStartBinary`,`steamGameserverToken`) VALUES ('',:steamgame,:appID,:serverID,:updates,:shorten,:description,:gamebinary,:gamebinaryWin,:binarydir,:modfolder,:fps,:slots,:map,:cmd,:modcmds,:tic,:gameq,:gamemod,:gamemod2,:configs,:configedit,:portStep,:portMax,:portOne,:portTwo,:portThree,:portFour,:portFive,:useQueryPort,:mapGroup,:protected,:protectedSaveCFGs,:ramLimited,:os,:copyStartBinary,:steamGameserverToken)");
            $query3 = $sql->prepare("UPDATE `servertypes` SET `steamgame`=:steamgame,`appID`=:appID,`serverID`=:serverID,`updates`=:updates,`shorten`=:shorten,`description`=:description,`gamebinary`=:gamebinary,`gamebinaryWin`=:gamebinaryWin,`binarydir`=:binarydir,`modfolder`=:modfolder,`fps`=:fps,`slots`=:slots,`map`=:map,`cmd`=:cmd,`modcmds`=:modcmds,`tic`=:tic,`gameq`=:gameq,`gamemod`=:gamemod,`gamemod2`=:gamemod2,`configs`=:configs,`configedit`=:configedit,`portStep`=:portStep,`portMax`=:portMax,`portOne`=:portOne,`portTwo`=:portTwo,`portThree`=:portThree,`portFour`=:portFour,`portFive`=:portFive,`useQueryPort`=:useQueryPort,`mapGroup`=:mapGroup,`protected`=:protected,`protectedSaveCFGs`=:protectedSaveCFGs,`ramLimited`=:ramLimited,`os`=:os,`copyStartBinary`=:copyStartBinary,`steamGameserverToken`=:steamGameserverToken WHERE `shorten`=:shorten AND `resellerid`=0 LIMIT 1");

            foreach ($gameImages as $image) {

                if (count($image) == 36) {

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
                    $displayToUser .= "<div class='alert alert-danger'>{$languageObject->error_game_insert} " . count($image) . " ${image[':description']}</div>";
                }

            }

            require_once(EASYWIDIR . '/stuff/data/addonslist.php');

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
                            $dependsID = (int) $query->fetchColumn();
                        }

                        $query2->execute(array($dependsID, $addon[':paddon'], $addon[':addon'], $addon[':type'], $addon[':folder'], $addon[':menudescription'], $addon[':configs'], $addon[':cmd'], $addon[':rmcmd']));

                        $addonID = (int) $sql->lastInsertId();

                        if (preg_match('/^[\d]{1,}$/', $addonID) and $addonID != 0) {
                            foreach ($addon[':supported'] as $supported) {

                                $query3->execute(array($supported));
                                $serverTypeID = (int) $query3->fetchColumn();

                                if (preg_match('/^[\d]{1,}$/', $serverTypeID) and $serverTypeID != 0) {
                                    $query4->execute(array($addonID, $serverTypeID));
                                }
                            }
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

    // Deactivate CMS module by default
    $query = $sql->prepare("SELECT `id` FROM `modules` WHERE `get`='pn' LIMIT 1");
    $query->execute();
    $cmdModuleId = (int) $query->fetchColumn();

    if ($cmdModuleId > 0) {
        $query = $sql->prepare("UPDATE `modules` SET `active`='N' WHERE `id`=? LIMIT 1");
        $query->execute(array($cmdModuleId));
    } else {
        $query = $sql->prepare("INSERT INTO `modules` (`get`,`sub`,`file`,`active`,`type`) VALUES ('pn','pn','','N','C')");
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
    <meta name="description" content="Easy-WI Installer <?php echo $easyWiVersion;?>">
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
t
<div class="container">
    <div class="header">
        <ul class="nav nav-pills pull-right">
            <li><a href="https://twitter.com/EasyWI" target="_blank"><i class="fa fa-twitter fa-fw"></i> Twitter</a></li>
            <li><a href="https://github.com/easy-wi/developer" target="_blank"><i class="fa fa-github fa-fw"></i> Github</a></li>
            <li><a href="https://easy-wi.com/forum/" target="_blank" title="easy-wi.com forum"><i class="fa fa-comments fa-fw"></i> Forum</a></li>
            <li><a href="https://easy-wi.com/wiki/" target="_blank" title="easy-wi.com wiki"><i class="fa fa-question-circle fa-fw"></i> Wiki</a></li>
            <li><a href="?step=<?php echo $currentStep . $developerGetParameter;?>&amp;language=de"><img src="../images/flags/de.png"></a></li>
            <li><a href="?step=<?php echo $currentStep . $developerGetParameter;?>&amp;language=en"><img src="../images/flags/uk.png"></a></li>
            <li><a href="?step=<?php echo $currentStep . $developerGetParameter;?>&amp;language=dk"><img src="../images/flags/dk.png"></a></li>
        </ul>
        <h3 class="text-muted">Easy-WI.com Installer <?php echo $easyWiVersion;?></h3>
    </div>

    <div class="row">
        <div class="col-md-3">
            <ul class="nav nav-pills nav-stacked">
                <li <?php if ($currentStep == 0) echo 'class="active"'; ?>><a href="?step=0<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-info-circle fa-fw"></i> <?php echo $languageObject->menu_welcome;?></a></li>
                <li <?php if ($currentStep == 1) echo 'class="active"'; ?>><a href="?step=1<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-stethoscope fa-fw"></i> <?php echo $languageObject->menu_system;?></a></li>
                <li <?php if ($currentStep == 2) echo 'class="active"'; ?>><a href="?step=2<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-key fa-fw"></i> <?php echo $languageObject->menu_db_access;?></a></li>
                <li <?php if ($currentStep == 3) echo 'class="active"'; ?>><a href="?step=3<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-stethoscope fa-fw"></i> <?php echo $languageObject->menu_db_access_check;?></a></li>
                <li <?php if ($currentStep == 4) echo 'class="active"'; ?>><a href="?step=4<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-tasks fa-fw"></i> <?php echo $languageObject->menu_db_add;?></a></li>
                <li <?php if ($currentStep == 5) echo 'class="active"'; ?>><a href="?step=5<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-eye fa-fw"></i> <?php echo $languageObject->menu_db_check;?></a></li>
                <li <?php if ($currentStep == 6) echo 'class="active"'; ?>><a href="?step=6<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-user fa-fw"></i> <?php echo $languageObject->menu_admin_add;?></a></li>
                <li <?php if ($currentStep == 7) echo 'class="active"'; ?>><a href="?step=7<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-cogs fa-fw"></i> <?php echo $languageObject->menu_page_data;?></a></li>
                <li <?php if ($currentStep == 8) echo 'class="active"'; ?>><a href="?step=8<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-upload fa-fw"></i> <?php echo $languageObject->menu_gamedata_add;?></a></li>
                <li <?php if ($currentStep == 9) echo 'class="active"'; ?>><a href="?step=9<?php echo $languageGetParameter . $developerGetParameter;?>"><i class="fa fa-smile-o fa-fw"></i> <?php echo $languageObject->menu_finish;?></a></li>
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
