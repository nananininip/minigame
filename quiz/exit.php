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
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Prompt', Arial, sans-serif;
            background: #ecfdf5;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        h1 {
            color: #2ea87c;
            font-size: 2.2em;
            margin-bottom: 0.4em;
            margin-top: 0.5em;
            font-weight: 800;
            letter-spacing: 1px;
            text-align: center;
        }

        .card {
            background: #fff;
            padding: 2em 2.4em;
            border-radius: 20px;
            box-shadow: 0 4px 22px #c1f4d9;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        .row {
            color: #3c7461;
            font-size: 1.1em;
            margin: 0.5em 0;
            font-weight: 500;
        }

        strong {
            color: #14995b;
        }

        .button-container {
            display: flex;
            gap: 1em;
            /* space between buttons */
            justify-content: center;
            /* center horizontally */
            margin-top: 1.5em;
        }

        .button-container form {
            margin: 0;
            /* remove default form margin */
        }

        .btn {
            font-size: 1em;
            font-weight: bold;
            padding: 0.67em 2.1em;
            color: #fff;
            border: none;
            border-radius: 13px;
            cursor: pointer;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
            transition: background 0.17s, box-shadow 0.17s;
            letter-spacing: 1.2px;
        }

        /* Menu button (green-teal) */
        .btn-menu {
            background: linear-gradient(90deg, #8ff3a8 0%, #5ce4c0 100%);
        }

        .btn-menu:hover {
            background: linear-gradient(90deg, #5ce4c0 0%, #8ff3a8 100%);
            box-shadow: 0 6px 28px #b9ffdc;
        }

        /* Exit button (red-orange) */
        .btn-exit {
            background: linear-gradient(90deg, #ff8a8a 0%, #ff4d4d 100%);
        }

        .btn-exit:hover {
            background: linear-gradient(90deg, #ff4d4d 0%, #ff8a8a 100%);
            box-shadow: 0 6px 28px rgba(255, 128, 128, 0.6);
        }
    </style>

</head>

<body>
    <div class="card">
        <h1>ขอบคุณที่เล่น, <?= h($nickname) ?></h1>
        <div class="row">Quiz: <strong><?= (int) $me['quiz'] ?></strong> คะแนน — เวลา
            <strong><?= fmtMMSS($me['time_quiz']) ?></strong>
        </div>
        <div class="row">Waste: <strong><?= (int) $me['waste'] ?></strong> คะแนน — เวลา
            <strong><?= fmtMMSS($me['time_waste']) ?></strong>
        </div>
        <div class="row"><strong>รวม:</strong> <?= (int) $me['overall'] ?> คะแนน — เวลา
            <strong><?= fmtMMSS($total_time) ?></strong>
        </div>
        <div class="button-container">
            <form action="menu.php" method="post">
                <button type="submit" class="btn btn-menu">กลับไปหน้าเมนู</button>
            </form>
            <form action="index.php" method="get">
                <button type="submit" class="btn btn-exit">ออกจากเกม</button>
            </form>
        </div>

    </div>
    <script>sessionStorage.removeItem("timeLeft");</script>
</body>

</html>