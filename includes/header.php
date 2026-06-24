<?php
// $active_page و $categories لازم يكونوا متعرفين قبل ما نعمل include للملف ده
$categories = $categories ?? [];
?>
<div class="topbar">
    <div class="container">
        <span><i class="fa-solid fa-truck-fast"></i> توصيل لجميع محافظات مصر</span>
        <div class="topbar-links">
            <a href="tel:01000000000"><i class="fa-solid fa-phone"></i> 01000000000</a>
        </div>
    </div>
</div>

<header class="header">
    <div class="container">
        <a href="index.php" class="logo">
            <i class="fas fa-crown"></i>
            <span>مزايا مول<small>MAZAYA MALL</small></span>
        </a>

        <form class="search-card" action="products.php" method="get">
            <select name="category">
                <option value="">كل الفئات</option>
                <?php foreach($categories as $cat): ?>
                <option value="<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="q" placeholder="دور على أي منتج..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <div class="header-actions">
            <a href="cart.php">
                <i class="fa-solid fa-cart-shopping"></i>
                السلة
                <span class="cart-count" id="cartCount">0</span>
            </a>
        </div>
    </div>
</header>

<nav class="catnav">
    <div class="container">
        <a href="index.php" class="<?php echo ($active_page ?? '') === 'home' ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> الرئيسية</a>
        <a href="products.php" class="<?php echo (($active_page ?? '') === 'products' && empty($_GET['category'])) ? 'active' : ''; ?>"><i class="fa-solid fa-grip"></i> كل المنتجات</a>
        <?php foreach($categories as $cat): ?>
        <a href="products.php?category=<?php echo $cat['slug']; ?>" class="<?php echo (($_GET['category'] ?? '') === $cat['slug']) ? 'active' : ''; ?>">
            <i class="<?php echo category_icon($cat['slug']); ?>"></i> <?php echo htmlspecialchars($cat['name']); ?>
        </a>
        <?php endforeach; ?>
    </div>
</nav>
