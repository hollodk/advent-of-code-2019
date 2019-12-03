<?php

$line1 = 'R1001,D915,R511,D336,L647,D844,R97,D579,L336,U536,L645,D448,R915,D473,L742,D470,R230,D558,R214,D463,L374,D450,R68,U625,L937,D135,L860,U406,L526,U555,R842,D988,R819,U995,R585,U218,L516,D756,L438,U921,R144,D62,R238,U144,R286,U934,L682,U13,L287,D588,L880,U630,L882,D892,R559,D696,L329,D872,L946,U219,R593,U536,R402,U946,L866,U690,L341,U729,R84,U997,L579,D609,R407,D846,R225,U953,R590,U79,R590,U725,L890,D384,L442,D364,R600,D114,R39,D962,R413,U698,R762,U520,L180,D557,R35,U902,L476,U95,R830,U858,L312,U879,L85,U620,R505,U248,L341,U81,L323,U296,L53,U532,R963,D30,L380,D60,L590,U699,R967,U88,L725,D730,R706,D337,L248,D46,R131,U541,L313,U508,R120,D719,R28,U342,R555,U780,R397,D523,L619,D820,R865,D4,L790,D544,L873,D249,L220,U343,R818,U803,R309,D576,R811,D717,L800,D171,R523,U630,L854,U265,R207,U147,R518,U237,R822,D672,L140,U580,R408,D739,L519,U759,R664,D61,R258,D313,R472,U437,R975,U828,L54,D892,L370,U509,L80,U593,L268,U856,L177,U950,L266,U29,R493,D228,L110,U390,L92,U8,L288,U732,R459,D422,R287,D359,R915,U295,R959,U215,R82,D357,L970,D782,L653,U399,L50,D720,R788,D396,L562,D560,R798,D196,R79,D732,R332,D957,L106,D199,R756,U379,R716,U282,R812,U346,R592,D416,L454,U612,L160,U884,R373,U306,R55,D492,R175,D233,L249,D616,L342,D650,L181,U868,L761,D170,L976,U711,R377,D113,L548,U39,R62,D99,R853,U249,L951,U617,R257,U457,R430,D355,L541,U595,L176,D987,R365,D77,L181,D192,L688,D942,R617,U484,R247,U180,R771,D392,R184,U597,L682,U454,R856,U616,R174,U629,L607,U41,L970,D602,R402,D208,R826';
/*
$line1 = 'R75,D30,R83,U83,L12,D49,R71,U7,L72';
$line1 = 'R98,U47,R26,D63,R33,U87,L62,D20,R33,U53,R51';
$line1 = 'R8,U5,L5,D3';
 */

$line2 = 'L994,U238,R605,U233,L509,U81,R907,U880,R666,D86,R6,U249,R345,D492,L912,U770,L827,D107,R988,D525,L471,U706,R31,U485,R835,D778,R419,D461,L937,D740,R559,U309,L379,U385,R828,D698,R276,U914,L911,U969,R282,D365,L43,D911,R256,D592,L451,U162,L829,D564,R349,U279,R19,D110,R259,D551,L172,D899,L924,D819,R532,U737,L794,U995,R168,D359,R847,U426,R224,U984,L929,D531,L797,U292,L332,D280,R317,D648,R776,D52,R916,U363,R919,U890,R583,U961,L89,D680,L894,D226,L83,U68,R551,U413,R259,D468,L702,U453,L128,U986,R238,U805,R431,U546,R944,D142,R677,D783,R336,D220,R40,U391,R5,D760,L963,D764,R653,U932,R473,U311,L189,D883,R216,U391,L634,U275,L691,U975,R130,D543,L163,U736,R964,U729,R752,D531,R90,D471,R687,D341,R441,U562,R570,U278,R570,U177,L232,U781,L874,U258,R180,D28,R916,D395,R96,U954,L222,U578,L394,U775,L851,D18,L681,D912,L761,U945,L866,D12,R420,D168,R490,U679,R521,D91,L782,U583,L823,U656,L365,D517,R319,U725,L824,D531,L747,U822,R893,D162,L11,D913,L295,D65,L393,D351,L432,U828,L131,D384,R311,U381,L26,D635,L180,D395,L576,D836,R548,D820,L219,U749,L64,D2,L992,U104,L501,U247,R693,D862,R16,U346,R332,U618,R387,U4,L206,U943,R734,D164,R771,U17,L511,D475,L75,U965,R116,D627,R243,D77,R765,D831,L51,U879,R207,D500,R289,U749,R206,D850,R832,U407,L985,U514,R290,U617,L697,U812,L633,U936,R214,D447,R509,D585,R787,D500,R305,D598,R866,U781,L771,D350,R558,U669,R284,D686,L231,U574,L539,D337,L135,D751,R315,D344,L694,D947,R118,U377,R50,U181,L96,U904,L776,D268,L283,U233,L757,U536,L161,D881,R724,D572,R322';
/*
$line2 = 'U62,R66,U55,R34,D71,R55,D58,R83';
$line2 = 'U98,R91,D20,R16,D67,R40,U7,R15,U6,R7';
$line2 = 'U7,R6,D4,L4';
 */

$path1 = getPath($line1);
$path2 = getPath($line2);

$c1 = count($path1);
$c2 = count($path2);

echo 'length path 1 '.$c1.PHP_EOL;
echo 'length path 2 '.$c2.PHP_EOL;

$stepCache1 = [];
$stepCache2 = [];

$cross = [];
$shortest = 100000;
$steps = 100000;
$shortId = null;
$stepId = null;

$t1 = time();

for ($i1 = 0; $i1 < $c1; $i1++) {
    for ($i2 = 0; $i2 < $c2; $i2++) {

        if ($path1[$i1] === $path2[$i2]) {
            if ($path1[$i1] !== '0,0') {

                list($x,$y) = preg_split("/,/", $path1[$i1]);

                $r = [
                    'x' => $x,
                    'y' => $y,
                ];

                if (!isset($stepCache1[$path1[$i1]])) {
                    $stepCache1[$path1[$i1]] = $i1;
                    $step1 = $i1;

                } else {
                    $step1 = $stepCache1[$path1[$i1]];
                }

                if (!isset($stepCache2[$path2[$i2]])) {
                    $stepCache2[$path1[$i2]] = $i2;
                    $step2 = $i2;

                } else {
                    $step2 = $stepCache2[$path2[$i2]];
                }

                $r['steps'] = $step1+$step2;

                $x = ($r['x'] < 0) ? $r['x']*-1 : $r['x'];
                $y = ($r['y'] < 0) ? $r['y']*-1 : $r['y'];

                $r['dist'] = $x+$y;

                $cross[] = $r;

                if ($r['dist'] < $shortest) {
                    $shortest = $r['dist'];
                    $shortId = $r;
                }

                if ($r['steps'] < $steps) {
                    $steps = $r['steps'];
                    $stepId = $r;
                }
            }
        }
    }

    if (($i1%1000) == 0) {
        $d = time()-$t1;
        $t1 = time();

        echo 'left: '.($c1-$i1).PHP_EOL;
        echo 'split time: '.$d.PHP_EOL;
    }
}

var_dump('time: '.$d, 'steps: '.$steps, 'shortest: '.$shortest, $shortId, $stepId);

function getPath($line)
{
    $lastX = 0;
    $lastY = 0;

    $path = ['0,0'];
    $line = preg_split("/,/", $line);

    foreach ($line as $l) {
        if (preg_match("/^(\w)(\d+)$/", $l, $o)) {
            switch ($o[1]) {
            case 'R':
                for ($i = 1; $i <= $o[2]; $i++) {
                    $nextX = $lastX+$i;
                    $nextY = $lastY;

                    $path[] = $nextX.','.$nextY;
                }

                break;

            case 'L':
                for ($i = 1; $i <= $o[2]; $i++) {
                    $nextX = $lastX-$i;
                    $nextY = $lastY;

                    $path[] = $nextX.','.$nextY;
                }

                break;

            case 'D':
                for ($i = 1; $i <= $o[2]; $i++) {
                    $nextX = $lastX;
                    $nextY = $lastY-$i;

                    $path[] = $nextX.','.$nextY;
                }

                break;

            case 'U':
                for ($i = 1; $i <= $o[2]; $i++) {
                    $nextX = $lastX;
                    $nextY = $lastY+$i;

                    $path[] = $nextX.','.$nextY;
                }

                break;

            default:
                throw new \Exception('not supported '.$o[1]);
            }

            $lastX = $nextX;
            $lastY = $nextY;
        }
    }

    return $path;
}
