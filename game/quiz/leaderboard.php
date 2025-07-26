<?php
session_start();
require 'functions.php';

// Redirect to the index page if the nickname is not set in the session
if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

// Get the leaderboard data
$leaderboardData = getLeaderboard();

// Convert the associative array into an indexed array for easier sorting
$leaderboard = [];
foreach ($leaderboardData as $nickname => $score) {
    $leaderboard[] = [
        'nickname' => $nickname,
        'score' => $score
    ];
}

// Search functionality
$searchNickname = isset($_POST['search_nickname']) ? trim($_POST['search_nickname']) : '';
if ($searchNickname) {
    $leaderboard = array_filter($leaderboard, function($player) use ($searchNickname) {
        return stripos($player['nickname'], $searchNickname) !== false; // Case-insensitive search
    });
}

// Determine the order by parameter
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'score';
if ($order_by === 'nickname') {
    // Sort by nickname
    usort($leaderboard, function($a, $b) {
        return strcmp($a['nickname'], $b['nickname']);
    });
} else {
    // Sort by score (default)
    usort($leaderboard, function($a, $b) {
        return $b['score'] - $a['score'];
    });
}
?>
<html>
<head>
    <title>Challenge FUN - Leaderboard</title>
    <style>
        .navbar {
            background-color: white;
            background: rgba(300, 300, 300, 0.9);
            padding: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            width: 100%;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .container {
            background-color: #FFFFFF;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #D3D3D3;
            max-width: 600px;
            margin: 200px auto 20px auto;
            text-align: center;
        }
        th, td {
            border: 4px solid #96D4D4;
            padding: 15px;
            text-align: center;
            position: relative; 
            background-color: #fff; 
            box-shadow: 0 0 0 2px #0066cc; 
        }
        th {
            font-size: 16px;
            font-weight: bold;
            color: #0066cc;
        }
        .btn-exit {
            background-color: #dc3545;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            border: 3px solid #0066cc;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            position: absolute;
            top: 40px;
            right: 90px;
        }
        .btn-submit {
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            color: #ffffff;
            border: 3px solid #0066cc;
            cursor: pointer;
            background-color: #e67300;
            border-radius: 5px;
            margin-top: 20px;
        } 
        table.center {
            margin-left: auto; 
            margin-right: auto;
        }
        .btn-search {
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 30px;
            margin-bottom: 25px;
        }
        input[type="text"] {
            margin-top: 30px;
            padding: 6px;
            width: 30%;
            font-size: 20px;
            border: 2px solid #0066cc; 
            border-radius: 5px;

        }
        table.center {
            margin-left: auto; 
            margin-right: auto;
        }
        .sort-indicator {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a class="navbar-brand" href="menu.php">Leaderboard</a>
        <div class="navbar-buttons">
            <form action="exit.php" method="post" style="display: inline;">
                <button type="submit" class="btn-exit">Exit</button>
            </form>
            <form action="menu.php" method="post" style="display: inline;">
                <button type="submit" class="btn-submit">Menu</button>
            </form>
        </div>
    </nav>
   
    <div class="container">
        <h1>Leaderboard</h1>
        <form method="POST">
            <input type="text" name="search_nickname" placeholder="Search by nickname" value="<?php echo htmlspecialchars($searchNickname); ?>">
            <button type="submit" class="btn-search">Search</button>
        </form>
        <table class="center">
            <tr>
                <th>
                    <a href="?order_by=nickname">Nickname</a>
                    <?php if ($order_by === 'nickname') echo '<span class="sort-indicator">↑</span>'; ?>
                </th>
                <th>
                    <a href="?order_by=score">Score</a>
                    <?php if ($order_by === 'score') echo '<span class="sort-indicator">↑</span>'; ?>
                </th>
            </tr>
            <?php foreach ($leaderboard as $player): ?>
                <tr>
                    <td><?php echo htmlspecialchars($player['nickname']); ?></td>
                    <td><?php echo htmlspecialchars($player['score']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <form action="?order_by=score" method="post" style="display: inline;">
            <button type="submit" class="btn-submit">Sort by Highest Score</button>
        </form>
        <form action="menu.php" method="post" style="display: inline;">
            <button type="submit" class="btn-submit">Back to Menu</button>
        </form>
    </div>
</body>
</html>
