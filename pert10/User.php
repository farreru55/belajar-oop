<?php 
class User {
    private $score;

    public function __construct() {
        $this->score = 0;
    }

    public function getScore() {
        return $this->score;
    }

    public function addScore($points) {
        $this->score += $points;
    }

    public function deductScore($points) {
        $this->score -= $points;
    }
}
?>