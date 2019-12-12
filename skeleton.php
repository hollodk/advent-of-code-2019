<?php

require __DIR__.'/bootstrap.php';

$file = 'input-12.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$tools = new Tools();
