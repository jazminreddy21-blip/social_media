<?php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$dbName = "mySocial";

$connect = new mysqli($server, $username, $password, $dbName);
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = $_POST['password'];


    $SQL = "SELECT id, username, password FROM users WHERE email = '$email'";
    $results = $connect->query($SQL);

    if ($results && $results->num_rows == 1) {
        $row = $results->fetch_assoc();

        $id = $row['id'];
        $username = $row['username'];
        $hashing = $row['password'];

        if (password_verify($password, $hashing)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Username and password combination don’t match.";
        }
    } else {
        $errors[] = "Email wasn’t found.";
    }
}

$connect->close();
?>

?>

<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
		    background-color: rgba(255, 255, 255, 0.8);
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
            color: #333;
        }
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
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-link a {
            color: #008080;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php
        if(!empty($errors)){
            foreach($errors as $error){
                echo "<p class='error'>$error</p>";
            }
        }
        ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="reg.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
