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
    <title>Challenge FUN - Menu</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">
        <a class="navbar-brand" href="menu.php">Menu</a>
        <div class="navbar-buttons">
            <form action="exit.php" method="post" style="display: inline;">
                <button type="submit" class="btn-exit">Exit</button>
            </form>
            <form action="menu.php" method="post" style="display: inline;">
                <button type="submit" class="btn-submit" style="right: 110px; position: relative;">Menu</button>
            </form>
        </div>
    </nav>
    <div class="container menu-container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['nickname']); ?></h1>
        <ul class="menu-list">
            <li><a class="menu-link" href="quiz.php">🌱 Quiz Game</a></li>
            <li><a class="menu-link" href="waste.php">🗑️ Waste Sorting Game</a></li>
            <li><a class="menu-link" href="leaderboard.php">🏆 Leaderboard</a></li>
        </ul>
    </div>
</body>

</html>