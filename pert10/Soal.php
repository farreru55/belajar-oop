<?php
class Soal {
    private $text;
    private $options;
    private $correctAnswerKey;

   public function __construct(string $text, array $options, string $correctAnswerKey) {
        $this->text = $text;
        $this->options = $options;
        $this->correctAnswerKey = strtoupper($correctAnswerKey);
    }

    /**
     * Menampilkan pertanyaan dan opsi jawaban ke konsol.
     */
    public function display(): void {
        echo $this->text . PHP_EOL;
        foreach ($this->options as $key => $value) {
            echo "  $key. $value" . PHP_EOL;
        }
        echo "Jawaban Anda (A/B/C/D): ";
    }

    /**
     * Memeriksa apakah jawaban pengguna benar.
     *
     * @param string $userAnswer Jawaban dari pengguna (sudah di-uppercase).
     * @return bool True jika benar, false jika salah.
     */
    public function checkAnswer(string $userAnswer): bool {
        return $userAnswer === $this->correctAnswerKey;
    }

    /**
     * Mendapatkan teks dari jawaban yang benar.
     *
     * @return string Teks jawaban yang benar.
     */
    public function getCorrectAnswerText(): string {
        if (isset($this->options[$this->correctAnswerKey])) {
            return $this->options[$this->correctAnswerKey];
        }
        return "Jawaban tidak ditemukan";
    }
}
?>