<?php

namespace BlackJack;

require_once 'Actor.php';

class Player extends Actor
{
    public string $name = 'あなた';

    public function __construct(private Rule $rule)
    {
        parent::__construct($rule);
    }

    public function hitOrStand(Deck $deck): int
    {
        $this->point = $this->rule->playerHitOrStand($this, $deck);
        return $this->point;
    }
}
