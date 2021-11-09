<?php

namespace BlackJack\Rules;

require_once __DIR__ . '/../Message.php';

use BlackJack\Actors\Actor;
use BlackJack\Cards\Deck;
use BlackJack\Message;

abstract class Rule
{
    protected Message $message;
    protected const CARD_POINT = [
        'A' => 11,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        'J' => 10,
        'Q' => 10,
        'K' => 10,
    ];
    protected const ACE_SMALL_POINT = 1;
    protected const ACE_POINT_DIFFERENCE = 10;
    protected const DRAW = 'draw';
    protected const WIN = 'win';
    protected const LOSE = 'lose';
    protected const DOUBLE_DOWN = 'double down';
    protected const SURRENDER = 'surrender';
    protected const SPLIT = 'split';
    protected const HIT = 'hit';

    public function __construct()
    {
        $this->message = $this->message = new Message();
    }

    public function dealerHitOrStand(Actor $dealer, Deck $deck): void
    {
        $hand = $dealer->getHand();
        $dealer->point = $this->calcPoint($hand);
        $this->message->dealerSecondCardOpen($hand[1]);
        $this->message->displayPoint($dealer);

        while ($dealer->point < 17) {
            $this->hitCardHandle($dealer, $deck);
        }
        return;
    }

    public function comPlayerHitOrStand(Actor $comPlayer, Deck $deck): void
    {
        $hand = $comPlayer->getHand();
        $comPlayer->point = $this->calcPoint($hand);
        $this->message->displayPoint($comPlayer);

        while ($comPlayer->point < 17) {
            $this->hitCardHandle($comPlayer, $deck);
        }
        return;
    }

    abstract public function playerHitOrStand(Actor $player, Deck $deck): void;

    public function calcPoint(array $hand): int
    {
        $numbers = [];
        $points = [];
        foreach ($hand as $card) {
            $numbers[] = $card->number;
            $points[] = static::CARD_POINT[$card->number];
        }

        $sum = array_sum($points);

        $numberFrequency = array_count_values($numbers);

        if ($this->bustCheck($sum) && array_key_exists('A', $numberFrequency)) {
            for ($i = 0; $i < $numberFrequency['A']; $i++) {
                $sum -= static::ACE_POINT_DIFFERENCE;
                if (!$this->bustCheck($sum)) {
                    return $sum;
                }
            }
        }

        return $sum;
    }

    protected function hitCardHandle(Actor $actor, Deck $deck): void
    {
        $hand = $actor->getHand();
        // hitの処理
        $hitCard = $deck->hitCard();
        $this->message->hitMessage($actor, $hitCard[0]);
        // ポイントの再計算
        $hand = array_merge($hand, $hitCard);
        $actor->point = $this->calcPoint($hand);
        // handの更新
        $actor->setHand($hand);
        // メッセージの表示
        $this->message->displayPoint($actor);
    }

    protected function bustCheck(int $point): bool
    {
        return $point > 21;
    }

    public function whichWinPlayerOrDealer(array $actors): int
    {
        // 各Actorの勝敗判定のためにPlayerの配列作成
        $player = $actors[0];
        $dealer = array_pop($actors);
        $comPlayers = array_slice($actors, 1);
        $allPlayers = [$player, ...$comPlayers];
        $playerWinChips = 0;

        // $playerが$splitPlayerを持っている場合の$allPlayers
        if (!empty($player->splitPlayers)) {
            $allPlayers = [...$player->splitPlayers, ...$comPlayers];
        }

        foreach ($allPlayers as $player) {
            // Player または SplitPlayer の時のみ、獲得チップを計算
            if ($player::class === 'BlackJack\Actors\Player' || $player::class === 'BlackJack\Actors\SplitPlayer') {
                $playerWinChips += $this->winCheck($player, $dealer);
                continue;
            }
            $this->winCheck($player, $dealer);
        }

        return $playerWinChips;
    }

    private function winCheck(Actor $player, Actor $dealer): int
    {
        // サレンダーしたときは、ベットを半分返す
        if ($player->surrender) {
            $this->message->surrenderComment($player);
            return $player->bet / 2;
        }

        return $this->winCheckByBust($player, $dealer);
    }

    // bustによる勝敗評価。どちらもbustしていない時のみ、pointによる勝敗評価
    private function winCheckByBust(Actor $player, Actor $dealer): int
    {
        if ($this->bustCheck($player->point) && $this->bustCheck($dealer->point)) {
            $this->message->commentJudgement($player, $dealer, static::DRAW);
            return $player->bet;
        } elseif ($this->bustCheck($player->point) && !$this->bustCheck($dealer->point)) {
            $this->message->commentJudgement($player, $dealer, static::LOSE);
            return 0;
        } elseif (!$this->bustCheck($player->point) && $this->bustCheck($dealer->point)) {
            $this->message->commentJudgement($player, $dealer, static::WIN);
            return $player->bet * 2;
        } elseif (!$this->bustCheck($player->point) && !$this->bustCheck($dealer->point)) {
            return $this->winCheckByPoint($player, $dealer);
        }
    }

    // pointによる勝敗評価
    private function winCheckByPoint(Actor $player, Actor $dealer): int
    {
        if ($player->point === $dealer->point) {
            $this->message->commentJudgement($player, $dealer, static::DRAW);
            return $player->bet;
        } elseif ($player->point > $dealer->point) {
            $this->message->commentJudgement($player, $dealer, static::WIN);
            return $player->bet * 2;
        } elseif ($player->point < $dealer->point) {
            $this->message->commentJudgement($player, $dealer, static::LOSE);
            return 0;
        }
    }
}
