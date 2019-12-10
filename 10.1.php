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
$onlyX = 11;
$onlyY = 13;

$input = trim(file_get_contents(__DIR__.'/input-10.0.txt'));
$onlyX = 30;
$onlyY = 34;

//unset($onlyX, $onlyY);

echo $input.PHP_EOL.PHP_EOL;

$g = preg_split("/\n/", $input);
$grid = [];
$astoroids = [];

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

$base = [
    'up-right' => [],
    'down-right' => [],
    'down-left' => [],
    'up-left' => [],
];

foreach ($astoroids as $k1=>$a1) {
    foreach ($astoroids as $k2=>$a2) {
        if (isset($onlyX) && ($a1->x != $onlyX || $a1->y != $onlyY)) continue;
        if ($k1 == $k2) continue;

        $res = findSlope($a1->x, $a1->y, $a2->x, $a2->y);
        $slope = (string)$res['slope'];
        $dir = $res['vert'].'-'.$res['hori'];
        $key = $a1->x.','.$a1->y;

        if (!isset($base[$dir])) {
            $base[$dir] = [];
        }

        /*
        if (!isset($base[$dir][$key])) {
            $base[$dir][$key] = [];
        }

        if (!isset($base[$dir][$key][$slope])) {
            $base[$dir][$key][$slope] = [];
        }
         */

        if (!isset($base[$dir][$slope])) {
            $base[$dir][$slope] = [];
        }

        $base[$dir][$slope][] = [
            'x' => $a2->x,
            'y' => $a2->y,
        ];
    }
}

foreach ($base as $k1=>$v1) {
    foreach ($v1 as $k2=>$v2) {
        $n = [];
        foreach ($v2 as $k3=>$v3) {
            $dist = getDistance($onlyX, $onlyY, $v3['x'], $v3['y']);
            $n[$dist] = [
                'x' => $v3['x'],
                'y' => $v3['y'],
                'dist' => $dist,
            ];
        }

        ksort($n);
        $base[$k1][$k2] = $n;
    }
}

$total = 0;
foreach ($base as $v1) {
    $total += count($v1);
}

$d = 'up-right';
$new = [];
$line = $base[$d]['line'];
unset($base[$d]['line']);
ksort($base[$d]);
$new = ['line' => $line];
foreach ($base[$d] as $k=>$v) {
    $new[$k] = $v;
}
$base[$d] = $new;

$d = 'down-right';
$new = [];
$line = $base[$d]['line'];
unset($base[$d]['line']);
ksort($base[$d]);
foreach ($base[$d] as $k=>$v) {
    $new[$k] = $v;
}
$new['line'] = $line;
$base[$d] = $new;

$d = 'down-left';
/*
$new = [];
$line = $base[$d]['line'];
unset($base[$d]['line']);
 */
ksort($base[$d]);
/*
if ($line) {
    $new = ['line' => $line];
}
foreach ($base[$d] as $k=>$v) {
    $new[$k] = $v;
}
$base[$d] = $new;
 */

$d = 'up-left';
/*
$new = [];
$line = $base[$d]['line'];
unset($base[$d]['line']);
 */
ksort($base[$d]);
/*
foreach ($base[$d] as $k=>$v) {
    $new[$k] = $v;
}
if ($line) {
    $new['line'] = $line;
}
$base[$d] = $new;
 */

$shot = 0;
echo 'BASE '.$onlyX.','.$onlyY.PHP_EOL;

$two = null;
while (true) {
    if ($shot >= 200) break;

    foreach ($base as $dir=>$v1) {
        if ($shot >= 200) break;

        var_dump('direction '.$dir);
        sleep(1);

        foreach ($v1 as $slope=>$v2) {
            foreach ($v2 as $key=>$v3) {
                $shot++;

                echo sprintf(' > shoot %s,%s, slope %s, total shots '.$shot,
                    $v3['x'],
                    $v3['y'],
                    $slope
                ).PHP_EOL;

                if ($shot == 200) {
                    $two = $v3;
                    break;
                }
                unset($base[$dir][$slope][$key]);

                break;
            }

            if (count($base[$dir][$slope]) == 0) {
                //echo 'slope empty, unset '.$slope.PHP_EOL;

                unset($base[$dir][$slope]);
            }

            usleep(5000);
        }

        if (count($base[$dir]) == 0) {
            echo 'dir empty, unset '.$dir.PHP_EOL;
            unset($base[$dir]);
        }
    }

    if (count($base) == 0) {
        echo 'done'.PHP_EOL;
        break;
    }
}

var_dump($two['x']*100+$two['y']);die();
function getDistance($x1, $y1, $x2, $y2)
{
    return pow($x2-$x1,2) + pow($y2-$y1,2);
}

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

    /*
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
     */

    $res = [
        'key' => $res,
        'hori' => $hori,
        'vert' => $vert,
        'slope' => $slope,
    ];

    return $res;
}
