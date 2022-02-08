<?php

abstract class AController
{
    /** @var array<string, string | string[]>  */
    protected array $data = [];

    /** @var string 'error'|'template'|'home' */
    protected string $view = '';

    /** @var Router */
    private Router $router;

    public function __construct(Router $router = new Router())
    {
        $this->router = $router;
    }

    public function renderView(): void
    {
        if ($this->view) {
            if (file_exists("../views/" . $this->view . ".phtml")) {
                extract($this->data);
                require("../views/" . $this->view . ".phtml");
            } else {
                $this->router->redirect("/error/404");
            }
        }
    }

}