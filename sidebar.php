<!-- Header ด้านขวาบนสำหรับแสดงชื่อและปุ่มออกจากระบบ -->
<div class="top-header">
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <strong class="me-1"><i class="bi bi-person-circle text-primary"></i> <?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : 'ผู้ใช้งาน'; ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item text-danger fw-bold" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> ออกจากระบบ</a></li>
        </ul>
    </div>
</div>

<!-- Sidebar เมนูด้านข้าง -->
<div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-white shadow-sm">
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
        <span class="fs-4 fw-bold text-primary">🏢 IDMS DORMITORY</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <!-- เมนูสำหรับ Admin -->
            <li class="nav-item mb-1">
                <a href="index.php" class="nav-link text-dark">
                    <i class="bi bi-house-door me-2"></i> หน้าหลัก
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="approve.php" class="nav-link text-dark">
                    <i class="bi bi-person-check me-2"></i> อนุมัติผู้เช่า
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="meter.php" class="nav-link text-dark">
                    <i class="bi bi-lightning-charge me-2"></i> คีย์ค่าไฟและออกบิล
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="admin_maintenance.php" class="nav-link text-dark">
                    <i class="bi bi-tools me-2"></i> จัดการแจ้งซ่อม
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="admin_history.php" class="nav-link text-dark">
                    <i class="bi bi-file-earmark-text me-2"></i> ประวัติและสัญญา
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="admin_payment.php" class="nav-link text-dark">
                    <i class="bi bi-credit-card me-2"></i> ตรวจสอบการชำระเงิน
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="admin_contact.php" class="nav-link text-dark">
                    <i class="bi bi-telephone me-2"></i> ข้อมูลติดต่อ
                </a>
            </li>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'tenant'): ?>
            <!-- เมนูสำหรับ Tenant (ผู้เช่า) -->
            <li class="nav-item mb-1">
                <a href="index.php" class="nav-link text-dark">
                    <i class="bi bi-house-door me-2"></i> หน้าหลัก
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="payment.php" class="nav-link text-dark">
                    <i class="bi bi-wallet2 me-2"></i> ชำระเงินค่าเช่า
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="payment_history.php" class="nav-link text-dark">
                    <i class="bi bi-receipt me-2"></i> ประวัติบิล
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="maintenance.php" class="nav-link text-dark">
                    <i class="bi bi-tools me-2"></i> แจ้งซ่อม
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="contract.php" class="nav-link text-dark">
                    <i class="bi bi-file-text me-2"></i> สัญญาเช่า
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="profile.php" class="nav-link text-dark">
                    <i class="bi bi-person me-2"></i> ข้อมูลส่วนตัว
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>

<style>
    /* ตั้งค่า Sidebar ด้านข้าง */
    .sidebar {
        width: 260px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
    }
    
    /* ตั้งค่า Header ขวาบนสำหรับปุ่มออกจากระบบ */
    .top-header {
        position: fixed;
        top: 0;
        right: 0;
        left: 260px;
        height: 70px;
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0 30px;
        z-index: 999;
    }

    /* พื้นที่เนื้อหาหลักเว้นหลบ Sidebar และ Header ด้านบน */
    .main-content {
        margin-left: 260px;
        margin-top: 70px;
        padding: 30px;
        background-color: #f8f9fa;
        min-height: calc(100vh - 70px);
    }

    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        background-color: #0d6efd !important;
        color: white !important;
    }
    .nav-pills .nav-link:hover {
        background-color: #e9ecef;
        color: #0d6efd;
    }

    @media (max-width: 768px) {
        .sidebar { width: 100%; height: auto; position: relative; }
        .top-header { left: 0; position: relative; justify-content: center; }
        .main-content { margin-left: 0; margin-top: 0; }
    }
</style>