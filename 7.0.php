<?php

$code = '3,225,1,225,6,6,1100,1,238,225,104,0,1002,36,25,224,1001,224,-2100,224,4,224,1002,223,8,223,101,1,224,224,1,223,224,223,1102,31,84,225,1102,29,77,225,1,176,188,224,101,-42,224,224,4,224,102,8,223,223,101,3,224,224,1,223,224,223,2,196,183,224,1001,224,-990,224,4,224,1002,223,8,223,101,7,224,224,1,224,223,223,102,14,40,224,101,-1078,224,224,4,224,1002,223,8,223,1001,224,2,224,1,224,223,223,1001,180,64,224,101,-128,224,224,4,224,102,8,223,223,101,3,224,224,1,223,224,223,1102,24,17,224,1001,224,-408,224,4,224,1002,223,8,223,101,2,224,224,1,223,224,223,1101,9,66,224,1001,224,-75,224,4,224,1002,223,8,223,1001,224,6,224,1,223,224,223,1102,18,33,225,1101,57,64,225,1102,45,11,225,1101,45,9,225,1101,11,34,225,1102,59,22,225,101,89,191,224,1001,224,-100,224,4,224,1002,223,8,223,1001,224,1,224,1,223,224,223,4,223,99,0,0,0,677,0,0,0,0,0,0,0,0,0,0,0,1105,0,99999,1105,227,247,1105,1,99999,1005,227,99999,1005,0,256,1105,1,99999,1106,227,99999,1106,0,265,1105,1,99999,1006,0,99999,1006,227,274,1105,1,99999,1105,1,280,1105,1,99999,1,225,225,225,1101,294,0,0,105,1,0,1105,1,99999,1106,0,300,1105,1,99999,1,225,225,225,1101,314,0,0,106,0,0,1105,1,99999,8,226,677,224,1002,223,2,223,1006,224,329,1001,223,1,223,108,226,226,224,1002,223,2,223,1006,224,344,1001,223,1,223,7,677,226,224,102,2,223,223,1005,224,359,101,1,223,223,7,226,677,224,102,2,223,223,1006,224,374,101,1,223,223,1008,677,226,224,1002,223,2,223,1006,224,389,101,1,223,223,8,677,677,224,1002,223,2,223,1005,224,404,101,1,223,223,8,677,226,224,102,2,223,223,1005,224,419,1001,223,1,223,1107,677,226,224,102,2,223,223,1005,224,434,1001,223,1,223,1107,226,677,224,1002,223,2,223,1006,224,449,1001,223,1,223,107,677,226,224,1002,223,2,223,1005,224,464,1001,223,1,223,1008,677,677,224,1002,223,2,223,1006,224,479,1001,223,1,223,1108,677,226,224,1002,223,2,223,1006,224,494,1001,223,1,223,1108,677,677,224,1002,223,2,223,1006,224,509,1001,223,1,223,107,677,677,224,1002,223,2,223,1005,224,524,101,1,223,223,1007,677,226,224,102,2,223,223,1005,224,539,1001,223,1,223,1107,226,226,224,1002,223,2,223,1006,224,554,1001,223,1,223,1008,226,226,224,1002,223,2,223,1006,224,569,101,1,223,223,1108,226,677,224,1002,223,2,223,1006,224,584,101,1,223,223,108,677,677,224,1002,223,2,223,1006,224,599,1001,223,1,223,1007,677,677,224,102,2,223,223,1006,224,614,101,1,223,223,107,226,226,224,102,2,223,223,1006,224,629,101,1,223,223,1007,226,226,224,102,2,223,223,1005,224,644,1001,223,1,223,108,226,677,224,102,2,223,223,1005,224,659,1001,223,1,223,7,677,677,224,102,2,223,223,1006,224,674,1001,223,1,223,4,223,99,226';

$ic = new IntCode();
$ic->setInput(5);
$ic->setCode($code);
$output = $ic->process();

if ($output != 773660) {
    var_dump($output);
    die('error in intcode');
}

var_dump('test is ok, '.$output);

$code = '3,8,1001,8,10,8,105,1,0,0,21,46,59,84,93,110,191,272,353,434,99999,3,9,101,2,9,9,102,3,9,9,1001,9,5,9,102,4,9,9,1001,9,4,9,4,9,99,3,9,101,3,9,9,102,5,9,9,4,9,99,3,9,1001,9,4,9,1002,9,2,9,101,2,9,9,102,2,9,9,1001,9,3,9,4,9,99,3,9,1002,9,2,9,4,9,99,3,9,102,2,9,9,1001,9,5,9,1002,9,3,9,4,9,99,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1001,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,99,3,9,101,1,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,99,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,101,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,99';

$phases = [];

$range = range(0,4);
foreach ($range as $r1) {

    $l2 = $range;
    unset($l2[$r1]);

    foreach ($l2 as $r2) {

        $l3 = $l2;
        unset($l3[$r2]);

        foreach ($l3 as $r3) {

            $l4 = $l3;
            unset($l4[$r3]);

            foreach ($l4 as $r4) {

                $l5 = $l4;
                unset($l5[$r4]);

                foreach ($l5 as $r5) {
                    $o = sprintf('%s,%s,%s,%s,%s',
                        $r1,
                        $r2,
                        $r3,
                        $r4,
                        $r5
                    );

                    $phases[$o] = $o;
                }
            }
        }
    }
}

$biggest = 0;
$key = '';

foreach ($phases as $key=>$p) {
    $ic = new IntCode([
        'debug' => false,
    ]);

    $input = 0;

    $ic->setPhases($p);
    $ic->setInput($input);
    $ic->setCode($code);

    $output = $ic->process();

    if ($output > $biggest) {
        $biggest = $output;
        $key = $key;
    }

    var_dump('output is '.$output);
}
var_dump($key, $biggest);

class IntCode
{
    private $output = [];
    private $userInput;
    private $input;
    private $inputCounter = 0;
    private $pointer;
    private $debug = false;
    private $fixedInput = false;
    private $code;
    private $phases = null;

    public function __construct($option=null)
    {
        $this->reset();

        if (isset($option['debug'])) {
            $this->debug = $option['debug'];
        }
    }

    public function setPhases($phases)
    {
        $this->phases = preg_split("/,/", $phases);
    }

    public function setCode($code)
    {
        $this->code = preg_split("/,/", $code);

        if ($this->debug) {
            $this->print();
        }
    }

    public function setInput($input)
    {
        if ($this->userInput) {
            throw new \Exception('can only set input once');
        }

        $this->userInput = $input;
        $this->input = [$input];
    }

    public function modifyInput($input)
    {
        $this->input = $input;
    }

    public function getNextInput()
    {
        if (!isset($this->input[$this->inputCounter])) {
            throw new \Exception('out of scope, input');
        }

        $input = $this->input[$this->inputCounter];

        if (!$this->fixedInput) {
            $this->inputCounter++;
        }

        return $input;
    }

    public function getOutput()
    {
        return implode(',', $this->output);
    }

    public function print()
    {
        foreach ($this->code as $i=>$o) {
            echo $i.': '.$o.PHP_EOL;
        }

        echo PHP_EOL;
    }

    public function process()
    {
        if ($this->phases) {
            $output = $this->userInput;

            foreach ($this->phases as $loop=>$next) {
                $this->reset();
                $this->modifyInput([$next, $output]);

                $output = $this->run();
            }

        } else {
            $this->run();
        }

        return $this->getOutput();
    }

    public function run()
    {
        while (true) {
            $this->pointer;
            $instruction = $this->code[$this->pointer];

            $this->debug('pointer: '.$this->pointer.', instruction: '.$instruction);

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

            $this->debug('instrustion: '.$instruction.', p1: '.$p1.', p2: '.$p2.', p3: '.$p3.', p4: '.$p4);

            if ($instruction == 99) break;

            $newInstruction = null;
            $res = null;

            switch ($instruction) {
            case 1:
                $next = 4;
                $outkey = $this->getOutKey($p3, 3);

                $val1 = $this->getVal($p1, 1);
                $val2 = $this->getVal($p2, 2);

                $res = $val1+$val2;

                $this->debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey);

                break;

            case 2:
                $next = 4;
                $outkey = $this->getOutKey($p3, 3);

                $val1 = $this->getVal($p1, 1);
                $val2 = $this->getVal($p2, 2);

                $res = $val1*$val2;

                $this->debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey);

                break;

            case 3:
                $next = 2;
                $outkey = $this->getOutKey($p1, 1);

                $res = $this->getNextInput();

                $this->debug('i '.$instruction.', o '.$outkey);

                break;

            case 4:
                $next = 2;
                $outkey = $this->getOutKey($p1, 1);

                $this->output[] = $this->code[$outkey];

                $this->debug('i '.$instruction.', o '.$outkey);

                break;

            case 5:
                $next = 3;

                $val1 = $this->getVal($p1, 1);
                $val2 = $this->getVal($p2, 2);

                $newInstruction = null;
                if ($val1 != 0) {
                    $next = 0;
                    $newInstruction = $val2;
                }

                $this->debug('i '.$newInstruction.', v1 '.$val1.', v2 '.$val2);

                break;

            case 6:
                $next = 3;

                $val1 = $this->getVal($p1, 1);
                $val2 = $this->getVal($p2, 2);

                $newInstruction = null;
                if ($val1 == 0) {
                    $next = 0;
                    $newInstruction = $val2;
                }

                $this->debug('i '.$newInstruction.', v1 '.$val1.', v2 '.$val2);

                break;

            case 7:
                $next = 4;
                $outkey = $this->getOutKey($p3, 3);

                $val1 = $this->getVal($p1, 1);
                $val2 = $this->getVal($p2, 2);

                $res = ($val1 < $val2) ? 1 : 0;

                $this->debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey);

                break;

            case 8:
                $next = 4;
                $outkey = $this->getOutKey($p3, 3);

                $val1 = $this->getVal($p1, 1);
                $val2 = $this->getVal($p2, 2);

                $res = ($val1 == $val2) ? 1 : 0;

                $this->debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey);

                break;

            default:
                throw new \Exception('not supported code '.$instruction);
            }

            if ($res !== null) {
                $this->debug('switch data on pointer '.$outkey.', from '.$this->code[$outkey].' to '.$res);
                $this->code[$outkey] = $res;
            }

            if (!$newInstruction) {
                $this->pointer += $next;
            } else {
                $this->pointer = $newInstruction;
            }

            if (!isset($this->code[$this->pointer])) throw new \Exception('out of scope');
        }

        return $this->getOutput();
    }

    private function getVal($p, $offset)
    {
        if ($p == 0) {
            $in = $this->code[$this->pointer+$offset];
            $val = $this->code[$in];

        } else {
            $val = $this->code[$this->pointer+$offset];
        }

        return $val;
    }

    private function getOutKey($p, $offset)
    {
        if ($p == 0) {
            $outkey = $this->code[$this->pointer+$offset];
        } else {
            $outkey = $this->pointer+$offset;
        }

        return $outkey;
    }

    private function reset()
    {
        $this->pointer = 0;
        $this->inputCounter = 0;
        $this->output = [];
    }

    private function debug($msg)
    {
        if ($this->debug) {
            var_dump($msg);
        }
    }
}
