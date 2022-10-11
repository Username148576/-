<?php //建立資料庫
$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password);

$sql = "CREATE DATABASE picture_db";
if ($conn->query($sql) === TRUE) {
  echo "Database created successfully";
} else {
  echo "Error creating database: " . $conn->error;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "picture_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sql = "CREATE TABLE `imageDB` (
    id int(11) NOT NULL AUTO_INCREMENT,
    image blob,
    PRIMARY KEY(id)
    ) ";

if ($conn->query($sql) === TRUE) {
  echo "Table MyGuests created successfully";
} else {
  echo "Error creating table: " . $conn->error;
}

$conn->close();
?>