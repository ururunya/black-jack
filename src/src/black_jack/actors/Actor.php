<?php

namespace BlackJack\Actors;

use BlackJack\Rules\Rule;
use BlackJack\Cards\Deck;

abstract class Actor
{
    protected array $hand;
    public string $name;
    public int $point;
    public bool $surrender = false;
    public array $splitPlayers;

    public function __construct(private Rule $rule)
    {
    }

    public function setHand(array $hand): void
    {
        $this->hand = $hand;
    }

    public function getHand(): array
    {
        return $this->hand;
    }

    public function dealCards(Deck $deck): void
    {
        $this->hand = $deck->dealCards();
    }

    abstract public function hitOrStand(Deck $deck): void;
}
