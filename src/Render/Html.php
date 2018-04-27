<?php

namespace Unittest\Render;

class Html
{
    public function render($test)
    {
        $template = dirname(dirname(__DIR__)) . '/templates/testresult.phtml';
        include $template;
    }

    public function renderList($list)
    {
        $template = dirname(dirname(__DIR__)) . '/templates/listtests.phtml';
        include $template;
    }

    public function renderError($error)
    {
        $template = dirname(dirname(__DIR__)) . '/templates/error.phtml';
        include $template;
    }
}
