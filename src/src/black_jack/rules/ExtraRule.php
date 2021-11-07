<?php

namespace BlackJack\Rules;

require_once 'Rule.php';
require_once __DIR__ . '/../Message.php';
require_once __DIR__ . '/../actors/SplitPlayer.php';

use BlackJack\Actors\Actor;
use BlackJack\Actors\SplitPlayer;
use BlackJack\Cards\Deck;

class ExtraRule extends Rule
{
    public function playerHitOrStand(Actor $player, Deck $deck): void
    {
        $hand = $player->getHand();
        $player->point = $this->calcPoint($hand);
        $this->message->displayPoint($player);
        // ダブルダウンするか
        if ($this->message->questionChoice(static::DOUBLE_DOWN)) {
            $this->doubleDown($player, $deck);
            return;
        }
        // ペアカードの場合、スプリットするか
        if ($hand[0]->number === $hand[1]->number) {
            if ($this->message->questionChoice(static::SPLIT)) {
                $this->doSplit($player, $deck);
                return;
            }
        }
        // サレンダー（レイトサレンダー）するか
        if ($this->message->questionChoice(static::SURRENDER)) {
            $player->surrender = true;
            return;
        }

        while (true) {
            // hitするかどうか
            if (!$this->message->questionChoice(static::HIT)) {
                return;
            }

            $this->hitCardHandle($player, $deck);

            if ($this->bustCheck($player->point)) {
                return;
            }
        }
    }

    private function doubleDown(Actor $player, Deck $deck): void
    {
        $this->hitCardHandle($player, $deck);
    }

    private function doSplit(Actor $player, Deck $deck): void
    {
        $hand = $player->getHand();
        $this->message->doSplit($hand);
        $splitHands = [array_merge([$hand[0]], $deck->hitCard()), array_merge([$hand[1]], $deck->hitCard())];

        $splitPlayers = [new SplitPlayer($this), new SplitPlayer($this)];

        foreach ($splitPlayers as $index => $splitPlayer) {
            $splitPlayer->setHand($splitHands[$index]);
            $number = $index + 1;
            $splitPlayer->name = "スプリットハンド{$number}";
        }

        foreach ($splitPlayers as $splitPlayer) {
            $card = $splitPlayer->getHand()[1];
            $this->message->hitMessage($splitPlayer, $card);
        }

        $player->splitPlayers = $splitPlayers;
        // Aのペアの場合、それぞれ1枚しか引くことができないルールなので終了
        if ($hand[0]->number === 'A' && $hand[1]->number === 'A') {
            return;
        }
        foreach ($splitPlayers as $splitPlayer) {
            $this->playerHitOrStand($splitPlayer, $deck);
        }
    }
}
