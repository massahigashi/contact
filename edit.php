<?php
 
 require('function.php');

 if (!empty($_SESSION['login'])) {
     debug('ログイン済です');

     } else {
         debug('未ログインユーザーです');
         if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
            header("Location:login.php"); //ログインページへ
            exit;
         }
     }
     if(empty($_GET['message_id'])) {
         debug('idがありません');
         debug('一覧ページへ遷移します。');
         header("Location:admin.php"); 
         exit;
     }

     if (!empty($_GET['message_id'])) {

         try {
             $dbh = dbConnect();
             $sql = 'SELECT * FROM users WHERE id = :id';
             $message_data = editGetData($dbh, $sql, $_GET['message_id'], ':id');
             debug('データ取得');
         } catch (Exception $e) {
             error_log('エラー発生：'.$e->getMessage());
         }

         if(empty($message_data)) {
             debug('編集データ取得失敗');
             header("Location:admin.php");
             exit;
         }
     }

     if(!empty($_POST['message_id'])) {

         debug('ポストの中身：'.print_r($_POST,true));
         $name = sanitize($_POST['name']);
         $message = sanitize(($_POST['message']));
         validRequired($name, 'name');
         validRequired($message, 'message');

         if(!empty($err_msg)) {
             debug('バリデーションエラー');
             exit;
         }

         if(empty($err_msg)) {

            try{
                $dbh->beginTransaction();
                $sql = 'Update users SET name = :name,message = :message WHERE id = :id';
                $data = array(':name' => $name, ':message' => $message, ':id' => $_POST['message_id']);

                $stmt = queryPost($dbh, $sql, $data);
                $res = $dbh->commit();
            }catch (Exception $e) {
                error_log('エラー発生：'.$e->getMessage());
                $dbh->rollBack();
            }

            if($res) {
                debug('データ編集成功');
                debug('投稿一覧ページに戻ります');
                header("Location:admin.php");
                exit;
            }
         }
     }
     $stmt = null;
     $dbh = null;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>編集ページ</title>
</head>
<body>
  <header id="header">
      <h1 class="title">Edit</h1>
      <nav id="top-nav">
          <ul class="header list">
              <li><a href="admin.php">viewer</a></li>
              <li><a href="">delete</a></li>
              <li><a href="">download</a></li>
          </ul>
      </nav>
  </header>  
  <main id="container" class="site-width">
      <section class="form-section">
          <h2 class="form-title">
              編集画面
          </h2>
          <div class="result-msg">
               <!-- 成功した場合-->
              <?php if(!empty($suc_msg['common'])): ?>
                <span class="suc-msg"> <?php echo $suc_msg['common']; ?> </span>
            　<!-- 失敗した場合　-->
              <?php elseif(!empty($err_msg['common'])): ?>
                <span class="err-msg"> <?php echo $err_msg['common']; ?></span>
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
                 value=" <?php if(!empty($name)) {
                           echo htmlspecialchars($name,ENT_QUOTES,'UTF-8');
                          }else {
                              echo $message_data['name'];
                          } ?>">
              </div>
              <div class="form-container">
                  <label for="message" class="label <?php if(!empty($err_msg['message'])) echo 'err'; ?> ">メッセージ</label>
                  <!--エラーメッセージの表示-->
                   <div class="err-msg">
                      <?php if(!empty($err_msg['message'])) echo $err_msg['message']; ?>
                  </div>
                  <textarea class="message <?php if(!empty($err_msg['message'])) echo 'back-err'; ?>" name="message" rows="3" > <?php if(!empty($message)) {
                      echo htmlspecialchars($message,ENT_QUOTES,'UTF-8');
                  }else {
                      echo $message_data['message'];
                  } ?></textarea>
              </div>
              <div class="form-container">
                  <div class="btn-container">
                        <a class="btn_cancel" href="admin.php">キャンセル</a>
                       <input type="submit" class="btn" name="submit-btn" value="更新">
                       <input type="hidden" name="message_id" 
                       value="<?php if(!empty($message_data['id'])) echo $message_data['id']; ?>">
                  </div>
              </div>
          </form>
      </section>
  </main>
  <footer>
      &copy; copy right
  </footer>
</body>
</html>