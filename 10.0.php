<?php

$input = trim(file_get_contents(__DIR__.'/input-10.0-test1.txt'));
$onlyX = 3;
$onlyY = 4;

$input = trim(file_get_contents(__DIR__.'/input-10.0-test2.txt'));
$onlyX = 5;
$onlyY = 8;

$input = trim(file_get_contents(__DIR__.'/input-10.0-test3.txt'));
$input = trim(file_get_contents(__DIR__.'/input-10.0-test4.txt'));
$onlyX = 6;
$onlyY = 3;

$input = trim(file_get_contents(__DIR__.'/input-10.0-test5.txt'));
$input = trim(file_get_contents(__DIR__.'/input-10.0.txt'));
unset($onlyX, $onlyY);

//echo $input.PHP_EOL.PHP_EOL;

$g = preg_split("/\n/", $input);
$grid = [];
$astoroids = [];
$base = [];

foreach ($g as $y=>$i) {
    if (!isset($grid[$y])) {
        $grid[$y] = [];
    }

    $xs = str_split($i);
    foreach ($xs as $x=>$v) {
        $grid[$y][] = $v;

        if ($v == '#') {
            $p = new \StdClass();
            $p->x = $x;
            $p->y = $y;

            $astoroids[] = $p;
        }
    }
}

foreach ($astoroids as $k1=>$a1) {
    foreach ($astoroids as $k2=>$a2) {
        if (isset($onlyX) && ($a1->x != $onlyX || $a1->y != $onlyY)) continue;
        if ($k1 == $k2) continue;

        $res = findSlope($a1->x, $a1->y, $a2->x, $a2->y);
        $slope = $res['key'];

        $key = $a1->x.','.$a1->y;
        if (!isset($base[$key])) {
            $base[$key] = [];
        }

        if (!isset($base[$key][$slope])) {
            $base[$key][$slope] = [];
        }

        $base[$key][$slope][] = [
            'x' => $a2->x,
            'y' => $a2->y,
        ];
    }
}

$highest = null;
$amount = null;

foreach ($base as $key=>$mark) {
    if ($highest === null) {
        $highest = $key;
        $amount = count($mark);
    }

    if (count($mark) > $amount) {
        $highest = $key;
        $amount = count($mark);
    }

    echo $key.': '.count($mark).PHP_EOL;
}

var_dump($highest, $amount);

function findSlope($x1, $y1, $x2, $y2)
{
    $dx = $x2 - $x1;
    $dy = $y2 - $y1;

    $vert = 'down';
    if ($dy < 0) {
        $vert = 'up';
    }

    $hori = 'right';
    if ($dx < 0) {
        $hori = 'left';
    }

    if ($dx == 0) {
        $slope = 'line';
    } else {
        $slope = $dy / $dx;
    }

    $res = $vert.','.$hori.','.$slope;

    echo sprintf('%s,%s -> %s,%s | dx %s, dy %s | vert %s, hori %s | slope %s | res %s',
        $x1,
        $y1,
        $x2,
        $y2,
        $dx,
        $dy,
        $vert,
        $hori,
        $slope,
        $res
    ).PHP_EOL;

    $res = [
        'key' => $res,
        'hori' => $hori,
        'slope' => $slope,
    ];

    return $res;
}
