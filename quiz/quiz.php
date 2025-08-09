<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

$topic = 'quiz';
$numQuestions = 5;
$totalTime = 50;

// First-time init
if (!isset($_SESSION['quiz_sequence'])) {
    $_SESSION['quiz_sequence'] = getQuestions($topic, $numQuestions);
    $_SESSION['quiz_current'] = 0;
    $_SESSION['score'] = 0;               // quiz points (10 per correct)
    $_SESSION['start_time'] = time();
    if (!isset($_SESSION['waste_score']))
        $_SESSION['waste_score'] = 0; // keep if already set
}

$questions = $_SESSION['quiz_sequence'];
$currentIndex = $_SESSION['quiz_current'];

// ---- Helper: save to leaderboard (merge, add time) ----
function save_leaderboard_now()
{
    $nickname = $_SESSION['nickname'];
    $quiz_score = isset($_SESSION['score']) ? (int) $_SESSION['score'] : 0;      // points
    $waste_score = isset($_SESSION['waste_score']) ? (int) $_SESSION['waste_score'] : 0;
    $time_used = time() - (int) $_SESSION['start_time'];                        // seconds this quiz run

    // Merge best scores; add time to total
    // signature: saveResultToLeaderboard($nickname, $quizDelta, $wasteDelta, $overall_ignored, $timeDelta)
    saveResultToLeaderboard($nickname, $quiz_score, 0, $time_used, 0);
    return [$quiz_score, $waste_score];
}

// If we've run out of questions (safety)
if (!isset($questions[$currentIndex])) {
    list($quiz_score, $waste_score) = save_leaderboard_now();
    $correctCount = (int) floor($quiz_score / 10);
    $numWrong = max(0, $GLOBALS['numQuestions'] - $correctCount);
    unset($_SESSION['quiz_sequence'], $_SESSION['quiz_current'], $_SESSION['score'], $_SESSION['start_time'], $_SESSION['quiz_current_answered']);
    header("Location: result.php?score=$quiz_score&wrong=$numWrong");
    exit();
}

$currentQuestion = $questions[$currentIndex];
$choices = $currentQuestion['choices'];
$correctIndex = $currentQuestion['correct_index'];
$selectedAnswer = null;
$feedback = "";
$showNext = false;
$quizOver = false;

// Timer
$elapsed = time() - $_SESSION['start_time'];
$timeLeft = $totalTime - $elapsed;
if ($timeLeft <= 0) {
    $quizOver = true;
}

// Handle answer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$quizOver) {
    $selectedAnswer = isset($_POST['answer']) ? intval($_POST['answer']) : null;
    $_SESSION['quiz_current_answered'][$currentIndex] = $selectedAnswer;

    if ($selectedAnswer === $correctIndex) {
        // +10 per correct to match waste game style
        $_SESSION['score'] += 10;
        $feedback = "ถูกต้อง!";
    } else {
        $feedback = "ผิด! คำตอบที่ถูกต้องคือ: " . htmlspecialchars($choices[$correctIndex]);
    }
    $showNext = true;
}

// Next question
if (isset($_POST['next'])) {
    $_SESSION['quiz_current']++;
    if ($_SESSION['quiz_current'] >= $numQuestions) {
        $quizOver = true;
    }
    header("Location: quiz.php");
    exit();
}

// Time up or finished
if ($quizOver || $_SESSION['quiz_current'] >= $numQuestions) {
    list($quiz_score, $waste_score) = save_leaderboard_now();
    $correctCount = (int) floor($quiz_score / 10);
    $numWrong = max(0, $numQuestions - $correctCount);
    unset($_SESSION['quiz_sequence'], $_SESSION['quiz_current'], $_SESSION['score'], $_SESSION['start_time'], $_SESSION['quiz_current_answered']);
    header("Location: result.php?score=$quiz_score&wrong=$numWrong");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Quiz Game</title>
    <link rel="stylesheet" href="style.css">
    <script src="timer.js"></script>
</head>

<body>
    <div class="progress-bar-bg">
        <div id="timerBar" class="progress-bar"></div>
    </div>
    <div class="timer-label">เวลาที่เหลือ: <span id="timeLeft"><?php echo max(0, $timeLeft); ?></span> วินาที</div>

    <div class="quiz-container">
        <form id="quizForm" method="post" action="quiz.php" autocomplete="off">
            <div class="quiz-card">
                <div class="question">
                    <span class="highlight">ข้อ <?php echo $currentIndex + 1; ?></span> /
                    <?php echo $numQuestions; ?><br>
                    <?php echo htmlspecialchars($currentQuestion['question']); ?>
                </div>

                <div class="choices">
                    <?php foreach ($choices as $i => $choice): ?>
                        <?php
                        $btnClass = 'choice-btn';
                        if ($showNext && $selectedAnswer !== null) {
                            if ($i == $selectedAnswer && $i == $correctIndex)
                                $btnClass .= ' correct';
                            else if ($i == $selectedAnswer && $i != $correctIndex)
                                $btnClass .= ' wrong';
                            else if ($i == $correctIndex)
                                $btnClass .= ' correct';
                        }
                        ?>
                        <button type="submit" name="answer" value="<?php echo $i; ?>" class="<?php echo $btnClass; ?>" <?php echo ($showNext ? 'disabled' : ''); ?>>
                            <?php echo htmlspecialchars($choice); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <?php if ($showNext && $selectedAnswer !== null): ?>
                    <div
                        class="feedback <?php echo ($selectedAnswer === $correctIndex) ? 'feedback-correct' : 'feedback-wrong'; ?>">
                        <?php echo $feedback; ?>
                    </div>
                    <button type="submit" name="next" class="btn-next">ข้อต่อไป</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>

</html>