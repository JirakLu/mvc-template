<?php

class HomeController extends AController
{

    public function render(): void
    {
        echo "HomeController:render";
    }

    public function list(string $param): void
    {
        echo "HomeController:list:$param";
    }

}