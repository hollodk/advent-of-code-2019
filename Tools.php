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

    public function resolvePattern($route)
    {
        $groups = [];

        for ($a = 1; $a < 20; $a++) {
            for ($b = 1; $b < 20; $b++) {
                for ($c = 1; $c < 20; $c++) {
                    $map = $route;

                    $ares = [];
                    $bres = [];
                    $cres = [];

                    for ($i = 0; $i < $a; $i++) {
                        $ares[] = array_shift($map);
                    }
                    $ares = implode(',', $ares);

                    $map = implode(',', $route);
                    $map = preg_replace("/".$ares."(,|)/", "A,", $map);
                    $map = preg_split("/,/", $map);

                    for ($i = 0; $i < $b; $i++) {
                        while (true) {
                            $o = array_shift($map);
                            if (!$o) break;
                            if (preg_match("/[^ABC]/", $o)) {
                                $bres[] = $o;
                                break;
                            }
                        }
                    }
                    $bres = implode(',', $bres);

                    $map = implode(',', $route);
                    $map = preg_replace("/".$ares."(,|)/", "A,", $map);
                    $map = preg_replace("/".$bres."(,|)/", "B,", $map);
                    $map = preg_split("/,/", $map);

                    for ($i = 0; $i < $c; $i++) {
                        while (true) {
                            $o = array_shift($map);
                            if (!$o) break;
                            if (preg_match("/[^ABC]/", $o)) {
                                $cres[] = $o;
                                break;
                            }
                        }
                    }
                    $cres = implode(',', $cres);

                    $map = implode(',', $route);
                    $map = preg_replace("/".$ares."(,|)/", "A,", $map);
                    $map = preg_replace("/".$bres."(,|)/", "B,", $map);
                    $map = preg_replace("/".$cres."(,|)/", "C,", $map);
                    $map = preg_replace("/,$/", "", $map);
                    $map = preg_split("/,/", $map);

                    $success = true;
                    foreach ($map as $v) {
                        if (preg_match("/[^ABC]/", $v)) {
                            $success = false;
                        }
                    }

                    if ($success) {
                        $diff = 0;
                        $len = 0;
                        $quality = 0;

                        $_a = strlen($ares);
                        $_b = strlen($bres);
                        $_c = strlen($cres);

                        $len = $_a+$_b+$_c;

                        $d = $_a-$_b;
                        $diff += ($d < 0) ? $d*-1 : $d;
                        $d = $_a-$_c;
                        $diff += ($d < 0) ? $d*-1 : $d;
                        $d = $_b-$_c;
                        $diff += ($d < 0) ? $d*-1 : $d;

                        $group = [
                            'diff' => $diff,
                            'len' => $len,
                            'quality' => $diff+$len,
                            'pattern' => $map,
                            'a' => preg_split("/,/", $ares),
                            'b' => preg_split("/,/", $bres),
                            'c' => preg_split("/,/", $cres),
                        ];

                        $groups[] = $group;
                    }
                }
            }
        }

        $lowest = null;
        $res = null;

        foreach ($groups as $group) {
            if ($lowest === null || $group['len'] < $lowest) {
                $lowest = $group['len'];
                $res = $group;
            }
        }

        return $res;
    }
}
