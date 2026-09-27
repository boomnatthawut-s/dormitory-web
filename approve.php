<?php 
session_start();
include("connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("Location: login.php"); 
    exit(); 
}

// 1. จัดการเมื่อแอดมินกด "อนุมัติ"
if (isset($_GET['approve_id'])) {
    $t_id = mysqli_real_escape_string($conn, $_GET['approve_id']);
    
    mysqli_begin_transaction($conn);
    try {
        // อัปเดตสถานะผู้เช่าเป็น approved
        mysqli_query($conn, "UPDATE TENANT SET tenant_status = 'approved' WHERE tenant_id = '$t_id'");
        
        // อัปเดตสถานะสัญญาเช่าเป็น active
        mysqli_query($conn, "UPDATE CONTRACT SET contract_status = 'active' WHERE tenant_id = '$t_id'");
        
        // อัปเดตสถานะห้องพักเป็น 'ไม่ว่าง' (หรือมีผู้เช่า)
        $room_res = mysqli_query($conn, "SELECT room_id FROM CONTRACT WHERE tenant_id = '$t_id' LIMIT 1");
        if($room_row = mysqli_fetch_assoc($room_res)) {
            $r_id = $room_row['room_id'];
            mysqli_query($conn, "UPDATE ROOM SET room_status = 'ไม่ว่าง' WHERE room_id = '$r_id'");
        }

        mysqli_commit($conn);
        echo "<script>alert('✅ อนุมัติผู้เช่าเรียบร้อยแล้ว'); window.location='approve.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('❌ เกิดข้อผิดพลาดในการอนุมัติ'); window.location='approve.php';</script>";
    }
}

// 2. จัดการเมื่อแอดมินกด "ไม่อนุมัติ" (ปฏิเสธและลบข้อมูลที่สมัครค้างไว้ออก เพื่อคืนห้องว่าง)
if (isset($_GET['reject_id'])) {
    $t_id = mysqli_real_escape_string($conn, $_GET['reject_id']);
    
    mysqli_begin_transaction($conn);
    try {
        // ลบสัญญาเช่าที่ค้าง (pending) ของผู้เช่าคนนี้ก่อน
        mysqli_query($conn, "DELETE FROM CONTRACT WHERE tenant_id = '$t_id' AND contract_status = 'pending'");
        
        // ลบข้อมูลผู้เช่าที่สมัครเข้ามา
        mysqli_query($conn, "DELETE FROM TENANT WHERE tenant_id = '$t_id' AND tenant_status = 'pending'");

        mysqli_commit($conn);
        echo "<script>alert('❌ ปฏิเสธและยกเลิกคำขอเรียบร้อยแล้ว'); window.location='approve.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('❌ เกิดข้อผิดพลาดในการปฏิเสธคำขอ'); window.location='approve.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>อนุมัติผู้เช่าใหม่ - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- เรียกใช้ Sidebar เมนูด้านข้าง -->
    <?php include("sidebar.php"); ?>

    <div class="main-content">
        <div class="container py-4">
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-person-check"></i> รายชื่อรออนุมัติ</h3>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>รหัสผู้เช่า</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>เบอร์โทรศัพท์</th>
                                <th>ห้องที่เลือก</th>
                                <th>สถานะ</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // ดึงข้อมูลผู้เช่าที่สถานะเป็น pending พร้อมห้องที่จองไว้
                            $sql = "SELECT t.*, c.room_id 
                                    FROM TENANT t 
                                    LEFT JOIN CONTRACT c ON t.tenant_id = c.tenant_id 
                                    WHERE t.tenant_status = 'pending'";
                            $res = mysqli_query($conn, $sql);
                            
                            if(mysqli_num_rows($res) > 0) {
                                while($row = mysqli_fetch_assoc($res)) {
                                    $room_display = !empty($row['room_id']) ? "ห้อง " . $row['room_id'] : '-';
                                    echo "<tr>
                                            <td class='fw-bold text-primary'>{$row['tenant_id']}</td>
                                            <td>{$row['tenant_fname']} {$row['tenant_lname']}</td>
                                            <td>{$row['tenant_phone']}</td>
                                            <td><span class='badge bg-light text-dark border'>{$room_display}</span></td>
                                            <td><span class='badge bg-warning text-dark'>รอยืนยัน</span></td>
                                            <td class='text-center'>
                                                <a href='approve.php?approve_id={$row['tenant_id']}' class='btn btn-success btn-sm rounded-pill px-3 me-1' onclick=\"return confirm('ยืนยันการอนุมัติผู้เช่ารายนี้?');\"><i class='bi bi-check-lg'></i> อนุมัติ</a>
                                                <a href='approve.php?reject_id={$row['tenant_id']}' class='btn btn-danger btn-sm rounded-pill px-3' onclick=\"return confirm('คุณต้องการไม่อนุมัติและลบคำขอนี้ใช่หรือไม่?');\"><i class='bi bi-x-lg'></i> ไม่อนุมัติ</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center text-muted py-4'>ไม่มีรายการรออนุมัติในขณะนี้</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>