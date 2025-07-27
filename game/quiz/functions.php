<?php

function getQuestions($topic) {
    $file = 'questions.txt';
    $questions = [];
    if (!file_exists($file)) throw new Exception("Questions file not found.");
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('|', $line);
        if (count($parts) < 6) continue;
        list($question_topic, $question, $choice1, $choice2, $choice3, $correct) = $parts;
        if ($question_topic === $topic) {
            $questions[] = [
                'question' => $question,
                'choices' => [$choice1, $choice2, $choice3],
                'answer' => $correct
            ];
        }
    }
    if (count($questions) < 4) throw new Exception("Not enough questions available for this topic.");
    shuffle($questions);
    return array_slice($questions, 0, 4);
}

// Always recompute 'overall' before saving
function getLeaderboard() {
    $file = 'leaderboard.txt';
    $leaderboard = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            // support legacy (quiz|waste) and new (quiz|waste|overall)
            $nickname = trim($parts[0]);
            $quiz = isset($parts[1]) ? (int)trim($parts[1]) : 0;
            $waste = isset($parts[2]) ? (int)trim($parts[2]) : 0;
            // calculate on the fly, ignore stored overall
            $overall = $quiz + $waste;
            $leaderboard[$nickname] = ['quiz'=>$quiz, 'waste'=>$waste, 'overall'=>$overall];
        }
    }
    return $leaderboard;
}

// Save/update score for a user and a game ('quiz' or 'waste'), always update overall
function saveScore($nickname, $gameScore, $game = 'quiz') {
    $file = 'leaderboard.txt';
    $scores = getLeaderboard();

    // Initialize if new
    if (!isset($scores[$nickname])) $scores[$nickname] = ['quiz'=>0, 'waste'=>0, 'overall'=>0];

    // Only the relevant game gets added
    if ($game == 'quiz') {
        $scores[$nickname]['quiz'] += $gameScore;
    } elseif ($game == 'waste') {
        $scores[$nickname]['waste'] += $gameScore;
    }
    // Always recalc overall (DO NOT just increment the old value)
    $scores[$nickname]['overall'] = $scores[$nickname]['quiz'] + $scores[$nickname]['waste'];

    // Write all
    $handle = fopen($file, 'w');
    if ($handle) {
        foreach ($scores as $player => $vals) {
            // Always write quiz|waste|overall so it's always correct
            fwrite($handle, $player . '|' . $vals['quiz'] . '|' . $vals['waste'] . '|' . $vals['overall'] . PHP_EOL);
        }
        fclose($handle);
    } else {
        throw new Exception("Failed to open leaderboard file for writing.");
    }
}
?>
