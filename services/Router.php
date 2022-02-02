<?php

use Nette\Neon\Neon;

class Router {

    /** @var array{static: array<string, array<String>>,dynamic: array<string, array<String>>}  */
    private array $routes;

    // <domain>/mvcTemplate/controller/...
    private string $domain;

    // www.example/<basePath>/controller/...
    private string $basePath;

    // www.example/mvcTemplate/<activeURL>
    private string $activeURL;


    /**
     * @throws \Nette\Neon\Exception
     */
    public function __construct(string $configPath = '/configs/routes.neon')
    {
       $this->loadRouteConfig($configPath);
       $this->generateDefaults();
    }

    /**
     * finds action depending on route URL
     * @return array{controller: class-string<AController>, action: string, params?: string }|null
     */
    public function findRoute(): ?array
    {
        // static URLs matching
        foreach ($this->routes["static"] as $routeMatcher => $actions) {
            if ($this->activeURL === $routeMatcher) {
                return $this->executeAction($actions["defaults"]);
            }
        }

        // dynamic URLs matching
        $splitActiveURL = explode("/", preg_replace("/^\//","",$this->activeURL));
        foreach ($this->routes["dynamic"] as $routeMatcher => $actions) {
            $splitMatcherURL = explode("/", preg_replace("/^\//","",$routeMatcher));
            $action = $actions["defaults"];
            $matched = false;
            if (count($splitMatcherURL) === count($splitActiveURL)) {
                foreach ($splitActiveURL as $key => $partActiveURL) {
                    $partMatcherURL = $splitMatcherURL[$key];
                    if ($partActiveURL !== $partMatcherURL && str_contains($partMatcherURL, '<')) {
                        $action = str_replace(str_replace(['<','>'], '', $partMatcherURL), $partActiveURL, $action);
                        $matched = true;
                    } else if ($partActiveURL !== $partMatcherURL) {
                        $matched = false;
                        break;
                    }
                    $matched = true;
                }
                if ($matched) {
                    return $this->executeAction($action);
                }
            }
        }

        return ["controller" => "ErrorController", "action" => "render", "params" => "404"];

    }


    /**
     * generates base href for html  <base href="$this->generateBase()" target="_blank">
     * @return string
     */
    public function generateBase(): string
    {
        return "{$this->domain}{$this->basePath}";
    }

    /**
     * redirects user to specified endpoint
     * @param string $endPoint
     * @param int $statusCode
     * @return void
     */
    public function redirect(string $endPoint, int $statusCode = 303): void
    {
        header("Location: /{$this->basePath}/$endPoint",true,$statusCode);
        die();
    }

    /**
     * creates link for <a> attributes <a href=$this->createLink(articles/15)>Article 15</a>
     * @param string $link
     * @return string
     */
    public function createLink(string $link): string
    {
        return "{$this->basePath}/$link";
    }

    /**
     * returns active url
     * @return string
     */
    public function getActiveURL(): string
    {
        return $this->activeURL;
    }

    /**
     * executes action if exists else redirects to error page
     * @param string $action
     * @return array{controller: class-string<AController>, action: string, params?: string }|null
     */
    private function executeAction(string $action): ?array
    {
        $formattedAction = $this->formatAction($action);
        if ($this->actionExists($formattedAction)) {
            return $formattedAction;
        }
        $this->redirect("/error/404");
        return null;
    }

    /**
     * checks if controller and action exists
     * @param array{controller: class-string<AController>, action: string, params?: string } $actions
     * @return bool
     */
    private function actionExists(array $actions): bool
    {
        if (class_exists($actions["controller"])) {
            $class = new $actions["controller"]();
            if (!method_exists($class, $actions["action"])) {
                throw new Error("Method - ". $actions["action"] . " on controller - " . $actions["controller"] . " does not exist.");
            }
        } else {
            throw new Error("Controller - ". $actions["controller"] . " does not exist.");
        }
        return true;
    }

    /**
     * parses Controller:action:?params into => ["controller" => "...", "action" => "...", "params" => "..."]
     * @param string $action
     * @return array{controller: class-string<AController>, action: string, params?: string }
     */
    private function formatAction(string $action): array
    {
        $splitAction = explode(':', $action);

        if (count($splitAction) === 2 || count($splitAction) === 3) {
            if (count($splitAction) === 2) { // no params only => Controller:action
                return ["controller" => $splitAction[0], "action" => $splitAction[1]];
            } else { // has params => Controller:action:params
                return ["controller" => $splitAction[0], "action" => $splitAction[1], "params" => $splitAction[2]];
            }
        } else {
            throw new Error("Action - " . $action . " is not valid");
        }
    }

    /**
     * @throws \Nette\Neon\Exception
     */
    private function loadRouteConfig(string $configPath): void
    {
        // loading urls from config
        $config = Neon::decodeFile(dirname(__DIR__, 1) . $configPath);

        // setting up static routes
        $this->routes["static"] = array_filter($config, function ($key) {
            return !str_contains($key,'<');
        }, ARRAY_FILTER_USE_KEY);

        // setting up dynamic routes
        $this->routes["dynamic"] = array_filter($config, function ($key) {
            return str_contains($key,'<');
        }, ARRAY_FILTER_USE_KEY);
    }

    private function generateDefaults(): void
    {
        $this->domain = $_SERVER['HTTP_HOST'];
        $dirname = explode("\\",dirname(__DIR__));
        $this->basePath = $dirname[count($dirname)-1] === "www" ? "" : $dirname[count($dirname)-1];
        $this->activeURL = str_replace("/".$this->basePath,"",$_SERVER["REQUEST_URI"]);
    }

}