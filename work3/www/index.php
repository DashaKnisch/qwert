<?php
require_once 'autoload.php';

use Infrastructure\DI\Container;
use InterfaceAdapters\Controllers\HomeController;

$container = new Container();
$controller = $container->get(HomeController::class);
$controller->index();
