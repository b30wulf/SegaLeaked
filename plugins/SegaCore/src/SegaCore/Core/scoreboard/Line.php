<?php

namespace SegaCore\Core\scoreboard;

class Line {

  private int $score;
  private string $text;

  public function __construct(int $score, string $text) {
      $this->score = $score;
      $this->text = $text;
  }

  public function getScore(): int {
      return $this->score;
  }

  public function getText(): string {
      return $this->text;
  }

}