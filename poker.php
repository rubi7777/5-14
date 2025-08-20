<?php
class Poker_Hand {
    private $cards = [];
    private $judge = '';
    private $imagePaths = [];

    // HTML <option> の value に合わせたスートマップ
    private $suitMap = [
        'spade'   => 'spade',
        'heart'   => 'heart',
        'diamond' => 'diamond',
        'club'    => 'club'
    ];


 public function setJudge($msg) {
        $this->judge = $msg;
    }




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
        if (count($this->cards) !== 5) {
            $this->judge = '不正判定 (Illegal hand)';
            return;
        }

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

        $numbersAceHigh = array_map(fn($n) => $n === 1 ? 14 : $n, $numbers);
        sort($numbersAceHigh, SORT_NUMERIC);
        $isStraightAceHigh = $this->isSequential($numbersAceHigh);

        $isWheel = ($numbers === [1,2,3,4,5]);

        $countMap = array_count_values($numbers);
        $counts   = array_values($countMap);
        rsort($counts, SORT_NUMERIC);
        $maxCnt     = $counts[0] ?? 0;
        $secondCnt  = $counts[1] ?? 0;

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
            $suit = strtolower(trim($card['suit']));
            $number = $card['number'];
            if (!isset($this->suitMap[$suit])) continue;
            $suitFolder = $this->suitMap[$suit];
            $this->imagePaths[] = "images/{$suitFolder}/{$number}.png";
        }
    }

    public function getCards()      { return $this->cards; }
    public function getJudge()      { return $this->judge; }
    public function getImagePaths() { return $this->imagePaths; }
}

