<?php

require_once(__DIR__.'/bootstrap.php');

$input = trim(file_get_contents(__DIR__.'/input-11.txt'));

$ic = new IntCode([
    'logLevel' => 1,
]);
$ic->setCode($input);
$phases = $ic->configurePhases();

$gridSize = 1000;
$grid = [];
$range = range(0,$gridSize);
foreach ($range as $k1=>$y) {
    foreach ($range as $k2=>$x) {
        $key = $x.','.$y;
        $grid[$key] = [
            'painted' => false,
            'color' => 'black',
        ];
    }
}

$x = $gridSize/2;
$y = $gridSize/2;
$facing = 360;
$painted = 0;

try {
    $loop = 0;
    while (true) {
        $loop++;
        $key = $x.','.$y;
        $input = ($grid[$key]['color'] == 'black') ? 0 : 1;

        $currentX = $x;
        $currentY = $y;
        $currentColor = $grid[$key]['color'];

        $ic->addInput(0, $input);
        $out = $ic->run(0);

        switch ($out[0]) {
        case 0:
            $color = 'black';
            break;

        case 1:
            $color = 'white';
            break;

        default:
            throw new \Exception('bad color: '.$out[0]);
        }

        if ($grid[$key]['painted'] == false) {
            $painted++;
        }

        $grid[$key]['painted'] = true;
        $grid[$key]['color'] = $color;

        switch ($out[1]) {
        case 0:
            $dir = 'left';
            $facing -= 90;
            break;

        case 1:
            $dir = 'right';
            $facing += 90;
            break;

        default:
            throw new \Exception('bad direction: '.$out[1]);
        }

        $facing = ($facing == 0) ? 360 : $facing;
        if ($facing > 360) {
            $facing = $facing-360;
        }

        switch ($facing) {
        case 360:
            $y -= 1;
            break;

        case 90:
            $x += 1;
            break;

        case 180:
            $y += 1;
            break;

        case 270:
            $x -= 1;
            break;

        default:
            throw new \Exception('bad facing: '.$facing);
        }

        debug($currentX, $currentY, $currentColor, $input, $out, $color, $dir, $facing, $x, $y, $painted);
    }

} catch (\Exception $e) {
    var_dump('painted '.$painted);
}

function debug($currentX, $currentY, $currentColor, $input, $out, $color, $dir, $facing, $x, $y, $painted)
{
    echo 'current pos '.$currentX.','.$currentY.PHP_EOL;
    echo 'current color '.$currentColor.PHP_EOL;
    echo 'input '.$input.PHP_EOL;
    echo 'output: '.implode(', ', $out).PHP_EOL;
    echo 'paint '.$color.PHP_EOL;
    echo 'turn '.$dir.PHP_EOL;
    echo 'facing '.$facing.PHP_EOL;
    echo 'move to '.$x.','.$y.PHP_EOL;
    echo 'total painted '.$painted.PHP_EOL;
    echo PHP_EOL;

}
