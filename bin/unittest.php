<?php

include '../../../autoload.php';

$file = $argv[1];

$test = new Unittest\Test([$file]);

$test();

$renderer = new Unittest\Render\Cli();
$renderer->render($test);
