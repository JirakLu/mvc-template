<?php

class HomeController extends AController
{

    public function render() {
        echo "HomeController:render";
    }

    public function list($param) {
        echo "HomeController:list:$param";
    }

    public function process(array $url): void
    {
        $this->headers = [
            'title' => 'home page',
            'keywords' => 'home',
            'description' => 'main page'
        ];
        $this->view = 'home';
    }
}