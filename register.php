<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $username = mysqli_real_escape_string($conn, $_POST['username']);$password = mysqli_real_escape_string($conn,$_POST['password']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);$lname = mysqli_real_escape_string($conn,$_POST['lname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);$room_id = mysqli_real_escape_string($conn,$_POST['room_id']); 
    
    // 1. สร้างรหัส tenant_id ให้อัตโนมัติในรูปแบบ T001, T002, ...
    $res_id = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(tenant_id, 2) AS UNSIGNED)) AS max_id FROM TENANT WHERE tenant_id LIKE 'T%'");
    $row_id = mysqli_fetch_assoc($res_id);$next_num = $row_id['max_id'] ?$row_id['max_id'] + 1 : 1;
    $tenant_id = 'T' . str_pad($next_num, 3, '0', STR_PAD_LEFT);

    // 2. บันทึกข้อมูลผู้เช่าลงตาราง TENANT พร้อม tenant_id และสถานะ pending (รอยืนยัน)
    $sql_tenant = "INSERT INTO TENANT (tenant_id, tenant_username, tenant_password, tenant_fname, tenant_lname, tenant_phone, tenant_status) 
                   VALUES ('$tenant_id', '$username', '$password', '$fname', '$lname', '$phone', 'pending')";
    
    if (mysqli_query($conn,$sql_tenant)) {
        // 3. สร้างข้อมูลสัญญาเช่าเบื้องต้นลงตาราง CONTRACT โดยใช้ tenant_id ที่สร้างขึ้น
        $sql_contract = "INSERT INTO CONTRACT (signed_date, start_date, end_date, deposit_total, contract_status, room_id, tenant_id) 
                         VALUES (CURDATE(), CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 5000, 'pending', '$room_id', '$tenant_id')";
        
        mysqli_query($conn,$sql_contract);

        echo "<script>alert('สมัครสมาชิกสำเร็จ ระบบได้จองห้องพักให้ท่านแล้ว กรุณารอแอดมินยืนยันการอนุมัติ'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการสมัครสมาชิก: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิกผู้เช่า - IDMS DORMITORY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="py-5">
<div class="container d-flex justify-content-center align-items-center">
    <div class="card p-4 shadow-sm border-0" style="width: 100%; max-width: 550px; border-radius: 20px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary"><i class="bi bi-building"></i> IDMS DORMITORY</h3>
            <p class="text-muted small">สมัครสมาชิกผู้เช่าใหม่</p>
        </div>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-muted fw-bold">ชื่อผู้ใช้งาน (Username)</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted fw-bold">รหัสผ่าน (Password)</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted fw-bold">ชื่อจริง</label>
                    <input type="text" name="fname" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted fw-bold">นามสกุล</label>
                    <input type="text" name="lname" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted fw-bold">เบอร์โทรศัพท์</label>
                <input type="text" name="phone" class="form-control" maxlength="10" required>
            </div>

            <hr class="my-4">
            <h6 class="text-primary fw-bold mb-3"><i class="bi bi-door-open"></i> เลือกห้องพักที่ต้องการเช่า</h6>

            <div class="mb-3">
                <label class="form-label text-muted fw-bold">เลือกชั้น</label>
                <select id="floorSelect" class="form-select" required>
                    <option value="">-- กรุณาเลือกชั้น --</option>
                    <option value="1">ชั้น 1 (โซน A - ห้องแอร์ 3,200 บ.)</option>
                    <option value="2">ชั้น 2 (โซน B - ห้องพัดลม 2,800 บ.)</option>
                    <option value="3">ชั้น 3 (โซน C - ห้องพัดลม 2,800 บ.)</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted fw-bold">เลือกเลขห้องพัก</label>
                <select name="room_id" id="roomSelect" class="form-select" required disabled>
                    <option value="">-- กรุณาเลือกชั้นก่อน --</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill mb-2 py-2 fw-bold shadow-sm">ยืนยันการสมัครสมาชิก</button>
            <a href="login.php" class="btn btn-outline-secondary w-100 rounded-pill py-2">กลับไปหน้าล็อกอิน</a>
        </form>
    </div>
</div>

<script>
    const rooms = [
        <?php
        $room_res = mysqli_query($conn, "SELECT room_id, floor FROM ROOM WHERE room_status = 'ว่าง' ORDER BY room_id ASC");
        while($room = mysqli_fetch_assoc($room_res)) {
            echo "{id: '{$room['room_id']}', floor: {$room['floor']}},";
        }
        ?>
    ];

    const floorSelect = document.getElementById('floorSelect');
    const roomSelect = document.getElementById('roomSelect');

    floorSelect.addEventListener('change', function() {
        const selectedFloor = this.value;
        roomSelect.innerHTML = '<option value="">-- กรุณาเลือกห้องพัก --</option>';
        
        if (selectedFloor) {
            roomSelect.disabled = false;
            const filteredRooms = rooms.filter(room => room.floor == selectedFloor);
            
            if (filteredRooms.length > 0) {
                filteredRooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = `ห้อง ${room.id}`;
                    roomSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = "";
                option.textContent = "ขออภัย ไม่มีห้องว่างในชั้นนี้";
                roomSelect.appendChild(option);
            }
        } else {
            roomSelect.disabled = true;
            roomSelect.innerHTML = '<option value="">-- กรุณาเลือกชั้นก่อน --</option>';
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>