<?php

require __DIR__.'/bootstrap.php';

$file = 'test-19.txt';
$file = 'input-19.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

if (file_exists('grid-19.txt')) {
    $grid = unserialize(file_get_contents('grid-19.txt'));
}

$x = 825;
$y = 1090;
$sq = 99;

/*
$g = $grid->get();
$grid->printn($g);

$x = $grid->x;
$y = $grid->y;

$x = 24;
$y = 28;
$sq = 9;
 */

/*
$x = 0;
$y = 0;
$sq = 10-1;

$grid->setGridFromMap($input);
 */

/*
while (true) {
    $out = test2($grid, $x, $y);
    logger($x.','.$y.', value '.$out);

    if ($out == 1) {

        // xtra
        $xtra = [];

        $lowX = $x-50;
        $highX = $x+130;
        $lowY = $y-20;
        $highY = $y+20;

        $g = $grid->get();

        foreach ($g as $yy=>$v1) {
            $c = 0;
            if ($yy > $lowY && $yy < $highY) {
                foreach ($v1 as $xx=>$v2) {
                    if ($xx > $lowX && $xx < $highX) {
                        if (!isset($xtra[$yy])) {
                            $xtra[$yy] = [];
                        }

                        if ($v2 == 1 && ($c%10) == 0) {
                            $xtra[$yy][$xx] = 9;
                        } else {
                            $xtra[$yy][$xx] = $v2;
                        }
                        $c++;
                    }
                }
            }
        }

        $xtra[$y-$sq][$x] = '^';
        $xtra[$y-$sq][$x+$sq] = '^';
        $xtra[$y][$x] = '<';
        $xtra[$y][$x+$sq] = '>';

        $grid->printn($xtra);

        sleep(1);
        // end xtra

        logger('> first @ '.$x.','.$y.', next '.$x.','.($y-$sq));
        $out = test2($grid, $x, $y-$sq);

        if ($out == 1) {
            logger('>> second @ '.($x+$sq).','.$y.', next '.($x+$sq).','.$y);
            $out = test2($grid, $x+$sq, $y);

            if ($out == 1) {
                logger('>>> third @ '.$x.','.($y-$sq).', next '.($x+$sq).','.($y-$sq));
                $out = test2($grid, $x+$sq, $y-$sq);

                if ($out == 1) {
                    logger('>>>> fourth @ '.($x+$sq).','.($y-$sq));

                    $res = $x*10000+($y-$sq);
                    logger('>> found '.$res);

                    $xtra = [];

                    $lowX = $x-50;
                    $lowY = $y-$sq-10;
                    $highY = $y+10;
                    $highX = $x+130;

                    $g = $grid->get();

                    foreach ($g as $yy=>$v1) {
                        $c = 0;
                        if ($yy > $lowY && $yy < $highY) {
                            foreach ($v1 as $xx=>$v2) {
                                if ($xx > $lowX && $xx < $highX) {
                                    if (!isset($xtra[$yy])) {
                                        $xtra[$yy] = [];
                                    }

                                    if ($v2 == 1 && ($c%10) == 0) {
                                        $xtra[$yy][$xx] = 9;
                                    } else {
                                        $xtra[$yy][$xx] = $v2;
                                    }
                                    $c++;
                                }
                            }
                        }
                    }

                    $xtra[$y-$sq][$x] = '^';
                    $xtra[$y-$sq][$x+$sq] = '^';
                    $xtra[$y][$x] = '<';
                    $xtra[$y][$x+$sq] = '>';

                    $grid->printn($xtra);

                    logger(sprintf('%s,%s => %s,%s',
                        $x,
                        $y-$sq,
                        $x+$sq,
                        $y-$sq
                    ));

                    logger(sprintf('%s,%s => %s,%s',
                        $x,
                        $y,
                        $x+$sq,
                        $y
                    ));

                    die('eeeeh');
                }
            }
        }

        $y += 1;

    } else {
        $x += 1;
    }
}

die();
 */

$foundOne = false;
$ended = false;
$rZero = 0;
$found = 0;
$lastX = null;
$lastY = null;

$grid = new Grid();

while (true) {
    if ($x < 0) $x = 0;
    if ($y < 0) $y = 0;

    $out = test($input, $x, $y);
    logger($x.','.$y.' value: '.(int)$out.', zero: '.$rZero.', found: '.$found);

    $grid->append($x, $y, $out);

    if ($out == 0 && $foundOne) {
        logger('ended');
        $ended = true;
    }

    if ($out == 1) {
        $outT = test($input, $x+$sq, $y);
        $grid->append($x+$sq, $y, $outT);

        if ($outT == 1) {
            $outT = test($input, $x, $y-$sq);
            //logger('looking for '.$sq.' in y, '.$x.','.($y-$sq));

            if ($outT == 1) {
                $outT = test($input, $x+$sq, $y-$sq);
                //logger('looking for '.$sq.' in x, '.($x+$sq).','.($y-$sq));

                if ($outT == 1) {
                    $res = $x*10000+($y-$sq);
                    logger('found '.$res);

                    die();
                }
            }
        }

    } else {
        //$rZero++;
        $x++;
    }

    if ($out == 1) {
        $foundOne = true;
        $ended = true;
    }

    if ($out == 1 && (($foundOne && $ended) || $rZero > 10)) {
        $g = $grid->get();
        $g = array_slice($g, -20);

        $grid->printn($g);

        $y++;
        //$x = $lastX-5;
        //$x = $lastX;

        $foundOne = false;
        $ended = false;
        $rZero = 0;
        $found = 0;

        $lastX = null;
        $lastY = null;

        $grid->x = $x;
        $grid->y = $y;

        logger('set x,y '.$x.','.$y);

        //file_put_contents('grid-19.txt', serialize($grid));

    } elseif ($out == 1) {
        if ($lastX === null) {
            $lastX = $x;
            $lastY = $y;
        }

        //logger('found at '.$x.','.$y);

        $found++;
        $foundOne = true;
    }
}

function test2($grid, $x, $y)
{
    $out = $grid->getCoord($x, $y);
    if ($out == 1 || $out == 9 || $out == '#' || $out == 'O') {
        return true;
    }

    return false;
}

function test($input, $x, $y)
{
    logger('looking for in y, '.$x.','.$y);

    $ic = new IntCode();
    $ic->setCode($input);
    $ic->configurePhases();

    $ic->addInput(0,$x);
    $ic->addInput(0,$y);

    $o = $ic->run(0);

    return $o[0];
}
