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
        $hasDouble = false;

        $last = null;
        $row = 1;

        foreach ($numbers as $o) {
            if ($last && $o == $last) {
                $isFound = true;
            }

            if ($o < $last) {
                $isIncrease = false;
            }

            if (!$hasDouble) {
                if ($last != $o && $row == 2) {
                    $hasDouble = true;
                } elseif ($last && $last == $o) {
                    $row++;
                } else {
                    $row = 1;
                }
            }

            $last = $o;
        }

        if ($row == 2) {
            $hasDouble = true;
        }

        if (!$isFound) throw new \Exception('no adjacents');
        if (!$isIncrease) throw new \Exception('not increase');
        if (!$hasDouble) throw new \Exception('no double');

        $success++;
        var_dump($n);

    } catch (\Exception $e) {
    }
}

var_dump($success);
