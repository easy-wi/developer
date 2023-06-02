<?php



var_dump(getBearerToken());


die();


require_once 'inti.php';
$router = new \Bramus\Router\Router();


$router->mount('/application', function () use ($router) {

    $router->mount('/addons', function () use ($router) {

        $router->get('/list', function () {

            getAddonsList();
        });
        $router->post('/add', function () {

            insertAddon(
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

            updateAddon(
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
        });

        $router->delete('/delete', function () {

            deleteAddon($_POST['id']);

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
