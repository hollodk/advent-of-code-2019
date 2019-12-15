<?php

require __DIR__.'/bootstrap.php';

$file = 'input-14-test1.txt';
$file = 'input-14-test2.txt';
$file = 'input-14-test3.txt';
$file = 'input-14.txt';

$input = trim(file_get_contents(__DIR__.'/'.$file));

$out = $tools->linesToArray($input);
$reaction = new Reaction();

$reaction->build($out);
$list = $reaction->getList();

$start = 1000000000000;
$fuel = 0;

while ($start > 0) {
    $reaction->reset();
    $ore = $reaction->bake('FUEL');
    $start -= $ore;

    if (($fuel%50) == 0) {
        dump('left '.number_format($start).', usage '.$ore.', fuel '.$fuel);
    }

    if ($start > 0) {
        $fuel++;
    } else {
        break;
    }
}

dump('total fuel: '.$fuel);

class Reaction
{
    private $list = [];
    private $requirements = [];
    private $inventory = [];
    private $oreUsage = 0;

    public function reset()
    {
        $this->oreUsage = 0;
        $this->requirements = [];
    }

    public function bake($type)
    {
        $this->requirements[] = [
            'type' => $type,
            'amount' => 1,
        ];

        while (true) {
            $req = array_pop($this->requirements);
            $reqType = $this->getOutput($req['type']);

            $requireType = $req['type'];
            $requireAmount = $req['amount'];
            $requireProduce = $reqType['output'][0]['amount'];

            $this->log('before inventory requirement '.$requireAmount.' '.$requireType);

            if (isset($this->inventory[$requireType])) {
                if ($this->inventory[$requireType] >= $requireAmount) {
                    $this->log('< use inventory, take '.$requireAmount.' '.$requireType);

                    $this->inventory[$requireType] -= $requireAmount;
                    $requireAmount = 0;

                } else {
                    $this->log('< use inventory, take '.$this->inventory[$requireType].' '.$requireType);

                    $requireAmount -= $this->inventory[$requireType];
                    $this->inventory[$requireType] = 0;
                }
            }

            $reactions = ceil($requireAmount/$requireProduce);
            $requireTotalProduction = $requireProduce*$reactions;
            $rest = $requireTotalProduction-$requireAmount;

            $this->log('requirement '.$requireAmount.' '.$requireType.', 1 reaction produces '.$requireProduce.' '.$requireType.', bake '.$reactions.', rest '.$rest.' '.$requireType);

            if ($rest > 0) {
                if (!isset($this->inventory[$requireType])) {
                    $this->inventory[$requireType] = 0;
                }

                $this->log('> add to inventory '.$rest.' '.$requireType);
                $this->inventory[$requireType] += $rest;
            }

            foreach ($reqType['input'] as $input) {
                $produce = $input['amount']*$reactions;

                $this->log('ingredient '.$input['amount'].' '.$input['type'].' per reaction, uses '.$produce);

                if ($input['type'] == 'ORE') {
                    $this->log('add '.$produce.' '.$input['type'].' to ore usage');

                    $this->oreUsage += $produce;

                } else {
                    if (!isset($this->requirements[$input['type']])) {
                        $this->requirements[$input['type']] = [
                            'type' => $input['type'],
                            'amount' => 0,
                        ];
                    }

                    $this->log('add '.$produce.' '.$input['type'].' to requirements');

                    $this->requirements[$input['type']]['amount'] += $produce;
                }
            }

            $this->log('ore usage: '.$this->oreUsage);

            if (count($this->requirements) == 0) break;
        }

        return (int)$this->oreUsage;
    }

    public function getOutput($type)
    {
        $this->log('looking for '.$type);
        foreach ($this->list as $l) {
            if ($l['output'][0]['type'] == $type) return $l;
        }

        throw new \Exception('did not find output type');
    }

    public function getList()
    {
        return $this->list;
    }

    public function build($out)
    {
        foreach ($out as $line) {
            list($in, $output) = preg_split("/ => /", $line);

            $input = preg_split("/,/", $in);
            foreach ($input as $key=>$value) {
                $input[$key] = trim($value);
            }

            $res = [
                'input' => [],
                'output' => [],
            ];

            $oo = preg_split("/ /", $output);
            $res['output'][] = [
                'amount' => $oo[0],
                'type' => $oo[1],
            ];

            foreach ($input as $o) {
                $oo = preg_split("/ /", $o);

                $res['input'][] = [
                    'amount' => $oo[0],
                    'type' => $oo[1],
                ];
            }

            $this->list[] = $res;
        }
    }

    private function log($msg)
    {
        return;
        echo $msg.PHP_EOL;
    }
}
