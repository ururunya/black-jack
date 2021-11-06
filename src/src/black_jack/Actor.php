<?php

namespace BlackJack;

abstract class Actor
{
    protected array $hand;
    public string $name;
    public int $point;

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

    public function dealCards(Deck $deck): array
    {
        $this->hand = $deck->dealCards();
        return $this->hand;
    }

    abstract public function hitOrStand(Deck $deck): int;
}
