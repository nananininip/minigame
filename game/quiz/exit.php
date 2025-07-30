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
$overall_points = isset($leaderboard[$nickname]) ? $leaderboard[$nickname] : 0;

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
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin-top: 5%;
            text-align: center;
            
        }
    .btn-newQ {
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            color: white;
            border: 3px solid #0066cc;
            border-radius: 5px;
            cursor: pointer;
            background-color: #96D4D4;
        }        
</style>
</head>
<body>
    <h1>Goodbye, <?php echo htmlspecialchars($nickname); ?></h1>
    <p>Your overall points: <?php echo $overall_points; ?></p>
    <form action="menu.php" method="post">
        <button type="submit" class="btn-newQ">Start New Game</button>
    </form>
    <script>sessionStorage.removeItem("timeLeft");</script>
</body>
</html>
