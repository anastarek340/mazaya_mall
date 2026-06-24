<?php
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إعداد قاعدة البيانات | مزايا مول</title>
<style>
body{font-family:Tajawal,Arial,sans-serif;background:#15171c;color:#fff;padding:40px;max-width:680px;margin:0 auto;line-height:2;}
h2{color:#c9a227;}
a{display:inline-block;background:#c9a227;color:#15171c;padding:12px 30px;border-radius:10px;text-decoration:none;font-weight:700;margin-top:20px;}
</style>
</head>
<body>
<h2>🛠️ إعداد قاعدة البيانات - مزايا مول</h2>
<?php

$conn->query("
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    cover VARCHAR(500) NOT NULL,
    description VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "✅ جدول الفئات تم إنشاؤه<br>";

$conn->query("
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) DEFAULT NULL,
    brand VARCHAR(100) NOT NULL,
    image VARCHAR(500) NOT NULL,
    description TEXT,
    rating DECIMAL(2,1) DEFAULT 4.5,
    stock INT DEFAULT 50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "✅ جدول المنتجات تم إنشاؤه<br>";

$conn->query("
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone1 VARCHAR(20) NOT NULL,
    phone2 VARCHAR(20),
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    notes TEXT,
    status ENUM('pending','processing','shipped','delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "✅ جدول الطلبات تم إنشاؤه<br>";

$conn->query("
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "✅ جدول عناصر الطلب تم إنشاؤه<br>";

$conn->query("
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "✅ جدول الأدمن تم إنشاؤه<br>";

// ---- Admin seed ----
$check = $conn->query("SELECT id FROM admins WHERE username = 'admin'");
if($check->num_rows == 0){
    $hashed = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $username = 'admin';
    $stmt->bind_param("ss", $username, $hashed);
    $stmt->execute();
    echo "✅ تم إضافة الأدمن (اسم المستخدم: admin | كلمة المرور: admin123)<br>";
} else {
    echo "ℹ️ الأدمن موجود بالفعل<br>";
}

// ---- Categories seed ----
$categories = [
    ['أدوات منزلية', 'household', 'https://loremflickr.com/800/500/kitchenware,cleaning', 'كل ما يحتاجه بيتك من أدوات تنظيف ومطبخ'],
    ['بلاستيكات', 'plastics', 'https://loremflickr.com/800/500/plastic,storage', 'حلول تخزين وأثاث بلاستيك عملية وعالية الجودة'],
    ['مفروشات', 'furnishings', 'https://loremflickr.com/800/500/bedding,textile', 'مفارش وستائر ووسائد تضيف لمسة من الأناقة لمنزلك'],
];
$check_cat = $conn->query("SELECT id FROM categories LIMIT 1");
if($check_cat->num_rows == 0){
    $stmt = $conn->prepare("INSERT INTO categories (name, slug, cover, description) VALUES (?, ?, ?, ?)");
    foreach($categories as $c){
        $stmt->bind_param("ssss", $c[0], $c[1], $c[2], $c[3]);
        $stmt->execute();
    }
    echo "✅ تم إضافة 3 فئات<br>";
} else {
    echo "ℹ️ الفئات موجودة بالفعل<br>";
}

// fetch category ids by slug
$cat_ids = [];
$res = $conn->query("SELECT id, slug FROM categories");
while($row = $res->fetch_assoc()) $cat_ids[$row['slug']] = $row['id'];

// ---- Products seed ----
$check_prod = $conn->query("SELECT id FROM products LIMIT 1");
if($check_prod->num_rows == 0){

    $products = [
        // أدوات منزلية
        ['household','مكنسة كهربائية لاسلكية 2200 وات', 1850, 2300,'هوم برو','https://loremflickr.com/500/500/vacuum,cleaner','مكنسة لاسلكية قوية بشفط عالي وفلتر هواء قابل للغسل، مثالية لكل أنواع الأرضيات.',4.6,40],
        ['household','طقم سكاكين مطبخ ستانلس 6 قطع', 420, null,'شارب إيدج','https://loremflickr.com/500/500/kitchen,knives','طقم سكاكين حادة من الستانلس ستيل مع حامل خشبي، مناسب لكل أعمال التقطيع اليومية.',4.4,80],
        ['household','مكواة بخار 2400 وات', 950, 1150,'ستيم برو','https://loremflickr.com/500/500/iron,steam','مكواة بخار قوية بقاعدة سيراميك تنزلق بسهولة على كل أنواع الأقمشة.',4.3,55],
        ['household','طقم تنظيف منزلي شامل 8 قطع', 380, null,'كلين ماستر','https://loremflickr.com/500/500/cleaning,supplies','يشمل ممسحة وفرشاة ومنظفات أساسية لتنظيف شامل للمنزل.',4.5,70],
        ['household','خلاط كهربائي متعدد السرعات 1.5 لتر', 1100, 1350,'باور ميكس','https://loremflickr.com/500/500/blender,kitchen','خلاط بقوة موتور عالية وكؤوس زجاجية مناسب للعصائر والصوصات.',4.2,35],
        ['household','مكنسة يد لاسلكية قابلة للشحن', 650, null,'هوم برو','https://loremflickr.com/500/500/handvacuum,home','خفيفة وسهلة الاستخدام لتنظيف السيارة والأماكن الضيقة.',4.1,60],
        // بلاستيكات
        ['plastics','دولاب بلاستيك 4 أدراج', 1450, 1700,'بلاست لاين','https://loremflickr.com/500/500/plastic,drawers','دولاب تخزين عملي بأدراج متينة، مناسب للملابس وأدوات المنزل.',4.4,30],
        ['plastics','طقم كراسي حديقة بلاستيك (4 قطع)', 1200, null,'جاردن لايف','https://loremflickr.com/500/500/plastic,chair','كراسي خفيفة ومتينة قابلة للتكديس، مناسبة للحدائق والشرفات.',4.3,45],
        ['plastics','صناديق تخزين بلاستيك شفافة (طقم 3)', 320, 400,'كليرباك','https://loremflickr.com/500/500/storage,box','صناديق شفافة بأغطية محكمة لتنظيم وتخزين مستلزمات المنزل.',4.6,90],
        ['plastics','طاولة بلاستيك قابلة للطي', 680, null,'فولد إيت','https://loremflickr.com/500/500/plastic,table','طاولة خفيفة قابلة للطي والنقل، مناسبة للاستخدام الداخلي والخارجي.',4.2,25],
        ['plastics','سلة غسيل بلاستيك مع مقابض', 180, null,'بلاست لاين','https://loremflickr.com/500/500/laundry,basket','سلة متينة بفتحات تهوية لنقل وتخزين الملابس بسهولة.',4.5,100],
        ['plastics','أرفف بلاستيك تخزين 5 طبقات', 890, 1050,'استورج پلس','https://loremflickr.com/500/500/plastic,shelf','أرفف قوية لتنظيم المخزن أو غرفة الغسيل بسهولة التركيب.',4.3,40],
        // مفروشات
        ['furnishings','طقم مفارش سرير قطن مزدوج', 1350, 1600,'كوزي هوم','https://loremflickr.com/500/500/bedding,bedsheet','طقم مفارش ناعم 100% قطن بتصميم أنيق يناسب جميع غرف النوم.',4.7,50],
        ['furnishings','ستائر بلاك آوت غرفة معيشة', 980, null,'دريم درايب','https://loremflickr.com/500/500/curtains,living','ستائر كثيفة تحجب الضوء تماماً وتضيف لمسة فخمة للصالة.',4.5,38],
        ['furnishings','طقم وسائد ديكور (4 قطع)', 460, 550,'سوفت تاتش','https://loremflickr.com/500/500/pillow,decor','وسائد ديكور بخامات فاخرة لإضافة لمسة جمالية للأنتريه.',4.4,65],
        ['furnishings','سجادة صالون كلاسيك 2×3 متر', 2200, 2600,'پرشان ستايل','https://loremflickr.com/500/500/carpet,rug','سجادة بنقشة كلاسيكية وخامة متينة تناسب الصالات الواسعة.',4.6,20],
        ['furnishings','مفرش طاولة طعام مطرز', 320, null,'إليجانت تيبل','https://loremflickr.com/500/500/tablecloth,dining','مفرش أنيق بتطريز يدوي يضيف رونقاً لسفرة المنزل.',4.3,55],
        ['furnishings','غطاء كنبة مرن 3 مقاعد', 540, 650,'كوزي هوم','https://loremflickr.com/500/500/sofa,cover','غطاء مطاطي مرن يناسب أغلب مقاسات الكنب ويحميه من الأتساخ.',4.2,42],
    ];

    $stmt = $conn->prepare("INSERT INTO products (category_id, name, price, old_price, brand, image, description, rating, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach($products as $p){
        $cat_id = $cat_ids[$p[0]];
        $stmt->bind_param("isddsssdi", $cat_id, $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8]);
        $stmt->execute();
    }
    echo "✅ تم إضافة " . count($products) . " منتج تجريبي على الفئات الثلاثة<br>";
} else {
    echo "ℹ️ المنتجات موجودة بالفعل<br>";
}

echo "<br><h2>✅ الإعداد اكتمل بنجاح!</h2>";
echo "<a href='index.php'>الذهاب للمتجر</a> &nbsp; <a href='admin/login.php'>لوحة التحكم</a>";
?>
</body>
</html>
