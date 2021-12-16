<?php

 require('function.php');

 if (!empty($_SESSION['login'])) {
     debug('ログイン済です');
     if (basename($_SERVER['PHP_SELF']) === 'login.php') {
         debug('管理ページへ遷移します。');
         header("Location:admin.php"); //マイページへ
         }
     } else {
         debug('未ログインユーザーです');
         if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
            header("Location:login.php"); //ログインページへ
         }
     }


         try {
             $dbh = dbConnect();
             $dbh->beginTransaction();
             $sql = 'SELECT * FROM users ORDER BY upload_time DESC';
             $message = getData($dbh, $sql);
         } catch (Exception $e) {
             error_log('エラー発生：'.$e->getMessage());
             $dbh->rollBack();
         }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>投稿一覧</title>
</head>
<body>
  <header id="header">
      <h1 class="title">Viewer</h1>
      <nav id="top-nav">
          <ul class="header list">
              <li><a href="">mypage</a></li>
              <li><a href="index.php">contact</a></li>
              <li><a href="logout.php">logout</a></li>
          </ul>
      </nav>
  </header>
  <main id="container" class="site-width">
          <h2 class="form-title">
              投稿一覧
          </h2>
          <form method="get" action="./download.php" class="form">
            <select name="limit" class="limit">
                <option value="">全て</option>
                <option value="10">10件</option>
                <option value="30">30件</option>
            </select>
             <div class="form-container">
               <input type="submit" name="download" value="ダウンロード" class="btn">
             </div>
          </form>
      <section class="message-section">
          <?php foreach($message as $value): ?>
             <article>
                 <div class="info">
                    <h2><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <time><?php echo date('Y年m月d日 H:i', strtotime($value['upload_time'])); ?> </time>
                    <p class="link-list">
                        <a href="edit.php?message_id=<?php echo $value['id']; ?>" class="link">編集</a>
                        <a href="delete.php?message_id=<?php echo $value['id']; ?>" class="link">削除</a>
                    </p>
                </div>
              <p class="message-container"><?php echo nl2br(htmlspecialchars($value['message'], ENT_QUOTES, 'UTF-8')) ; ?></p>
             </article>
          <?php endforeach; ?>
      </section>
  </main>
  <footer>
      &copy; copy right
  </footer>
</body>
</html>
