<?php

require dirname(__DIR__, 1) . '/vendor/autoload.php';

$router = new Router();
$actions = $router->findRoute();
