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
function saveScore($nickname, $score, $gameType) {
    $filename = 'leaderboard.txt';
    $leaderboard = [];

    // Load existing data
    if (file_exists($filename)) {
        $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            $name = $parts[0];
            $leaderboard[$name] = [
                'quiz' => isset($parts[1]) ? (int)$parts[1] : 0,
                'waste' => isset($parts[2]) ? (int)$parts[2] : 0,
                'overall' => isset($parts[3]) ? (int)$parts[3] : 0
            ];
        }
    }

    // If user not in leaderboard, initialize
    if (!isset($leaderboard[$nickname])) {
        $leaderboard[$nickname] = ['quiz' => 0, 'waste' => 0, 'overall' => 0];
    }

    // Update score
    $leaderboard[$nickname][$gameType] = $score;
    $leaderboard[$nickname]['overall'] = $leaderboard[$nickname]['quiz'] + $leaderboard[$nickname]['waste'];

    // Save back to file
    $fp = fopen($filename, 'w');
    foreach ($leaderboard as $name => $scores) {
        $line = implode('|', [$name, $scores['quiz'], $scores['waste'], $scores['overall']]);
        fwrite($fp, $line . PHP_EOL);
    }
    fclose($fp);
}
