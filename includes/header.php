<?php
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก - ร้านของฉัน</title>
    <link rel="stylesheet" href="../assets/css/header.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- แถบนำทาง -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="home.php">My Bank Shop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="home.php">หน้าแรก</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="shop.php">ร้านค้า</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="cart.php">ตะกร้าสินค้า</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="history.php">ประวัติการสั่งซื้อ</a>
                </li>
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<li class="nav-item"><a class="nav-link text-white" href="logout.php">ออกจากระบบ</a></li>';
                } else {
                    echo '<li class="nav-item"><a class="nav-link text-white" href="login.php">เข้าสู่ระบบ</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/header.js"></script> 

</body>
</html>
