<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SebastianBergmann\Type\Parameter;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate app
$app = AppFactory::create();
$app->addErrorMiddleware(false, false, false);


// $app->add(new Tuupola\Middleware\JwtAuthentication([
// 	"secret" => ""
// ]));

// Add Error Handling Middleware


require_once '../inc/Class.WiApi.php';
// require_once '../inc/config.php';


// Add route callbacks
// $app->get('/api/', function (Request $request, Response $response, array $args) {
//     $response->getBody()->write('Hello World');
//     return $response;
// });


$app->post('/api/application/addons/add', function (Request $request, Response $response) {
	$callApi = new addons();
	$params = $request->getQueryParams();
	/**	
	 * string $type,
	 * string $addon,
	 * string $paddon,
	 * string $folder,
	 * string $active,
	 * string $menudescription,
	 * string $configs,
	 * string $cmd,
	 * string $rmcmd,
	 * string $depending 
	 */
	$result = $callApi->insertAddon(
		$params['type'],
		$params['addon'],
		$params['paddon'],
		$params['folder'],
		$params['active'],
		$params['menudescription'],
		$params['configs'],
		$params['cmd'],
		$params['rmcmd'],
		$params['depending']
	);
	$response->getBody()->write($result);
	return $response;
});

$app->put('/api/application/addons/update', function (Request $request, Response $response) {
	$callApi = new addons();
	$params = $request->getQueryParams();
	/**
	 *  int $id,
	 *  string $menudescription,
	 *  string $active,
	 *  string $folder,
	 *  string $addon,
	 *  string $paddon,
	 *  string $type,
	 *  string $configs,
	 *  string $cmd,
	 *  string $rmcmd,
	 *  string $depending
	 */
	$result = $callApi->updateAddon(
		$params['id'],
		$params['menudescription'],
		$params['active'],
		$params['folder'],
		$params['addon'],
		$params['paddon'],
		$params['type'],
		$params['configs'],
		$params['cmd'],
		$params['rmcmd'],
		$params['depending']
	);

	$response->getBody()->write($result);
	return $response;
});

$app->delete('/api/application/addons/delete', function (Request $request, Response $response) {
	$callApi = new addons();
	$id = $request->getQueryParams()['id'];
	$response->getBody()->write($callApi->deleteAddon($id));
	return $response;
});

$app->get('/api/application/addons/list', function (Request $request, Response $response) {
	$callApi = new addons();
	$response->getBody()->write($callApi->getAddonsList());
	return $response;
});

// Run application
$app->run();
