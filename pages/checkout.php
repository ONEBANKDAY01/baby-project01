<?php
include '../config.php';
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ดึงข้อมูลสินค้าจากตะกร้า
$cart = $_SESSION['cart'] ?? [];
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// เมื่อส่งคำสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method']; // รับค่าการชำระเงินจากฟอร์ม

    if (empty($customer_name) || empty($address) || empty($phone) || empty($payment_method)) {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        // บันทึกคำสั่งซื้อ
        $pdo->beginTransaction();
        try {
            // เพิ่มคำสั่งซื้อพร้อม payment_method
            $stmt = $pdo->prepare(
                "INSERT INTO orders (customer_id, customer_name, address, phone_number, total_price, payment_method, created_at, status) 
                 VALUES (:customer_id, :customer_name, :address, :phone_number, :total_price, :payment_method, NOW(), 'pending')"
            );
            $stmt->execute([
                ':customer_id' => $_SESSION['user_id'], // ID ของลูกค้าจาก session
                ':customer_name' => $customer_name, // ชื่อของลูกค้า
                ':address' => $address, // ที่อยู่ของลูกค้า
                ':phone_number' => $phone, // เบอร์โทรศัพท์ลูกค้า
                ':total_price' => $total_price, // ราคารวมทั้งหมด
                ':payment_method' => $payment_method, // วิธีการชำระเงิน
            ]);
            $order_id = $pdo->lastInsertId();

            // เพิ่มสินค้าใน order_items
            $stmt = $pdo->prepare(
                "INSERT INTO order_items (order_id, product_name, quantity, product_price) 
                 VALUES (:order_id, :product_name, :quantity, :product_price)"
            );
            foreach ($cart as $item) {
                $stmt->execute([
                    ':order_id' => $order_id,
                    ':product_name' => $item['name'],
                    ':quantity' => $item['quantity'],
                    ':product_price' => $item['price'],
                ]);
            }

            $_SESSION['cart'] = [];
            $pdo->commit();

            header('Location: history.php');
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "เกิดข้อผิดพลาดขณะดำเนินการคำสั่งซื้อ: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h1>ชำระเงิน</h1>

        <?php if (!empty($cart)): ?>
            <div class="row">
                <!-- แสดงสินค้าในตะกร้า -->
                <div class="col-md-6">
                    <h4>สินค้าของคุณ</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>สินค้า</th>
                                <th>จำนวน</th>
                                <th>ราคา</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']); ?></td>
                                    <td><?= htmlspecialchars($item['quantity']); ?></td>
                                    <td><?= htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?> ฿</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <h5>ราคารวมทั้งหมด: <?= number_format($total_price, 2); ?> ฿</h5>
                </div>

                <!-- ฟอร์มกรอกข้อมูล -->
                <div class="col-md-6">
                    <h4>ข้อมูลการจัดส่ง</h4>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">ชื่อ-นามสกุล</label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">ที่อยู่</label>
                            <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">วิธีการชำระเงิน</label><br>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="เก็บปลายทาง" required>
                                <label class="form-check-label" for="cod">
                                    เก็บปลายทาง
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="PromptPay" required>
                                <label class="form-check-label" for="bank_transfer">
                                    พร้อมเพย์
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">สั่งซื้อ</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted">ตะกร้าสินค้าของคุณว่างเปล่า <a href="shop.php">เลือกซื้อสินค้า</a>.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
