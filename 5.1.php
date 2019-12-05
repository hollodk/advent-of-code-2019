<?php

$codes = [
    '1,0,0,3,1,1,2,3,1,3,4,3,1,5,0,3,2,6,1,19,1,19,10,23,2,13,23,27,1,5,27,31,2,6,31,35,1,6,35,39,2,39,9,43,1,5,43,47,1,13,47,51,1,10,51,55,2,55,10,59,2,10,59,63,1,9,63,67,2,67,13,71,1,71,6,75,2,6,75,79,1,5,79,83,2,83,9,87,1,6,87,91,2,91,6,95,1,95,6,99,2,99,13,103,1,6,103,107,1,2,107,111,1,111,9,0,99,2,14,0,0',
    '3,21,1008,21,8,20,1005,20,22,107,8,21,20,1006,20,31,1106,0,36,98,0,0,1002,21,125,20,4,20,1105,1,46,104,999,1105,1,46,1101,1000,1,20,4,20,1105,1,46,98,99',
    '3,225,1,225,6,6,1100,1,238,225,104,0,1002,36,25,224,1001,224,-2100,224,4,224,1002,223,8,223,101,1,224,224,1,223,224,223,1102,31,84,225,1102,29,77,225,1,176,188,224,101,-42,224,224,4,224,102,8,223,223,101,3,224,224,1,223,224,223,2,196,183,224,1001,224,-990,224,4,224,1002,223,8,223,101,7,224,224,1,224,223,223,102,14,40,224,101,-1078,224,224,4,224,1002,223,8,223,1001,224,2,224,1,224,223,223,1001,180,64,224,101,-128,224,224,4,224,102,8,223,223,101,3,224,224,1,223,224,223,1102,24,17,224,1001,224,-408,224,4,224,1002,223,8,223,101,2,224,224,1,223,224,223,1101,9,66,224,1001,224,-75,224,4,224,1002,223,8,223,1001,224,6,224,1,223,224,223,1102,18,33,225,1101,57,64,225,1102,45,11,225,1101,45,9,225,1101,11,34,225,1102,59,22,225,101,89,191,224,1001,224,-100,224,4,224,1002,223,8,223,1001,224,1,224,1,223,224,223,4,223,99,0,0,0,677,0,0,0,0,0,0,0,0,0,0,0,1105,0,99999,1105,227,247,1105,1,99999,1005,227,99999,1005,0,256,1105,1,99999,1106,227,99999,1106,0,265,1105,1,99999,1006,0,99999,1006,227,274,1105,1,99999,1105,1,280,1105,1,99999,1,225,225,225,1101,294,0,0,105,1,0,1105,1,99999,1106,0,300,1105,1,99999,1,225,225,225,1101,314,0,0,106,0,0,1105,1,99999,8,226,677,224,1002,223,2,223,1006,224,329,1001,223,1,223,108,226,226,224,1002,223,2,223,1006,224,344,1001,223,1,223,7,677,226,224,102,2,223,223,1005,224,359,101,1,223,223,7,226,677,224,102,2,223,223,1006,224,374,101,1,223,223,1008,677,226,224,1002,223,2,223,1006,224,389,101,1,223,223,8,677,677,224,1002,223,2,223,1005,224,404,101,1,223,223,8,677,226,224,102,2,223,223,1005,224,419,1001,223,1,223,1107,677,226,224,102,2,223,223,1005,224,434,1001,223,1,223,1107,226,677,224,1002,223,2,223,1006,224,449,1001,223,1,223,107,677,226,224,1002,223,2,223,1005,224,464,1001,223,1,223,1008,677,677,224,1002,223,2,223,1006,224,479,1001,223,1,223,1108,677,226,224,1002,223,2,223,1006,224,494,1001,223,1,223,1108,677,677,224,1002,223,2,223,1006,224,509,1001,223,1,223,107,677,677,224,1002,223,2,223,1005,224,524,101,1,223,223,1007,677,226,224,102,2,223,223,1005,224,539,1001,223,1,223,1107,226,226,224,1002,223,2,223,1006,224,554,1001,223,1,223,1008,226,226,224,1002,223,2,223,1006,224,569,101,1,223,223,1108,226,677,224,1002,223,2,223,1006,224,584,101,1,223,223,108,677,677,224,1002,223,2,223,1006,224,599,1001,223,1,223,1007,677,677,224,102,2,223,223,1006,224,614,101,1,223,223,107,226,226,224,102,2,223,223,1006,224,629,101,1,223,223,1007,226,226,224,102,2,223,223,1005,224,644,1001,223,1,223,108,226,677,224,102,2,223,223,1005,224,659,1001,223,1,223,7,677,677,224,102,2,223,223,1006,224,674,1001,223,1,223,4,223,99,226',
];

foreach ($codes as $loop=>$next) {
    $debug = false;
    $input = 5;

    $pointer = 0;
    $output = '';

    $code = preg_split("/,/", $next);

    if ($loop == 0) {
        $code[1] = 12;
        $code[2] = 2;
    }

    if ($debug) {
        foreach ($code as $i=>$o) {
            echo $i.': '.$o.PHP_EOL;
        }
        echo PHP_EOL;
    }

    while (true) {
        $pos = $pointer;
        $instruction = $code[$pos];

        debug('pos: '.$pos.', instruction: '.$instruction, $debug);

        $p1 = 0;
        $p2 = 0;
        $p3 = 0;
        $p4 = 0;

        if ($instruction > 10) {
            $o = sprintf('%05d', $instruction);

            $instruction = (int)substr($o, -2);

            $p1 = (int)substr($o, -3, 1);
            $p2 = (int)substr($o, -4, 1);
            $p3 = (int)substr($o, -5, 1);
            $p4 = (int)substr($o, -6, 1);
        }

        debug('instrustion: '.$instruction.', p1: '.$p1.', p2: '.$p2.', p3: '.$p3.', p4: '.$p4, $debug);

        if ($instruction == 99) break;

        $newInstruction = null;
        $res = null;

        switch ($instruction) {
        case 1:
            $next = 4;
            $outkey = getOutKey($p3, $code, $pos, 3);

            $val1 = getVal($p1, $code, $pos, 1);
            $val2 = getVal($p2, $code, $pos, 2);

            $res = $val1+$val2;

            debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey, $debug);

            break;

        case 2:
            $next = 4;
            $outkey = getOutKey($p3, $code, $pos, 3);

            $val1 = getVal($p1, $code, $pos, 1);
            $val2 = getVal($p2, $code, $pos, 2);

            $res = $val1*$val2;

            debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey, $debug);

            break;

        case 3:
            $next = 2;
            $outkey = getOutKey($p1, $code, $pos, 1);

            $res = $input;

            debug('i '.$instruction.', o '.$outkey, $debug);

            break;

        case 4:
            $next = 2;
            $outkey = getOutKey($p1, $code, $pos, 1);

            $output .= $code[$outkey];

            debug('i '.$instruction.', o '.$outkey, $debug);

            break;

        case 5:
            $next = 3;

            $val1 = getVal($p1, $code, $pos, 1);
            $val2 = getVal($p2, $code, $pos, 2);

            $newInstruction = null;
            if ($val1 != 0) {
                $next = 0;
                $newInstruction = $val2;
            }

            debug('i '.$newInstruction.', v1 '.$val1.', v2 '.$val2, $debug);

            break;

        case 6:
            $next = 3;

            $val1 = getVal($p1, $code, $pos, 1);
            $val2 = getVal($p2, $code, $pos, 2);

            $newInstruction = null;
            if ($val1 == 0) {
                $next = 0;
                $newInstruction = $val2;
            }

            debug('i '.$newInstruction.', v1 '.$val1.', v2 '.$val2, $debug);

            break;

        case 7:
            $next = 4;
            $outkey = getOutKey($p3, $code, $pos, 3);

            $val1 = getVal($p1, $code, $pos, 1);
            $val2 = getVal($p2, $code, $pos, 2);

            $res = ($val1 < $val2) ? 1 : 0;

            debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey, $debug);

            break;

        case 8:
            $next = 4;
            $outkey = getOutKey($p3, $code, $pos, 3);

            $val1 = getVal($p1, $code, $pos, 1);
            $val2 = getVal($p2, $code, $pos, 2);

            $res = ($val1 == $val2) ? 1 : 0;

            debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey, $debug);

            break;

        default:
            throw new \Exception('not supported code '.$instruction);
        }

        if ($res !== null) {
            debug('switch data on pos '.$outkey.', from '.$code[$outkey].' to '.$res, $debug);
            $code[$outkey] = $res;
        }

        if (!$newInstruction) {
            $pointer += $next;
        } else {
            $pointer = $newInstruction;
        }

        if (!isset($code[$pos])) throw new \Exception('out of scope');
    }

    echo PHP_EOL;

    $result = $output;
    if ($output) {
        var_dump('!!! RESULT !!! ', $result);
    }

    if ($loop == 0) {
        $result = $code[0];
        var_dump('!!! RESULT !!! ', $result);

        if ($result != '4138687') {
            var_dump(' !!! ERROR !!! ');
            die();
        }
    }

    echo PHP_EOL;
    sleep(1);
}

function getVal($p, $code, $pos, $offset)
{
    if ($p == 0) {
        $in = $code[$pos+$offset];
        $val = $code[$in];

    } else {
        $val = $code[$pos+$offset];
    }

    return $val;
}

function getOutKey($p, $code, $pos, $offset)
{
    if ($p == 0) {
        $outkey = $code[$pos+$offset];
    } else {
        $outkey = $pos+$offset;
    }

    return $outkey;
}

function debug($msg, $debug)
{
    if ($debug) {
        var_dump($msg);
    }
}
