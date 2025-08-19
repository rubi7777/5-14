<!DOCTYPE html>
<html data-wf-page="65a6358f98ae25d9e60af7b3" data-wf-site="65a6257c9b4dab4f4c5b2ebc">
<head>
  <meta charset="utf-8">
  <title>Pocker Program</title>
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta content="Webflow" name="generator">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/stylesheet.css" rel="stylesheet" type="text/css">
  <link href="css/poker-game-sample.css" rel="stylesheet" type="text/css">
  <link href="images/spade/1.png" rel="shortcut icon" type="image/x-icon">
</head>
<body>
  <div class="w-form">
    <form id="email-form" action="index.php" name="email-form" data-name="Email Form" method="post" class="form-2">
      <div class="w-layout-blockcontainer container-2 w-container">

        <?php for ($i=1; $i<=5; $i++): ?>
        <div class="w-layout-blockcontainer container w-container">
          <label for="suit<?= $i ?>" class="field-label">CARD <?= $i ?></label>
          <select id="suit<?= $i ?>" name="suit<?= $i ?>" class="suit-<?= $i ?> w-select">
            <option value=""></option>
            <option value="spade" <?= ($_POST['suit'.$i] ?? '')==='spade'?'selected':'' ?>>spade</option>
            <option value="heart" <?= ($_POST['suit'.$i] ?? '')==='heart'?'selected':'' ?>>heart</option>
            <option value="diamond" <?= ($_POST['suit'.$i] ?? '')==='diamond'?'selected':'' ?>>diamond</option>
            <option value="club" <?= ($_POST['suit'.$i] ?? '')==='club'?'selected':'' ?>>club</option>
          </select>
          <select id="number<?= $i ?>" name="number<?= $i ?>" class="number<?= $i ?> w-select">
            <option value=""></option>
            <?php for ($n=1; $n<=13; $n++): ?>
              <option value="<?= $n ?>" <?= (intval($_POST['number'.$i] ?? 0)==$n)?'selected':'' ?>><?= $n ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <?php endfor; ?>

      </div>
      <button type="submit" class="button w-button">SEND</button>
    </form>
  </div>

  <section>
    <h1 class="heading-2">hand of cards：</h1>
    <div class="w-layout-grid grid">
      <?php
        if (!empty($hand)) {
          foreach ($hand->getImagePaths() as $img) {
            echo "<img src='{$img}' alt='card' style='width:80px;margin:5px;'>";
          }
        }
      ?>
    </div>
  </section>

  <h1 class="heading-3">
    <strong>A poker hand→</strong>
    <?php if(!empty($hand)) echo $hand->getJudge(); ?>
  </h1>
</body>
</html>
