<?php
include '../config.php';
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// ตรวจสอบว่า ID ถูกส่งมาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'รหัสคำสั่งซื้อไม่ถูกต้อง!';
    header('Location: admin_orders.php');
    exit();
}

$order_id = intval($_GET['id']); // แปลงค่า ID ให้ปลอดภัย

// ดึงข้อมูลคำสั่งซื้อ
$stmt = $pdo->prepare("SELECT id, customer_name, address, phone_number, status, payment_method FROM orders WHERE id = :id");
$stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['message'] = 'ไม่พบคำสั่งซื้อ!';
    header('Location: admin_orders.php');
    exit();
}

// ดึงข้อมูลรายการสินค้าในคำสั่งซื้อ
$stmt = $pdo->prepare("SELECT product_name, product_price, quantity FROM order_items WHERE order_id = :order_id");
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// อัปเดตสถานะคำสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = trim($_POST['status']);

    // ตรวจสอบสถานะใหม่
    $valid_statuses = ['pending', 'shipped', 'cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        $_SESSION['message'] = 'สถานะไม่ถูกต้อง!';
        header("Location: order_details.php?id=" . $order_id);
        exit();
    }

    // อัปเดตสถานะในฐานข้อมูล
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
    $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['message'] = 'อัปเดตสถานะสำเร็จ!';
    header("Location: order_details.php?id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคำสั่งซื้อ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>รายละเอียดคำสั่งซื้อ (รหัสคำสั่งซื้อ: <?= htmlspecialchars($order['id']); ?>)</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['message']); ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- ข้อมูลลูกค้า -->
        <div class="mb-3">
            <h4>ข้อมูลลูกค้า</h4>
            <p><strong>ชื่อ:</strong> <?= htmlspecialchars($order['customer_name']); ?></p>
            <p><strong>ที่อยู่:</strong> <?= htmlspecialchars($order['address'] ?? 'ไม่มีข้อมูล'); ?></p>
            <p><strong>เบอร์โทรศัพท์:</strong> <?= htmlspecialchars($order['phone_number'] ?? 'ไม่มีข้อมูล'); ?></p>
            <p><strong>วิธีการชำระเงิน:</strong> <?= htmlspecialchars(ucfirst($order['payment_method'])); ?></p>
        </div>

        <!-- รายการสินค้า -->
        <div class="mb-3">
            <h4>รายการสินค้า</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>ชื่อสินค้า</th>
                        <th>ราคา</th>
                        <th>จำนวน</th>
                        <th>ยอดรวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($order_items as $item): ?>
                        <?php $subtotal = $item['product_price'] * $item['quantity']; ?>
                        <?php $total += $subtotal; ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']); ?></td>
                            <td><?= htmlspecialchars(number_format($item['product_price'], 2)); ?> ฿</td>
                            <td><?= htmlspecialchars($item['quantity']); ?></td>
                            <td><?= htmlspecialchars(number_format($subtotal, 2)); ?> ฿</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- สรุปคำสั่งซื้อ -->
        <div class="mb-3">
            <h4>สรุปคำสั่งซื้อ</h4>
            <p><strong>ราคารวมทั้งหมด:</strong> <?= htmlspecialchars(number_format($total, 2)); ?> ฿</p>
            <p><strong>วิธีการชำระเงิน:</strong> <?= htmlspecialchars(ucfirst($order['payment_method'])); ?></p>
            <p><strong>สถานะ:</strong> <?= htmlspecialchars(ucfirst($order['status'])); ?></p>
        </div>

        <!-- แบบฟอร์มอัปเดตสถานะ -->
        <form action="" method="POST" class="mb-3">
            <div class="form-group">
                <label for="status">อัปเดตสถานะ</label>
                <select name="status" id="status" class="form-control">
                    <option value="pending" <?= ($order['status'] === 'pending') ? 'selected' : ''; ?>>รอดำเนินการ</option>
                    <option value="shipped" <?= ($order['status'] === 'shipped') ? 'selected' : ''; ?>>จัดส่งแล้ว</option>
                    <option value="cancelled" <?= ($order['status'] === 'cancelled') ? 'selected' : ''; ?>>ยกเลิก</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">อัปเดตสถานะ</button>
        </form>

        <a href="admin_orders.php" class="btn btn-secondary">กลับไปหน้ารายการคำสั่งซื้อ</a>
    </div>
</body>
</html>
