<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

$nickname  = $_SESSION['nickname'];
// Quiz points come from quiz.php redirect (?score=...)
$quizScore = isset($_GET['score']) ? (int)$_GET['score'] : (isset($_SESSION['score']) ? (int)$_SESSION['score'] : 0);
$wrong     = isset($_GET['wrong']) ? (int)$_GET['wrong'] : 0;

// We will POST waste score & waste time from localStorage via JS below.
// Important: quiz time was already added in quiz.php, so we only add waste time here.
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>ผลลัพธ์</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .result-wrap{max-width:820px;margin:40px auto;padding:24px;background:#fff;border-radius:16px;box-shadow:0 4px 24px #dcf5e8;font-family:'Prompt',sans-serif}
    .result-title{font-size:24px;font-weight:700;margin-bottom:12px;color:#2d4632}
    .score-box{display:flex;gap:16px;flex-wrap:wrap;margin:12px 0}
    .pill{padding:10px 14px;border-radius:999px;background:#f5fce8;border:1px solid #e0f0e6}
    .actions{margin-top:20px;display:flex;gap:10px;flex-wrap:wrap}
    .btn{padding:10px 16px;border-radius:10px;border:1px solid #38a169;background:#38a169;color:#fff;font-weight:700;text-decoration:none;display:inline-block}
    .btn.outline{background:#fff;color:#38a169}
    .muted{color:#6b7280}
  </style>
</head>
<body>
  <div class="result-wrap">
    <div class="result-title">สรุปคะแนน</div>
    <div class="score-box">
      <div class="pill">Quiz: <strong id="quizScore"><?php echo $quizScore; ?></strong></div>
      <div class="pill">Waste: <strong id="wasteScore">กำลังดึง…</strong></div>
      <div class="pill">รวม: <strong id="totalScore">–</strong></div>
    </div>
    <div class="muted">ระบบจะอัปเดตตารางคะแนนและเวลาที่ใช้จากรอบ Waste ให้อัตโนมัติ</div>

    <div class="actions">
      <a class="btn" href="leaderboard.php">ดู Leaderboard</a>
      <a class="btn outline" href="menu.php">กลับเมนู</a>
    </div>
  </div>

  <script>
    (function(){
      const quiz = parseInt(document.getElementById('quizScore').textContent, 10) || 0;
      const waste = parseInt(localStorage.getItem('waste_score') || '0', 10) || 0;
      const wasteTime = parseInt(localStorage.getItem('waste_time') || '0', 10) || 0;

      document.getElementById('wasteScore').textContent = waste;
      document.getElementById('totalScore').textContent = quiz + waste;

      // Send merged result:
      // - quiz points (already saved with time in quiz.php)
      // - waste points
      // - overall (quiz + waste)
      // - time_used = wasteTime ONLY (avoid double-counting quiz time)
      const params = new URLSearchParams();
      params.set('nickname', '<?php echo htmlspecialchars($nickname, ENT_QUOTES, "UTF-8"); ?>');
      params.set('quiz', quiz);
      params.set('waste', waste);
      params.set('overall', quiz + waste);
      params.set('time_used', wasteTime);

      fetch('save_leaderboard.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
      }).then(()=> {
        // clear local waste values after save
        localStorage.removeItem('waste_score');
        localStorage.removeItem('waste_time');
      }).catch(()=>{ /* no-op */ });
    })();
  </script>
</body>
</html>
