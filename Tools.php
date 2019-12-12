<?php

class Tools
{
    public function getDistance($x1, $y1, $x2, $y2)
    {
        return sqrt(pow($x2-$x1,2) + pow($y2-$y1,2));
    }

    public function pi()
    {
        $theValue = 1000000;
        $pi = 0;
        for ($i=1; $i<$theValue; $i++){
            if ($i % 2 == 1){
                $pi += 4.0 / ($i * 2 - 1);
            } else {
                $pi -= 4.0 / ($i * 2 - 1);
            }
        }

        return $pi;
    }

    public function linesToArray($input)
    {
        return preg_split("/\n/", $input);
    }
}
