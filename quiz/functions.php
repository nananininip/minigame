<?php
// ====== STORAGE ======
define('LB_FILE', __DIR__ . '/leaderboard.txt');

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

function getLeaderboard() {
    if (!file_exists(LB_FILE)) return [];
    $rows = [];
    foreach (file(LB_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line === '') continue;
        $rows[] = lb_parse($line);
    }
    return $rows;
}

/**
 * Merge-save:
 *   $quizDelta / $wasteDelta   = new points from this run (0 if not played)
 *   $timeQuizDelta / $timeWasteDelta = seconds to ADD (0 if not played)
 * Keeps best component scores (max), sums time per game, recomputes overall.
 */
function saveResultToLeaderboard($nickname, $quizDelta, $wasteDelta, $timeQuizDelta = 0, $timeWasteDelta = 0) {
    $nickname = trim($nickname);
    if ($nickname === '') return false;

    $rows = getLeaderboard();
    $found = false;

    foreach ($rows as &$r) {
        if (strcasecmp($r['nickname'], $nickname) === 0) {
            $r['quiz']       = max((int)$r['quiz'],  (int)$quizDelta);
            $r['waste']      = max((int)$r['waste'], (int)$wasteDelta);
            $r['time_quiz']  = max(0, (int)$r['time_quiz'])  + max(0, (int)$timeQuizDelta);
            $r['time_waste'] = max(0, (int)$r['time_waste']) + max(0, (int)$timeWasteDelta);
            $r['overall']    = (int)$r['quiz'] + (int)$r['waste'];
            $r['updated_at'] = time();
            $found = true; break;
        }
    } unset($r);

    if (!$found) {
        $rows[] = [
            'nickname'   => $nickname,
            'quiz'       => max(0, (int)$quizDelta),
            'waste'      => max(0, (int)$wasteDelta),
            'overall'    => max(0, (int)$quizDelta) + max(0, (int)$wasteDelta),
            'time_quiz'  => max(0, (int)$timeQuizDelta),
            'time_waste' => max(0, (int)$timeWasteDelta),
            'updated_at' => time(),
        ];
    }

    // Keep only top 10 by overall desc, total time asc, quiz desc, name
    usort($rows, function($a, $b) {
        $ta = $a['time_quiz'] + $a['time_waste'];
        $tb = $b['time_quiz'] + $b['time_waste'];
        if ($a['overall'] !== $b['overall']) return $b['overall'] - $a['overall'];
        if ($ta !== $tb) return $ta - $tb;
        if ($a['quiz'] !== $b['quiz']) return $b['quiz'] - $a['quiz'];
        return strcasecmp($a['nickname'], $b['nickname']);
    });
    $rows = array_slice($rows, 0, 10);

    $tmp = LB_FILE . '.tmp';
    file_put_contents($tmp, implode('', array_map('lb_line', $rows)));
    rename($tmp, LB_FILE);
    return true;
}

// ====== QUIZ QUESTIONS ======
function getQuestions($topic, $num = 5) {
    $file = __DIR__ . '/questions.txt';
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
?>
