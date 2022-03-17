<?php

use Jenssegers\Blade\Blade;

abstract class AController
{

    /** @var Router */
    protected Router $router;

    public function __construct(Router $router = new Router())
    {
        $this->router = $router;
    }

    /** @param array<string, mixed> $params */
    public function renderView(string $page, array $params = []): void
    {
        $router = ["generateBase" => fn() => $this->router->generateBase(), "getActiveUrl" => fn() => $this->router->getActiveURL(), "createLink" => fn($link) => $this->router->createLink($link)];

        $blade = new Blade(dirname(__DIR__, 2)."\\resources\\views", dirname(__DIR__, 2)."\\cache");

        echo $blade->render("$page", array_merge($params, $router));
    }

}