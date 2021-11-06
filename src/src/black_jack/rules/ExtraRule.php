<?php

namespace BlackJack\Rules;

require_once 'Rule.php';
require_once __DIR__ . '/../Message.php';
require_once __DIR__ . '/../actors/SplitPlayer.php';

use BlackJack\Actors\Actor;
use BlackJack\Actors\Player;
use BlackJack\Actors\SplitPlayer;
use BlackJack\Cards\Deck;
use BlackJack\Message;

class ExtraRule extends Rule
{
    public function playerHitOrStand(Actor $player, Deck $deck)
    {
        $hand = $player->getHand();
        $player->point = $this->pointCalc($hand);
        Message::pointDisplay($player);
        // ダブルダウンするか
        if (Message::questionDoubleDown()) {
            $this->doubleDown($player, $deck);
            return $player->point;
        }

        if ($hand[0]->number === $hand[1]->number) {
            if (Message::questionSplit()) {
                $this->doSplit($player, $deck);
                return $player->point;
            }
        }

        while (true) {
            // hitするかどうか
            if (!Message::QuestionAndReply()) {
                return $player->point;
            }

            $this->hitCardHandle($player, $deck);

            if ($this->bustCheck($player->point)) {
                return $player->point;
            }
        }
        return $player->point;
    }

    private function doubleDown(Player $player, Deck $deck) {
        $this->hitCardHandle($player, $deck);
    }

    private function doSplit(Player $player, Deck $deck) {
        $hand = $player->getHand();
        Message::doSplit($hand);
        $splitHands = [array_merge([$hand[0]], $deck->hitCard()), array_merge([$hand[1]], $deck->hitCard())];

        $splitPlayers = [new SplitPlayer($this), new SplitPlayer($this)];

        foreach ($splitPlayers as $index=>$splitPlayer) {
            $splitPlayer->setHand($splitHands[$index]);
            $number = $index + 1;
            $splitPlayer->name = "スプリットハンド{$number}";
        }

        foreach ($splitPlayers as $splitPlayer) {
            $card = $splitPlayer->getHand()[1];
            Message::hitMessage($splitPlayer, $card);
        }

        $player->splitPlayers = $splitPlayers;

        if ($hand[0]->number === 'A' && $hand[1]->number === 'A') {
            return;
        }
        foreach ($splitPlayers as $splitPlayer) {
            $this->playerHitOrStand($splitPlayer, $deck);
        }
    }
}
