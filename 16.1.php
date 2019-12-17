<?php
require __DIR__.'/bootstrap.php';

$fft = new Fft();
$fft->test();

$skipTen = false;

//getRealInput();

$file = 'input-16.txt';
$input = trim(file_get_contents(__DIR__.'/'.$file));
$search = 'gfdgdg';
$filename = '/tmp/cache.txt';

$chars = 7;
$length = 8;

/*
$chars = 1;
$length = 8;
$input = '41234153';
$skipTen = true;

// ^ "start input 4123412"
// ^ "offset: 4"
// ^ "result: 412"

// ^ "loop 10000, output: 70000, per second 2000"
// ^ "result: 73204622"
// ^ "loop 100, per sec 50"

// ^ "loop 69700, per sec 79.204545454545"
// ^ "loop 69800, per sec 79.228149829739"
// ^ "loop 69900, per sec 79.251700680272"
// ^ "loop 70000, per sec 79.275198187995"
// ^ "> loop 0, result: 84586502463460223564269544163167666741462601593014, offset: 65024634, per second 0"

$chars = 7;
$length = 100;

$input = '02935109699940807407585447034323';
$search = '78725270';

$input = '03036732577212944063491565474664';
$search = '84462026';

$filename = '/tmp/mehxx_'.substr($input, 0, 10);
 */

$offset = substr($input, 0, $chars);

dump('start input '.substr($input, 0, 50));
dump('offset: '.$offset);

$output = [];
$input = str_split($input);

if (!$skipTen) {
    dump('run 10k');
    if (file_exists($filename)) {
        $input = trim(file_get_contents($filename));

    } else {

        for ($i = 0; $i < 10000; $i++) {
            $output = array_merge($output, $input);

            if (($i%500) == 0) {
                dump('loop '.$i.', output: '.count($output));
            }
        }

        $input = implode($output);
        file_put_contents($filename, $input);
    }
}

$start = time();

dump('result: '.substr($input, $offset, $length));
dump(strlen($input));

$input = substr($input, $offset);
$input = str_split($input);

dump(count($input));
dump('out -1, '.substr(implode($input), 0, $length));

for ($i = 0; $i < 100; $i++) {
    $input = array_reverse($input);

    $output = [];
    $sum = 0;

    foreach ($input as $key=>$value) {
        $sum += $value;

        $o = substr($sum, -1);
        $output[] = $o;
    }

    if (preg_match("/$search/", implode($output))) {
        dump($i.', wuhuu');
    }

    $output = array_reverse($output);

    if (($i%10) == 0) {
        dump('out '.sprintf('%02d', $i).', '.substr(implode($output), 0, $length));
    }

    $input = $output;

    if (preg_match("/$search/", implode($output))) {
        dump($i.', wuhuu');
    }
}

dump('out '.$i.', '.substr(implode($output), 0, $length));

/*
die();

$input = $startInput;

$last = $offsetRes;
$lastRes = $input;
$total = 0;
$resTotal = 0;

dump('loop '.sprintf('%02d', -1).', result: '.substr($input, 0, 50).', offset: '.$offsetRes);
for ($i = 0; $i < 100; $i++) {
    $input = implode($fft->calc($input, true));

    $diff = time()-$start;
    $diff = ($diff == 0) ? 1 : $diff;

    $offsetRes = substr($input, $offset, $length);
    $resDiff = ($lastRes-$input);
    $total += $input;
    $offsetDiff = ($last-$offsetRes);
    $resTotal += $resDiff;

    //dump('loop '.sprintf('%02d', $i).', result: '.substr($input, 0, 50).', offset: '.$offsetRes.', per second '.round($i/$diff).', offset diff '.$offsetDiff.', res diff '.number_format($resDiff).', total diff '.number_format($total).', res total '.number_format($resTotal));
    dump('loop '.sprintf('%02d', $i).', result: '.substr($input, 0, 50).', offset: '.$offsetRes.', per second '.round($i/$diff));

    $last = $offsetRes;
    $lastRes = $input;
}

dump('result: '.substr($input, $offset, $length).', loops: '.$i);

dump('start input '.substr($startInput, 0, 50));
dump('offset: '.$startOffset);
 */

class Fft
{
    private $list;
    private $countList;

    public function test($debug=false, $modulus=1)
    {
        $input = '69317163492948606335995924319873';

        for ($i = 0; $i < 100; $i++) {
            $input = implode($this->calc($input, $debug, $modulus));
        }

        $result = substr($input, 0, 8);
        if ($result != '52432133') {
            die('bad test, something is wrong');
        }
    }

    public function calc($input, $debug=false, $modulus=100)
    {
        if (is_string($input)) {
            $input = str_split($input);
        }

        $results = [];

        $start = time();
        for ($loop = 1; $loop <= count($input); $loop++) {
            $this->setPattern(count($input), $loop);

            if ($debug) {
                $diff = time()-$start;
                $diff = ($diff > 0) ? $diff : 1;

                if (($loop%$modulus) == 0) {
                    dump('loop '.$loop.', per sec '.$loop/$diff);
                }
            }

            $result = 0;
            foreach ($input as $key=>$value) {
                if ($key > $loop-2) {
                    $pat = $this->getPat($key);
                    $sum = $value*$pat;

                    $result += $sum;
                }
            }

            $results[] = substr($result, -1);
        }

        return $results;
    }

    public function getPat($key)
    {
        $index = $key+1;

        $r = $index/$this->countList;
        if ($r > 0) {
            $index = $index-(floor($r)*$this->countList);
        }

        return $this->list[$index];
    }

    public function setPattern($length, $loop)
    {
        $pat = [
            0,
            1,
            0,
            -1,
        ];

        $this->list = [];
        $break = false;

        foreach ($pat as $key=>$value) {
            $r = array_fill(0, $loop, $value);
            $this->list = array_merge($this->list, $r);

            if (count($this->list) > $length) break;
        }

        $this->countList = count($this->list);

        return $this;
    }

    public function getRealInput()
    {
        $file = 'input-16.txt';
        $input = trim(file_get_contents(__DIR__.'/'.$file));

        $output = [];
        $start = time();

        for ($i = 0; $i < 10000; $i++) {
            $out = $this->calc($input);

            $output = array_merge($output, $out);
            $input = implode($out);

            $diff = time()-$start;
            if (($i%10) == 0 && $diff > 0) {
                dump('loop '.$i.', output: '.count($output).', per second '.round($i/$diff));
            }
        }

        dump('loop '.$i.', output: '.count($output).', per second '.round($i/$diff));

        $input = implode($output);

        file_put_contents(__DIR__.'/input-16.10000.txt', $input);
    }
}
