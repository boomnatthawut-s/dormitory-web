<?php
session_start();

// ตรวจสอบสิทธิ์การเข้าใช้งาน (ต้องเป็น Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติและจัดการสัญญา - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .nav-pills .nav-link { border-radius: 20px; margin-right: 10px; color: #555; background-color: #fff; border: 1px solid #ddd; }
        .nav-pills .nav-link.active { background-color: #0d6efd; color: white; border-color: #0d6efd; }
        .table-responsive { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-light">

    <!-- เรียกใช้ Sidebar ด้านข้าง -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-2">
            <h3 class="mb-4 fw-bold text-primary"><i class="bi bi-file-earmark-text"></i> ประวัติและจัดการสัญญาเช่า</h3>

            <!-- ปุ่มเมนู Tabs -->
            <ul class="nav nav-pills mb-4" id="historyTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="active-tab" data-bs-toggle="pill" data-bs-target="#active_contracts" type="button" role="tab" aria-controls="active_contracts" aria-selected="true">
                        📜 สัญญาปัจจุบัน & ยกเลิก
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="old-tab" data-bs-toggle="pill" data-bs-target="#old_contracts" type="button" role="tab" aria-controls="old_contracts" aria-selected="false">
                        📁 ประวัติสัญญาเก่า
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="billing-tab" data-bs-toggle="pill" data-bs-target="#billing_history" type="button" role="tab" aria-controls="billing_history" aria-selected="false">
                        🧾 ประวัติบิล & การชำระเงิน
                    </button>
                </li>
            </ul>

            <!-- เนื้อหาของ Tabs ทั้งหมด -->
            <div class="tab-content" id="historyTabsContent">

                <!-- ========================================== -->
                <!-- TAB 1: สัญญาปัจจุบัน -->
                <!-- ========================================== -->
                <div class="tab-pane fade show active" id="active_contracts" role="tabpanel" aria-labelledby="active-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ห้องพัก</th>
                                    <th>รหัสสัญญา</th>
                                    <th>ชื่อผู้เช่า</th>
                                    <th>เบอร์โทร</th>
                                    <th>LINE ID</th>
                                    <th>วันเริ่มสัญญา</th>
                                    <th>สิ้นสุดสัญญา</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_active = "SELECT c.*, r.room_type, r.monthly_rent, 
                                                      t.tenant_fname, t.tenant_lname, t.tenant_phone, t.tenant_lineid,
                                                      p.parent_fname, p.parent_lname, p.relation_tenant, p.parent_phone
                                               FROM CONTRACT c 
                                               JOIN ROOM r ON c.room_id = r.room_id
                                               JOIN TENANT t ON c.tenant_id = t.tenant_id 
                                               LEFT JOIN PARENT p ON t.tenant_id = p.tenant_id
                                               WHERE c.contract_status = 'active' 
                                               ORDER BY c.room_id ASC";
                                               
                                $result_active = mysqli_query($conn, $sql_active);

                                if (mysqli_num_rows($result_active) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_active)) {
                                        $start_date = date("d/m/Y", strtotime($row['start_date']));
                                        $end_date = date("d/m/Y", strtotime($row['end_date']));
                                        $line_id = !empty($row['tenant_lineid']) ? $row['tenant_lineid'] : '<span class="text-muted">-</span>';

                                        echo "<tr>";
                                        echo "<td class='fw-bold text-primary'>" . $row['room_id'] . "</td>";
                                        echo "<td>" . $row['contract_id'] . "</td>";
                                        echo "<td>" . $row['tenant_fname'] . " " . $row['tenant_lname'] . "</td>";
                                        echo "<td><a href='tel:".$row['tenant_phone']."' class='text-decoration-none'>".$row['tenant_phone']."</a></td>";
                                        echo "<td>" . $line_id . "</td>";
                                        echo "<td>" . $start_date . "</td>";
                                        echo "<td>" . $end_date . "</td>";
                                        echo "<td>
                                                <button type='button' class='btn btn-primary btn-sm rounded-pill px-3' data-bs-toggle='modal' data-bs-target='#detailModal" . $row['room_id'] . "'>
                                                    📄 รายละเอียด
                                                </button>
                                              </td>";
                                        echo "</tr>";
                                        ?>
                                        <!-- Modal รายละเอียด -->
                                        <div class="modal fade" id="detailModal<?php echo $row['room_id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content rounded-4">
                                                    <div class="modal-header bg-primary text-white rounded-top-4">
                                                        <h5 class="modal-title fw-bold">รายละเอียดห้อง <?php echo $row['room_id']; ?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <h6 class="fw-bold text-primary border-bottom pb-2">🏠 ข้อมูลห้องพักและสัญญา</h6>
                                                        <p class="mb-1"><strong>ประเภทห้อง:</strong> <?php echo $row['room_type']; ?></p>
                                                        <p class="mb-1"><strong>ค่าเช่า:</strong> ฿<?php echo number_format($row['monthly_rent'], 2); ?> / เดือน</p>
                                                        <p class="mb-1"><strong>รหัสสัญญา:</strong> <?php echo $row['contract_id']; ?></p>
                                                        <p class="mb-3"><strong>ระยะเวลาสัญญา:</strong> <?php echo $start_date . " ถึง " . $end_date; ?></p>

                                                        <h6 class="fw-bold text-primary border-bottom pb-2">👤 ผู้เช่า (Tenant)</h6>
                                                        <p class="mb-1"><strong>ชื่อ-สกุล:</strong> <?php echo $row['tenant_fname'] . " " . $row['tenant_lname']; ?></p>
                                                        <p class="mb-1"><strong>เบอร์โทร:</strong> <?php echo $row['tenant_phone']; ?></p>
                                                        <p class="mb-3"><strong>LINE ID:</strong> <span class="text-primary fw-bold"><?php echo !empty($row['tenant_lineid']) ? $row['tenant_lineid'] : '-'; ?></span></p>

                                                        <h6 class="fw-bold text-danger border-bottom pb-2">📞 ติดต่อฉุกเฉิน / ผู้ปกครอง</h6>
                                                        <?php if(!empty($row['parent_fname'])): ?>
                                                            <p class="mb-1"><strong>ชื่อ-สกุล:</strong> <?php echo $row['parent_fname'] . " " . $row['parent_lname']; ?></p>
                                                            <p class="mb-1"><strong>ความสัมพันธ์:</strong> <?php echo $row['relation_tenant']; ?></p>
                                                            <p class="mb-1"><strong>เบอร์โทร:</strong> <a href="tel:<?php echo $row['parent_phone']; ?>" class="text-danger fw-bold"><?php echo $row['parent_phone']; ?></a></p>
                                                        <?php else: ?>
                                                            <p class="text-muted fst-italic">ไม่มีข้อมูลผู้ติดต่อฉุกเฉิน</p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center py-4 text-muted'>ไม่มีสัญญาที่กำลังใช้งาน</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TAB 2: ประวัติสัญญาเก่า -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="old_contracts" role="tabpanel" aria-labelledby="old-tab">
                    <div class="table-responsive">
                        <h5 class="mb-3 text-secondary">ประวัติผู้เช่ารายเก่า (Terminated)</h5>
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ห้องพัก</th>
                                    <th>อดีตผู้เช่า</th>
                                    <th>เบอร์โทร</th>
                                    <th>วันที่เริ่มเช่า</th>
                                    <th>วันที่ย้ายออก</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_old = "SELECT c.*, t.tenant_fname, t.tenant_lname, t.tenant_phone 
                                            FROM CONTRACT c 
                                            JOIN TENANT t ON c.tenant_id = t.tenant_id 
                                            WHERE c.contract_status = 'terminated' 
                                            ORDER BY c.end_date DESC";
                                            
                                $result_old = mysqli_query($conn, $sql_old);

                                if (mysqli_num_rows($result_old) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_old)) {
                                        echo "<tr>";
                                        echo "<td class='fw-bold text-secondary'>" . $row['room_id'] . "</td>";
                                        echo "<td>" . $row['tenant_fname'] . " " . $row['tenant_lname'] . "</td>";
                                        echo "<td>" . $row['tenant_phone'] . "</td>";
                                        echo "<td>" . date("d/m/Y", strtotime($row['start_date'])) . "</td>";
                                        echo "<td>" . date("d/m/Y", strtotime($row['end_date'])) . "</td>";
                                        echo "<td><span class='badge bg-secondary'>ยกเลิกสัญญาแล้ว</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>ไม่มีประวัติสัญญาเก่า</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TAB 3: ประวัติบิล & การชำระเงิน -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="billing_history" role="tabpanel" aria-labelledby="billing-tab">
                    <div class="table-responsive">
                        <h5 class="mb-3 text-secondary">ประวัติการออกบิลทั้งหมด</h5>
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสบิล</th>
                                    <th>ห้องพัก</th>
                                    <th>วันที่ออกบิล</th>
                                    <th>หน่วยไฟ (เก่า-ใหม่)</th>
                                    <th>ยอดสุทธิ</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_bill = "SELECT b.*, u.room_id, u.electric_meter AS current_unit,
                                                   (SELECT u2.electric_meter 
                                                    FROM UTILITY_RECORD u2 
                                                    WHERE u2.room_id = u.room_id AND u2.record_id < u.record_id 
                                                    ORDER BY u2.record_id DESC LIMIT 1) AS prev_unit
                                            FROM BILL b 
                                            JOIN UTILITY_RECORD u ON b.record_id = u.record_id 
                                            ORDER BY b.bill_date DESC, b.bill_id DESC";
                                            
                                $result_bill = mysqli_query($conn, $sql_bill);

                                if (mysqli_num_rows($result_bill) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_bill)) {
                                        $prev = !empty($row['prev_unit']) ? $row['prev_unit'] : '0';
                                        $curr = $row['current_unit'];
                                        
                                        $status_badge = ($row['bill_status'] == 'paid') ? '<span class="badge bg-success">ชำระแล้ว</span>' : '<span class="badge bg-danger">ค้างชำระ</span>';

                                        echo "<tr>";
                                        echo "<td>" . $row['bill_id'] . "</td>";
                                        echo "<td class='fw-bold text-primary'>" . $row['room_id'] . "</td>";
                                        echo "<td>" . date("d/m/Y", strtotime($row['bill_date'])) . "</td>";
                                        echo "<td>" . $prev . " ➔ " . $curr . "</td>";
                                        echo "<td>฿" . number_format($row['net_total'], 2) . "</td>";
                                        echo "<td>" . $status_badge . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>ไม่มีประวัติบิล</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> <!-- ปิด tab-content -->
        </div> <!-- ปิด container-fluid -->
    </div> <!-- ปิด main-content -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>