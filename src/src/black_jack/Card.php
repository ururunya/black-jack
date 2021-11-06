<?php

namespace BlackJack;

class Card
{
    public function __construct(public string $suit, public string $number)
    {
    }
}
