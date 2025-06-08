<?php
require_once 'config/db_connect.php';
require_once 'includes/auth_check.php';

$page_title = "Reports & Analytics";
require_once 'includes/header.php';

// Get date range from query parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Sales statistics
$sales_stats = $pdo->prepare("
    SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_revenue,
        AVG(total_amount) as avg_order_value,
        COUNT(DISTINCT user_id) as unique_customers
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ?
");
$sales_stats->execute([$start_date, $end_date]);
$stats = $sales_stats->fetch();

// Daily sales data for chart
$daily_sales = $pdo->prepare("
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as orders,
        SUM(total_amount) as revenue
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY date
");
$daily_sales->execute([$start_date, $end_date]);
$daily_data = $daily_sales->fetchAll();

// Top products
$top_products = $pdo->prepare("
    SELECT 
        p.name,
        SUM(oi.quantity) as total_sold,
        SUM(oi.item_total) as revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE DATE(o.created_at) BETWEEN ? AND ?
    GROUP BY p.id, p.name
    ORDER BY total_sold DESC
    LIMIT 10
");
$top_products->execute([$start_date, $end_date]);
$products = $top_products->fetchAll();

// Order status distribution
$status_distribution = $pdo->prepare("
    SELECT 
        status,
        COUNT(*) as count
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY status
");
$status_distribution->execute([$start_date, $end_date]);
$status_data = $status_distribution->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <form method="GET" class="d-flex">
                        <input type="date" class="form-control me-2" name="start_date" value="<?php echo $start_date; ?>">
                        <input type="date" class="form-control me-2" name="end_date" value="<?php echo $end_date; ?>">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary">₹<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h3>
                            <p class="card-text">Total Revenue</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success"><?php echo number_format($stats['total_orders'] ?? 0); ?></h3>
                            <p class="card-text">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-info">₹<?php echo number_format($stats['avg_order_value'] ?? 0, 2); ?></h3>
                            <p class="card-text">Avg Order Value</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-warning"><?php echo number_format($stats['unique_customers'] ?? 0); ?></h3>
                            <p class="card-text">Unique Customers</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Sales Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Daily Sales Trend</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Order Status -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Status</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Top Selling Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Units Sold</th>
                                            <th>Revenue</th>
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $index => $product): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary me-2">#<?php echo $index + 1; ?></span>
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </td>
                                            <td><?php echo number_format($product['total_sold']); ?></td>
                                            <td>₹<?php echo number_format($product['revenue'], 2); ?></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" style="width: <?php echo min(100, ($product['total_sold'] / max(1, $products[0]['total_sold'] ?? 1)) * 100); ?>%">
                                                        <?php echo number_format(($product['total_sold'] / max(1, $products[0]['total_sold'] ?? 1)) * 100, 1); ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: [<?php echo "'" . implode("','", array_column($daily_data, 'date')) . "'"; ?>],
        datasets: [{
            label: 'Revenue',
            data: [<?php echo implode(',', array_column($daily_data, 'revenue')); ?>],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }, {
            label: 'Orders',
            data: [<?php echo implode(',', array_column($daily_data, 'orders')); ?>],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.1,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: [<?php echo "'" . implode("','", array_column($status_data, 'status')) . "'"; ?>],
        datasets: [{
            data: [<?php echo implode(',', array_column($status_data, 'count')); ?>],
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

<?php include 'includes/footer.php'; ?>
