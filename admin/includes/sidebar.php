<?php $current = basename($_SERVER['PHP_SELF']); ?>
<div class="admin-shell">
<aside class="sidebar">
    <div class="brand"><i class="fas fa-crown"></i> <span>مزايا مول</span></div>
    <nav>
        <a href="dashboard.php" class="<?php echo $current==='dashboard.php'?'active':''; ?>"><i class="fa-solid fa-gauge"></i> <span>الرئيسية</span></a>
        <a href="products.php" class="<?php echo $current==='products.php'?'active':''; ?>"><i class="fa-solid fa-box"></i> <span>المنتجات</span></a>
        <a href="orders.php" class="<?php echo $current==='orders.php'?'active':''; ?>"><i class="fa-solid fa-receipt"></i> <span>الطلبات</span></a>
        <a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> <span>عرض المتجر</span></a>
    </nav>
    <div class="logout">
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span>تسجيل الخروج</span></a>
    </div>
</aside>
<main class="main-area">
