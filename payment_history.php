<?php
session_start();
include("connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'tenant') {
    header("Location: login.php");
    exit();
}
$tenant_id = isset($_SESSION['tenant_id']) ? $_SESSION['tenant_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติบิลค่าใช้จ่าย - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- เรียกใช้ Sidebar เมนูด้านข้าง -->
    <?php include("sidebar.php"); ?>

    <div class="main-content">
        <div class="container py-4">
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-receipt"></i> ประวัติบิลค่าใช้จ่าย</h3>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสบิล</th>
                                    <th>วันที่ออกบิล</th>
                                    <th>ครบกำหนด</th>
                                    <th>ค่าห้องพัก</th>
                                    <th>ค่าไฟฟ้า</th>
                                    <th>ค่าน้ำประปา</th>
                                    <th>ค่าปรับ</th>
                                    <th>ยอดรวมสุทธิ</th>
                                    <th>สถานะ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // 1. หาห้องที่เช่าอยู่ปัจจุบัน
                                $room_sql = "SELECT room_id FROM CONTRACT WHERE tenant_id = '$tenant_id' AND contract_status = 'active' LIMIT 1";
                                $room_res = mysqli_query($conn, $room_sql);
                                
                                if($room_row = mysqli_fetch_assoc($room_res)) {
                                    $room_id = $room_row['room_id'];
                                    
                                    // 2. ดึงบิลของห้องนี้ พร้อมดึงข้อมูลจากตาราง UTILITY_RECORD มาแสดงด้วย
                                    $bill_sql = "SELECT b.*, u.electric_meter FROM BILL b 
                                                 JOIN UTILITY_RECORD u ON b.record_id = u.record_id 
                                                 WHERE u.room_id = '$room_id' ORDER BY b.bill_id DESC";
                                    $bill_res = mysqli_query($conn, $bill_sql);
                                    
                                    if(mysqli_num_rows($bill_res) > 0) {
                                        while($bill = mysqli_fetch_assoc($bill_res)) {
                                            $status_badge = $bill['bill_status'] == 'paid' ? '<span class="badge bg-success">ชำระแล้ว</span>' : '<span class="badge bg-danger">ค้างชำระ</span>';
                                            $action_btn = $bill['bill_status'] == 'pending' ? "<a href='payment.php' class='btn btn-sm btn-primary rounded-pill px-3'>ชำระเงิน</a>" : "<span class='text-muted'>-</span>";
                                            
                                            echo "<tr>
                                                    <td class='fw-bold text-primary'>{$bill['bill_id']}</td>
                                                    <td>".date('d/m/Y', strtotime($bill['bill_date']))."</td>
                                                    <td>".date('d/m/Y', strtotime($bill['due_date']))."</td>
                                                    <td>฿".number_format($bill['room_rent'], 2)."</td>
                                                    <td>฿".number_format($bill['electric_total'], 2)."</td>
                                                    <td>฿".number_format($bill['water_total'], 2)."</td>
                                                    <td class='text-danger'>฿".number_format($bill['penalty_total'], 2)."</td>
                                                    <td class='fw-bold text-dark'>฿".number_format($bill['net_total'], 2)."</td>
                                                    <td>{$status_badge}</td>
                                                    <td class='text-center'>{$action_btn}</td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='10' class='text-center text-muted py-4'>ยังไม่มีประวัติบิลค่าใช้จ่าย</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center text-muted py-4'>ไม่พบข้อมูลสัญญาเช่า</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>