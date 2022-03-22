<?php

namespace App\Controllers;

use App\Models\ArticleModel;

class HomeController extends AController
{
    public function render(): void
    {
        $model = new ArticleModel();
        $data = $model->getUsers();
        $this->renderView("pages.index", ["data" => $data]);
        $this->forward("ErrorController", "render404");
    }

    public function list(string $param): void
    {
        echo "HomeController:list:$param";
    }

}