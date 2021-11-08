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
        $player->point = $this->calcPoint($hand);
        $this->message->displayPoint($player);

        while (true) {
            // hitするかどうか
            if (!$this->message->questionChoice(static::HIT)) {
                break;
            }

            $this->hitCardHandle($player, $deck);

            if ($this->bustCheck($player->point)) {
                break;
            }
        }
    }
}
