<?php
class Poker_Hand {
    private $cards = [];
    private $judge = '';
    private $imagePaths = [];

    private $suitMap = [
        'hearts'   => 'suit1',
        'diamonds' => 'suit2',
        'clubs'    => 'suit3',
        'spades'   => 'suit4'
    ];

    public function __construct(array $cards) {
        // 入力正規化：suitは小文字・trim、numberは整数化
        $this->cards = array_map(function($c){
            return [
                'suit'   => strtolower(trim($c['suit'] ?? '')),
                'number' => intval($c['number'] ?? 0),
            ];
        }, $cards);
    }

    public function judgeHand() {
        // 枚数チェック
        if (count($this->cards) !== 5) {
            $this->judge = '不正判定 (Illegal hand)';
            return;
        }
        // 同一カード重複チェック
        $dupKey = array_map(fn($c) => $c['suit'].'-'.$c['number'], $this->cards);
        if (count($dupKey) !== count(array_unique($dupKey))) {
            $this->judge = '不正判定 (Illegal hand)';
            return;
        }

        $numbers = array_column($this->cards, 'number');
        $suits   = array_column($this->cards, 'suit');

        sort($numbers, SORT_NUMERIC);
        $isFlush    = count(array_unique($suits)) === 1;
        $isStraight = $this->isSequential($numbers);

        // Aを14として再判定
        $numbersAceHigh = array_map(fn($n) => $n === 1 ? 14 : $n, $numbers);
        sort($numbersAceHigh, SORT_NUMERIC);
        $isStraightAceHigh = $this->isSequential($numbersAceHigh);

        // A-2-3-4-5 (ホイール)
        $isWheel = ($numbers === [1,2,3,4,5]);

        // 同じ数字の枚数
        $countMap = array_count_values($numbers);
        $counts   = array_values($countMap);
        rsort($counts, SORT_NUMERIC);
        $maxCnt     = $counts[0] ?? 0;
        $secondCnt  = $counts[1] ?? 0;

        // 役判定
        if ($isFlush && $isStraightAceHigh && $numbersAceHigh === [10,11,12,13,14]) {
            $this->judge = 'ロイヤルストレートフラッシュ (Royal Straight Flush)';
        } elseif ($isFlush && ($isStraight || $isStraightAceHigh || $isWheel)) {
            $this->judge = 'ストレートフラッシュ (Straight Flush)';
        } elseif ($maxCnt === 4) {
            $this->judge = 'フォーカード (Four Card)';
        } elseif ($maxCnt === 3 && $secondCnt === 2) {
            $this->judge = 'フルハウス (Full House)';
        } elseif ($isFlush) {
            $this->judge = 'フラッシュ (Flush)';
        } elseif ($isStraight || $isStraightAceHigh || $isWheel) {
            $this->judge = 'ストレート (Straight)';
        } elseif ($maxCnt === 3) {
            $this->judge = 'スリーカード (Three Card)';
        } elseif ($maxCnt === 2 && $secondCnt === 2) {
            $this->judge = 'ツーペア (Two Pair)';
        } elseif ($maxCnt === 2) {
            $this->judge = 'ワンペア (One Pair)';
        } else {
            $this->judge = '役なし (None)';
        }
    }

    private function isSequential(array $numbers): bool {
        for ($i = 0; $i < count($numbers) - 1; $i++) {
            if ($numbers[$i] + 1 !== $numbers[$i + 1]) {
                return false;
            }
        }
        return true;
    }

    public function generateImagePaths() {
        $this->imagePaths = [];
        foreach ($this->cards as $card) {
            if (!isset($this->suitMap[$card['suit']])) {
                continue; // 想定外スートはスキップ
            }
            $suitKey = $this->suitMap[$card['suit']];
            $numKey  = 'number' . $card['number'];
            $this->imagePaths[] = "images/{$suitKey}_{$numKey}.png";
        }
    }

    public function getCards()      { return $this->cards; }
    public function getJudge()      { return $this->judge; }
    public function getImagePaths() { return $this->imagePaths; }
}

// =========================
// 実行部分
// =========================

// 例：POSTで cards[0][suit]=hearts, cards[0][number]=10 など送られる想定
$cards = $_POST['cards'] ?? [];

$hand = new Poker_Hand($cards);
$hand->judgeHand();
$hand->generateImagePaths();

echo "<h2>判定結果: {$hand->getJudge()}</h2>";

foreach ($hand->getImagePaths() as $img) {
    echo "<img src='{$img}' alt='card' style='width:80px;margin:5px;'>";
}
