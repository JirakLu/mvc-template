<?php

class ErrorController extends AController
{

    public function render505(): void
    {
        echo "ErrorController:render505";
    }

    public function render404(): void
    {
        echo "ErrorController:render404";
    }

}