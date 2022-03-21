<?php

namespace App\Models\DB;

use PDO;

class DbParam{

    public string $name;
    public mixed $value;
    public int $type;

    public function __construct(string $name, mixed $value, int $type = PDO::PARAM_STR){
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

}