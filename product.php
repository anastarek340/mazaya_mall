<?php
include 'config/db.php';
include 'config/helpers.php';

$categories = $conn->query("SELECT * FROM categories ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);

if(!isset($_GET['id'])){ header('Location: products.php'); exit; }
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT p.*, c.slug as cat_slug, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if(!$product){ header('Location: products.php'); exit; }

$rel_stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY id DESC LIMIT 4");
$rel_stmt->bind_param("ii", $product['category_id'], $product['id']);
$rel_stmt->execute();
$related = $rel_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$active_page = 'products';
$discount_pct = $product['old_price'] ? round((($product['old_price'] - $product['price']) / $product['old_price']) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | مزايا مول</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">الرئيسية</a> <i class="fa-solid fa-chevron-left"></i>
            <a href="products.php?category=<?php echo $product['cat_slug']; ?>"><?php echo htmlspecialchars($product['cat_name']); ?></a>
            <i class="fa-solid fa-chevron-left"></i> <?php echo htmlspecialchars($product['name']); ?>
        </div>
    </div>

    <section class="product-detail">
        <div class="container">
            <div class="product-detail-grid">
                <div class="product-detail-image">
                    <?php if($discount_pct > 0): ?><div class="ribbon danger">خصم <?php echo $discount_pct; ?>%</div><?php endif; ?>
                    <img src="<?php echo htmlspecialchars(get_product_image($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='assets/images/placeholder.png'">
                </div>
                <div class="product-detail-info">
                    <span class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></span>
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-rating"><span class="stars"><?php echo star_rating($product['rating']); ?></span> (<?php echo $product['rating']; ?> تقييم العملاء)</div>

                    <div class="price-row" style="margin-top:16px;">
                        <span class="product-price-large"><?php echo format_price($product['price']); ?></span>
                        <?php if($product['old_price']): ?>
                        <span class="product-price-old"><?php echo format_price($product['old_price']); ?></span>
                        <span class="save-tag">وفّر <?php echo $discount_pct; ?>%</span>
                        <?php endif; ?>
                    </div>

                    <div class="stock-badge"><i class="fa-solid fa-circle"></i> متوفر في المخزون (<?php echo $product['stock']; ?> قطعة)</div>

                    <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

                    <div class="qty-row">
                        <span style="font-weight:700;font-size:14px;">الكمية:</span>
                        <div class="qty-control">
                            <button onclick="document.getElementById('detailQty').textContent = Math.max(1, parseInt(document.getElementById('detailQty').textContent)-1)">-</button>
                            <span id="detailQty">1</span>
                            <button onclick="document.getElementById('detailQty').textContent = parseInt(document.getElementById('detailQty').textContent)+1">+</button>
                        </div>
                    </div>

                    <div class="detail-actions">
                        <button class="btn-add-cart btn-large" onclick="addProductMultiple()">
                            <i class="fa-solid fa-cart-plus"></i> أضف للسلة
                        </button>
                        <a href="cart.php" class="btn-outline-gold" style="color:#8b6914;border-color:#c9a227;">عرض السلة</a>
                    </div>

                    <div class="perks-box">
                        <div><i class="fa-solid fa-truck"></i> توصيل لجميع المحافظات خلال 2-5 أيام</div>
                        <div><i class="fa-solid fa-rotate-left"></i> إمكانية الاستبدال خلال 14 يوم</div>
                        <div><i class="fa-solid fa-shield-halved"></i> ضمان الجودة من مزايا مول</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if(!empty($related)): ?>
    <section class="products-section">
        <div class="container">
            <div class="section-title-row">
                <h2 class="section-title">منتجات مشابهة</h2>
            </div>
            <div class="products-grid">
                <?php foreach($related as $r): ?>
                <div class="product-card">
                    <?php if($r['old_price']): ?><div class="ribbon">خصم</div><?php endif; ?>
                    <a href="product.php?id=<?php echo $r['id']; ?>" class="product-image">
                        <img src="<?php echo htmlspecialchars(get_product_image($r['image'])); ?>" alt="<?php echo htmlspecialchars($r['name']); ?>" onerror="this.src='assets/images/placeholder.png'">
                    </a>
                    <div class="product-info">
                        <span class="product-brand"><?php echo htmlspecialchars($r['brand']); ?></span>
                        <a href="product.php?id=<?php echo $r['id']; ?>"><h3><?php echo htmlspecialchars($r['name']); ?></h3></a>
                        <div class="product-rating"><span class="stars"><?php echo star_rating($r['rating']); ?></span> (<?php echo $r['rating']; ?>)</div>
                        <div class="product-footer">
                            <div class="price-block">
                                <span class="product-price"><?php echo format_price($r['price']); ?></span>
                                <?php if($r['old_price']): ?><span class="product-price-old"><?php echo format_price($r['old_price']); ?></span><?php endif; ?>
                            </div>
                            <button class="btn-add-cart" onclick="addToCart(<?php echo $r['id']; ?>, '<?php echo addslashes($r['name']); ?>', <?php echo $r['price']; ?>, '<?php echo addslashes($r['image']); ?>')">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/cart.js"></script>
    <script>
        function addProductMultiple(){
            const qty = parseInt(document.getElementById('detailQty').textContent);
            for(let i=0;i<qty;i++){
                addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo addslashes($product['image']); ?>');
            }
        }
    </script>
</body>
</html>
