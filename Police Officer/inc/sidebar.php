<!-- sidebar.php -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse pt-3 mt-3" style="height: 100vh; font-size: 18px;">
  <div class="position-sticky pt-3">
    <ul class="nav flex-column text-white gap-3">
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active bg-secondary' : '' ?>" href="index.php">
          <i class="bi bi-speedometer2 me-2"></i> Profile
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'suspect.php' ? 'active bg-secondary' : '' ?>" href="suspect.php">
          <i class="bi bi-person-badge me-2"></i> All Criminals
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'pass.php' ? 'active bg-secondary' : '' ?>" href="pass.php">
          <i class="bi bi-person-lines-fill me-2"></i> Change Password
        </a>
      </li>
            <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active bg-secondary' : '' ?>" href="report.php">
          <i class="bi bi-folder2-open me-2"></i> Investigation Reports
        </a>
      </li>


    </ul>
  </div>
</nav>

