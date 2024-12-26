<?php
include '../config.php';
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// ลบคำสั่งซื้อหากได้รับคำสั่ง
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // ลบข้อมูลในตาราง order_items
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = :order_id");
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();

    // ลบข้อมูลในตาราง orders
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->bindParam(':id', $order_id);
    $stmt->execute();

    // หลังจากลบเสร็จ ให้รีเฟรชหน้าไปที่ `admin_orders.php`
    header('Location: admin_orders.php');
    exit();
}

// ดึงรายการคำสั่งซื้อทั้งหมด
$stmt = $pdo->prepare("SELECT * FROM orders");
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผู้ดูแลระบบ - คำสั่งซื้อ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-4">
        <h1>รายการคำสั่งซื้อ</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>รหัสคำสั่งซื้อ</th>
                    <th>ชื่อลูกค้า</th>
                    <th>ราคารวม</th>
                    <th>สถานะ</th>
                    <th>การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id']; ?></td>
                        <td><?= $order['customer_name']; ?></td>
                        <td><?= $order['total_price']; ?> ฿</td>
                        <td><?= ucfirst($order['status']); ?></td>
                        <td>
                            <a href="order_details.php?id=<?= $order['id']; ?>" class="btn btn-primary">ดูรายละเอียด</a>
                            <!-- ปุ่ม Delete -->
                            <a href="admin_orders.php?action=delete&id=<?= $order['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคำสั่งซื้อนี้?');">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="btn btn-secondary">กลับไปที่แผงควบคุม</a>
    </div>

</body>
</html>
