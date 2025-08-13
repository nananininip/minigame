<?php
session_start();
if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Green Quest: Hi-Trust Mini Games</title>
    <script>
        // Reset quiz timer when user lands on the menu
        sessionStorage.removeItem("timeLeft");
    </script>

    <style>
        /* === GLOBAL RESETS === */
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Kanit', 'Prompt', Arial, sans-serif;
            background: #f9ffe6;
            color: #2d4632;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* === NAVBAR === */
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

        /* Exit Button */
        .btn-exit {
            background: linear-gradient(90deg, #ff7575 10%, #ffd6d6 100%);
            color: #fff;
            font-size: 1.13rem;
            font-weight: bold;
            border: none;
            border-radius: 12px;
            padding: 11px 34px;
            box-shadow: 0 2px 10px #ffbebe80;
            cursor: pointer;
            transition: background 0.15s, color 0.15s, transform 0.13s;
        }

        .btn-exit:hover {
            background: linear-gradient(90deg, #ff9898 0%, #ffdada 100%);
            color: #fff;
            transform: scale(1.05);
        }

        /* Remove the extra Menu button on the right */
        .btn-submit {
            display: none;
        }

        /* === MAIN MENU CARD === */
        .menu-container {
            margin-top: 120px;
            background: rgba(255, 255, 255, 0.97);
            padding: 48px 26px 46px 26px;
            border-radius: 28px;
            box-shadow: 0 10px 40px #c4ffd8a3, 0 2px 16px #b6d6ff33;
            max-width: 500px;
            width: 90vw;
            text-align: center;
            position: relative;
            z-index: 1;
            animation: popin 0.5s cubic-bezier(.65, 1.4, .42, 1.1);
        }

        @keyframes popin {
            0% {
                transform: scale(.95) translateY(40px);
                opacity: 0;
            }

            60% {
                transform: scale(1.04) translateY(-10px);
            }

            100% {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        .menu-container h1 {
            font-size: 2.1rem;
            font-weight: 800;
            color: #19744b;
            letter-spacing: 1.2px;
            margin-bottom: 40px;
            margin-top: 0;
            text-shadow: 0 4px 14px #dbffbe45;
        }

        /* === MENU BUTTONS === */
        .menu-buttons {
            display: flex;
            flex-direction: row;
            gap: 28px;
            justify-content: center;
            margin-top: 5px;
            flex-wrap: wrap;
        }

        .menu-btn {
            background: linear-gradient(120deg, #fffbe6 10%, #d6ffe6 90%);
            color: #222;
            border: none;
            border-radius: 20px;
            font-size: 1.22rem;
            font-weight: 700;
            padding: 28px 20px 24px 20px;
            min-width: 175px;
            width: 170px;
            max-width: 92vw;
            box-shadow: 0 7px 26px #e7fff522, 0 2px 12px #d7fffc;
            margin-bottom: 10px;
            cursor: pointer;
            outline: none;
            transition: background 0.17s, color 0.14s, box-shadow 0.13s, transform 0.13s;
            border-bottom: 3px solid #b8ffbe;
            border-top: 2px solid #f9f7ff;
            text-align: center;
            margin-top: 0;
            letter-spacing: 0.02em;
        }

        .menu-btn:hover {
            background: linear-gradient(110deg, #d6ffe6 0%, #fffbe6 100%);
            color: #209765;
            transform: scale(1.06) rotate(-1.5deg);
            box-shadow: 0 10px 38px #f5fff7b3, 0 6px 18px #e1ffc1b1;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px 8vw 12px 8vw;
            }

            .menu-container {
                margin-top: 92px;
                padding: 32px 3vw 28px 3vw;
                max-width: 98vw;
            }

            .menu-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .menu-btn {
                width: 94vw;
                font-size: 1.08rem;
                padding: 22px 0 17px 0;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a class="navbar-brand" href="menu.php">หน้าหลัก</a>
        <div class="navbar-buttons">
            <form action="exit.php" method="post" style="display: inline;">
                <button type="submit" class="btn-exit">Exit</button>
            </form>
        </div>
    </nav>
    <div class="container menu-container">
        <h1>ยินดีต้อนรับสู่เกม, <?php echo htmlspecialchars($_SESSION['nickname']); ?></h1>
        <div class="menu-buttons">
            <button class="menu-btn" onclick="location.href='quiz.php'">🧠 ควิซ</button>
            <button class="menu-btn" onclick="location.href='/minigame/game/waste.html'">🗑️ เกมแยกขยะ</button>
            <button class="menu-btn" onclick="location.href='leaderboard.php'">🏆 อันดับผู้เล่น</button>
        </div>

    </div>
</body>

</html>