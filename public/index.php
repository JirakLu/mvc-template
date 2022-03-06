<?php

require dirname(__DIR__, 1) . '/vendor/autoload.php';

$router = new Router();
$action = $router->findRoute();

(new $action["controller"]($router))->{$action["action"]}(array_key_exists("params",$action) ? $action["params"] : null);