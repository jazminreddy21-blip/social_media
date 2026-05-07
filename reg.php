<?php
session_start();

$connect = new mysqli("localhost", "root", "", "mySocial");
if ($connect->connect_error) die("Connection failed: " . $connect->connect_error);

$errors = [];
$username = $email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordt = $_POST['passwordt'] ?? '';

if (!$username) {
        $errors[] = "Full name must be filled.";
    } elseif (!preg_match("/^[a-zA-Z ]{2,50}$/", $username)) {
        $errors[] = "Full name can only contain letters .";
    }



    if (!$email) {
        $errors[] = "Email must be filled.";
    } elseif (!preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $email)) {
        $errors[] = "Please enter a valid email address.";
    }

  
    if (!$password) $errors[] = "Password must be filled.";
    if (!$passwordt) $errors[] = "Confirmtion of password must be filled.";
    if ($password && $passwordt && $password !== $passwordt) {
        $errors[] = "Passwords dont  match.";
    }

    if ($email) {
        $safeemail = $connect->real_escape_string($email);
        $check = $connect->query("SELECT id FROM users WHERE email='$safeemail' LIMIT 1");
        if ($check && $check->num_rows > 0) {
            $errors[] = "This email already exists please log in ";
        }
    }

 
    if (empty($errors)) {
        $safename = $connect->real_escape_string($username);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $insert = "INSERT INTO users (username, email, password) VALUES ('$safename', '$safeemail', '$hash')";
        if ($connect->query($insert)) {
            $_SESSION['user_id'] = $connect->insert_id;
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Error creating account: " . $connect->error;
        }
    }
}
?>

<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;

			background-color: rgba(255, 255, 255, 0.8);;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; 
			background-size:cover;
        }
        .container {
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #323;
        }
        form input[type="text"],
        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #008080;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #800000;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            color: #008080;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Register</h2>
        <?php if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p class='error'>$error</p>";
            }
        } ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="username" >
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="passwordt" placeholder="Confirm Password" required>
            <input type="submit" value="Register">
        </form>
        <div class="login">
            <p>Already have an account? <a href="logging.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
