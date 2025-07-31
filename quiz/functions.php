<?php

// Load 4 random questions from a given topic
function getQuestions($topic) {
    $file = 'questions.txt';
    $questions = [];

    if (!file_exists($file)) {
        throw new Exception("Questions file not found.");
    }

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

    if (count($questions) < 4) {
        throw new Exception("Not enough questions available for this topic.");
    }

    shuffle($questions);
    return array_slice($questions, 0, 4);
}

// Get current leaderboard scores from file
function getLeaderboard() {
    $file = 'leaderboard.txt';
    $leaderboard = [];

    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            $nickname = trim($parts[0]);
            $quiz = isset($parts[1]) ? (int)trim($parts[1]) : 0;
            $waste = isset($parts[2]) ? (int)trim($parts[2]) : 0;
            $overall = $quiz + $waste;  // recalculate every time
            $leaderboard[$nickname] = [
                'quiz' => $quiz,
                'waste' => $waste,
                'overall' => $overall
            ];
        }
    }

    return $leaderboard;
}

// Save or update user's score properly
function saveScore($nickname, $score, $topic) {
    $filename = 'leaderboard.txt';
    $data = [];

    // Load existing data
    if (file_exists($filename)) {
        $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            $name = $parts[0];
            $quizScore = isset($parts[1]) ? (int)$parts[1] : 0;
            $wasteScore = isset($parts[2]) ? (int)$parts[2] : 0;
            $data[$name] = ['quiz' => $quizScore, 'waste' => $wasteScore];
        }
    }

    // Update or initialize user's scores
    if (!isset($data[$nickname])) {
        $data[$nickname] = ['quiz' => 0, 'waste' => 0];
    }

    // Increment points according to the game topic
    if (isset($data[$nickname][$topic])) {
        $data[$nickname][$topic] += $score;
    } else {
        $data[$nickname][$topic] = $score; // safety check
    }

    // Rewrite the file with correct individual overall scores
    $lines = [];
    foreach ($data as $name => $scores) {
        $overall = $scores['quiz'] + $scores['waste']; // compute per user
        $lines[] = "{$name}|{$scores['quiz']}|{$scores['waste']}|{$overall}";
    }

    file_put_contents($filename, implode(PHP_EOL, $lines));
}
