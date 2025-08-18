<?php
require '../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname   = isset($_POST['nickname']) ? trim($_POST['nickname']) : '';
    $quiz       = isset($_POST['quiz']) ? (int)$_POST['quiz'] : 0;
    $waste      = isset($_POST['waste']) ? (int)$_POST['waste'] : 0;
    $time_quiz  = isset($_POST['time_quiz'])  ? (int)$_POST['time_quiz']  : 0;
    $time_waste = isset($_POST['time_waste']) ? (int)$_POST['time_waste'] : 0;

    // Legacy support: some older callers send time_used (waste time)
    if ($time_waste === 0 && isset($_POST['time_used'])) {
        $time_waste = (int)$_POST['time_used'];
    }

    if ($nickname === '') {
        http_response_code(400);
        echo 'Missing nickname';
        exit;
    }

    // Append one run (append-only). UI aggregates per player.
    saveResultToLeaderboard($nickname, $quiz, $waste, $time_quiz, $time_waste);

    echo 'ok';
    exit;
}

http_response_code(405);
echo 'Method Not Allowed';
