<?php
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname  = isset($_POST['nickname']) ? trim($_POST['nickname']) : '';
    $quiz      = isset($_POST['quiz']) ? (int)$_POST['quiz'] : 0;
    $waste     = isset($_POST['waste']) ? (int)$_POST['waste'] : 0;
    $overall   = $quiz + $waste; // server recompute (ignored but kept here for clarity)
    $time_used = isset($_POST['time_used']) ? (int)$_POST['time_used'] : 0; // this is WASTE time

    // Save: quizDelta, wasteDelta, timeQuizDelta, timeWasteDelta
    saveResultToLeaderboard($nickname, $quiz, $waste, 0, $time_used);

    echo 'ok';
    exit;
}

http_response_code(405);
echo 'Method Not Allowed';
