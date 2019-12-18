<?php

require __DIR__.'/bootstrap.php';

$file = 'input-17.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$ic = new IntCode();
$ic->setCode($input);
$o = $ic->process();

$output = $o['output'];

$start = getGrid($grid, $output);
$sum = $grid->addIntersection();

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

$spawn = $robot->run(true, true, 250);
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

$group = $tools->resolvePattern($route);

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

