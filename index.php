<?php
session_start();
include("connect.php");

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>หน้าหลัก - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .room-scroll-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 15px;
            scroll-snap-type: x mandatory;
        }
        .room-card-wrapper {
            flex: 0 0 auto;
            width: 220px;
            scroll-snap-align: start;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .room-card-wrapper:hover {
            transform: translateY(-5px);
        }
        .room-scroll-row::-webkit-scrollbar { height: 8px; }
        .room-scroll-row::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .room-scroll-row::-webkit-scrollbar-thumb { background: #0d6efd; border-radius: 10px; }
        .clickable-card {
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .clickable-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>
<body class="bg-light">

    <!-- เรียกใช้ Sidebar ด้านข้าง -->
    <?php include("sidebar.php"); ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-2">

            <?php
            // =========================================================
            // 1. มุมมองของ ADMIN (ผู้ดูแลระบบ)
            // =========================================================
            if ($_SESSION['role'] == 'admin') {

                $sql_income = "SELECT SUM(net_total) as total_income, SUM(room_rent) as total_rent, 
                                  SUM(water_total) as total_water, SUM(electric_total) as total_electric, 
                                  SUM(penalty_total) as total_penalty 
                           FROM BILL WHERE bill_status = 'paid'";
                $res_income = mysqli_query($conn, $sql_income);
                $income = mysqli_fetch_assoc($res_income);

                $total_income = $income['total_income'] ?? 0;
                $rent = $income['total_rent'] ?? 0;
                $water = $income['total_water'] ?? 0;
                $electric = $income['total_electric'] ?? 0;
                $penalty = $income['total_penalty'] ?? 0;

                $sql_unpaid = "SELECT COUNT(bill_id) as unpaid_count, SUM(net_total) as unpaid_total 
                           FROM BILL WHERE bill_status = 'pending'";
                $res_unpaid = mysqli_query($conn, $sql_unpaid);
                $unpaid = mysqli_fetch_assoc($res_unpaid);
                $unpaid_count = $unpaid['unpaid_count'] ?? 0;
                $unpaid_total = $unpaid['unpaid_total'] ?? 0;

                $sql_repair_q = "SELECT COUNT(request_id) as repair_count 
                             FROM MAINTENANCE_REQUEST WHERE repair_status != 'เสร็จสิ้น'";
                $res_repair_q = mysqli_query($conn, $sql_repair_q);
                $repair_q = mysqli_fetch_assoc($res_repair_q);
                $repair_count = $repair_q['repair_count'] ?? 0;

                $sql_expense = "SELECT SUM(spare_part_cost) as total_expense FROM MAINTENANCE_REQUEST";
                $res_expense = mysqli_query($conn, $sql_expense);
                $expense = mysqli_fetch_assoc($res_expense);
                $maintenance_cost = $expense['total_expense'] ?? 0;

                $sql_pending_pay = "SELECT COUNT(payment_id) AS total_pending FROM PAYMENT WHERE verify_status = 'pending'";
                $res_pending_pay = mysqli_query($conn, $sql_pending_pay);
                $row_pending_pay = mysqli_fetch_assoc($res_pending_pay);
                $pending_payments = $row_pending_pay['total_pending'] ?? 0;

                $sql_monthly = "SELECT DATE_FORMAT(bill_date, '%b %Y') as month_name, SUM(net_total) as monthly_total 
                            FROM BILL WHERE bill_status = 'paid' 
                            GROUP BY DATE_FORMAT(bill_date, '%Y-%m'), month_name 
                            ORDER BY DATE_FORMAT(bill_date, '%Y-%m') ASC LIMIT 6";
                $res_monthly = mysqli_query($conn, $sql_monthly);
                $months = [];
                $monthly_incomes = [];
                while ($m_row = mysqli_fetch_assoc($res_monthly)) {
                    $months[] = $m_row['month_name'];
                    $monthly_incomes[] = $m_row['monthly_total'];
                }
            ?>
                <h3 class="fw-bold text-primary mb-4"><i class="bi bi-speedometer2"></i> แดชบอร์ดผู้ดูแลระบบ</h3>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <h6 class="mb-2"><i class="bi bi-wallet2"></i> รายได้รวม (รับแล้ว)</h6>
                                <h3 class="fw-bold mb-0">฿<?php echo number_format($total_income, 2); ?></h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-danger text-white border-0 shadow-sm rounded-4 h-100 clickable-card" data-bs-toggle="modal" data-bs-target="#unpaidBillsModal">
                            <div class="card-body">
                                <h6 class="mb-2"><i class="bi bi-receipt"></i> บิลค้างชำระ (<?php echo $unpaid_count; ?> รายการ)</h6>
                                <h3 class="fw-bold mb-0">฿<?php echo number_format($unpaid_total, 2); ?></h3>
                                <small class="text-white-50"><i class="bi bi-hand-index-thumb"></i> คลิกเพื่อดูรายละเอียด</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-warning text-dark border-0 shadow-sm rounded-4 h-100 clickable-card" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                            <div class="card-body">
                                <h6 class="mb-2"><i class="bi bi-tools"></i> คิวแจ้งซ่อมค้างอยู่</h6>
                                <h3 class="fw-bold mb-0"><?php echo $repair_count; ?> <span class="fs-6 fw-normal">รายการ</span></h3>
                                <small class="text-dark-50" style="opacity: 0.7;"><i class="bi bi-hand-index-thumb"></i> คลิกเพื่อดูรายละเอียด</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-secondary text-white border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <h6 class="mb-2"><i class="bi bi-cash-stack"></i> ค่าใช้จ่าย (ค่าซ่อม)</h6>
                                <h3 class="fw-bold mb-0">฿<?php echo number_format($maintenance_cost, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 rounded-4 text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-xs fw-bold text-uppercase mb-1">
                                        รอตรวจสอบการโอนเงิน
                                    </div>
                                    <div class="h3 mb-0 fw-bold">
                                        <?php echo $pending_payments; ?> <span class="fs-6">รายการ</span>
                                    </div>
                                </div>
                                <div>
                                    <h1 class="opacity-50 mb-0">💳</h1>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                                <a href="admin_payment.php" class="btn btn-light btn-sm w-100 fw-bold shadow-sm rounded-pill text-primary">
                                    ตรวจสอบสลิปทันที ➔
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4 text-center text-primary">สัดส่วนรายได้</h5>
                                <div style="height: 250px; display: flex; justify-content: center;">
                                    <canvas id="incomeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4 text-center text-primary">รายได้ 6 เดือนล่าสุด</h5>
                                <div style="height: 250px;">
                                    <canvas id="monthlyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="fw-bold text-primary mb-4 mt-5"><i class="bi bi-building"></i> สถานะห้องพัก</h3>

                <?php
                $zones = ['A', 'B', 'C'];
                foreach ($zones as $zone) {
                    $check_sql = "SELECT COUNT(*) as count FROM ROOM WHERE room_id LIKE '$zone%'";
                    $check_res = mysqli_query($conn, $check_sql);
                    $check_data = mysqli_fetch_assoc($check_res);

                    if ($check_data['count'] > 0) {
                        echo "<h5 class='fw-bold text-secondary mb-3'>โซน $zone</h5>";
                        echo "<div class='room-scroll-row mb-4'>";

                        $sql = "SELECT * FROM ROOM WHERE room_id LIKE '$zone%' ORDER BY room_id ASC";
                        $result = mysqli_query($conn, $sql);

                        while ($room = mysqli_fetch_assoc($result)) {
                            $bg = $room['room_status'] == 'ว่าง' ? 'bg-success text-white' : ($room['room_status'] == 'ไม่ว่าง' ? 'bg-danger text-white' : 'bg-warning text-dark');

                            echo "
                        <div class='room-card-wrapper' data-bs-toggle='modal' data-bs-target='#roomModal-{$room['room_id']}'>
                            <div class='card shadow-sm h-100 border-0 $bg' style='border-radius: 15px;'>
                                <div class='card-body text-center p-4'>
                                    <h2 class='fw-bold mb-1'>{$room['room_id']}</h2>
                                    <p class='small mb-2'>{$room['room_type']}</p>
                                    <span class='badge bg-light text-dark fs-6 px-3 py-1'>{$room['room_status']}</span>
                                </div>
                            </div>
                        </div>";

                            $tenant_info = "<p class='text-muted'>- ไม่มีผู้เช่า -</p>";
                            $sql_tenant = "SELECT t.tenant_fname, t.tenant_lname, t.tenant_phone 
                                       FROM CONTRACT c JOIN TENANT t ON c.tenant_id = t.tenant_id 
                                       WHERE c.room_id = '{$room['room_id']}' AND c.contract_status = 'active' LIMIT 1";
                            $res_tenant = mysqli_query($conn, $sql_tenant);
                            if ($t_row = mysqli_fetch_assoc($res_tenant)) {
                                $tenant_info = "<strong>ชื่อ:</strong> {$t_row['tenant_fname']} {$t_row['tenant_lname']} <br>
                                            <strong>เบอร์โทร:</strong> {$t_row['tenant_phone']}";
                            }

                            $assets_info = "<ul>";
                            $sql_assets = "SELECT asset_name, quantity FROM ROOM_ASSET WHERE room_id = '{$room['room_id']}'";
                            $res_assets = mysqli_query($conn, $sql_assets);
                            if (mysqli_num_rows($res_assets) > 0) {
                                while ($a_row = mysqli_fetch_assoc($res_assets)) {
                                    $assets_info .= "<li>{$a_row['asset_name']} (จำนวน {$a_row['quantity']})</li>";
                                }
                            } else {
                                $assets_info .= "<li>ไม่มีข้อมูลทรัพย์สิน</li>";
                            }
                            $assets_info .= "</ul>";

                            echo "
                        <div class='modal fade' id='roomModal-{$room['room_id']}' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-centered'>
                                <div class='modal-content rounded-4 border-0 shadow'>
                                    <div class='modal-header bg-primary text-white rounded-top-4'>
                                        <h5 class='modal-title fw-bold'>รายละเอียดห้อง {$room['room_id']}</h5>
                                        <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body p-4'>
                                        <h6 class='text-primary fw-bold mb-3'>👤 ข้อมูลผู้เช่าปัจจุบัน</h6>
                                        <div class='mb-4 p-3 bg-light rounded'>
                                            $tenant_info
                                        </div>
                                        <h6 class='text-primary fw-bold mb-3'>🛋️ ทรัพย์สินภายในห้อง</h6>
                                        <div class='p-3 bg-light rounded'>
                                            $assets_info
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>";
                        }
                        echo "</div>";
                    }
                }
                ?>

                <!-- Modal บิลค้างชำระ -->
                <div class="modal fade" id="unpaidBillsModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header bg-danger text-white rounded-top-4">
                                <h5 class="modal-title fw-bold"><i class="bi bi-receipt"></i> รายชื่อห้องที่ค้างชำระ</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ห้อง</th>
                                                <th>ผู้เช่า</th>
                                                <th>กำหนดชำระ</th>
                                                <th>ยอดรวม (บาท)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql_unpaid_list = "SELECT b.bill_id, b.due_date, b.net_total, u.room_id, t.tenant_fname, t.tenant_lname 
                                                            FROM BILL b 
                                                            JOIN UTILITY_RECORD u ON b.record_id = u.record_id 
                                                            LEFT JOIN CONTRACT c ON u.room_id = c.room_id AND c.contract_status = 'active'
                                                            LEFT JOIN TENANT t ON c.tenant_id = t.tenant_id
                                                            WHERE b.bill_status = 'pending'
                                                            ORDER BY b.due_date ASC";
                                            $res_unpaid_list = mysqli_query($conn, $sql_unpaid_list);
                                            if (mysqli_num_rows($res_unpaid_list) > 0) {
                                                while ($row_up = mysqli_fetch_assoc($res_unpaid_list)) {
                                                    $t_name = $row_up['tenant_fname'] ? $row_up['tenant_fname'] . ' ' . $row_up['tenant_lname'] : '-';
                                                    echo "<tr>
                                                        <td class='fw-bold text-danger'>{$row_up['room_id']}</td>
                                                        <td>{$t_name}</td>
                                                        <td>" . date('d/m/Y', strtotime($row_up['due_date'])) . "</td>
                                                        <td class='fw-bold'>฿" . number_format($row_up['net_total'], 2) . "</td>
                                                      </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center py-3 text-muted'>ไม่มีรายการค้างชำระ</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal คิวแจ้งซ่อม -->
                <div class="modal fade" id="maintenanceModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header bg-warning text-dark rounded-top-4">
                                <h5 class="modal-title fw-bold"><i class="bi bi-tools"></i> คิวแจ้งซ่อมที่ยังไม่เสร็จสิ้น</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ห้อง</th>
                                                <th>รายละเอียดปัญหา</th>
                                                <th>วันที่แจ้ง</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql_repair_list = "SELECT m.request_id, m.description, m.request_date, m.repair_status, c.room_id 
                                                            FROM MAINTENANCE_REQUEST m
                                                            JOIN TENANT t ON m.tenant_id = t.tenant_id
                                                            LEFT JOIN CONTRACT c ON t.tenant_id = c.tenant_id AND c.contract_status = 'active'
                                                            WHERE m.repair_status != 'เสร็จสิ้น'
                                                            ORDER BY m.request_date ASC";
                                            $res_repair_list = mysqli_query($conn, $sql_repair_list);
                                            if (mysqli_num_rows($res_repair_list) > 0) {
                                                while ($row_rp = mysqli_fetch_assoc($res_repair_list)) {
                                                    $r_room = $row_rp['room_id'] ? $row_rp['room_id'] : 'ไม่ระบุ';
                                                    $badge_color = 'bg-secondary';
                                                    if ($row_rp['repair_status'] == 'รอคิวช่าง') $badge_color = 'bg-warning text-dark';
                                                    if ($row_rp['repair_status'] == 'กำลังดำเนินการ') $badge_color = 'bg-info text-dark';

                                                    echo "<tr>
                                                        <td class='fw-bold text-primary'>{$r_room}</td>
                                                        <td>{$row_rp['description']}</td>
                                                        <td>" . date('d/m/Y', strtotime($row_rp['request_date'])) . "</td>
                                                        <td><span class='badge {$badge_color}'>{$row_rp['repair_status']}</span></td>
                                                      </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center py-3 text-muted'>ไม่มีคิวแจ้งซ่อมค้างอยู่</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var ctx1 = document.getElementById('incomeChart').getContext('2d');
                        new Chart(ctx1, {
                            type: 'doughnut',
                            data: {
                                labels: ['ค่าเช่าห้อง', 'ค่าน้ำ', 'ค่าไฟ', 'ค่าปรับ'],
                                datasets: [{
                                    data: [<?php echo "$rent, $water, $electric, $penalty"; ?>],
                                    backgroundColor: ['#0d6efd', '#0dcaf0', '#ffc107', '#6c757d'],
                                    borderWidth: 0
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
                        });

                        var ctx2 = document.getElementById('monthlyChart').getContext('2d');
                        new Chart(ctx2, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode($months); ?>,
                                datasets: [{
                                    label: 'รายได้รวม (บาท)',
                                    data: <?php echo json_encode($monthly_incomes); ?>,
                                    backgroundColor: '#0d6efd',
                                    borderRadius: 5
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                        });
                    });
                </script>

            <?php
                // =========================================================
                // 2. มุมมองของ TENANT (ผู้เช่า)
                // =========================================================
            } elseif ($_SESSION['role'] == 'tenant') {

                $tenant_id = isset($_SESSION['tenant_id']) ? $_SESSION['tenant_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['id']);

                $sql_room = "SELECT c.room_id, r.room_type, c.end_date 
                         FROM CONTRACT c JOIN ROOM r ON c.room_id = r.room_id 
                         WHERE c.tenant_id = '$tenant_id' AND c.contract_status = 'active' LIMIT 1";
                $res_room = mysqli_query($conn, $sql_room);
                $my_room = mysqli_fetch_assoc($res_room);
                $room_id = $my_room ? $my_room['room_id'] : 'กำลังรออนุมัติ';

                $sql_bill = "SELECT * FROM BILL b 
                         JOIN UTILITY_RECORD u ON b.record_id = u.record_id 
                         WHERE u.room_id = '$room_id' AND b.bill_status = 'pending' 
                         ORDER BY b.bill_id DESC LIMIT 1";
                $res_bill = mysqli_query($conn, $sql_bill);
                $unpaid_bill = mysqli_fetch_assoc($res_bill);

                $sql_repair = "SELECT * FROM MAINTENANCE_REQUEST 
                           WHERE tenant_id = '$tenant_id' 
                           ORDER BY request_id DESC LIMIT 1";
                $res_repair = mysqli_query($conn, $sql_repair);
                $latest_repair = mysqli_fetch_assoc($res_repair);
            ?>

                <div class="row mb-4">
                    <div class="col-12">
                        <h3 class="fw-bold text-primary">🏠 ยินดีต้อนรับ, <?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : 'ผู้เช่า'; ?></h3>
                        <p class="text-muted">ห้องพักของคุณ: <strong class="text-primary fs-5"><?php echo $room_id; ?></strong> (<?php echo isset($my_room['room_type']) ? $my_room['room_type'] : '-'; ?>)</p>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <?php if ($unpaid_bill) { ?>
                            <div class="card text-white h-100 shadow border-0 rounded-4" style="background: linear-gradient(135deg, #0d6efd, #0dcaf0);">
                                <div class="card-body p-4">
                                    <h5 class="mb-3"><i class="bi bi-exclamation-circle"></i> ยอดค้างชำระเดือนล่าสุด</h5>
                                    <h1 class="fw-bold mb-3">฿ <?php echo number_format($unpaid_bill['net_total'], 2); ?></h1>
                                    <p class="mb-0">กำหนดชำระ: <?php echo date('d/m/Y', strtotime($unpaid_bill['due_date'])); ?></p>
                                    <a href="payment.php" class="btn btn-light btn-sm fw-bold mt-3 px-4 rounded-pill text-primary">ชำระเงินทันที</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="card bg-success text-white h-100 shadow border-0 rounded-4" style="background: linear-gradient(135deg, #198754, #20c997);">
                                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                                    <h1 class="display-4 mb-3">🎉</h1>
                                    <h4 class="fw-bold">ยอดเยี่ยม! คุณไม่มียอดค้างชำระ</h4>
                                    <p class="mb-0">ขอบคุณที่ชำระค่าเช่าตรงเวลาครับ</p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-white h-100 shadow-sm border-0 rounded-4">
                            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                                <h5 class="fw-bold text-primary mb-0">🛠️ สถานะแจ้งซ่อมล่าสุด</h5>
                            </div>
                            <div class="card-body p-4">
                                <?php if ($latest_repair) {
                                    $status_color = 'bg-secondary text-white';
                                    if ($latest_repair['repair_status'] == 'รอคิวช่าง') $status_color = 'bg-warning text-dark';
                                    if ($latest_repair['repair_status'] == 'เสร็จสิ้น') $status_color = 'bg-success text-white';
                                    if ($latest_repair['repair_status'] == 'กำลังดำเนินการ') $status_color = 'bg-info text-white';
                                ?>
                                    <div class="p-3 bg-light rounded-3 mb-3">
                                        <p class="fw-bold mb-1 text-truncate"><?php echo $latest_repair['description']; ?></p>
                                        <small class="text-muted">วันที่แจ้ง: <?php echo date('d/m/Y', strtotime($latest_repair['request_date'])); ?></small>
                                    </div>
                                    <span class="badge <?php echo $status_color; ?> fs-6 px-3 py-2 rounded-pill">
                                        สถานะ: <?php echo $latest_repair['repair_status']; ?>
                                    </span>
                                <?php } else { ?>
                                    <div class="text-center text-muted mt-3">
                                        <p>ไม่มีประวัติการแจ้งซ่อม</p>
                                    </div>
                                <?php } ?>
                                <div class="mt-4 text-center">
                                    <a href="maintenance.php" class="btn btn-outline-primary btn-sm rounded-pill px-4">ดูทั้งหมด / แจ้งซ่อมใหม่</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>