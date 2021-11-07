<?php

namespace BlackJack\Actors;

require_once 'Actor.php';

use BlackJack\Rules\Rule;
use BlackJack\Cards\Deck;

class Player extends Actor
{
    public string $name = 'あなた';

    public function __construct(private Rule $rule)
    {
        parent::__construct($rule);
    }

    public function hitOrStand(Deck $deck): void
    {
        $this->rule->playerHitOrStand($this, $deck);
    }
}
