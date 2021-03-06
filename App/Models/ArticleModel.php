<?php

namespace App\Models;

use App\Models\DB\Db;
use stdClass;

class ArticleModel
{

    private Db $db;

    public function __construct()
    {
        $this->db = new Db();
    }

    public function getUsers(): array|bool
    {
        return $this->db->getAll("SELECT * FROM osoba", stdClass::class);
    }
}