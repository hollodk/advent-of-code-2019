<?php

require __DIR__.'/bootstrap.php';

$file = 'input-19.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$x = 456;
$y = 601;
$sq = 100;

/*
for ($y = 0; $y < 6; $y++) {
    for ($x = 0; $x < 6; $x++) {
        $out = test($input, $x, $y);

        $grid->append($x, $y, $out);
        $grid->printn();

        echo PHP_EOL;
    }
}

$g = $grid->get();
$y = count($g)-1;
$line = array_pop($g);
$x = 0;
 */

while (true) {
    dump($x.','.$y);

    $out = test($input, $x, $y);

    $grid->append($x, $y, $out);

    if ($out == 1) {
        /*
        $range = range(0,$x);

        $g = $grid->get();
        foreach ($range as $r) {
            if (!isset($g[$y][$r])) {
                $grid->append($r, $y, 'o');
            }
        }
         */

        $grid->append($x+$sq, $y, $out);

        $out = test($input, $x, $y-$sq);
        dump('looking for '.$sq.' in y, '.$x.','.($y-$sq));

        if ($out == 1) {
            dump('found');

            $out = test($input, $x+$sq, $y-$sq);
            dump('looking for '.$sq.' in x, '.($x+$sq).','.($y-$sq));

            if ($out == 1) {
                $res = ($y-$sq)*10000+$x;
                dump('found '.$res);

                die();
            }
        }

        $y += 1;

    } else {
        $x += 1;
    }
}

function test($input, $x, $y)
{
    $ic = new IntCode();
    $ic->setCode($input);
    $ic->configurePhases();

    $ic->addInput(0,$x);
    $ic->addInput(0,$y);

    $o = $ic->run(0);
    return $o[0];
}
