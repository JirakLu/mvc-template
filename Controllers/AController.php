<?php

abstract class AController
{
    /** @var array<string, string | string[]>  */
    protected array $data = [];

    /** @var array{'title': string, 'keywords': string, 'description': string}  */
    protected array $headers = ['title' => '', 'keywords' => '', 'description' => ''];

    /** @var string 'error'|'template'|'home' */
    protected string $view = '';

    public function renderView(): void
    {
        if ($this->view) {
            if (file_exists("./Views/" . $this->view . ".phtml")) {
                extract($this->data);
                require("./Views/" . $this->view . ".phtml");
            } else {
                extract($this->data);
                require("./Views/error.phtml");
            }
        }
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        header("Connection: close");
        exit;
    }

    /**
     * @param array<int, string> $url
     */
    abstract public function process(array $url): void;


}