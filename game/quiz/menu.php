<?php
session_start();
if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}
?>
<html>
<head>
    <title>Challenge FUN - Menu</title>
	 <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            background-color: #f9e8e1;
    
        }
        .navbar {
            background-color: rgba(255, 255, 255, 0.9); 
            padding: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
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
            max-width: 300px;
            margin: 100px auto 20px auto; /* Adjusted for fixed navbar */
            align-items: center;
            text-align: center;
            margin-top: 10%;
        }
        li{
            font-size: 20px;
            font-weight: bold;
            color: #0099ff;
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
            top: 30px;
            right: 50px;

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
            margin-top: 10px;
            margin-left: 10px;
        } 
        .question-container {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
  <nav class="navbar">
        <a class="navbar-brand" href="menu.php">Menu</a>
        <div class="navbar-buttons">
            <form action="exit.php" method="post" style="display: inline;">
                <button type="submit" class="btn-exit">Exit</button>
            </form>
            <form action="menu.php" method="post" style="display: inline;">
                <button type="submit" class="btn-submit"style="right: 110px; position: relative;">Menu</button>
            </form>
        </div>
    </nav>
	 <div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['nickname']); ?></h1>
    <ul> 
        <li><a href="quiz.php?topic=movies">Movies</a></li> 
        <li><a href="quiz.php?topic=sport">Sport</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>

    </ul>
	</div>
</body>
</html>

