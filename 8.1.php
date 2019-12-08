<?php

$input = trim(file_get_contents('input-8.0.txt'));
$w = 25;
$h = 6;

/*
$input = '0222112222120000';
$w = 2;
$h = 2;
 */

$digits = str_split($input);
$gi = new getImage();
$image = $gi->process($digits, $w, $h);
$res = $gi->validate($image);
var_dump('validate: '.$res);

$image = $gi->render($image);
$gi->print($image, 8);

class getImage
{
    public function process($digits, $w, $h)
    {
        $layers = [];

        $myLayersCount = 0;
        $myDigitsCount = 0;

        while (true) {
            $layers[$myLayersCount] = [];

            for ($myHCount = 0; $myHCount < $h; $myHCount++) {

                for ($myWCount = 0; $myWCount < $w; $myWCount++) {

                    if (!isset($layers[$myLayersCount][$myHCount])) {
                        $layers[$myLayersCount][$myHCount] = [];
                    }

                    $layers[$myLayersCount][$myHCount][] = $digits[$myDigitsCount];

                    $myDigitsCount++;

                    if (!isset($digits[$myDigitsCount])) {
                        return $layers;
                    }
                }
            }

            $myLayersCount++;
        }
    }

    public function validate($image)
    {
        $count = [];

        foreach ($image as $layerCount=>$layer) {
            $count[$layerCount] = [];

            foreach ($layer as $height) {
                foreach ($height as $digit) {

                    if (!isset($count[$layerCount][$digit])) {
                        $count[$layerCount][$digit] = 0;
                    }

                    $count[$layerCount][$digit]++;
                }
            }
        }

        $mostZero = null;
        $amount = 0;

        foreach ($count as $layerCount=>$layer) {
            if ($mostZero === null) {
                $mostZero = $layerCount;

                $amount = 0;
                if (isset($count[$layerCount][0])) {
                    $amount = $count[$layerCount][0];
                }
            }

            if (isset($count[$layerCount][0]) && $count[$layerCount][0] < $amount) {
                $mostZero = $layerCount;
                $amount = $count[$layerCount][0];
            }
        }

        var_dump('most zero layer: '.$mostZero);

        if (!isset($count[$mostZero][1]) || !isset($count[$mostZero][2])) {
            return 0;
        } else {
            return $count[$mostZero][1]*$count[$mostZero][2];
        }
    }

    public function print($image, $scale=1)
    {
        foreach ($image as $layer) {
            foreach ($layer as $height) {

                for ($myHScale = 0; $myHScale < $scale; $myHScale++) {
                    foreach ($height as $digit) {

                        for ($myWScale = 0; $myWScale < $scale; $myWScale++) {
                            echo $digit;
                        }
                    }

                    echo PHP_EOL;
                }
            }

            echo PHP_EOL;
        }
    }

    public function render($image)
    {
        $grid = [];

        foreach ($image as $layer) {
            foreach ($layer as $h=>$height) {
                if (!isset($grid[$h])) {
                    $grid[$h] = [];
                }

                foreach ($height as $w=>$digit) {
                    if (!isset($grid[$h][$w])) {
                        $grid[$h][$w] = null;
                    }

                    if ($grid[$h][$w] === null || $grid[$h][$w] == 2) {
                        $grid[$h][$w] = $digit;
                    }
                }
            }
        }

        return [$grid];
    }
}