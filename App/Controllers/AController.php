<?php

abstract class AController
{
    /** @var array<string, string | string[]> */
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
            if (file_exists("../resources/" . $this->view . ".phtml")) {
                extract($this->data);
                require("../resources/" . $this->view . ".swift.php");
            } else {
                $this->router->redirect("/error/404");
            }
        }
    }

}