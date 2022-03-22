<?php

use App\Kernel;

require __DIR__.'/../vendor/autoload.php';

$kernel = new Kernel();
$kernel->listen();
