<?php

$range = range(273025,767253);

$success = 0;
foreach ($range as $n) {
    try {
        $numbers = str_split($n);

        $isValid = true;

        // 2 adjacent
        $isFound = false;
        $isIncrease = true;
        $last = null;

        foreach ($numbers as $o) {
            if (!$last) {
                $last = $o;
            } elseif ($o == $last) {
                $isFound = true;
            }

            if ($o < $last) {
                $isIncrease = false;
            }

            $last = $o;
        }

        if (!$isFound) throw new \Exception('no adjacents');
        if (!$isIncrease) throw new \Exception('not increase');

        $success++;

    } catch (\Exception $e) {
    }
}

var_dump($success);
