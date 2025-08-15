<?php
session_start();
require_once 'functions.php';

$error = '';

// Handle form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = trim($_POST['nickname']);
    $nickname = htmlspecialchars($nickname, ENT_QUOTES, 'UTF-8');

    // Check if name exists in leaderboard
    $exists = false;
    $leaderboard = getLeaderboard();
    foreach ($leaderboard as $entry) {
        if (strcasecmp($entry['nickname'], $nickname) == 0) { // case-insensitive
            $exists = true;
            break;
        }
    }

    if ($exists) {
        $error = "ขออภัย, ชื่อนี้ถูกใช้ใน TOP 10 แล้ว กรุณาใช้ชื่ออื่น!";
    } else {
        $_SESSION['nickname'] = $nickname;
        // Reset all game/quiz-related session values
        $_SESSION['overall_points'] = 0;
        $_SESSION['correct'] = 0;
        $_SESSION['incorrect'] = 0;
        unset($_SESSION['current_quiz']);

        header('Location: quiz/menu.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Green Quest: Hi-Trust Mini Games</title>
    <style>
        /* === Hi-Trust Mini Game — Entry Page (FULL CSS) === */

        /* Fonts */
        @import url("https://fonts.googleapis.com/css2?family=Prompt&display=swap");

        /* Theme tokens (single :root) */
        :root {
            --bg: #fffae2;
            --panel: #ffffff;
            --ink: #2d4632;
            --muted: #5a7a63;
            --brand: #209765;
            --brand-2: #53d3a1;
            --brand-3: #b3ffc9;
            --ring: rgba(117, 232, 176, 0.35);
            --danger-ink: #b74a4a;
            --line: #e8f9ef;
            --shadow-lg: 0 18px 44px rgba(32, 151, 101, .16);
            --shadow-md: 0 10px 26px rgba(32, 151, 101, .12);

            /* header + brand */
            --header-h: 104px;
            /* you liked this */
            --brand-size: clamp(1.2rem, 2.2vw, 1.6rem);
            --logo-scale: 2.4;
            /* big logo */
        }

        /* Resets */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: 'Kanit', 'Prompt', system-ui, -apple-system, Segoe UI, Arial, sans-serif;
            color: var(--ink);
            background: #f9ffe6;
            padding-top: var(--header-h);
            overflow-x: hidden;
        }

        /* ===== Header (keep your visual) ===== */
        .header-box {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 20;
            width: 100vw;
            height: var(--header-h);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 28px;
            background: rgba(255, 255, 255, .85);
            border-bottom: 1px solid #f1fff6;
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 26px rgba(0, 0, 0, .05);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 12px 22px;
            text-decoration: none;
            white-space: nowrap;
            border-bottom: 2px solid var(--brand-3);
            background: linear-gradient(0deg, rgba(243, 255, 246, .6), rgba(255, 255, 255, .6));
            box-shadow: 0 2px 10px #bcffdb30;
            transition: transform .18s ease, box-shadow .18s ease, filter .2s ease;
            font-size: var(--brand-size);
        }

        .brand:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px #b8ffb370;
            filter: saturate(1.02);
        }

        .brand-logo {
            height: calc(1em * var(--logo-scale));
            width: auto;
            display: block;
        }

        .brand-title {
            font-family: 'Kanit', 'Prompt', system-ui, -apple-system, Segoe UI, Arial, sans-serif;
            font-weight: 500;
            letter-spacing: .5px;
            color: var(--brand);
            font-size: 1em;
            line-height: 1;
            white-space: nowrap;
            display: inline-block;
            transform: translateY(0.81em);
            /* you liked this position */
        }

        /* iPad / Tablet */
        @media (min-width:768px) and (max-width:1180px) {
            :root {
                --header-h: 112px;
                --brand-size: 1.35rem;
                --logo-scale: 2.6;
            }

            .brand {
                gap: 18px;
                padding: 12px 24px;
            }
        }

        /* Small screens */
        @media (max-width:720px) {
            :root {
                --header-h: 88px;
                --brand-size: 1.15rem;
                --logo-scale: 1.9;
            }
        }

        /* ===== Entry Card ===== */
        .form-container {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: clamp(18px, 3vw, 28px);
            margin-top: clamp(56px, 12vh, 160px);
        }

        .form-container form {
            width: min(720px, 92vw);
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: clamp(20px, 3vw, 28px);
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        /* soft decorative glow */
        .form-container form::before {
            content: "";
            position: absolute;
            inset: -1px -1px auto -1px;
            height: 46%;
            background:
                radial-gradient(60% 100% at 20% 0%, rgba(179, 255, 201, .45), transparent 70%),
                radial-gradient(60% 100% at 100% 0%, rgba(138, 217, 232, .35), transparent 70%);
            z-index: -1;
        }

        /* Label */
        .form-container label {
            display: block;
            margin: 8px 2px 10px;
            font-weight: 800;
            font-size: clamp(1.02rem, 1.8vw, 1.18rem);
            color: #1e814e;
            letter-spacing: .2px;
        }

        /* === Row like pic #1: input left, button right, error under input === */
        .input-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            /* lets the error line drop below */
            border-radius: 16px;
        }

        /* KILL the big halo around the whole row when focusing the input */
        .input-row:focus-within {
            box-shadow: none !important;
            transform: none;
        }

        /* Input */
        #nickname {
            order: 1;
            flex: 1 1 420px;
            /* grow nicely */
            min-height: 52px;
            /* same visual height as button */
            appearance: none;
            border: 2px solid #dff5e9;
            background: #ffffffcc;
            border-radius: 16px;
            padding: 16px 18px;
            font-size: clamp(1rem, 1.7vw, 1.07rem);
            color: var(--ink);
            outline: none;
            transition: border-color .18s ease, background .18s ease, box-shadow .2s ease, transform .15s ease;
        }

        #nickname::placeholder {
            color: #94b3a0;
        }

        #nickname:hover {
            border-color: #b8efd6;
        }

        #nickname:focus {
            border-color: #7eddac;
            background: #ffffff;
            transform: translateY(-1px);
        }
        
        /* Button (pill) */
        .btn-submit {
            order: 2;
            /* stays on first row */
            font-family: 'Prompt' !important;
            flex: 0 0 auto;
            min-width: 160px;
            min-height: 52px;
            border: 0;
            outline: none;
            border-radius: 14px;
            padding: 0 24px;
            font-weight: 800;
            font-size: clamp(.98rem, 1.6vw, 1.05rem);
            letter-spacing: .3px;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            box-shadow: 0 10px 24px rgba(32, 163, 106, .35), inset 0 -2px 0 rgba(0, 0, 0, .08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 28px rgba(32, 163, 106, .36);
            filter: saturate(1.08);
        }

        .btn-submit:active {
            transform: translateY(0) scale(.98);
            box-shadow: 0 8px 16px rgba(32, 163, 106, .32);
        }

        .btn-submit::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 35%, rgba(255, 255, 255, .4) 48%, transparent 60%);
            transform: translateX(-200%);
            pointer-events: none;
        }

        .btn-submit:hover::after {
            animation: shine 1000ms ease forwards;
        }

        @keyframes shine {
            to {
                transform: translateX(200%);
            }
        }

        /* Error line (no bg box) – sits below input */
        .error-message {
            order: 3;
            /* always after input+button */
            flex: 1 0 100%;
            /* new line */
            margin: 6px 4px 0 6px;
            background: none !important;
            border: 0 !important;
            padding: 0 !important;
            color: #d43c3c;
            font-weight: 700;
            font-size: clamp(.9rem, 1.4vw, .98rem);
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Mobile: stack input → error → button */
        @media (max-width:720px) {
            .input-row {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-submit {
                order: 3;
                width: 100%;
            }

            .error-message {
                order: 2;
                width: 100%;
                margin-left: 2px;
            }
        }

        /* Mascot */
        .mascot-row {
            position: fixed;
            right: clamp(10px, 4vw, 28px);
            bottom: clamp(8px, 3vw, 24px);
            z-index: 1;
            pointer-events: none;
        }

        .mascot {
            width: clamp(120px, 18vw, 220px);
            filter: drop-shadow(0 14px 22px rgba(0, 0, 0, .09));
            opacity: .98;
        }

        .mascot-leaf {
            animation: leafBob 3.2s ease-in-out infinite;
        }

        @keyframes leafBob {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-8px) rotate(-2deg);
            }
        }

        /* Autofill */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 40px #ffffff inset;
            -webkit-text-fill-color: var(--ink);
        }

        /* Hover lift on card (pointer devices) */
        @media (hover:hover) {
            .form-container form:hover {
                transform: translateY(-2px);
                box-shadow: 0 20px 50px rgba(32, 151, 101, .18);
                transition: transform .2s ease, box-shadow .25s ease;
            }
        }

        /* Reduced motion */
        @media (prefers-reduced-motion:reduce) {
            * {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>
    <div class="header-box">
        <a href="index.php" class="brand" aria-label="Mini Game Home">
            <img class="brand-logo" src="HT.jpg" alt="Hi-Trust Logo">
            <span class="brand-title">Mini Game</span>
        </a>
    </div>

    <div class="form-container">
        <form method="post" autocomplete="off">
            <label for="nickname">กรอกชื่อเล่นของคุณ:</label>
            <div class="input-row">
                <input type="text" id="nickname" name="nickname" required>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <button type="submit" class="btn-submit">เริ่มเกม!</button>
            </div>
        </form>
    </div>

    <div class="mascot-row">
        <img src="mascot-leaf.png" alt="Leaf Mascot" class="mascot mascot-leaf">
    </div>
</body>

</html>