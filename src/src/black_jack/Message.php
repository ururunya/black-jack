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

    /**
 * @param array<Actor> $actors
 */
    public function startMessage(array $actors): void
    {
        echo 'ブラックジャックを開始します。' . PHP_EOL;
        foreach ($actors as $actor) {
            foreach ($actor->getHand() as $card) {
                $suit = self::SUIT_NAME[$card->suit];
                echo "{$actor->name}の引いたカードは{$suit}の{$card->number}です。" . PHP_EOL;
                if ($actor::class === 'BlackJack\Actors\Dealer') {
                    echo 'ディーラーの引いた2枚目のカードはわかりません。' . PHP_EOL;
                    break;
                }
            }
        }
    }

    public function hitMessage(Actor $actor, Card $card): void
    {
        $suit = self::SUIT_NAME[$card->suit];
        echo "{$actor->name}の引いたカードは{$suit}の{$card->number}です。" . PHP_EOL;
    }

    public function displayPoint(Actor $actor): void
    {
        echo "{$actor->name}の現在の得点は{$actor->point}です。" . PHP_EOL;
    }

    public function dealerSecondCardOpen(Card $card): void
    {
        $suit = self::SUIT_NAME[$card->suit];
        echo "ディーラーの引いた2枚目のカードは{$suit}の{$card->number}でした。" . PHP_EOL;
    }

    public function commentJudgement(Actor $player, Actor $dealer, string $judge): void
    {
        if ($judge === 'draw') {
            $this->commentDraw($player, $dealer);
        } elseif ($judge === 'win') {
            $this->commentWin($player);
        } elseif ($judge === 'lose') {
            $this->commentLose($player);
        }
    }

    private function commentDraw(Actor $player, Actor $dealer): void
    {
        echo "{$player->name}と{$dealer->name}は引き分けです。" . PHP_EOL;
    }

    private function commentWin(Actor $player): void
    {
        echo "{$player->name}の勝ちです！" . PHP_EOL;
    }

    private function commentLose(Actor $player): void
    {
        echo "残念！{$player->name}の負けです。" . PHP_EOL;
    }

    public function questionChoice(string $action): bool
    {
        if ($action === 'hit') {
            return $this->questionDraw();
        } elseif ($action === 'double down') {
            return $this->questionDoubleDown();
        } elseif ($action === 'surrender') {
            return $this->questionSurrender();
        } elseif ($action === 'split') {
            return $this->questionSplit();
        }
        return true;
    }

    private function questionDraw(): bool
    {
        $reply = readline('カードを引きますか？（Y/N）');
        if ($reply === 'N' || $reply === 'n') {
            return false;
        } elseif ($reply === 'Y' || $reply === 'y') {
            return true;
        }
        echo 'Y/yかN/nを入力してください。';
        return $this->questionDraw();
    }

    private function questionDoubleDown(): bool
    {
        $reply = readline('ダブルダウンしますか？（Y/N）');
        if ($reply === 'Y' || $reply === 'y') {
            return true;
        } elseif ($reply === 'N' || $reply === 'n') {
            return false;
        }
        echo 'Y/yかN/nを入力してください。';
        return $this->questionDoubleDown();
    }

    private function questionSplit(): bool
    {
        $reply = readline('スプリットしますか？（Y/N）');
        if ($reply === 'Y' || $reply === 'y') {
            return true;
        } elseif ($reply === 'N' || $reply === 'n') {
            return false;
        }
        echo 'Y/yかN/nを入力してください。';
        return $this->questionSplit();
    }

    private function questionSurrender(): bool
    {
        $reply = readline('サレンダーしますか？（Y/N）');
        if ($reply === 'Y' || $reply === 'y') {
            return true;
        } elseif ($reply === 'N' || $reply === 'n') {
            return false;
        }
        echo 'Y/yかN/nを入力してください。';
        return $this->questionSurrender();
    }

    /**
     * @param array<Card> $hand
     */
    public function doSplit(array $hand): void
    {
        $card1Name = self::SUIT_NAME[$hand[0]->suit];
        $card2Name = self::SUIT_NAME[$hand[1]->suit];

        echo "ハンドを{$card1Name}の{$hand[0]->number}と{$card2Name}の{$hand[1]->number}にスプリットします。" . PHP_EOL;
    }

    public function surrenderComment(Actor $player): void
    {
        echo "{$player->name}はゲームを降りました。" . PHP_EOL;
    }
}
