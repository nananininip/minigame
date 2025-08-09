<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php'); exit();
}
$nickname  = $_SESSION['nickname'];

// Score sent from quiz.php (if we just finished quiz)
$quizFromGet = isset($_GET['score']) ? (int)$_GET['score'] : (isset($_SESSION['score']) ? (int)$_SESSION['score'] : 0);

// Read current best-from-server
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
    <div class="muted">ระบบจะอัปเดตตารางคะแนนและเวลาที่ใช้จากรอบ Waste ให้อัตโนมัติ</div>

    <div class="actions">
      <a class="btn" href="leaderboard.php">ดู Leaderboard</a>
      <a class="btn outline" href="menu.php">กลับเมนู</a>
    </div>
  </div>

<script>
(function(){
  // Server values (best so far)
  const server = {
    quiz:  <?= (int)$server['quiz'] ?>,
    waste: <?= (int)$server['waste'] ?>,
  };

  // Local values from this session (present only after WASTE)
  const localWaste = parseInt(localStorage.getItem('waste_score') || '0', 10) || 0;
  const localWasteTime = parseInt(localStorage.getItem('waste_time') || '0', 10) || 0;

  // Quiz value can come from GET (quiz run) or server (previous best)
  const quizFromGet = <?= (int)$quizFromGet ?>;
  const quizBest = Math.max(quizFromGet, server.quiz);

  // Waste to display = best(server vs local)
  const wasteBest = Math.max(server.waste, localWaste);

  // Update UI
  document.getElementById('wasteScore').textContent = wasteBest;
  document.getElementById('quizScore').textContent  = quizBest;
  document.getElementById('totalScore').textContent = quizBest + wasteBest;

  // If we just finished waste (have local info), post it once
  if (localWaste || localWasteTime) {
    const params = new URLSearchParams();
    params.set('nickname', '<?= h($nickname) ?>');
    params.set('quiz', quizBest);                 // send current best quiz
    params.set('waste', Math.max(server.waste, localWaste)); // best waste
    params.set('overall', quizBest + Math.max(server.waste, localWaste));
    params.set('time_used', localWasteTime);      // counts into waste time bucket on server

    fetch('save_leaderboard.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString()
    }).then(()=>{
      // Clear local so we don't re-post on refresh
      localStorage.removeItem('waste_score');
      localStorage.removeItem('waste_time');
    }).catch(()=>{ /* ignore */ });
  }
})();
</script>
</body>
</html>
