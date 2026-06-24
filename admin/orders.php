<?php
require_once 'includes/auth.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'helpers.php';

if(isset($_POST['update_status'])){
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $allowed = ['pending','processing','shipped','delivered'];
    if(in_array($status, $allowed)){
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
    }
    
    header('Location: orders.php?msg=updated');
    exit;

    
}

$items = [];
$order = null;

if(isset($_GET['view'])){
    $order_id = intval($_GET['view']);

    $orderResult = $conn->query("SELECT * FROM orders WHERE id=$order_id");
    if($orderResult && $orderResult->num_rows > 0){
        $order = $orderResult->fetch_assoc();

        $itemsResult = $conn->query("
            SELECT oi.*, p.name, p.image 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = $order_id
        ");

        if($itemsResult){
            $items = $itemsResult->fetch_all(MYSQLI_ASSOC);
        }
    }
}

$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$status_labels = ['pending'=>'قيد الانتظار','processing'=>'قيد التجهيز','shipped'=>'تم الشحن','delivered'=>'تم التوصيل'];

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الطلبات | لوحة تحكم مزايا مول</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
    <div class="page-head">
        <div>
            <h1>الطلبات</h1>
            <p>متابعة وتحديث حالة طلبات العملاء</p>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?><div class="alert alert-success">تم تحديث حالة الطلب</div><?php endif; ?>

    <?php if(isset($order)): ?>
    <div class="panel" style="margin-bottom:22px;">
        <div class="panel-head">
            <h3>تفاصيل الطلب #<?php echo $order['id']; ?></h3>
            <a href="orders.php" class="btn btn-ghost btn-sm">رجوع للقائمة</a>
        </div>
        <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:14px;">
            <div><strong>الهاتف:</strong> <?php echo htmlspecialchars($order['phone1']); ?> <?php echo $order['phone2'] ? '/ '.htmlspecialchars($order['phone2']) : ''; ?></div>
            <div><strong>المدينة:</strong> <?php echo htmlspecialchars($order['city']); ?></div>
            <div style="grid-column:1/-1;"><strong>العنوان:</strong> <?php echo htmlspecialchars($order['address']); ?></div>
            <?php if($order['notes']): ?><div style="grid-column:1/-1;"><strong>ملاحظات:</strong> <?php echo htmlspecialchars($order['notes']); ?></div><?php endif; ?>
        </div>
        <table>
            <tr><th>المنتج</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th></tr>
            <?php $order_total = 0; foreach($items as $it): $line = $it['price']*$it['quantity']; $order_total += $line; ?>
            <tr>
               <td style="display:flex;align-items:center;gap:10px;">
    <img 
    src="../uploads/<?php echo htmlspecialchars($it['image']); ?>" 
    onerror="this.src='../assets/images/placeholder.png'" 
    style="width:50px;height:50px;object-fit:cover;">
</td>
                <td><?php echo $it['quantity']; ?></td>
                <td><?php echo number_format($it['price']); ?> ج.م</td>
                <td><?php echo number_format($line); ?> ج.م</td>
            </tr>
            <?php endforeach; ?>
            <tr><td colspan="3" style="text-align:left;font-weight:800;">الإجمالي</td><td style="font-weight:800;"><?php echo number_format($order_total); ?> ج.م</td></tr>
        </table>
    </div>
    <?php endif; ?>

    <div class="panel">
        <table>
            <tr><th>رقم الطلب</th><th>الهاتف</th><th>المدينة</th><th>الحالة</th><th>التاريخ</th><th></th></tr>
            <?php foreach($orders as $o): ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td><?php echo htmlspecialchars($o['phone1']); ?></td>
                <td><?php echo htmlspecialchars($o['city']); ?></td>
                <td>
                    <form method="post" style="display:inline-flex;gap:8px;align-items:center;">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <select name="status" class="status-select" onchange="this.form.submit()">
                            <?php foreach($status_labels as $val=>$label): ?>
                            <option value="<?php echo $val; ?>" <?php echo $o['status']===$val?'selected':''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </td>
                <td><?php echo date('Y-m-d H:i', strtotime($o['created_at'])); ?></td>
                <td><a href="orders.php?view=<?php echo $o['id']; ?>" class="icon-btn"><i class="fa-solid fa-eye"></i></a></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($orders)): ?>
            <tr><td colspan="6" style="text-align:center;color:#787a82;">لا توجد طلبات</td></tr>
            <?php endif; ?>
        </table>
    </div>
</main>
</div>
</body>
</html>
