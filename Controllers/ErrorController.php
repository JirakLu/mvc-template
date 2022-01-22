<?php

class ErrorController extends AController
{

    function process(array $url): void
    {
        $this->headers = [
            'title' => 'error',
            'keywords' => 'error',
            'description' => 'page does not exist'
        ];
        $this->view = 'error';
    }
}