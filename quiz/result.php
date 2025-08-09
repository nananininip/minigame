<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php'); exit();
}
$nickname  = $_SESSION['nickname'];

// If we just finished quiz, its points come via GET (or session fallback)
$quizFromGet = isset($_GET['score']) ? (int)$_GET['score'] : (isset($_SESSION['score']) ? (int)$_SESSION['score'] : 0);

// Read current saved row (best so far)
$server = ['quiz'=>0,'waste'=>0,'time_quiz'=>0,'time_waste'=>0,'overall'=>0];
foreach (getLeaderboard() as $r) {
    if (strcasecmp($r['nickname'], $nickname) === 0) { $server = $r; break; }
}

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8" />
<title>ผลลัพธ์</title>
<link rel="stylesheet" href="style.css" />
<style>
  body{background:#f5fce8;margin:0;font-family:'Prompt',sans-serif;color:#234}
  .wrap{max-width:820px;margin:40px auto;padding:24px;background:#fff;border-radius:16px;box-shadow:0 4px 24px #dcf5e8}
  .title{font-size:24px;font-weight:700;margin-bottom:8px;color:#2d4632}
  .pills{display:flex;gap:12px;flex-wrap:wrap;margin:12px 0}
  .pill{padding:10px 14px;border-radius:999px;background:#f5fce8;border:1px solid #e0f0e6}
  .actions{margin-top:20px;display:flex;gap:10px;flex-wrap:wrap}
  .btn{padding:10px 16px;border-radius:10px;border:1px solid #38a169;background:#38a169;color:#fff;font-weight:700;text-decoration:none;display:inline-block}
  .btn.outline{background:#fff;color:#38a169}
  .muted{color:#6b7280}
</style>
</head>
<body>
  <div class="wrap">
    <div class="title">สรุปคะแนน</div>
    <div class="pills">
      <div class="pill">Quiz: <strong id="quizScore"><?= (int)max($quizFromGet, $server['quiz']) ?></strong></div>
      <div class="pill">Waste: <strong id="wasteScore">กำลังดึง…</strong></div>
      <div class="pill">รวม: <strong id="totalScore">–</strong></div>
    </div>
    <div class="muted">ระบบจะอัปเดตคะแนนและเวลาจากเกม Waste ที่เล่นรอบนี้ให้อัตโนมัติ</div>

    <div class="actions">
      <a class="btn" href="leaderboard.php">ดู Leaderboard</a>
      <a class="btn outline" href="menu.php">กลับเมนู</a>
    </div>
  </div>

<script>
(function(){
  // Best values from server row
  const server = {
    quiz:  <?= (int)$server['quiz'] ?>,
    waste: <?= (int)$server['waste'] ?>
  };

  // Local values present only after Waste game
  const localWaste = parseInt(localStorage.getItem('waste_score') || '0', 10) || 0;
  const localWasteTime = parseInt(localStorage.getItem('waste_time') || '0', 10) || 0;

  const quizFromGet = <?= (int)$quizFromGet ?>;
  const quizBest = Math.max(quizFromGet, server.quiz);
  const wasteBest = Math.max(server.waste, localWaste);

  // Show combined best
  document.getElementById('quizScore').textContent  = quizBest;
  document.getElementById('wasteScore').textContent = wasteBest;
  document.getElementById('totalScore').textContent = quizBest + wasteBest;

  // If we have waste info locally, post it once (maps to time_waste on server)
  if (localWaste || localWasteTime) {
    const params = new URLSearchParams();
    params.set('nickname', '<?= h($nickname) ?>');
    params.set('quiz', quizBest);
    params.set('waste', wasteBest);
    params.set('overall', quizBest + wasteBest);
    params.set('time_used', Math.max(0, localWasteTime)); // waste time delta

    fetch('save_leaderboard.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString()
    }).then(()=>{
      localStorage.removeItem('waste_score');
      localStorage.removeItem('waste_time');
    }).catch(()=>{ /* ignore */ });
  }
})();
</script>
</body>
</html>
