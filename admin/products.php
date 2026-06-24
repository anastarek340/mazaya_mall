<?php
require_once 'includes/auth.php';
require_once __DIR__ . '/../config/helpers.php';

$msg = '';
$msg_type = '';

// ===================== DELETE =====================
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);

    $stmt = $conn->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();

    header('Location: products.php?msg=deleted');
    exit;
}

// ===================== ADD / EDIT =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $old_price = $_POST['old_price'] !== '' ? floatval($_POST['old_price']) : null;
    $brand = trim($_POST['brand']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category_id']);
    $stock = intval($_POST['stock']);
    $product_id = intval($_POST['product_id'] ?? 0);

    // ================= IMAGE UPLOAD =================
    $image = '';
    $upload_error = false;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            $msg = 'نوع الملف غير مسموح';
            $msg_type = 'error';
            $upload_error = true;

        } elseif ($_FILES['image']['size'] > 5242880) {
            $msg = 'الملف كبير جداً (5MB max)';
            $msg_type = 'error';
            $upload_error = true;

        } else {

            $img_name = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image']['name']);
            $tmp_name = $_FILES['image']['tmp_name'];

            // ✅ التعديل المهم
            $upload_dir = __DIR__ . '/../uploads/';
            $full_path = $upload_dir . $img_name;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($tmp_name, $full_path)) {
                $image = $img_name;
                chmod($full_path, 0644);
            } else {
                $msg = 'فشل رفع الصورة';
                $msg_type = 'error';
                $upload_error = true;
            }
        }
    }

    if (!$upload_error) {

        // ================= UPDATE =================
        if ($product_id > 0) {

            if ($image != '') {
                $stmt = $conn->prepare("
                    UPDATE products 
                    SET name=?, price=?, old_price=?, brand=?, image=?, description=?, category_id=?, stock=? 
                    WHERE id=?
                ");

                $stmt->bind_param(
                    "sddsssiii",
                    $name, $price, $old_price, $brand, $image,
                    $description, $category_id, $stock, $product_id
                );

            } else {
                $stmt = $conn->prepare("
                    UPDATE products 
                    SET name=?, price=?, old_price=?, brand=?, description=?, category_id=?, stock=? 
                    WHERE id=?
                ");

                $stmt->bind_param(
                    "sddssiii",
                    $name, $price, $old_price, $brand,
                    $description, $category_id, $stock, $product_id
                );
            }

            $stmt->execute();
            header('Location: products.php?msg=updated');
            exit;
        }

        // ================= INSERT =================
        else {

            $stmt = $conn->prepare("
                INSERT INTO products (name, price, old_price, brand, image, description, category_id, stock)
                VALUES (?,?,?,?,?,?,?,?)
            ");

            $stmt->bind_param(
                "sddsssii",
                $name, $price, $old_price, $brand,
                $image, $description, $category_id, $stock
            );

            $stmt->execute();
            header('Location: products.php?msg=added');
            exit;
        }
    }
}

// ===================== MESSAGES =====================
if (isset($_GET['msg'])) {
    $map = [
        'added' => 'تمت الإضافة',
        'updated' => 'تم التعديل',
        'deleted' => 'تم الحذف'
    ];

    $msg = $map[$_GET['msg']] ?? '';
    $msg_type = 'success';
}

// ===================== DATA =====================
$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);

$products = $conn->query("
    SELECT p.*, c.name as cat_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.deleted_at IS NULL
")->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المنتجات | لوحة تحكم مزايا مول</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
    <div class="page-head">
        <div>
            <h1>المنتجات</h1>
            <p>إدارة منتجات المتجر — الإضافة والتعديل والحذف</p>
        </div>
        <button class="btn btn-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> منتج جديد</button>
    </div>

    <?php if($msg): ?><div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div><?php endif; ?>

    <div class="panel">
        <table>
            <tr><th>المنتج</th><th>الفئة</th><th>السعر</th><th>المخزون</th><th></th></tr>
            <?php foreach($products as $p): ?>
            <tr>
                <td style="display:flex;align-items:center;gap:12px;">
                  <img 
    src="../uploads/<?php echo htmlspecialchars($p['image']); ?>" 
    onerror="this.src='../assets/images/placeholder.png'" 
    style="width:50px;height:50px;object-fit:cover;">
                    <div><strong><?php echo htmlspecialchars($p['name']); ?></strong><br><span style="color:#787a82;font-size:12px;"><?php echo htmlspecialchars($p['brand']); ?></span></div>
                </td>
                <td><?php echo htmlspecialchars($p['cat_name']); ?></td>
                <td><?php echo format_price($p['price']); ?><?php if($p['old_price']): ?><br><span style="text-decoration:line-through;color:#787a82;font-size:12px;"><?php echo format_price($p['old_price']); ?></span><?php endif; ?></td>
                <td><?php echo $p['stock']; ?></td>
                <td style="display:flex;gap:8px;">
                    <button class="icon-btn" title="تعديل" onclick='openEditModal(<?php echo json_encode($p, JSON_HEX_APOS|JSON_HEX_QUOT); ?>)'><i class="fa-solid fa-pen"></i></button>
                    <a class="icon-btn" title="حذف" href="products.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('متأكد من حذف المنتج؟')"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($products)): ?>
            <tr><td colspan="5" style="text-align:center;color:#787a82;">لا توجد منتجات</td></tr>
            <?php endif; ?>
        </table>
    </div>
</main>
</div>

<!-- Modal -->
<div class="modal-overlay" id="productModal">
    <div class="modal-box">
        <form method="post" enctype="multipart/form-data">
            <div class="modal-head">
                <h3 id="modalTitle">إضافة منتج جديد</h3>
                <span class="close-x" onclick="closeModal()" style="cursor:pointer;">&times;</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="product_id" id="product_id">
                <div class="f-group">
                    <label>اسم المنتج</label>
                    <input type="text" name="name" id="f_name" required>
                </div>
                <div class="f-row">
                    <div class="f-group">
                        <label>السعر (ج.م)</label>
                        <input type="number" step="0.01" name="price" id="f_price" required>
                    </div>
                    <div class="f-group">
                        <label>السعر قبل الخصم (اختياري)</label>
                        <input type="number" step="0.01" name="old_price" id="f_old_price">
                    </div>
                </div>
                <div class="f-row">
                    <div class="f-group">
                        <label>الماركة</label>
                        <input type="text" name="brand" id="f_brand" required>
                    </div>
                    <div class="f-group">
                        <label>الفئة</label>
                        <select name="category_id" id="f_category_id" required>
                            <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="f-group">
    <label>الصورة</label>
    <input type="file" name="image" id="f_image">
    <small>اتركها فارغة لو مش عايز تغيّر الصورة</small>
</div>
                <div class="f-group">
                    <label>الكمية في المخزون</label>
                    <input type="number" name="stock" id="f_stock" value="50">
                </div>
                <div class="f-group">
                    <label>الوصف</label>
                    <textarea name="description" id="f_description" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">إلغاء</button>
                <button type="submit" class="btn btn-primary">حفظ المنتج</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal(){
    document.getElementById('modalTitle').textContent = 'إضافة منتج جديد';
    document.querySelector('#productModal form').reset();
    document.getElementById('product_id').value = '';
    document.getElementById('f_stock').value = 50;
    document.getElementById('productModal').classList.add('show');
}
function openEditModal(p){
    document.getElementById('modalTitle').textContent = 'تعديل المنتج';
    document.getElementById('product_id').value = p.id;
    document.getElementById('f_name').value = p.name;
    document.getElementById('f_price').value = p.price;
    document.getElementById('f_old_price').value = p.old_price ?? '';
    document.getElementById('f_brand').value = p.brand;
    document.getElementById('f_category_id').value = p.category_id;

    document.getElementById('f_stock').value = p.stock;
    document.getElementById('f_description').value = p.description;
    document.getElementById('productModal').classList.add('show');
	
}
function closeModal(){
    document.getElementById('productModal').classList.remove('show');
}
</script>
</body>
</html>