<?php

$input = trim(file_get_contents('input-8.0.txt'));
$w = 25;
$h = 6;

$digits = str_split($input);
$gi = new getImage();
$image = $gi->process($digits, $w, $h);
$gi->print($image);
$res = $gi->validate($image);

var_dump($res);

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

        return $count[$mostZero][1]*$count[$mostZero][2];
    }

    public function print($image)
    {
        foreach ($image as $layer) {
            foreach ($layer as $height) {
                foreach ($height as $digit) {
                    echo $digit;
                }

                echo PHP_EOL;
            }

            echo PHP_EOL;
        }
    }
}
