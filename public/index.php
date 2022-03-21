<?php

use Services\Router;

require __DIR__.'/../vendor/autoload.php';

$router = new Router();
$action = $router->findRoute();

(new $action["controller"]($router))->{$action["action"]}(array_key_exists("params",$action) ? $action["params"] : null);