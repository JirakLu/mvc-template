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

    // prefix before params in routes.neon
    private string $paramPrefix = "$";


    /**
     * @throws \Nette\Neon\Exception
     */
    public function __construct(string $configPath = '/routes/routes.neon')
    {
       $this->loadRouteConfig($configPath);
       $this->generateDefaults();
       echo "<base href='{$this->generateBase()}'>";
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
            $splitMatcherURL = preg_split("/(?<!{)\/(?![^\s{]*[}])/", preg_replace("/^\//","",$routeMatcher));
            $action = $actions["defaults"];
            $matched = false;
            if (count($splitMatcherURL) === count($splitActiveURL)) {;
                foreach ($splitActiveURL as $key => $partActiveURL) {
                    $partMatcherURL = $splitMatcherURL[$key];
                    if ($partActiveURL !== $partMatcherURL && str_contains($partMatcherURL, '<')) {
                        preg_match("/(?<=<)\w+/",$partMatcherURL,$paramName);
                        preg_match("/(?<==)\w+/",$partMatcherURL,$defaultValue);
                        preg_match("/(?<={).+?(?=})/",$partMatcherURL,$regexMatcher);
                        $paramName = "{$this->paramPrefix}{$paramName[0]}";
                        $defaultValue = $defaultValue ? $defaultValue[0] : null;
                        $regexMatcher = $regexMatcher ? $regexMatcher[0] : null;
                        if (empty($partActiveURL) && !empty($defaultValue)) {
                            $action = str_replace($paramName, $defaultValue, $action);
                            $matched = true;
                        } else if (!empty($partActiveURL) && !empty($regexMatcher)) {
                            $matchedRegex = preg_match($regexMatcher, $partActiveURL);
                            if ($matchedRegex) {
                                $action = str_replace($paramName, $partActiveURL, $action);
                                $matched = true;
                            } else {
                                throw new Error("Your url param did not match the regex - " . $regexMatcher);
                            }
                        } else if(!empty($partActiveURL)){
                            $action = str_replace($paramName, $partActiveURL, $action);
                            $matched = true;
                        } else {
                            $matched = false;
                        }
                    } else if ($partActiveURL !== $partMatcherURL) {
                        $matched = false;
                        break;
                    }
                }
                if ($matched) {
                    return $this->executeAction($action);
                }
            }
        }

        return ["controller" => "ErrorController", "action" => "render404"];
    }


    /**
     * generates base href for html  <base href="$this->generateBase()" target="_blank">
     * @return string
     */
    public function generateBase(): string
    {
        return "{$this->domain}/{$this->basePath}";
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
        header("Connection: close");
        exit();
    }

    /**
     * redirects user to chosen controller without changing url
     * @param class-string<AController> $controller
     * @param string $action
     * @param string|null $params
     * @return void
     */
    public function forward(string $controller, string $action, ?string $params = ""): void
    {
        $action = $this->executeAction(implode(":",[$controller,$action,$params]));
        (new $action["controller"]())->{$action["action"]}(array_key_exists("params",$action) ? $action["params"] : null);
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
        $this->redirect("error/404");
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
            } else if ($splitAction[2] === ""){ // has empty params => Controller:action:
                return ["controller" => $splitAction[0], "action" => $splitAction[1]];
            } else {  // has params => Controller:action:params
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
        if (!file_exists(dirname(__DIR__, 1) . $configPath)) {
            throw new Error("Routes.neon config file does not exists in directory - " . dirname(__DIR__, 1) . $configPath);
        }
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