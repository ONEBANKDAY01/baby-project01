<?php
include '../config.php';
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// ตรวจสอบว่ามีการส่ง id ของผู้ใช้มาหรือไม่
if (!isset($_GET['id'])) {
    header('Location: admin_users.php');
    exit();
}

$user_id = $_GET['id'];

// ดึงข้อมูลผู้ใช้
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    echo "User not found!";
    exit();
}

// อัปเดตข้อมูลผู้ใช้หากฟอร์มถูกส่งมา
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // ถ้ารหัสผ่านใหม่ถูกกรอก จะทำการอัปเดตรหัสผ่าน
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        $password = $user['password']; // รักษารหัสผ่านเดิมถ้าไม่เปลี่ยน
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, password = :password, role = :role WHERE id = :id");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    // หลังจากอัปเดตเสร็จ ให้ไปที่หน้า `admin_users.php`
    header('Location: admin_users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-4">
        <h1>Edit User (ID: <?= $user['id']; ?>)</h1>
        <form action="edit_user.php?id=<?= $user['id']; ?>" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?= $user['username']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password (Leave empty if not changing)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success mt-3">Save Changes</button>
        </form>
        <a href="admin_users.php" class="btn btn-secondary mt-3">Back to User List</a>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
