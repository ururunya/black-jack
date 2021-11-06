<?php

namespace BlackJack\Cards;

class Card
{
    public function __construct(public string $suit, public string $number)
    {
    }
}
