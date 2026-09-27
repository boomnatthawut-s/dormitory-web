<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลติดต่อฉุกเฉิน - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container py-4">
        <h3 class="mb-4 fw-bold text-primary"><i class="bi bi-telephone"></i> ข้อมูลติดต่อผู้เช่าและผู้ปกครอง (กรณีฉุกเฉิน)</h3>

        <div class="table-responsive bg-white rounded-4 shadow-sm p-3">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">ห้องพัก</th>
                        <th>ชื่อผู้เช่า</th>
                        <th>เบอร์โทรผู้เช่า</th>
                        <th>ชื่อผู้ปกครอง / ผู้ติดต่อฉุกเฉิน</th>
                        <th>ความสัมพันธ์</th>
                        <th>เบอร์โทรผู้ปกครอง</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT c.room_id, 
                                   t.tenant_fname, t.tenant_lname, t.tenant_phone, 
                                   p.parent_fname, p.parent_lname, p.relation_tenant, p.parent_phone 
                            FROM TENANT t
                            LEFT JOIN PARENT p ON t.tenant_id = p.tenant_id
                            JOIN CONTRACT c ON t.tenant_id = c.tenant_id
                            WHERE c.contract_status = 'active'
                            ORDER BY c.room_id ASC";
                            
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $parent_name = !empty($row['parent_fname']) ? $row['parent_fname'] . " " . $row['parent_lname'] : "<span class='text-muted fst-italic'>ไม่มีข้อมูล</span>";
                            $relation = !empty($row['relation_tenant']) ? $row['relation_tenant'] : "-";
                            $parent_phone = !empty($row['parent_phone']) ? "<a href='tel:".$row['parent_phone']."' class='text-decoration-none text-primary fw-bold'>".$row['parent_phone']."</a>" : "-";
                            $tenant_phone = "<a href='tel:".$row['tenant_phone']."' class='text-decoration-none'>".$row['tenant_phone']."</a>";

                            echo "<tr>";
                            echo "<td class='text-center fw-bold text-primary'>" . $row['room_id'] . "</td>";
                            echo "<td>" . $row['tenant_fname'] . " " . $row['tenant_lname'] . "</td>";
                            echo "<td>" . $tenant_phone . "</td>";
                            echo "<td>" . $parent_name . "</td>";
                            echo "<td>" . $relation . "</td>";
                            echo "<td>" . $parent_phone . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>ไม่มีข้อมูลผู้เช่าในขณะนี้</td></tr>";
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