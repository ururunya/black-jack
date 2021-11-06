<?php

namespace BlackJack;

class Dealer extends Actor
{
    public string $name = 'ディーラー';

    public function __construct(private Rule $rule)
    {
        parent::__construct($rule);
    }

    public function hitOrStand(Deck $deck): int
    {
        $this->rule->dealerHitOrStand($this, $deck);
        return $this->point;
    }
}
