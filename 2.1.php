<?php

$codes = [
    '1,0,0,3,1,1,2,3,1,3,4,3,1,5,0,3,2,6,1,19,1,19,10,23,2,13,23,27,1,5,27,31,2,6,31,35,1,6,35,39,2,39,9,43,1,5,43,47,1,13,47,51,1,10,51,55,2,55,10,59,2,10,59,63,1,9,63,67,2,67,13,71,1,71,6,75,2,6,75,79,1,5,79,83,2,83,9,87,1,6,87,91,2,91,6,95,1,95,6,99,2,99,13,103,1,6,103,107,1,2,107,111,1,111,9,0,99,2,14,0,0',
];

foreach ($codes as $next) {
    echo 'original code'.PHP_EOL;
    echo $next.PHP_EOL;

    while (true) {
        $nouns = range(0,99);
        $verbs = range(0,99);

        foreach ($nouns as $noun) {
            foreach ($verbs as $verb) {

                $pos = 0;
                $code = preg_split("/,/", $next);

                $code[1] = $noun;
                $code[2] = $verb;

                while (true) {
                    if ($code[$pos] == 99) {
                        break;
                    }

                    $pos1 = $pos+1;
                    $pos2 = $pos+2;
                    $pos3 = $pos+3;

                    $in1 = $code[$pos1];
                    $in2 = $code[$pos2];
                    $in3 = $code[$pos3];

                    $val1 = $code[$in1];
                    $val2 = $code[$in2];
                    $val3 = $code[$in3];

                    if ($code[$pos] == 1) {
                        $res = $val1+$val2;

                    } elseif ($code[$pos] == 2) {
                        $res = $val1*$val2;

                    } else {
                        echo 'not supported code '.$code[$pos];
                        die();
                    }

                    $code[$in3] = $res;
                    $pos += 4;

                    if (!isset($code[$pos])) {
                        break;
                    }
                }

                if ($code[0] == 19690720) {
                    echo implode(',', $code).PHP_EOL;
                    echo PHP_EOL;
                    echo 'INPUT 1: '.$noun.PHP_EOL;
                    echo 'INPUT 2: '.$verb.PHP_EOL;

                    echo 'result is '.(100*$noun+$verb).PHP_EOL;

                    die();
                }
            }
        }

        die('did not find the answer');
    }
}
