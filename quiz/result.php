<?php
session_start();
$score = isset($_GET['score']) ? intval($_GET['score']) : 0;
$wrong = isset($_GET['wrong']) ? intval($_GET['wrong']) : 0;
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Quiz Result</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navbar">
        <span class="navbar-brand">Quiz Result</span>
    </div>
    <div class="result-card">
        <h2>คะแนนของคุณ: <span class="highlight"><?php echo $score; ?></span></h2>
        <div style="margin-bottom: 12px;">
            <span style="color:#219a2a;font-weight:700;">ถูกต้อง: <?php echo $score; ?> ข้อ</span>
            &nbsp; | &nbsp;
            <span style="color:#e43c3c;font-weight:700;">ผิด: <?php echo $wrong; ?> ข้อ</span>
        </div>
        <?php
        if ($score == 5) {
            $msg = "เยี่ยมมาก! คุณตอบถูกทุกข้อ 🎉";
        } elseif ($score >= 3) {
            $msg = "เก่งมาก! ลองเล่นอีกครั้งเพื่อคะแนนที่สมบูรณ์!";
        } else {
            $msg = "อย่ายอมแพ้! ฝึกฝนต่อไปนะ!";
        }
        ?>
        <div style="margin-bottom:16px;font-weight:600;"><?php echo $msg; ?></div>
        <p>คุณต้องการเล่นเกมถัดไปหรือไม่?</p>
        <div class="menu-buttons">
            <a href="/minigame/game/waste.html" class="btn-main">ไปเล่นเกมถัดไป</a>
            <a href="menu.php" class="btn-alt">กลับสู่เมนูหลัก</a>
        </div>
    </div>

</body>

</html>