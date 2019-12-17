<?php

require __DIR__.'/bootstrap.php';

$file = 'input-15.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$grid->set(unserialize(file_get_contents('grid-15.txt')));

$robot = new Robot(
    true,
    $grid,
    14,
    -14,
    [
        'yellow',
    ],
    [
        'green',
    ],
    [
        'red',
    ],
    'n'
);

$robot->run(true, true, 500);
