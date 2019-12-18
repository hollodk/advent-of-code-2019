<?php

require __DIR__.'/bootstrap.php';

$input = <<<EOF
<x=-13, y=14, z=-7>
<x=-18, y=9, z=0>
<x=0, y=-3, z=-3>
<x=-15, y=3, z=-13>
EOF;

/*
$total = 4686774924;
$input = <<<EOF
<x=-8, y=-10, z=0>
<x=5, y=5, z=10>
<x=2, y=-7, z=3>
<x=9, y=-8, z=-3>
EOF;

$total = 2772;
$input = <<<EOF
<x=-1, y=0, z=2>
<x=2, y=-10, z=-7>
<x=4, y=-8, z=8>
<x=3, y=5, z=-1>
EOF;
 */

$lines = $tools->linesToArray($input);

$moons = [];
$orbits = [
    0 => [],
    1 => [],
    2 => [],
    3 => [],
];

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

$compare = [];
$comparex = [];
$comparey = [];
$comparez = [];

foreach ($moons as $key=>$moon) {
    $compare[$key] = compare($moon);
    $comparex[$key] = compare($moon, 'x');
    $comparey[$key] = compare($moon, 'y');
    $comparez[$key] = compare($moon, 'z');
}

$loop = 0;
$lastx = 0;
$lasty = 0;
$lastz = 0;

$ox = null;
$oy = null;
$oz = null;

$start = time();

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

    /*
    foreach ($compare as $key=>$moon) {
        if ($compare[$key] == compare($moons[$key])) {
            $divide = round($total/$loop, 8);
            $mod = ($total%$loop);

            if ($mod == 0) {
                dump($key.': '.$loop.', divide by '.$divide);
            }
        }
    }
     */

    $foundx = false;
    $foundy = false;
    $foundz = false;

    $allx = true;
    $ally = true;
    $allz = true;

    $divide = 0;

    foreach ($comparex as $key=>$moon) {
        if ($moon != compare($moons[$key],'x')) {
            $allx = false;
        }
    }

    if ($allx) {
        $diff = $loop-$lastx;
        $lastx = $loop;
        $foundx = true;
        $ox = $diff;

        dump($key.':x '.$loop.', lastx: '.$diff.', divide by '.$divide);
    }

    foreach ($comparey as $key=>$moon) {
        if ($moon != compare($moons[$key],'y')) {
            $ally = false;
        }
    }

    if ($ally) {
        $diff = $loop-$lasty;
        $lasty = $loop;
        $foundy = true;
        $oy = $diff;

        dump($key.':y '.$loop.', lasty: '.$diff.', divide by '.$divide);
    }

    foreach ($comparez as $key=>$moon) {
        if ($moon != compare($moons[$key],'z')) {
            $allz = false;
        }
    }

    if ($allz) {
        $diff = $loop-$lastz;
        $lastz = $loop;
        $foundz = true;
        $oz = $diff;

        dump($key.':z '.$loop.', lastz: '.$diff.', divide by '.$divide);
    }

    if ($loop > 100000 && $ox && $oy && $oz) break;

    if (($loop%500000) == 0) {
        $diff = time()-$start;

        echo number_format($loop).', '.round($loop/$diff).'/sec'.PHP_EOL;
    }

    if ($foundx || $foundy || $foundz) {
        echo PHP_EOL;
    }
}

dump('find lcm of: '.$ox.', '.$oy.', '.$oz);

function compare($moon, $coord=null)
{
    if ($coord) {
        return sprintf('%s,%s',
            $moon['pos'][$coord],
            $moon['vel'][$coord]
        );
    }

    return sprintf('%s,%s,%s-%s,%s,%s',
        $moon['pos']['x'],
        $moon['pos']['y'],
        $moon['pos']['z'],
        $moon['vel']['x'],
        $moon['vel']['y'],
        $moon['vel']['z']
    );
}
