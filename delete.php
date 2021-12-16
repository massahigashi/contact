<?php
 
 require('function.php');

 if (!empty($_SESSION['login'])) {
     debug('ログイン済です');

     } else {
         debug('未ログインユーザーです');
         if(basename($_SERVER['PHP_SELF']) !== 'edit.php'){
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

            try {
                $dbh->beginTransaction();
                $sql = 'DELETE FROM users WHERE id = :id';
                $stmt = deleteData($dbh, $sql, $_POST['message_id'], ':id');
                $res = $dbh->commit();

            } catch (Exception $e) {
                error_log('エラー発生：'.$e->getMessage());
                $dbh->rollBack();
            } 

            if($res) {
                debug('削除成功');
                debug('投稿一覧ページに戻ります');
                header("Location:admin.php");
                exit;
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
    <title>削除ページ</title>
</head>
<body>
  <header id="header">
      <h1 class="title">Delete</h1>
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
              削除画面
          </h2>
          <p class="text-confirm">以下の投稿を削除します。<br>よろしければ「削除」ボタンを押してください。</p>
          <form action="" class="form" method="POST">
              <div class="form-container">
                 <label for="name" class="label">名前</label>
                 <input type="text" class="name" name="name" 
                 value=" <?php echo $message_data['name'];?>" disabled>
              </div>
              <div class="form-container">
                  <label for="message" class="label">メッセージ</label>
                  <textarea class="message" name="message" rows="3" disabled> <?php echo $message_data['message'];?> </textarea>
              </div>
              <div class="form-container">
                  <div class="btn-container">
                        <a class="btn_cancel" href="admin.php">キャンセル</a>
                       <input type="submit" class="btn" name="submit-btn" value="削除">
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