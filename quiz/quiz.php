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

if (!isset($_SESSION['quiz_sequence'])) {
    $_SESSION['quiz_sequence'] = getQuestions($topic, $numQuestions);
    $_SESSION['quiz_current'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['start_time'] = time();
    $_SESSION['waste_score'] = 0;
}

$questions = $_SESSION['quiz_sequence'];
$currentIndex = $_SESSION['quiz_current'];

// Helper
function save_leaderboard_now() {
    $nickname = $_SESSION['nickname'];
    $quiz_score = isset($_SESSION['score']) ? $_SESSION['score'] : 0;
    $waste_score = isset($_SESSION['waste_score']) ? $_SESSION['waste_score'] : 0;
    $overall = $quiz_score + $waste_score;
    $time_used = time() - $_SESSION['start_time'];
    saveResultToLeaderboard($nickname, $quiz_score, $waste_score, $overall, $time_used);
    return [$quiz_score, $waste_score];
}

if (!isset($questions[$currentIndex])) {
    list($quiz_score, $waste_score) = save_leaderboard_now();
    $numWrong = $numQuestions - $quiz_score;
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

$elapsed = time() - $_SESSION['start_time'];
$timeLeft = $totalTime - $elapsed;
if ($timeLeft <= 0) {
    $quizOver = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$quizOver) {
    $selectedAnswer = isset($_POST['answer']) ? intval($_POST['answer']) : null;
    $_SESSION['quiz_current_answered'][$currentIndex] = $selectedAnswer;
    if ($selectedAnswer === $correctIndex) {
        $_SESSION['score']++;
        $feedback = "ถูกต้อง!";
    } else {
        $feedback = "ผิด! คำตอบที่ถูกต้องคือ: " . htmlspecialchars($choices[$correctIndex]);
    }
    $showNext = true;
}

if (isset($_POST['next'])) {
    $_SESSION['quiz_current']++;
    if ($_SESSION['quiz_current'] >= $numQuestions) {
        $quizOver = true;
    }
    header("Location: quiz.php");
    exit();
}

if ($quizOver || $_SESSION['quiz_current'] >= $numQuestions) {
    list($quiz_score, $waste_score) = save_leaderboard_now();
    $numWrong = $numQuestions - $quiz_score;
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
    <div class="navbar"><span class="navbar-brand">Mini Quiz Game</span>
        <form action="exit.php" method="post" style="display:inline;">
            <button type="submit" class="btn-exit">ออก</button>
        </form>
    </div>
    <div class="progress-bar-bg">
        <div id="timerBar" class="progress-bar"></div>
    </div>
    <div class="timer-label">เวลาที่เหลือ: <span id="timeLeft"><?php echo $timeLeft; ?></span> วินาที</div>
    <div class="quiz-container">
        <form id="quizForm" method="post" action="quiz.php" autocomplete="off">
            <div class="quiz-card">
                <div class="question">
                    <span class="highlight">ข้อ <?php echo $currentIndex + 1; ?></span> / <?php echo $numQuestions; ?><br>
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
                        <button type="submit" name="answer" value="<?php echo $i; ?>"
                            class="<?php echo $btnClass; ?>" <?php echo ($showNext ? 'disabled' : ''); ?>>
                            <?php echo htmlspecialchars($choice); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php if ($showNext && $selectedAnswer !== null): ?>
                    <div class="feedback <?php echo ($selectedAnswer === $correctIndex) ? 'feedback-correct' : 'feedback-wrong'; ?>">
                        <?php echo $feedback; ?>
                    </div>
                    <button type="submit" name="next" class="btn-next">ข้อต่อไป</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>
</html>
