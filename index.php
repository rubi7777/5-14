<?php
require_once 'poker.php'; // クラス定義だけ読み込む

$hand = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cards = [];
    for ($i = 1; $i <= 5; $i++) {
        $suitKey   = 'suit' . $i;
        $numberKey = 'number' . $i;
        if (!empty($_POST[$suitKey]) && !empty($_POST[$numberKey])) {
            $cards[] = [
                'suit'   => $_POST[$suitKey],
                'number' => intval($_POST[$numberKey])
            ];
        }
    }
    if ($cards) {
        $hand = new Poker_Hand($cards);
        $hand->judgeHand();
        $hand->generateImagePaths();
    }
} else {
    // デフォルトはスペード1〜5
    $defaultCards = [];
    for ($i = 1; $i <= 5; $i++) {
        $defaultCards[] = ['suit' => 'spade', 'number' => $i];
    }
    $hand = new Poker_Hand($defaultCards);
    $hand->judgeHand();
    $hand->generateImagePaths();
}
?>

<!DOCTYPE html>
<html data-wf-page="65a6358f98ae25d9e60af7b3" data-wf-site="65a6257c9b4dab4f4c5b2ebc">
<head>
  <meta charset="utf-8">
  <title>Pocker Program</title>
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/stylesheet.css" rel="stylesheet" type="text/css">
  <link href="css/poker-game-sample.css" rel="stylesheet" type="text/css">
  <link href="images/spade/1.png" rel="shortcut icon" type="image/x-icon">
</head>
<body>
  <div class="w-form">
    <form id="email-form" action="index.php" method="post" class="form-2">
      <div class="w-layout-blockcontainer container-2 w-container">

        <!-- CARD 1 -->
        <div class="w-layout-blockcontainer container-3 w-container">
          <label for="" class="field-label">CARD 1</label>
          <div class="w-layout-blockcontainer container w-container">
            <select id="suit1" name="suit1" class="suit-1 w-select">
              <option value=""></option>
              <option value="spade">spade</option>
              <option value="heart">heart</option>
              <option value="diamond">diamond</option>
              <option value="club">club</option>
            </select>
            <select id="number1" name="number1" class="number1 w-select">
              <option value=""></option>
              <?php for ($n=1; $n<=13; $n++): ?>
                <option value="<?= $n ?>"><?= $n ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <!-- CARD 2 -->
        <div class="w-layout-blockcontainer container-4 w-container">
          <label for="" class="field-label-2">CARD 2</label>
          <div class="w-layout-blockcontainer container w-container">
            <select id="suit2" name="suit2" class="suit-2 w-select">
              <option value=""></option>
              <option value="spade">spade</option>
              <option value="heart">heart</option>
              <option value="diamond">diamond</option>
              <option value="club">club</option>
            </select>
            <select id="number2" name="number2" class="number2 w-select">
              <option value=""></option>
              <?php for ($n=1; $n<=13; $n++): ?>
                <option value="<?= $n ?>"><?= $n ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <!-- CARD 3 -->
        <div class="w-layout-blockcontainer container-5 w-container">
          <label for="" class="field-label-3">CARD 3</label>
          <div class="w-layout-blockcontainer container w-container">
            <select id="suit3" name="suit3" class="suit-3 w-select">
              <option value=""></option>
              <option value="spade">spade</option>
              <option value="heart">heart</option>
              <option value="diamond">diamond</option>
              <option value="club">club</option>
            </select>
            <select id="number3" name="number3" class="number3 w-select">
              <option value=""></option>
              <?php for ($n=1; $n<=13; $n++): ?>
                <option value="<?= $n ?>"><?= $n ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <!-- CARD 4 -->
        <div class="w-layout-blockcontainer container-6 w-container">
          <label for="" class="field-label-4">CARD 4</label>
          <div class="w-layout-blockcontainer container w-container">
            <select id="suit4" name="suit4" class="suit-4 w-select">
              <option value=""></option>
              <option value="spade">spade</option>
              <option value="heart">heart</option>
              <option value="diamond">diamond</option>
              <option value="club">club</option>
            </select>
            <select id="number4" name="number4" class="number4 w-select">
              <option value=""></option>
              <?php for ($n=1; $n<=13; $n++): ?>
                <option value="<?= $n ?>"><?= $n ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <!-- CARD 5 -->
        <div class="w-layout-blockcontainer w-container">
          <label for="" class="field-label-5">CARD 5</label>
          <div class="w-layout-blockcontainer container w-container">
            <select id="suit5" name="suit5" class="suit-5 w-select">
              <option value=""></option>
              <option value="spade">spade</option>
              <option value="heart">heart</option>
              <option value="diamond">diamond</option>
              <option value="club">club</option>
            </select>
            <select id="number5" name="number5" class="number5 w-select">
              <option value=""></option>
              <?php for ($n=1; $n<=13; $n++): ?>
                <option value="<?= $n ?>"><?= $n ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

      </div>
      <button type="submit" class="button w-button">SEND</button>
    </form>
  </div>

  <?php if ($hand): ?>
    <section>
      <h1 class="heading-2">hand of cards：</h1>
      <div class="w-layout-grid grid">
        <?php foreach ($hand->getImagePaths() as $path): ?>
          <img src="<?= htmlspecialchars($path) ?>" loading="lazy" alt="">
        <?php endforeach; ?>
      </div>
    </section>
    <h1 class="heading-3">
      <strong>A poker hand→</strong><?= htmlspecialchars($hand->getJudge()) ?>
    </h1>
  <?php endif; ?>
</body>
</html>
