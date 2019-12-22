<?php

class Deck
{
    public $cards = [];

    public function __construct($cards)
    {
        $this->reset($cards);
    }

    public function reset($cards)
    {
        $this->cards = [];

        for ($i = 0; $i < $cards; $i++) {
            $this->cards[] = $i;
        }
    }

    public function apply($rules)
    {
        foreach ($rules as $rule) {
            switch (true) {
            case preg_match("/deal into new stack/", $rule):
                logger('apply: '.$rule);
                $this->newStack();

                break;

            case preg_match("/deal with increment (\d+)/", $rule, $o):
                logger('apply: increment with '.$o[1]);
                $this->increment($o[1]);

                break;

            case preg_match("/cut ((\-|)\d+)/", $rule, $o):
                logger('apply: cut with '.$o[1]);
                $this->cut($o[1]);

                break;

            default:
                throw new \Exception('not supported: '.$rule);
            }
        }

    }

    public function newStack()
    {
        $this->cards = array_reverse($this->cards);
    }

    public function increment($with)
    {
        $pos = 0;
        $count = 0;
        $pick = 0;
        $shuffle = [];

        while (count($shuffle) < count($this->cards)) {
            foreach ($this->cards as $card) {

                if (($count%$with) == 0) {
                    $shuffle[$pos] = $this->cards[$pick];
                    $pick++;
                }

                $pos++;

                if ($pos > count($this->cards)) {
                    $pos = 0;

                } else {
                    $count++;
                }
            }
        }

        ksort($shuffle);
        $this->cards = $shuffle;
    }

    public function cut($with)
    {
        if ($with < 0) {
            $two = array_slice($this->cards, $with);
            $one = array_slice($this->cards, 0, $with);

        } else {
            $one = array_slice($this->cards, 0, $with);
            $two = array_slice($this->cards, $with);
        }

        $this->cards = array_merge($two, $one);
    }

    public function test()
    {
        $rules = [];
        $rules[] = 'deal with increment 7';
        $rules[] = 'deal into new stack';
        $rules[] = 'deal into new stack';

        $this->reset(10);
        $this->apply($rules);

        if (implode(' ',$this->cards) != '0 3 6 9 2 5 8 1 4 7') {
            die('error 1');
        }

        $rules = [];
        $rules[] = 'cut 6';
        $rules[] = 'deal with increment 7';
        $rules[] = 'deal into new stack';

        $this->reset(10);
        $this->apply($rules);
        if (implode(' ',$this->cards) != '3 0 7 4 1 8 5 2 9 6') {
            die('error 2');
        }

        $rules = [];
        $rules[] = 'deal with increment 7';
        $rules[] = 'deal with increment 9';
        $rules[] = 'cut -2';

        $this->reset(10);
        $this->apply($rules);
        if (implode(' ',$this->cards) != '6 3 0 7 4 1 8 5 2 9') {
            die('error 3');
        }

        $rules = [];
        $rules[] = 'deal into new stack';
        $rules[] = 'cut -2';
        $rules[] = 'deal with increment 7';
        $rules[] = 'cut 8';
        $rules[] = 'cut -4';
        $rules[] = 'deal with increment 7';
        $rules[] = 'cut 3';
        $rules[] = 'deal with increment 9';
        $rules[] = 'deal with increment 3';
        $rules[] = 'cut -1';

        $this->reset(10);
        $this->apply($rules);
        if (implode(' ',$this->cards) != '9 2 5 8 1 4 7 0 3 6') {
            die('error 4');
        }

        echo 'deck test, all good'.PHP_EOL;

        return true;
    }
}
