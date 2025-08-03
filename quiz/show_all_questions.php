<?php
require 'functions.php';

// Only load the first 12 questions
try {
    $all = getQuestions('quiz', 12);
} catch (Exception $e) {
    echo "<h2>Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Test First 12 Questions</title>
    <style>
        body { font-family: 'Kanit', Arial, sans-serif; background: #fcffe7; margin: 40px; }
        .question-box {
            border: 1.5px solid #aee571;
            border-radius: 14px;
            background: #fff;
            margin-bottom: 22px;
            padding: 20px 20px 16px 20px;
            box-shadow: 0 3px 18px #d0ffc46e;
            max-width: 700px;
        }
        .question-title { font-weight: bold; color: #2573b9; font-size: 1.22rem; }
        .choice { margin: 7px 0 0 18px; font-size: 1.1em; color: #555; }
        .correct { color: #23a232; font-weight: 700; background: #e7ffe2; border-radius: 6px; padding: 2px 7px; }
        .mark { font-weight: bold; font-size: 1.16em; margin-left: 7px; color: #2bb150;}
        .q-num { color: #3b8c5a; margin-right: 14px; }
    </style>
</head>
<body>
    <h2>First 12 Questions for <span style="color:#2bb150">Quiz</span> (<?php echo count($all); ?> ข้อ)</h2>
    <?php foreach ($all as $n => $q): ?>
        <div class="question-box">
            <div class="question-title">
                <span class="q-num">Q<?php echo ($n+1); ?>.</span>
                <?php echo htmlspecialchars($q['question']); ?>
            </div>
            <div>
            <?php foreach ($q['choices'] as $i => $c): ?>
                <?php $isCorrect = ($i == $q['correct_index']); ?>
                <div class="choice<?php echo $isCorrect ? ' correct' : ''; ?>">
                    <?php echo chr(65+$i) . ". " . htmlspecialchars($c); ?>
                    <?php if ($isCorrect): ?><span class="mark">&#10004;</span><?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>
