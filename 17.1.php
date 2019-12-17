<?php

require __DIR__.'/bootstrap.php';

$file = 'input-17.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$ic = new IntCode();
$ic->setCode($input);
$o = $ic->process();

$output = $o['output'];

$start = getGrid($grid, $output);
$sum = addIntersection($grid);

$robot = new Robot(
    false,
    $grid,
    $start['x'],
    $start['y'],
    [
        '#',
        'O',
        '^',
    ],
    [
    ],
    [
        '.',
    ],
    'n',
    'to-block'
);

$spawn = $robot->run();
$path = $spawn['path'];

$res = [];
$last = $path[0];
$count = 0;
foreach ($path as $key=>$value) {
    if ($value == $last) {
        $count++;
    } else {
        $res[$last.$count.'-'.$key] = [
            'dir' => $last,
            'count' => $count,
        ];

        $count = 1;
    }

    $last = $value;
}

$res[$last.$count.'-'.$key] = [
    'dir' => $last,
    'count' => $count,
];

$route = [];

$last = 'n';
foreach ($res as $key=>$value) {
    if ($last == 'n' && $value['dir'] == 'e') {
        $dir = 'r';

    } elseif ($last == 'n' && $value['dir'] == 'w') {
        $dir = 'l';

    } elseif ($last == 's' && $value['dir'] == 'e') {
        $dir = 'l';

    } elseif ($last == 's' && $value['dir'] == 'w') {
        $dir = 'r';

    } elseif ($last == 'w' && $value['dir'] == 'n') {
        $dir = 'r';

    } elseif ($last == 'w' && $value['dir'] == 's') {
        $dir = 'l';

    } elseif ($last == 'e' && $value['dir'] == 'n') {
        $dir = 'l';

    } elseif ($last == 'e' && $value['dir'] == 's') {
        $dir = 'r';
    }

    $route[] = $dir;
    $route[] = $value['count'];
    $route2[] = $dir.'-'.$value['count'];

    $last = $value['dir'];
}

$group = resolvePattern($route);

$ic = new IntCode([
    'logLevel' => 1,
]);
$ic->setCode($input);
$ic->configurePhases([
    'phase' => [
        'phase' => 0,
        'switch' => [
            '0' => 2,
        ],
    ],
]);

$first = true;
while (true) {
    $output = $ic->run(0);

    foreach ($output as $o) {
        if ($o > 1000) {
            $l = str_split($o, 2);
            foreach ($l as $ll) {
                echo chr($ll);
            }
        } else {
            echo chr($o);
        }
    }

    $phase = $ic->getPhase(0);
    if ($phase->halt) {
        dump('system halted');
        die();
    }

    if ($first) {
        $first = false;

        foreach ($group['pattern'] as $key=>$value) {
            $c = ord(strtoupper($value));
            $ic->addInput(0, $c);

            if (count($group['pattern']) > $key+1) {
                $c = ord(',');
                $ic->addInput(0, $c);
            }
        }
        $c = ord(PHP_EOL);
        $ic->addInput(0, $c);

        $list = ['a','b','c'];
        foreach ($list as $l) {
            foreach ($group[$l] as $key=>$value) {

                $o = str_split($value);
                foreach ($o as $oo) {
                    $c = ord(strtoupper($oo));
                    $ic->addInput(0, $c);
                }

                if (count($group[$l]) > $key+1) {
                    $ic->addInput(0, ord(','));
                }
            }

            $ic->addInput(0, ord(PHP_EOL));
        }
    }

    $ic->addInput(0, ord('n'));
    $ic->addInput(0, ord(PHP_EOL));
}

function addIntersection($grid)
{
    $sum = 0;
    $g = $grid->get();

    foreach ($g as $y=>$v1) {
        foreach ($v1 as $x=>$value) {
            $points = [
                [
                    'x' => $x,
                    'y' => $y,
                ],
                [
                    'x' => $x-1,
                    'y' => $y,
                ],
                [
                    'x' => $x+1,
                    'y' => $y,
                ],
                [
                    'x' => $x,
                    'y' => $y-1,
                ],
                [
                    'x' => $x,
                    'y' => $y+1,
                ],
            ];

            $intersection = true;
            foreach ($points as $point) {
                $xkey = $point['x'];
                $ykey = $point['y'];

                if (isset($g[$ykey][$xkey]) && $g[$ykey][$xkey] != '#') {
                    $intersection = false;
                }
            }

            if ($intersection) {
                $sum += $x*$y;

                $g[$y][$x] = 'O';
            }
        }
    }

    $grid->set($g);

    return $sum;
}

function getGrid($grid, $output)
{
    $line = 0;
    $rows = -1;
    $startX = null;
    $startY = null;

    foreach ($output as $key=>$value) {
        switch (true) {
        case $value == 35:
            if ($rows == 1) $line++;

            break;

        case $value == 46:
            if ($rows == 1) $line++;

            break;

        case $value == 10:
            $rows++;

            break;

        default:
            if ($rows == 1) $line++;

            break;
        }
    }

    $line--;
    $rows--;

    $x = 0;
    $y = 0;

    foreach ($output as $key=>$value) {
        switch (true) {
        case $value == 35:
            $grid->append($x, $y, '#');

            if ($rows == 1) $line++;
            $x++;

            break;

        case $value == 46:
            $grid->append($x, $y, '.');

            if ($rows == 1) $line++;
            $x++;

            break;

        case $value == 10:
            $rows++;
            $y++;
            $x = 0;

            break;

        case $value == 94:
            $startX = $x;
            $startY = $y;

            $grid->append($x, $y, chr($value));

            if ($rows == 1) $line++;
            $x++;

            break;

        default:
            dump($value);

            break;
        }
    }

    return [
        'x' => $startX,
        'y' => $startY,
    ];
}

function resolvePattern($route)
{
    $groups = [];

    for ($a = 1; $a < 20; $a++) {
        for ($b = 1; $b < 20; $b++) {
            for ($c = 1; $c < 20; $c++) {
                $map = $route;

                $ares = [];
                $bres = [];
                $cres = [];

                for ($i = 0; $i < $a; $i++) {
                    $ares[] = array_shift($map);
                }
                $ares = implode(',', $ares);

                $map = implode(',', $route);
                $map = preg_replace("/".$ares."(,|)/", "A,", $map);
                $map = preg_split("/,/", $map);

                for ($i = 0; $i < $b; $i++) {
                    while (true) {
                        $o = array_shift($map);
                        if (!$o) break;
                        if (preg_match("/[^ABC]/", $o)) {
                            $bres[] = $o;
                            break;
                        }
                    }
                }
                $bres = implode(',', $bres);

                $map = implode(',', $route);
                $map = preg_replace("/".$ares."(,|)/", "A,", $map);
                $map = preg_replace("/".$bres."(,|)/", "B,", $map);
                $map = preg_split("/,/", $map);

                for ($i = 0; $i < $c; $i++) {
                    while (true) {
                        $o = array_shift($map);
                        if (!$o) break;
                        if (preg_match("/[^ABC]/", $o)) {
                            $cres[] = $o;
                            break;
                        }
                    }
                }
                $cres = implode(',', $cres);

                $map = implode(',', $route);
                $map = preg_replace("/".$ares."(,|)/", "A,", $map);
                $map = preg_replace("/".$bres."(,|)/", "B,", $map);
                $map = preg_replace("/".$cres."(,|)/", "C,", $map);
                $map = preg_replace("/,$/", "", $map);
                $map = preg_split("/,/", $map);

                $success = true;
                foreach ($map as $v) {
                    if (preg_match("/[^ABC]/", $v)) {
                        $success = false;
                    }
                }

                if ($success) {
                    $diff = 0;
                    $len = 0;
                    $quality = 0;

                    $_a = strlen($ares);
                    $_b = strlen($bres);
                    $_c = strlen($cres);

                    $len = $_a+$_b+$_c;

                    $d = $_a-$_b;
                    $diff += ($d < 0) ? $d*-1 : $d;
                    $d = $_a-$_c;
                    $diff += ($d < 0) ? $d*-1 : $d;
                    $d = $_b-$_c;
                    $diff += ($d < 0) ? $d*-1 : $d;

                    $group = [
                        'diff' => $diff,
                        'len' => $len,
                        'quality' => $diff+$len,
                        'pattern' => $map,
                        'a' => preg_split("/,/", $ares),
                        'b' => preg_split("/,/", $bres),
                        'c' => preg_split("/,/", $cres),
                    ];

                    $groups[] = $group;
                }
            }
        }
    }

    $lowest = null;
    $res = null;

    foreach ($groups as $group) {
        if ($lowest === null || $group['len'] < $lowest) {
            $lowest = $group['len'];
            $res = $group;
        }
    }

    return $res;
}
