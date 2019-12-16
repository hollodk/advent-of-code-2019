<?php

require __DIR__.'/bootstrap.php';

$file = 'input-15.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$ic = new IntCode();
$ic->setCode($input);
$ic->configurePhases();

$sleep = 100*1000;

//$grid->set(unserialize(file_get_contents('grid-15.txt')));

$robot = new Robot();

$options = $robot->getOptions(0, 0);
$foundOxygen = null;
$oxygenX = null;
$oxygenY = null;

foreach ($options as $i=>$opt) {
    $cloneInt = clone $ic;
    $cloneInt->addInput(0, $opt['direction']);

    $spawns[] = [
        'ic' => $cloneInt,
        'x' => $opt['x'],
        'y' => $opt['y'],
        'steps' => 0,
    ];
}

while (true) {
    logger('spawns '.count($spawns));

    $g = $grid->get();

    foreach ($spawns as $spawnKey=>$spawn) {
        $x = $spawn['x'];
        $y = $spawn['y'];

        $spawn['steps']++;

        $output = $spawn['ic']->run(0);

        if (isset($output[0])) {
            if ($output[0] === 1) {
                $grid->append($x, $y, 'yellow');

                $g[$y][$x] = 'blue';
                logger('ok, move to '.$x.','.$y);

                $options = $robot->getOptions($x, $y);

                foreach ($options as $i=>$opt) {
                    logger('spawn '.$spawnKey.', walk in direction '.$opt['direction'].', steps '.$spawn['steps']);

                    $cloneInt = clone $spawn['ic'];
                    $cloneInt->addInput(0, $opt['direction']);

                    $spawns[] = [
                        'ic' => $cloneInt,
                        'x' => $opt['x'],
                        'y' => $opt['y'],
                        'steps' => $spawn['steps'],
                    ];
                }

            } elseif ($output[0] === 2) {
                $grid->append($x, $y, 'green');

                logger('found oxigen at '.$x.','.$y);

                if ($foundOxygen === null) {
                    $foundOxygen = $spawn['steps'];
                    $oxygenX = $spawn['x'];
                    $oxygenY = $spawn['y'];
                }

            } elseif ($output[0] === 0) {
                $grid->append($x, $y, 'red');

                logger('wall at '.$x.','.$y);
            }
        }

       unset($spawns[$spawnKey]);
    }

    $grid->print($g);
    if ($foundOxygen) {
        dump('found oxygen after '.$foundOxygen.', '.$oxygenX.','.$oxygenY);
    }

    if (count($spawns) == 0) break;

    usleep($sleep);

    echo PHP_EOL;
}

function logger($msg)
{
    return;
    echo $msg.PHP_EOL;
}

class Robot
{
    private $paths = [];

    public function getOptions($x, $y)
    {
        $options = [];

        $key = $x.','.$y;

        $range = range(1, 4);
        foreach ($range as $r) {
            switch ($r) {
            case 1:
                $nx = $x;
                $ny = $y-1;

                break;

            case 2:
                $nx = $x;
                $ny = $y+1;

                break;

            case 3:
                $nx = $x-1;
                $ny = $y;

                break;

            case 4:
                $nx = $x+1;
                $ny = $y;

                break;
            }

            $nkey = $nx.','.$ny;

            if (!isset($this->paths[$nkey])) {
                $options[] = [
                    'direction' => $r,
                    'x' => $nx,
                    'y' => $ny,
                ];
            }
        }

        $this->paths[$key] = true;

        return $options;
    }
}
