<?php
include('../Database/database.php');

if($_SERVER["REQUEST_METHOD"]=="POST") {
    $username=trim($_POST['username']);
    $password=trim($_POST['password']);

    $sql="SELECT * FROM admins WHERE username=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("s",$username);
    if($stmt->execute()){
        $stmt->store_result();
        if($stmt->num_rows()>0){
            $stmt->bind_result($id,$username,$hpasword);
            $stmt->fetch();
            if(password_verify($password,$hpasword)){
                 session_start();
                 $_SESSION["username"]="admin";
                header("location:admin_login.php");
    
             }
                header("location:dashboard.php");
                exit;
            }
            else{
               header("location:admin_login.php?error=ture");
               exit;
            }
        }
        else{
            header("location:admin_login.php?error=true");
            exit;
        }
    }
    $stmt->close();
?>