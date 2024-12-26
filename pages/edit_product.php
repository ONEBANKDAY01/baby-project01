<?php
include '../config.php';
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// ตรวจสอบว่ามีการส่ง id ของสินค้ามาหรือไม่
if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

$product_id = $_GET['id'];

// ดึงข้อมูลสินค้า
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->bindParam(':id', $product_id);
$stmt->execute();
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found!";
    exit();
}

// อัปเดตข้อมูลสินค้าหากฟอร์มถูกส่งมา
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    // ถ้ามีการอัปโหลดไฟล์ภาพใหม่
    if ($image) {
        $target_dir = "../assets/images/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        // ถ้าไม่อัปโหลดภาพใหม่ ให้ใช้ภาพเดิม
        $image = $product['image'];
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, description = :description, image = :image WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':image', $image);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();

    // หลังจากอัปเดตเสร็จ ให้ไปที่หน้า `admin_dashboard.php`
    header('Location: admin_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h1>Edit Product (ID: <?= $product['id']; ?>)</h1>
        <form action="edit_product.php?id=<?= $product['id']; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= $product['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price (฿)</label>
                <input type="number" name="price" id="price" class="form-control" value="<?= $product['price']; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" required><?= $product['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" name="image" id="image" class="form-control">
                <p>Current image: <?= $product['image']; ?></p>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
        </form>
        <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
