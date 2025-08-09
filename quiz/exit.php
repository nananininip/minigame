<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}
$nickname = $_SESSION['nickname'];

// Find the player's row in the list-style leaderboard
$me = [
    'quiz' => 0,
    'waste' => 0,
    'overall' => 0,
    'time_quiz' => 0,
    'time_waste' => 0
];
foreach (getLeaderboard() as $r) {
    if (strcasecmp($r['nickname'], $nickname) === 0) {
        $me = $r;
        break;
    }
}
$total_time = $me['time_quiz'] + $me['time_waste'];

// (Optional) clear session after we read the values
// $_SESSION = []; session_destroy();

function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ออกจากเกม</title>
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background: #f5fce8;
            color: #234;
            margin: 0;
            padding: 40px
        }

        .card {
            max-width: 720px;
            margin: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px #dcf5e8;
            padding: 24px
        }

        h1 {
            margin-top: 0;
            color: #38a169
        }

        .row {
            margin: 8px 0
        }

        .muted {
            color: #6b7280
        }

        .btn {
            margin-top: 16px;
            padding: 10px 16px;
            border: 1px solid #38a169;
            border-radius: 12px;
            background: #38a169;
            color: #fff;
            font-weight: 700;
            cursor: pointer
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>ขอบคุณที่เล่น, <?= h($nickname) ?></h1>
        <div class="row">Quiz: <strong><?= (int) $me['quiz'] ?></strong> คะแนน — เวลา
            <strong><?= fmtMMSS($me['time_quiz']) ?></strong></div>
        <div class="row">Waste: <strong><?= (int) $me['waste'] ?></strong> คะแนน — เวลา
            <strong><?= fmtMMSS($me['time_waste']) ?></strong></div>
        <div class="row"><strong>รวม:</strong> <?= (int) $me['overall'] ?> คะแนน — เวลา
            <strong><?= fmtMMSS($total_time) ?></strong></div>
        <form action="menu.php" method="post">
            <button type="submit" class="btn">เริ่มเกมใหม่</button>
        </form>
        <form action="index.php" method="get">
            <button type="submit" class="btn-newQ">กลับไปหน้าแรก</button>
        </form>
    </div>
    <script>sessionStorage.removeItem("timeLeft");</script>
</body>

</html>