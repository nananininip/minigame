<?php
session_start();
require '../functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: ../index.php');
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
    if (!isset($_SESSION['waste_score'])) $_SESSION['waste_score'] = 0; // keep if already set
}
if (!isset($_SESSION['quiz_current_answered'])) {
    $_SESSION['quiz_current_answered'] = []; // avoid undefined index warnings
}

$questions = $_SESSION['quiz_sequence'];
$currentIndex = $_SESSION['quiz_current'];

// ---- Helper: save to leaderboard (merge, add time) ----
function save_leaderboard_now()
{
    $nickname    = $_SESSION['nickname'];
    $quiz_score  = isset($_SESSION['score']) ? (int) $_SESSION['score'] : 0; // points
    $waste_score = isset($_SESSION['waste_score']) ? (int) $_SESSION['waste_score'] : 0;
    $time_used   = max(0, time() - (int) ($_SESSION['start_time'] ?? time())); // seconds this quiz run

    // saveResultToLeaderboard($nickname, $quizDelta, $wasteDelta, $timeQuizDelta, $timeWasteDelta)
    saveResultToLeaderboard($nickname, $quiz_score, 0, $time_used, 0);
    return [$quiz_score, $waste_score];
}

// If we've run out of questions (safety)
if (!isset($questions[$currentIndex])) {
    list($quiz_score, $waste_score) = save_leaderboard_now();
    $correctCount = (int) floor($quiz_score / 10);
    $numWrong = max(0, $numQuestions - $correctCount); // <-- fixed from $GLOBALS['numQuestions']
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

// ==== Timer (server is source of truth) ====
$elapsed  = max(0, time() - (int)($_SESSION['start_time'] ?? time()));
$timeLeft = max(0, $totalTime - $elapsed);

// If time is up, end immediately
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
    <title>Green Quest: Hi-Trust Quiz Game</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- add data so timer.js can sync accurately -->
    <div class="progress-bar-bg"
         data-total="<?php echo (int)$totalTime; ?>"
         data-left="<?php echo (int)$timeLeft; ?>">
        <div id="timerBar" class="progress-bar"></div>
    </div>
    <div class="timer-label">เวลาที่เหลือ:
        <span id="timeLeft"><?php echo (int)$timeLeft; ?></span> วินาที
    </div>

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
                    <div class="feedback <?php echo ($selectedAnswer === $correctIndex) ? 'feedback-correct' : 'feedback-wrong'; ?>">
                        <?php echo $feedback; ?>
                    </div>
                    <button type="submit" name="next" class="btn-next">ข้อต่อไป</button>
                <?php endif; ?>
            </div>
            <!-- hidden field so timer.js can force-finish cleanly -->
            <input type="hidden" name="force_finish" id="force_finish" value="0">
        </form>
    </div>

    <!-- load timer after DOM to avoid race -->
    <script src="timer.js"></script>
</body>
</html>
