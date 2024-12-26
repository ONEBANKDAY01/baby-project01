<?php
include '../config.php';
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    // ตรวจสอบว่าได้อัปโหลดไฟล์ภาพหรือไม่
    if ($image) {
        $target_dir = "../assets/images/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        // ถ้าไม่ได้อัปโหลดภาพ ให้กำหนดค่าภาพเริ่มต้น
        $image = "default.jpg";
    }

    // เพิ่มข้อมูลสินค้าลงในฐานข้อมูล
    $stmt = $pdo->prepare("INSERT INTO products (name, price, description, image) VALUES (:name, :price, :description, :image)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':image', $image);
    $stmt->execute();

    // หลังจากเพิ่มสินค้าสำเร็จ ให้รีไดเรกต์ไปที่หน้า admin_dashboard.php
    header('Location: admin_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้า</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-4">
        <h1>เพิ่มสินค้าใหม่</h1>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">ชื่อสินค้า</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="price">ราคา (฿)</label>
                <input type="number" name="price" id="price" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">รายละเอียด</label>
                <textarea name="description" id="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">รูปภาพสินค้า</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary mt-3">เพิ่มสินค้า</button>
        </form>
        <a href="admin_dashboard.php" class="btn btn-secondary mt-3">กลับไปที่แผงควบคุม</a>
    </div>

</body>
</html>
