<?php
 require('function.php');

  if (!empty($_SESSION['login'])) {
     debug('ログイン済です');
     if (basename($_SERVER['PHP_SELF']) === 'login.php') {
         debug('ダウンロードを行います');
         header("Location:download.php"); //マイページへ
         }
     } else {
         debug('未ログインユーザーです');
         if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
            header("Location:login.php"); //ログインページへ
         }
     }
     
     if(!empty($_GET['limit'])) {
         if($_GET['limit'] === "10") {
             $limit = 10;
         } elseif($_GET['limit'] === "30") {
             $limit = 30;
         }
     }
     
   try {
       $dbh = dbConnect();
       if(!empty($_GET['limit'])) {
           $sql = 'SELECT * FROM users ORDER BY upload_time ASC LIMIT :limit';
           $message = limitGetData($dbh, $sql, $_GET['limit'], ':limit');
       }else{
           $sql = 'SELECT * FROM users ORDER BY upload_time ASC';
           $message = getData($dbh, $sql);
       }

       if(!empty($message)) {
           debug('データ取得成功');
           $dbh = null; 
       }

   } catch (Exception $e) {
       error_log('エラー発生；'.$e->getMessage());
       debug('管理ページへ遷移します');
       header("Location:admin.php");
   }
   

   header("Content-Type: text/csv");
   header("Content-Disposition: attachment; filename=メッセージデータ.csv");
   header("Content-Transfer-Encoding: binary");

   if(!empty($message)) {
       $csv_data .= '"ID","表示名","メッセージ","投稿日時"'."\n";
       foreach($message as $value) {
           $csv_data .= '"'.$value['id'].'","'.$value['name'].'","'.$value['message'].'","'.$value['upload_time']."\"\n";
       }
       echo $csv_data;
   }

?>