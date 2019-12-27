<?php

require __DIR__.'/bootstrap.php';

$file = 'input-21.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$ic = new IntCode([
    'logLevel' => 1,
]);
$ic->setCode($input);
$ic->configurePhases();

/*
AND X Y
OR X Y
NOT X Y
 */

$path = [];
$path[] = 'OR A J';
$path[] = 'AND C J';
$path[] = 'NOT J J';
$path[] = 'AND D J';

$path[] = 'WALK';

/*
$path[] = 'NOT A J';
$path[] = 'NOT B T';
$path[] = 'AND T J';
$path[] = 'NOT C T';
$path[] = 'AND T J';
$path[] = 'NOT D J';
$path[] = 'AND T J';
 */

foreach ($path as $p) {
    $r = $tools->toAscii($p, true);

    foreach ($r as $rr) {
        $ic->addInput(0, $rr);
    }
}

while (true) {
    $o = $ic->run(0);
    dump($o);
    dump($tools->printAscii($o));

    usleep(100000);
}
