<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit();
}

// Initialize overall points if not set
if (!isset($_SESSION['overall_points'])) {
    $_SESSION['overall_points'] = 0;
}

// Check if quiz is already initialized in the session
if (!isset($_SESSION['current_quiz'])) {
    $topic = $_GET['topic'];
    $questions = getQuestions($topic);
    $_SESSION['current_quiz'] = $questions; // Store the current quiz in session
    $_SESSION['correct'] = 0;
    $_SESSION['incorrect'] = 0;
} else {
    $questions = $_SESSION['current_quiz']; // Retrieve questions from session
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $answers = $_POST['answers'];
    foreach ($answers as $index => $answer) {
        // Check the answer against the questions in the session
        if (trim(strtolower($answer)) == trim(strtolower($questions[$index]['answer']))) {
            $_SESSION['correct']++;
        } else {
            $_SESSION['incorrect']++;
        }
    }

    // Calculate the current game score
    $currentGameScore = ($_SESSION['correct'] * 3) - ($_SESSION['incorrect'] * 1);
    
    // Save the score only if the quiz is completed
    $nickname = $_SESSION['nickname'];
    saveScore($nickname, $currentGameScore); // Save current game score to leaderboard

    // Update overall points in session
    $_SESSION['overall_points'] += $currentGameScore; // Update overall points

    // Clear current quiz session data for next quiz
    unset($_SESSION['current_quiz']);
    
    header('Location: result.php'); // Redirect to results page
    exit();
}
?>

<html>
<head>
    <title>Challenge FUN - Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            
        }
        .navbar {
            background-color: rgba(255, 255, 255, 0.9); /* Corrected rgba value */
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
            background-color: #e6f7ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #D3D3D3;
            max-width: 790px;
            margin: 100px auto 20px auto; /* Adjusted for fixed navbar */
            align-items: center;
            text-align: center;
        }
        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
            margin-bottom: 20px;
            width: auto;
            display: flex;
            flex-direction: column;
            
        }
        .card-body {
            padding: 15px;
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
    </style>
</head>
<body>
    <nav class="navbar">
        <a class="navbar-brand" href="menu.php">Quiz</a>
        <div class="navbar-buttons">
            <form action="exit.php" method="post" style="display: inline;">
                <button type="submit" class="btn-exit">Exit</button>
            </form>
            <form action="menu.php" method="post" style="display: inline;">
                <button type="submit" class="btn-submit" style="right: 110px; position: relative;">Menu</button>
            </form>
        </div>
    </nav>
    <form method="post">
        <div class="container">
            <h1><?php echo ucfirst($topic); ?> Quiz</h1>
            <?php foreach ($questions as $index => $question): ?>
                <div class="card">
                    <div class="card-body">
                        <?php if ($topic == 'movies'): ?>
                            <p><?php echo htmlspecialchars($question['question']); ?></p>
                            <label><input type="radio" name="answers[<?php echo $index; ?>]" value="true" required> True</label>
                            <label><input type="radio" name="answers[<?php echo $index; ?>]" value="false" required> False</label>
                        <?php else: ?> 
                            <img src="<?php echo htmlspecialchars($question['image']); ?>" width="250"  alt="Question Image">
                            <p><?php echo htmlspecialchars($question['question']); ?></p>
                            <input type="text" name="answers[<?php echo $index; ?>]">
                        <?php endif; ?>
                    </div>
                </div>	
            <?php endforeach; ?>
            <button class="btn-submit" type="submit">Submit Answers</button>
        </div>
    </form>
</body>
</html>