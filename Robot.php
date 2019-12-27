<?php

class Robot
{
    private $paths = [];
    private $blocks = [];
    private $found = [];
    private $warps = [];
    private $levels = [];
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

        if (!is_array($target)) {
            $this->target = [];
        } else {
            $this->target = $target;
        }

        $this->type = $type;
        $this->startDirection = $startDirection;
    }

    public function getFound()
    {
        return $this->found;
    }

    public function setGrid($grid)
    {
        $this->grid = $grid;
    }

    public function setStart($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function reset()
    {
        foreach ($this->levels as $level) {
            $n = new \StdClass();
            $n->paths = [];
            $n->spawns = [];
            $n->blocks = [];
            $n->found = [];
            $n->allowed = $this->allowed;

            $this->levels[$level] = $n;
        }
    }

    public function getGrid()
    {
        return $this->grid;
    }

    public function setWarps($warps)
    {
        $this->warps = $warps;
    }

    public function setTargets($target, $level=0)
    {
        if (!is_array($target)) {
            $this->levels[$level]->target = [$target];
        } else {
            $this->levels[$level]->target = $target;
        }
    }

    public function print($spawns)
    {
        $levels = [];
        foreach ($spawns as $spawn) {
            $level = $spawn['level'];

            if (isset($this->levels[$level]->grid)) {
                $levels[$level] = $this->levels[$level]->grid->get();
            }
        }

        krsort($levels);

        foreach ($spawns as $s) {
            $level = $s['level'];

            if (isset($this->levels[$level]->grid)) {
                $out = $this->levels[$level]->grid->getCoord($s['x'], $s['y']);

                if (in_array($out, $this->levels[$level]->target) || in_array($out, $this->levels[$level]->allowed)) {
                    $levels[$level][$s['y']][$s['x']] = '^';
                }
            }
        }

        foreach ($levels as $level=>$image) {
            dump('level '.$level);

            $this->grid->print($image, 1, 0, true, true);
        }
    }

    private function buildLevel($level)
    {
        if (!isset($this->levels[$level]) || !isset($this->levels[$level]->grid)) {
            $n = new \StdClass();
            $n->paths = [];
            $n->blocks = [];
            $n->grid = $this->grid;
            $n->warps = $this->warps;
            $n->allowed = $this->allowed;

            if ($level === 0) {
                $n->target = $this->target;
            } else {
                $n->target = [];
            }

            $this->levels[$level] = $n;
        }

        return $this->levels[$level];
    }

    public function run($print=false, $markCurrent=true, $sleep=0, $returnFound=false)
    {
        $sleep = $sleep*1000;
        $spawns = [];

        $spawn = [
            'x' => $this->x,
            'y' => $this->y,
            'level' => 0,
            'prevX' => $this->x,
            'prevY' => $this->y,
            'steps' => 0,
            'direction' => $this->startDirection,
            'lastSuccessDirection' => $this->startDirection,
            'switch' => false,
            'path' => [],
        ];

        $this->buildLevel(0);

        $spawns = $this->getSpawns($spawn, null);
        if ($print) $this->print($spawns);

        while (true) {
            $this->log('spawns '.count($spawns));

            foreach ($spawns as $spawnKey=>$spawn) {
                $level = $spawn['level'];

                $this->log('spawn at '.$spawn['x'].','.$spawn['y']);

                $x = $spawn['x'];
                $y = $spawn['y'];

                $spawn['steps']++;

                $output = $this->levels[$level]->grid->getCoord($spawn['x'], $spawn['y']);

                $foundTarget = false;
                $hasWarp = false;

                if (in_array($output, $this->levels[$level]->target)) {
                    $foundTarget = true;
                }

                foreach ($this->levels[$level]->target as $target) {
                    if (isset($target['x']) && $x == $target['x'] && $y == $target['y']) {
                        $foundTarget = true;
                    }
                }

                foreach ($this->warps as $warp) {
                    if ($x == $warp['from']['x'] && $y == $warp['from']['y']) {
                        $hasWarp = true;

                        $this->log('warp from '.$x.','.$y.' to '.$warp['to']['x'].','.$warp['to']['y']);

                        $x = $warp['to']['x'];
                        $y = $warp['to']['y'];

                        $spawn['x'] = $x;
                        $spawn['y'] = $y;
                        $spawn['steps']++;
                        $spawn['level'] += $warp['level'];

                        $spawn['path'][] = 'warp '.$warp['name'].', '.$warp['level'].', curr '.$spawn['level'];
                        $this->buildLevel($spawn['level']);

                        break;
                    }
                }

                if (!$foundTarget && ($hasWarp || in_array($output, $this->levels[$level]->allowed))) {
                    $this->log('ok, move to '.$x.','.$y);

                    $spawn['lastSuccessDirection'] = $spawn['direction'];
                    $spawn['path'][] = $spawn['direction'];

                    if ($markCurrent) {
                        $g[$y][$x] = '^';
                    }

                    $s = $this->getSpawns($spawn, null);
                    foreach ($s as $ss) {
                        $spawns[] = $ss;
                    }

                } elseif ($foundTarget) {
                    $this->log('found target at '.$x.','.$y);

                    if ($markCurrent) {
                        $g[$y][$x] = '^';
                    }

                    $spawn['foundTarget'] = true;
                    $spawn['target'] = $output;

                    $this->found[] = [
                        'value' => $output,
                        'spawn' => $spawn,
                    ];

                    if ($returnFound) {
                        return $spawn;
                    }

                } else {
                    $this->log('border at '.$x.','.$y);

                    $key = $x.','.$y;
                    $this->levels[$level]->blocks[$key] = true;

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

            if ($print) $this->print($spawns);

            if (count($spawns) == 0) break;

            usleep($sleep);
        }

        return isset($last) ? $last : null;
    }

    private function newSpawn($parent, $opt)
    {
        $spawn = [
            'direction' => $opt['direction'],
            'switch' => false,
            'lastSuccessDirection' => $parent['lastSuccessDirection'],
            'foundTarget' => false,
            'x' => $opt['x'],
            'y' => $opt['y'],
            'level' => $parent['level'],
            'prevX' => $parent['prevX'],
            'prevY' => $parent['prevY'],
            'steps' => $parent['steps'],
            'path' => $parent['path'],
        ];

        $this->levels[$parent['level']]->paths[$opt['pathKey']] = true;

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

                $options = $this->getOptions($parent['prevX'], $parent['prevY'], null, $blockDirection, $spawn['level']);

                foreach ($options as $i=>$opt) {
                    $spawns[] = $this->newSpawn($parent, $opt);

                    break;
                }

                //dump('paths', $this->paths, 'blocks', $this->blocks, 'parent', $parent, 'spawns', $spawns);

                $parent['switch'] = false;

            } else {
                $options = $this->getOptions($parent['x'], $parent['y'], $parent['direction'], null, $spawn['level']);

                foreach ($options as $i=>$opt) {
                    $s = $this->newSpawn($parent, $opt);
                    $s['prevX'] = $parent['x'];
                    $s['prevY'] = $parent['y'];

                    $spawns[] = $s;

                    break;
                }
            }

        } else {
            $options = $this->getOptions($parent['x'], $parent['y'], null, null, $parent['level']);

            foreach ($options as $i=>$opt) {
                $spawns[] = $this->newSpawn($parent, $opt);
            }
        }

        return $spawns;
    }

    public function getOptions($x, $y, $onlyDirection=null, $blockDirection=null, $level=0)
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
                if (!isset($this->levels[$level]->blocks[$nkey])) {
                    $options[] = [
                        'direction' => $r,
                        'x' => $nx,
                        'y' => $ny,
                        'pathKey' => $nkey,
                    ];
                }

            } else {
                if (!isset($this->levels[$level]->paths[$nkey]) && !isset($this->levels[$level]->blocks[$nkey])) {
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
