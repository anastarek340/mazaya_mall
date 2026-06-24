<?php
session_start();
require_once '../config/db.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result && $result->num_rows === 1){

        $admin = $result->fetch_assoc();

        // 👇 لو الباسورد عندك plain (بدون hash)
        if($password === $admin['password']){

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            header("Location: dashboard.php");
            exit;

        } else {
            $error = "بيانات الدخول غلط";
        }

    } else {
        $error = "بيانات الدخول غلط";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | لوحة تحكم مزايا مول</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-wrap">
        <div class="login-box">
            <div class="seal"><i class="fas fa-crown"></i></div>
            <h1>لوحة تحكم مزايا مول</h1>
            <p>سجّل دخولك لإدارة المتجر</p>

            <?php if(!empty($error)): ?>
                <div class="login-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="text" name="username" placeholder="اسم المستخدم" required autofocus>
                <input type="password" name="password" placeholder="كلمة المرور" required>
                <button type="submit">تسجيل الدخول</button>
            </form>

        </div>
    </div>
</body>
</html>