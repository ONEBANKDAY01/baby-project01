<?php
include '../config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// ตรวจสอบ ID สินค้า
if (!isset($_GET['id'])) {
    die("Product ID is required.");
}

$product_id = $_GET['id'];

// ลบสินค้า
$stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$stmt->bindParam(':id', $product_id);

if ($stmt->execute()) {
    echo "Product deleted successfully! <a href='admin_dashboard.php'>Back to Dashboard</a>";
} else {
    echo "Failed to delete product.";
}
?>
