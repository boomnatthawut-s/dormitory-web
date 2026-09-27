<?php
session_start();
include("connect.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // 1. ตรวจสอบในตาราง TENANT (ผู้เช่า)
    $sql_tenant = "SELECT * FROM TENANT WHERE tenant_username = '$username' AND tenant_password = '$password'";
    $res_tenant = mysqli_query($conn, $sql_tenant);

    if ($row_tenant = mysqli_fetch_assoc($res_tenant)) {
        // เช็คสถานะการอนุมัติ
        if ($row_tenant['tenant_status'] == 'pending') {
            $error = "บัญชีของคุณยังไม่ได้รับการอนุมัติจากแอดมิน";
        } else {
            $_SESSION['role'] = 'tenant';
            $_SESSION['tenant_id'] = $row_tenant['tenant_id'];
            $_SESSION['fname'] = $row_tenant['tenant_fname'];
            header("Location: index.php");
            exit();
        }
    } else {
        // 2. ตรวจสอบในตาราง ADMIN (ผู้ดูแลระบบ) - ปรับชื่อตารางตามระบบฐานข้อมูลของคุณ
        $sql_admin = "SELECT * FROM ADMIN WHERE admin_username = '$username' AND admin_password = '$password'";
        $res_admin = mysqli_query($conn, $sql_admin);

        if ($row_admin = mysqli_fetch_assoc($res_admin)) {
            $_SESSION['role'] = 'admin';
            $_SESSION['admin_id'] = $row_admin['admin_id'];
            $_SESSION['fname'] = $row_admin['admin_fname'];
            header("Location: index.php");
            exit();
        } else {
            $error = "ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center py-5">
<div class="container" style="max-width: 420px;">
    <div class="card p-4 shadow-sm border-0 rounded-4">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary"><i class="bi bi-building"></i> IDMS DORMITORY</h3>
            <p class="text-muted small">ระบบบริหารจัดการหอพัก</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 text-center small rounded-3"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-muted fw-bold">ชื่อผู้ใช้งาน (Username)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" name="username" class="form-control border-start-0 ps-0" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted fw-bold">รหัสผ่าน (Password)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 ps-0" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm mb-3">เข้าสู่ระบบ</button>
            
            <div class="text-center">
                <span class="text-muted small">ยังไม่มีบัญชีผู้เช่า?</span>
                <a href="register.php" class="text-decoration-none fw-bold text-primary small ms-1">สมัครสมาชิกที่นี่</a>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>