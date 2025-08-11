<?php
class Poker_Hand {
    private $card = [];
    private $judge = '';

    public function __construct($cards) {
        $this->card = $cards;
    }

    public function judgeHand() {
        $numbers = array_column($this->card, 'number');
        $suits = array_column($this->card, 'suit');

        sort($numbers);

        $isFlush = count(array_unique($suits)) === 1;
        $isStraight = $this->isSequential($numbers);

        // Aを14として再判定（10, J, Q, K, A）
        $numbersAceHigh = array_map(fn($n) => $n === 1 ? 14 : $n, $numbers);
        sort($numbersAceHigh);
        $isStraightAceHigh = $this->isSequential($numbersAceHigh);

        // 同じ数字の枚数をカウント
        $counts = array_count_values($numbers);
        rsort($counts); // 出現数の降順

        // 判定ロジック
        if ($isFlush && ($isStraight || $isStraightAceHigh)) {
            $this->judge = 'Straight Flush';
        } elseif ($counts[0] === 4) {
            $this->judge = 'Four of a Kind';
        } elseif ($counts[0] === 3 && $counts[1] === 2) {
            $this->judge = 'Full House';
        } elseif ($isFlush) {
            $this->judge = 'Flush';
        } elseif ($isStraight || $isStraightAceHigh) {
            $this->judge = 'Straight';
        } elseif ($counts[0] === 3) {
            $this->judge = 'Three of a Kind';
        } elseif ($counts[0] === 2 && $counts[1] === 2) {
            $this->judge = 'Two Pair';
        } elseif ($counts[0] === 2) {
            $this->judge = 'One Pair';
        } else {
            $this->judge = 'High Card';
        }
    }

    private function isSequential($numbers) {
        for ($i = 0; $i < count($numbers) - 1; $i++) {
            if ($numbers[$i] + 1 !== $numbers[$i + 1]) {
                return false;
            }
        }
        return true;
    }

    public function getCards() {
        return $this->card;
    }

    public function getJudge() {
        return $this->judge;
    }
}

// メイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cards = $_POST['cards'] ?? [];

    // POSTでカードが送られてこなかった場合はランダムに生成
    if (empty($cards)) {
        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $numbers = range(1, 13); // 1 = A, 11 = J, 12 = Q, 13 = K
        $deck = [];

        foreach ($suits as $suit) {
            foreach ($numbers as $number) {
                $deck[] = ['number' => $number, 'suit' => $suit];
            }
        }

        shuffle($deck);
        $cards = array_slice($deck, 0, 5);
    }

    $poker = new Poker_Hand($cards);
    $poker->judgeHand();

    $judge = $poker->getJudge();
    $selectedCards = $poker->getCards();

    include 'index.php'; // ここで$judgeと$selectedCardsを使う
}
