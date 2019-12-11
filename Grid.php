<?php

class Grid
{
    public function build($hSize, $wSize)
    {
        $hRange = range(0, $hSize);
        $wRange = range(0, $wSize);

        $grid = [];
        foreach ($hRange as $h) {
            foreach ($wRange as $w) {
                if (!isset($grid[$h])) {
                    $grid[$h] = [];
                }

                if (!isset($grid[$h][$w])) {
                    $grid[$h][$w] = [];
                }
            }
        }

        return $grid;
    }

    public function print($image, $scale=1, $speed=0, $ascii=true)
    {
        $white = 107;
        $black = 40;
        $red = 41;

        foreach ($image as $layer) {
            foreach ($layer as $height) {

                for ($myHScale = 0; $myHScale < $scale; $myHScale++) {
                    foreach ($height as $digit) {

                        for ($myWScale = 0; $myWScale < $scale; $myWScale++) {
                            usleep($speed);

                            switch ($digit) {
                            case 0:
                                if ($digit === null) {
                                    echo ' ';
                                } else {
                                    if ($ascii) {
                                        echo "\e[0;39;".$black."m \e[0m";
                                    } else {
                                        echo ".";
                                    }
                                }
                                break;

                            case 1:
                                if ($ascii) {
                                    echo "\e[0;39;".$white."m \e[0m";
                                } else {
                                    echo "#";
                                }
                                break;

                            case 2:
                                if ($ascii) {
                                    echo "\e[0;39;".$red."m \e[0m";
                                } else {
                                    echo "@";
                                }
                                break;

                            default:
                                echo ' ';
                                break;
                            }
                        }
                    }

                    echo PHP_EOL;
                }
            }

            echo PHP_EOL;
        }
    }

}
