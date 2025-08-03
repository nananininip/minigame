<?php
require 'functions.php';
$leaderboard = getLeaderboard();

function formatTime($seconds) {
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    return sprintf("%d:%02d", $minutes, $secs);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        th, td { text-align:center; }
        .gold { color:#efb400; font-weight:700; }
        .silver { color:#aaa; font-weight:700; }
        .bronze { color:#b87333; font-weight:700; }
    </style>
</head>
<body>
    <div class="navbar">
        <span class="navbar-brand">Top 10 ผู้เล่น</span>
        <a href="menu.php" class="btn-main">กลับเมนู</a>
    </div>
    <div class="container" style="margin-top:100px;">
        <h2>อันดับผู้เล่น 10 อันดับ</h2>
        <table style="margin:0 auto; font-size:1.13em;">
            <tr>
                <th>อันดับ</th>
                <th>ชื่อ</th>
                <th>คะแนน Quiz</th>
                <th>คะแนน Waste</th>
                <th>รวมทั้งหมด</th>
                <th>เวลาที่ใช้<br>(วินาที / mm:ss)</th>
            </tr>
            <?php
            $rankClass = ['gold','silver','bronze'];
            $idx = 0;
            foreach ($leaderboard as $name => $user): 
                if ($idx >= 10) break;
                $rowClass = $idx < 3 ? $rankClass[$idx] : '';
            ?>
                <tr class="<?php echo $rowClass; ?>">
                    <td><?php echo $idx + 1; ?></td>
                    <td><?php echo htmlspecialchars($name); ?></td>
                    <td><?php echo (int)$user['quiz']; ?></td>
                    <td><?php echo (int)$user['waste']; ?></td>
                    <td><strong><?php echo (int)$user['overall']; ?></strong></td>
                    <td>
                        <?php echo (int)$user['time_used']; ?>
                        <span style="color:#888;">(<?php echo formatTime($user['time_used']); ?>)</span>
                    </td>
                </tr>
            <?php $idx++; endforeach; ?>
        </table>
    </div>
</body>
</html>
