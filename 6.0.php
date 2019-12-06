<?php

$file = file_get_contents(__DIR__.'/input-6.0.txt');

$list = preg_split("/\n/", $file);

foreach ($list as $key=>$value) {
    if (!$value) {
        unset($list[$key]);
    }
}

$nest = new nest();

foreach ($list as $o) {
    list($inner, $outer) = preg_split("/\)/", $o);

    $nest->build($inner, $outer);
}

$n = 0;
foreach ($nest->res as $search=>$data) {
    $nest->found = false;

    $nest->fix($nest->res, $search, $data, $n);
}

var_dump($nest->res);
var_dump($nest->count());

class nest
{
    public $res;
    public $unordered;
    public $found;
    public $total;

    public function __construct()
    {
        $this->res = new \StdClass();
    }

    public function fix($loop, $search, $data, $n)
    {
        if ($this->found) return;

        $n++;

        foreach ($loop as $key=>$value) {
            if ($this->found) break;

            if ($n > 1 && $key == $search) {
                $loop->{$key} = $data;
                unset($this->res->{$search});

                $this->found = true;
            }

            if ($this->found) break;

            $this->fix($value, $search, $data, $n);
        }
    }

    public function build($inner, $outer)
    {
        $this->found = false;
        $nest = 0;

        $this->loop($this->res, $inner, $outer, $nest);

        if (!$this->found) {
            $this->res->{$inner} = new \StdClass();
            $this->res->{$inner}->{$outer} = new \StdClass();
        }
    }

    public function loop($loop, $innerKey, $outerKey, $n)
    {
        $n++;

        if ($this->found) return;

        foreach ($loop as $key=>$inner) {
            if ($key == $innerKey) {

                if (isset($this->res->{$outerKey})) {
                    //var_dump('outer exists in root, '.$innerKey.' => '.$outerKey);

                    $inner->{$outerKey} = $this->res->{$outerKey};

                    unset($this->res->{$outerKey});

                } elseif (isset($this->res->{$innerKey})) {
                    //var_dump('inner exists in root, '.$innerKey.' => '.$outerKey);

                    $this->res->{$innerKey}->$outerKey = new \StdClass();

                } else {
                    $inner->{$outerKey} = new \StdClass();
                }

                $this->found = true;
                break;
            }

            $this->loop($inner, $innerKey, $outerKey, $n);
        }
    }

    public function count()
    {
        $n = 0;
        $this->c($this->res, $n);

        return $this->total;
    }

    public function c($loop, $n)
    {
        $n++;
        foreach ($loop as $key=>$value) {
            $this->total += $n-1;

            $this->c($value, $n);
        }
    }
}
