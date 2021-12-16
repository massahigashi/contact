<?php

 require('function.php');
 $dbh = dbConnect();
 
  if (!empty($dbh)) {
             $sql = 'SELECT name,message,upload_time FROM users ORDER BY upload_time DESC';
             $message = getData($dbh, $sql);
    }


 if(!empty($_POST)) {
     debug('ポスト送信があります');
     debug('ポスト送信の内容：'.print_r($_POST,true));
     

     $name = sanitize($_POST['name']);
     $message = sanitize($_POST['message']);
     //バリデーションチェック
     validRequired($name, 'name');
     validRequired($message, 'message');
     if(!empty($err_msg)) {
         debug('バリデーションエラー');
         
     }
     //バリデーションOKの場合
     if (empty($err_msg)) {
         debug('バリデーションOK');
         debug('セッションに保存します');
         $_SESSION['name'] = $name;
         //データベースへ接続;
         try {

             $dbh->beginTransaction();
             $sql = 'INSERT INTO users(name,message,upload_time) VALUE (:name,:message,:upload_time)';
             $data = array(':name' => $name, ':message' => $message, 'upload_time' => date('Y-m-d H:i:s'));

             $stmt = queryPost($dbh, $sql, $data);
             $stmt = $dbh->commit();
         } catch (Exception $e) {
             error_log('エラー発生：'.$e->getMessage());
             $dbh->rollBack();
         }

        if($stmt) {
            $_SESSION['suc-msg'] =SUC01;
        } else {
            $err_msg['common'] = MSG02; 
        }

         debug('prepareステートメントを削除します');
         $stmt = null;
         debug('データベースの接続を閉じます');
         $dbh = null;
         header("Location:index.php");
         exit;
     }
 }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>contact</title>
</head>
<body>
  <header id="header">
      <h1 class="title">Contact</h1>
      <nav id="top-nav">
          <ul class="header list">
              <li><a href="login.php">login</a></li>
              <li><a href="">mypage</a></li>
              <li><a href="">contact</a></li>
          </ul>
      </nav>
  </header>  
  <main id="container" class="site-width">
      <section class="form-section">
          <h2 class="form-title">
              ひと言掲示板
          </h2>
          <div class="result-msg">
              <!-- 成功した場合-->
              <?php if(empty($_POST['submit_btn']) && !empty($_SESSION['suc-msg'])): ?>
                <span class="suc-msg"> <?php echo htmlspecialchars($_SESSION['suc-msg'],ENT_QUOTES,'UTF-8'); ?> </span>
                <?php unset($_SESSION['suc-msg']); ?>
            　<!-- 失敗した場合　-->
              <?php elseif(empty($_POST['submit_btn']) && !empty($err_msg['common'])): ?>
                <span class="err-msg"> <?php echo htmlspecialchars($err_msg['common'], ENT_QUOTES,'UTF-8'); ?></span>
                <?php unset($err_msg['common']); ?>
              <?php endif; ?>
          </div>
          <form action="" class="form" method="POST">
              <div class="form-container">
                 <label for="name" class="label <?php if(!empty($err_msg['name'])) echo 'err'; ?>">名前</label>
                 <!--エラーメッセージの表示-->
                  <div class="err-msg">
                      <?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?>
                  </div>
                 <input type="text" class="name <?php if(!empty($err_msg['name'])) echo 'back-err'; ?>" name="name" 
                 value="<?php if(!empty($_SESSION['name'])) echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>">
              </div>
              <div class="form-container">
                  <label for="message" class="label <?php if(!empty($err_msg['message'])) echo 'err'; ?> ">メッセージ</label>
                  <!--エラーメッセージの表示-->
                   <div class="err-msg">
                      <?php if(!empty($err_msg['message'])) echo $err_msg['message']; ?>
                  </div>
                　<textarea class="message <?php if(!empty($err_msg['message'])) echo 'back-err'; ?>" name="message" rows="3" value=""></textarea>
              </div>
              <div class="form-container">
                 <input type="submit" class="btn" name="submit-btn" value="投稿する">
              </div>
          </form>
      </section>
      <section class="message-section">
          <?php if(!empty($message)) : ?>
            <?php foreach($message as $value): ?>
          <article>
              <div class="info">
                  <h2><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                  <time><?php echo date('Y年m月d日 H:i', strtotime($value['upload_time'])); ?> </time>
              </div>
              <p class="message-container"><?php echo nl2br(htmlspecialchars($value['message'], ENT_QUOTES, 'UTF-8')) ; ?></p>
          </article>
          <?php endforeach; ?>
          <?php endif; ?>
      </section>
  </main>
  <footer>
      &copy; copy right
  </footer>
</body>
</html>