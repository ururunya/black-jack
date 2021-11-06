<?php

namespace BlackJack\Rules;

require_once 'Rule.php';
require_once __DIR__ . '/../Message.php';

use BlackJack\Actors\Actor;
use BlackJack\Cards\Deck;
use BlackJack\Message;

class SimpleRule extends Rule
{
    public function playerHitOrStand(Actor $player, Deck $deck): void
    {
        $hand = $player->getHand();
        $player->point = $this->pointCalc($hand);
        Message::pointDisplay($player);

        while (true) {
            // hitするかどうか
            if (!Message::QuestionAndReply()) {
                return;
            }

            $this->hitCardHandle($player, $deck);

            if ($this->bustCheck($player->point)) {
                return;
            }
        }
        return;
    }

}
