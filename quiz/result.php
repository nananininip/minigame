<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

$nickname = $_SESSION['nickname'];
$gameType = isset($_SESSION['game_type']) ? $_SESSION['game_type'] : 'quiz';
$correct = isset($_SESSION['correct']) ? $_SESSION['correct'] : 0;
$incorrect = isset($_SESSION['incorrect']) ? $_SESSION['incorrect'] : 0;
$currentGameScore = $correct;

saveScore($nickname, $currentGameScore, $gameType); // ✅ this is correct



// Load leaderboard
$leaderboard = getLeaderboard();

// Pull updated scores
$overall_points = isset($leaderboard[$nickname]) ? $leaderboard[$nickname]['overall'] : 0;
$thisGamePoints = isset($leaderboard[$nickname][$gameType]) ? $leaderboard[$nickname][$gameType] : $currentGameScore;

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Quiz Result</title>
    <style>
        body { background-color: #f6f7fa; margin: 0; font-family: 'Prompt', Arial, sans-serif;}
        .navbar { background: rgba(255,255,255,0.95); padding: 18px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.07); width: 100vw; position: fixed; top: 0; left: 0; display: flex; justify-content: space-between; align-items: center; z-index: 10;}
        .navbar-brand { font-size: 2rem; font-weight: 700; color: #e67300; margin-left: 2rem; letter-spacing: 1px;}
        .navbar-buttons { margin-right: 2rem; display: flex; gap: 1rem;}
        .btn-exit { background-color: #dc3545; color: #fff; font-size: 1rem; font-weight: bold; border: 2px solid #0066cc; padding: 9px 20px; border-radius: 6px; cursor: pointer; transition: background 0.16s;}
        .btn-exit:hover { background: #e96b7a; }
        .btn-submit, .btn-newQ { font-size: 1rem; font-weight: bold; padding: 9px 22px; color: #fff; border: 2px solid #0066cc; border-radius: 6px; cursor: pointer; background-color: #e67300; margin-left: 8px; transition: background 0.16s;}
        .btn-submit:hover, .btn-newQ:hover { background: #f59f42; }
        .container { background: #fff; padding: 32px 18px 24px 18px; border-radius: 16px; box-shadow: 0px 4px 24px #D3D3D3bb; max-width: 640px; margin: 110px auto 30px auto; text-align: center;}
        table { margin: 0 auto 8px auto; border-collapse: separate; border-spacing: 0;}
        th, td { border: 3px solid #96D4D4; padding: 13px 20px; text-align: center; background: #fff; min-width: 90px; font-size: 1.02rem;}
        th { font-size: 1.15rem; font-weight: 600; color: #0066cc; background: #eaf6ff;}
        .button-container { display: flex; justify-content: center; margin-top: 20px;}
        @media (max-width: 600px) {.container { margin-top: 120px;} .navbar { flex-direction: column; align-items: flex-start; } .navbar-brand, .navbar-buttons { margin: 0; } table, th, td { font-size: .99rem; }}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Quiz Result</div>
        <div class="navbar-buttons">
            <form action="menu.php" method="post" style="display:inline;">
                <button type="submit" class="btn-submit">Menu</button>
            </form>
            <form action="exit.php" method="post" style="display:inline;">
                <button type="submit" class="btn-exit">Exit</button>
            </form>
        </div>
    </nav>
    <div class="container">
        <h2 style="margin-top:0;color:#e67300;">สรุปคะแนน</h2>
        <table>
            <tr>
                <th>Player</th>
                <th>Correct</th>
                <th>Incorrect</th>
                <th>Points<br>this quiz</th>
                <th>Overall Points</th>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($nickname); ?></td>
                <td><?php echo $correct; ?></td>
                <td><?php echo $incorrect; ?></td>
                <td><?php echo $thisGamePoints; ?></td>
                <td><?php echo $overall_points; ?></td>

            </tr>
        </table>
        <div class="button-container">
            <form action="menu.php" method="post" style="margin-right:12px;">
                <button type="submit" class="btn-newQ">Back to Menu</button>
            </form>
        </div>
    </div>
    <script>sessionStorage.removeItem("timeLeft");</script>
</body>
</html>
