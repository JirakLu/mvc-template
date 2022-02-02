<?php

class ErrorController extends AController
{

    public function render($param) {
        echo "ErrorController:render:$param";
    }

    public function process(array $url): void
    {
        $this->headers = [
            'title' => 'error',
            'keywords' => 'error',
            'description' => 'page does not exist'
        ];
        $this->view = 'error';
    }
}