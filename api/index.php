<?php
require_once 'inti.php';
require_once '../stuff/config.php';
require_once '../stuff/keyphrasefile.php';
require_once '../stuff/methods/functions.php';


die();

try {
    $dbConnect['type'] = "mysql";
    $dbConnect['host'] = $host;
    $dbConnect['user'] = $user;
    $dbConnect['pwd'] = $pwd;
    $dbConnect['db'] = $db;
    $dbConnect['charset'] = "utf8";

    $dbConnect['connect'] = "{$dbConnect['type']}:host={$dbConnect['host']};dbname={$dbConnect['db']};charset={$dbConnect['charset']}";
    $sql = new \PDO($dbConnect['connect'], $dbConnect['user'], $dbConnect['pwd']);
} catch (PDOException $error) {
    die($error->getMessage());
}


$query = $sql->prepare("INSERT INTO `userdata` (`creationTime`,`updateTime`,`active`,`salutation`,`birthday`,`country`,`fax`,`cname`,`security`,`name`,`vname`,`mail`,`phone`,`handy`,`city`,`cityn`,`street`,`streetn`,`fdlpath`,`accounttype`,`mail_backup`,`mail_gsupdate`,`mail_securitybreach`,`mail_serverdown`,`mail_ticket`,`mail_vserver`,`language`) VALUES (NOW(),NOW(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
$query->execute(array(
    $active = 'Y',
    $salutation = '1',
    $birthday = null,
    $country = null,
    $fax = null,
    $cname = 'ali',
    $password = '37823814',
    $name = 'ali',
    $vname = 'ali',
    $mail = 'temp@mail.temp',
    $phone = null,
    $handy = null,
    $city = null,
    $cityn = null,
    $street = null,
    $streetn = null,
    $fdlpath = null,
    $accountType = 'u',
    $mail_backup = 'N',
    $mail_gsupdate = 'N',
    $mail_securitybreach = 'N',
    $mail_serverdown = 'N',
    $mail_ticket = 'N',
    $mail_vserver = 'N',
    $language = 'uk'
));

$id = $sql->lastInsertId();

$cname = $cname . $id;

// $newHash = passwordCreate($cname, $password);
$newHash = passwordCreate('ali', '37823814');

$query = $sql->prepare("UPDATE `userdata` SET `cname`=?,`security`=?,`resellerid`=0 WHERE `id`=? LIMIT 1");
$query->execute(array($cname, $newHash, $id));























die();

require_once 'inti.php';
$router = new \Bramus\Router\Router();



$router->mount('/application', function () use ($router) {

    $router->mount('/addons', function () use ($router) {


        $router->get('/list', function () {
            $callApi = new addons();
            echo $callApi->getAddonsList();
        });
        $router->post('/add', function () {
            $callApi = new addons();
            $this->callApi->insertAddon(
                $_POST['type'],
                $_POST['addon'],
                $_POST['paddon'],
                $_POST['folder'],
                $_POST['active'],
                $_POST['menudescription'],
                $_POST['configs'],
                $_POST['cmd'],
                $_POST['rmcmd'],
                $_POST['depending']
            );
        });

        $router->patch('/update', function () {
            $callApi = new addons();
            $result = $this->callApi->updateAddon(
                $_POST['id'],
                $_POST['menudescription'],
                $_POST['active'],
                $_POST['folder'],
                $_POST['addon'],
                $_POST['paddon'],
                $_POST['type'],
                $_POST['configs'],
                $_POST['cmd'],
                $_POST['rmcmd'],
                $_POST['depending']
            );

            if ($result === TRUE) {
                echo "Addon updated successfully";
            } else {
                echo "Error updating addon";
            }
        });

        $router->delete('/delete', function () {
            $callApi = new addons();
            $this->callApi->deleteAddon($_POST['id']);
        });
    });
});





$router->set404(function () {
    http_response_code(405);
    header('HTTP/1.1 405 Method Not Allowed');
    $jsonArray['status'] = '405';
    $jsonArray['status_text'] = '405 Method Not Allowed';
    echo json_encode($jsonArray);
});


$router->run();
