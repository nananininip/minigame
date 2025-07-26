<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['nickname'] = $_POST['nickname'];
    $_SESSION['overall_points'] = 0;
    header('Location: menu.php');
    exit();
}
?>

<html>
<head>
    <title>MiniGame</title>
	   <style>
        .error {color: #FF0000;}
        .header-box {
            background-color: white; 
            background: rgba(300, 300, 300, 0.9);
            padding: 20px; /* Spacing inside the box */
            text-align: center; /* Center-align text */
            border-radius: 15px; /* Rounded corners for a softer look */
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            margin: 0 auto; /* Centered horizontally */
            width: 100%; /* Adjusts width to fit content */
            position: absolute; /* Position it absolutely */
            top: 0; /* Position it at the top of the page */
            left: 50%; /* Center horizontally */
            transform: translateX(-50%); /* Center horizontally relative to the left position */
        }

        h3 {
            text-align: center;
            color: black;
            font: italic bold 70px Georgia, Serif;
            text-shadow: -4px 3px 0 #ffe0cc, -14px 7px 0 #ffa366;
            margin: 0px;
        }

        body {
            background: url('/A1-T2FG14-7765290,8221601(Janeeta,Punyisa)/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            text-align: center;
            font-family: verdana;
            font-size: 20px;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            
        }
       
        .form-container {
            background: rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #D3D3D3;
            text-align: left;
            width: 30%;
            
        }
         label {
            font-weight: bold;
            color: #e67300;
            display: block;
            margin-bottom: 20px;
        }

        input[type="text"] {
            margin-bottom: 5px;
            padding: 5px;
            width: 90%;
            font-size: 20px;
            border: 2px solid #0066cc; 
            border-radius: 5px;
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
       
    </style>
</head>
<body>
    <div class="header-box">
    <h3>MiniGame</h3>
</div>
<div class="form-container">
    <form method="post">
        <label for="nickname">Enter your nickname:</label>
        <input type="text" id="nickname" name="nickname" required>
		 <input type="submit" class="btn-submit" value="Let's Begin">
    </form>
    </div>
</body>
</html>
