<?php

require __DIR__.'/bootstrap.php';

$input = <<<EOF
<x=-13, y=14, z=-7>
<x=-18, y=9, z=0>
<x=0, y=-3, z=-3>
<x=-15, y=3, z=-13>
EOF;

/*
$input = <<<EOF
<x=-1, y=0, z=2>
<x=2, y=-10, z=-7>
<x=4, y=-8, z=8>
<x=3, y=5, z=-1>
EOF;
 */

$lines = $tools->linesToArray($input);

$moons = [];
foreach ($lines as $l) {
    if (preg_match("/x=((|\-)\d+), y=((|\-)\d+), z=((|\-)\d+)/", $l, $o)) {
        $moons[] = [
            'pos' => [
                'x' => (int)$o[1],
                'y' => (int)$o[3],
                'z' => (int)$o[5],
            ],
            'vel' => [
                'x' => 0,
                'y' => 0,
                'z' => 0,
            ],
        ];
    }
}

$compare = json_encode($moons);
$originCompare = $moons[0]['pos']['x'].$moons[0]['vel']['x'];

$loop = 0;
while (true) {
    $loop++;
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

    $simpleCompare = $moons[0]['pos']['x'].$moons[0]['vel']['x'];
    if ($originCompare == $simpleCompare) {
        $d = json_encode($moons);
        if ($compare == $d) break;
    }

    if (($loop%100000) == 0) echo number_format($loop).PHP_EOL;
}

dump($moons, $loop);
dump($originCompare, $simpleCompare);
