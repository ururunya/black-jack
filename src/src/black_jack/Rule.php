<?php

namespace BlackJack;

require_once 'Message.php';

abstract class Rule
{
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

    public function dealerHitOrStand(Actor $dealer, Deck $deck): int
    {
        $hand = $dealer->getHand();
        $dealer->point = $this->pointCalc($hand);
        Message::dealerSecondCardOpen($hand[1]);
        Message::pointDisplay($dealer);

        while ($dealer->point < 17) {
            $this->hitCardHandle($dealer, $deck);
        }
        return $dealer->point;
    }

    public function ComPlayerHitOrStand(Actor $comPlayer, Deck $deck) {
        $hand = $comPlayer->getHand();
        $comPlayer->point = $this->pointCalc($hand);
        Message::pointDisplay($comPlayer);

        while ($comPlayer->point < 17) {
            $this->hitCardHandle($comPlayer, $deck);
        }
        return $comPlayer->point;
    }

    abstract public function playerHitOrStand(Actor $player, Deck $deck);

    public function pointCalc(array $hand)
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

    protected function hitCardHandle(Actor $actor, Deck $deck)
    {
        $hand = $actor->getHand();
        // hitの処理
        $hitCard = $deck->hitCard();
        Message::hitMessage($actor, $hitCard[0]);
        // ポイントの再計算
        $hand = array_merge($hand, $hitCard);
        $actor->point = $this->pointCalc($hand);
        // handの更新
        $actor->setHand($hand);
        // メッセージの表示
        Message::pointDisplay($actor);
    }

    protected function bustCheck(int $point)
    {
        return $point > 21;
    }

    public function whichWinPlayerOrDealer(array $actors): void
    {
        $player = $actors[0];
        $dealer = array_pop($actors);
        $comPlayers = array_slice($actors, 1);
        $allPlayers = [$player, ...$comPlayers];

        foreach ($allPlayers as $player) {
            $this->winCheck($player, $dealer);
        }

    }

    private function winCheck(Actor $player, Actor $dealer): void
    {
        if ($this->bustCheck($player->point) && $this->bustCheck($dealer->point)) {
            Message::drawComment($player, $dealer);
        } elseif ($this->bustCheck($player->point) && !$this->bustCheck($dealer->point)) {
            Message::loseComment($player);
        } elseif (!$this->bustCheck($player->point) && $this->bustCheck($dealer->point)) {
            Message::winComment($player);
        } else {
            if ($player->point === $dealer->point) {
                Message::drawComment($player, $dealer);
            } elseif ($player->point > $dealer->point) {
                Message::winComment($player);
            } else {
                Message::loseComment($player);
            }
        }
    }
}
