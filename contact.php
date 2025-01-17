<?php
  # セッション変数使えるように (WEBはそのページの値を次の画面に渡さない＝セッションが切れるため、ページまたいでも値を保持するように)
  session_start(); 

  # ===== CSRF対策 トークン比較 =====
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isTokenValid = false;
    # $_POST['token']と$_SESSION['token']が空ではなく、値が一致する時のみ$isTokenValidをtrueにする
    if (!empty($_POST['token']) && !empty($_SESSION['token'])){
        if ($_POST['token'] === $_SESSION['token']){
            $isTokenValid = true;
        }
    }
    if ($isTokenValid === true){
        # トークンが有効の時 ＝ トークン値が一致する時は処理が進む
    }
    if ($isTokenValid === false){
        # トークンが無効の時
        echo "<h1 style='color:red;'>不正な処理が行われました！</h1>";
        // header('Location: ./index.html');
        exit();
    }
  }
  # ===== CSRF対策 トークン生成 =====
  $token = bin2hex(random_bytes(32));
  $_SESSION['token'] = $token;

  # ===== バリデーション =====
  $error = [];
  # フォームの送信時＝POSTで呼びだられた時のみにエラーチェックする
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    # formから直接の値($_POST)を扱うのはセキュリティリスクが高いため、filter_input_array → POSTの値をフィルタリングして安全性を高める(危険な文字をなくす)
    $post = filter_input_array(INPUT_POST, $_POST);
    # =====「名前」
    if (empty($post['name'])) {
      $error['name'] = 'blank';
    } elseif (mb_strlen($post['name']) > 50) {
      # 50文字以上だった場合
      $error['name'] = 'exceed';
    } elseif (preg_match("/^[ぁ-んァ-ヶ一ー-龠a-zA-Z0-9　 ]+$/", $post['name'])) {
      # ひらがな・カナ・漢字・英数字・数字・全半空白
      # エラーなし
    } else {
      $error['name'] = 'invaild';
    }
    # =====「メール」
    if (empty($post['email'])) {
      $error['email'] = 'blank';
    } elseif (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
      $error['email'] = 'invaild';
    }
    # =====「電話番号」
    if (preg_match("/[^0-9 ]/", $post['phone'])) {
      # 半角数字以外の場合
      $error['phone'] = 'invaild';
    } elseif (mb_strlen($post['phone']) > 20) {
      # 20文字以上だった場合
      $error['phone'] = 'exceed';
    } else {
      # エラーなし
    }

    // if (empty($post['message'])) {
    //   $error['message'] = 'blank';
    // } elseif (mb_strlen($post['message']) > 500) {
    //   # 500文字以上だった場合
    //   $error['message'] = 'exceed';
    // } else {
    //   # エラーなし
    // }

    # =====「問い合わせ内容」
    if (empty($post['message'])) {
      $error['message'] = 'blank';
    } elseif (mb_strlen($post['message']) > 500) {
      # 500文字以上だった場合
      $error['message'] = 'exceed';
    } elseif (preg_match("/^[ぁ-んァ-ヶ一ー-龠a-zA-Z0-9　 ,.、。]+$/u", $post['message'])) {
      # 正規表現に合致する場合(ひらがな・カナ・漢字・半英数字・全半空白・記号,.、。)
      # エラーなし
    } elseif (preg_match("/\r\n/", $post['message'])) {
      # 改行があった場合
      # エラーなし
    } else {
      $error['message'] = 'invaild';
    }

    # 上記で$errorの配列数カウントが０であれば ＝ 上記で設定したエラーに一つも当てはまらなければ確認画面confiem.phpへ
    if (count($error) === 0) {
      # postの内容をセッションに代入
      $_SESSION['form'] = $post;
      # 確認画面 contact.phpへ移動
      header('Location: ./confirm.php');
      exit();
    }
  } else {
    # セッションデータがあるならデータを再現 (入力画面でリロードしたり、確認画面から修正で入力画面に戻った際etc) 
    if (isset($_SESSION['form'])) {
      $post = $_SESSION['form'];
    }
  }
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>お問い合わせ | Contact</title>
  <!-- Viewport マルチデバイス対応のため -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Favicon icon -->
  <link rel="shortcut icon" href="img/programmer.png">
  <!-- Google fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&amp;display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&family=Sansita+Swashed:wght@600&display=swap" rel="stylesheet">
  <!-- CSS -->
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/style-rwd.css">
</head>

<body>
  <!-- header -->
  <header id="header" class="header-wrap">
    <!-- logo -->
    <a href="index.html" class="site-title-header"><img src="./img/title-logo.svg" width="160px" alt="サイトタイトルロゴ"></a>
    <!-- ハンバーガーメニュー -->
    <div class="nav">
      <!-- 表示・非表示を切り替えるチェックボックス -->
      <input id="drawer_input" class="drawer_hidden" type="checkbox">
      <label for="drawer_input" class="drawer_open">
        <span></span>
      </label>
      <nav id="navi" class="nav-content">
        <ul id="page-link" class="nav-list">
          <li><a href="index.html#about">私について</a></li>
          <li><a href="index.html#skills">スキル</a></li>
          <li><a href="index.html#service">サービス</a></li>
          <li><a href="index.html#works">制作物・実績</a></li>
          <div class="contact-show"><li>お問い合わせ</li></div>
          <li><a class="twitter-icon" href="https://twitter.com/Chacha_P_C_Log" target="_blank" rel="noopener noreferrer"><img src="./img/icons/twitter.png" width="27px" alt="Twitterアイコン"></a></li>
          <li><a class="github-icon" href="https://github.com/ChachaWeb-main" target="_blank" rel="noopener noreferrer"><img src="./img/icons/github.png" width="27px" alt="GitHubアイコン"></a></li>
        </ul>
      </nav>
    </div>
  </header>

  <?php 
# ----- 代入値確認用 -----
// echo '<br>';
// echo '<br>';
// echo '<br>';
// echo 'エラー判定代入値の確認';
// echo '<pre>';
// var_dump($error);
// echo '</pre>';
// echo '生成されたトークン $token';
// echo '<pre>';
// var_dump($token);
// echo '</pre>';
// echo "SESSION に保存されたトークン";
// echo '<pre>';
// var_dump($_SESSION['token']);
// echo '</pre>';
?>

  <main id="contact-container">
    <br>
    <br>
    <div class="section-title contact-section">
      <h2 class="en">Contact</h2>
      <p class="jp">お問い合わせ</p>
    </div>
    <div class="contact-wrap">
      <form action="" method="post" novalidate>
        <!-- 生成されたトークンを出力 -->
        <input type="hidden" name='token' value="<?php echo $_SESSION['token'] ?>">
        <p class="contact-info">お問い合わせ内容をご入力の上、「確認画面へ」をクリックしてください。</p>
        <p class="contact-info-en">Please enter the contact form and click "To Confirm".</p>
        <table class="contact-table">
          <tr>
            <th>名前 | Name<span class="required">必須 | Required</span></th>
            <td>
              <!-- 入力内容を送信時に再現 → value属性 -->
              <input size="20" type="text" class="wide" name="name" placeholder="ex).  山田太郎 | Taro Yamada" value="<?php echo htmlspecialchars($post['name']); ?>" autofocus/>
              <!-- Notice: Undefined index の防止 -->
              <?php if (!empty($error['name'])) : ?>
                <?php if ($error['name'] === 'blank') : ?>
                  <p class="error_msg">※お名前をご記入下さい。| Please fill in your name.</p>
                <?php endif; ?>
                <?php if ($error['name'] === 'invaild') : ?>
                  <p class="error_msg">※使用できない文字が含まれています。| Includes characters that can't be used.</p>
                <?php endif; ?>
                <?php if ($error['name'] === 'exceed') : ?>
                  <p class="error_msg">※50字以内で入力して下さい。| Please enter it within 50 characters.</p>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>メール | E-mail<span class="required">必須 | Required</span></th>
            <td>
              <input size="30" type="text" class="wide" name="email" placeholder="ex).  example@gmail.com" value="<?php echo htmlspecialchars($post['email']); ?>" />
              <?php if (!empty($error['email'])) : ?>
                <?php if ($error['email'] === 'blank') : ?>
                  <p class="error_msg">※メールアドレスをご記入下さい。| Please fill in your E-mail.</p>
                <?php endif; ?>
                <?php if ($error['email'] === 'invaild') : ?>
                  <p class="error_msg">※正しい形式ではありません。| It's not the correct format.</p>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>電話番号 | Phone<span class="any">任意 | Any</sapn></th>
            <td>
              <input size="30" type="text" class="wide" name="phone" placeholder="ex).  01234567890   ※半角数字のみ | Only half -width numbers" value="<?php echo htmlspecialchars($post['phone']); ?>" />
              <?php if (!empty($error['phone'])) : ?>
                <?php if ($error['phone'] === 'invaild') : ?>
                  <p class="error_msg">※半角数字のみご記入下さい。| Please fill in only half-width numbers</p>
                <?php endif; ?>
                <?php if ($error['phone'] === 'exceed') : ?>
                  <p class="error_msg">※20字以内で入力して下さい。| Please enter it within 20 characters.</p>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>お問い合わせ内容 | Message<br><br><span class="required last">必須 | Required</span></th>
            <td>
              <!-- value属性ないためタグ間に入力値 -->
              <textarea name="message" cols="50" rows="5" placeholder="お見積もりは無料で承ります。まずはお気軽にお問い合わせくださいませ。| I accept estimation for free. Please feel free to contact me first." required><?php echo htmlspecialchars($post['message']); ?></textarea>
              <?php if (!empty($error['message'])) : ?>
                <?php if ($error['message'] === 'blank') : ?>
                  <p class="error_msg">※お問い合わせ内容をご記入下さい。| Please fill in the inquiry.</p>
                <?php endif; ?>
                <?php if ($error['message'] === 'invaild') : ?>
                  <p class="error_msg">※使用できない文字が含まれています。| Includes characters that can't be used.</p>
                <?php endif; ?>
                <?php if ($error['message'] === 'exceed') : ?>
                  <p class="error_msg">※500字以内で入力して下さい。| Please enter it within 500 characters.</p>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
        </table>

        <p class="confirm-btn">
          <span><input type="submit" name="confirm" value="確認画面へ | To Confirm" /></span>
        </p>
        <div class="btn-to-top">
          <a href="./index.html" class="return"><span>トップへ戻る | Return to Top</span></a>
        </div>

      </form>
    </div>
    <br>
    <br>
  </main>

  <footer id="footer" class="footer-wrap">
    <p id="page-top"><a href="#"><span>Page Top</span></a></p>
    <a href="index.html" class="site-logo-footer"><img src="./img/title-logo.svg" width="120" alt="サイトタイトルロゴ"></a>
    <p class="copyright">&copy; <span>Chacha WEB Create</span>&nbsp; 2022</p>
  </footer>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <!-- JavaScript -->
  <script src="./js/fadein.js"></script>
  <script src="./js/main.js"></script>
</body>

</html>
