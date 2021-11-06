<?php

namespace BlackJack;

require_once 'Rule.php';
require_once 'Message.php';

class SimpleRule extends Rule
{
    public function playerHitOrStand(Actor $player, Deck $deck): int
    {
        $hand = $player->getHand();
        $player->point = $this->pointCalc($hand);
        Message::pointDisplay($player);

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

}
