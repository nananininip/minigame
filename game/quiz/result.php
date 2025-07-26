<?php
session_start();
require 'functions.php'; // Include your functions file to access leaderboard

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

// Get the player's nickname
$nickname = $_SESSION['nickname'];

// Load overall points from the leaderboard
$leaderboard = getLeaderboard(); // Ensure this function reads from leaderboard.txt
$overall_points = isset($leaderboard[$nickname]) ? $leaderboard[$nickname] : 0; // Fetch player's score

$correct = $_SESSION['correct'];
$incorrect = $_SESSION['incorrect'];
$currentGameScore = ($correct * 3) - ($incorrect * 1); // Calculate current game score
?>

<html>
<head>
    <title>Challenge FUN - Result</title>
</head>
<style>
    /* Your existing styles */
    .navbar {
        background-color: white;
        background: rgba(300, 300, 300, 0.9);
        padding: 20px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
        width: 100%;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .navbar-brand {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    .container {
        background-color: #FFFFFF;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px #D3D3D3;
        max-width: 600px;
        margin: 200px auto 20px auto;
        text-align: center;
    }
    th, td {
        border: 4px solid #96D4D4; /* Outer border layer with #96D4D4 */
        padding: 15px;
        text-align: center;
        position: relative; 
        background-color: #fff; 
        box-shadow: 0 0 0 2px #0066cc; 
    }
    th {
        font-size: 16px;
        font-weight: bold;
        color: #0066cc;
    }
    .btn-exit {
        background-color: #dc3545;
        color: #ffffff;
        font-size: 16px;
        font-weight: bold;
        border: 3px solid #0066cc;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        position: absolute;
        top: 40px;
        right: 90px;
    }
    .btn-submit {
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
        color: #ffffff;
        border: 3px solid #0066cc;
        cursor: pointer;
        background-color: #e67300;
        border-radius: 5px;
        margin-top: 20px;
    }
    .button-container {
        display: flex; /* Enable flexbox */
        justify-content: center; /* Center items horizontally */
        margin-top: 20px; /* Margin to add space around the container */
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
<body>
    <nav class="navbar">
        <a class="navbar-brand" href="menu.php">Quiz Result</a>
        <div class="navbar-buttons">
            <form action="exit.php" method="post" style="display: inline;">
                <button type="submit" class="btn-exit">Exit</button>
            </form>
            <form action="menu.php" method="post" style="display: inline;">
                <button type="submit" class="btn-submit">Menu</button>
            </form>
        </div>
    </nav>
    <div class="container">
        <table>
            <tr>
                <th>Player</th>
                <th>Correct Answers</th> 
                <th>Incorrect Answers</th> 
                <th>Points from this quiz</th> 
                <th>Overall Points</th> 
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($nickname); ?></td>
                <td><?php echo $correct; ?></td>
                <td><?php echo $incorrect; ?></td>
                <td><?php echo $currentGameScore; ?></td>
                <td><?php echo $overall_points; ?></td>
            </tr>
        </table> 
    </div>
    <div class="button-container">
        <form action="menu.php" method="post">
            <button type="submit" class="btn-newQ">Back to Menu</button>
        </form>
    </div>
</body>
</html>
