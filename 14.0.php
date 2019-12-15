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

$reaction->bake('FUEL');

class Reaction
{
    private $list = [];
    private $oreUsage = 0;
    private $requirements = [];
    private $inventory = [];

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

            dump('before inventory requirement '.$requireAmount.' '.$requireType);

            if (isset($this->inventory[$requireType])) {
                if ($this->inventory[$requireType] >= $requireAmount) {
                    dump('< use inventory, take '.$requireAmount.' '.$requireType);

                    $this->inventory[$requireType] -= $requireAmount;
                    $requireAmount = 0;

                } else {
                    dump('< use inventory, take '.$this->inventory[$requireType].' '.$requireType);

                    $requireAmount -= $this->inventory[$requireType];
                    $this->inventory[$requireType] = 0;
                }
            }

            $reactions = ceil($requireAmount/$requireProduce);
            $requireTotalProduction = $requireProduce*$reactions;
            $rest = $requireTotalProduction-$requireAmount;

            dump('requirement '.$requireAmount.' '.$requireType.', 1 reaction produces '.$requireProduce.' '.$requireType.', bake '.$reactions.', rest '.$rest.' '.$requireType);

            if ($rest > 0) {
                if (!isset($this->inventory[$requireType])) {
                    $this->inventory[$requireType] = 0;
                }

                dump('> add to inventory '.$rest.' '.$requireType);
                $this->inventory[$requireType] += $rest;
            }

            foreach ($reqType['input'] as $input) {
                $produce = $input['amount']*$reactions;

                dump('ingredient '.$input['amount'].' '.$input['type'].' per reaction, uses '.$produce);

                if ($input['type'] == 'ORE') {
                    dump('add '.$produce.' '.$input['type'].' to ore usage');

                    $this->oreUsage += $produce;

                } else {
                    if (!isset($this->requirements[$input['type']])) {
                        $this->requirements[$input['type']] = [
                            'type' => $input['type'],
                            'amount' => 0,
                        ];
                    }

                    dump('add '.$produce.' '.$input['type'].' to requirements');

                    $this->requirements[$input['type']]['amount'] += $produce;
                }
            }

            //dump('requirement list', $this->requirements);
            dump('ore usage: '.$this->oreUsage);

            echo PHP_EOL;
            if (count($this->requirements) == 0) break;
        }

        dump($this->inventory);
        dump($this->oreUsage);
    }

    public function getOutput($type)
    {
        dump('looking for '.$type);
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
}
