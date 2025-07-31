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
    $selected = $_POST['answer'] ?? '';
    $currentIndex = $_SESSION['quiz_current'];
    $questions = $_SESSION['quiz_sequence'];
    $question = $questions[$currentIndex];

    // Check if answer is correct
    if ($selected == $question['answer']) {
        $_SESSION['correct']++;
    } else {
        $_SESSION['incorrect']++;
    }

    $_SESSION['quiz_current']++;

    // Redirect to results page if quiz completed
    if ($_SESSION['quiz_current'] >= count($questions)) {
        $_SESSION['points_this_quiz'] = $_SESSION['correct'];
        $_SESSION['overall_points'] += $_SESSION['points_this_quiz'];

        // Save results to leaderboard
        saveScore($_SESSION['nickname'], $_SESSION['correct'], $_SESSION['incorrect'], $_SESSION['overall_points']);

        header('Location: result.php');
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
            <div id="timerBarContainer"
                style="width: 100%; background-color: #ddd; height: 12px; border-radius: 10px; margin-bottom: 20px;">
                <div id="timerBar"
                    style="width: 100%; height: 100%; background-color: #28a745; border-radius: 10px; transition: width 0.2s;">
                </div>
            </div>
            <div style="text-align:right; font-size:14px; color:#555; margin-bottom:10px;">
                เหลือเวลา <span id="timeLeft">50</span> วินาที
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="font-weight:bold;"><?php echo htmlspecialchars($question['question']); ?></p>
                    <?php foreach ($question['choices'] as $choice): ?>
                        <button type="button" class="answer-btn" data-answer="<?php echo htmlspecialchars($choice); ?>">
                            <?php echo htmlspecialchars($choice); ?>
                        </button>
                    <?php endforeach; ?>
                    <input type="hidden" name="answer" id="selectedAnswer" required>

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
    <script>
        const answerButtons = document.querySelectorAll('.answer-btn');
        const hiddenInput = document.getElementById('selectedAnswer');
        const correctAnswer = <?php echo json_encode(trim($question['answer'])); ?>;
        const submitBtn = document.querySelector('.btn-submit');

        // Initially disable submit button
        submitBtn.disabled = true;

        answerButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                if (hiddenInput.value !== "") return; // Prevent re-selection

                hiddenInput.value = btn.dataset.answer.trim();

                // Highlight correct/wrong answers
                answerButtons.forEach(b => {
                    b.disabled = true; // disable all buttons after selection
                    if (b.dataset.answer.trim() === correctAnswer) {
                        b.classList.add('correct');
                    }
                });

                if (btn.dataset.answer.trim() !== correctAnswer) {
                    btn.classList.add('wrong');
                }

                // Enable submit button after selection
                submitBtn.disabled = false;

                // Automatically submit last question after 1.5 seconds
                <?php if ($currentIndex + 1 == count($questions)): ?>
                    setTimeout(() => document.querySelector("form").submit(), 1500);
                <?php endif; ?>
            });
        });
    </script>
    <script src="timer.js"></script>


</body>

</html>