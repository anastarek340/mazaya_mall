<?php
session_start();
include 'config/db.php';
include 'config/helpers.php';


$categories = $conn->query("SELECT * FROM categories ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);

$featured = $conn->query("
SELECT p.*, c.slug as cat_slug 
FROM products p 
JOIN categories c ON p.category_id = c.id 
ORDER BY p.id DESC 
LIMIT 8
")->fetch_all(MYSQLI_ASSOC);

$active_page = 'home';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مزايا مول | تسوق أدوات منزلية، بلاستيكات، ومفروشات</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- قسم البطل -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-eyebrow"><i class="fa-solid fa-crown"></i> عضوية مزايا</div>
                <h1>كل ما يخص بيتك<br><span>بمزايا مش هتلاقيها فين تاني</span></h1>
                <p>أدوات منزلية، بلاستيكات، ومفروشات مختارة بعناية بأسعار تنافسية وتوصيل لباب البيت.</p>
                <a href="products.php" class="btn-gold"><i class="fa-solid fa-bag-shopping"></i> تسوق الآن</a>
            </div>
            <div class="hero-seal">
                <i class="fa-solid fa-award"></i>
                <span>جودة مضمونة</span>
            </div>
        </div>
    </section>

    <!-- أقسام الفئات -->
    <section class="cat-strip">
        <div class="container">
            <div class="cat-strip-grid">
                <?php foreach($categories as $cat): ?>
                <a href="products.php?category=<?php echo $cat['slug']; ?>" class="cat-card">
                    <img src="<?php echo htmlspecialchars($cat['cover']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                    <div class="cat-card-body">
                        <div class="cat-icon"><i class="<?php echo category_icon($cat['slug']); ?>"></i></div>
                        <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <span>تسوق الآن <i class="fa-solid fa-arrow-left-long"></i></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- قسم المنتجات الجديدة -->
    <section class="products-section">
        <div class="container">
            <div class="section-title-row">
                <h2 class="section-title">🔥 وصل حديثاً</h2>
                <a href="products.php" class="section-link">عرض الكل <i class="fa-solid fa-arrow-left-long"></i></a>
            </div>
            <div class="products-grid">
                <?php foreach($featured as $product): ?>
                <div class="product-card">
                    <?php if($product['old_price']): ?><div class="ribbon">خصم</div><?php endif; ?>
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="product-image">
                        <img src="<?php echo htmlspecialchars(get_product_image($product['image'])); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             onerror="this.src='assets/images/placeholder.png'">
                    </a>
                    <div class="product-info">
                        <span class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></span>
                        <a href="product.php?id=<?php echo $product['id']; ?>"><h3><?php echo htmlspecialchars($product['name']); ?></h3></a>
                        <div class="product-rating">
                            <span class="stars"><?php echo star_rating($product['rating']); ?></span> 
                            (<?php echo $product['rating']; ?>)
                        </div>
                        <div class="product-footer">
                            <div class="price-block">
                                <span class="product-price"><?php echo format_price($product['price']); ?></span>
                                <?php if($product['old_price']): ?><span class="product-price-old"><?php echo format_price($product['old_price']); ?></span><?php endif; ?>
                            </div>
                            <button class="btn-add-cart" 
                                    onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo addslashes(get_product_image($product['image'])); ?>')">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/cart.js"></script>
</body>
</html>
