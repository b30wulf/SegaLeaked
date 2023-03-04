<?php

namespace SegaCore\Core\game;

use pocketmine\player\Player;

class Game{

    /** @var bool[] */
    public static $bed = [];

    /** @var int[] */
    public static $bedpos = ["blue" => [["x" => 1, "y" => 1, "z" => 1], ["x" => 1, "y" => 1, "z" => 1]], "red" => [["x" => 1, "y" => 1, "z" => 1], ["x" => 1, "y" => 1, "z" => 1]]];

    /** @var array */
    public static $team = [];

    /** @var Player[] */
    public static $enemy = [];
}
