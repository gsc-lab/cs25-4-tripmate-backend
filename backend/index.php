<?php
include 'db.php';

$result = $conn->query("SELECT NOW() AS now_time");
$row = $result->fetch_assoc();

echo "DB 연결 성공! 현재 시간: " . $row['now_time'];
?>