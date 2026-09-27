<?php
session_start();
include("connect.php");

// ตรวจสอบสิทธิ์ Admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); 
    exit();
}

// จัดการเมื่อแอดมินกดปุ่ม "บันทึก" ใน Modal
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_maintenance'])) {
    $req_id = mysqli_real_escape_string($conn, $_POST['request_id']);
    $status = mysqli_real_escape_string($conn, $_POST['repair_status']);
    $cost = mysqli_real_escape_string($conn, $_POST['spare_part_cost']);
    if($cost == '') $cost = 0; // ป้องกันค่าว่าง
    
    $update_sql = "UPDATE MAINTENANCE_REQUEST 
                   SET repair_status = '$status', spare_part_cost = '$cost' 
                   WHERE request_id = '$req_id'";
                   
    if(mysqli_query($conn, $update_sql)) {
        echo "<script>alert('อัปเดตสถานะการแจ้งซ่อมสำเร็จ'); window.location='admin_maintenance.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการแจ้งซ่อม - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- เรียกใช้ Sidebar -->
    <?php include("sidebar.php"); ?>

    <div class="main-content">
        <div class="container py-4">
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-tools"></i> จัดการแจ้งซ่อม</h3>
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4">รหัสซ่อม</th>
                                    <th>ห้องพัก</th>
                                    <th>ผู้แจ้ง</th>
                                    <th>วันที่แจ้ง</th>
                                    <th>ค่าอะไหล่</th>
                                    <th>สถานะ</th>
                                    <th class="text-center pe-4">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // ดึงข้อมูลแจ้งซ่อม โยงกับผู้เช่าและห้องพัก
                                $sql = "SELECT m.*, t.tenant_fname, t.tenant_lname, c.room_id 
                                        FROM MAINTENANCE_REQUEST m 
                                        LEFT JOIN TENANT t ON m.tenant_id = t.tenant_id 
                                        LEFT JOIN CONTRACT c ON t.tenant_id = c.tenant_id AND c.contract_status = 'active'
                                        ORDER BY m.request_id DESC";
                                $res = mysqli_query($conn, $sql);
                                
                                if($res && mysqli_num_rows($res) > 0) {
                                    while($row = mysqli_fetch_assoc($res)) {
                                        // จัดการสีของ Badge สถานะ
                                        $badge = 'bg-secondary';
                                        if($row['repair_status'] == 'รอคิวช่าง') $badge = 'bg-warning text-dark';
                                        if($row['repair_status'] == 'กำลังดำเนินการ') $badge = 'bg-info text-dark';
                                        if($row['repair_status'] == 'เสร็จสิ้น') $badge = 'bg-success';
                                        
                                        $room_show = !empty($row['room_id']) ? $row['room_id'] : '-';
                                        $name_show = $row['tenant_fname'] . ' ' . $row['tenant_lname'];
                                        $cost_show = number_format($row['spare_part_cost'], 2);
                                        
                                        echo "<tr>
                                                <td class='ps-4 fw-bold text-secondary'>#{$row['request_id']}</td>
                                                <td class='fw-bold text-primary'>{$room_show}</td>
                                                <td>{$name_show}</td>
                                                <td>".date('d/m/Y', strtotime($row['request_date']))."</td>
                                                <td class='text-danger'>฿{$cost_show}</td>
                                                <td><span class='badge {$badge}'>{$row['repair_status']}</span></td>
                                                <td class='text-center pe-4'>
                                                    <button type='button' class='btn btn-primary btn-sm rounded-pill px-3' data-bs-toggle='modal' data-bs-target='#modal-{$row['request_id']}'>อัปเดต</button>
                                                </td>
                                              </tr>";
                                        ?>
                                        
                                        <!-- Modal สำหรับอัปเดตแต่ละรายการ -->
                                        <div class="modal fade" id="modal-<?php echo $row['request_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content border-0 shadow">
                                                    <form method="POST">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title fw-bold">อัปเดตการแจ้งซ่อม #<?php echo $row['request_id']; ?></h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            
                                                            <!-- ส่วนแสดงรายละเอียดที่ผู้เช่าแจ้งมา -->
                                                            <div class="bg-light p-3 rounded mb-4 border">
                                                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">ข้อมูลการแจ้งซ่อมจากผู้เช่า</h6>
                                                                <div class="mb-2">
                                                                    <span class="text-muted fw-bold">รายละเอียดปัญหา:</span>
                                                                    <p class="mb-2 text-dark"><?php echo nl2br($row['description']); ?></p>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <span class="text-muted fw-bold">ช่วงเวลาที่สะดวก:</span>
                                                                    <span class="text-dark"><?php echo $row['preferred_time']; ?></span>
                                                                </div>
                                                                
                                                                <?php if(!empty($row['issue_image'])): ?>
                                                                <div class="mt-3">
                                                                    <span class="text-muted fw-bold d-block mb-2">รูปภาพประกอบ:</span>
                                                                    <a href="uploads/<?php echo $row['issue_image']; ?>" target="_blank">
                                                                        <img src="uploads/<?php echo $row['issue_image']; ?>" alt="Issue Image" class="img-fluid rounded border shadow-sm" style="max-height: 180px;">
                                                                    </a>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- ส่วนฟอร์มสำหรับแอดมินอัปเดต -->
                                                            <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">สถานะการซ่อม</label>
                                                                <select name="repair_status" class="form-select">
                                                                    <option value="รอดำเนินการ" <?php if($row['repair_status'] == 'รอดำเนินการ') echo 'selected'; ?>>รอดำเนินการ</option>
                                                                    <option value="รอคิวช่าง" <?php if($row['repair_status'] == 'รอคิวช่าง') echo 'selected'; ?>>รอคิวช่าง</option>
                                                                    <option value="กำลังดำเนินการ" <?php if($row['repair_status'] == 'กำลังดำเนินการ') echo 'selected'; ?>>กำลังดำเนินการ</option>
                                                                    <option value="เสร็จสิ้น" <?php if($row['repair_status'] == 'เสร็จสิ้น') echo 'selected'; ?>>เสร็จสิ้น</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label fw-bold">ค่าอะไหล่ / ค่าใช้จ่าย (บาท)</label>
                                                                <input type="number" step="0.01" name="spare_part_cost" class="form-control" value="<?php echo $row['spare_part_cost']; ?>">
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                                                            <button type="submit" name="update_maintenance" class="btn btn-primary rounded-pill px-4 fw-bold">บันทึกข้อมูล</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- จบ Modal -->

                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-4 text-muted'>ไม่มีข้อมูลการแจ้งซ่อม</td></tr>";
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