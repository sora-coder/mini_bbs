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
      $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, created=NOW()');//メッセージを登録する
      $message->execute(array(
        $member['id'],
        $_POST['message']
      ));
      header('location: index.php');//データベースに登録したあと素のindex.phpに移動することで再読込で登録しないようにする($_POSTを空にする)
      exit();
    }
  }

  $posts = $db->query('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC');
    //memberにm postsにp というエイリアスを付けてmemberのidとpostsのmember_idを一致させcreatedで並び替える
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
          <textarea name="message" cols="50" rows="5"></textarea>
          <input type="hidden" name="reply_post_id" value="" />
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
    <p><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?><span class="name">（<?php print(htmlspecialchars($post['name'],ENT_QUOTES)); ?>) </span>[<a href="index.php?res=">Re</a>]</p>
    <p class="day"><a href="view.php?id="><?php print(htmlspecialchars($post['created'],ENT_QUOTES)); ?></a>
<a href="view.php?id=">
返信元のメッセージ</a>
[<a href="delete.php?id="
style="color: #F33;">削除</a>]
    </p>
    </div>
<?php endforeach; ?>

<ul class="paging">
<li><a href="index.php?page=">前のページへ</a></li>
<li><a href="index.php?page=">次のページへ</a></li>
</ul>
  </div>
</div>
</body>
</html>
