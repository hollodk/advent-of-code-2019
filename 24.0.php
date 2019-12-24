<?php

require __DIR__.'/bootstrap.php';

$file = 'input-24.txt';
//$file = 'input-24-test1.txt';

$input = trim(file_get_contents(__DIR__.'/'.$file));

$grid->setGridFromMap($input);
$prev = [];

while (true) {
    $snap = clone $grid;

    $hash = getHash($grid);
    $prev[$hash] = true;

    foreach ($grid->get() as $y=>$v1) {
        foreach ($v1 as $x=>$value) {
            $fields = [];
            $fields[] = $snap->getCoord($x, $y-1);
            $fields[] = $snap->getCoord($x, $y+1);
            $fields[] = $snap->getCoord($x-1, $y);
            $fields[] = $snap->getCoord($x+1, $y);

            $bugs = [];
            foreach ($fields as $v2) {
                if ($v2 == '#') $bugs[] = $v2;
            }

            $act = null;

            if ($value == '.' && (count($bugs) == 1 || count($bugs) == 2)) {
                $grid->append($x, $y, '#');
                $act = 'birth';
            }

            if ($value == '#' && count($bugs) != 1) {
                $grid->append($x, $y, '.');
                $act = 'die';
            }

            //dump($x.','.$y.', val '.$value.', cnt '.count($bugs).', action '.$act);
        }
    }

    if (isset($prev[getHash($grid)])) {
        $grid->printn();

        dump('loops '.count($prev).', sum '.sum($grid));

        break;
    }
}

function sum($grid)
{
    $sum = 0;
    $pcount = 0;
    $num = 0;

    foreach ($grid->get() as $y=>$v1) {
        foreach ($v1 as $x=>$value) {
            $num++;
            $power = pow(2, $pcount);

            if ($value == '#') {
                $sum += $power;
            }

            dump($x.','.$y.', num '.$num.', val '.$value.', power '.$power);
            $pcount++;
        }
    }

    return $sum;
}

function getHash($grid)
{
    $map = $grid->get();

    return hash('md5', json_encode($map));
}
