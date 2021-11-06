<?php

namespace Test;

require_once __DIR__ . '/../src/black_jack/SimpleRule.php';
require_once __DIR__ . '/../src/black_jack/Deck.php';
require_once __DIR__ . '/../src/black_jack/Player.php';
require_once __DIR__ . '/../src/black_jack/Message.php';
require_once __DIR__ . '/../src/black_jack/Card.php';

use PHPUnit\Framework\TestCase;
use BlackJack\SimpleRule;
use BlackJack\Deck;
use BlackJack\Player;
use BlackJack\Message;
use BlackJack\Card;

class SimpleRuleTest extends TestCase
{
    public function testPlayerHitOrStand()
    {
        $deck = new Deck();
        $rule = new SimpleRule();
        $player = new Player($rule);
        $player->dealCards($deck);
        $message = new Message();
        $this->assertSame(true, is_int($rule->playerHitOrStand($player, $deck, $message)));
    }

    public function testPointCalc()
    {
        $rule = new SimpleRule();
        $hand = [new Card('S', 'A'), new Card('H', 'A'), new Card('C', 'A'), new Card('D', 'A'), new Card('C', '7')];
        $this->assertSame(21, $rule->pointCalc($hand));
    }
}
