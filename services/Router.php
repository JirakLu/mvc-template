<?php

use Nette\Neon\Neon;

class Router {

    /** @var array<string, array<string,string>>  */
    private array $routes;

    // baseDir of project structure
    private string $baseDir;

    private string $activeURL;

    /**
     * @throws \Nette\Neon\Exception
     */
    public function __construct()
    {
        $config = Neon::decodeFile(dirname(__DIR__, 1) . '/configs/routes.neon');
        $this->routes = $config['routes'];
        $dirname = explode("\\", __DIR__);
        $this->baseDir = "/" . $dirname[count($dirname)-2];
    }

    public function findRoute()
    {
        $this->activeURL = str_replace($this->baseDir, '', $_SERVER['REQUEST_URI']);

        $action = $this->routes['error'];

        foreach ($this->routes as $pattern => $reaction){
            if (preg_match("/".$pattern."/", $this->activeURL)) {
                $action = $reaction;
            }
        }
        var_dump($action);
    }
}