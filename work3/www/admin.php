<?php
require_once 'autoload.php';

use Infrastructure\DI\Container;
use InterfaceAdapters\Controllers\AdminController;

$container = new Container();
$controller = $container->get(AdminController::class);
$controller->index();
