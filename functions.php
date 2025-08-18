<?php
// ====== STORAGE ======
define('LB_FILE', __DIR__ . '/quiz/leaderboard.txt');

/**
 * Parse one leaderboard line.
 * Supports old formats and upgrades them:
 *   7 fields: nickname|quiz|waste|overall|time_quiz|time_waste|updated_at
 *   6 fields: nickname|quiz|waste|overall|time_used|updated_at  (treated as time_quiz, time_waste=0)
 *   5 fields: nickname|quiz|waste|overall|time_used             (treated as time_quiz, updated_at=now)
 */
function lb_parse($line) {
    $p = array_map('trim', explode('|', trim($line)));
    $n = count($p);

    $rec = [
        'nickname'   => $p[0] ?? '',
        'quiz'       => isset($p[1]) ? (int)$p[1] : 0,
        'waste'      => isset($p[2]) ? (int)$p[2] : 0,
        'overall'    => isset($p[3]) ? (int)$p[3] : 0,
        'time_quiz'  => 0,
        'time_waste' => 0,
        'updated_at' => time(),
    ];

    if ($n >= 7) {
        $rec['time_quiz']  = (int)$p[4];
        $rec['time_waste'] = (int)$p[5];
        $rec['updated_at'] = (int)$p[6];
    } elseif ($n === 6) {
        // Legacy: single time_used -> assume it was quiz time; waste time = 0
        $rec['time_quiz']  = (int)$p[4];
        $rec['time_waste'] = 0;
        $rec['updated_at'] = (int)$p[5];
    } elseif ($n === 5) {
        $rec['time_quiz']  = (int)$p[4];
        $rec['time_waste'] = 0;
        $rec['updated_at'] = time();
    }

    if ($rec['overall'] === 0) $rec['overall'] = $rec['quiz'] + $rec['waste'];
    return $rec;
}

function lb_line($r) {
    return implode('|', [
        $r['nickname'],
        (int)$r['quiz'],
        (int)$r['waste'],
        (int)$r['overall'],
        (int)$r['time_quiz'],
        (int)$r['time_waste'],
        (int)$r['updated_at'],
    ]) . "\n";
}

/** Load ALL rows (entire history) */
function getLeaderboardAll() {
    if (!file_exists(LB_FILE)) return [];
    $rows = [];
    foreach (file(LB_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line === '') continue;
        $r = lb_parse($line);
        if ($r) $rows[] = $r;
    }
    return $rows;
}

/** Top 10 *per player* (best run only for each nickname) — Overall */
function getLeaderboardTop10PerPlayerOverall() {
    $all = getLeaderboardAll();
    $best = [];
    foreach ($all as $r) {
        $k = function_exists('mb_strtolower') ? mb_strtolower($r['nickname'], 'UTF-8') : strtolower($r['nickname']);
        if ($k === '') continue;
        if (!isset($best[$k])) { $best[$k] = $r; continue; }

        $cur  = $best[$k];
        $curT = $cur['time_quiz'] + $cur['time_waste'];
        $newT = $r['time_quiz'] + $r['time_waste'];

        $replace = false;
        if ($r['overall'] > $cur['overall']) $replace = true;
        elseif ($r['overall'] == $cur['overall']) {
            if ($newT < $curT) $replace = true;
            elseif ($newT == $curT && $r['quiz'] > $cur['quiz']) $replace = true;
        }
        if ($replace) $best[$k] = $r;
    }
    $rows = array_values($best);
    usort($rows, function($a,$b){
        $ta=$a['time_quiz']+$a['time_waste'];
        $tb=$b['time_quiz']+$b['time_waste'];
        if($a['overall']!==$b['overall']) return $b['overall']-$a['overall'];
        if($ta!==$tb) return $ta-$tb;
        if($a['quiz']!==$b['quiz']) return $b['quiz']-$a['quiz'];
        return strcasecmp($a['nickname'],$b['nickname']);
    });
    return array_slice($rows,0,10);
}

/** Top 10 *per player* — Quiz */
function getLeaderboardTop10PerPlayerQuiz() {
    $all = getLeaderboardAll();
    $best = [];
    foreach ($all as $r) {
        $k = function_exists('mb_strtolower') ? mb_strtolower($r['nickname'], 'UTF-8') : strtolower($r['nickname']);
        if ($k === '') continue;
        if (!isset($best[$k])) { $best[$k] = $r; continue; }
        $cur = $best[$k];
        $replace = false;
        if ($r['quiz'] > $cur['quiz']) $replace = true;
        elseif ($r['quiz'] == $cur['quiz'] && $r['time_quiz'] < $cur['time_quiz']) $replace = true;
        if ($replace) $best[$k] = $r;
    }
    $rows = array_values($best);
    usort($rows, function($a,$b){
        if($a['quiz']!==$b['quiz']) return $b['quiz']-$a['quiz'];
        if($a['time_quiz']!==$b['time_quiz']) return $a['time_quiz']-$b['time_quiz'];
        return strcasecmp($a['nickname'],$b['nickname']);
    });
    return array_slice($rows,0,10);
}

/** Top 10 *per player* — Waste */
function getLeaderboardTop10PerPlayerWaste() {
    $all = getLeaderboardAll();
    $best = [];
    foreach ($all as $r) {
        $k = function_exists('mb_strtolower') ? mb_strtolower($r['nickname'], 'UTF-8') : strtolower($r['nickname']);
        if ($k === '') continue;
        if (!isset($best[$k])) { $best[$k] = $r; continue; }
        $cur = $best[$k];
        $replace = false;
        if ($r['waste'] > $cur['waste']) $replace = true;
        elseif ($r['waste'] == $cur['waste'] && $r['time_waste'] < $cur['time_waste']) $replace = true;
        if ($replace) $best[$k] = $r;
    }
    $rows = array_values($best);
    usort($rows, function($a,$b){
        if($a['waste']!==$b['waste']) return $b['waste']-$a['waste'];
        if($a['time_waste']!==$b['time_waste']) return $a['time_waste']-$b['time_waste'];
        return strcasecmp($a['nickname'],$b['nickname']);
    });
    return array_slice($rows,0,10);
}

/** Append *one* run (append-only storage) */
function saveResultToLeaderboard($nickname, $quizDelta, $wasteDelta, $timeQuizDelta = 0, $timeWasteDelta = 0) {
    // sanitize nickname: trim, drop newlines, remove delimiter
    $nickname = trim((string)$nickname);
    $nickname = preg_replace('/\R/u', ' ', $nickname); // remove newlines
    $nickname = str_replace('|', ' ', $nickname);      // remove delimiter
    if ($nickname === '') return false;

    $entry = [
        'nickname'   => $nickname,
        'quiz'       => max(0,(int)$quizDelta),
        'waste'      => max(0,(int)$wasteDelta),
        'overall'    => max(0,(int)$quizDelta) + max(0,(int)$wasteDelta),
        'time_quiz'  => max(0,(int)$timeQuizDelta),
        'time_waste' => max(0,(int)$timeWasteDelta),
        'updated_at' => time(),
    ];

    $dir = dirname(LB_FILE);
    if (!is_dir($dir)) @mkdir($dir, 0775, true);

    $fp = @fopen(LB_FILE, 'a');
    if (!$fp) return false;
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, lb_line($entry));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
    return true;
}

// ====== QUIZ QUESTIONS ======
function getQuestions($topic, $num = 5) {
    $file = __DIR__ . '/quiz/questions.txt';
    if (!file_exists($file)) throw new Exception('Questions file not found.');
    $all = [];
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $parts = explode('|', $line);
        if (count($parts) < 6) continue;
        list($q_topic, $question, $c1, $c2, $c3, $correct) = $parts;
        if ($q_topic !== $topic) continue;

        $choices = [$c1, $c2, $c3];
        if (!in_array($correct, $choices, true)) $choices[] = $correct;
        shuffle($choices);
        $correct_index = array_search($correct, $choices, true);

        $all[] = ['question'=>$question,'choices'=>$choices,'correct_index'=>$correct_index];
    }
    shuffle($all);
    return array_slice($all, 0, $num);
}

function fmtMMSS($sec) {
    $sec = max(0, (int)$sec);
    return sprintf('%d:%02d', floor($sec/60), $sec%60);
}

/** Legacy shim: some pages may still call this old helper */
if (!function_exists('getLeaderboard')) {
    function getLeaderboard() { return getLeaderboardAll(); }
}
