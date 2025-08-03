<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

// Quiz score: from session (or query string as fallback)
$quizScore = isset($_SESSION['score']) ? $_SESSION['score'] : (isset($_GET['score']) ? intval($_GET['score']) : 0);
$nickname = $_SESSION['nickname'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ผลคะแนนรวม</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="result-card">
        <h1>คะแนนของคุณ</h1>
        <div>ชื่อผู้เล่น: <strong><?php echo htmlspecialchars($nickname); ?></strong></div>
        <div>คะแนน Quiz: <span id="quiz-score"><?php echo $quizScore; ?></span></div>
        <div>คะแนน Waste Game: <span id="waste-score"></span></div>
        <div><b>คะแนนรวม:</b> <span id="total-score"></span></div>
        <br>
        <a href="leaderboard.php" class="btn-main">ดูอันดับ</a>
        <a href="menu.php" class="btn-alt">กลับเมนู</a>
    </div>
    <script>
    // Get waste_score from localStorage (from waste.html's script)
    window.onload = function() {
        let waste = localStorage.getItem('waste_score') || 0;
        document.getElementById('waste-score').textContent = waste;
        let quiz = <?php echo $quizScore; ?>;
        let total = parseInt(quiz) + parseInt(waste);
        document.getElementById('total-score').textContent = total;
        // Optional: clear waste_score if you want to reset for next game
        // localStorage.removeItem('waste_score');
    };
    </script>
</body>
</html>
