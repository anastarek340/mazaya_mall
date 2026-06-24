-- ==================================================================
-- مزايا مول - نظام إدارة المتجر الإلكتروني
-- قاعدة البيانات الكاملة مع البيانات التجريبية
-- ==================================================================

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS `mazaya_mall` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `mazaya_mall`;

-- ==================================================================
-- جدول الفئات (Categories)
-- ==================================================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `cover` VARCHAR(500) NOT NULL,
    `description` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ==================================================================
-- جدول المنتجات (Products)
-- ==================================================================
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `old_price` DECIMAL(10,2) DEFAULT NULL,
    `brand` VARCHAR(100) NOT NULL,
    `image` VARCHAR(500) NOT NULL,
    `description` TEXT,
    `rating` DECIMAL(2,1) DEFAULT 4.5,
    `stock` INT DEFAULT 50,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
    INDEX `idx_category` (`category_id`),
    INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ==================================================================
-- جدول الطلبات (Orders)
-- ==================================================================
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `phone1` VARCHAR(20) NOT NULL,
    `phone2` VARCHAR(20),
    `address` TEXT NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `notes` TEXT,
    `status` ENUM('pending','processing','shipped','delivered') DEFAULT 'pending',
    `total_price` DECIMAL(10,2) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ==================================================================
-- جدول عناصر الطلب (Order Items)
-- ==================================================================
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `price` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ==================================================================
-- جدول المسؤولين (Admins)
-- ==================================================================
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ==================================================================
-- إدراج بيانات الفئات
-- ==================================================================
INSERT INTO `categories` (`name`, `slug`, `cover`, `description`) VALUES
('أدوات منزلية', 'household', 'https://loremflickr.com/800/500/kitchenware,cleaning', 'كل ما يحتاجه بيتك من أدوات تنظيف ومطبخ'),
('بلاستيكات', 'plastics', 'https://loremflickr.com/800/500/plastic,storage', 'حلول تخزين وأثاث بلاستيك عملية وعالية الجودة'),
('مفروشات', 'furnishings', 'https://loremflickr.com/800/500/bedding,textile', 'مفارش وستائر ووسائد تضيف لمسة من الأناقة لمنزلك');

-- ==================================================================
-- إدراج بيانات المنتجات التجريبية
-- ==================================================================

-- أدوات منزلية
INSERT INTO `products` (`category_id`, `name`, `price`, `old_price`, `brand`, `image`, `description`, `rating`, `stock`) VALUES
(1, 'مكنسة كهربائية لاسلكية 2200 وات', 1850, 2300, 'هوم برو', 'https://loremflickr.com/500/500/vacuum,cleaner', 'مكنسة لاسلكية قوية بشفط عالي وفلتر هواء قابل للغسل، مثالية لكل أنواع الأرضيات.', 4.6, 40),
(1, 'طقم سكاكين مطبخ ستانلس 6 قطع', 420, NULL, 'شارب إيدج', 'https://loremflickr.com/500/500/kitchen,knives', 'طقم سكاكين حادة من الستانلس ستيل مع حامل خشبي، مناسب لكل أعمال التقطيع اليومية.', 4.4, 80),
(1, 'مكواة بخار 2400 وات', 950, 1150, 'ستيم برو', 'https://loremflickr.com/500/500/iron,steam', 'مكواة بخار قوية بقاعدة سيراميك تنزلق بسهولة على كل أنواع الأقمشة.', 4.3, 55),
(1, 'طقم تنظيف منزلي شامل 8 قطع', 380, NULL, 'كلين ماستر', 'https://loremflickr.com/500/500/cleaning,supplies', 'يشمل ممسحة وفرشاة ومنظفات أساسية لتنظيف شامل للمنزل.', 4.5, 70),
(1, 'خلاط كهربائي متعدد السرعات 1.5 لتر', 1100, 1350, 'باور ميكس', 'https://loremflickr.com/500/500/blender,kitchen', 'خلاط بقوة موتور عالية وكؤوس زجاجية مناسب للعصائر والصوصات.', 4.2, 35),
(1, 'مكنسة يد لاسلكية قابلة للشحن', 650, NULL, 'هوم برو', 'https://loremflickr.com/500/500/handvacuum,home', 'خفيفة وسهلة الاستخدام لتنظيف السيارة والأماكن الضيقة.', 4.1, 60),

-- بلاستيكات
(2, 'دولاب بلاستيك 4 أدراج', 1450, 1700, 'بلاست لاين', 'https://loremflickr.com/500/500/plastic,drawers', 'دولاب تخزين عملي بأدراج متينة، مناسب للملابس وأدوات المنزل.', 4.4, 30),
(2, 'طقم كراسي حديقة بلاستيك (4 قطع)', 1200, NULL, 'جاردن لايف', 'https://loremflickr.com/500/500/plastic,chair', 'كراسي خفيفة ومتينة قابلة للتكديس، مناسبة للحدائق والشرفات.', 4.3, 45),
(2, 'صناديق تخزين بلاستيك شفافة (طقم 3)', 320, 400, 'كليرباك', 'https://loremflickr.com/500/500/storage,box', 'صناديق شفافة بأغطية محكمة لتنظيم وتخزين مستلزمات المنزل.', 4.6, 90),
(2, 'طاولة بلاستيك قابلة للطي', 680, NULL, 'فولد إيت', 'https://loremflickr.com/500/500/plastic,table', 'طاولة خفيفة قابلة للطي والنقل، مناسبة للاستخدام الداخلي والخارجي.', 4.2, 25),
(2, 'سلة غسيل بلاستيك مع مقابض', 180, NULL, 'بلاست لاين', 'https://loremflickr.com/500/500/laundry,basket', 'سلة متينة بفتحات تهوية لنقل وتخزين الملابس بسهولة.', 4.5, 100),
(2, 'أرفف بلاستيك تخزين 5 طبقات', 890, 1050, 'استورج پلس', 'https://loremflickr.com/500/500/plastic,shelf', 'أرفف قوية لتنظيم المخزن أو غرفة الغسيل بسهولة التركيب.', 4.3, 40),

-- مفروشات
(3, 'طقم مفارش سرير قطن مزدوج', 1350, 1600, 'كوزي هوم', 'https://loremflickr.com/500/500/bedding,bedsheet', 'طقم مفارش ناعم 100% قطن بتصميم أنيق يناسب جميع غرف النوم.', 4.7, 50),
(3, 'ستائر بلاك آوت غرفة معيشة', 980, NULL, 'دريم درايب', 'https://loremflickr.com/500/500/curtains,living', 'ستائر كثيفة تحجب الضوء تماماً وتضيف لمسة فخمة للصالة.', 4.5, 38),
(3, 'طقم وسائد ديكور (4 قطع)', 460, 550, 'سوفت تاتش', 'https://loremflickr.com/500/500/pillow,decor', 'وسائد ديكور بخامات فاخرة لإضافة لمسة جمالية للأنتريه.', 4.4, 65),
(3, 'سجادة صالون كلاسيك 2×3 متر', 2200, 2600, 'پرشان ستايل', 'https://loremflickr.com/500/500/carpet,rug', 'سجادة بنقشة كلاسيكية وخامة متينة تناسب الصالات الواسعة.', 4.6, 20),
(3, 'مفرش طاولة طعام مطرز', 320, NULL, 'إليجانت تيبل', 'https://loremflickr.com/500/500/tablecloth,dining', 'مفرش أنيق بتطريز يدوي يضيف رونقاً لسفرة المنزل.', 4.3, 55),
(3, 'غطاء كنبة مرن 3 مقاعد', 540, 650, 'كوزي هوم', 'https://loremflickr.com/500/500/sofa,cover', 'غطاء مطاطي مرن يناسب أغلب مقاسات الكنب ويحميه من الأتساخ.', 4.2, 42);

-- ==================================================================
-- إدراج بيانات المسؤول
-- ==================================================================
-- كلمة المرور الافتراضية: admin123
-- كلمة المرور المشفرة باستخدام PASSWORD_DEFAULT
INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '$2y$10$YourHashedPasswordHere');

-- ملاحظة: استبدل الـ hash بـ:
-- password_hash('admin123', PASSWORD_DEFAULT)
-- الكود PHP: echo password_hash('admin123', PASSWORD_DEFAULT);
