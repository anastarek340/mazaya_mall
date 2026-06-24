<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if(!$data || empty($data['items']) || empty($data['phone1']) || empty($data['address']) || empty($data['city'])){
    echo json_encode(['success' => false, 'message' => 'من فضلك أكمل كل البيانات المطلوبة']);
    exit;
}

$phone1 = $conn->real_escape_string($data['phone1']);
$phone2 = $conn->real_escape_string($data['phone2'] ?? '');
$address = $conn->real_escape_string($data['address']);
$city = $conn->real_escape_string($data['city']);
$notes = $conn->real_escape_string($data['notes'] ?? '');

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO orders (phone1, phone2, address, city, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $phone1, $phone2, $address, $city, $notes);
    $stmt->execute();
    $order_id = $conn->insert_id;

    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach($data['items'] as $item){
        $product_id = intval($item['id']);
        $qty = intval($item['qty']);
        $price = floatval($item['price']);
        if($qty < 1) $qty = 1;
        $item_stmt->bind_param("iiid", $order_id, $product_id, $qty, $price);
        $item_stmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch(Exception $e){
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء حفظ الطلب']);
}
