<?php
include 'config/db.php';
include 'config/helpers.php';
$categories = $conn->query("SELECT * FROM categories ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
$active_page = 'cart';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سلة التسوق | مزايا مول</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="cart-section">
        <div class="container">
            <h2 class="section-title" style="margin-bottom:24px;">سلة التسوق</h2>
            <div id="cartContainer">
                <div class="cart-empty" id="cartEmpty">
                    <i class="fas fa-shopping-basket"></i>
                    <p>السلة فارغة</p>
                    <a href="products.php" class="btn-gold">تسوق الآن</a>
                </div>
                <div class="cart-items" id="cartItems"></div>
                <div class="cart-summary" id="cartSummary" style="display:none;">
                    <div class="summary-row">
                        <span>الإجمالي:</span>
                        <span class="total-price" id="totalPrice">0 ج.م</span>
                    </div>
                    <a href="checkout.php" class="btn-checkout">إتمام الشراء</a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/cart.js"></script>
    <script>renderCart();</script>
</body>
</html>
