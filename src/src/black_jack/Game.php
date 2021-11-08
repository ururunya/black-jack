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
use BlackJack\Rules\Rule;

class Game
{
    private Message $message;

    public function __construct()
    {
        $this->message = new Message();
    }

    public function start(): void
    {
        $numberOfPlayer = (int) readline('あなたを含めたプレイヤーの数を入力してください。（1～3）');
        $numberOfPlayer = $numberOfPlayer > 3 ? 3 : $numberOfPlayer;

        $ruleName = readline('使用するルール番号を入力してください。（1: シンプルルール、2: エキストラルール）');
        $rule = $this->getRule($ruleName);

        $deck = new Deck();

        $actors = [];
        $comPlayers = [];

        $player = new Player($rule);
        $dealer = new Dealer($rule);



        if ($numberOfPlayer > 1) {
            for ($i = 1; $i < $numberOfPlayer; $i++) {
                $comPlayer = new ComPlayer($rule);
                $comPlayer->name = "comPlayer{$i}";
                $comPlayers[] = $comPlayer;
            }
        }

        $actors = [$player, ...$comPlayers, $dealer];

        foreach ($actors as $actor) {
            $actor->dealCards($deck);
        }

        $this->message->startMessage($actors);

        foreach ($actors as $actor) {
            $actor->hitOrStand($deck);
        }

        $rule->whichWinPlayerOrDealer($actors);
        echo 'ブラックジャックを終了します。' . PHP_EOL;
    }

    private function getRule(string $ruleName): Rule
    {
        if ($ruleName === '1') {
            return new SimpleRule();
        } elseif ($ruleName === '2') {
            return new ExtraRule();
        }
        echo '「1」か「2」を入力してください。' . PHP_EOL;
        return $this->getRule($ruleName);
    }
}
