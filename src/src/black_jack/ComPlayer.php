<?php

namespace BlackJack;

require_once 'Actor.php';

class ComPlayer extends Actor
{
    public string $name = '';

    public function __construct(private Rule $rule)
    {
        parent::__construct($rule);
    }

    public function hitOrStand(Deck $deck): int
    {
        $this->point = $this->rule->ComPlayerHitOrStand($this, $deck);
        return $this->point;
    }
}
