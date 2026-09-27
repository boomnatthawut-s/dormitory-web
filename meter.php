<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
require_once 'connect.php';

if (isset($_POST['save_bill'])) { 
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
    $prev_meter = (float)$_POST['prev_meter'];
    $electric_meter = (float)$_POST['electric_meter'];
    $electric_rate = (float)$_POST['electric_rate'];
    $water_total = (float)$_POST['water_total'];
    $water_rate = 18.00;
    $room_rent = (float)$_POST['room_rent'];
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $bill_date = date('Y-m-d');

    $units = $electric_meter - $prev_meter;
    if ($units < 0) { $units = 0; }
    
    $electric_total = $units * $electric_rate;
    $net_total = $room_rent + $electric_total + $water_total;

    $res_rec = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(record_id, 4) AS UNSIGNED)) AS max_id FROM UTILITY_RECORD");
    $row_rec = mysqli_fetch_assoc($res_rec);
    $next_rec_num = $row_rec['max_id'] ? $row_rec['max_id'] + 1 : 1;
    $record_id = 'REC' . str_pad($next_rec_num, 2, '0', STR_PAD_LEFT);

    $res_bill = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(bill_id, 4) AS UNSIGNED)) AS max_id FROM BILL");
    $row_bill = mysqli_fetch_assoc($res_bill);
    $next_bill_num = $row_bill['max_id'] ? $row_bill['max_id'] + 1 : 1;
    $bill_id = 'BIL' . str_pad($next_bill_num, 2, '0', STR_PAD_LEFT);

    mysqli_begin_transaction($conn);
    try {
        $sql_utility = "INSERT INTO UTILITY_RECORD (record_id, electric_meter, record_date, electric_rate, water_rate, room_id) 
                        VALUES ('$record_id', $electric_meter, '$bill_date', $electric_rate, $water_rate, '$room_id')";
        mysqli_query($conn, $sql_utility);

        $sql_bill = "INSERT INTO BILL (bill_id, bill_date, due_date, room_rent, electric_total, water_total, penalty_total, net_total, bill_status, record_id) 
                     VALUES ('$bill_id', '$bill_date', '$due_date', $room_rent, $electric_total, $water_total, 0, $net_total, 'pending', '$record_id')";
        mysqli_query($conn, $sql_bill);

        mysqli_commit($conn);
        echo "<script>alert('✅ ออกบิลสำเร็จ'); window.location.href = window.location.href;</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('❌ เกิดข้อผิดพลาด');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>คีย์ค่าไฟและออกบิล - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-2">
            <h3 class="mb-4 fw-bold text-primary"><i class="bi bi-lightning-charge"></i> คีย์ค่าไฟและออกบิลประจำเดือน</h3>

            <div class="table-responsive bg-white rounded-4 shadow-sm p-3">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">ห้องพัก</th>
                            <th>ผู้เช่า</th>
                            <th>ค่าเช่ารายเดือน</th>
                            <th>เลขมิเตอร์ล่าสุด</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_rooms = "SELECT CONTRACT.room_id, ROOM.monthly_rent, TENANT.tenant_fname, TENANT.tenant_lname,
                                             (SELECT UTILITY_RECORD.electric_meter 
                                              FROM UTILITY_RECORD 
                                              WHERE UTILITY_RECORD.room_id = CONTRACT.room_id 
                                              ORDER BY UTILITY_RECORD.record_id DESC LIMIT 1) AS previous_meter
                                      FROM CONTRACT
                                      JOIN ROOM ON CONTRACT.room_id = ROOM.room_id
                                      JOIN TENANT ON CONTRACT.tenant_id = TENANT.tenant_id
                                      WHERE CONTRACT.contract_status = 'active'
                                      ORDER BY CONTRACT.room_id ASC";
                        $result_rooms = mysqli_query($conn, $sql_rooms);
                        if (mysqli_num_rows($result_rooms) > 0) {
                            while ($row = mysqli_fetch_assoc($result_rooms)) {
                                $prev_meter = !empty($row['previous_meter']) ? $row['previous_meter'] : '0';
                                echo "<tr>
                                        <td class='text-center fw-bold text-primary'>{$row['room_id']}</td>
                                        <td>{$row['tenant_fname']} {$row['tenant_lname']}</td>
                                        <td>฿" . number_format($row['monthly_rent'], 2) . "</td>
                                        <td>{$prev_meter} หน่วย</td>
                                        <td><button class='btn btn-primary btn-sm rounded-pill px-3' data-bs-toggle='modal' data-bs-target='#billModal{$row['room_id']}'>➕ สร้างบิล</button></td>
                                      </tr>";
                                ?>
                                <div class="modal fade" id="billModal<?php echo $row['room_id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content rounded-4">
                                            <div class="modal-header bg-primary text-white rounded-top-4">
                                                <h5 class="modal-title">ออกบิลห้อง <?php echo $row['room_id']; ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="room_id" value="<?php echo $row['room_id']; ?>">
                                                    <input type="hidden" name="room_rent" value="<?php echo $row['monthly_rent']; ?>">
                                                    <input type="hidden" name="prev_meter" value="<?php echo $prev_meter; ?>">
                                                    
                                                    <div class="alert alert-info py-2 mb-3">
                                                        <strong>มิเตอร์เดิม:</strong> <?php echo $prev_meter; ?> หน่วย<br>
                                                        <strong>ค่าเช่าห้อง:</strong> ฿<?php echo number_format($row['monthly_rent'], 2); ?>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label text-primary fw-bold">เลขมิเตอร์ไฟใหม่ (จดล่าสุด)</label>
                                                        <input type="number" step="0.01" class="form-control" name="electric_meter" placeholder="ระบุเลขมิเตอร์..." required>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">อัตราค่าไฟ (บาท/หน่วย)</label>
                                                            <input type="number" step="0.01" class="form-control" name="electric_rate" value="8" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">ค่าน้ำรวม (เหมาจ่าย)</label>
                                                            <input type="number" step="0.01" class="form-control" name="water_total" value="120" required>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">กำหนดวันชำระเงิน</label>
                                                        <input type="date" class="form-control" name="due_date" value="<?php echo date('Y-m-05', strtotime('+1 month')); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                                                    <button type="submit" name="save_bill" class="btn btn-primary rounded-pill px-4">บันทึกและออกบิล</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4 text-muted'>ไม่มีห้องที่มีสัญญาเช่าอยู่ในขณะนี้</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>