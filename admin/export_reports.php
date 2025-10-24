<?php
session_start();
require_once '../db.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../index.php');
}

$format = $_GET['format'] ?? 'excel';

// Fetch all report data
// Daily sales
$daily_query = "SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue 
                FROM orders WHERE status != 'cancelled' 
                GROUP BY DATE(created_at) 
                ORDER BY date DESC LIMIT 30";
$daily_result = mysqli_query($conn, $daily_query);

// Monthly sales
$monthly_query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as orders, SUM(total_amount) as revenue 
                  FROM orders WHERE status != 'cancelled' 
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                  ORDER BY month DESC LIMIT 12";
$monthly_result = mysqli_query($conn, $monthly_query);

// Top selling items
$top_items_query = "SELECT f.name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as revenue 
                    FROM order_items oi 
                    JOIN food_items f ON oi.food_item_id = f.id 
                    GROUP BY f.id 
                    ORDER BY total_sold DESC LIMIT 20";
$top_items_result = mysqli_query($conn, $top_items_query);

// Overall statistics
$stats_query = "SELECT 
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_order_value
                FROM orders WHERE status != 'cancelled'";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

if ($format === 'excel') {
    // Export as Excel (CSV format)
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=sales_report_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Overall Statistics
    fputcsv($output, ['SALES REPORT - GOURMET SENTINEL']);
    fputcsv($output, ['Generated on: ' . date('F j, Y g:i A')]);
    fputcsv($output, []);
    
    fputcsv($output, ['OVERALL STATISTICS']);
    fputcsv($output, ['Metric', 'Value']);
    fputcsv($output, ['Total Orders', $stats['total_orders']]);
    fputcsv($output, ['Total Revenue', '₱' . number_format($stats['total_revenue'], 2)]);
    fputcsv($output, ['Average Order Value', '₱' . number_format($stats['avg_order_value'], 2)]);
    fputcsv($output, []);
    
    // Daily Sales
    fputcsv($output, ['DAILY SALES (Last 30 Days)']);
    fputcsv($output, ['Date', 'Orders', 'Revenue']);
    mysqli_data_seek($daily_result, 0);
    while ($row = mysqli_fetch_assoc($daily_result)) {
        fputcsv($output, [
            date('M j, Y', strtotime($row['date'])),
            $row['orders'],
            '₱' . number_format($row['revenue'], 2)
        ]);
    }
    fputcsv($output, []);
    
    // Monthly Sales
    fputcsv($output, ['MONTHLY SALES']);
    fputcsv($output, ['Month', 'Orders', 'Revenue']);
    mysqli_data_seek($monthly_result, 0);
    while ($row = mysqli_fetch_assoc($monthly_result)) {
        fputcsv($output, [
            date('F Y', strtotime($row['month'].'-01')),
            $row['orders'],
            '₱' . number_format($row['revenue'], 2)
        ]);
    }
    fputcsv($output, []);
    
    // Top Selling Items
    fputcsv($output, ['TOP SELLING ITEMS']);
    fputcsv($output, ['Item Name', 'Quantity Sold', 'Revenue']);
    mysqli_data_seek($top_items_result, 0);
    while ($row = mysqli_fetch_assoc($top_items_result)) {
        fputcsv($output, [
            $row['name'],
            $row['total_sold'],
            '₱' . number_format($row['revenue'], 2)
        ]);
    }
    
    fclose($output);
    exit;
    
} elseif ($format === 'pdf') {
    // Export as PDF using HTML/CSS
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Sales Report</title>
        <style>
            @media print {
                @page { margin: 20mm; }
            }
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                color: #333;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 3px solid #f59e0b;
                padding-bottom: 15px;
            }
            .header h1 {
                color: #f59e0b;
                margin: 0;
                font-size: 28px;
            }
            .header p {
                margin: 5px 0;
                color: #666;
            }
            .stats-box {
                background: #f9fafb;
                border: 2px solid #f59e0b;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 30px;
            }
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }
            .stat-item {
                text-align: center;
            }
            .stat-label {
                font-size: 12px;
                color: #666;
                text-transform: uppercase;
            }
            .stat-value {
                font-size: 24px;
                font-weight: bold;
                color: #f59e0b;
                margin-top: 5px;
            }
            .section {
                margin-bottom: 30px;
                page-break-inside: avoid;
            }
            .section-title {
                background: #f59e0b;
                color: white;
                padding: 10px 15px;
                margin-bottom: 15px;
                border-radius: 5px;
                font-size: 16px;
                font-weight: bold;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th {
                background: #f3f4f6;
                padding: 10px;
                text-align: left;
                font-size: 12px;
                text-transform: uppercase;
                border-bottom: 2px solid #f59e0b;
            }
            td {
                padding: 8px 10px;
                border-bottom: 1px solid #e5e7eb;
            }
            tr:hover {
                background: #f9fafb;
            }
            .footer {
                margin-top: 40px;
                text-align: center;
                font-size: 11px;
                color: #999;
                border-top: 1px solid #ddd;
                padding-top: 15px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>🍽️ GOURMET SENTINEL</h1>
            <p style="font-size: 18px; font-weight: bold;">Sales Report</p>
            <p>Generated on <?php echo date('F j, Y g:i A'); ?></p>
        </div>

        <!-- Overall Statistics -->
        <div class="stats-box">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">₱<?php echo number_format($stats['total_revenue'], 2); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Avg Order Value</div>
                    <div class="stat-value">₱<?php echo number_format($stats['avg_order_value'], 2); ?></div>
                </div>
            </div>
        </div>

        <!-- Daily Sales -->
        <div class="section">
            <div class="section-title">Daily Sales (Last 30 Days)</div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Orders</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($daily_result, 0);
                    while ($row = mysqli_fetch_assoc($daily_result)): 
                    ?>
                    <tr>
                        <td><?php echo date('M j, Y', strtotime($row['date'])); ?></td>
                        <td><?php echo $row['orders']; ?></td>
                        <td><strong>₱<?php echo number_format($row['revenue'], 2); ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Monthly Sales -->
        <div class="section">
            <div class="section-title">Monthly Sales</div>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Orders</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($monthly_result, 0);
                    while ($row = mysqli_fetch_assoc($monthly_result)): 
                    ?>
                    <tr>
                        <td><?php echo date('F Y', strtotime($row['month'].'-01')); ?></td>
                        <td><?php echo $row['orders']; ?></td>
                        <td><strong>₱<?php echo number_format($row['revenue'], 2); ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Top Selling Items -->
        <div class="section">
            <div class="section-title">Top Selling Items</div>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($top_items_result, 0);
                    while ($row = mysqli_fetch_assoc($top_items_result)): 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['total_sold']; ?> units</td>
                        <td><strong>₱<?php echo number_format($row['revenue'], 2); ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>© <?php echo date('Y'); ?> Gourmet Sentinel - Food Ordering System</p>
            <p>This is a computer-generated report. No signature is required.</p>
        </div>

        <script>
            // Auto-print when PDF format is selected
            window.onload = function() {
                window.print();
            }
        </script>
    </body>
    </html>
    <?php
    exit;
}
?>
