<?php

class Grid
{
    private $coords = [];

    public function append($x, $y, $value)
    {
        if (!isset($this->coords[$y])) {
            $this->coords[$y] = [];
        }

        $this->coords[$y][$x] = $value;
    }

    public function set($grid)
    {
        $this->coords = $grid;
    }

    public function get($fix=true)
    {
        if ($fix) {
            $this->coords = $this->fix($this->coords);
        }

        return $this->coords;
    }

    public function getCoord($x, $y)
    {
        return $this->coords[$y][$x];
    }

    public function fix($input)
    {
        if (count($input) == 0) {
            $input = [];

        } else {
            $minY = min(array_keys($input));
            $maxY = max(array_keys($input));
            $minX = null;
            $maxX = null;

            foreach ($input as $i) {
                $keys = array_keys($i);
                if ($minX === null || min($keys) < $minX) {
                    $minX = min($keys);
                }

                if ($maxX === null || max($keys) > $maxX) {
                    $maxX = max($keys);
                }
            }

            $rangeY = range($minY, $maxY);
            $rangeX = range($minX, $maxX);

            foreach ($rangeY as $y) {
                foreach ($rangeX as $x) {
                    if (!isset($input[$y])) {
                        $input[$y] = [];
                    }

                    if (!isset($input[$y][$x])) {
                        $input[$y][$x] = null;
                    }
                }

                ksort($input[$y]);
            }
            ksort($input);
        }

        return $input;
    }

    public function build($hSize, $wSize, $default=null)
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
                    $grid[$h][$w] = $default;
                }
            }
        }

        return $grid;
    }

    public function printLayer($image, $scale=1, $speed=0, $ascii=true)
    {
        foreach ($image as $layer) {
            $this->print($layer, $scale, $speed, $ascii);

            echo PHP_EOL;
        }
    }

    public function print($image, $scale=1, $speed=0, $ascii=true)
    {
        $white = 107;
        $black = 40;
        $red = 41;
        $green = 42;
        $yellow = 43;
        $blue = 44;
        $magenta = 45;
        $cyan = 46;

        foreach ($image as $height) {
            for ($myHScale = 0; $myHScale < $scale; $myHScale++) {
                foreach ($height as $digit) {

                    for ($myWScale = 0; $myWScale < $scale; $myWScale++) {
                        usleep($speed);

                        switch (true) {
                        case $digit === 0:
                        case $digit === 'black':
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

                        case $digit === 1:
                        case $digit === 'white':
                            if ($ascii) {
                                echo "\e[0;39;".$white."m \e[0m";
                            } else {
                                echo "#";
                            }
                            break;

                        case $digit === 2:
                        case $digit === 'red':
                            if ($ascii) {
                                echo "\e[0;39;".$red."m \e[0m";
                            } else {
                                echo "@";
                            }
                            break;

                        case $digit === 'green':
                            if ($ascii) {
                                echo "\e[0;39;".$green."m \e[0m";
                            } else {
                                echo "@";
                            }
                            break;

                        case $digit === 'yellow':
                            if ($ascii) {
                                echo "\e[0;39;".$yellow."m \e[0m";
                            } else {
                                echo "@";
                            }
                            break;

                        case $digit === 'blue':
                            if ($ascii) {
                                echo "\e[0;39;".$blue."m \e[0m";
                            } else {
                                echo "@";
                            }
                            break;

                        case $digit === 'magenta':
                            if ($ascii) {
                                echo "\e[0;39;".$magenta."m \e[0m";
                            } else {
                                echo "@";
                            }
                            break;

                        case $digit === 'cyan':
                            if ($ascii) {
                                echo "\e[0;39;".$cyan."m \e[0m";
                            } else {
                                echo "@";
                            }
                            break;

                        case $digit === '.':
                        case $digit === '#':
                        case $digit === '@':
                        case $digit === 'O':
                            echo $digit;
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
    }
}
