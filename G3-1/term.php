<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/term.css">
        <title>ターム</title>
    </head>
<body>
    <div class="center-container">
        <div class="content">
            <!-- ここにDBからのそれぞれのテキストを追加 -->
            <h1>１年目終了</h1>
            <p>1ターム目</p>
            <h2>部長⇒課長</h2>
            <h3>信頼度：20<br>
                技術力：10<br>
                交渉力：50<br>
                容　姿：50<br>
                好感度：50<br>
            </h3>

            <button class="roundbutton" id="nextButton">つぎへ</button>
    </div>
    <script>
        document.getElementById('nextButton').addEventListener('click', function () {
            window.location.href = '../G2-1/home.php';
        });
    </script>
</body>
</html>