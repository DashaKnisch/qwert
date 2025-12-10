<?php
require_once '../autoload.php';

use Infrastructure\DI\Container;
use InterfaceAdapters\Controllers\ApiController;

$container = new Container();
$controller = $container->get(ApiController::class);
$controller->users();
