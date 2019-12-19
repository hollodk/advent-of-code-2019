<?php

require __DIR__.'/bootstrap.php';

$file = 'input-19.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$g = $grid->build(49, 49, 0);
$total = 0;

foreach ($g as $y=>$v1) {
    foreach ($v1 as $x=>$v2) {
        dump($x.','.$y.' total '.$total);

        $ic = new IntCode([
            'logLevel' => 1,
        ]);
        $ic->setCode($input);
        $ic->configurePhases();

        $ic->addInput(0,$x);
        $ic->addInput(0,$y);

        $o = $ic->run(0);

        if ($o[0] == 1) {
            $total++;
        }

        $g[$y][$x] = $o[0];
    }
}

file_put_contents('grid-19.txt', serialize($grid));

$grid->set($g);
$grid->print();

dump($total);
