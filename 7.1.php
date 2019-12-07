<?php
$ic = new IntCode([
    'info' => false,
]);
$ic->test();

$code = '3,8,1001,8,10,8,105,1,0,0,21,46,59,84,93,110,191,272,353,434,99999,3,9,101,2,9,9,102,3,9,9,1001,9,5,9,102,4,9,9,1001,9,4,9,4,9,99,3,9,101,3,9,9,102,5,9,9,4,9,99,3,9,1001,9,4,9,1002,9,2,9,101,2,9,9,102,2,9,9,1001,9,3,9,4,9,99,3,9,1002,9,2,9,4,9,99,3,9,102,2,9,9,1001,9,5,9,1002,9,3,9,4,9,99,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1001,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,99,3,9,101,1,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,99,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,101,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,99';

$phases = [];
$range = [];

for ($i = 5; $i <= 9; $i++) {
    $range[$i] = $i;
}

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
$k = '';

foreach ($phases as $key=>$p) {
    $ic = new IntCode([
        'debug' => false,
        'info' => false,
    ]);

    $input = 0;

    $ic->setPhases($p);
    $ic->setInput($input);
    $ic->setFeedback(true);
    $ic->setCode($code);

    $output = $ic->process();

    if ($output > $biggest) {
        $biggest = $output;
        $k = $key;
    }

    var_dump('output is '.$output);
}
var_dump($k, $biggest);

class IntCode
{
    // internal
    private $phases;

    // user modify
    private $info = false;
    private $debug = false;
    private $feedback = false;
    private $userCode;
    private $runPhases;
    private $userInput;

    public function __construct($option=null)
    {
        $this->reset();

        if (isset($option['debug'])) {
            $this->debug = $option['debug'];
        }

        if (isset($option['info'])) {
            $this->info = $option['info'];
        }
    }

    private function reset()
    {
        $this->phases = [];
    }

    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
    }

    public function setPhases($phases)
    {
        $this->runPhases = preg_split("/,/", $phases);
    }

    public function setCode($code)
    {
        $this->userCode = preg_split("/,/", $code);

        if ($this->debug) {
            $this->print();
        }
    }

    public function setInput($input)
    {
        $this->userInput = $input;
    }

    private function appendInput($input, $phase)
    {
        $next = $phase+1;

        if (!isset($this->phases[$next])) {
            $next = 0;
        }

        $this->debug('add output from phase '.$phase.' ['.$input.'] to input phase '.$next);

        $this->phases[$next]->input[] = $input;
    }

    private function hasInput($phase)
    {
        return isset($this->phases[$phase]->input[$this->phases[$phase]->inputCounter]);
    }

    private function getNextInput($phase)
    {
        if (!isset($this->phases[$phase]->input[$this->phases[$phase]->inputCounter])) {
            throw new \Exception('out of scope phase '.$phase.', input');
        }

        $input = $this->phases[$phase]->input[$this->phases[$phase]->inputCounter];

        $this->phases[$phase]->inputCounter++;

        return $input;
    }

    private function getPhase()
    {
        $p = new \StdClass();
        $p->halt = false;
        $p->input = [];
        $p->output = null;
        $p->pointer = 0;
        $p->inputCounter = 0;
        $p->code = $this->userCode;

        return $p;
    }

    public function process()
    {
        if (!$this->runPhases) {
            $phases = [0];
        } else {
            $phases = $this->runPhases;
        }

        foreach ($phases as $loop=>$next) {
            $this->phases[$loop] = $this->getPhase();

            if ($this->runPhases) {
                $this->phases[$loop]->input[] = $next;

                if ($loop == 0) {
                    $this->phases[$loop]->input[] = $this->userInput;
                }

            } else {
                $this->phases[$loop]->input[] = $this->userInput;
            }
        }

        while (true) {
            foreach ($phases as $loop=>$next) {
                if (!$this->phases[$loop]->halt) {

                    $this->info('switch phase to '.$loop.', input '. implode(',', $this->phases[$loop]->input));
                    $output = $this->run($loop);
                }
            }

            if ($this->phases[$loop]->halt) {
                $this->info('system halted, instruction 99');
                break;
            }
        }

        return $this->getOutput($loop);
    }

    private function getCode($phase)
    {
        return $this->phases[$phase]->code;
    }

    private function run($phase)
    {
        while (true) {
            $code = $this->getCode($phase);

            $instruction = $code[$this->phases[$phase]->pointer];

            $this->debug('pointer: '.$this->phases[$phase]->pointer.', instruction: '.$instruction);

            $p1 = 0;
            $p2 = 0;
            $p3 = 0;
            $p4 = 0;

            $o = sprintf('%05d', $instruction);

            $instruction = (int)substr($o, -2);
            $p1 = (int)substr($o, -3, 1);
            $p2 = (int)substr($o, -4, 1);
            $p3 = (int)substr($o, -5, 1);
            $p4 = (int)substr($o, -6, 1);

            $this->debug('instrustion: '.$instruction.', p1: '.$p1.', p2: '.$p2.', p3: '.$p3.', p4: '.$p4);

            if ($instruction == 99) {
                $this->phases[$phase]->halt = true;
                break;
            }

            $newInstruction = null;
            $res = null;
            $skip = false;

            switch ($instruction) {
            case 1:
                $next = 4;
                $outkey = $this->getOutKey($p3, 3, $phase);

                $val1 = $this->getVal($p1, 1, $phase);
                $val2 = $this->getVal($p2, 2, $phase);

                $res = $val1+$val2;

                $this->debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey);

                break;

            case 2:
                $next = 4;
                $outkey = $this->getOutKey($p3, 3, $phase);

                $val1 = $this->getVal($p1, 1, $phase);
                $val2 = $this->getVal($p2, 2, $phase);

                $res = $val1*$val2;

                $this->debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey);

                break;

            case 3:
                $next = 2;
                $outkey = $this->getOutKey($p1, 1, $phase);

                $res = $this->getNextInput($phase);

                $this->debug('i '.$instruction.', o '.$outkey);

                break;

            case 4:
                $next = 2;
                $outkey = $this->getOutKey($p1, 1, $phase);

                $o = $code[$outkey];
                $this->phases[$phase]->output = $o;

                $this->debug('i '.$instruction.', o '.$outkey);

                $this->debug('phase '.$phase.', output '.$o);
                $this->appendInput($o, $phase);

                if ($this->feedback) {
                    $skip = true;
                }

                break;

            case 5:
                $next = 3;

                $val1 = $this->getVal($p1, 1, $phase);
                $val2 = $this->getVal($p2, 2, $phase);

                $newInstruction = null;
                if ($val1 != 0) {
                    $next = 0;
                    $newInstruction = $val2;
                }

                $this->debug('i '.$newInstruction.', v1 '.$val1.', v2 '.$val2);

                break;

            case 6:
                $next = 3;

                $val1 = $this->getVal($p1, 1, $phase);
                $val2 = $this->getVal($p2, 2, $phase);

                $newInstruction = null;
                if ($val1 == 0) {
                    $next = 0;
                    $newInstruction = $val2;
                }

                $this->debug('i '.$newInstruction.', v1 '.$val1.', v2 '.$val2);

                break;

            case 7:
                $next = 4;
                $outkey = $this->getOutKey($p3, 3, $phase);

                $val1 = $this->getVal($p1, 1, $phase);
                $val2 = $this->getVal($p2, 2, $phase);

                $res = ($val1 < $val2) ? 1 : 0;

                $this->debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey);

                break;

            case 8:
                $next = 4;
                $outkey = $this->getOutKey($p3, 3, $phase);

                $val1 = $this->getVal($p1, 1, $phase);
                $val2 = $this->getVal($p2, 2, $phase);

                $res = ($val1 == $val2) ? 1 : 0;

                $this->debug('i '.$instruction.', v1 '.$val1.', v2 '.$val2.', o '.$outkey);

                break;

            default:
                throw new \Exception('not supported code '.$instruction);
            }

            if ($skip) {
                $this->phases[$phase]->pointer += $next;
                break;

            } else {
                if ($res !== null) {
                    $this->debug('switch data on pointer '.$outkey.', from '.$code[$outkey].' to '.$res);
                    $this->phases[$phase]->code[$outkey] = $res;
                }

                if (!$newInstruction) {
                    $this->phases[$phase]->pointer += $next;
                } else {
                    $this->phases[$phase]->pointer = $newInstruction;
                }
            }

            if (!isset($this->phases[$phase]->code[$this->phases[$phase]->pointer])) throw new \Exception('out of scope');
        }

        return $this->getOutput($phase);
    }

    public function getOutput($phase)
    {
        return $this->phases[$phase]->output;
    }

    public function print()
    {
        foreach ($this->userCode as $i=>$o) {
            echo $i.': '.$o.PHP_EOL;
        }

        echo PHP_EOL;
    }

    private function getVal($p, $offset, $phase)
    {
        $code = $this->getCode($phase);

        if ($p == 0) {
            $in = $code[$this->phases[$phase]->pointer+$offset];
            $val = $code[$in];

        } else {
            $val = $code[$this->phases[$phase]->pointer+$offset];
        }

        if (!is_numeric($val)) {
            var_dump($this->phases[$phase]->input);
            var_dump($val);die('value is not numeric');
        }

        return $val;
    }

    private function getOutKey($p, $offset, $phase)
    {
        $code = $this->getCode($phase);

        if ($p == 0) {
            $outkey = $code[$this->phases[$phase]->pointer+$offset];
        } else {
            $outkey = $this->phases[$phase]->pointer+$offset;
        }

        return $outkey;
    }

    private function info($msg)
    {
        if ($this->info) {
            var_dump($msg);
        }
    }

    private function debug($msg)
    {
        if ($this->debug) {
            var_dump($msg);
        }
    }

    public function test()
    {
        $code = '3,225,1,225,6,6,1100,1,238,225,104,0,1002,36,25,224,1001,224,-2100,224,4,224,1002,223,8,223,101,1,224,224,1,223,224,223,1102,31,84,225,1102,29,77,225,1,176,188,224,101,-42,224,224,4,224,102,8,223,223,101,3,224,224,1,223,224,223,2,196,183,224,1001,224,-990,224,4,224,1002,223,8,223,101,7,224,224,1,224,223,223,102,14,40,224,101,-1078,224,224,4,224,1002,223,8,223,1001,224,2,224,1,224,223,223,1001,180,64,224,101,-128,224,224,4,224,102,8,223,223,101,3,224,224,1,223,224,223,1102,24,17,224,1001,224,-408,224,4,224,1002,223,8,223,101,2,224,224,1,223,224,223,1101,9,66,224,1001,224,-75,224,4,224,1002,223,8,223,1001,224,6,224,1,223,224,223,1102,18,33,225,1101,57,64,225,1102,45,11,225,1101,45,9,225,1101,11,34,225,1102,59,22,225,101,89,191,224,1001,224,-100,224,4,224,1002,223,8,223,1001,224,1,224,1,223,224,223,4,223,99,0,0,0,677,0,0,0,0,0,0,0,0,0,0,0,1105,0,99999,1105,227,247,1105,1,99999,1005,227,99999,1005,0,256,1105,1,99999,1106,227,99999,1106,0,265,1105,1,99999,1006,0,99999,1006,227,274,1105,1,99999,1105,1,280,1105,1,99999,1,225,225,225,1101,294,0,0,105,1,0,1105,1,99999,1106,0,300,1105,1,99999,1,225,225,225,1101,314,0,0,106,0,0,1105,1,99999,8,226,677,224,1002,223,2,223,1006,224,329,1001,223,1,223,108,226,226,224,1002,223,2,223,1006,224,344,1001,223,1,223,7,677,226,224,102,2,223,223,1005,224,359,101,1,223,223,7,226,677,224,102,2,223,223,1006,224,374,101,1,223,223,1008,677,226,224,1002,223,2,223,1006,224,389,101,1,223,223,8,677,677,224,1002,223,2,223,1005,224,404,101,1,223,223,8,677,226,224,102,2,223,223,1005,224,419,1001,223,1,223,1107,677,226,224,102,2,223,223,1005,224,434,1001,223,1,223,1107,226,677,224,1002,223,2,223,1006,224,449,1001,223,1,223,107,677,226,224,1002,223,2,223,1005,224,464,1001,223,1,223,1008,677,677,224,1002,223,2,223,1006,224,479,1001,223,1,223,1108,677,226,224,1002,223,2,223,1006,224,494,1001,223,1,223,1108,677,677,224,1002,223,2,223,1006,224,509,1001,223,1,223,107,677,677,224,1002,223,2,223,1005,224,524,101,1,223,223,1007,677,226,224,102,2,223,223,1005,224,539,1001,223,1,223,1107,226,226,224,1002,223,2,223,1006,224,554,1001,223,1,223,1008,226,226,224,1002,223,2,223,1006,224,569,101,1,223,223,1108,226,677,224,1002,223,2,223,1006,224,584,101,1,223,223,108,677,677,224,1002,223,2,223,1006,224,599,1001,223,1,223,1007,677,677,224,102,2,223,223,1006,224,614,101,1,223,223,107,226,226,224,102,2,223,223,1006,224,629,101,1,223,223,1007,226,226,224,102,2,223,223,1005,224,644,1001,223,1,223,108,226,677,224,102,2,223,223,1005,224,659,1001,223,1,223,7,677,677,224,102,2,223,223,1006,224,674,1001,223,1,223,4,223,99,226';

        $this->setInput(5);
        $this->setCode($code);
        $output = $this->process();

        if ($output != 773660) {
            var_dump($output);
            die('error in test 1'.PHP_EOL);
        }

        var_dump('test 1/3 is ok, '.$output);

        $this->reset();

        $code = '3,8,1001,8,10,8,105,1,0,0,21,46,59,84,93,110,191,272,353,434,99999,3,9,101,2,9,9,102,3,9,9,1001,9,5,9,102,4,9,9,1001,9,4,9,4,9,99,3,9,101,3,9,9,102,5,9,9,4,9,99,3,9,1001,9,4,9,1002,9,2,9,101,2,9,9,102,2,9,9,1001,9,3,9,4,9,99,3,9,1002,9,2,9,4,9,99,3,9,102,2,9,9,1001,9,5,9,1002,9,3,9,4,9,99,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1001,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,99,3,9,101,1,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,99,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,101,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,99';

        $phases = "2,0,1,4,3";
        $this->setPhases($phases);
        $this->setInput(0);
        $this->setCode($code);
        $output = $this->process();

        if ($output != 19650) {
            var_dump($output);
            die('error in test 2'.PHP_EOL);
        }

        var_dump('test 2/3 is ok, '.$output);

        $this->reset();

        $code = '3,8,1001,8,10,8,105,1,0,0,21,46,59,84,93,110,191,272,353,434,99999,3,9,101,2,9,9,102,3,9,9,1001,9,5,9,102,4,9,9,1001,9,4,9,4,9,99,3,9,101,3,9,9,102,5,9,9,4,9,99,3,9,1001,9,4,9,1002,9,2,9,101,2,9,9,102,2,9,9,1001,9,3,9,4,9,99,3,9,1002,9,2,9,4,9,99,3,9,102,2,9,9,1001,9,5,9,1002,9,3,9,4,9,99,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1001,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,99,3,9,101,1,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,99,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,101,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,99';

        $phases = "7,8,6,9,5";
        $this->setPhases($phases);
        $this->setInput(0);
        $this->setFeedback(true);
        $this->setCode($code);
        $output = $this->process();

        if ($output != 35961106) {
            var_dump($output);
            die('error in test 3'.PHP_EOL);
        }

        var_dump('test 3/3 is ok, '.$output);
        echo PHP_EOL;

        usleep(500000);
    }
}
