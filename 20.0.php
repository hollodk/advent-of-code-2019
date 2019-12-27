<?php

require __DIR__.'/bootstrap.php';

$file = 'input-20.txt';
$input = file_get_contents(__DIR__.'/'.$file);

$grid->setGridFromMap($input);

$start = $grid->getAreaFields('AA');
$start = array_pop($start);
$target = $grid->getAreaFields('ZZ');
$target = array_pop($target);

$values = $grid->getValues();

$warps = [];
$w = [];

foreach ($values as $coord=>$value) {
    if (preg_match("/[A-Z]/", $value)) {
        $o = preg_split("/,/", $coord);
        $around = $grid->getAround($o[0], $o[1]);

        $k = null;
        if (preg_match("/[A-Z]/", $around['down']['value'])) {
            $k = $value.$around['down']['value'];
        } elseif (preg_match("/[A-Z]/", $around['right']['value'])) {
            $k = $value.$around['right']['value'];
        }

        if ($k) {
            $warps[$k] = $k;
        }
    }
}

foreach ($warps as $key=>$warp) {
    $fields = $grid->getAreaFields($key);

    if (count($fields) == 2) {
        $from = array_shift($fields);
        $to = array_shift($fields);

        $w[] = [
            'from' => $from['coord'],
            'to' => $to['coord'],
        ];

        $w[] = [
            'from' => $to['coord'],
            'to' => $from['coord'],
        ];

    } else {
        unset($warps[$key]);
    }
}

$robot = new Robot(
    true,
    $grid,
    $start['coord']['x'],
    $start['coord']['y'],
    [
        '.',
    ],
    [
        $target['coord'],
    ],
    null,
    'n'
);

$robot->setWarps($w);

$r = $robot->run(false, false, 0, true);
dump($r);
