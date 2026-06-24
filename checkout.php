<?php
include 'config/db.php';
include 'config/helpers.php';
$categories = $conn->query("SELECT * FROM categories ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
$active_page = 'checkout';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إتمام الطلب | مزايا مول</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="checkout-section">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">الرئيسية</a> <i class="fa-solid fa-chevron-left"></i>
                <a href="cart.php">السلة</a> <i class="fa-solid fa-chevron-left"></i> إتمام الطلب
            </div>
            <div class="checkout-grid">
                <div class="checkout-form">
                    <h2>معلومات التوصيل</h2>
                    <form id="checkoutForm">
                        <div class="form-group">
                            <label>رقم الهاتف الأول *</label>
                            <input type="tel" name="phone1" id="phone1" required placeholder="01xxxxxxxxx">
                        </div>
                        <div class="form-group">
                            <label>رقم الهاتف الثاني (اختياري)</label>
                            <input type="tel" name="phone2" id="phone2" placeholder="01xxxxxxxxx">
                        </div>
                        <div class="form-group">
                            <label>العنوان بالكامل *</label>
                            <textarea name="address" id="address" rows="3" required placeholder="الشارع، المبنى، الشقة..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>المدينة *</label>
                            <input type="text" name="city" id="city" required placeholder="مثال: القاهرة">
                        </div>
                        <div class="form-group">
                            <label>ملاحظات (اختياري)</label>
                            <textarea name="notes" id="notes" rows="2" placeholder="أي تعليمات خاصة بالتوصيل..."></textarea>
                        </div>
                        <button type="submit" class="btn-checkout">تأكيد الطلب</button>
                    </form>
                </div>
                <div class="checkout-summary">
                    <h3>ملخص الطلب</h3>
                    <div id="checkoutItems"></div>
                    <div class="summary-total">
                        <span>الإجمالي:</span>
                        <span id="checkoutTotal">0 ج.م</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/cart.js"></script>
    <script>
        renderCheckoutSummary();

        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const cart = getCart();
            if(cart.length === 0) {
                alert('السلة فارغة!');
                return;
            }

            const formData = {
                phone1: document.getElementById('phone1').value.trim(),
                phone2: document.getElementById('phone2').value.trim(),
                address: document.getElementById('address').value.trim(),
                city: document.getElementById('city').value.trim(),
                notes: document.getElementById('notes').value.trim(),
                items: cart
            };

            const submitBtn = e.target.querySelector('button[type=submit]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'جاري التأكيد...';

            try {
                const response = await fetch('api/orders.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if(result.success) {
                    localStorage.removeItem('mazaya_cart');
                    window.location.href = 'success.php?order_id=' + result.order_id;
                } else {
                    alert(result.message || 'حدث خطأ، حاول مرة أخرى');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'تأكيد الطلب';
                }
            } catch(err) {
                alert('خطأ في الاتصال بالخادم');
                submitBtn.disabled = false;
                submitBtn.textContent = 'تأكيد الطلب';
            }
        });
    </script>
</body>
</html>
