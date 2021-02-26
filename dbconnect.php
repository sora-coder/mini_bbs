<?php
try{
    $db = new PDO('mysql:dbname=mini_bbs;host=localhost;charsetutf8', 'root', 'root');//db接続
} catch(PDOException $e) {
    print('DB接続エラー：' . $e->getMessage());//db接続できなかったらエラーメッセージを表示
}
