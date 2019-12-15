<?php

require __DIR__.'/bootstrap.php';

$file = 'input-15.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$ic = new IntCode();
$ic->setCode($input);
$ic->configurePhases();

$x = 0;
$y = 0;
$sleep = 250*1000;
$sleep = 0;
$auto = 0;
$predefined = [
    1,1,
    3,3,
    2,2,
    3,3,3,3,
    1,1,1,1,1,1,1,1,1,1,
    4,4,
    2,2,2,2,
    4,4,
    2,2,
    4,4,
    1,1,
    4,4,
    2,2,
    4,4,
    1,1,
    4,4,
    2,2,2,2,
    4,4,
    2,2,2,2,
    4,4,4,4,
    2,2,
    3,3,
    2,2,
    4,4,4,4,4,4,4,4,
    1,1,
    3,3,
    1,1,
    4,4,
    1,1,
    3,3,3,3,3,3,
    1,1,
    4,4,4,4,4,4,
    1,1,1,1,1,1,1,1,
    3,3,
    2,2,2,2,
    3,3,
    1,1,1,1,
    3,3,
    2,2,2,2,2,2,
    3,3,3,3,
    1,1,
    4,4,
    1,1,1,1,
    3,3,3,3,
    2,2,
    3,3,3,3,3,3,
    1,1,1,1,
    3,3,3,3,3,3,
    1,1,
    4,4,
    1,1,1,1,
    4,4,4,4,4,4,
    2,2,2,2,
    4,4,
    2,2,
    4,4,4,4,4,4,4,4,
    1,1,1,1,
    3,3,
    2,2,
    3,3,3,3,
    1,1,
    4,4,
    1,1,
    4,4,
    1,1,
    4,4,
    2,2,
    4,4,
    2,2,
    4,4,4,4,
    2,2,
    3,3,
    2,2,
    3,3,
    1,1,
];
dump(count($predefined));die();

$kill = false;
$kill = true;

$grid->set(unserialize(file_get_contents('grid-15.txt')));

while (true) {
    //dump('current location '.$x.','.$y);

    $output = $ic->run(0);
    $halt = $ic->getPhase(0)->halt;

    $continue = false;

    if (isset($output[0])) {
        if ($output[0] === 1) {
            $coord = getCoord($direction, $x, $y);

            $x = $coord['x'];
            $y = $coord['y'];

            $grid->append($x, $y, 'yellow');

            //dump('ok, move to '.$x.','.$y);

            $continue = true;

        } elseif ($output[0] === 2) {
            $grid->append($x, $y, 'green');

            dump('found oxigen');

        } elseif ($output[0] === 0) {
            $coord = getCoord($direction, $x, $y);
            $grid->append($coord['x'], $coord['y'], 'red');

            dump('current: '.$direction);
            dump(implode(', ',$predefined));
            dump('wall');

            if ($kill) die();
        }
    } else {
        //dump('error', $output);
    }

    //dump('halt: '.(int)$halt);

    // 1 north
    // 2 south
    // 3 west
    // 4 east

    $g = $grid->get();

    if (count($predefined)) {
        $direction = array_shift($predefined);

    } else {
        if ($auto == 0) {
            $input = readline('direction');
            switch ($input) {
            case 'auto-50':
                $auto = 50;
                break;

            case 'auto-100':
                $auto = 100;
                break;

            case 'auto-200':
                $auto = 200;
                break;

            case 'w':
                $direction = 1;
                break;

            case 'a':
                $direction = 3;
                break;

            case 's':
                $direction = 2;
                break;

            default:
                $direction = 4;
                break;
            }

        } else {
            if ($kill) die();

            while (true) {
                $direction = rand(1,4);
                $coord = getCoord($direction, $x, $y);

                $nx = $coord['x'];
                $ny = $coord['y'];

                if (!isset($g[$ny]) || !isset($g[$ny][$nx]) || $g[$ny][$nx] != 'red') break;
            }

            $auto--;
        }
    }

    //dump('walk in direction '.$direction);

    $ic->addInput(0, $direction);
    $g[$y][$x] = 'blue';
    $grid->print($g);

    usleep($sleep);

    echo PHP_EOL;

    file_put_contents('grid-15.txt', serialize($g));
}

function getCoord($direction, $x, $y)
{
    switch ($direction) {
    case 1:
        $ywall = $y-1;
        $xwall = $x;
        break;

    case 2:
        $ywall = $y+1;
        $xwall = $x;
        break;

    case 3:
        $ywall = $y;
        $xwall = $x-1;
        break;

    case 4:
        $ywall = $y;
        $xwall = $x+1;
        break;
    }

    return [
        'x' => $xwall,
        'y' => $ywall,
    ];
}
