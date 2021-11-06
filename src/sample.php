<?php

require_once 'src/black_jack/Game.php';

use BlackJack\Game;

$game = new Game(3, 'extra');
$game->start();
