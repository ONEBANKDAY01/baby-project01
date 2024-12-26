<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - My Bank Shop</title>
    <link rel="stylesheet" href="../assets/css/home.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Introduction Section -->
    <div class="container mt-5">
        <div class="row align-items-center">
            <div class="col-md-6 text-center">
                <img src="../assets/images/pro.jpg" alt="Your Photo" class="img-fluid rounded-circle profile-img shadow-lg">
            </div>
            <div class="col-md-6">
                <h2 class="display-4 text-dark mb-4">Hello, I'm Bank</h2>
                <p class="lead text-muted mb-4">
                    Welcome to My Bank Shop! I'm passionate about coding and creating beautiful websites. Enjoy your shopping experience 😍
                </p>
                <a href="shop.php" class="btn btn-primary btn-lg">Start Shopping</a>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/home.js"></script> 
</body>
</html>
