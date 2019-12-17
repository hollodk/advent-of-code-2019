<?php

require __DIR__.'/bootstrap.php';

$file = 'input-17.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$file = '/tmp/cache.txt';

if (file_exists($file)) {
    $output = json_decode(file_get_contents($file));
} else {
    $ic = new IntCode();
    $ic->setCode($input);
    $o = $ic->process();

    $output = $o['output'];

    file_put_contents($file, json_encode($output));
}

getGrid($grid, $output);
$sum = addIntersection($grid);

$g = $grid->get();
$grid->print($g);

dump('sum: '.$sum);

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

        default:
            $grid->append($x, $y, '@');

            if ($rows == 1) $line++;
            $x++;

            break;
        }
    }
}
