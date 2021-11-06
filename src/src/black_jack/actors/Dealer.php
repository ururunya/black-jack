<?php

namespace BlackJack\Actors;

use BlackJack\Rules\Rule;
use BlackJack\Cards\Deck;

class Dealer extends Actor
{
    public string $name = 'ディーラー';

    public function __construct(private Rule $rule)
    {
        parent::__construct($rule);
    }

    public function hitOrStand(Deck $deck): void
    {
        $this->rule->dealerHitOrStand($this, $deck);
    }
}
