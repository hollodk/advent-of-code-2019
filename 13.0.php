<?php

require __DIR__.'/bootstrap.php';

$file = 'input-13.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$ic = new IntCode();
$ic->setCode($input);
$out = $ic->process();

$chunk = array_chunk($out['output'],3);

$tiles = 0;
foreach ($chunk as $o) {
    switch ($o[2]) {
    case 0:
        break;

    case 1:
    case 3:
    case 4:
        break;

    case 2:
        $tiles++;
        break;

    default:
        throw new \Exception('not supported');
    }
}

dump('total tiles '.$tiles);
