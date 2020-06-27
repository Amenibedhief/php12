<?php
ob_start();
// ここから、register.phpと同様
session_start();
if (isset($_SESSION['user']) != '') {
    header('Location: index.php');
}
include_once 'dbconnect.php';
// ここまで、register.phpと同様
?>

<?php
if (isset($_POST['login'])) {
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $mysqli->real_escape_string($_POST['password']);
    // クエリの実行
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $mysqli->query($query);
    if (!$result) {
        echo 'クエリーが失敗しました。'.$mysqli->error;
        $mysqli->close();
        exit();
    }
    // パスワード(暗号化済み）とユーザーIDの取り出し
    while ($row = $result->fetch_assoc()) {
        $db_hashed_pwd = $row['password'];
        $user_id = $row['user_id'];
    }
    // データベースの切断
    $result->close();
    // ハッシュ化されたパスワードがマッチするかどうかを確認
    if (password_verify($password, $db_hashed_pwd)) {
        $_SESSION['user'] = $user_id;
        header('Location: index.php');
        exit;
    } else {
        ?>
		<div class="alert alert-danger" role="alert">メールアドレスとパスワードが一致しません。</div>
	<?php
    }
} ?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=width=device-width, initial-scale=1">
<title>Todo List Login</title>
<link rel="stylesheet" href="style.css">
<!-- Bootstrap読み込み（スタイリングのため） -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
</head>
</head>
<body>
<div class="container">

<?php
// signupがPOSTされたときに下記を実行
if (isset($_POST['signup'])) {
    $username = $mysqli->real_escape_string($_POST['username']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $password = password_hash($password, PASSWORD_BCRYPT);
    // POSTされた情報をDBに格納する
    $query = "INSERT INTO users(username,email,password) VALUES('$username','$email','$password')";
    if ($mysqli->query($query)) {
        ?>
		<div class="alert alert-success" role="alert">登録しました</div>
		<?php
    } else {
        ?>
		<div class="alert alert-danger" role="alert">エラーが発生しました。</div>
		<?php
    }
} ?>

<form method="post">
	<h1>Todo List ログイン</h1>
	<div class="row">
		<div class="col-xs-12 col-lg-12">
			<input type="email"  class="form-control" id="exampleInputEmail1" name="email" placeholder="メールアドレス" required />
		</div>
	</div>
		<br>
	<div class="row">
		<div class="col-xs-12 col-lg-12">
			<input type="password" pattern="^([a-zA-Z0-9]{8,})$" class="form-control" id="exampleInputPassword1" name="password" placeholder="パスワード" required />
		</div>
	</div>
	<br>
	<button type="submit" class="btn btn-primary" name="login">ログインする</button>
	<a href="register.php">会員登録はこちら</a>
</form>

</div>
</body>
</html>
