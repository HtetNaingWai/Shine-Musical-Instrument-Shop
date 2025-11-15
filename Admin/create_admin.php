<?php
include('../Database/database.php');
$username ="Htet Naing";
$password = "123";
$hpassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $hpassword);
if($stmt->execute()){
    echo" Added successfully ";
}
else{
    echo"Data Insert error";
}
?>