<?php

require __DIR__.'/bootstrap.php';

$file = 'input-16.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$pat = [
    0,
    1,
    0,
    -1,
];

for ($i = 0; $i < 100; $i++) {
    $input = calc($input, $pat);

    dump('loop '.$i.': '.$input);
}

dump('result: '.substr($input, 0, 8));

function calc($input, $pat)
{
    $input = str_split($input);
    $results = [];

    for ($loop = 1; $loop <= count($input); $loop++) {
        $pattern = getPattern($pat, count($input), $loop);

        $result = 0;
        foreach ($input as $key=>$value) {
            $sum = $value*$pattern[$key+1];

            $result += $sum;
        }

        $results[] = substr($result, -1);
    }

    return implode($results);
}

function getPattern($pat, $length, $loop)
{
    $pattern = [];
    $list = [];

    while (true) {
        $break = false;

        for ($i = 0; $i < $length; $i++) {
            for ($j = 0; $j < $loop; $j++) {
                $list[] = ($i%count($pat));

                if (count($list) > $length) $break = true;

                if ($break) break;
            }

            if ($break) break;
        }

        if ($break) break;
    }

    foreach ($list as $key=>$value) {
        $pattern[] = $pat[$value];
    }

    unset($pattern[0]);

    return $pattern;
}
