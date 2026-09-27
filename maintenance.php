<?php
session_start();
include("connect.php");

// ตรวจสอบสิทธิ์ผู้เช่า
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'tenant') {
    header("Location: login.php"); 
    exit();
}
$tenant_id = isset($_SESSION['tenant_id']) ? $_SESSION['tenant_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_SESSION['id']) ? $_SESSION['id'] : ''));

$error_msg = "";
$success_msg = "";

// จัดการเมื่อมีการกดส่งเรื่องแจ้งซ่อม
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_repair'])) {
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $time = mysqli_real_escape_string($conn, $_POST['preferred_time']);
    $date = date('Y-m-d');
    
    // จัดการอัปโหลดรูปภาพปัญหา (issue_image)
    $issue_image = '';
    if(isset($_FILES['issue_image']) && $_FILES['issue_image']['name'] != '') {
        $ext = pathinfo($_FILES['issue_image']['name'], PATHINFO_EXTENSION);
        $issue_image = 'issue_' . time() . '.' . $ext;
        if(!is_dir('uploads')) { mkdir('uploads', 0777, true); }
        move_uploaded_file($_FILES['issue_image']['tmp_name'], 'uploads/' . $issue_image);
    }

    // 1. สร้างรหัส request_id อัตโนมัติในรูปแบบ REQ01, REQ02, ...
    $res_id = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(request_id, 4) AS UNSIGNED)) AS max_id FROM MAINTENANCE_REQUEST WHERE request_id LIKE 'REQ%'");
    $row_id = mysqli_fetch_assoc($res_id);
    $next_num = $row_id['max_id'] ? $row_id['max_id'] + 1 : 1;
    $request_id = 'REQ' . str_pad($next_num, 2, '0', STR_PAD_LEFT);

    // 2. บันทึกข้อมูลลงฐานข้อมูล พร้อมใส่ request_id ที่สร้างขึ้น
    $sql = "INSERT INTO MAINTENANCE_REQUEST (request_id, description, preferred_time, request_date, repair_status, issue_image, tenant_id) 
            VALUES ('$request_id', '$desc', '$time', '$date', 'รอดำเนินการ', '$issue_image', '$tenant_id')";
            
    if(mysqli_query($conn, $sql)) {
        $success_msg = "ส่งเรื่องแจ้งซ่อมเรียบร้อยแล้ว";
    } else {
        $error_msg = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แจ้งซ่อม - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <?php include("sidebar.php"); ?>

    <div class="main-content">
        <div class="container py-4">
            
            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-5 mb-4">
                    <h3 class="fw-bold text-primary mb-4"><i class="bi bi-tools"></i> แจ้งซ่อมแซม</h3>
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">รายละเอียดปัญหา</label>
                                    <textarea name="description" class="form-control" rows="4" required placeholder="เช่น แอร์ไม่เย็น, น้ำรั่ว..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ช่วงเวลาที่สะดวกให้ช่างเข้าซ่อม</label>
                                    <input type="text" name="preferred_time" class="form-control" required placeholder="เช่น วันเสาร์ ช่วงบ่าย">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">แนบรูปภาพปัญหา (ถ้ามี)</label>
                                    <input type="file" name="issue_image" class="form-control" accept="image/*">
                                </div>
                                <button type="submit" name="submit_repair" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm py-2">ส่งเรื่องแจ้งซ่อม</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <h3 class="fw-bold text-dark mb-4">ประวัติการแจ้งซ่อม</h3>
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <?php
                            $sql_list = "SELECT * FROM MAINTENANCE_REQUEST WHERE tenant_id = '$tenant_id' ORDER BY request_id DESC";
                            $res_list = mysqli_query($conn, $sql_list);
                            
                            if($res_list && mysqli_num_rows($res_list) > 0) {
                                while($row = mysqli_fetch_assoc($res_list)) {
                                    $badge = 'bg-secondary text-white';
                                    if($row['repair_status'] == 'รอคิวช่าง') $badge = 'bg-warning text-dark';
                                    if($row['repair_status'] == 'กำลังดำเนินการ') $badge = 'bg-info text-white';
                                    if($row['repair_status'] == 'เสร็จสิ้น') $badge = 'bg-success text-white';
                                    
                                    echo "<div class='border-bottom pb-3 mb-3'>";
                                    echo "<div class='d-flex justify-content-between align-items-start'>";
                                    echo "<div>";
                                    echo "<span class='badge bg-light text-primary border mb-1'>#{$row['request_id']}</span>";
                                    echo "<h6 class='fw-bold mb-1'>{$row['description']}</h6>";
                                    echo "<small class='text-muted'>เวลาสะดวก: {$row['preferred_time']} | แจ้งเมื่อ: ".date('d/m/Y', strtotime($row['request_date']))."</small>";
                                    
                                    if(!empty($row['issue_image'])) {
                                        echo "<div class='mt-2'>";
                                        echo "<a href='uploads/{$row['issue_image']}' target='_blank'>";
                                        echo "<img src='uploads/{$row['issue_image']}' alt='Issue Image' class='img-thumbnail' style='max-height: 80px;'>";
                                        echo "</a>";
                                        echo "</div>";
                                    }
                                    
                                    echo "</div>";
                                    echo "<span class='badge {$badge}'>{$row['repair_status']}</span>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<p class='text-muted text-center py-3'>ไม่มีประวัติการแจ้งซ่อม</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>