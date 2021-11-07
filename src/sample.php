<?php

require_once 'src/black_jack/Game.php';

use BlackJack\Game;

$game = new Game(2, 'extra');
$game->start();
