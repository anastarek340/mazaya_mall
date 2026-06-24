<?php
require_once 'includes/auth.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'helpers.php';



$total_products = $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'];
$total_orders = $conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
$pending_orders = $conn->query("SELECT COUNT(*) c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
$revenue = $conn->query("SELECT SUM(oi.price*oi.quantity) r FROM order_items oi")->fetch_assoc()['r'] ?? 0;

$recent_orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);

$status_labels = ['pending'=>'قيد الانتظار','processing'=>'قيد التجهيز','shipped'=>'تم الشحن','delivered'=>'تم التوصيل'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الرئيسية | لوحة تحكم مزايا مول</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
    <div class="page-head">
        <div>
            <h1>نظرة عامة</h1>
            <p>أهلاً بك مرة أخرى،يا ادمان 👋</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon"><i class="fa-solid fa-box"></i></div>
            <div class="value"><?php echo $total_products; ?></div>
            <div class="label">إجمالي المنتجات</div>
        </div>
        <div class="stat-card">
            <div class="icon"><i class="fa-solid fa-receipt"></i></div>
            <div class="value"><?php echo $total_orders; ?></div>
            <div class="label">إجمالي الطلبات</div>
        </div>
        <div class="stat-card">
            <div class="icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="value"><?php echo $pending_orders; ?></div>
            <div class="label">طلبات قيد الانتظار</div>
        </div>
        <div class="stat-card">
            <div class="icon"><i class="fa-solid fa-sack-dollar"></i></div>
            <div class="value"><?php echo number_format($revenue); ?></div>
            <div class="label">إجمالي المبيعات (ج.م)</div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h3>آخر الطلبات</h3>
            <a href="orders.php" class="btn btn-ghost btn-sm">عرض الكل</a>
        </div>
        <table>
            <tr><th>رقم الطلب</th><th>الهاتف</th><th>المدينة</th><th>الحالة</th><th>التاريخ</th></tr>
            <?php foreach($recent_orders as $o): ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td><?php echo htmlspecialchars($o['phone1']); ?></td>
                <td><?php echo htmlspecialchars($o['city']); ?></td>
                <td><span class="badge <?php echo $o['status']; ?>"><?php echo $status_labels[$o['status']]; ?></span></td>
                <td><?php echo date('Y-m-d', strtotime($o['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($recent_orders)): ?>
            <tr><td colspan="5" style="text-align:center;color:#787a82;">لا توجد طلبات حتى الآن</td></tr>
            <?php endif; ?>
        </table>
    </div>
</main>
</div>
</body>
</html>
