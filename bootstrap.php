<?php

require_once(__DIR__.'/IntCode.php');
require_once(__DIR__.'/Grid.php');

$ic = new IntCode([
    'logLevel' => 0,
]);
$ic->test();

