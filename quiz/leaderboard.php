<?php
require 'functions.php';
$leaderboard = getLeaderboard();

function formatTime($seconds)
{
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
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background: #f5fce8;
            margin: 0;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1em 2em;
            background: #fff;
            color: #38a169;
            font-weight: 700;
            font-size: 1.16em;
            box-shadow: 0 2px 16px #dcf5e8;
            min-height: 56px;
            border-radius: 0 0 16px 16px;
        }

        .navbar-brand {
            font-size: 1.08em;
            font-weight: 700;
            letter-spacing: 1px;
            color: #40b53a;
            opacity: 1;
        }

        .btn-main {
            background: linear-gradient(90deg, #baffc9 0, #b1ffe9 100%);
            color: #24995c;
            border: none;
            border-radius: 9px;
            font-weight: 600;
            padding: 0.38em 1.02em;
            font-size: 0.95em;
            cursor: pointer;
            box-shadow: 0 2px 7px #bcf7d1;
            margin-left: 8px;
            transition: background 0.16s, color 0.16s;
        }

        .btn-main:hover {
            background: linear-gradient(90deg, #97f8a1 0, #a5fae5 100%);
            color: #24995c;
        }

        .container {
            margin: 120px auto 0 auto;
            max-width: 640px;
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 8px 36px #dcf5e8;
            padding: 1.6em 1.2em 1.7em 1.2em;
        }

        h2 {
            text-align: center;
            margin-bottom: 0em;
            font-size: 1.18em;
            letter-spacing: 1px;
            color: #2d8957;
            font-weight: 700;
        }

        table {
            border-collapse: collapse;
            min-width: 100%;
            width: 100%;
            background: transparent;
            border-radius: 14px;
        }

        th,
        td {
            text-align: center;
            font-size: 1.04em;
            border: none;
        }

        th {
            background: #e5ffe6;
            color: #37b24d;
            font-weight: 700;
            padding: 0.85em 0.7em;
            letter-spacing: 0.3px;
            border-bottom: 2px solid #b6edc8;
        }

        td {
            background: #fff;
            padding: 0.7em 0.4em;
            border-bottom: 1px solid #e7f6e6;
            color: #247846;
            transition: background 0.12s;
        }

        tr.gold {
            background: linear-gradient(90deg, #f5ffd5 80%, #ebffe8 100%) !important;
        }

        tr.silver {
            background: linear-gradient(90deg, #e8fff2 70%, #e6f8fd 100%) !important;
        }

        tr.bronze {
            background: linear-gradient(90deg, #ffeedd 70%, #e9ffed 100%) !important;
        }

        .gold {
            color: #c6ad16 !important;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .silver {
            color: #70bda6 !important;
            font-weight: 800;
        }

        .bronze {
            color: #e6ac5a !important;
            font-weight: 800;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 700px) {
            .container {
                max-width: 98vw;
                padding: 1em 0.3em;
            }

            table,
            th,
            td {
                font-size: 0.97em;
            }
        }

        ::-webkit-scrollbar {
            width: 10px;
            background: #eee;
        }

        ::-webkit-scrollbar-thumb {
            background: #d0ffe2;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <span class="navbar-brand">Top 10 ผู้เล่น</span>
        <a href="menu.php" class="btn-main">กลับเมนู</a>
    </div>
    <div class="container">
        <h2>อันดับผู้เล่น 10 อันดับ</h2>
        <div style="overflow-x:auto;">
            <table>
                <tr>
                    <th>อันดับ</th>
                    <th>ชื่อ</th>
                    <th>Quiz</th>
                    <th>Waste</th>
                    <th>รวม</th>
                    <th>เวลาที่ใช้<br>(วินาที / mm:ss)</th>
                </tr>
                <?php
                $rankClass = ['gold', 'silver', 'bronze'];
                $idx = 0;
                if (empty($leaderboard)) {
                    echo '<tr><td colspan="6">ยังไม่มีข้อมูลผู้เล่น</td></tr>';
                } else {
                    foreach ($leaderboard as $name => $user):
                        if ($idx >= 10)
                            break;
                        $rowClass = $idx < 3 ? $rankClass[$idx] : '';
                        ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td class="<?php echo $rowClass; ?>"><?php echo $idx + 1; ?></td>
                            <td><?php echo htmlspecialchars($name); ?></td>
                            <td><?php echo (int) $user['quiz']; ?></td>
                            <td><?php echo (int) $user['waste']; ?></td>
                            <td><strong><?php echo (int) $user['overall']; ?></strong></td>
                            <td>
                                <?php echo (int) $user['time_used']; ?>
                                <span style="color:#888;">(<?php echo formatTime($user['time_used']); ?>)</span>
                            </td>
                        </tr>
                        <?php $idx++; endforeach;
                }
                ?>
            </table>
        </div>
    </div>
</body>

</html>