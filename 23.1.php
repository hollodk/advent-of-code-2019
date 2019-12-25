<?php

// 19405 too low

require __DIR__.'/bootstrap.php';

$file = 'input-23.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$nics = [];
$range = range(0,49);
$nat = [
    'x' => null,
    'y' => null,
];
$lastDelivered = null;

foreach ($range as $r) {
    $int = new IntCode([
        'logLevel' => 0
    ]);
    $int->setCode($input);
    $int->configurePhases();
    $int->addInput(0, $r);

    $nics[$r] = [
        'address' => $r,
        'int' => $int,
    ];
}

$idle = 0;

while (true) {
    foreach ($nics as $nic) {
        $int = $nic['int'];

        $o = $int->run(0);
        if (count($o) > 0) {
            $packages = [];
            $p = [];
            $new = false;

            foreach ($o as $oo) {
                $p[] = $oo;

                if (count($p) == 3) {
                    $packages[] = $p;
                    $p = [];
                }
            }

            //dump('sending from '.$nic['address'].', packages '.count($packages));

            foreach ($packages as $package) {
                if ($package[0] == 255) {
                    $nat['x'] = $package[1];
                    $nat['y'] = $package[2];

                } else {
                    //dump('sending to '.$package[0]);

                    $i = $nics[$package[0]]['int'];
                    $i->addInput(0, $package[1]);
                    $i->addInput(0, $package[2]);
                }
            }

            $idle = 0;

        } else {
            //dump('sending -1 on '.$nic['address']);
            $int->addInput(0, -1);

            $idle++;
        }
    }

    dump('idle count '.$idle);

    if ($idle > 200 && $nat['x'] != null) {
        dump('inject network with', $nat);
        $int = $nics[0]['int'];

        $int->addInput(0, $nat['x']);
        $int->addInput(0, $nat['y']);

        if ($nat['x'].'-'.$nat['y'] == $lastDelivered) {
            dump($nat);

            die('found last twice sent');
        }

        $lastDelivered = $nat['x'].'-'.$nat['y'];
    }

    //sleep(1);
}
