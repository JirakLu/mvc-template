<?php declare(strict_types = 1);

require dirname(__DIR__, 1) . '/vendor/autoload.php';

$router = new RouterController();
$router->process([$_SERVER['REQUEST_URI']]);
$router->renderView();