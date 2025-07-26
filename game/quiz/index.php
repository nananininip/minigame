<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['nickname'] = $_POST['nickname'];

    // Reset all game/quiz-related session values
    $_SESSION['overall_points'] = 0;
    $_SESSION['correct'] = 0;
    $_SESSION['incorrect'] = 0;
    unset($_SESSION['current_quiz']);

    header('Location: menu.php'); // go to menu to select a game
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>MiniGame</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header-box">
        <h3>MiniGame</h3>
    </div>
    <div class="form-container">
        <form method="post">
            <label for="nickname">Enter your nickname:</label>
            <input type="text" id="nickname" name="nickname" required>
            <input type="submit" class="btn-submit" value="Let's Begin">
        </form>
    </div>
</body>

</html>