<?php
$server = "localhost";
$username = "root";
$password = "";
$dbName = "mySocial";

// Connect to MySQL server
$connect = new mysqli($server, $username, $password);
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Create database if it doesn't exist
if ($connect->query("CREATE DATABASE IF NOT EXISTS $dbName") === TRUE) {
    echo "Database '$dbName' created successfully!<br>";
} else {
    die("Error creating database: " . $connect->error);
}

// Select the database
$connect->select_db($dbName);

// Create users table if not exists
$users = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($connect->query($users)) {
    echo "Users table was created successfully!<br>";
} else {
    die("Error creating the users table: " . $connect->error);
}


// Create posts table if not exists
$posts = "
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    content TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
if ($connect->query($posts)) {
    echo "Posts table was created successfully!<br>";
} else {
    die("Error creating the posts table: " . $connect->error);
}

$messages = "
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_email VARCHAR(100),
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id)
)";
if ($connect->query($messages)) {
    echo "Messages table created successfully!<br>";
} else {
    die("Error creating messages table: " . $connect->error);
}

$connect->close();
?>
