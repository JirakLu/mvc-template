<?php

class RouterController extends AController
{

    private static string $debugValue = '';

    /** @var object  */
    protected object $controller;

    /** @return string[] */
    private function parseUrl(string $url): array
    {
        $res = explode("\\", __DIR__);
        $parsedUrl = parse_url($url);
        $parsedUrl = str_replace($res[array_search("www", $res)+1], '', $parsedUrl);
        $parsedUrl["path"] = ltrim($parsedUrl["path"], "/");
        $parsedUrl["path"] = trim($parsedUrl["path"]);
        return explode("/", $parsedUrl["path"]);
    }

    private function camelCase(string $text): string
    {
        $text = str_replace('-', '', $text);
        $text = ucwords($text);
        return str_replace(' ', '', $text);
    }

    public function process(array $url): void
    {
        $parsedUrl = $this->parseUrl($url[0]);

        if (empty($parsedUrl[0]))
            $this->redirect('home');

        $controllerClass = $this->camelCase(array_shift($parsedUrl) . "Controller");

        if (file_exists(__DIR__ . "/$controllerClass.php"))
            $this->controller = new $controllerClass();
        else
            $this->redirect('error');

        $this->controller->process($parsedUrl);

        $this->data['title'] = $this->controller->headers['title'];
        $this->data['description'] = $this->controller->headers['description'];
        $this->data['keywords'] = $this->controller->headers['keywords'];

        $this->view = 'template';

    }

    public static function setDebug(string $value): void
    {
        RouterController::$debugValue = $value;
    }

    public static function debug(): void
    {
        if (!empty(RouterController::$debugValue)) {
            $arr = ["value" => RouterController::$debugValue];
            extract($arr);
            require("../views/debug.phtml");
        }
    }
}


