<?php
$sever="localhost:3306";
$username="root";
$password="";
$database="com_project";
//creat conncetion
$conn= new mysqli($sever,$username,$password,$database);


//check connection
if($conn->connect_error){
    echo"Connection Eror" . $conn->connect_error;
}

?>
