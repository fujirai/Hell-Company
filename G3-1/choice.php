<?php
session_start();
require_once __DIR__ . '/../db.php';   // DB接続ファイルをインクルード

if (!isset($_SESSION['user_id'])) {
    header("Location: ../G1-0/index.html");
    exit;
}

try {
    $conn = connectDB();
    $user_id = $_SESSION['user_id'];

    // ユーザーデータとステータスを取得
    $query = "SELECT User.user_name, Status.trust_level, Status.technical_skill, 
                     Status.negotiation_skill, Status.appearance, Status.popularity, 
                     Status.total_score, User.role_id 
              FROM User 
              JOIN Status ON User.status_id = Status.status_id 
              WHERE User.user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "ユーザー情報が見つかりません。";
        exit;
    }

     // Roleテーブルから全役職を取得
     $role_query = "SELECT * FROM Role ORDER BY repuired_status DESC";
     $role_stmt = $conn->query($role_query);
     $roles = $role_stmt->fetchAll();
 
     $new_role_id = $user['role_id'];  // 役職の初期化（デフォルトは現在の役職）
 
     foreach ($roles as $role) {
         if ($user['total_score'] >= $role['repuired_status'] &&
             $user['trust_level'] >= $role['repuired_trust'] &&
             $user['technical_skill'] >= $role['repuired_technical'] &&
             $user['negotiation_skill'] >= $role['repuired_negotiation'] &&
             $user['appearance'] >= $role['repuired_appearance'] &&
             $user['popularity'] >= $role['repuired_popularity']
         ) {
             $new_role_id = $role['role_id'];
             break;
         }
     }


    // Careerテーブルからcurrent_termとcurrent_monthsを取得
    $career_query = "SELECT current_term, current_months FROM Career WHERE user_id = :user_id";
    $career_stmt = $conn->prepare($career_query);
    $career_stmt->execute([':user_id' => $user_id]);
    $career = $career_stmt->fetch();

    // 現在の役職を取得
    $current_role_query = "SELECT role_name, role_explanation FROM Role WHERE role_id = :role_id";
    $current_role_stmt = $conn->prepare($current_role_query);
    $current_role_stmt->execute([':role_id' => $new_role_id]);
    $current_role = $current_role_stmt->fetch();
    


} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css\choice.css">
        <title>選択イベント</title>
    </head>
<body>
    <div id="popup" class="popup">
        <h2><p><span class="rotate-text">ステータス</span></p></h2>
        <!-- ここにDBからステータスを追加 -->
        <p><h2><?php echo htmlspecialchars($current_role['role_name']); ?></h2></p>
        <p><h1><?php echo htmlspecialchars($user['user_name']); ?></h1></p>
        <p><h3>
            信頼度：<br>
            技術力：<br>
            交渉力：<br>
            容　姿：<br>
            好感度：<br>
        </h3></p>
    </div>
    <!--テキスト-->
    <div class="fixed-title">
    </div>
    <div class="footer-box">
        <h2>選択イベントです。なにか選択してください。</h2>
    </div>
   <!--選択肢-->
    <div class="options">
        <button class="option-button" onclick="updateFooter(1)">1：ああああああああああああああああああ</button>
        <button class="option-button" onclick="updateFooter(2)">2：選択肢2</button>
        <button class="option-button" onclick="updateFooter(3)">3：選択肢3</button>
        <button class="option-button" onclick="updateFooter(4)">4：選択肢4</button>
    </div>
    <div id="modo" class="modo" style="display: none;">
        <button id="backButton">戻る</button>
    </div>

    <!-- <div id="nextTermButton" class="nextTermButton" style="display: none;">
        <button id="next-term" class="game-button">次のタームへ</button>
    </div> -->

<script>
        var popup = document.getElementById("popup");
        popup.addEventListener("click",function(){
            popup.classList.toggle("show");
        })

        document.addEventListener("DOMContentLoaded", function () {
            const textElement = document.querySelector(".footer-box h2");
            const optionsElement = document.querySelector(".options");
            const text = textElement.textContent;
            textElement.textContent = "";
            let i = 0;

            function type() {
                if (i < text.length) {
                    textElement.textContent += text.charAt(i);
                    i++;
                    setTimeout(type, 25); // 25msごとに1文字ずつ表示
                } else {
                    showOptions(); // テキストが全て表示されたら選択肢を表示
                }
            }

            function showOptions() {
                optionsElement.style.opacity = "1"; // フェードイン表示
            }

            type();
        });

        function updateFooter(option) {
            const footerBox = document.querySelector('.footer-box h2');
            switch (option) {
                case 1:
                    footerBox.textContent = '選択肢1が選ばれました。あ～あ';
                    break;
                case 2:
                    footerBox.textContent = '選択肢2が選ばれました。あ～あ';
                    break;
                case 3:
                    footerBox.textContent = '選択肢3が選ばれました。あ～あ';
                    break;
                case 4:
                    footerBox.textContent = '選択肢4が選ばれました。あ～あ';
                    break;
            }
            const buttons = document.querySelectorAll('.option-button');
            buttons.forEach(button => button.style.display = 'none');

            // 1秒後にhome.htmlに遷移
            setTimeout(() => {
                        const modo = document.getElementById("modo");
                        modo.style.display = "block";
                    }, 1000);

            // 戻るボタンのクリックイベント
            const backButton = document.getElementById("backButton");
            backButton.addEventListener("click", function () {
                window.location.href = '../G2-1/home.php';
            });

        }
</script>
</body>
</html>