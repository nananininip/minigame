<?php
require 'functions.php';
$rows = getLeaderboard();

// === Build views ===
// Overall: by overall desc, then total time asc
$overallView = $rows;
usort($overallView, function ($a, $b) {
    $ta = $a['time_quiz'] + $a['time_waste'];
    $tb = $b['time_quiz'] + $b['time_waste'];
    if ($a['overall'] !== $b['overall'])
        return $b['overall'] - $a['overall'];
    if ($ta !== $tb)
        return $ta - $tb;
    return strcasecmp($a['nickname'], $b['nickname']);
});
$overallView = array_slice($overallView, 0, 10);

// Quiz: by quiz desc, then quiz time asc
$quizView = $rows;
usort($quizView, function ($a, $b) {
    if ($a['quiz'] !== $b['quiz'])
        return $b['quiz'] - $a['quiz'];
    if ($a['time_quiz'] !== $b['time_quiz'])
        return $a['time_quiz'] - $b['time_quiz'];
    return strcasecmp($a['nickname'], $b['nickname']);
});
$quizView = array_slice($quizView, 0, 10);

// Waste: by waste desc, then waste time asc
$wasteView = $rows;
usort($wasteView, function ($a, $b) {
    if ($a['waste'] !== $b['waste'])
        return $b['waste'] - $a['waste'];
    if ($a['time_waste'] !== $b['time_waste'])
        return $a['time_waste'] - $b['time_waste'];
    return strcasecmp($a['nickname'], $b['nickname']);
});
$wasteView = array_slice($wasteView, 0, 10);

function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #38a169;
            --bg: #f5fce8;
            --card: #fff;
            --text: #1f2937;
            --muted: #6b7280;
            --tab: #e5f7ee
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: 'Prompt', sans-serif;
            background: var(--bg);
            color: var(--text)
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            background: #fff;
            box-shadow: 0 2px 16px #dcf5e8
        }

        .brand {
            font-weight: 700;
            color: var(--green)
        }

        .wrap {
            max-width: 980px;
            margin: 24px auto;
            padding: 0 16px
        }

        .tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            flex-wrap: wrap
        }

        .tab-btn {
            border: 1px solid var(--green);
            background: #fff;
            padding: 10px 14px;
            border-radius: 999px;
            font-weight: 700;
            cursor: pointer
        }

        .tab-btn.active {
            background: var(--tab)
        }

        .card {
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 4px 24px #dcf5e8;
            padding: 8px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #eef5ef;
            text-align: left
        }

        th {
            font-size: 14px
        }

        .rank {
            width: 64px;
            font-weight: 700;
            color: var(--green)
        }

        .muted {
            color: var(--muted);
            font-size: 13px
        }

        .hidden {
            display: none
        }
    </style>
</head>

<body>
    <div class="navbar">
        <span class="navbar-brand">Top 10 ผู้เล่น</span>
        <a href="menu.php" class="btn-main">กลับเมนู</a>
    </div>

    <div class="wrap">
        <div class="tabs">
            <button class="tab-btn active" data-tab="overall">Overall</button>
            <button class="tab-btn" data-tab="quiz">Quiz</button>
            <button class="tab-btn" data-tab="waste">Waste</button>
        </div>

        <!-- OVERALL -->
        <div id="tab-overall" class="card">
            <table>
                <thead>
                    <tr>
                        <th class="rank">#</th>
                        <th>Nickname</th>
                        <th>Score (Quiz+Waste)</th>
                        <th>Time (Quiz+Waste)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($overallView as $r):
                        $tot = $r['time_quiz'] + $r['time_waste']; ?>
                        <tr>
                            <td class="rank"><?= $i++ ?></td>
                            <td><?= h($r['nickname']) ?></td>
                            <td><strong><?= (int) $r['overall'] ?></strong></td>
                            <td><?= fmtMMSS($tot) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- QUIZ -->
        <div id="tab-quiz" class="card hidden">
            <table>
                <thead>
                    <tr>
                        <th class="rank">#</th>
                        <th>Nickname</th>
                        <th>Quiz Score</th>
                        <th>Quiz Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($quizView as $r): ?>
                        <tr>
                            <td class="rank"><?= $i++ ?></td>
                            <td><?= h($r['nickname']) ?></td>
                            <td><strong><?= (int) $r['quiz'] ?></strong></td>
                            <td><?= fmtMMSS($r['time_quiz']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- WASTE -->
        <div id="tab-waste" class="card hidden">
            <table>
                <thead>
                    <tr>
                        <th class="rank">#</th>
                        <th>Nickname</th>
                        <th>Waste Score</th>
                        <th>Waste Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($wasteView as $r): ?>
                        <tr>
                            <td class="rank"><?= $i++ ?></td>
                            <td><?= h($r['nickname']) ?></td>
                            <td><strong><?= (int) $r['waste'] ?></strong></td>
                            <td><?= fmtMMSS($r['time_waste']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const tabs = document.querySelectorAll('.tab-btn');
        const views = {
            overall: document.getElementById('tab-overall'),
            quiz: document.getElementById('tab-quiz'),
            waste: document.getElementById('tab-waste')
        };
        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                tabs.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const key = btn.dataset.tab;
                Object.entries(views).forEach(([k, el]) => {
                    if (k === key) { el.classList.remove('hidden'); }
                    else { el.classList.add('hidden'); }
                });
            });
        });
    </script>
</body>

</html>