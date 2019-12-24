<?php

// 1922, too low

require __DIR__.'/bootstrap.php';

$file = 'input-24-test1.txt';
$file = 'input-24.txt';

$input = trim(file_get_contents(__DIR__.'/'.$file));

$grid->setGridFromMap($input);

$bug = new Bug();
$bug->addMap($grid, 0);

for ($minutes = 0; $minutes < 200; $minutes++) {
    /**
    echo PHP_EOL;
    echo '####################################'.PHP_EOL;
    echo '# MINUTE '.$minutes.PHP_EOL;
    echo '####################################'.PHP_EOL;
    echo PHP_EOL;
     */

    dump('minute '.$minutes);
    $bug->process();
}

$bug->print();

class Bug
{
    private $maps;

    public function print($type=null)
    {
        $list = $this->maps;

        ksort($list);

        $count = 0;
        foreach ($list as $map) {
            $r = $this->count($map);
            $count += $r;

            if ($r > 0) {
                dump('level '.$map['level']);

                if ($type == 'snap') {
                    $map['snap']->printn();
                } else {
                    $map['grid']->printn();
                }

                echo PHP_EOL;
            }
        }

        dump('bugs '.$count);
    }

    public function count($map)
    {
        $sum = 0;
        $values = $map['grid']->getValues();

        foreach ($values as $value) {
            if ($value == '#') {
                $sum++;
            }
        }

        return $sum;
    }

    public function process()
    {
        foreach ($this->maps as $key=>$map) {
            $this->maps[$key]['snap'] = clone $map['grid'];
        }

        foreach ($this->maps as $level=>$map) {
            /*
            dump('### SHOW MAP ###');
            $this->print('snap');
             */

            $this->processMap($map, $level);
        }
    }

    public function addMap($grid, $level, $isRecurring=false)
    {
        $this->maps[$level] = [];
        $this->maps[$level]['level'] = $level;
        $this->maps[$level]['grid'] = $grid;
        $this->maps[$level]['snap'] = clone $grid;

        if (!$isRecurring) {
            if (!isset($this->maps[$level-1])) {
                $this->checkLevel($level-1, true);
            }

            if (!isset($this->maps[$level+1])) {
                $this->checkLevel($level+1, true);
            }
        }

        ksort($this->maps);
    }

    private function checkLevel($level, $isRecurring=false)
    {
        if (!isset($this->maps[$level])) {
            $g = new Grid();
            $g->init(4, 4, '.');

            $this->addMap($g, $level, $isRecurring);
        }
    }

    public function processMap($map, $level)
    {
        $grid = $map['grid'];

        foreach ($grid->get() as $y=>$v1) {
            foreach ($v1 as $x=>$value) {
                if ($x == 2 && $y == 2) continue;

                logger(' > working level '.$level.' @ '.$x.','.$y);

                $coords = [];
                $coords[] = [$x, $y-1];
                $coords[] = [$x, $y+1];
                $coords[] = [$x-1, $y];
                $coords[] = [$x+1, $y];

                $fields = [];

                foreach ($coords as $coord) {
                    switch (true) {
                    case ($coord[0] == 2 && $coord[1] == 2):
                        $l = $level+1;

                        $this->checkLevel($l);

                        if ($x == 2 && $y == 1) {
                            $fields[] = [
                                'level' => $l,
                                'coord' => [0,0],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [1,0],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [2,0],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [3,0],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [4,0],
                            ];

                        } elseif ($x == 3 && $y == 2) {
                            $fields[] = [
                                'level' => $l,
                                'coord' => [4,0],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [4,1],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [4,2],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [4,3],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [4,4],
                            ];

                        } elseif ($x == 2 && $y == 3) {
                            $fields[] = [
                                'level' => $l,
                                'coord' => [0,4],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [1,4],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [2,4],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [3,4],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [4,4],
                            ];

                        } elseif ($x == 1 && $y == 2) {
                            $fields[] = [
                                'level' => $l,
                                'coord' => [0,0],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [0,1],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [0,2],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [0,3],
                            ];

                            $fields[] = [
                                'level' => $l,
                                'coord' => [0,4],
                            ];
                        }

                        break;

                    case ($coord[0] < 0):
                        $l = $level-1;

                        $this->checkLevel($l);

                        $fields[] = [
                            'level' => $l,
                            'coord' => [1,2],
                        ];

                        break;

                    case ($coord[0] > 4):
                        $l = $level-1;

                        $this->checkLevel($l);

                        $fields[] = [
                            'level' => $l,
                            'coord' => [3,2],
                        ];

                        break;

                    case ($coord[1] < 0):
                        $l = $level-1;

                        $this->checkLevel($l);

                        $fields[] = [
                            'level' => $l,
                            'coord' => [2,1],
                        ];

                        break;

                    case ($coord[1] > 4):
                        $l = $level-1;

                        $this->checkLevel($l);

                        $fields[] = [
                            'level' => $l,
                            'coord' => [2,3],
                        ];

                        break;

                    default:
                        $fields[] = [
                            'level' => $level,
                            'coord' => $coord,
                        ];

                        break;
                    }
                }

                $g = $map['snap']->get();
                $g[$y][$x] = 'X';

                $p = [];
                $p[$level] = $g;

                $bugs = [];
                foreach ($fields as $v2) {
                    $vlevel = $v2['level'];
                    $vx = $v2['coord'][0];
                    $vy = $v2['coord'][1];

                    $snap = $this->maps[$vlevel]['snap'];
                    $v = $snap->getCoord($vx, $vy);

                    if (!isset($p[$vlevel])) {
                        $p[$vlevel] = $snap->get();
                    }

                    $p[$vlevel][$vy][$vx] = '^';

                    logger('contains field '.$vlevel.' @ '.$vx.','.$vy.', val '.$v);

                    if ($v == '#') $bugs[] = true;
                }

                /*
                ksort($p);
                echo 'ss'.PHP_EOL;
                foreach ($p as $k=>$pp) {
                    dump('level '.$k);
                    $snap->printn($pp);
                    echo PHP_EOL;
                }
                echo 'ee'.PHP_EOL;
                 */

                $act = null;
                if ($value == '.' && (count($bugs) == 1 || count($bugs) == 2)) {
                    $grid->append($x, $y, '#');
                    $act = 'birth';
                }

                if ($value == '#' && count($bugs) != 1) {
                    $grid->append($x, $y, '.');
                    $act = 'die';
                }

                logger('summary, '.$x.','.$y.', val '.$value.', bugs '.count($bugs).', action '.$act);
            }
        }
    }
}
