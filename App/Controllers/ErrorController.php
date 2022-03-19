<?php

class ErrorController extends AController
{

    public function render404(): void
    {
        $this->renderView("pages.error404");
    }


}