<?php

namespace BlackJack;

require_once 'Rule.php';
require_once 'Message.php';

class ExtraRule extends Rule
{
    public function playerHitOrStand(Actor $player, Deck $deck): int
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

    }
}
