<?php

require __DIR__.'/bootstrap.php';

$input = <<<EOF
<x=-13, y=14, z=-7>
<x=-18, y=9, z=0>
<x=0, y=-3, z=-3>
<x=-15, y=3, z=-13>
EOF;

$lines = $tools->linesToArray($input);

$moons = [];
foreach ($lines as $l) {
    if (preg_match("/x=((|\-)\d+), y=((|\-)\d+), z=((|\-)\d+)/", $l, $o)) {
        $moons[] = [
            'pos' => [
                'x' => $o[1],
                'y' => $o[3],
                'z' => $o[5],
            ],
            'vel' => [
                'x' => 0,
                'y' => 0,
                'z' => 0,
            ],
        ];
    }
}

for ($i = 0; $i < 1000; $i++) {
    foreach ($moons as $k1=>$moon) {
        foreach ($moons as $k2=>$moon2) {
            if ($k1 == $k2) continue;

            if ($moon['pos']['x'] < $moon2['pos']['x']) {
                $moons[$k1]['vel']['x'] += 1;

            } elseif ($moon['pos']['x'] > $moon2['pos']['x']) {
                $moons[$k1]['vel']['x'] -= 1;
            }

            if ($moon['pos']['y'] < $moon2['pos']['y']) {
                $moons[$k1]['vel']['y'] += 1;

            } elseif ($moon['pos']['y'] > $moon2['pos']['y']) {
                $moons[$k1]['vel']['y'] -= 1;
            }

            if ($moon['pos']['z'] < $moon2['pos']['z']) {
                $moons[$k1]['vel']['z'] += 1;

            } elseif ($moon['pos']['z'] > $moon2['pos']['z']) {
                $moons[$k1]['vel']['z'] -= 1;
            }
        }
    }

    foreach ($moons as $k=>$moon) {
        $moons[$k]['pos']['x'] += $moon['vel']['x'];
        $moons[$k]['pos']['y'] += $moon['vel']['y'];
        $moons[$k]['pos']['z'] += $moon['vel']['z'];
    }
}

dump($moons);

$total = 0;
foreach ($moons as $moon) {
    $moon['pos']['x'] *= ($moon['pos']['x'] < 0) ? -1 : 1;
    $moon['pos']['y'] *= ($moon['pos']['y'] < 0) ? -1 : 1;
    $moon['pos']['z'] *= ($moon['pos']['z'] < 0) ? -1 : 1;

    $moon['vel']['x'] *= ($moon['vel']['x'] < 0) ? -1 : 1;
    $moon['vel']['y'] *= ($moon['vel']['y'] < 0) ? -1 : 1;
    $moon['vel']['z'] *= ($moon['vel']['z'] < 0) ? -1 : 1;

    $pos = $moon['pos']['x'] + $moon['pos']['y'] + $moon['pos']['z'];
    $vel = $moon['vel']['x'] + $moon['vel']['y'] + $moon['vel']['z'];

    $total += $pos*$vel;
}

dump($total);
