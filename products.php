<?php
include 'config/db.php';
include 'config/helpers.php';

$categories = $conn->query("SELECT * FROM categories ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);

$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$sort = trim($_GET['sort'] ?? 'newest');

$where = [];
$params = [];
$types = '';

if($q !== ''){
    $where[] = "(p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)";
    $like = '%' . $q . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}

$current_cat = null;
if($category !== ''){
    foreach($categories as $c){ if($c['slug'] === $category) $current_cat = $c; }
    if($current_cat){
        $where[] = "p.category_id = ?";
        $params[] = $current_cat['id'];
        $types .= 'i';
    }
}

$order_by = "p.id DESC";
if($sort === 'price_asc') $order_by = "p.price ASC";
elseif($sort === 'price_desc') $order_by = "p.price DESC";
elseif($sort === 'rating') $order_by = "p.rating DESC";

$sql = "SELECT p.*, c.slug as cat_slug, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id";
if($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY $order_by";

$stmt = $conn->prepare($sql);
if($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$active_page = 'products';
$page_title = $current_cat ? $current_cat['name'] : ($q !== '' ? 'نتائج البحث عن "' . $q . '"' : 'كل المنتجات');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> | مزايا مول</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">الرئيسية</a> <i class="fa-solid fa-chevron-left"></i> <?php echo htmlspecialchars($page_title); ?>
        </div>
    </div>

    <div class="container">
        <div class="catalog-layout">
            <aside class="filters-box">
                <h4>الفئات</h4>
                <div class="filter-group">
                    <label>
                        <input type="radio" name="cat_filter" onclick="location.href='products.php<?php echo $q ? '?q='.urlencode($q) : ''; ?>'" <?php echo $category==='' ? 'checked':''; ?>>
                        كل الفئات
                    </label>
                    <?php foreach($categories as $cat): ?>
                    <label>
                        <input type="radio" name="cat_filter" onclick="location.href='products.php?category=<?php echo $cat['slug']; ?><?php echo $q ? '&q='.urlencode($q) : ''; ?>'" <?php echo $category===$cat['slug'] ? 'checked':''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php if($q !== '' || $category !== ''): ?>
                <a href="products.php" class="filter-clear"><i class="fa-solid fa-rotate-left"></i> إعادة تعيين الفلاتر</a>
                <?php endif; ?>
            </aside>

            <div>
                <div class="catalog-toolbar">
                    <span class="results-count"><b><?php echo count($products); ?></b> منتج متاح</span>
                    <form method="get" onchange="this.submit()">
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                        <select name="sort" class="sort-select">
                            <option value="newest" <?php echo $sort==='newest'?'selected':''; ?>>الأحدث</option>
                            <option value="price_asc" <?php echo $sort==='price_asc'?'selected':''; ?>>الأقل سعراً</option>
                            <option value="price_desc" <?php echo $sort==='price_desc'?'selected':''; ?>>الأعلى سعراً</option>
                            <option value="rating" <?php echo $sort==='rating'?'selected':''; ?>>الأعلى تقييماً</option>
                        </select>
                    </form>
                </div>

                <?php if(empty($products)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <p>لم نجد منتجات مطابقة</p>
                    <span>حاول تغيير كلمة البحث أو الفئة</span>
                    <a href="products.php" class="btn-gold">عرض كل المنتجات</a>
                </div>
                <?php else: ?>
                <div class="catalog-grid">
                    <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <?php if($product['old_price']): ?><div class="ribbon">خصم</div><?php endif; ?>
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="product-image">
                            <img src="<?php echo htmlspecialchars(get_product_image($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='assets/images/placeholder.png'">
                        </a>
                        <div class="product-info">
                            <span class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></span>
                            <a href="product.php?id=<?php echo $product['id']; ?>"><h3><?php echo htmlspecialchars($product['name']); ?></h3></a>
                            <div class="product-rating"><span class="stars"><?php echo star_rating($product['rating']); ?></span> (<?php echo $product['rating']; ?>)</div>
                            <div class="product-footer">
                                <div class="price-block">
                                    <span class="product-price"><?php echo format_price($product['price']); ?></span>
                                    <?php if($product['old_price']): ?><span class="product-price-old"><?php echo format_price($product['old_price']); ?></span><?php endif; ?>
                                </div>
                                <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo addslashes(get_product_image($product['image'])); ?>')">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/cart.js"></script>
</body>
</html>
