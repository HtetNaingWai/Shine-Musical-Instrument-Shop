<?php
include_once("../Database/database.php");
include_once("utils.php");
require_login();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pid = (int)($_POST['product_id'] ?? 0);
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $text = trim($_POST['review_text'] ?? '');
    $uid = (int)$_SESSION['user']['user_id'];

    $stmt = $conn->prepare("INSERT INTO review_rating (product_id, user_id, review_text, rating, review_status) VALUES (?,?,?,?, 'pending')");
    $stmt->bind_param("iisi", $pid, $uid, $text, $rating);
    $stmt->execute();
}
header("Location: product_detail.php?id=" . (int)($_POST['product_id'] ?? 0));
