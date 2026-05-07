
<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: reg.php");
    exit;
}

$server = "localhost";
$username = "root";
$password = "";
$dbName = "mySocial";

$connect = new mysqli($server, $username, $password, $dbName);
if($connect->connect_error){
    die("Connection failed: ".$connect->connect_error);
}


if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])){
    $content = mysqli_real_escape_string($connect, $_POST['content']);
    $user_id = $_SESSION['user_id'];
    $image = null;

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $targetDir = "uploads/";
        if(!is_dir($targetDir)) mkdir($targetDir, 0755);
        $image = time().'_'.basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir.$image);
    }


    $SQL = "INSERT INTO posts (user_id, content, image) 
            VALUES ('$user_id', '$content', " . ($image ? "'$image'" : "NULL") . ")";
    $connect->query($SQL);
}

$user_id = $_SESSION['user_id'];
$results = $connect->query("SELECT * FROM posts WHERE user_id='$user_id' ORDER BY created_at DESC");

$searching = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $query = mysqli_real_escape_string($connect, $_POST['search']);
    $pattern = "%$query%"; 


    $SQL = "SELECT username, email 
            FROM users 
            WHERE username LIKE '$pattern' OR email LIKE '$pattern'";
    $searchOutput = $connect->query($SQL);

    while ($row = $searchOutput->fetch_assoc()) {
        $searching[] = $row; 
    }
}
?>




<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin: 0;
            padding-top: 50px;
            background-color: rgba(255, 255, 255, 0.8);;
            background-size: cover;
        }
        .dashboard-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(25px);
            padding: 40px;
            border-radius: 20px;
            width: 400px;
            color: white;
        }
        h1, h2 { text-align: center; }
        form textarea {
            width: 100%;
            height: 75px;
            margin-bottom: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 14px;
        }
        form input[type="file"] { margin-bottom: 10px; }
        form input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        form input[type="submit"]:hover { background-color: #45a049; }
        .post {
            background: rgba(0,0,0,0.2);
            margin-top: 15px;
            padding: 10px;
            border-radius: 10px;
        }
        .post img {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 10px;
        }
        a { color: #4CAF50; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="dashboard-box">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p style="text-align:center;"><a href="logout.php">Logout</a></p>

        <h2>Create a Post</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?" required></textarea>
            <input type="file" name="image" accept="image/*">
            <input type="submit" value="Post">
        </form>
<form method="POST" action="">
    <input type="text" name="search" placeholder="Search users with email or username " required>
    <input type="submit" value="Search">
</form>
        <h2>Your Posts</h2>
        <?php if($results->num_rows > 0): ?>
            <?php while($row = $results->fetch_assoc()): ?>
                <div class="post">
                    <p><?php echo htmlspecialchars($row['content']); ?></p>
                    <?php if($row['image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Post Image">
                    <?php endif; ?>
                    <small><?php echo $row['created_at']; ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Dashboard is Empty </p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
if (!empty($searching)) {
    echo "<h3>Search Results:</h3>";
    foreach ($searching as $user) {
        echo "<p><b>Username:</b> " . htmlspecialchars($user['username']) .
             " | <b>Email:</b> " . htmlspecialchars($user['email']) . "</p><hr>";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    echo "<p>Users not found.</p>";
}

?>
