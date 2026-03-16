<?php

require_once './vendor/autoload.php';

use Slim\Factory\AppFactory;
use App\Controllers\Repository;

$orm = new Repository();
$orm::start();

$app = AppFactory::create();
$route = require __DIR__ . '/routes/api.php';

$route($app);

$app->run();


$admin = ['everyone'];
$manager = ['crudProduct', 'crudUsers'];
$finance = ['crudProduct', 'chargeback'];
$users = ['purchaseDetails', 'listAllPurchases', 'alterPriorityGateway', 'start/disableGateway'];