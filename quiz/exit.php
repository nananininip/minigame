<?php
session_start();
require '../functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: ../index.php');
    exit();
}
$nickname = $_SESSION['nickname'];

$me = ['quiz' => 0, 'waste' => 0, 'overall' => 0, 'time_quiz' => 0, 'time_waste' => 0];
foreach (getLeaderboard() as $r) {
    if (strcasecmp($r['nickname'], $nickname) === 0) {
        $me = $r;
        break;
    }
}
$total_time = (int) $me['time_quiz'] + (int) $me['time_waste'];
function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>ออกจากเกม</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@700;800&family=Prompt:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --panel: #fff;
            --ink: #2d4632;
            --brand: #209765;
            --brand-2: #53d3a1;
            --line: #e8f9ef;
            --shadow-lg: 0 18px 44px rgba(32, 151, 101, .16);
        }

        * {
            box-sizing: border-box
        }

        html,
        body {
            height: 100%
        }

        body {
            font-family: 'Prompt', sans-serif;
            margin: 0;
            font-family: 'Prompt', system-ui, -apple-system, Segoe UI, Arial, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(28rem 28rem at 12% 10%, rgba(138, 217, 232, .28) 0%, rgba(138, 217, 232, 0) 55%),
                radial-gradient(28rem 28rem at 85% 26%, rgba(122, 203, 150, .26) 0%, rgba(122, 203, 150, 0) 52%),
                linear-gradient(180deg, #eefdf4 0%, #e9fff1 100%);
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding: 6vh 12px;
        }

        .card {
            width: min(820px, 96vw);
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: clamp(20px, 3.6vw, 34px);
            box-shadow: var(--shadow-lg);
            text-align: center;
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        .card::before {
            content: "";
            position: absolute;
            inset: -1px -1px auto -1px;
            height: 42%;
            background:
                radial-gradient(60% 100% at 20% 0%, rgba(179, 255, 201, .45), transparent 70%),
                radial-gradient(60% 100% at 100% 0%, rgba(138, 217, 232, .35), transparent 70%);
            z-index: -1;
        }

        h1 {
            font-family: 'Kanit', 'Prompt', sans-serif;
            font-weight: 800;
            color: var(--brand);
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            letter-spacing: .3px;
            margin: 2px 0 16px;
        }

        .row {
            color: #3c7461;
            font-size: clamp(1rem, 1.7vw, 1.12rem);
            margin: 8px 0;
            font-weight: 600;
        }

        .row strong {
            color: #159a63;
        }

        .actions {
            margin-top: 18px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            font-family: 'Prompt', sans-serif;
            --h: 52px;
            font-size: clamp(1rem, 1.4vw, 1.1rem);
            height: var(--h);
            padding: 0 22px;
            border-radius: 14px;
            border: 0;
            outline: none;
            font-weight: 800;
            letter-spacing: .3px;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(32, 163, 106, .28), inset 0 -2px 0 rgba(0, 0, 0, .08);
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
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

        @keyframes shine {
            to {
                transform: translateX(200%);
            }
        }

        .btn-menu {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
        }

        .btn-exit {
            background: linear-gradient(135deg, #ff6666, #ff4d4d);
            box-shadow: 0 10px 24px rgba(255, 90, 90, .25), inset 0 -2px 0 rgba(0, 0, 0, .08);
        }

        #confetti-canvas {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 25;
        }
    </style>
</head>

<body>
    <canvas id="confetti-canvas"></canvas>

    <div class="card" role="region" aria-label="สรุปคะแนนและการออกจากเกม">
        <h1>ขอขอบพระคุณ คุณ<?= h($nickname) ?> ที่ร่วมสนุกกับเรา</h1>

        <div class="row">ควิซ: <strong><?= (int) $me['quiz'] ?></strong> คะแนน — เวลา
            <strong><?= fmtMMSS((int) $me['time_quiz']) ?></strong>
        </div>
        <div class="row">เกมแยกขยะ: <strong><?= (int) $me['waste'] ?></strong> คะแนน — เวลา
            <strong><?= fmtMMSS((int) $me['time_waste']) ?></strong>
        </div>
        <div class="row"><strong>รวม:</strong> <?= (int) $me['overall'] ?> คะแนน — เวลา
            <strong><?= fmtMMSS($total_time) ?></strong>
        </div>

        <div class="actions">
            <form action="menu.php" method="post"><button type="submit" class="btn btn-menu">กลับไปหน้าเมนู</button>
            </form>
            <form action="../index.php" method="get"><button type="submit" class="btn btn-exit">ออกจากเกม</button></form>
        </div>
    </div>

    <!-- Use the WEB path (not filesystem). Adjust if your site path differs. -->
    <audio id="cheer" preload="auto" playsinline autoplay>
        <!-- Absolute-from-root path -->
        <source src="/minigame/game/assets/sfx/celebrate.mp3" type="audio/mpeg">
        <!-- Relative fallback (works if exit.php is inside /minigame/game/) -->
        <source src="assets/sfx/celebrate.mp3" type="audio/mpeg">
    </audio>

    <script>
        (function () {
            /* Load confetti */
            function loadConfetti(cb) {
                if (window.confetti) return cb();
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js';
                s.onload = cb; document.head.appendChild(s);
            }
            function burst() {
                if (!window.confetti) return;
                const D = 1800, end = Date.now() + D;
                (function frame() {
                    window.confetti({ particleCount: 3, angle: 60, spread: 60, origin: { x: 0 }, startVelocity: 48 });
                    window.confetti({ particleCount: 3, angle: 120, spread: 60, origin: { x: 1 }, startVelocity: 48 });
                    if (Date.now() < end) requestAnimationFrame(frame);
                })();
                window.confetti({ particleCount: 120, spread: 70, startVelocity: 45, origin: { y: .6 } });
            }
            loadConfetti(burst);

            /* Sound: try on load; if blocked, play on first gesture */
            const KEY = 'exitSoundPlayed';
            const audio = document.getElementById('cheer');

            async function playNow() {
                if (!audio) return;
                try {
                    audio.currentTime = 0; audio.volume = 0.9;
                    await audio.play();
                    sessionStorage.setItem(KEY, '1');
                } catch (e) { /* blocked */ }
            }

            function tryStart() {
                if (sessionStorage.getItem(KEY)) return;
                // If user activation is still considered "active" right after navigation:
                if (navigator.userActivation && navigator.userActivation.isActive) { playNow(); return; }
                // Otherwise try standard load/pageshow
                playNow();
                // And fall back to first gesture
                ['pointerdown', 'touchstart', 'keydown', 'click'].forEach(ev => {
                    window.addEventListener(ev, function onEv() {
                        window.removeEventListener(ev, onEv);
                        if (!sessionStorage.getItem(KEY)) playNow();
                    }, { once: true });
                });
                // Safari back/forward cache
                window.addEventListener('pageshow', (e) => { if (e.persisted && !sessionStorage.getItem(KEY)) playNow(); }, { once: true });
            }

            window.addEventListener('load', tryStart, { once: true });
        })();
    </script>
</body>

</html>