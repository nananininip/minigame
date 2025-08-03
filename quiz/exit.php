<?php
session_start();
require 'functions.php';

// Check if the user is logged in
if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

$nickname = $_SESSION['nickname'];

// Get the overall points from the leaderboard
$leaderboard = getLeaderboard();
$overall_points = isset($leaderboard[$nickname]['overall']) ? $leaderboard[$nickname]['overall'] : 0;
$quiz_points = isset($leaderboard[$nickname]['quiz']) ? $leaderboard[$nickname]['quiz'] : 0;
$waste_points = isset($leaderboard[$nickname]['waste']) ? $leaderboard[$nickname]['waste'] : 0;

// Reset session variables
unset($_SESSION['overall_points']); // Reset overall points
unset($_SESSION['current_quiz']);   // Reset current quiz data
unset($_SESSION['correct']);         // Reset correct answer count
unset($_SESSION['incorrect']);       // Reset incorrect answer count
unset($_SESSION['nickname']);        // Optionally unset the nickname to log out the user

session_destroy(); // Destroy the session
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Challenge FUN - Exit</title>
</head>
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
        margin-bottom: 0.2em;
        margin-top: 0.5em;
        font-weight: 800;
        letter-spacing: 1px;
    }

    p {
        color: #3c7461;
        font-size: 1.18em;
        margin: 0.2em 0 0.5em 0;
        font-weight: 500;
    }

    strong {
        color: #14995b;
    }

    .btn-newQ {
        font-size: 1em;
        font-weight: bold;
        padding: 0.67em 2.1em;
        color: #fff;
        border: none;
        border-radius: 13px;
        cursor: pointer;
        background: linear-gradient(90deg, #8ff3a8 0%, #5ce4c0 100%);
        box-shadow: 0 4px 18px #c1f4d9;
        margin-top: 2em;
        transition: background 0.17s, box-shadow 0.17s;
        letter-spacing: 1.2px;
    }

    .btn-newQ:hover {
        background: linear-gradient(90deg, #5ce4c0 0%, #8ff3a8 100%);
        box-shadow: 0 6px 28px #b9ffdc;
    }

    form {
        margin-top: 1.5em;
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">

</head>

<body>
    <h1>Goodbye, <?php echo htmlspecialchars($nickname); ?></h1>
    <p>Quiz Points: <?php echo $quiz_points; ?></p>
    <p>Waste Points: <?php echo $waste_points; ?></p>
    <p><strong>Total Overall Points:</strong> <?php echo $overall_points; ?></p>
    <form action="menu.php" method="post">
        <button type="submit" class="btn-newQ">Start New Game</button>
    </form>
    <script>sessionStorage.removeItem("timeLeft");</script>
</body>

</html>