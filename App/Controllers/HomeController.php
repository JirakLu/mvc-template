<?php

class HomeController extends AController
{


    public function render(): void
    {
        $model = new ArticleModel();
        $data = $model->getUsers();
        $this->renderView("pages.index", ["data" => $data]);
    }

    public function list(string $param): void
    {
        echo "HomeController:list:$param";
    }

}