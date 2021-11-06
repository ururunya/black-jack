<?php

namespace BlackJack;

require_once 'cards/Deck.php';
require_once 'actors/Player.php';
require_once 'actors/ComPlayer.php';
require_once 'actors/Dealer.php';
require_once 'rules/SimpleRule.php';
require_once 'rules/ExtraRule.php';
require_once 'Message.php';

use BlackJack\Actors\Player;
use BlackJack\Actors\Dealer;
use BlackJack\Actors\ComPlayer;
use BlackJack\Cards\Deck;
use BlackJack\Message;
use BlackJack\Rules\SimpleRule;
use BlackJack\Rules\ExtraRule;

class Game
{
    public function __construct(private int $numberOfPlayer, private string $ruleName)
    {
    }

    public function start()
    {
        $deck = new Deck();
        $rule = $this->getRule($this->ruleName);
        $actors = [];
        $comPlayers = [];

        $player = new Player($rule);
        $dealer = new Dealer($rule);

        $this->numberOfPlayer = $this->numberOfPlayer > 3 ? 3 : $this->numberOfPlayer;

        if ($this->numberOfPlayer > 1) {
            for ($i = 1; $i < $this->numberOfPlayer; $i++) {
                $comPlayer = new ComPlayer($rule);
                $comPlayer->name = "comPlayer{$i}";
                $comPlayers[] = $comPlayer;
            }
        }

        $actors = [$player, ...$comPlayers, $dealer];

        foreach ($actors as $actor) {
            $actor->dealCards($deck);
        }

        Message::startMessage($actors);

        foreach ($actors as $actor) {
            $actor->hitOrStand($deck);
        }

        $rule->whichWinPlayerOrDealer($actors);
        echo 'ブラックジャックを終了します。';
    }

    private function getRule(string $ruleName)
    {
        if ($ruleName === 'simple') {
            return new SimpleRule();
        } elseif ($ruleName === 'extra') {
            return new ExtraRule();
        } else {
            exit("「simple」か「extra」を入力してください。\n");
        }
    }

}
