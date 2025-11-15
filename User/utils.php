<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php?next=" . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function cart_init() {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = []; // product_id => qty
}

function cart_count() {
    cart_init();
    return array_sum($_SESSION['cart']);
}

function add_to_cart($pid, $qty = 1) {
    cart_init();
    $pid = (int)$pid; $qty = max(1, (int)$qty);
    if (!isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] = 0;
    $_SESSION['cart'][$pid] += $qty;
}

function remove_from_cart($pid) {
    cart_init();
    unset($_SESSION['cart'][(int)$pid]);
}

function update_cart_qty($pid, $qty) {
    cart_init();
    $pid = (int)$pid; $qty = (int)$qty;
    if ($qty <= 0) unset($_SESSION['cart'][$pid]);
    else $_SESSION['cart'][$pid] = $qty;
}

function money($n) {
    return number_format((float)$n, 2);
}

function active_promotion($code, $conn) {
    $stmt = $conn->prepare("SELECT promotion_id, promotion_code, promotion_amount, start_date, end_date, is_active 
                            FROM promotion 
                            WHERE promotion_code = ? AND is_active = 1 
                              AND start_date <= CURDATE() AND end_date >= CURDATE() LIMIT 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function wishlist_count() {
    if (!isset($_SESSION['user'])) {
        return 0;
    }
    
    global $conn;
    $user_id = $_SESSION['user']['user_id'];
    
    // First check if wishlist table exists, if not return 0
    $check_table = $conn->query("SHOW TABLES LIKE 'wishlist'");
    if ($check_table->num_rows == 0) {
        return 0;
    }
    
    $result = $conn->query("SELECT COUNT(*) as count FROM wishlist WHERE user_id = $user_id");
    $data = $result->fetch_assoc();
    return $data['count'];
}
?>
