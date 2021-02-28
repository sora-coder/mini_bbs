<?php
    session_start();

    $_SESSION = array();//セッション変数を全て解除する
    if(ini_set('session.use_cookies')){//sessionにcookieを使うかどうか設定するファイル
        $params = session_get_cookie_params();//sessionのパラーメータを入れる
        setcookie(session_name() . '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);//cookieの有効期限を切る
    }
    session_destroy();//sessionを完全に消す

    setcookie('email', '', time() - 3600);//emailにからの値を入れて有効期限を消す

    header('location: login.php');
    exit();
?>
