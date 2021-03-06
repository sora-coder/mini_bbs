<?php
  session_start();
  require('dbconnect.php');

  if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){//最後の行動からした時間から１時間以下なら
    $_SESSION['time'] = time();//sessionの時間を更新する

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
  }else{
    header('location: login.php');
    exit();
  }

  if(!empty($_POST)){
    if($_POST['message'] !== ''){
      $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_message_id=?, created=NOW()');//メッセージを登録する
      $message->execute(array(
        $member['id'],
        $_POST['message'],
        $_POST['reply_post_id']
      ));
      header('location: index.php');//データベースに登録したあと素のindex.phpに移動することで再読込で登録しないようにする($_POSTを空にする)
      exit();
    }
  }

  $page = $_REQUEST['page'];
  if($_REQUEST['page'] == ''){//ページ指定がないときは1ページ目を表示させる
    $page = 1;
  }
  $page = max($page, 1);//$pageと1を比べて1のほうが大きいときは$pageに1を入れる

  $counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');//メッセージの件数を取得する
  $cnt = $counts->fetch();
  $maxpage = ceil($cnt['cnt'] / 5);//メッセージ件数を5で割って最大ページ数を計算する(ceilは小数点を切り上げる)
  $page = min($page, $maxpage);//$pageと$maxpageを比べて小さい方が$pageに入る

  $start = ($page - 1) * 5;//下のSELECT文で何個目から表示させるか(LIMITの後の?の部分)
  $posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?, 5');
    //memberにm postsにp というエイリアスを付けてmemberのidとpostsのmember_idを一致させcreatedで並び替える
  $posts->bindParam(1, $start, PDO::PARAM_INT);//?の部分に$startを数字として入れる(executeのパラメータとして指定すると文字列として入る)
  $posts->execute();

  if(isset($_REQUEST['res'])){//Reボタンがクリックされたら
    //返信の処理
    $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');
    $response->execute(array($_REQUEST['res']));//resのidを取得する

    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ' . $table['message'];//resのnameとmessageを取得する
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  	<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <dt><?php print(htmlspecialchars($member['name'],ENT_QUOTES)); ?> さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"><?php print(htmlspecialchars($message, ENT_QUOTES)); ?></textarea><!-- Reをクリックしたメッセージを表示させる -->
          <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)); ?>" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>

<?php foreach($posts as $post): ?>       <!-- dbで取得した$postsを順に取得する -->
    <div class="msg">
    <img src="member_picture/<?php print(htmlspecialchars($post['picture'],ENT_QUOTES)); ?>" width="48" height="48" alt="<?php print(htmlspecialchars($post['name'],ENT_QUOTES)); ?> " />
    <p><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?><span class="name">（<?php print(htmlspecialchars($post['name'],ENT_QUOTES)); ?>) </span>[<a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>">Re</a>]</p>
    <p class="day"><a href="view.php?id=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>"><?php print(htmlspecialchars($post['created'],ENT_QUOTES)); ?></a>
  <?php if($post['reply_message_id'] > 0): ?>
    <a href="view.php?id=<?php print(htmlspecialchars($post['reply_message_id'], ENT_QUOTES)); ?>">
    返信元のメッセージ</a>
  <?php endif; ?>

  <?php if($_SESSION['id'] == $post['member_id']): ?>
    [<a href="delete.php?id=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>" style="color: #F33;">削除</a>]
  <?php endif; ?>
    </p>
    </div>
<?php endforeach; ?>
<ul class="paging">
<?php if($page > 1): ?>
  <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
<?php else: ?>
  <li>前のページへ</li>
<?php endif; ?>
<?php if($page < $maxpage): ?>
  <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
<?php else: ?>
  <li>次のページへ</li>
<?php endif; ?>
</ul>
  </div>
</div>
</body>
</html>
