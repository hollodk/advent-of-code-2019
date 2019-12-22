<?php

require __DIR__.'/bootstrap.php';

$file = 'input-22.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));

$deck = new Deck(10);
$deck->test();

$rules = $tools->linesToArray($input);
$deck = new Deck(10007);
$deck->apply($rules);

foreach ($deck->cards as $key=>$value) {
    if ($value == '2019') {
        dump('card position is '.$key);
    }
}
