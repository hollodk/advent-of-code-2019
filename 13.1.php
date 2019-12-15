<?php

$noTests = true;

require __DIR__.'/bootstrap.php';

$file = 'input-13.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$options = [
    'phase' => [
        'phase' => 0,
        'switch' => [
            '0' => 2,
        ],
    ],
];

$ic = new IntCode([
    'logLevel' => 1,
]);
$ic->setCode($input);
$ic->configurePhases($options);

$score = null;
$pad = new \StdClass();
$ball = new \StdClass();

while (true) {
    $out = $ic->run(0);
    $phase = $ic->getPhase(0);

    $chunk = array_chunk($out, 3);

    foreach ($chunk as $o) {
        if ($o[0] == '-1' && $o[1] == 0) {
            $score = $o[2];

        } else {
            switch ($o[2]) {
            case 0:
                break;

            case 1:
                // wall
                $grid->append($o[0], $o[1], 'red');
                break;

            case 2:
                // block
                $grid->append($o[0], $o[1], 'green');
                break;

            case 3:
                // paddle
                $pad->x = $o[0];
                $pad->y = $o[1];

                $grid->append($o[0], $o[1], 'yellow');

                break;

            case 4:
                // ball
                $ball->x = $o[0];
                $ball->y = $o[1];

                $grid->append($o[0], $o[1], 'blue');

                break;

            default:
                throw new \Exception('not supported');
            }
        }
    }

    $i = $grid->get();
    $grid->print($i,2 );

    $move = 0;
    if ($ball->x > $pad->x) {
        $move = 1;
    } elseif ($ball->x < $pad->x) {
        $move = -1;
    }

    dump('score: '.$score);

    $ic->addInput(0, $move);
    die();
}
