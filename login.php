<?php
 require('function.php');

 if(!empty($_POST)) {
     debug('POST送信があります。');
     debug('POST内容：'.print_r($_POST,true));
     
     $pass = $_POST['password'];
     validRequired($pass, 'password');
     if(!empty($err_msg)) {
         debug('バリデーションエラー');
     }
     if(empty($err_msg)) {
         validLogin($pass, 'login');
     }

     if($login) {
         debug('パスワードOK');
         header("Location:admin.php");
         exit;
     } else {
         debug(('パスワードが違います'));
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
    <title>ログインページ</title>
</head>
<body>
  <header id="header">
      <h1 class="title">Login</h1>
  </header>  
    <main id="container" class="site-width">
        <section class="form-section">
            <h2 class="form-title">
                掲示板管理ページ
            </h2>
            <form action="" method="POST" class="form">
                <div class="form-container">
                    <label for="password" class="label <?php if(!empty($err_msg['password'])) echo 'err'; ?>">ログインパスワード</label>
                    <div class="err-msg">
                      <?php if(!empty($err_msg['password'])) echo $err_msg['password']; ?>
                   </div>
                    <input type="password" name="password" class="password <?php if(!empty($err_msg['password'])) echo 'back-err'; ?>" 
                    value="<?php if(!empty($_POST['password'])) echo $_POST['password']; ?>">
                </div>
                <div class="form-container">
                 <input type="submit" class="btn" name="submit-btn" value="ログイン">
                </div>
            </form>
        </section>
    </main>
  <footer>
      &copy; copy right
  </footer>
</body>
</html>