<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

$leaderboard = getLeaderboard();

// Search functionality (search by nickname, which is the key of $leaderboard)
$searchNickname = isset($_POST['search_nickname']) ? trim($_POST['search_nickname']) : '';
if ($searchNickname) {
    $leaderboard = array_filter($leaderboard, function ($row, $name) use ($searchNickname) {
        return stripos($name, $searchNickname) !== false;
    }, ARRAY_FILTER_USE_BOTH);
}

// Sort by overall points (descending)
uasort($leaderboard, function ($a, $b) {
    return $b['overall'] - $a['overall'];
});
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <style>
        body {
            background-color: #f6f7fa;
            margin: 0;
            font-family: 'Prompt', Arial, sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            padding: 18px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            width: 100vw;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .navbar-brand {
            font-size: 2rem;
            font-weight: 700;
            color: #e67300;
            margin-left: 2rem;
            letter-spacing: 1px;
        }

        .navbar-buttons {
            margin-right: 2rem;
            display: flex;
            gap: 1rem;
        }

        .btn-exit {
            background-color: #dc3545;
            color: #fff;
            font-size: 1rem;
            font-weight: bold;
            border: 2px solid #0066cc;
            padding: 9px 20px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.16s;
        }

        .btn-exit:hover {
            background: #e96b7a;
        }

        .btn-submit,
        .btn-newQ {
            font-size: 1rem;
            font-weight: bold;
            padding: 9px 22px;
            color: #fff;
            border: 2px solid #0066cc;
            border-radius: 6px;
            cursor: pointer;
            background-color: #e67300;
            margin-left: 8px;
            transition: background 0.16s;
        }

        .btn-submit:hover,
        .btn-newQ:hover {
            background: #f59f42;
        }

        .container {
            background: #fff;
            padding: 32px 18px 24px 18px;
            border-radius: 16px;
            box-shadow: 0px 4px 24px #D3D3D3bb;
            max-width: 640px;
            margin: 110px auto 30px auto;
            text-align: center;
        }

        table {
            margin: 0 auto 8px auto;
            border-collapse: separate;
            border-spacing: 0;
        }

        th,
        td {
            border: 3px solid #96D4D4;
            padding: 13px 20px;
            text-align: center;
            background: #fff;
            min-width: 90px;
            font-size: 1.02rem;
        }

        th {
            font-size: 1.15rem;
            font-weight: 600;
            color: #0066cc;
            background: #eaf6ff;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        input[type="text"] {
            margin-top: 30px;
            padding: 6px;
            width: 30%;
            font-size: 20px;
            border: 2px solid #0066cc;
            border-radius: 5px;
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

        @media (max-width: 600px) {
            .container {
                margin-top: 120px;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar-brand,
            .navbar-buttons {
                margin: 0;
            }

            table,
            th,
            td {
                font-size: .99rem;
            }
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
            <input type="text" name="search_nickname" placeholder="Search by nickname"
                value="<?php echo htmlspecialchars($searchNickname); ?>">
            <button type="submit" class="btn-search">Search</button>
        </form>
        <table class="center">
            <tr>
                <th>Nickname</th>
                <th>Quiz</th>
                <th>Waste</th>
                <th>Overall</th>
            </tr>
            <?php $rank = 1; ?>
            <?php foreach ($leaderboard as $name => $row): ?>
                <tr style="<?php echo $rank == 1 ? 'background-color:#fffae6;font-weight:bold;' : ''; ?>">
                    <td><?php echo htmlspecialchars($name); ?></td>
                    <td><?php echo isset($row['quiz']) ? $row['quiz'] : 0; ?></td>
                    <td><?php echo isset($row['waste']) ? $row['waste'] : 0; ?></td>
                    <td><?php echo $row['overall']; ?></td>
                </tr>
                <?php $rank++; ?>
            <?php endforeach; ?>

        </table>
    </div>
</body>

</html>