<?php
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم الطلب | مزايا مول</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background:var(--ink);">
    <section class="success-section">
        <div class="container">
            <div class="success-box">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>تم تأكيد طلبك بنجاح!</h1>
                <p>رقم الطلب: <strong>#<?php echo $order_id; ?></strong></p>
                <p>سنتواصل معك قريباً لتأكيد التفاصيل</p>
                <a href="index.php" class="btn-gold">العودة للتسوق</a>
            </div>
        </div>
    </section>
</body>
</html>
