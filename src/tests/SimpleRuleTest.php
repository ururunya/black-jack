<?php

namespace Test;

require_once __DIR__ . '/../src/black_jack/rules/SimpleRule.php';
require_once __DIR__ . '/../src/black_jack/cards/Deck.php';
require_once __DIR__ . '/../src/black_jack/actors/Player.php';
require_once __DIR__ . '/../src/black_jack/actors/Dealer.php';
require_once __DIR__ . '/../src/black_jack/actors/ComPlayer.php';
require_once __DIR__ . '/../src/black_jack/Message.php';
require_once __DIR__ . '/../src/black_jack/cards/Card.php';

use PHPUnit\Framework\TestCase;
use BlackJack\Cards\Deck;
use BlackJack\Actors\Player;
use BlackJack\Actors\Dealer;
use BlackJack\Actors\ComPlayer;
use BlackJack\Message;
use BlackJack\Cards\Card;
use BlackJack\Rules\SimpleRule;

class SimpleRuleTest extends TestCase
{
    protected SimpleRule $rule;
    protected Deck $deck;
    protected Player $player;
    protected Dealer $dealer;
    protected ComPlayer $comPlayer;
    protected Message $message;

    protected function setUp(): void
    {

        $this->rule = new SimpleRule();
        $this->deck = new Deck();
        $this->message = new Message();

        $this->player = new Player($this->rule);
        $this->dealer = new Dealer($this->rule);
        $this->comPlayer = new ComPlayer($this->rule);

        $this->player->dealCards($this->deck);
        $this->dealer->dealCards($this->deck);
        $this->comPlayer->dealCards($this->deck);

        $this->rule->playerHitOrStand($this->player, $this->deck);
        $this->rule->dealerHitOrStand($this->dealer, $this->deck);
        $this->rule->comPlayerHitOrStand($this->comPlayer, $this->deck);
        // $actors = [$player, $comPlayers, $dealer];

        // foreach ($actors as $actor) {
        //     $actor->dealCards($this->deck);
        // }
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(Message::class, $this->message);
        $this->assertInstanceOf(Card::class, $this->player->getHand()[0]);
        $this->assertInstanceOf(Card::class, $this->dealer->getHand()[0]);
        $this->assertInstanceOf(Card::class, $this->comPlayer->getHand()[0]);
    }

    public function testFailure(): void
    {
        $this->assertIsInt($this->player->point);
        $this->assertIsInt($this->dealer->point);
        $this->assertIsInt($this->comPlayer->point);
    }
}
