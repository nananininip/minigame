<?php
session_start();
require '../utils/functions.php';

if (!isset($_SESSION['nickname'])) {
  header('Location: ../utils/index.php');
  exit();
}
$nickname = $_SESSION['nickname'];

$quizFromGet = isset($_GET['score']) ? (int) $_GET['score'] : (isset($_SESSION['score']) ? (int) $_SESSION['score'] : 0);

// Find this user's BEST saved row from full history (for display only)
$server = ['quiz' => 0, 'waste' => 0, 'time_quiz' => 0, 'time_waste' => 0, 'overall' => 0];
$all = getLeaderboardAll();
foreach ($all as $r) {
  if (strcasecmp($r['nickname'], $nickname) !== 0)
    continue;
  $curT = $server['time_quiz'] + $server['time_waste'];
  $newT = $r['time_quiz'] + $r['time_waste'];
  $replace = false;
  if ($r['overall'] > $server['overall'])
    $replace = true;
  elseif ($r['overall'] == $server['overall']) {
    if ($newT < $curT)
      $replace = true;
    elseif ($newT == $curT && $r['quiz'] > $server['quiz'])
      $replace = true;
  }
  if ($replace)
    $server = $r;
}

function h($s)
{
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8" />
  <title>Green Quest: Hi-Trust Mini Games</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* === Green Quest — Summary Page (FULL CSS) === */

    /* Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@600;700;800&family=Prompt:wght@400;600;700&display=swap');

    /* Theme tokens */
    :root {
      --bg: #fffae2;
      --panel: #ffffff;
      --ink: #2d4632;
      --muted: #5a7a63;
      --brand: #209765;
      --brand-2: #53d3a1;
      --brand-3: #b3ffc9;
      --line: #e8f9ef;
      --shadow-lg: 0 18px 44px rgba(32, 151, 101, .16);
      --shadow-md: 0 10px 26px rgba(32, 151, 101, .12);
    }

    /* Base */
    * {
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%;
    }

    body {
      margin: 0;
      font-family: 'Prompt', system-ui, -apple-system, Segoe UI, Arial, sans-serif;
      color: var(--ink);
      background:
        radial-gradient(28rem 28rem at 12% 8%, rgba(138, 217, 232, .28) 0%, rgba(138, 217, 232, 0) 55%),
        radial-gradient(24rem 24rem at 85% 20%, rgba(122, 203, 150, .26) 0%, rgba(122, 203, 150, 0) 52%),
        linear-gradient(180deg, var(--bg) 0%, #fffdf4 100%);
      padding: 32px 12px 64px;
    }

    /* Card */
    .wrap {
      width: min(840px, 92vw);
      margin: clamp(16px, 6vh, 48px) auto;
      padding: clamp(20px, 3vw, 28px);
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 22px;
      box-shadow: var(--shadow-lg);
      position: relative;
      overflow: hidden;
      isolation: isolate;
    }

    /* soft top glow */
    .wrap::before {
      content: "";
      position: absolute;
      inset: -1px -1px auto -1px;
      height: 46%;
      background:
        radial-gradient(60% 100% at 20% 0%, rgba(179, 255, 201, .45), transparent 70%),
        radial-gradient(60% 100% at 100% 0%, rgba(138, 217, 232, .35), transparent 70%);
      z-index: -1;
    }

    /* Title */
    .title {
      font-family: 'Kanit', 'Prompt', sans-serif;
      font-size: clamp(1.25rem, 2.2vw, 1.6rem);
      font-weight: 800;
      color: var(--brand);
      letter-spacing: .2px;
      margin: 2px 0 14px;
      display: inline-block;
      border-bottom: 3px solid var(--brand-3);
      padding-bottom: 6px;
    }

    /* Score pills */
    .pills {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
      margin: 12px 0 10px;
    }

    @media (max-width: 680px) {
      .pills {
        grid-template-columns: 1fr;
      }
    }

    .pill {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      border-radius: 16px;
      background: linear-gradient(0deg, #f7fff8, #ffffffcc);
      border: 1px solid #e0f3ea;
      box-shadow: 0 8px 22px rgba(32, 151, 101, .08) inset;
      font-weight: 600;
      color: var(--muted);
    }

    .pill::before {
      content: "";
      width: 34px;
      height: 34px;
      flex: 0 0 34px;
      border-radius: 11px;
      background: #ecfff4;
      border: 1px solid #d5f7e5;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .8);
    }

    .pill strong {
      font-family: 'Kanit', 'Prompt', sans-serif;
      font-size: clamp(1.1rem, 2vw, 1.4rem);
      font-weight: 800;
      color: var(--brand);
      margin-left: 2px;
    }

    /* cute emoji badges by position (no HTML changes needed) */
    .pills .pill:nth-child(1)::before {
      content: "🧠";
      display: grid;
      place-items: center;
      font-size: 18px;
    }

    .pills .pill:nth-child(2)::before {
      content: "🗑️";
      display: grid;
      place-items: center;
      font-size: 18px;
    }

    .pills .pill:nth-child(3)::before {
      content: "⭐️";
      display: grid;
      place-items: center;
      font-size: 18px;
    }

    .pills .pill:nth-child(3) strong {
      color: #1a8f62;
    }

    .muted {
      color: #94a3b8;
      font-size: .95rem;
      margin-top: 2px;
    }

    /* Buttons row */
    .actions {
      margin-top: 18px;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .btn {
      --btn-h: 46px;
      height: var(--btn-h);
      padding: 0 18px;
      border-radius: 12px;
      border: 1px solid #2aa06b;
      background: linear-gradient(135deg, var(--brand), var(--brand-2));
      color: #fff;
      text-decoration: none;
      font-weight: 800;
      letter-spacing: .2px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 24px rgba(32, 163, 106, .28), inset 0 -2px 0 rgba(0, 0, 0, .08);
      transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
      position: relative;
      overflow: hidden;
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 16px 28px rgba(32, 163, 106, .32);
      filter: saturate(1.06);
    }

    .btn:active {
      transform: translateY(0) scale(.98);
      box-shadow: 0 8px 16px rgba(32, 163, 106, .25);
    }

    .btn::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(110deg, transparent 35%, rgba(255, 255, 255, .4) 48%, transparent 60%);
      transform: translateX(-200%);
      pointer-events: none;
    }

    .btn:hover::after {
      animation: shine 1000ms ease forwards;
    }

    .btn.outline {
      background: #fff;
      color: var(--brand);
      border-color: #2aa06b;
      box-shadow: 0 6px 16px rgba(32, 163, 106, .12);
    }

    .btn.outline:hover {
      filter: none;
      box-shadow: 0 10px 20px rgba(32, 163, 106, .16);
    }

    @keyframes shine {
      to {
        transform: translateX(200%);
      }
    }

    /* Small niceties on selection/focus */
    a:focus-visible,
    .btn:focus-visible {
      outline: 3px solid rgba(117, 232, 176, .45);
      outline-offset: 2px;
    }
  </style>

</head>

<body>
  <div class="wrap">
    <div class="title">สรุปคะแนน</div>
    <div class="pills">
      <div class="pill">ควิซ: <strong id="quizScore"><?= (int) max($quizFromGet, $server['quiz']) ?></strong></div>
      <div class="pill">เกมแยกขยะ: <strong id="wasteScore">กำลังดึง…</strong></div>
      <div class="pill">คะแนนรวม: <strong id="totalScore">–</strong></div>
    </div>
    <!-- <div class="muted">ระบบจะอัปเดตคะแนนและเวลาจากเกม Waste ที่เล่นรอบนี้ให้อัตโนมัติ</div> -->

    <div class="actions">
      <a class="btn" href="../leaderboard/leaderboard.php">อันดับผู้เล่น</a>
      <a class="btn outline" href="../utils/menu.php">กลับเมนู</a>
    </div>
  </div>

  <script>
    (function () {
      // Server’s best values (for display only)
      const server = {
        quiz: <?= (int) ($server['quiz'] ?? 0) ?>,
        waste: <?= (int) ($server['waste'] ?? 0) ?>
      };

      // Values from this run (URL/session)
      const quizFromGet = <?= (int) $quizFromGet ?>;

      // Values from localStorage (set by games)
      const localWaste = parseInt(localStorage.getItem('waste_score') || '0', 10) || 0;
      const localWasteTime = parseInt(localStorage.getItem('waste_time') || '0', 10) || 0;
      const localQuizTime = parseInt(localStorage.getItem('quiz_time') || '0', 10) || 0;

      // For display
      const quizBest = Math.max(quizFromGet, server.quiz);
      const wasteBest = Math.max(localWaste, server.waste);

      document.getElementById('quizScore').textContent = quizBest;
      document.getElementById('wasteScore').textContent = wasteBest;
      document.getElementById('totalScore').textContent = quizBest + wasteBest;

      // Send to server:
      // - Quiz was already saved accurately by quiz.php (server-side).
      // - Only send Quiz here if we *actually* have a measured local quiz_time (otherwise we’d create a 0s tie record).
      const hasNewWaste = (localWaste > 0 || localWasteTime > 0);
      const hasNewQuiz = (localQuizTime > 0); // <-- important: require real time

      if (hasNewWaste || hasNewQuiz) {
        const params = new URLSearchParams();
        params.set('nickname', '<?= h($nickname) ?>');
        params.set('quiz', hasNewQuiz ? quizBest : 0);
        params.set('waste', hasNewWaste ? wasteBest : 0);
        params.set('time_quiz', hasNewQuiz ? localQuizTime : 0);
        params.set('time_waste', hasNewWaste ? localWasteTime : 0);

        fetch('../leaderboard/save_leaderboard.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: params.toString()
        }).then(() => {
          // clear only what we used
          if (hasNewWaste) {
            localStorage.removeItem('waste_score');
            localStorage.removeItem('waste_time');
          }
          if (hasNewQuiz) {
            localStorage.removeItem('quiz_time');
          }
        }).catch(() => { /* ignore */ });
      }
    })();
  </script>


</body>

</html>