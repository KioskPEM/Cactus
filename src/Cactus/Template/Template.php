<?php

namespace Cactus\Template;


use Cactus\Template\Controller\ITemplateController;

class Template
{
    private string $name;
    private string $content;
    private ?ITemplateController $controller;

    public function __construct(string $name, string $content, ?ITemplateController $controller)
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

    public function getController(): ?ITemplateController
    {
        return $this->controller;
    }

}