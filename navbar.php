<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            🏢 IDMS DORMITORY
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <!-- เมนูสำหรับ Admin -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">หน้าหลัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approve.php">อนุมัติผู้เช่า</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="meter.php">คีย์ค่าไฟและออกบิล</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_maintenance.php">จัดการแจ้งซ่อม</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_history.php">ประวัติและสัญญา</a>
                    </li>
                    <li class="nav-item">
    <a class="nav-link" href="admin_contact.php">ข้อมูลติดต่อ</a>
</li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-outline-danger rounded-pill px-4" href="logout.php">ออกจากระบบ</a>
                    </li>

                <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'tenant'): ?>
                    <!-- เมนูสำหรับ Tenant (ผู้เช่า) -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">หน้าหลัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="payment_history.php">ประวัติบิล</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="maintenance.php">แจ้งซ่อม</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contract.php">สัญญาเช่า</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">ข้อมูลส่วนตัว</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-outline-danger rounded-pill px-4" href="logout.php">ออกจากระบบ</a>
                    </li>
                <?php endif; ?>
                
            </ul>
        </div>
    </div>
</nav>