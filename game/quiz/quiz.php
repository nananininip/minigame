<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

// Topic can be from URL or fixed as 'quiz'
$topic = isset($_GET['topic']) ? $_GET['topic'] : 'quiz';

// Initialize overall points if not set
if (!isset($_SESSION['overall_points'])) {
    $_SESSION['overall_points'] = 0;
}

// Check if quiz is already initialized in the session
if (!isset($_SESSION['current_quiz'])) {
    $questions = getQuestions($topic);
    $_SESSION['current_quiz'] = $questions; // Store the current quiz in session
    $_SESSION['correct'] = 0;
    $_SESSION['incorrect'] = 0;
} else {
    $questions = $_SESSION['current_quiz']; // Retrieve questions from session
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $answers = $_POST['answers'];
    foreach ($answers as $index => $answer) {
        // Check the answer against the questions in the session
        if (trim($answer) == trim($questions[$index]['answer'])) {
            $_SESSION['correct']++;
        } else {
            $_SESSION['incorrect']++;
        }
    }

    // Calculate the current game score
    $currentGameScore = ($_SESSION['correct'] * 3) - ($_SESSION['incorrect'] * 1);
    
    // Save the score only if the quiz is completed
    $nickname = $_SESSION['nickname'];
    saveScore($nickname, $currentGameScore); // Save current game score to leaderboard

    // Update overall points in session
    $_SESSION['overall_points'] += $currentGameScore; // Update overall points

    // Clear current quiz session data for next quiz
    unset($_SESSION['current_quiz']);
    
    header('Location: result.php'); // Redirect to results page
    exit();
}
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
        <div class="container">
            <h1>Quiz Game</h1>
            <?php foreach ($questions as $index => $question): ?>
            <div class="card">
                <div class="card-body">
                    <p><?php echo htmlspecialchars($question['question']); ?></p>
                    <?php foreach ($question['choices'] as $choice): ?>
                    <label>
                        <input type="radio" name="answers[<?php echo $index; ?>]"
                            value="<?php echo htmlspecialchars($choice); ?>" required>
                        <?php echo htmlspecialchars($choice); ?>
                    </label><br>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <button class="btn-submit" type="submit">Submit Answers</button>
        </div>
    </form>
</body>

</html>