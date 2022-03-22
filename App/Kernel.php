<?php

namespace App;

use Jenssegers\Blade\Blade;
use Services\Router;

class Kernel {

    public function listen(): void
    {
        $blade = new Blade(__DIR__."/../resources/views", __DIR__."/../cache");

        $router = new Router();
        $action = $router->findRoute();

        (new $action["controller"]($router, $blade))->{$action["action"]}(array_key_exists("params",$action) ? $action["params"] : null);
    }
}