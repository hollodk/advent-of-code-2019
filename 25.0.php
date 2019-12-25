<?php

// 1077936448 passcode

require __DIR__.'/bootstrap.php';

$cache = '/tmp/rob.cache2';

$file = 'input-25.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$path = [];

$items = [
    'pointer',
    'coin',
    'mug',
    'manifold',
    'hypercube',
    'easter egg',
    'astrolabe',
    'candy cane',
];

if (false && file_exists($cache)) {
    $ic = unserialize(file_get_contents($cache));

    $path = getToStarFromWeight();

} else {
    $ic = new IntCode();
    $ic->setCode($input);
    $ic->configurePhases();

    $path[] = 'west';
    $path[] = 'take mug';
    $path[] = 'east';

    $path[] = 'east';
    $path[] = 'take coin';
    $path[] = 'north';
    $path[] = 'north';
    $path[] = 'take hypercube';
    $path[] = 'south';
    $path[] = 'south';

    $path[] = 'south';
    $path[] = 'west';
    $path[] = 'take astrolabe';
    $path[] = 'north';
    $path[] = 'east';
    $path[] = 'north';
    $path[] = 'east';
}

while (true) {
    $o = $ic->run(0);

    $msg = $tools->printAscii($o);
    echo $msg;

    $input = array_shift($path);
    if (!$input) {
        die('no more instructions');
    }

    dump($input);
    $input .= PHP_EOL;

    $in = $tools->toAscii($input);
    foreach ($in as $i) {
        $ic->addInput(0, $i);
    }

    file_put_contents($cache, serialize($ic));
}

function getToStarFromWeight()
{
    $path = [];
    $path[] = 'south';
    $path[] = 'west';
    $path[] = 'south';
    $path[] = 'east';
    $path[] = 'north';
    $path[] = 'west';
    $path[] = 'west';
    $path[] = 'west';

    return $path;
}

function getKey($items)
{
    $path = [];

    $items = [];
    $items[] = 'hypercube';
    $items[] = 'coin';
    $items[] = 'mug';
    $items[] = 'astrolabe';

    foreach ($items as $item) {
        $path[] = 'take '.$item;
    }

    return $path;
}

function dropAll($items)
{
    $path = [];

    foreach ($items as $item) {
        $path[] = 'drop '.$item;
    }

    return $path;
}

function getAll($items)
{
    $path = [];

    foreach ($items as $item) {
        $path[] = 'take '.$item;
    }

    return $path;
}

function walk()
{
    $path[] = 'west';
    $path[] = 'take mug';
    $path[] = 'west';
    $path[] = 'east';
    $path[] = 'north';
    $path[] = 'take easter egg';
    $path[] = 'south';
    $path[] = 'east';

    // from start to checkpoint
    $path[] = 'south';
    $path[] = 'east';
    $path[] = 'north';
    $path[] = 'take candy cane';
    $path[] = 'south';
    $path[] = 'west';
    $path[] = 'south';
    $path[] = 'north';
    $path[] = 'north';

    // start

    $path[] = 'east';
    $path[] = 'north';
    $path[] = 'north';
    $path[] = 'take hypercube';
    $path[] = 'south';
    $path[] = 'east';
    $path[] = 'take manifold';
    $path[] = 'west';
    $path[] = 'south';

    $path[] = 'take coin';
    $path[] = 'south';
    $path[] = 'east';
    $path[] = 'take pointer';
    $path[] = 'west';
    $path[] = 'west';
    $path[] = 'take astrolabe';
    $path[] = 'south';
    //$path[] = 'take giant electromagnet';
    $path[] = 'north';
    $path[] = 'north';
    $path[] = 'east';
    //$path[] = 'take molten lava'; die
    $path[] = 'north';
    $path[] = 'east'; // weight
    $path[] = 'inv';

    return $path;
}
