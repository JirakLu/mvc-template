<?php

class ArticlesController extends AController
{

    public function mainArticle() {
        echo 'ArticlesController:mainArticle';
    }

    public function articles($param) {
        echo "ArticlesController:articles:$param";
    }

}