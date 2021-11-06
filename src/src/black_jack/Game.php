<?php

namespace BlackJack;

require_once 'Deck.php';
require_once 'Player.php';
require_once 'ComPlayer.php';
require_once 'Dealer.php';
require_once 'SimpleRule.php';
require_once 'ExtraRule.php';
require_once 'Message.php';

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
