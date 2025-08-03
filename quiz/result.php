<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

// Get quiz score from session or query string
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
    window.onload = function () {
        // Read waste score from localStorage (set by waste game)
        let waste = localStorage.getItem('waste_score') || 0;
        document.getElementById('waste-score').textContent = waste;
        let quiz = <?php echo $quizScore; ?>;
        let total = parseInt(quiz) + parseInt(waste);
        document.getElementById('total-score').textContent = total;

        // --- SEND TO PHP TO UPDATE LEADERBOARD ---
        fetch('save_leaderboard.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'nickname=' + encodeURIComponent('<?php echo $nickname; ?>') +
                '&quiz=' + quiz +
                '&waste=' + waste +
                '&overall=' + total
        });

        // Clear waste_score for next time (optional but recommended)
        localStorage.removeItem('waste_score');
    };
    </script>
</body>
</html>
