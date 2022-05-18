<?php

namespace Banana\Template;


use Banana\Template\Render\IRenderHandler;

class Template
{
    private string $name;
    private string $content;
    private ?IRenderHandler $controller;

    public function __construct(string $name, string $content, ?IRenderHandler $controller)
    {
        $this->name = $name;
        $this->content = $content;
        $this->controller = $controller;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getController(): ?IRenderHandler
    {
        return $this->controller;
    }

}