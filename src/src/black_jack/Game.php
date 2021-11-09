<?php

namespace BlackJack;

require_once 'cards/Deck.php';
require_once 'actors/Player.php';
require_once 'actors/ComPlayer.php';
require_once 'actors/Dealer.php';
require_once 'rules/SimpleRule.php';
require_once 'rules/ExtraRule.php';
require_once 'Message.php';

use BlackJack\Actors\Actor;
use BlackJack\Actors\Player;
use BlackJack\Actors\Dealer;
use BlackJack\Actors\ComPlayer;
use BlackJack\Cards\Deck;
use BlackJack\Message;
use BlackJack\Rules\SimpleRule;
use BlackJack\Rules\ExtraRule;
use BlackJack\Rules\Rule;

class Game
{
    private Message $message;
    private int $casinoChips = 100;
    private const CONTINUE = 'continue';

    public function __construct()
    {
        $this->message = new Message();
    }

    public function start(): void
    {
        $numberOfPlayer = $this->settingNumberOfPlayer();
        $ruleName = $this->settingRule();
        $rule = $this->getRule($ruleName);
        echo "現在、チップを{$this->casinoChips}枚もっています。" . PHP_EOL;


        $deck = new Deck();

        $actors = [];
        $comPlayers = [];

        $player = new Player($rule);
        $dealer = new Dealer($rule);


        if ($numberOfPlayer > 1) {
            for ($i = 1; $i < $numberOfPlayer; $i++) {
                $comPlayer = new ComPlayer($rule);
                $comPlayer->name = "comPlayer{$i}";
                $comPlayers[] = $comPlayer;
            }
        }

        $actors = [$player, ...$comPlayers, $dealer];

        while (true) {
            $this->play($actors, $deck, $rule);
            // チップが0以下になっていたら強制終了
            if ($this->casinoChips <= 0) {
                echo '破産しました。これ以上ゲームに参加できません。' . PHP_EOL;
                break;
            }

            $reply = $this->message->questionChoice(static::CONTINUE);
            if (!$reply) {
                break;
            }
        }

        echo 'ブラックジャックを終了します。' . PHP_EOL;
    }

    /**
     * @param array<Actor> $actors
     */
    private function play(array $actors, Deck $deck, Rule $rule): void
    {
        $bet = $this->message->howManyBet($this->casinoChips);
        $player = $actors[0];
        $player->bet = $bet;

        foreach ($actors as $actor) {
            $actor->dealCards($deck);
        }

        $this->message->startMessage($actors);

        foreach ($actors as $actor) {
            $actor->hitOrStand($deck);
        }

        // extraルールを適用していた場合に対応して後でベット数分減らす
        $newPlayerBet = $player->bet;
        // スプリットしていた場合のベット数修正
        if (!empty($player->splitPlayers)) {
            $newPlayerBet = $this->splitPlayersBet($player);
        }
        $this->casinoChips -= $newPlayerBet;

        // 勝敗結果によって返ってくるチップ数
        $getChips = $rule->whichWinPlayerOrDealer($actors);
        $this->casinoChips += $getChips;

        // Playerインスタンスの$betと$surrenderと$splitPlayersを初期化
        $player->bet = 0;
        $player->surrender = false;
        $player->splitPlayers = [];

        echo "所持しているチップは{$this->casinoChips}枚になりました。" . PHP_EOL;
    }

    private function settingNumberOfPlayer(): int
    {
        $numberOfPlayer = (int) readline('あなたを含めたプレイヤーの数を入力してください。（1～3）');

        if ($numberOfPlayer === 0) {
            echo '1から3の数字を入力してください。' . PHP_EOL;
            return $this->settingNumberOfPlayer();
        }

        return $numberOfPlayer > 3 ? 3 : $numberOfPlayer;
    }

    private function settingRule(): int
    {
        $rule = (int) readline('使用するルール番号を入力してください。（1: シンプルルール、2: エキストラルール）');

        if ($rule === 0) {
            echo '「1」か「2」の数字を入力してください。' . PHP_EOL;
            return $this->settingRule();
        }

        return $rule;
    }

    // スプリットしたカードでさらにextraルールを適用したケースに備え再帰的に計算
    private function splitPlayersBet(Actor $actor): int
    {
        $splitPlayers = $actor->splitPlayers;
        $betSum = 0;

        foreach ($splitPlayers as $splitPlayer) {
            $betSum += $splitPlayer->bet;
            if (!empty($splitPlayer->splitPlayers)) {
                $betSum += $this->splitPlayersBet($splitPlayer);
            }
        }

        return $betSum;
    }

    private function getRule(string $ruleName): Rule
    {
        if ($ruleName === '1') {
            return new SimpleRule();
        } elseif ($ruleName === '2') {
            return new ExtraRule();
        }
        echo '「1」か「2」を入力してください。' . PHP_EOL;
        return $this->getRule($ruleName);
    }
}
