<?php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['verify_action'])) {
    $payment_id = mysqli_real_escape_string($conn, $_POST['payment_id']);
    $bill_id = mysqli_real_escape_string($conn, $_POST['bill_id']);
    $action = $_POST['verify_action'];

    if ($action == 'approve') {
        mysqli_begin_transaction($conn);
        try {
            mysqli_query($conn, "UPDATE PAYMENT SET verify_status = 'verified' WHERE payment_id = '$payment_id'");
            if (!empty($bill_id)) {
                mysqli_query($conn, "UPDATE BILL SET bill_status = 'paid' WHERE bill_id = '$bill_id'");
            }
            mysqli_commit($conn);
            echo "<script>alert('✅ ยืนยันการชำระเงินเรียบร้อยแล้ว'); window.location.href='admin_payment.php';</script>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('❌ เกิดข้อผิดพลาด');</script>";
        }
    } elseif ($action == 'reject') {
        mysqli_query($conn, "UPDATE PAYMENT SET verify_status = 'rejected' WHERE payment_id = '$payment_id'");
        echo "<script>alert('ปฏิเสธการชำระเงินเรียบร้อยแล้ว'); window.location.href='admin_payment.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบการชำระเงิน - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .slip-image { max-width: 100%; max-height: 60vh; object-fit: contain; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-2">
            <h3 class="mb-4 fw-bold text-primary"><i class="bi bi-credit-card"></i> ตรวจสอบการชำระเงิน</h3>

            <div class="table-responsive bg-white rounded-4 shadow-sm p-3">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>รหัสการโอน</th>
                            <th>ห้องพัก</th>
                            <th>รหัสบิล</th>
                            <th>วันที่แจ้งโอน</th>
                            <th>ยอดเงินที่โอน</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT p.*, b.net_total, u.room_id 
                                FROM PAYMENT p
                                LEFT JOIN BILL b ON p.bill_id = b.bill_id
                                LEFT JOIN UTILITY_RECORD u ON b.record_id = u.record_id
                                WHERE p.verify_status = 'pending'
                                ORDER BY p.payment_id DESC";
                        $result = mysqli_query($conn, $sql);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $room_display = !empty($row['room_id']) ? $row['room_id'] : '-';
                                $bill_display = !empty($row['bill_id']) ? $row['bill_id'] : '-';
                                $net_total = isset($row['net_total']) ? $row['net_total'] : $row['total_paid'];

                                echo "<tr>
                                        <td>#{$row['payment_id']}</td>
                                        <td class='fw-bold text-primary'>{$room_display}</td>
                                        <td>{$bill_display}</td>
                                        <td>" . date("d/m/Y", strtotime($row['payment_date'])) . "</td>
                                        <td>฿" . number_format($row['total_paid'], 2) . "</td>
                                        <td><span class='badge bg-warning text-dark'>รอตรวจสอบ</span></td>
                                        <td><button class='btn btn-primary btn-sm rounded-pill px-3' data-bs-toggle='modal' data-bs-target='#verifyModal{$row['payment_id']}'>🧾 ตรวจสอบ</button></td>
                                      </tr>";
                                ?>
                                <div class="modal fade" id="verifyModal<?php echo $row['payment_id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg text-center">
                                        <div class="modal-content rounded-4">
                                            <div class="modal-header bg-primary text-white rounded-top-4">
                                                <h5 class="modal-title">ตรวจสอบสลิป - ห้อง <?php echo $room_display; ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body bg-light p-4">
                                                <div class="mb-3">
                                                    <p class="fs-5 mb-1">ยอดเรียกเก็บ: <strong>฿<?php echo number_format($net_total, 2); ?></strong></p>
                                                    <p class="fs-5 text-success">ยอดที่โอนมา: <strong>฿<?php echo number_format($row['total_paid'], 2); ?></strong></p>
                                                </div>
                                                <?php if(!empty($row['slip_image'])): ?>
                                                    <img src="uploads/<?php echo $row['slip_image']; ?>" alt="Slip" class="slip-image mb-3 border">
                                                <?php else: ?>
                                                    <p class="text-danger">ไม่พบไฟล์รูปภาพสลิป</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer justify-content-between p-3">
                                                <form action="" method="POST" class="w-100 d-flex justify-content-between">
                                                    <input type="hidden" name="payment_id" value="<?php echo $row['payment_id']; ?>">
                                                    <input type="hidden" name="bill_id" value="<?php echo $bill_display; ?>">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
                                                    <div>
                                                        <button type="submit" name="verify_action" value="reject" class="btn btn-danger rounded-pill px-4 me-2" onclick="return confirm('ปฏิเสธสลิปนี้?');">❌ ปฏิเสธ</button>
                                                        <button type="submit" name="verify_action" value="approve" class="btn btn-success rounded-pill px-4" onclick="return confirm('อนุมัติยอดโอนนี้?');">✅ อนุมัติ</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-4 text-muted'>ไม่มีรายการแจ้งชำระเงินที่รอตรวจสอบ</td></tr>";
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