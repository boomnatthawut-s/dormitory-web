<?php
session_start();
include("connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'tenant') {
    header("Location: login.php"); exit();
}
$tenant_id = isset($_SESSION['tenant_id']) ? $_SESSION['tenant_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สัญญาเช่า - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<?php include("sidebar.php"); ?>

<div class="main-content">
    <div class="container py-4 d-flex justify-content-center">
        <div class="col-md-8">
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-file-text"></i> ข้อมูลสัญญาเช่า</h3>
            
            <?php
            $sql = "SELECT c.*, r.room_type, r.monthly_rent 
                    FROM CONTRACT c JOIN ROOM r ON c.room_id = r.room_id 
                    WHERE c.tenant_id = '$tenant_id' ORDER BY c.contract_id DESC LIMIT 1";
            $res = mysqli_query($conn, $sql);
            
            if($contract = mysqli_fetch_assoc($res)):
            ?>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <div class="row mb-4 text-center">
                        <div class="col-12">
                            <h5 class="text-muted">ห้องพักหมายเลข</h5>
                            <h1 class="display-3 fw-bold text-primary"><?php echo $contract['room_id']; ?></h1>
                            <span class="badge bg-light text-dark fs-6"><?php echo $contract['room_type']; ?></span>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-4">
                        <div class="col-sm-6 mb-3">
                            <p class="text-muted mb-1">วันที่เริ่มสัญญา:</p>
                            <h5 class="fw-bold"><?php echo date('d/m/Y', strtotime($contract['start_date'])); ?></h5>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <p class="text-muted mb-1">วันที่สิ้นสุดสัญญา:</p>
                            <h5 class="fw-bold"><?php echo date('d/m/Y', strtotime($contract['end_date'])); ?></h5>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <p class="text-muted mb-1">ค่าเช่ารายเดือน:</p>
                            <h5 class="fw-bold text-dark">฿<?php echo number_format($contract['monthly_rent'], 2); ?></h5>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <p class="text-muted mb-1">เงินมัดจำ (แรกเข้า):</p>
                            <h5 class="fw-bold text-dark">฿<?php echo number_format($contract['deposit_total'], 2); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-warning rounded-4 shadow-sm">ไม่พบข้อมูลสัญญาเช่าที่เปิดใช้งานอยู่</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>