<?php
	session_start();//セッションスタート
	require('../dbconnect.php');
	if(!empty($_POST)){//何か入力された状態なら（初期画面じゃなければ）
		if($_POST['name'] === ''){// nameが空
			$error['name'] = 'blank';
		}
		if($_POST['email'] === ''){//emailが空
			$error['email'] = 'blank';
		}
		if(strlen($_POST['password']) < 4){//passwordが4文字未満
			$error['password'] = 'length';
		}
		if($_POST['password'] === ''){//passwordが空
			$error['password'] = 'blank';
		}
		$filename = $_FILES['image']['name'];
		if(!empty($filename)){//$filenameがある
			$ext = substr($filename, -3);//filenameの最後から3文字を$extに代入する
			if($ext != 'jpg' && $ext != 'gif' && $ext != 'png'){
				$error['image'] = 'type';
			}
		}
		//アカウントの重複のチェック
		if(empty($error)){
			$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
			$member->execute(array($_POST['email']));
			$record = $member->fetch();
			if($record['cnt'] > 0){//指定したemailの件数(cnt)が1件以上あったら
				$error['email'] = 'duplicate';//duplicate=重複
			}
		}
		if(empty($error)){//$errorが空
			$image = date('YmdHis') . $_FILES['image']['name'];//表示例) 20210225132801myface.png
			move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);//['tmp_name']=一時的にアップロードされている場所から'../member_picture/' . $imageに移して保存する
			$_SESSION['join'] = $_POST;//joinに$_postの内容を保管する
			$_SESSION['join']['image'] = $image;//joinのimageに$image(ファイルの名前)を保管する
			header('location: check.php');//check.phpにジャンプ
			exit();
		}
	}

	if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){//actionがrewriteかつセッションのjoinに入力があれば
		$_POST = $_SESSION['join'];//$_POSTに$_SESSION['join']の内容を代入する
	}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>次のフォームに必要事項をご記入ください。</p>
<form action="" method="post" enctype="multipart/form-data">
	<!-- enctype="multipart/form-data" ファイルのアップロード時はform属性に付与する -->
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'],ENT_QUOTES)); ?>" />
			<?php if($error['name'] === 'blank'): ?>
			<p class="error">＊ニックネームを入力してください</p>
			<?php endif; ?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['email'],ENT_QUOTES)); ?>" />
			<?php if($error['email'] === 'blank'): ?>
			<p class="error">＊メールアドレスを入力してください</p>
			<?php endif; ?>
			<?php if($error['email'] === 'duplicate'): ?>
			<p class="error">＊指定されたメールアドレスは、既に登録されています</p>
			<?php endif; ?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" value="<?php print(htmlspecialchars($_POST['password'],ENT_QUOTES)); ?>" />
			<?php if($error['password'] === 'blank'): ?>
			<p class="error">＊パスワードを入力してください</p>
			<?php endif; ?>
			<?php if($error['password'] === 'length'): ?>
			<p class="error">＊パスワードは４文字以上で入力してください</p>
			<?php endif; ?>
        </dd>
		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />
				<!-- type="file"で指定すると$_FILESに入る -->
			<?php if($error['image'] === 'type'): ?>
			<p class="error">＊写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
			<?php endif; ?>
			<?php if(!empty($error)): ?>
			<p class="error">恐れ入りますが。画像を改めて指定してください</p>
			<?php endif; ?>
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
