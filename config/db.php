<?php
/**
 * ملف الاتصال بقاعدة البيانات
 * مزايا مول - نظام إدارة المتجر
 */

// إعدادات الاتصال
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'mazaya_mall';

// الاتصال
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// التحقق من الاتصال
if ($conn->connect_error) {
    die('❌ فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error . 
        '<br><br>تأكد من:<br>1. تشغيل XAMPP/MySQL<br>2. تنفيذ ملف setup.php أولاً<br>3. اسم المستخدم كلمة المرور');
}

// تعيين الترميز
$conn->set_charset('utf8mb4');
?>
