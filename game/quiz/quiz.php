<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

$topic = isset($_GET['topic']) ? $_GET['topic'] : 'quiz';
$_SESSION['game_type'] = $topic;


// Initialize overall points if not set
if (!isset($_SESSION['overall_points'])) {
    $_SESSION['overall_points'] = 0;
}

// === INITIALIZE THE QUIZ SEQUENCE ===
if (!isset($_SESSION['quiz_sequence'])) {
    $questions = getQuestions($topic); // This returns an array
    shuffle($questions); // Randomize order
    $_SESSION['quiz_sequence'] = $questions;
    $_SESSION['quiz_current'] = 0; // Start at first question
    $_SESSION['correct'] = 0;
    $_SESSION['incorrect'] = 0;
}

// Handle form submission (answering one question)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected = isset($_POST['answer']) ? trim($_POST['answer']) : '';
    $currentIndex = $_SESSION['quiz_current'];
    $questions = $_SESSION['quiz_sequence'];
    $question = $questions[$currentIndex];

    // Check answer
    if ($selected == trim($question['answer'])) {
        $_SESSION['correct']++;
    } else {
        $_SESSION['incorrect']++;
    }

    $_SESSION['quiz_current']++;

    // If finished all questions
    if ($_SESSION['quiz_current'] >= count($questions)) {
        // 1 point per correct answer, 0 for incorrect
        $currentGameScore = $_SESSION['correct'];
        $nickname = $_SESSION['nickname'];
        saveScore($nickname, $currentGameScore, $topic);
        $_SESSION['overall_points'] = getLeaderboard()[$nickname]['overall'];

        // Cleanup for next quiz
        unset($_SESSION['quiz_sequence']);
        unset($_SESSION['quiz_current']);
        header('Location: result.php'); // Go to results
        exit();
    }
}

// Determine which question to show
$currentIndex = $_SESSION['quiz_current'];
$questions = $_SESSION['quiz_sequence'];
$question = $questions[$currentIndex];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>MiniGame - Quiz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <a class="navbar-brand" href="menu.php">Quiz</a>
        <div class="navbar-buttons">
            <form action="exit.php" method="post" style="display: inline;">
                <button type="submit" class="btn-exit">Exit</button>
            </form>
            <form action="menu.php" method="post" style="display: inline;">
                <button type="submit" class="btn-submit" style="right: 110px; position: relative;">Menu</button>
            </form>
        </div>
    </nav>
    <form method="post">
        <div class="container" style="margin-top:40px">
            <h1><?php echo ucfirst($topic); ?> Quiz</h1>
            <div class="card">
                <div class="card-body">
                    <p style="font-weight:bold;"><?php echo htmlspecialchars($question['question']); ?></p>
                    <?php foreach ($question['choices'] as $choice): ?>
                        <label>
                            <input type="radio" name="answer" value="<?php echo htmlspecialchars($choice); ?>" required>
                            <?php echo htmlspecialchars($choice); ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="btn-submit" type="submit">
                <?php echo ($currentIndex + 1 == count($questions)) ? 'Finish' : 'Next'; ?>
            </button>
            <div style="margin-top:10px; color:#666;">
                คำถาม <?php echo $currentIndex + 1; ?> / <?php echo count($questions); ?>
            </div>
        </div>
    </form>
</body>
</html>
