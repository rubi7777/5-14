<?php
class Poker_Hand {
    private $cards = [];
    private $judge = '';
    private $imagePaths = [];

    // スート変換表（画像用）
    private $suitMap = [
        'hearts'   => 'suit1',
        'diamonds' => 'suit2',
        'clubs'    => 'suit3',
        'spades'   => 'suit4'
    ];

    public function __construct($cards) {
        $this->cards = $cards;
    }

    public function judgeHand() {
        // 不正判定チェック
        if (count($this->cards) !== 5) {
            $this->judge = '不正判定 (Illegal hand)';
            return;
        }
        if (count($this->cards) !== count(array_unique(array_map(fn($c) => $c['suit'] . $c['number'], $this->cards)))) {
            $this->judge = '不正判定 (Illegal hand)';
            return;
        }

        $numbers = array_column($this->cards, 'number');
        $suits   = array_column($this->cards, 'suit');

        sort($numbers);
        $isFlush = count(array_unique($suits)) === 1;
        $isStraight = $this->isSequential($numbers);

        // Aを14として再判定
        $numbersAceHigh = array_map(fn($n) => $n === 1 ? 14 : $n, $numbers);
        sort($numbersAceHigh);
        $isStraightAceHigh = $this->isSequential($numbersAceHigh);

        // 同じ数字の枚数をカウント
        $counts = array_count_values($numbers);
        rsort($counts);

        // 役判定
        if ($isFlush && $isStraightAceHigh && $numbersAceHigh === [10, 11, 12, 13, 14]) {
            $this->judge = 'ロイヤルストレートフラッシュ (Royal Straight Flush)';
        } elseif ($isFlush && ($isStraight || $isStraightAceHigh)) {
            $this->judge = 'ストレートフラッシュ (Straight Flush)';
        } elseif ($counts[0] === 4) {
            $this->judge = 'フォーカード (Four Card)';
        } elseif ($counts[0] === 3 && $counts[1] === 2) {
            $this->judge = 'フルハウス (Full House)';
        } elseif ($isFlush) {
            $this->judge = 'フラッシュ (Flush)';
        } elseif ($isStraight || $isStraightAceHigh) {
            $this->judge = 'ストレート (Straight)';
        } elseif ($counts[0] === 3) {
            $this->judge = 'スリーカード (Three Card)';
        } elseif ($counts[0] === 2 && $counts[1] === 2) {
            $this->judge = 'ツーペア (Two Pair)';
        } elseif ($counts[0] === 2) {
            $this->judge = 'ワンペア (One Pair)';
        } else {
            $this->judge = '役なし (None)';
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

    // 画像パス生成
    public function generateImagePaths() {
        $this->imagePaths = [];
        foreach ($this->cards as $card) {
            $suitKey = $this->suitMap[$card['suit']];
            $numKey = 'number' . $card['number'];
            $this->imagePaths[] = "images/{$suitKey}_{$numKey}.png";
        }
    }

    public function getCards() {
        return $this->cards;
    }

    public function getJudge() {
        return $this->judge;
    }

    public function getImagePaths() {
        return $this->imagePaths;
    }
}

// ========== メイン処理 ==========

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSONで送信された場合に対応
    $raw = file_get_contents("php://input");
    $json = json_decode($raw, true);

    if (is_array($json) && isset($json['cards'])) {
        $cards = $json['cards'];
    } else {
        $cards = $_POST['cards'] ?? [];
    }

    // データが空ならランダム配布
    if (empty($cards)) {
        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $numbers = range(1, 13); // 1=A, 11=J, 12=Q, 13=K
        $deck = [];
        foreach ($suits as $suit) {
            foreach ($numbers as $number) {
                $deck[] = ['number' => $number, 'suit' => $suit];
            }
        }
        shuffle($deck);
        $cards = array_slice($deck, 0, 5);
    }

    // 判定
    $poker = new Poker_Hand($cards);
    $poker->judgeHand();
    $poker->generateImagePaths();

    // JSONで返却
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "judge" => $poker->getJudge(),
        "cards" => $poker->getCards(),
        "images" => $poker->getImagePaths()
    ]);
    exit;
}
