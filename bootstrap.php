<?php

require_once(__DIR__.'/vendor/autoload.php');
require_once(__DIR__.'/IntCode.php');
require_once(__DIR__.'/Grid.php');
require_once(__DIR__.'/Tools.php');

$ic = new IntCode([
    'logLevel' => 0,
]);
$ic->test();

$tools = new Tools();
