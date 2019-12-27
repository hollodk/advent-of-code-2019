<?php

class Grid
{
    public $x;
    public $y;

    private $coords = [];

    public function getUp($x, $y)
    {
        if (isset($this->coords[$y-1]) && isset($this->coords[$y-1][$x])) {
            return [
                'coord' => [
                    'x' => $x,
                    'y' => $y-1,
                ],
                'value' => $this->coords[$y-1][$x],
            ];
        }

        return null;
    }

    public function getDown($x, $y)
    {
        if (isset($this->coords[$y+1]) && isset($this->coords[$y+1][$x])) {
            return [
                'coord' => [
                    'x' => $x,
                    'y' => $y+1,
                ],
                'value' => $this->coords[$y+1][$x],
            ];
        }

        return null;
    }

    public function getLeft($x, $y)
    {
        if (isset($this->coords[$y]) && isset($this->coords[$y][$x-1])) {
            return [
                'coord' => [
                    'x' => $x-1,
                    'y' => $y,
                ],
                'value' => $this->coords[$y][$x-1],
            ];
        }

        return null;
    }

    public function getRight($x, $y)
    {
        if (isset($this->coords[$y]) && isset($this->coords[$y][$x+1])) {
            return [
                'coord' => [
                    'x' => $x+1,
                    'y' => $y,
                ],
                'value' => $this->coords[$y][$x+1],
            ];
        }

        return null;
    }

    public function getAround($x, $y)
    {
        $fields = [];
        $fields['up'] = $this->getUp($x, $y);
        $fields['down'] = $this->getDown($x, $y);
        $fields['left'] = $this->getLeft($x, $y);
        $fields['right'] = $this->getRight($x, $y);

        return $fields;
    }

    public function getAreaFields($name)
    {
        $values = $this->getFieldsByValue($name);
        $res = [];

        foreach ($values as $r) {
            $fields = [];
            $fields['up'] = $this->getUp($r['x'], $r['y']);
            $fields['down'] = $this->getDown($r['x'], $r['y']);
            $fields['left'] = $this->getLeft($r['x'], $r['y']);
            $fields['right'] = $this->getRight($r['x'], $r['y']);

            foreach ($fields as $key=>$field) {
                if ($field['value'] == '.') {
                    $res[$r['x'].','.$r['y']] = $field;
                }
            }
        }

        return $res;
    }

    public function getFieldsByValue($value)
    {
        $res = [];

        $values = str_split($value);

        foreach ($this->coords as $y=>$v1) {
            foreach ($v1 as $x=>$v2) {
                $fields = $this->getAround($x, $y);
                $match = 0;

                if ($v2 == $values[0]) {
                    foreach ($fields as $field) {
                        if ($field && $field['value'] == $values[1]) {
                            $match++;
                        }
                    }
                }

                if ($v2 == $values[1]) {
                    foreach ($fields as $field) {
                        if ($field && $field['value'] == $values[0]) {
                            $match++;
                        }
                    }
                }

                if ($match >= 1) {
                    $res[] = [
                        'x' => $x,
                        'y' => $y,
                    ];
                }
            }
        }

        return $res;
    }

    public function getFirstValue($y)
    {
        ksort($this->coords[$y]);

        dump($this->coords[$y]);die();
    }

    public function getValues()
    {
        $values = [];

        foreach ($this->coords as $y=>$v1) {
            foreach ($v1 as $x=>$value) {
                $key = $x.','.$y;
                $values[$key] = $value;
            }
        }

        return $values;
    }

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
        if (!isset($this->coords[$y]) || !isset($this->coords[$y][$x])) {
            return null;
        }

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

    public function init($hSize, $wSize, $default=null)
    {
        $grid = $this->build($hSize, $wSize, $default);
        $this->set($grid);

        return $this;
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

    public function printn($image=null, $exact=true, $scale=1, $speed=0, $ascii=true)
    {
        return $this->print($image, $scale, $speed, $ascii, $exact);
    }

    public function print($image=null, $scale=1, $speed=0, $ascii=true, $exact=false)
    {
        if ($image == null) {
            $image = $this->get();
        }

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

                        if ($exact) {
                            if ($digit === 0) {
                                echo ' ';

                            } elseif (strlen($digit) == 1) {
                                echo $digit;

                            } else {
                                echo ' ';
                            }

                        } else {
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
                            case $digit === '<':
                            case $digit === '>':
                            case $digit === '^':
                            case $digit === 'v':
                                echo $digit;
                                break;

                            default:
                                echo ' ';
                                break;
                            }
                        }
                    }
                }

                echo PHP_EOL;
            }
        }
    }

    public function addIntersection()
    {
        $sum = 0;
        $g = $this->get();

        foreach ($g as $y=>$v1) {
            foreach ($v1 as $x=>$value) {
                $points = [
                    [
                        'x' => $x,
                        'y' => $y,
                    ],
                    [
                        'x' => $x-1,
                        'y' => $y,
                    ],
                    [
                        'x' => $x+1,
                        'y' => $y,
                    ],
                    [
                        'x' => $x,
                        'y' => $y-1,
                    ],
                    [
                        'x' => $x,
                        'y' => $y+1,
                    ],
                ];

                $intersection = true;
                foreach ($points as $point) {
                    $xkey = $point['x'];
                    $ykey = $point['y'];

                    if (isset($g[$ykey][$xkey]) && $g[$ykey][$xkey] != '#') {
                        $intersection = false;
                    }
                }

                if ($intersection) {
                    $sum += $x*$y;

                    $g[$y][$x] = 'O';
                }
            }
        }

        $this->set($g);

        return $sum;
    }

    function setGridFromMap($input, $start=null)
    {
        $line = 0;
        $rows = 0;
        $startX = null;
        $startY = null;

        $o = str_split($input);

        foreach ($o as $key=>$value) {
            switch (true) {
            case $value != "\n":
                if ($rows == 1) $line++;
                break;

            default:
                $rows++;

                break;
            }
        }

        $line--;
        if ($o == "\n") {
            $rows++;
        }

        $x = 0;
        $y = 0;

        foreach ($o as $key=>$value) {
            if ($value == $start) {
                $startX = $x;
                $startY = $y;
            }

            switch (true) {
            case $value != "\n":
                $this->append($x, $y, $value);
                $x++;

                break;

            default:
                $rows++;
                $y++;
                $x = 0;

                break;
            }
        }

        return [
            'x' => $startX,
            'y' => $startY,
        ];
    }

    public function replace($search, $replace)
    {
        foreach ($this->coords as $y=>$v1) {
            foreach ($v1 as $x=>$value) {
                if ($search == $value) {
                    $this->coords[$y][$x] = $replace;
                }
            }
        }
    }

    public function getMax()
    {
        $minX = null;
        $minY = null;
        $maxX = null;
        $maxY = null;

        foreach ($this->coords as $y=>$v1) {
            if ($minY === null || $y < $minY) $minY = $y;
            if ($maxY === null || $y > $maxY) $maxY = $y;

            foreach ($v1 as $x=>$v2) {
                if ($minX === null || $x < $minX) $minX = $x;
                if ($maxX === null || $x > $maxX) $maxX = $x;
            }
        }

        return [
            'minX' => $minX,
            'minY' => $minY,
            'maxX' => $maxX,
            'maxY' => $maxY,
        ];
    }
}
