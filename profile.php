<?php
session_start();
include("connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'tenant') {
    header("Location: login.php"); exit();
}
$tenant_id = isset($_SESSION['tenant_id']) ? $_SESSION['tenant_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['id']);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $line = mysqli_real_escape_string($conn, $_POST['lineid']);
    
    $sql_update = "UPDATE TENANT SET tenant_fname='$fname', tenant_lname='$lname', tenant_phone='$phone', tenant_lineid='$line' WHERE tenant_id='$tenant_id'";
    if(mysqli_query($conn, $sql_update)) {
        $_SESSION['fname'] = $fname;
        echo "<script>alert('บันทึกข้อมูลเรียบร้อยแล้ว'); window.location='profile.php';</script>";
    }
}

$sql = "SELECT * FROM TENANT WHERE tenant_id = '$tenant_id'";
$user = mysqli_fetch_assoc(mysqli_query($conn, $sql));
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลส่วนตัว - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="container py-4" style="max-width: 700px;">
        <h3 class="fw-bold text-primary mb-4"><i class="bi bi-person-circle"></i> ข้อมูลส่วนตัว</h3>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted">ชื่อผู้ใช้งาน (Username)</label>
                        <input type="text" class="form-control" value="<?php echo $user['tenant_username']; ?>" disabled>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">ชื่อจริง</label>
                            <input type="text" name="fname" class="form-control" value="<?php echo $user['tenant_fname']; ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">นามสกุล</label>
                            <input type="text" name="lname" class="form-control" value="<?php echo $user['tenant_lname']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">เบอร์โทรศัพท์</label>
                        <input type="text" name="phone" class="form-control" maxlength="10" value="<?php echo $user['tenant_phone']; ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">LINE ID</label>
                        <input type="text" name="lineid" class="form-control" value="<?php echo $user['tenant_lineid']; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2 shadow-sm">บันทึกการเปลี่ยนแปลง</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>