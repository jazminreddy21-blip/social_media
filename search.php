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

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$results = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = $_POST['query'];

    $stmt = $connect->prepare("SELECT username, email FROM users WHERE username LIKE ? OR email LIKE ?");
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    $stmt->close();
}

$connect->close();
?>

<html>
<head>
    <title>Search Users</title>
</head>
<body>
<h2>Search Users</h2>
<form method="POST" action="">
    <input type="text" name="query" placeholder="Enter name or email" required>
    <input type="submit" value="Search">
</form>

<?php
if (!empty($results)) {
    echo "<h3>Results:</h3>";
    foreach ($results as $user) {
        echo "<p>";
        echo "<b>Name:</b> " . htmlspecialchars($user['username']) . "<br>";
        echo "<b>Email:</b> " . htmlspecialchars($user['email']);
        echo "</p><hr>";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<p>No users found.</p>";
}
?>
</body>
</html>
