<?php

namespace BlackJack\Cards;

require_once 'Card.php';

class Deck
{
    private const SUIT = ['S', 'H', 'C', 'D'];
    private const NUMBER = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
    private const CARD_NUM = 2;
    private array $deck;

    public function __construct()
    {
        foreach (self::SUIT as $suit) {
            foreach (self::NUMBER as $number) {
                $this->deck[] = new Card($suit, $number);
            }
        }
    }

    public function dealCards(): array
    {
        shuffle($this->deck);
        return array_splice($this->deck, 0, self::CARD_NUM);
    }

    public function hitCard(): array
    {
        shuffle($this->deck);
        return array_splice($this->deck, 0, 1);
    }
}
