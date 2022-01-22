<?php

require __DIR__.'/vendor/autoload.php';

$router = new RouterController();
$router->process([$_SERVER['REQUEST_URI']]);
$router->renderView();