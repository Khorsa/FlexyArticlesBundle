<?php
namespace flexycms\FlexyArticlesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FlexyArticlesBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}