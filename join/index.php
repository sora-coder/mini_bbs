<?php
	session_start();//セッションスタート
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

		if(empty($error)){//$errorが空
			$_SESSION['join'] = $_POST;//joinに$_postの内容を保管する
			header('location: check.php');//check.phpにジャンプ
			exit();
		}
	}

	if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
		$_POST = $_SESSION['join'];
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
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
