<?php

class HomeController extends AController
{

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