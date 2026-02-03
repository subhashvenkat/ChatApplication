<?php
session_start();
$conn = new mysqli("localhost", "root", "", "chat_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT id, email, password FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
             if ($user['password'] == $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: chat.php");
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chat</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .input {
            width: 90%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            color: #555;
        }
        .input:focus {
            border-color: #5fae31;
            outline: none;
        }
        .btn {
            width: 100%;
            padding: 15px;
            background-color: #5fae31;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .btn:hover {
            background-color: #4e9c28;
        }
        .error-message {
            color: red;
            text-align: center;
            margin: 15px 0;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    
    <?php
    if (isset($error)) {
        echo "<p class='error-message'>$error</p>";
    }
    ?>
    <form action="login.php" method="POST">
        <input type="email" name="email" class="input" id="email" placeholder="Enter your email" required>
        <input type="password" name="password" class="input" id="password" placeholder="Enter your password" required>
        <button type="submit" class="btn">Login</button>
    </form>
</div>
</body>
</html>
