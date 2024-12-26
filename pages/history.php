<?php
include '../config.php';
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ดึงข้อมูลประวัติคำสั่งซื้อ
$stmt = $pdo->prepare("
    SELECT o.id, o.customer_name, o.total_price, o.created_at, 
           COALESCE(o.status, 'pending') AS status, 
           GROUP_CONCAT(CONCAT(oi.product_name, ' (', oi.quantity, ')') SEPARATOR ', ') AS product_details
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    WHERE o.customer_id = :customer_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([':customer_id' => $_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h1>ประวัติการสั่งซื้อ</h1>

        <?php if (!empty($orders)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>รหัสคำสั่งซื้อ</th>
                        <th>ชื่อลูกค้า</th>
                        <th>รายการสินค้า</th>
                        <th>ราคารวม</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']); ?></td>
                            <td><?= htmlspecialchars($order['customer_name']); ?></td>
                            <td><?= htmlspecialchars($order['product_details']); ?></td>
                            <td><?= htmlspecialchars($order['total_price']); ?> ฿</td>
                            <td><?= htmlspecialchars($order['created_at']); ?></td>
                            <td>
                                <span class="badge bg-<?= $order['status'] === 'shipped' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                    <?= htmlspecialchars($order['status'] === 'shipped' ? 'จัดส่งแล้ว' : ($order['status'] === 'pending' ? 'รอดำเนินการ' : 'อื่นๆ')); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">คุณยังไม่มีคำสั่งซื้อในระบบ <a href="shop.php">เริ่มช้อปปิ้งเลย</a>.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
