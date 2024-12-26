<?php
// เริ่ม session และเช็คว่ามีการสั่งซื้อหรือไม่
session_start();

// เช็คข้อมูลคำสั่งซื้อจาก session หรือ URL ถ้ามีการสั่งซื้อสำเร็จ
$orderID = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : null;
$successMessage = "Thank you for your order!";
if ($orderID) {
    // ทำการแสดงข้อความหรือรายละเอียดคำสั่งซื้อ
    $successMessage = "Thank you for your order! Your order ID is " . $orderID . ". You will receive an email confirmation shortly.";
    // ล้าง session ของคำสั่งซื้อเพื่อไม่ให้แสดงในหน้าต่อไป
    unset($_SESSION['order_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="text-center">
            <h1>Order Completed</h1>
            <p class="lead"><?= $successMessage; ?></p>
            <p>Thank you for shopping with us!</p>
            <a href="shop.php" class="btn btn-primary">Back to Home</a>
        </div>
    </div>

</body>
</html>
