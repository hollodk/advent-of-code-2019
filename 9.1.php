<?php
$ic = new IntCode([
    'logLevel' => 0,
]);
$ic->test();

$code = trim(file_get_contents(__DIR__.'/input-9.0.txt'));

$ic = new IntCode([
    'debug' => false,
    'info' => false,
]);

$ic->setInput(2);
$ic->setCode($code);

$output = $ic->process();
var_dump($output['output']);
var_dump($output['lastPhase']->currentLoop);

class IntCode
{
    // internal
    private $resets = 0;
    private $logLevel = 0;

    // user modify
    private $phases;
    private $feedback;
    private $userCode;
    private $runPhases;
    private $userInput;
    private $maxLoops;

    public function __construct($option=null)
    {
        $this->reset();

        if (isset($option['logLevel'])) {
            $this->logLevel = $option['logLevel'];
        }
    }

    private function reset()
    {
        $this->resets++;

        $this->phases = [];
        $this->feedback = false;
        $this->userCode = null;
        $this->runPhases = null;
        $this->userInput = null;
        $this->maxLoops = 1000000;
        $this->memoryAppend = 105;
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
    }

    public function setInput($input)
    {
        $this->userInput = $input;
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

    private function initPhase()
    {
        $p = new \StdClass();
        $p->out = [];
        $p->code = $this->userCode;
        $p->halt = false;
        $p->input = [];
        $p->pointer = 0;
        $p->relative = 0;
        $p->inputCounter = 0;
        $p->maxLoops = $this->maxLoops;
        $p->currentLoop = 0;

        return $p;
    }

    public function process($options=null)
    {
        if (!$this->runPhases) {
            $phases = [0];
        } else {
            $phases = $this->runPhases;
        }

        foreach ($phases as $loop=>$next) {
            $this->phases[$loop] = $this->initPhase();

            if ($this->runPhases) {
                $this->phases[$loop]->input[] = $next;

                if ($loop == 0) {
                    $this->phases[$loop]->input[] = $this->userInput;
                }

            } else {
                $this->phases[$loop]->input[] = $this->userInput;
            }

            if ($options && isset($options['phase']['switch'])) {
                foreach ($options['phase']['switch'] as $key=>$value) {
                    $this->phases[$loop]->code[$key] = $value;
                }
            }

            $this->printCode($loop, 2);

            for ($i = 0; $i < $this->memoryAppend; $i++) {
                $this->phases[$loop]->code[] = 0;
            }
        }

        while (true) {
            foreach ($phases as $loop=>$next) {
                if (!$this->phases[$loop]->halt) {

                    if (isset($oo)) {
                        $this->debug('add output ['.implode(', ', $oo).'] from last phase to input phase '.$loop);

                        foreach ($oo as $o) {
                            $this->phases[$loop]->input[] = $o;
                        }
                    }

                    $this->info('switch phase to '.$loop.', input '. implode(',', $this->phases[$loop]->input));

                    $this->run($loop);

                    $oo = $this->phases[$loop]->out;
                    $this->phases[$loop]->out = [];
                }
            }

            if ($this->phases[$loop]->halt) {
                $this->info('system halted phase '.$loop.', instruction 99');
                break;
            }

            if ($this->phases[$loop]->currentLoop > $this->phases[$loop]->maxLoops) {
                $this->info('system breaks phase '.$loop.', too many loops');
                break;
            }
        }

        return [
            'output' => $oo,
            'lastPhase' => $this->phases[$loop],
        ];
    }

    public function getCode($phase)
    {
        return $this->phases[$phase]->code;
    }

    private function run($phase)
    {
        while (true) {
            $this->printCode($phase, 3);

            $this->phases[$phase]->currentLoop++;

            if ($this->phases[$phase]->currentLoop > $this->phases[$phase]->maxLoops) {
                break;
            }

            $code = $this->getCode($phase);
            $pointer = $this->getPointer($phase);

            $instruction = $code[$pointer];

            $this->debug('pointer: '.$pointer.', relative: '.$this->phases[$phase]->relative.', instruction: '.$instruction);

            $o = sprintf('%05d', $instruction);

            $data = [];
            $modes = [];
            $params = [];

            $instruction = (int)substr($o, -2);

            $modes[] = (int)substr($o, -3, 1);
            $modes[] = (int)substr($o, -4, 1);
            $modes[] = (int)substr($o, -5, 1);
            $modes[] = (int)substr($o, -6, 1);

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

                $params[] = $code[$pointer+1];
                $params[] = $code[$pointer+2];
                $params[] = $pointer+3;

                $val1 = $this->getValNg($modes[0], $params[0], $phase);
                $val2 = $this->getValNg($modes[1], $params[1], $phase);
                $outkey = $this->getOutKeyNg($modes[2], $params[2], $phase);

                $data[] = $val1;
                $data[] = $val2;
                $data[] = $outkey;

                $res = $val1+$val2;

                break;

            case 2:
                $next = 4;

                $params[] = $code[$pointer+1];
                $params[] = $code[$pointer+2];
                $params[] = $pointer+3;

                $val1 = $this->getValNg($modes[0], $params[0], $phase);
                $val2 = $this->getValNg($modes[1], $params[1], $phase);
                $outkey = $this->getOutKeyNg($modes[2], $params[2], $phase);

                $data[] = $val1;
                $data[] = $val2;
                $data[] = $outkey;

                $res = $val1*$val2;

                break;

            case 3:
                if (!$this->hasInput($phase)) {
                    $next = 0;
                    $skip = true;

                } else {
                    $next = 2;

                    $params[] = $pointer+1;

                    $outkey = $this->getOutKeyNg($modes[0], $params[0], $phase);

                    $data[] = $outkey;

                    $res = $this->getNextInput($phase);
                }

                break;

            case 4:
                $next = 2;
                $params[] = $pointer+1;

                $outkey = $this->getOutKeyNg($modes[0], $params[0], $phase);
                $o = $code[$outkey];

                $data[] = $outkey;

                $this->phases[$phase]->out[] = $o;

                $this->debug('phase '.$phase.', output '.$o);

                break;

            case 5:
                $next = 3;

                $params[] = $code[$pointer+1];
                $params[] = $code[$pointer+2];

                $val1 = $this->getValNg($modes[0], $params[0], $phase);
                $val2 = $this->getValNg($modes[1], $params[1], $phase);

                $data[] = $val1;
                $data[] = $val2;

                $newInstruction = null;
                if ($val1 != 0) {
                    $next = 0;
                    $newInstruction = $val2;
                }

                break;

            case 6:
                $next = 3;

                $params[] = $code[$pointer+1];
                $params[] = $code[$pointer+2];

                $val1 = $this->getValNg($modes[0], $params[0], $phase);
                $val2 = $this->getValNg($modes[1], $params[1], $phase);

                /*
                $val1 = $params[0];
                $val2 = $params[1];
                 */

                $data[] = $val1;
                $data[] = $val2;

                $newInstruction = null;
                if ($val1 == 0) {
                    $next = 0;
                    $newInstruction = $val2;
                }

                break;

            case 7:
                $next = 4;

                $params[] = $code[$pointer+1];
                $params[] = $code[$pointer+2];
                $params[] = $pointer+3;

                $val1 = $this->getValNg($modes[0], $params[0], $phase);
                $val2 = $this->getValNg($modes[1], $params[1], $phase);

                $outkey = $this->getOutKeyNg($modes[2], $params[2], $phase);

                $data[] = $val1;
                $data[] = $val2;
                $data[] = $outkey;

                $res = ($val1 < $val2) ? 1 : 0;
                break;

            case 8:
                $next = 4;

                $params[] = $code[$pointer+1];
                $params[] = $code[$pointer+2];
                $params[] = $pointer+3;

                $val1 = $this->getValNg($modes[0], $params[0], $phase);
                $val2 = $this->getValNg($modes[1], $params[1], $phase);

                $outkey = $this->getOutKeyNg($modes[2], $params[2], $phase);

                $data[] = $val1;
                $data[] = $val2;
                $data[] = $outkey;

                $res = ($val1 == $val2) ? 1 : 0;

                break;

            case 9:
                $next = 2;

                $params[] = $code[$pointer+1];

                $val1 = $this->getValNg($modes[0], $params[0], $phase);

                $data[] = $val1;

                $this->phases[$phase]->relative += $val1;

                $this->debug('relative value is '.$this->phases[$phase]->relative);

                break;

            default:
                throw new \Exception('not supported code '.$instruction);
            }

            $this->debugInstruction($params, $data, $modes);

            if ($skip) {
                break;

            } else {
                if ($res !== null) {
                    $this->debug('switch data on pointer '.$outkey.', from '.$code[$outkey].' to '.$res);
                    $this->phases[$phase]->code[$outkey] = $res;
                }

                if ($newInstruction === null) {
                    $this->phases[$phase]->pointer += $next;
                } else {
                    $this->phases[$phase]->pointer = $newInstruction;
                }
            }

            if (!isset($this->phases[$phase]->code[$this->phases[$phase]->pointer])) throw new \Exception('out of scope');
        }
    }

    public function debugInstruction($params, $data, $modes)
    {
        $this->debug('modes '.implode(',', $modes));
        $this->debug('params '.implode(',', $params));
        $this->debug('data '.implode(',', $data));
    }

    public function printCode($phase, $logLevel)
    {
        $output = PHP_EOL;
        foreach ($this->phases[$phase]->code as $i=>$o) {
            $output .= $i.': '.$o.PHP_EOL;
        }

        $output .= PHP_EOL;

        switch ($logLevel) {
        case 2:
            $this->debug($output);
            break;

        case 3:
            $this->verbose($output);
            break;
        }
    }

    private function getPointer($phase)
    {
        return $this->phases[$phase]->pointer;
    }

    private function getValNg($mode, $param, $phase)
    {
        $code = $this->getCode($phase);
        $pointer = $this->getPointer($phase);

        switch ($mode) {
        case 0:
            // position mode
            $in = $param;
            $val = $code[$in];
            break;

        case 1:
            // parameter mode
            $val = $param;
            break;

        case 2:
            // relative mode
            $in = $param;
            $in += $this->phases[$phase]->relative;
            $val = $code[$in];
            break;

        default:
            throw new \Exception('parameter mode not supported, '.$p);
        }

        if (!is_numeric($val)) {
            var_dump($this->phases[$phase]->input);
            var_dump($val);die('value is not numeric');
        }

        return $val;
    }

    private function getOutKeyNg($mode, $param, $phase)
    {
        $code = $this->getCode($phase);
        $pointer = $this->getPointer($phase);
        $relative = $this->phases[$phase]->relative;

        switch ($mode) {
        case 0:
            // position mode
            $outkey = $code[$param];
            break;

        case 1:
            // parameter mode
            $outkey = $param;
            break;

        case 2:
            // relative mode
            $r = $code[$param];
            $outkey = $relative+$r;

            break;

        default:
            throw new \Exception('parameter mode not supported, '.$p);
        }

        return $outkey;
    }

    private function getOutKey($mode, $offset, $phase)
    {
        $code = $this->getCode($phase);
        $pointer = $this->getPointer($phase);
        $relative = $this->phases[$phase]->relative;

        switch ($mode) {
        case 0:
            // position mode
            $outkey = $code[$pointer+$offset];
            break;

        case 1:
            // parameter mode
            $outkey = $pointer+$offset;
            break;

        case 2:
            // relative mode
            $r = $code[$pointer+$offset];
            $outkey = $relative+$r;

            break;

        default:
            throw new \Exception('parameter mode not supported, '.$p);
        }

        return $outkey;
    }

    private function info($msg)
    {
        if ($this->logLevel >= 1) {
            var_dump($msg);
        }
    }

    private function debug($msg)
    {
        if ($this->logLevel >= 2) {
            echo $msg.PHP_EOL;
        }
    }

    private function verbose($msg)
    {
        if ($this->logLevel >= 3) {
            echo $msg.PHP_EOL;
        }
    }

    private function test1()
    {
        $this->reset();

        $code = '1,0,0,3,1,1,2,3,1,3,4,3,1,5,0,3,2,6,1,19,1,19,10,23,2,13,23,27,1,5,27,31,2,6,31,35,1,6,35,39,2,39,9,43,1,5,43,47,1,13,47,51,1,10,51,55,2,55,10,59,2,10,59,63,1,9,63,67,2,67,13,71,1,71,6,75,2,6,75,79,1,5,79,83,2,83,9,87,1,6,87,91,2,91,6,95,1,95,6,99,2,99,13,103,1,6,103,107,1,2,107,111,1,111,9,0,99,2,14,0,0';

        $this->setCode($code);

        $output = $this->process([
            'phase' => [
                'phase' => 0,
                'switch' => [
                    '1' => 12,
                    '2' => 2,
                ],
            ],
        ]);

        $code = $this->getCode(0);

        if ($code[0] != 4138687) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$code[0]);
    }

    private function test2()
    {
        $this->reset();
        $code = '3,225,1,225,6,6,1100,1,238,225,104,0,1002,36,25,224,1001,224,-2100,224,4,224,1002,223,8,223,101,1,224,224,1,223,224,223,1102,31,84,225,1102,29,77,225,1,176,188,224,101,-42,224,224,4,224,102,8,223,223,101,3,224,224,1,223,224,223,2,196,183,224,1001,224,-990,224,4,224,1002,223,8,223,101,7,224,224,1,224,223,223,102,14,40,224,101,-1078,224,224,4,224,1002,223,8,223,1001,224,2,224,1,224,223,223,1001,180,64,224,101,-128,224,224,4,224,102,8,223,223,101,3,224,224,1,223,224,223,1102,24,17,224,1001,224,-408,224,4,224,1002,223,8,223,101,2,224,224,1,223,224,223,1101,9,66,224,1001,224,-75,224,4,224,1002,223,8,223,1001,224,6,224,1,223,224,223,1102,18,33,225,1101,57,64,225,1102,45,11,225,1101,45,9,225,1101,11,34,225,1102,59,22,225,101,89,191,224,1001,224,-100,224,4,224,1002,223,8,223,1001,224,1,224,1,223,224,223,4,223,99,0,0,0,677,0,0,0,0,0,0,0,0,0,0,0,1105,0,99999,1105,227,247,1105,1,99999,1005,227,99999,1005,0,256,1105,1,99999,1106,227,99999,1106,0,265,1105,1,99999,1006,0,99999,1006,227,274,1105,1,99999,1105,1,280,1105,1,99999,1,225,225,225,1101,294,0,0,105,1,0,1105,1,99999,1106,0,300,1105,1,99999,1,225,225,225,1101,314,0,0,106,0,0,1105,1,99999,8,226,677,224,1002,223,2,223,1006,224,329,1001,223,1,223,108,226,226,224,1002,223,2,223,1006,224,344,1001,223,1,223,7,677,226,224,102,2,223,223,1005,224,359,101,1,223,223,7,226,677,224,102,2,223,223,1006,224,374,101,1,223,223,1008,677,226,224,1002,223,2,223,1006,224,389,101,1,223,223,8,677,677,224,1002,223,2,223,1005,224,404,101,1,223,223,8,677,226,224,102,2,223,223,1005,224,419,1001,223,1,223,1107,677,226,224,102,2,223,223,1005,224,434,1001,223,1,223,1107,226,677,224,1002,223,2,223,1006,224,449,1001,223,1,223,107,677,226,224,1002,223,2,223,1005,224,464,1001,223,1,223,1008,677,677,224,1002,223,2,223,1006,224,479,1001,223,1,223,1108,677,226,224,1002,223,2,223,1006,224,494,1001,223,1,223,1108,677,677,224,1002,223,2,223,1006,224,509,1001,223,1,223,107,677,677,224,1002,223,2,223,1005,224,524,101,1,223,223,1007,677,226,224,102,2,223,223,1005,224,539,1001,223,1,223,1107,226,226,224,1002,223,2,223,1006,224,554,1001,223,1,223,1008,226,226,224,1002,223,2,223,1006,224,569,101,1,223,223,1108,226,677,224,1002,223,2,223,1006,224,584,101,1,223,223,108,677,677,224,1002,223,2,223,1006,224,599,1001,223,1,223,1007,677,677,224,102,2,223,223,1006,224,614,101,1,223,223,107,226,226,224,102,2,223,223,1006,224,629,101,1,223,223,1007,226,226,224,102,2,223,223,1005,224,644,1001,223,1,223,108,226,677,224,102,2,223,223,1005,224,659,1001,223,1,223,7,677,677,224,102,2,223,223,1006,224,674,1001,223,1,223,4,223,99,226';

        $this->setInput(5);
        $this->setCode($code);
        $output = $this->process();

        if ($output['output'][0] != 773660) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test3()
    {
        $this->reset();
        $code = '3,8,1001,8,10,8,105,1,0,0,21,46,59,84,93,110,191,272,353,434,99999,3,9,101,2,9,9,102,3,9,9,1001,9,5,9,102,4,9,9,1001,9,4,9,4,9,99,3,9,101,3,9,9,102,5,9,9,4,9,99,3,9,1001,9,4,9,1002,9,2,9,101,2,9,9,102,2,9,9,1001,9,3,9,4,9,99,3,9,1002,9,2,9,4,9,99,3,9,102,2,9,9,1001,9,5,9,1002,9,3,9,4,9,99,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1001,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,99,3,9,101,1,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,99,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,101,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,99';

        $phases = "2,0,1,4,3";
        $this->setPhases($phases);
        $this->setInput(0);
        $this->setCode($code);
        $output = $this->process();

        if ($output['output'][0] != 19650) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test4()
    {
        $this->reset();
        $code = '3,8,1001,8,10,8,105,1,0,0,21,46,59,84,93,110,191,272,353,434,99999,3,9,101,2,9,9,102,3,9,9,1001,9,5,9,102,4,9,9,1001,9,4,9,4,9,99,3,9,101,3,9,9,102,5,9,9,4,9,99,3,9,1001,9,4,9,1002,9,2,9,101,2,9,9,102,2,9,9,1001,9,3,9,4,9,99,3,9,1002,9,2,9,4,9,99,3,9,102,2,9,9,1001,9,5,9,1002,9,3,9,4,9,99,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,101,2,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1001,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,1001,9,2,9,4,9,99,3,9,101,1,9,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,99,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,3,9,101,1,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,102,2,9,9,4,9,99,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,1,9,4,9,3,9,101,1,9,9,4,9,3,9,102,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,101,2,9,9,4,9,3,9,1002,9,2,9,4,9,3,9,1001,9,2,9,4,9,99';

        $phases = "7,8,6,9,5";
        $this->setPhases($phases);
        $this->setInput(0);
        $this->setFeedback(true);
        $this->setCode($code);
        $output = $this->process();

        if ($output['output'][0] != 35961106) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test5()
    {
        $this->reset();
        $code = '104,1125899906842624,99';

        $this->setCode($code);
        $output = $this->process();

        if ($output['output'][0] != 1125899906842624) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test6()
    {
        $this->reset();
        $code = '1102,34915192,34915192,7,4,7,99,0';

        $this->setCode($code);
        $output = $this->process();

        if ($output['output'][0] != 1219070632396864) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test7()
    {
        $this->reset();
        $code = '109,1,204,-1,1001,100,1,100,1008,100,16,101,1006,101,0,99';

        $this->maxLoops = 10;
        $this->setCode($code);
        $output = $this->process();

        if ($output['output'][0] != 109) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0].', loop '.$output['lastPhase']->currentLoop);
    }

    private function test8()
    {
        $this->reset();
        $code = '3,9,8,9,10,9,4,9,99,-1,8';

        $this->setCode($code);
        $this->setInput(8);

        $output = $this->process();

        if ($output['output'][0] != 1) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test9()
    {
        $this->reset();
        $code = '3,9,8,9,10,9,4,9,99,-1,8';

        $this->setCode($code);
        $this->setInput(7);

        $output = $this->process();

        if ($output['output'][0] != 0) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test10()
    {
        $this->reset();
        $code = '3,9,7,9,10,9,4,9,99,-1,8';

        $this->setCode($code);
        $this->setInput(7);

        $output = $this->process();

        if ($output['output'][0] != 1) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test11()
    {
        $this->reset();
        $code = '3,9,7,9,10,9,4,9,99,-1,8';

        $this->setCode($code);
        $this->setInput(9);

        $output = $this->process();

        if ($output['output'][0] != 0) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test12()
    {
        $this->reset();
        $code = '3,9,7,9,10,9,4,9,99,-1,8';

        $this->setCode($code);
        $this->setInput(9);

        $output = $this->process();

        if ($output['output'][0] != 0) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test13()
    {
        $this->reset();
        $code = '3,3,1108,-1,8,3,4,3,99';

        $this->setCode($code);
        $this->setInput(8);

        $output = $this->process();

        if ($output['output'][0] != 1) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test14()
    {
        $this->reset();
        $code = '3,3,1108,-1,8,3,4,3,99';

        $this->setCode($code);
        $this->setInput(2);

        $output = $this->process();

        if ($output['output'][0] != 0) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test15()
    {
        $this->reset();
        $code = '3,3,1107,-1,8,3,4,3,99';

        $this->setCode($code);
        $this->setInput(2);

        $output = $this->process();

        if ($output['output'][0] != 1) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test16()
    {
        $this->reset();
        $code = '3,3,1107,-1,8,3,4,3,99';

        $this->setCode($code);
        $this->setInput(8);

        $output = $this->process();

        if ($output['output'][0] != 0) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test17()
    {
        $this->reset();
        $code = '3,12,6,12,15,1,13,14,13,4,13,99,-1,0,1,9';

        $this->setCode($code);
        $this->setInput(0);

        $output = $this->process();

        if ($output['output'][0] != 0) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test18()
    {
        $this->reset();
        $code = '3,12,6,12,15,1,13,14,13,4,13,99,-1,0,1,9';

        $this->setCode($code);
        $this->setInput(2);

        $output = $this->process();

        if ($output['output'][0] != 1) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test19()
    {
        $this->reset();
        $code = '3,3,1105,-1,9,1101,0,0,12,4,12,99,1';

        $this->setCode($code);
        $this->setInput(2);

        $output = $this->process();

        if ($output['output'][0] != 1) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test20()
    {
        $this->reset();
        $code = '3,3,1105,-1,9,1101,0,0,12,4,12,99,1';

        $this->setCode($code);
        $this->setInput(0);

        $output = $this->process();

        if ($output['output'][0] != 0) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test21()
    {
        $this->reset();
        $code = '3,21,1008,21,8,20,1005,20,22,107,8,21,20,1006,20,31,1106,0,36,98,0,0,1002,21,125,20,4,20,1105,1,46,104,999,1105,1,46,1101,1000,1,20,4,20,1105,1,46,98,99';

        $this->setCode($code);
        $this->setInput(1);

        $output = $this->process();

        if ($output['output'][0] != 999) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test22()
    {
        $this->reset();
        $code = '3,21,1008,21,8,20,1005,20,22,107,8,21,20,1006,20,31,1106,0,36,98,0,0,1002,21,125,20,4,20,1105,1,46,104,999,1105,1,46,1101,1000,1,20,4,20,1105,1,46,98,99';

        $this->setCode($code);
        $this->setInput(8);

        $output = $this->process();

        if ($output['output'][0] != 1000) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test23()
    {
        $this->reset();
        $code = '3,21,1008,21,8,20,1005,20,22,107,8,21,20,1006,20,31,1106,0,36,98,0,0,1002,21,125,20,4,20,1105,1,46,104,999,1105,1,46,1101,1000,1,20,4,20,1105,1,46,98,99';

        $this->setCode($code);
        $this->setInput(12);

        $output = $this->process();

        if ($output['output'][0] != 1001) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    private function test24()
    {
        $this->reset();
        $code = trim(file_get_contents(__DIR__.'/input-9.0.txt'));

        $this->setCode($code);
        $this->setInput(1);

        $output = $this->process();

        if ($output['output'][0] != 4006117640) {
            die('error in test '.($this->resets-1).PHP_EOL);
        }

        var_dump('test '.$this->resets.' is ok, '.$output['output'][0]);
    }

    public function test()
    {
        $this->test2();
        $this->test7(); // https://www.reddit.com/r/adventofcode/comments/e87d79/2019_day_9_part_1_help_understanding_the_test/

        $this->test1();
        $this->test2();
        $this->test3();
        $this->test4();
        $this->test5();
        $this->test6();
        $this->test8();
        $this->test9();
        $this->test10();
        $this->test11();
        $this->test12();
        $this->test13();
        $this->test14();
        $this->test15();
        $this->test16();
        $this->test17();
        $this->test18();
        $this->test19();
        $this->test20();
        $this->test21();
        $this->test22();
        $this->test23();
        $this->test24();

        echo PHP_EOL;

        usleep(500000);
    }
}
