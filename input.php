<?php

session_start();

require 'validation.php';

header('X-FRAME-OPTIONS: DENY');
// スーパーグローバル変数 php 9種類
// 連想配列
// if (!empty($_POST)) {
//   echo '<pre>';
//   var_dump($_POST);
//   echo '<pre>';
// }

function h($str) {
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 入力、確認、完了 input.php, confirm.php, thanks.php
// CSRF 偽物のinput.php->悪意のあるページ
// input.php

$pageFlag = 0;
$errors = validation($_POST);

if (!empty($_POST['btn_confirm']) && empty($errors)) {
  $pageFlag = 1;
}

if (!empty($_POST['btn_submit'])) {
  $pageFlag = 2;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body>


<?php if ($pageFlag === 1) : ?>
<?php if ($_POST['csrf'] === $_SESSION['csrfToken']):?>
<div class="container">
  <form method="POST" action="input.php">
  <label class=".col-sm-2 control-label" for="your_name">【氏名】</label>
  <div class="col-sm-10">
    <?php echo h($_POST['your_name']); ?>
  </div>

  <label class=".col-sm-2 control-label" for="email">メールアドレス</label>
    <div class="col-sm-10">
      <?php echo h($_POST['email']); ?>
    </div>

    <label class=".col-sm-2 control-label" for="url">ホームページ</label>
    <div class=".col-sm-10">
      <?php echo h($_POST['url']); ?>
    </div>

    <label class=".col-sm-2 control-label" for="gender">性別</label>
    <div class=".col-sm-10">
      <?php
        if ($_POST['gender'] === '0'){ echo '男性'; }
        if ($_POST['gender'] === '1'){ echo '女性'; }
      ?>
    </div>

    <label class=".col-sm-2 control-label" for="age">年齢</label>
    <div class=".col-sm-10">
      <?php
        if ($_POST['age'] === '1'){ echo '〜19歳'; }
        if ($_POST['age'] === '2'){ echo '20歳〜29歳'; }
        if ($_POST['age'] === '3'){ echo '30歳〜39歳'; }
        if ($_POST['age'] === '4'){ echo '40歳〜49歳'; }
        if ($_POST['age'] === '5'){ echo '50歳〜59歳'; }
        if ($_POST['age'] === '6'){ echo '60歳〜'; }
      ?>
    </div>

    <label class=".col-sm-2 control-label" for="age">お問い合わせ内容</label>
    <div class=".col-sm-10">
      <?php echo h($_POST['contact']); ?>
    </div>

    <input class=".col-sm-2 btn btn-secondary" type="submit" name="back" value="戻る">
    <input class="btn btn-success" type="submit" name="btn_submit" value="送信する">
    <input type="hidden" name="your_name" value="<?php echo h($_POST['your_name']); ?>">
    <input type="hidden" name="email" value="<?php echo h($_POST['email']);?>">
    <input type="hidden" name="url" value="<?php echo h($_POST['url']); ?>">
    <input type="hidden" name="gender" value="<?php echo h($_POST['gender']); ?>">
    <input type="hidden" name="age" value="<?php echo h($_POST['age']); ?>">
    <input type="hidden" name="contact" value="<?php echo h($_POST['contact']); ?>">
    <input type="hidden" name="csrf" value="<?php echo h($_POST['csrf']);?>">
    </form>
</div>
<?php endif; ?>
<?php endif; ?>

<?php if ($pageFlag === 2) : ?>
<?php if ($_POST['csrf'] === $_SESSION['csrfToken']) :?>

<?php
require 'insert.php';

insertContact($_POST);

?>

送信が完了しました。

<?php unset($_SESSION['csrfToken']); ?>
<?php endif; ?>
<?php endif; ?>

<?php if ($pageFlag === 0) : ?>
<?php
if (!isset($_SESSION['csrfToken'])) {
  $csrfToken = bin2hex(random_bytes(32));
  $_SESSION['csrfToken'] = $csrfToken;
}
$token = $_SESSION['csrfToken'];
?>

<?php if (!empty($errors) && !empty($_POST['btn_confirm'])) : ?>
  <?php echo '<ul>'; ?>
    <?php 
      foreach($errors as $error){
        echo '<li>' . $error . '</li>';
      }
    ?>
    </li>
  <?php echo '</ul>'; ?>
<?php endif; ?>

<div class="container">
  <div class="row">
    <div class=".col-md-6">
    <form method="POST" action="input.php">
    <div class="form-group">
      <label for="your_name">氏名</label>
      <input type="text" class="form-control" id="your_name" name="your_name" value="<?php if (!empty($_POST['your_name'])) {echo h($_POST['your_name']);} ?>" required>
    </div>

    <div class="form-group">
      <label for="email">メールアドレス</label>
      <input type="email" class="form-control" id="email" name="email" value="<?php if (!empty($_POST['email'])) {echo h($_POST['email']);} ?>" required>
    </div>

    <div class="form-group">
      <label for="url">ホームページ</label>
      <input type="url" class="form-control" id="url" name="url" value="<?php if (!empty($_POST['url'])) {echo h($_POST['url']);} ?>">
    </div>

    性別
    <div class="form-check form-check-inline">
      <input type="radio" class="form-check-input" name="gender" id="gender1" value="0" <?php if (isset($_POST['gender']) && $_POST['gender'] === '0'){ echo 'checked'; }?>>
      <label class="form-check-label" for="gender1">男性</label>
    </div>
    <div class="form-check form-check-inline">
      <input type="radio" class="form-check-input" name="gender" id="gender2" value="1" <?php if (isset($_POST['gender']) && $_POST['gender'] === '1'){ echo 'checked'; }?>>
      <label class="form-check-label" for="gender2">女性</label>
    </div>
    <div class="form-group">
      <label for="age">年齢</label>
      <select class="form-control" id="age" name="age">
        <option value="">選択してください</option>
        <option value="1" <?php if (isset($_POST['age']) && $_POST['age'] === '1'){ echo 'selected'; } ?>>〜19歳</option>
        <option value="2" <?php if (isset($_POST['age']) && $_POST['age'] === '2'){ echo 'selected'; } ?>>20歳〜29歳</option>
        <option value="3" <?php if (isset($_POST['age']) && $_POST['age'] === '3'){ echo 'selected'; } ?>>30歳〜39歳</option>
        <option value="4" <?php if (isset($_POST['age']) && $_POST['age'] === '4'){ echo 'selected'; } ?>>40歳〜49歳</option>
        <option value="5" <?php if (isset($_POST['age']) && $_POST['age'] === '5'){ echo 'selected'; } ?>>50歳〜59歳</option>
        <option value="6" <?php if (isset($_POST['age']) && $_POST['age'] === '6'){ echo 'selected'; } ?>>60歳〜</option>
      </select>
    </div>

    <div class="form-group">
      <label for="contact">お問い合わせ内容</label>
      <textarea class="form-control" id="contact" row="3" name="contact"><?php if (!empty($_POST['contact'])) {echo h($_POST['contact']);} ?></textarea>
    </div>

    <div class="form-check">
      <input type="checkbox" class="form-check-input" id="caution" name="caution" value="1">
      <label class="form-check-label" for="caution">注意事項にチェックする</label>
    </div>
    <input class="btn btn-info" type="submit" name="btn_confirm" value="確認する">
    <input type="hidden" name="csrf" value="<?php echo $token; ?>">

    </form>
    </div><!-- .col-md-6-->
  </div>
</div>

<?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</body>
</html>