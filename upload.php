<?php
$db_host = "localhost";
$db_user = "root";
$db_passwd = "";
$db_name = "picture_db";
$conn = mysqli_connect($db_host,$db_user,$db_passwd,$db_name);

// 接收前端傳來的 DataURL 字串
$imagestring = trim($_REQUEST["imagestring"]);

//echo $imagestring;

// 解析 DataURL
$token = explode(',', $imagestring);
// 取出圖片的資料並將 base64 還原成二進位格式
$image = base64_decode($token[1]);

// 以下為 PHP 將 Blob 放入Mysql的方法
$null = NULL;
$sql = 'insert into imagedb(image) values(?)';
$stmt = mysqli_prepare($conn, $sql);
$stmt->bind_param('b', $null);
$stmt->send_long_data(0, $image);
$success = $stmt->execute();
$stmt->close();
header('location:index.php');
exit;
?>