<!-- sidebar.php -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse pt-5 mt-3"
  style="height: 100vh; font-size: 18px;">
  <div class="position-sticky pt-3">
    <ul class="nav flex-column text-white gap-3">
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active bg-secondary' : '' ?>"
          href="index.php">
          <i class="bi bi-person-circle me-2"></i> Profile
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'suspect.php' ? 'active bg-secondary' : '' ?>"
          href="suspect.php">
          <i class="bi bi-people-fill me-2"></i> Criminals
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'investigation.php' ? 'active bg-secondary' : '' ?>"
          href="investigation.php">
          <i class="bi bi-journal-plus me-2"></i> Add Investigations
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active bg-secondary' : '' ?>"
          href="report.php">
          <i class="bi bi-file-earmark-text me-2"></i> Investigations & Reports
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'statistic.php' ? 'active bg-secondary' : '' ?>"
          href="statistic.php">
          <i class="bi bi-bar-chart-line-fill me-2"></i> Statistics
        </a>
      </li>
    </ul>
  </div>
</nav>
