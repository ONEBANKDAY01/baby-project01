<?php
include '../config.php';
session_start();

// เริ่มต้นตะกร้าสินค้าถ้ายังไม่มี
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// จัดการ Action ต่าง ๆ
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $product_id = $_GET['id'] ?? null;

    if ($action == 'add' && $product_id) {
        // ดึงข้อมูลสินค้าจากฐานข้อมูล
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(':id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch();

        if ($product) {
            // เพิ่มสินค้าในตะกร้า
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity']++;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                ];
            }
        }
    } elseif ($action == 'remove' && $product_id) {
        // ลบสินค้าออกจากตะกร้า
        unset($_SESSION['cart'][$product_id]);
    } elseif ($action == 'clear') {
        // ล้างตะกร้าสินค้า
        $_SESSION['cart'] = [];
    }
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h1>ตะกร้าสินค้า</h1>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ชื่อสินค้า</th>
                        <th>ราคา</th>
                        <th>จำนวน</th>
                        <th>รวม</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <?php $subtotal = $item['price'] * $item['quantity']; ?>
                        <?php $total += $subtotal; ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']); ?></td>
                            <td><?= htmlspecialchars($item['price']); ?> ฿</td>
                            <td><?= htmlspecialchars($item['quantity']); ?></td>
                            <td><?= htmlspecialchars($subtotal); ?> ฿</td>
                            <td>
                                <a href="cart.php?action=remove&id=<?= htmlspecialchars($id); ?>" class="btn btn-danger">ลบ</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>ยอดรวมทั้งหมด: <?= $total; ?> ฿</h3>
            <a href="cart.php?action=clear" class="btn btn-warning">ล้างตะกร้า</a>
            <a href="checkout.php" class="btn btn-primary">ชำระเงิน</a>
        <?php else: ?>
            <p>ตะกร้าสินค้าว่างเปล่า <a href="shop.php">กลับไปเลือกซื้อสินค้า</a>.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
