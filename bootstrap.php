<?php

require_once(__DIR__.'/vendor/autoload.php');
require_once(__DIR__.'/Grid.php');
require_once(__DIR__.'/IntCode.php');
require_once(__DIR__.'/Robot.php');
require_once(__DIR__.'/Tools.php');

if (!isset($noTests)) {
    $ic = new IntCode([
        'logLevel' => 0,
    ]);
    $ic->test();
}

$tools = new Tools();
$grid = new Grid();
