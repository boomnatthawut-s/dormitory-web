<?php
session_start();
include("connect.php");

// ตรวจสอบสิทธิ์ผู้เช่า
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'tenant') {
    header("Location: login.php");
    exit();
}
$tenant_id = isset($_SESSION['tenant_id']) ? $_SESSION['tenant_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['id']);

// 1. ค้นหาห้องที่ผู้เช่าพักอยู่ปัจจุบัน
$room_sql = "SELECT room_id FROM CONTRACT WHERE tenant_id = '$tenant_id' AND contract_status = 'active' LIMIT 1";
$room_res = mysqli_query($conn, $room_sql);
$room_row = mysqli_fetch_assoc($room_res);
$room_id = $room_row ? $room_row['room_id'] : '';

// 2. ฟังก์ชันคำนวณและอัปเดตค่าปรับอัตโนมัติ (วันละ 50 บาท)
function updateOverduePenalty($conn, $room_id) {
    if(!$room_id) return;
    $current_date = date('Y-m-d');
    $penalty_per_day = 50.00;

    $sql = "SELECT b.bill_id, b.due_date, b.room_rent, b.electric_total, b.water_total, 
                   DATEDIFF('$current_date', b.due_date) AS days_late 
            FROM BILL b 
            JOIN UTILITY_RECORD u ON b.record_id = u.record_id 
            WHERE u.room_id = '$room_id' AND b.bill_status = 'pending' AND b.due_date < '$current_date'";
    
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $bill_id = $row['bill_id'];
        $days_late = $row['days_late'];
        
        $penalty_total = $days_late * $penalty_per_day;
        $net_total = $row['room_rent'] + $row['electric_total'] + $row['water_total'] + $penalty_total;
        
        $update_sql = "UPDATE BILL 
                       SET penalty_total = '$penalty_total', net_total = '$net_total' 
                       WHERE bill_id = '$bill_id'";
        mysqli_query($conn, $update_sql);
    }
}

updateOverduePenalty($conn, $room_id);

// 3. ดึงข้อมูลบิลค้างชำระ (pending) ล่าสุดขึ้นมาแสดง พร้อมหาเลขมิเตอร์เดือนก่อน
$bill_sql = "SELECT b.*, u.electric_meter, u.electric_rate, u.water_rate, u.room_id, u.record_id,
                   (SELECT u2.electric_meter 
                    FROM UTILITY_RECORD u2 
                    WHERE u2.room_id = u.room_id AND u2.record_id < u.record_id 
                    ORDER BY u2.record_id DESC LIMIT 1) AS prev_meter
             FROM BILL b 
             JOIN UTILITY_RECORD u ON b.record_id = u.record_id 
             WHERE u.room_id = '$room_id' AND b.bill_status = 'pending' 
             ORDER BY b.bill_id DESC LIMIT 1";
$bill_res = mysqli_query($conn, $bill_sql);
$bill = mysqli_fetch_assoc($bill_res);

$units_used = 0;
if ($bill) {
    $prev_meter = !empty($bill['prev_meter']) ? $bill['prev_meter'] : 0;
    $units_used = $bill['electric_meter'] - $prev_meter;
    if ($units_used < 0) {
        $units_used = 0;
    }
}

// 4. จัดการเมื่อผู้เช่ากดส่งหลักฐานการชำระเงิน
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
    if ($bill) {
        $bill_id = $bill['bill_id'];
        $total_paid = $bill['net_total'];
        $payment_date = date('Y-m-d H:i:s'); // บันทึกทั้งวันที่และเวลา
        
        // จัดการอัปโหลดไฟล์สลิป
        $slip_image = 'slip_sample.jpg'; 
        if(isset($_FILES['slip_image']) && $_FILES['slip_image']['name'] != '') {
            $ext = pathinfo($_FILES['slip_image']['name'], PATHINFO_EXTENSION);
            $slip_image = 'slip_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['slip_image']['tmp_name'], 'uploads/' . $slip_image);
        }

        // สร้างรหัส payment_id อัตโนมัติ (เช่น PAY11, PAY12...) เพื่อป้องกันค่าว่าง
        $res_pay_id = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(payment_id, 4) AS UNSIGNED)) AS max_id FROM PAYMENT");
        $row_pay_id = mysqli_fetch_assoc($res_pay_id);
        $next_pay_num = $row_pay_id['max_id'] ? $row_pay_id['max_id'] + 1 : 1;
        $payment_id = 'PAY' . str_pad($next_pay_num, 2, '0', STR_PAD_LEFT);

        // บันทึกข้อมูลลงตาราง PAYMENT พร้อม payment_id ที่สร้างขึ้น
        $pay_sql = "INSERT INTO PAYMENT (payment_id, payment_date, total_paid, slip_image, verify_status, bill_id) 
                    VALUES ('$payment_id', '$payment_date', '$total_paid', '$slip_image', 'pending', '$bill_id')";
        
        if (mysqli_query($conn, $pay_sql)) {
            echo "<script>alert('แจ้งชำระเงินสำเร็จ กรุณารอแอดมินตรวจสอบ'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ชำระเงินค่าเช่า - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- เรียกใช้ Sidebar เมนูด้านข้าง -->
    <?php include("sidebar.php"); ?>

    <div class="main-content">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h3 class="fw-bold text-primary mb-4"><i class="bi bi-wallet2"></i> ชำระเงินค่าเช่าห้อง <?php echo $room_id; ?></h3>
                    
                    <?php if ($bill): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                            <h5 class="fw-bold text-dark mb-3">รายละเอียดบิลประจำงวด</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>ค่าห้องพัก:</span> <strong>฿<?php echo number_format($bill['room_rent'], 2); ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>ค่าไฟฟ้า (ใช้ไป <?php echo number_format($units_used, 2); ?> หน่วย):</span> <strong>฿<?php echo number_format($bill['electric_total'], 2); ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>ค่าน้ำประปา (เหมา):</span> <strong>฿<?php echo number_format($bill['water_total'], 2); ?></strong>
                                </li>
                                <?php if ($bill['penalty_total'] > 0): ?>
                                    <li class="list-group-item d-flex justify-content-between text-danger bg-light">
                                        <span>ค่าปรับเกินกำหนด:</span> <strong>฿<?php echo number_format($bill['penalty_total'], 2); ?></strong>
                                    </li>
                                <?php endif; ?>
                                <li class="list-group-item d-flex justify-content-between fs-5 fw-bold text-primary bg-white pt-3">
                                    <span>ยอดชำระสุทธิรวมทั้งสิ้น:</span> <span>฿<?php echo number_format($bill['net_total'], 2); ?></span>
                                </li>
                            </ul>
                            <p class="text-muted small mb-0">กำหนดชำระภายในวันที่: <?php echo date('d/m/Y', strtotime($bill['due_date'])); ?></p>
                        </div>

                        <!-- ฟอร์มแนบสลิป -->
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <form method="POST" enctype="multipart/form-data">
                                <h5 class="fw-bold text-primary mb-3">โอนเงินชำระผ่านบัญชีธนาคาร</h5>
                                <p class="text-muted mb-3">ธนาคารไทยพาณิชย์ (SCB) <br><strong>เลขที่บัญชี: 123-4-56789-0</strong> <br>ชื่อบัญชี: หอพัก IDMS DORMITORY</p>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">อัปโหลดหลักฐานการโอนเงิน (สลิป)</label>
                                    <input type="file" name="slip_image" class="form-control" accept="image/*" required>
                                </div>
                                <button type="submit" name="pay_now" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">ยืนยันการชำระเงิน</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success rounded-4 shadow-sm text-center py-4">
                            <h4 class="fw-bold">ยอดเยี่ยม!</h4>
                            <p class="mb-0">คุณไม่มียอดค้างชำระในขณะนี้</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>