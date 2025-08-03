<?php
require 'functions.php';  // <--- THIS IS REQUIRED!
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = $_POST['nickname'];
    $quiz = (int)$_POST['quiz'];
    $waste = (int)$_POST['waste'];
    $overall = (int)$_POST['overall'];
    $time_used = 0; // Or pass from JS if desired
    saveResultToLeaderboard($nickname, $quiz, $waste, $overall, $time_used);
    echo 'ok';
}
?>
