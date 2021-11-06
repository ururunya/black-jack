<?php

namespace BlackJack;

use BlackJack\Actors\Actor;
use BlackJack\Cards\Card;

class Message
{
    private const SUIT_NAME = [
        'S' => 'スペード',
        'H' => 'ハート',
        'C' => 'クラブ',
        'D' => 'ダイヤ'
    ];

    public static function startMessage(array $actors): void
    {
        echo 'ブラックジャックを開始します。' . PHP_EOL;
        foreach ($actors as $actor) {
            foreach ($actor->getHand() as $card) {
                $suit = static::SUIT_NAME[$card->suit];
                echo "{$actor->name}の引いたカードは{$suit}の{$card->number}です。" . PHP_EOL;
                if ($actor::class === 'BlackJack\Actors\Dealer') {
                    echo 'ディーラーの引いた2枚目のカードはわかりません。' . PHP_EOL;
                    break;
                }
            }
        }
    }

    public static function hitMessage(Actor $actor, Card $card): void
    {
        $suit = static::SUIT_NAME[$card->suit];
        echo "{$actor->name}の引いたカードは{$suit}の{$card->number}です。" . PHP_EOL;
    }

    public static function pointDisplay(Actor $actor): void
    {
        echo "{$actor->name}の現在の得点は{$actor->point}です。" . PHP_EOL;
    }

    public static function actorsPointDisplay(array $actors): void
    {
        foreach ($actors as $actor) {
            echo "{$actor->name}の得点は{$actor->point}です。" . PHP_EOL;
        }
    }

    public static function dealerSecondCardOpen(Card $card): void
    {
        $suit = static::SUIT_NAME[$card->suit];
        echo "ディーラーの引いた2枚目のカードは{$suit}の{$card->number}でした。" . PHP_EOL;
    }

    public static function drawComment(Actor $player, Actor $dealer)
    {
        echo "{$player->name}と{$dealer->name}は引き分けです。" . PHP_EOL;
    }

    public static function winComment(Actor $player)
    {
        echo "{$player->name}の勝ちです！" . PHP_EOL;
    }

    public static function loseComment(Actor $player)
    {
        echo "残念！{$player->name}の負けです。" . PHP_EOL;
    }

    public static function QuestionAndReply(): bool
    {
        $reply = readline('カードを引きますか？（Y/N）');
        if ($reply === 'N' || $reply === 'n') {
            return false;
        } elseif ($reply === 'Y' || $reply === 'y') {
            return true;
        } else {
            echo 'Y/yかN/nを入力してください。';
            return static::QuestionAndReply();
        }
    }

    public static function questionDoubleDown(): bool
    {
        $reply = readline('ダブルダウンしますか？（Y/N）');
        if ($reply === 'Y' || $reply === 'y') {
            return true;
        } elseif ($reply === 'N' || $reply === 'n') {
            return false;
        } else {
            echo 'Y/yかN/nを入力してください。';
            return static::questionDoubleDown();
        }
    }

    public static function questionSplit()
    {
        $reply = readline('スプリットしますか？（Y/N）');
        if ($reply === 'Y' || $reply === 'y') {
            return true;
        } elseif ($reply === 'N' || $reply === 'n') {
            return false;
        } else {
            echo 'Y/yかN/nを入力してください。';
            static::questionSplit();
        }
    }

    public static function doSplit(array $hand)
    {
        $card1Name = static::SUIT_NAME[$hand[0]->suit];
        $card2Name = static::SUIT_NAME[$hand[1]->suit];

        echo "ハンドを{$card1Name}の{$hand[0]->number}と{$card2Name}の{$hand[1]->number}にスプリットします。" . PHP_EOL;
    }
}
