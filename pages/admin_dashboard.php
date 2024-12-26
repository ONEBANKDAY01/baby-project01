<?php
include '../config.php';
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// ดึงข้อมูลสรุป
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// ดึงข้อมูลสินค้า
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แผงควบคุมผู้ดูแลระบบ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-4">
        <h1>เเอดมิน</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">จำนวนสินค้าทั้งหมด</h5>
                        <p class="card-text"><?= $total_products; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">จำนวนผู้ใช้งาน</h5>
                        <p class="card-text"><?= $total_users; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">จำนวนคำสั่งซื้อ</h5>
                        <p class="card-text"><?= $total_orders; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <a href="add_product.php" class="btn btn-primary mt-4">เพิ่มสินค้าใหม่</a>
        <a href="admin_orders.php" class="btn btn-primary mt-4">จัดการคำสั่งซื้อ</a>
        <a href="admin_users.php" class="btn btn-primary mt-4">แก้ไขผู้ใช้งาน</a>
        <div class="mt-4">
            <h2>รายการสินค้า</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th>ราคา</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id']; ?></td>
                        <td><?= $product['name']; ?></td>
                        <td><?= $product['price']; ?></td>
                        <td>
                            <a href="edit_product.php?id=<?= $product['id']; ?>" class="btn btn-warning">แก้ไข</a>
                            <a href="delete_product.php?id=<?= $product['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?');">ลบ</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>
