<?php
 ini_set('log_errors','on');
 ini_set('error_log','php.log');
 
 define('PASS', '4141');
 define('MSG01', '未入力です');
 define('MSG02', '書き込みに失敗しました');
 define('MSG03', 'データ習得に失敗しました');
 define('MSG04', 'パスワードが違います');
 define('SUC01', '書き込みに成功しました');

 $debug_flg = true;

 function debug($str) {
     global $debug_flg;
     if(!empty($debug_flg)) {
        error_log('デバッグ：'.$str);
     }
 }
 //-------セッション--------//
session_start();
 
 $err_msg = array();
 $suc_msg = array();
 $message = array();
 $login = null;
 $message_data = null;
 $csv_data = null;
 $limit = null;

  //バリデーション
 function validRequired($str, $key) {
     if(empty($str)) {
         global $err_msg;
         $err_msg[$key] = MSG01;
     }
 }
 //サニタイズ
 function sanitize($str) {
  $clean = preg_replace( '/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $str);
  return $clean;
 }
 //ログインするための関数
 function validLogin($str, $key) {
     global $login;
    if($str === PASS) {
      $_SESSION[$key] = 1;
      $login = $_SESSION[$key];
      return $login;
    } else {
      $err_msg[$key] = MSG04;
      return 0;
    }
 }
 //データベース接続
 function dbConnect() {
     $dsn = 'mysql:dbname=contact;host=localhost;charset=utf8';
     $user = 'root';
     $pass = 'root';
     $options = array(
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
     );

     $dbh = new PDO($dsn, $user, $pass, $options);
     return $dbh;
 }
 
 //クエリー実行
 function queryPost($dbh, $sql, $data) {
     $stmt = $dbh->prepare($sql);
     if(!$stmt->execute($data)) {
         debug('クエリー失敗');       
         return 0;
     }else {
         debug('クエリー成功');
         return $stmt;
     }
 }
//編集データの削除
 function deleteData($dbh, $sql, $str, $key) {
     $stmt = $dbh->prepare($sql);
     $stmt->bindValue($key, $str, PDO::PARAM_INT);
     if (!$stmt->execute()) {
         return 0;
     } else {
         return $stmt;
     }
 }
//編集データを取得
 function editGetData($dbh, $sql, $str, $key) {
     global $message_data;
     $stmt = $dbh->prepare($sql);
     $stmt->bindValue($key, $str, PDO::PARAM_INT);
     $stmt->execute();
     $message_data = $stmt->fetch();
     if (empty($message_data)) {
         return 0;
     } else {
         return $message_data;
     } 
 }
 //制限数ダウンロードする場合の関数
 function limitGetData($dbh, $sql, $str, $key) {
     global $message;
     $stmt = $dbh->prepare($sql);
     $stmt->bindValue($key, $str, PDO::PARAM_INT);
     $stmt->execute();
     $message = $stmt->fetchAll();
     if (empty($message)) {
         return 0;
     } else {
         return $message;
     }
 }
//データベースから情報を習得（制限なし）
 function getData($dbh, $sql) {
     global $message;
     $message = $dbh->query($sql);
     if(empty($message)) {
         return 0;
     }else{
        return $message;
     }
 }

?>