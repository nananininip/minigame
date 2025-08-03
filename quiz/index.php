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

        header('Location: menu.php');
        exit();
    }
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
        <h3>Hi-Trust Mini Game</h3>
    </div>
    <div class="form-container">
        <form method="post" autocomplete="off">
            <label for="nickname">Enter your nickname:</label>
            <div class="input-row">
                <input type="text" id="nickname" name="nickname" required>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <button type="submit" class="btn-submit">Let's Begin</button>
            </div>
        </form>
    </div>

    <div class="mascot-row">
        <img src="mascot-leaf.png" alt="Leaf Mascot" class="mascot mascot-leaf">
    </div>
</body>

</html>