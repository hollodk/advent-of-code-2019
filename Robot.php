<?php

class Robot
{
    private $paths = [];
    private $blocks = [];
    private $allowed;
    private $target;
    private $block;
    private $grid;
    private $x;
    private $y;
    private $log;
    private $type;

    public function __construct($log, $grid, $startX, $startY, $allowed, $target, $block, $startDirection, $type=null)
    {
        $this->log = $log;
        $this->grid = $grid;
        $this->x = $startX;
        $this->y = $startY;
        $this->allowed = $allowed;
        $this->target = $target;
        $this->block = $block;
        $this->type = $type;
        $this->startDirection = $startDirection;
    }

    public function run($print=false, $markCurrent=true, $sleep=0)
    {
        $sleep = $sleep*1000;
        $spawns = [];

        $spawn = [
            'x' => $this->x,
            'y' => $this->y,
            'prevX' => $this->x,
            'prevY' => $this->y,
            'steps' => 0,
            'direction' => $this->startDirection,
            'lastSuccessDirection' => $this->startDirection,
            'switch' => false,
            'path' => [],
        ];

        $spawns = $this->getSpawns($spawn, null);

        if ($print) {
            $this->grid->print();
        }

        while (true) {
            $this->log('spawns '.count($spawns));

            $g = $this->grid->get();
            foreach ($spawns as $spawnKey=>$spawn) {
                $this->log('spawn at '.$spawn['x'].','.$spawn['y']);

                $x = $spawn['x'];
                $y = $spawn['y'];

                $spawn['steps']++;

                $output = $this->grid->getCoord($spawn['x'], $spawn['y']);

                if (in_array($output, $this->allowed)) {
                    $this->log('ok, move to '.$x.','.$y);

                    if ($markCurrent) {
                        $g[$y][$x] = 'blue';
                    }

                    $spawn['lastSuccessDirection'] = $spawn['direction'];
                    $spawn['path'][] = $spawn['direction'];

                    $s = $this->getSpawns($spawn, null);
                    foreach ($s as $ss) {
                        $spawns[] = $ss;
                    }

                } elseif (in_array($output, $this->target)) {
                    $this->log('found target at '.$x.','.$y);

                } elseif (in_array($output, $this->block)) {
                    $this->log('block at '.$x.','.$y);

                    $key = $x.','.$y;
                    $this->blocks[$key] = true;

                    $spawn['switch'] = true;

                    if ($this->type == 'to-block') {
                        $s = $this->getSpawns($spawn);
                        foreach ($s as $ss) {
                            $spawns[] = $ss;
                        }
                    }

                } else {
                    $this->log('border at '.$x.','.$y);

                    $key = $x.','.$y;
                    $this->blocks[$key] = true;

                    $spawn['switch'] = true;

                    if ($this->type == 'to-block') {
                        $s = $this->getSpawns($spawn);
                        foreach ($s as $ss) {
                            $spawns[] = $ss;
                        }
                    }
                }

                $last = $spawns[$spawnKey];

                unset($spawns[$spawnKey]);
            }

            if ($print) {
                $this->grid->print($g);
            }

            if (count($spawns) == 0) break;

            usleep($sleep);

            if ($print) {
                echo PHP_EOL;
            }
        }

        return $last;
    }

    private function newSpawn($parent, $opt)
    {
        $spawn = [
            'direction' => $opt['direction'],
            'switch' => false,
            'lastSuccessDirection' => $parent['lastSuccessDirection'],
            'x' => $opt['x'],
            'y' => $opt['y'],
            'prevX' => $parent['prevX'],
            'prevY' => $parent['prevY'],
            'steps' => $parent['steps'],
            'path' => $parent['path'],
        ];

        $this->paths[$opt['pathKey']] = true;

        return $spawn;
    }

    private function getSpawns($parent)
    {
        $spawns = [];

        if ($this->type == 'to-block') {
            if ($parent['switch']) {
                switch ($parent['lastSuccessDirection']) {
                case 'n':
                    $blockDirection = 's';
                    break;

                case 's':
                    $blockDirection = 'n';
                    break;

                case 'w':
                    $blockDirection = 'e';
                    break;

                case 'e':
                    $blockDirection = 'w';
                    break;

                default:
                    throw new \Exception('not supported direction: '.$parent['direction']);
                }

                $options = $this->getOptions($parent['prevX'], $parent['prevY'], null, $blockDirection);

                foreach ($options as $i=>$opt) {
                    $spawns[] = $this->newSpawn($parent, $opt);

                    break;
                }

                //dump('paths', $this->paths, 'blocks', $this->blocks, 'parent', $parent, 'spawns', $spawns);

                $parent['switch'] = false;

            } else {
                $options = $this->getOptions($parent['x'], $parent['y'], $parent['direction']);

                foreach ($options as $i=>$opt) {
                    $s = $this->newSpawn($parent, $opt);
                    $s['prevX'] = $parent['x'];
                    $s['prevY'] = $parent['y'];

                    $spawns[] = $s;

                    break;
                }
            }

        } else {
            $options = $this->getOptions($parent['x'], $parent['y']);

            foreach ($options as $i=>$opt) {
                $spawns[] = $this->newSpawn($parent, $opt);
            }
        }

        return $spawns;
    }

    public function getOptions($x, $y, $onlyDirection=null, $blockDirection=null)
    {
        $options = [];

        $key = $x.','.$y;

        $range = ['n','s','w','e'];
        if ($onlyDirection) {
            $range = [$onlyDirection];
        }

        if ($blockDirection) {
            unset($range[array_search($blockDirection, $range)]);
        }

        foreach ($range as $r) {
            switch ($r) {
            case 'n':
                $nx = $x;
                $ny = $y-1;

                break;

            case 's':
                $nx = $x;
                $ny = $y+1;

                break;

            case 'w':
                $nx = $x-1;
                $ny = $y;

                break;

            case 'e':
                $nx = $x+1;
                $ny = $y;

                break;
            }

            $nkey = $nx.','.$ny;

            if ($this->type == 'to-block') {
                if (!isset($this->blocks[$nkey])) {
                    $options[] = [
                        'direction' => $r,
                        'x' => $nx,
                        'y' => $ny,
                        'pathKey' => $nkey,
                    ];
                }

            } else {
                if (!isset($this->paths[$nkey]) && !isset($this->blocks[$nkey])) {
                    $options[] = [
                        'direction' => $r,
                        'x' => $nx,
                        'y' => $ny,
                        'pathKey' => $nkey,
                    ];
                }
            }
        }

        return $options;
    }

    private function log($msg)
    {
        if ($this->log) {
            echo $msg.PHP_EOL;
        }
    }
}
