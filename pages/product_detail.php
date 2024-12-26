<?php
include '../config.php';
session_start();

// ตรวจสอบ ID สินค้า
if (!isset($_GET['id'])) {
    die("จำเป็นต้องระบุรหัสสินค้า");
}

$product_id = $_GET['id'];

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->bindParam(':id', $product_id);
$stmt->execute();
$product = $stmt->fetch();

if (!$product) {
    die("ไม่พบสินค้า");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']); ?> - รายละเอียดสินค้า</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h1><?= htmlspecialchars($product['name']); ?></h1>
        <div class="row">
            <div class="col-md-6">
                <img src="../assets/images/<?= htmlspecialchars($product['image']); ?>" class="img-fluid" alt="<?= htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-md-6">
                <h2>ราคา: <?= htmlspecialchars($product['price']); ?> ฿</h2>
                <p><?= htmlspecialchars($product['description']); ?></p>

                <a href="cart.php?action=add&id=<?= htmlspecialchars($product['id']); ?>" class="btn btn-success">เพิ่มลงในตะกร้า</a>

            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
