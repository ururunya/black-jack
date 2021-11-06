<?php

namespace Test;

require_once __DIR__ . '/../src/black_jack/Deck.php';

use PHPUnit\Framework\TestCase;
use BlackJack\Deck;

class DeckTest extends TestCase
{

    public function testDealCards(): void
    {
        $game = new Deck();
        $this->assertSame(2, count($game->dealCards()));
    }
}
