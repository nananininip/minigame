<?php
function getQuestions($topic, $num = 5) {
    $file = __DIR__ . '/questions.txt';
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
                'correct' => $correct
            ];
        }
    }
    shuffle($questions);

    if (count($questions) < $num) {
        throw new Exception("Not enough questions available for this topic.");
    }

    $selected = array_slice($questions, 0, $num);

    // Lock correct answer index after shuffling
    foreach ($selected as &$q) {
        $choices = $q['choices'];
        shuffle($choices);
        $q['choices'] = $choices;
        $q['correct_index'] = array_search($q['correct'], $choices);
    }
    return $selected;
}


function saveResultToLeaderboard($nickname, $quiz, $waste, $overall, $time_used) {
    $file = __DIR__ . '/leaderboard.txt';
    $leaderboard = [];

    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $fields = explode('|', $line);
            if (count($fields) < 5) continue;
            list($name, $q, $w, $o, $t) = $fields;
            $leaderboard[] = [
                'nickname' => $name,
                'quiz' => (int)$q,
                'waste' => (int)$w,
                'overall' => (int)$o,
                'time_used' => (int)$t
            ];
        }
    }

    $found = false;
    foreach ($leaderboard as &$entry) {
        if ($entry['nickname'] == $nickname) {
            $found = true;
            // Always use latest (or pick your own logic)
            $entry['quiz'] = $quiz;
            $entry['waste'] = $waste;
            $entry['overall'] = $overall;
            $entry['time_used'] = $time_used;
        }
    }
    if (!$found) {
        $leaderboard[] = [
            'nickname' => $nickname,
            'quiz' => $quiz,
            'waste' => $waste,
            'overall' => $overall,
            'time_used' => $time_used
        ];
    }

    // Sort, as you wish
    usort($leaderboard, function($a, $b) {
        if ($b['overall'] == $a['overall']) {
            return $a['time_used'] - $b['time_used'];
        }
        return $b['overall'] - $a['overall'];
    });
    $leaderboard = array_slice($leaderboard, 0, 10);

    $fp = fopen($file, 'w');
    foreach ($leaderboard as $entry) {
        fwrite($fp, implode('|', [
            $entry['nickname'],
            $entry['quiz'],
            $entry['waste'],
            $entry['overall'],
            $entry['time_used']
        ]) . "\n");
    }
    fclose($fp);
}


function getLeaderboard() {
    $file = __DIR__ . '/leaderboard.txt';
    $leaderboard = [];

    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $fields = explode('|', $line);
            if (count($fields) < 5) continue;
            list($name, $quiz, $waste, $overall, $time_used) = $fields;
            $leaderboard[$name] = [
                'quiz' => (int)$quiz,
                'waste' => (int)$waste,
                'overall' => (int)$overall,
                'time_used' => (int)$time_used
            ];
        }
    }
    // You can return all, or slice for top N
    return $leaderboard;
}

?>
