<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

//$rutaImagenCuenta = 'C:\xampp\htdocs\PP_Progra3\ImagenesCuentas\2023';
//$rutaImagenDeposito = 'C:\xampp\htdocs\PP_Progra3\ImagenesDepositos\2023';
//$rutaBackUpCuentas = 'C:\xampp\htdocs\PP_Progra3\ImagenesBackupCuenta';

require_once './controllers/CuentaController.php';
require_once './controllers/DepositoController.php';
require_once './controllers/RetiroController.php';
require_once './controllers/AjusteController.php';
require_once './controllers/MovimientoController.php';
require_once './db/dataAccess.php';

require __DIR__ . '/vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

$app->group('/cuenta', function (RouteCollectorProxy $group) {
    $group->post('/alta', \CuentaController::class . ':AltaCuenta');
    $group->post('/consultar', \CuentaController::class . ':consultarCuenta');
    $group->post('/baja', \CuentaController::class . ':borrarCuenta');
    $group->post('/modificar', \CuentaController::class . ':modificarCuenta');
});

$app->post('/retiro', \RetiroController::class . ':retirarCuenta');
$app->post('/ajuste', \AjusteController::class . ':ajusteCuenta');
$app->post('/deposito', \DepositoController::class . ':depositoCuenta');

$app->group('/movimiento', function (RouteCollectorProxy $group) {
    //depositos
    $group->get('/totaldeposito', \MovimientoController::class . ':consultaTotalDeposito');
    $group->get('/depositousuario', \MovimientoController::class . ':depositosPorUsuario');
    $group->get('/depositoentrefecha', \MovimientoController::class . ':depositosEntreDosFechas');
    $group->get('/depositotipo', \MovimientoController::class . ':depositosPorTipoCuenta');
    $group->get('/depositomoneda', \MovimientoController::class . ':depositosPorMoneda');
    //ajustes
    $group->get('/totalretiro', \MovimientoController::class . ':consultaTotalRetiro');
    $group->get('/retirousuario', \MovimientoController::class . ':retirosPorUsuario');
    $group->get('/retiroentrefecha', \MovimientoController::class . ':retirosEntreDosFechas');
    $group->get('/retirotipo', \MovimientoController::class . ':retirosPorTipoCuenta');
    $group->get('/retiromoneda', \MovimientoController::class . ':retirosPorMoneda');

    //f
    $group->get('/operaciones', \MovimientoController::class . ':consultaOperaciones');
});


$app->run();
?>