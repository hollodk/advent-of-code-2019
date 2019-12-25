<?php

require __DIR__.'/bootstrap.php';

$file = 'input-23.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$nics = [];
$range = range(0,49);

foreach ($range as $r) {
    $int = new IntCode([
        'logLevel' => 1
    ]);
    $int->setCode($input);
    $int->configurePhases();
    $int->addInput(0, $r);

    $nics[$r] = [
        'address' => $r,
        'int' => $int,
    ];
}

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

            dump('sending from '.$nic['address'].', packages '.count($packages));

            foreach ($packages as $package) {
                if ($package[0] == 255) {
                    dump($package);die();
                }

                dump('sending to '.$package[0]);

                $i = $nics[$package[0]]['int'];
                $i->addInput(0, $package[1]);
                $i->addInput(0, $package[2]);
            }

        } else {
            dump('sending -1 on '.$nic['address']);
            $int->addInput(0, -1);
        }
    }

    sleep(1);
}
