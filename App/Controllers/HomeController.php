<?php

class HomeController extends AController
{

    public function render(): void
    {
        $this->renderView("pages.index");
    }

    public function list(string $param): void
    {
        echo "HomeController:list:$param";
    }

}