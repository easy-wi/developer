<?php

require './vendor/autoload.php';
define('EASYWIDIR', '../');
require_once '../stuff/keyphrasefile.php';
require_once '../stuff/config.php';
require_once './function.php';
require_once '../stuff/methods/class_app.php';
require_once '../stuff/methods/functions.php';
require_once '../stuff/methods/class_ftp.php';
require_once '../stuff/methods/functions_gs.php';
require_once '../stuff/methods/functions_ssh_exec.php';



$dbConnect['type'] = "mysql";
$dbConnect['host'] = $host;
$dbConnect['user'] = $user;
$dbConnect['pwd'] = $pwd;
$dbConnect['db'] = $db;
$dbConnect['charset'] = "utf8";


try {
    $dbConnect['connect'] = "{$dbConnect['type']}:host={$dbConnect['host']};dbname={$dbConnect['db']};charset={$dbConnect['charset']}";
    $sql = new \PDO($dbConnect['connect'], $dbConnect['user'], $dbConnect['pwd']);
} catch (PDOException $error) {
    die($error->getMessage());
}



// require_once '../stuff/api/api_list.php';
// require_once '../third_party/phpseclib/autoloader.php';
// require_once '../third_party/password_compat/password.php';
