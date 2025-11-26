<?php
class Soal {
    private $question;
    private $options;
    private $answer;

    public function __construct($question, $options, $answer) {
        $this->question = $question;
        $this->options = $options;
        $this->answer = $answer;
    }

    public function getQuestion() {
        return $this->question;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getAnswer() {
        return $this->answer;
    }

    public function isCorrect($userAnswer) {
        return strtoupper(trim($userAnswer)) === strtoupper(trim($this->answer));
    }
}
