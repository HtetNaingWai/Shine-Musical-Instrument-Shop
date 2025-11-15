<?php
include('../Database/database.php');

// Get total revenue from all delivered orders
$revenue_result = $conn->query("
    SELECT COALESCE(SUM(total_amount), 0) as total_revenue 
    FROM order_ 
    WHERE order_status = 'delivered'
");
$total_revenue = $revenue_result->fetch_assoc()['total_revenue'];

// Get count of active promotions
$promo_result = $conn->query("SELECT COUNT(*) as active_promos FROM promotion WHERE is_active = 1 AND end_date >= CURDATE()");
$active_promos = $promo_result->fetch_assoc()['active_promos'];

// Get pending reviews count
$reviews_result = $conn->query("SELECT COUNT(*) as pending_reviews FROM review_rating WHERE review_status = 'pending'");
$pending_reviews = $reviews_result->fetch_assoc()['pending_reviews'];

// Get recent orders (last 5)
$recent_orders = $conn->query("
    SELECT o.order_id, o.order_date, o.total_amount, o.order_status,
           u.user_name, u.email
    FROM order_ o 
    JOIN users u ON o.customer_id = u.user_id 
    ORDER BY o.order_date DESC 
    LIMIT 5
");

// Get top selling products
$top_products = $conn->query("
    SELECT p.title, SUM(od.quantity) as total_sold
    FROM order_detail od
    JOIN products p ON od.product_item_id = p.product_id
    JOIN order_ o ON od.order_id = o.order_id
    WHERE o.order_status = 'delivered'
    GROUP BY p.product_id, p.title
    ORDER BY total_sold DESC 
    LIMIT 5
");

// Get total orders count by status
$orders_stats = $conn->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
        SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
        SUM(CASE WHEN order_status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders
    FROM order_
");
$stats = $orders_stats->fetch_assoc();
?>

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo number_format($total_revenue, 2); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $stats['total_orders']; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Promotions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $active_promos; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Reviews</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $pending_reviews; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-1 p-2 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="dashboard.php?page=admin_order.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = $recent_orders->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($order['user_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                    </td>
                                    <td><?php echo $order['order_date']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            switch($order['order_status']) {
                                                case 'delivered': echo 'success'; break;
                                                case 'processing': echo 'primary'; break;
                                                case 'shipped': echo 'info'; break;
                                                case 'pending': echo 'warning'; break;
                                                case 'cancelled': echo 'danger'; break;
                                                default: echo 'secondary';
                                            }
                                        ?>"><?php echo ucfirst($order['order_status']); ?></span>
                                    </td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue and Top Products -->
        <div class="col-xl-4 col-lg-5">
            <!-- Revenue Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Summary</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="text-success">$<?php echo number_format($total_revenue, 2); ?></h4>
                        <p class="mb-3">Total Revenue from Delivered Orders</p>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-right">
                                    <h5 class="text-primary"><?php echo $stats['delivered_orders']; ?></h5>
                                    <small>Delivered</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h5 class="text-warning"><?php echo $stats['pending_orders'] + $stats['processing_orders'] + $stats['shipped_orders']; ?></h5>
                                <small>In Progress</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-right">Qty Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($product = $top_products->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['title']); ?></td>
                                    <td class="text-right"><strong><?php echo $product['total_sold']; ?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Breakdown -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-warning">
                                <div class="card-body">
                                    <h5 class="text-warning"><?php echo $stats['pending_orders']; ?></h5>
                                    <small>Pending Orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <h5 class="text-info"><?php echo $stats['processing_orders']; ?></h5>
                                    <small>Processing</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <h5 class="text-primary"><?php echo $stats['shipped_orders']; ?></h5>
                                    <small>Shipped</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <h5 class="text-success"><?php echo $stats['delivered_orders']; ?></h5>
                                    <small>Delivered</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
}
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.text-xs { font-size: 0.7rem; }
.badge-success { background-color: #1cc88a; }
.badge-primary { background-color: #4e73df; }
.badge-info { background-color: #36b9cc; }
.badge-warning { background-color: #f6c23e; }
.badge-danger { background-color: #e74a3b; }
</style>