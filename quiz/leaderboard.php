<?php
require '../functions.php';
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
    <title>Green Quest: Hi-Trust Mini Games</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* ============ CUTE PASTEL THEME ============ */
        * {
            box-sizing: border-box;
        }

        :root {
            --bg: #f9ffe6;
            --paper: #ffffff;
            --ink: #264f3a;
            --muted: #6a8f7c;

            --mint-50: #f0fff4;
            --mint-100: #e7fff2;
            --mint-200: #d9ffea;
            --mint-300: #c8ffe1;

            --green-400: #2aa46a;
            --green-500: #1e8a58;

            --tab-idle: #eafff0;
            --tab-active: #d5ffe6;

            --row-border: #e7f6e6;

            --glow: 0 12px 40px #d9ffe9, 0 4px 18px #d7fffc;
        }

        /* ===== Page ===== */
        body {
            margin: 0;
            font-family: 'Prompt', sans-serif;
            background: radial-gradient(1200px 800px at 20% -10%, #fff 0, #f8fff2 45%, var(--bg) 100%);
            color: var(--ink);
        }

        .navbar {
            width: 100vw;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 4px 24px #c6ffc680;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 32px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 20;
        }

        .navbar-brand {
            font-family: 'Prompt', sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            color: #209765;
            padding: 7px 30px 7px 26px;
            box-shadow: 0 2px 10px #bcffdb30;
            text-decoration: none;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s;
            letter-spacing: 1.3px;
            border-bottom: 2px solid #b3ffc9;
        }

        .navbar-brand:hover {
            background: linear-gradient(90deg, #f2ffe8 10%, #e0ffd9 90%);
            color: #1a714e;
            box-shadow: 0 4px 14px #b8ffb370;
        }

        .navbar-buttons {
            display: flex;
            gap: 1.1rem;
        }

        .btn-main {
            font-family: 'Prompt', sans-serif;
            text-decoration: none;
            background: linear-gradient(90deg, #baffc9 0, #b1ffe9 100%);
            color: #1f7f52;
            border: none;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 800;
            box-shadow: 0 4px 14px #c9ffe1;
            transition: transform .12s, box-shadow .15s, background .18s;
        }

        .btn-main:hover {
            background: linear-gradient(90deg, #9cf3b0 0, #a7f7eb 100%);
            transform: translateY(-1px);
            box-shadow: 0 10px 24px #caffdf;
        }

        /* ===== Layout wrapper ===== */
        .wrap {
            max-width: 980px;
            margin: 112px auto 48px;
            padding: 0 16px;
        }

        /* ===== Card ===== */
        .card {
            background: var(--paper);
            border-radius: 22px;
            box-shadow: var(--glow);
            padding: 16px;
            border: 2px solid var(--mint-300);
            animation: pop .45s cubic-bezier(.2, .9, .3, 1);
        }

        @keyframes pop {
            from {
                transform: translateY(10px) scale(.985);
                opacity: 0
            }

            to {
                transform: none;
                opacity: 1
            }
        }

        /* ===== Tabs (pill buttons) ===== */
        /* === Bigger, cuter tab buttons === */
        .tabs {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            /* bigger spacing */
            margin: 6px 0 18px;
        }

        .tab-btn {
            font-family: 'Prompt', sans-serif;
            appearance: none;
            border: none;
            cursor: pointer;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: .6ch;

            /* size */
            font-weight: 900;
            font-size: 1.12rem;
            /* bigger text */
            letter-spacing: .2px;
            padding: .9em 1.4em;
            /* bigger pill */
            border-radius: 999px;

            /* cute pastel look */
            background: linear-gradient(180deg, #ffffff 0, #f5fff9 100%);
            border: 2px solid #c6ffd9;
            color: #208b5a;
            box-shadow: 0 8px 24px #dfffee, 0 2px 8px #d7fffc;

            transition:
                transform .14s ease,
                box-shadow .18s ease,
                background .18s ease,
                border-color .18s ease;
        }

        .tab-btn:hover {
            transform: translateY(-2px) scale(1.02);
            background: linear-gradient(180deg, #f9fffb 0, #eafff1 100%);
            box-shadow: 0 12px 30px #d2ffea;
        }

        .tab-btn:active {
            transform: translateY(-1px) scale(.99);
        }

        /* Active state: brighter and with an underline indicator */
        .tab-btn.active {
            background:  #fffae2;
            border-color: #a9ffd1;
            color: #137e4e;
            box-shadow: 0 16px 36px #caffdf, inset 0 -2px 0 #b6ffd1;
        }

        .tab-btn::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -6px;
            width: 64%;
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(90deg, #a9ffd1, #a7f7eb);
            transform: translateX(-50%) scaleX(0);
            transform-origin: center;
            transition: transform .22s ease;
        }

        .tab-btn.active::after {
            transform: translateX(-50%) scaleX(1);
        }

        /* Cute emojis without changing HTML */
        .tab-btn[data-tab="overall"]::before {
            content: "🏆";
        }

        .tab-btn[data-tab="quiz"]::before {
            content: "🧠";
        }

        .tab-btn[data-tab="waste"]::before {
            content: "🗑️";
        }

        /* Focus visibility (a11y) */
        .tab-btn:focus-visible {
            outline: 3px solid #9cf3b0;
            outline-offset: 3px;
        }

        /* Mobile tweaks */
        @media (max-width: 740px) {
            .tab-btn {
                font-size: 1.05rem;
                padding: .8em 1.1em;
            }
        }


        /* ===== Table (soft, centered, sticky head) ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 16px;
            overflow: hidden;
        }

        thead th {
            position: sticky;
            top: 0;
            background: linear-gradient(180deg, #eafff2 0, #deffe9 100%);
            color: #2da067;
            text-align: center;
            font-weight: 900;
            padding: .9em .6em;
            border-bottom: 2px solid #bff1d0;
            letter-spacing: .3px;
            z-index: 1;
        }

        th,
        td {
            font-size: 1.02rem;
        }

        td {
            text-align: center;
            padding: .78em .6em;
            border-bottom: 1px solid var(--row-border);
            color: #2c6b4c;
            background: #fff;
            transition: background .14s ease, transform .08s ease;
        }

        tbody tr:hover td {
            background: #f7fff9;
        }

        /* Rank column */
        .rank {
            width: 60px;
            font-weight: 900;
            color: var(--green-500);
        }

        /* ===== Cute top-3 highlight (no extra classes needed) ===== */
        tbody tr:nth-child(1) td {
            background: linear-gradient(90deg, #fff9d9 0, #edfff2 100%);
            font-weight: 900;
            color: #b19619;
        }

        tbody tr:nth-child(2) td {
            background: linear-gradient(90deg, #effef6 0, #e7f7ff 100%);
            font-weight: 800;
            color: #58a89a;
        }

        tbody tr:nth-child(3) td {
            background: linear-gradient(90deg, #fff0e2 0, #f2fff2 100%);
            font-weight: 800;
            color: #d08a4c;
        }

        /* ===== Subtle row lift on click/keyboard focus ===== */
        tbody tr:active td {
            transform: scale(.997);
        }

        /* ===== Utilities ===== */
        .muted {
            color: var(--muted);
            font-size: .9rem;
        }

        .hidden {
            display: none;
        }

        /* ===== Focus styles (a11y) ===== */
        .tab-btn:focus-visible,
        .btn-main:focus-visible,
        .navbar-brand:focus-visible {
            outline: 3px solid #9cf3b0;
            outline-offset: 2px;
            border-radius: 14px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 740px) {
            .navbar {
                padding: 14px 18px;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

            .wrap {
                margin: 102px auto 36px;
            }

            th,
            td {
                font-size: .98rem;
            }

            .tab-btn {
                padding: 9px 14px;
            }
        }

        /* ===== Reduced motion support ===== */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <span class="navbar-brand">อันดับผู้เล่น</span>
        <a href="menu.php" class="btn-main">กลับเมนู</a>
    </div>

    <div class="wrap">
        <div class="tabs">
            <button class="tab-btn active" data-tab="overall">คะแนนรวม</button>
            <button class="tab-btn" data-tab="quiz">ควิซ</button>
            <button class="tab-btn" data-tab="waste">เกมแยกขยะ</button>
        </div>

        <!-- OVERALL -->
        <div id="tab-overall" class="card">
            <table>
                <thead>
                    <tr>
                        <th class="rank">#</th>
                        <th>ชื่อผู้เล่น</th>
                        <th>คะแนนรวม</th>
                        <th>เวลาที่ใช้ทั้งหมด</th>
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
                        <th>ชื่อผู้เล่น</th>
                        <th>คะแนน</th>
                        <th>เวลาที่ใช้</th>
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
                        <th>ชื่อผู้เล่น</th>
                        <th>คะแนน</th>
                        <th>เวลาที่ใช้</th>
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