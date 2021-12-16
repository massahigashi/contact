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
            exit;
         }
     }
     if(!empty($_GET)) {
         debug('ゲット送信があります');
         debug('ゲットの中身'.print_r($_GET,true));
     }

     if(!empty($_GET['logout'])) {
         $_SESSION = array();
         session_destroy();
         debug('セッションの中身'.print_r($_SESSION,true));
     }
     if(empty($_SESSION)) {
         debug('ログアウト成功');
         header("Location:login.php");
         exit;
     }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>ログアウト</title>
</head>
<body>
  <header id="header">
      <h1 class="title">Logout</h1>
  </header>  
  <main id="container" class="site-width">
      <section class="form-section">
          <h2 class="form-title">
              ログアウト
          </h2>
          <p class="text-confirm">ログアウトします。 よろしいですか？</p>
          <form method="GET" action="">
             <div class="form-container">
               <input type="submit" name="logout" value="ログアウト" class="btn delete-btn">
             </div>
          </form>
      </section>
  </main>
  <footer>
      &copy; copy right
  </footer>
</body>
</html>