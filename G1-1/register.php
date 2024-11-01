<?php
session_start();
require_once __DIR__ . '/../db.php';  // DB接続ファイルをインクルード

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = connectDB();
        $conn->beginTransaction();  // トランザクションの開始

        $username = $_POST['name'];
        $password = $_POST['number'];
        $password_confirm = $_POST['numberconfirm'];

        // パスワードが一致するか確認
        if ($password !== $password_confirm) {
            throw new Exception("パスワードが一致しません。");
        }

        // ユーザー名とパスワードが空でないか確認
        if (!empty($username) && !empty($password)) {
            // 1. Statusテーブルに新しいレコードを追加
            $status_query = "INSERT INTO Status (trust_level, technical_skill, negotiation_skill, apparance, popularity, total_score) 
                             VALUES (5, 5, 5, 5, 5, 25)";
            $conn->query($status_query);

            // 2. 挿入された status_id を取得
            $status_id = $conn->lastInsertId();

            // 3. Userテーブルにレコードを追加
            $user_query = "INSERT INTO User (user_name, password, status_id, role_id, login, game_situation) 
                           VALUES (:username, :password, :status_id, 1, 'yes', 'started')";
            $stmt = $conn->prepare($user_query);
            $stmt->execute([
                ':username' => $username,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':status_id' => $status_id
            ]);

            // 4. 挿入された user_id を取得し、セッションに保存
            $user_id = $conn->lastInsertId();
            $_SESSION['user_id'] = $user_id;

            // 5. Careerテーブルに初期データを挿入（4月スタート）
            $career_query = "INSERT INTO Career (user_id, current_term, current_months) 
                             VALUES (:user_id, 1, 4)";
            $career_stmt = $conn->prepare($career_query);
            $career_stmt->execute([':user_id' => $user_id]);

            $conn->commit();  // トランザクションをコミット

            // home.php にリダイレクト
            header("Location: ../G2-1/home.php");
            exit;
        } else {
            throw new Exception("ユーザー名とパスワードを入力してください。");
        }
    } catch (Exception $e) {
        // エラーが発生した場合、トランザクションをロールバック
        $conn->rollBack();
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style/register.css">
    <title>登録画面</title>
</head>
<body>
    <div class="register">
        <p class="register-text">このキャラクターに名前をつけてください</p>
        <form action="register.php" method="post">
            <p class="name">名前<br>
                <input class="name-input" type="text" placeholder="名前を入力してください(10文字以内)" name="name" required>
            </p>
            <p class="number">社畜番号<br>
                <input class="number-input" type="password" placeholder="社畜番号を入力してください(6桁)" name="number" required>
            </p>
            <p class="numberconfirm">確認<br>
                <input class="numberconfirm-input" type="password" placeholder="社畜番号を確認してください(6桁)" name="numberconfirm" required>
            </p>
            <button class="register-ok" type="submit">決定</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <button onclick="location.href='../G1-0/index.html'">戻る</button>
    </div>
</body>
</html>
