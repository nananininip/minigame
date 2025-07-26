<?php

function getQuestions($topic) {
    $file = 'questions.txt';
    $questions = [];

    // Check if the questions file exists
    if (!file_exists($file)) {
        throw new Exception("Questions file not found.");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        list($question_topic, $question, $answer, $image) = explode('|', $line);
        if ($question_topic === $topic) {
            $questions[] = [
                'question' => $question,
                'answer' => $answer,
                'image' => $image
            ];
        }
    }

    // Ensure at least 4 questions are available for the quiz
    if (count($questions) < 4) {
        throw new Exception("Not enough questions available for this topic.");
    }

    shuffle($questions);
    return array_slice($questions, 0, 4);
}

function getLeaderboard() {
    $file = 'leaderboard.txt';
    $leaderboard = [];

    // Check if the file exists
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            list($nickname, $score) = explode('|', $line);
            // Store scores in an associative array
            $leaderboard[trim($nickname)] = (int)trim($score);
        }
    }

    return $leaderboard;
}

function saveScore($nickname, $currentGameScore) {
    $file = 'leaderboard.txt';
    $current_scores = getLeaderboard(); // Get the current scores

    // Check if the player already exists in the leaderboard
    if (isset($current_scores[$nickname])) {
        // If the player exists, add the current game score to their overall score
        $current_scores[$nickname] += $currentGameScore; // Update overall score
    } else {
        // If the player does not exist, set their score to the current game score
        $current_scores[$nickname] = $currentGameScore;
    }

    // Write updated scores back to the file
    $handle = fopen($file, 'w'); // Open the file for writing (this overwrites existing content)
    if ($handle) {
        foreach ($current_scores as $player => $playerScore) {
            fwrite($handle, $player . '|' . $playerScore . PHP_EOL); // Write each player and their score
        }
        fclose($handle); // Close the file
    } else {
        throw new Exception("Failed to open leaderboard file for writing."); // Improved error handling
    }
}
?>
