<?php
include '../config.php';
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// การลบผู้ใช้
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $userId = $_GET['id'];

    // ตรวจสอบว่าผู้ใช้มีอยู่ในฐานข้อมูล
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();

    // ถ้าผู้ใช้มีอยู่ในฐานข้อมูล
    if ($user) {
        // ลบผู้ใช้
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect กลับไปที่หน้า admin_users.php
        header("Location: admin_users.php?success=deleted");
        exit();
    } else {
        // ถ้าผู้ใช้ไม่พบ
        echo "<script>alert('ไม่พบผู้ใช้!');</script>";
    }
}

// กำหนดจำนวนผู้ใช้ต่อหน้า
$limit = 10; // จำนวนผู้ใช้ที่แสดงในแต่ละหน้า
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // หน้าที่แสดงผล (ค่าเริ่มต้นเป็น 1)
$start = ($page - 1) * $limit; // คำนวณตำแหน่งเริ่มต้น

// คำสั่งค้นหาผู้ใช้
$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $searchQuery = " WHERE username LIKE :search OR email LIKE :search";
}

// ดึงจำนวนผู้ใช้ทั้งหมด (ใช้ในการคำนวณจำนวนหน้า)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users" . $searchQuery);
if ($searchQuery) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->execute();
$totalUsers = $stmt->fetchColumn();

// คำนวณจำนวนหน้าที่จะมี
$totalPages = ceil($totalUsers / $limit);

// ดึงข้อมูลผู้ใช้ตามหน้าที่ต้องการ
$stmt = $pdo->prepare("SELECT * FROM users" . $searchQuery . " LIMIT :start, :limit");
if ($searchQuery) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผู้ดูแลระบบ - ผู้ใช้</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-4">
        <h1>การจัดการผู้ใช้</h1>

        <!-- ฟอร์มค้นหาผู้ใช้ -->
        <form class="mb-3" action="admin_users.php" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อผู้ใช้หรืออีเมล" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button class="btn btn-primary" type="submit">ค้นหา</button>
            </div>
        </form>

        <!-- ตารางแสดงผู้ใช้ -->
        <table class="table">
            <thead>
                <tr>
                    <th>รหัสผู้ใช้</th>
                    <th>ชื่อผู้ใช้</th>
                    <th>อีเมล</th>
                    <th>บทบาท</th>
                    <th>การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id']; ?></td>
                            <td><?= $user['username']; ?></td>
                            <td><?= $user['email']; ?></td>
                            <td><?= ucfirst($user['role']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id']; ?>" class="btn btn-warning">แก้ไข</a>
                                <a href="admin_users.php?action=delete&id=<?= $user['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?');">ลบ</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">ไม่พบผู้ใช้</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- ลิงก์สำหรับการแบ่งหน้า -->
        <nav>
            <ul class="pagination">
                <li class="page-item <?= $page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="admin_users.php?page=<?= $page - 1; ?>" tabindex="-1">ย้อนกลับ</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="admin_users.php?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page == $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="admin_users.php?page=<?= $page + 1; ?>">ถัดไป</a>
                </li>
            </ul>
        </nav>

        <a href="admin_dashboard.php" class="btn btn-secondary">กลับไปที่แผงควบคุม</a>
    </div>

</body>
</html>
