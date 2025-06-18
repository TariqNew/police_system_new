<!-- sidebar.php -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse pt-5 mt-3"
  style="height: 100vh; font-size: 18px;">
  <div class="position-sticky pt-3">
    <ul class="nav flex-column text-white gap-3">
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active bg-secondary' : '' ?>"
          href="index.php">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'police_officer.php' ? 'active bg-secondary' : '' ?>"
          href="police_officer.php">
          <i class="bi bi-person-badge me-2"></i> Officers
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'investigator.php' ? 'active bg-secondary' : '' ?>"
          href="investigator.php">
          <i class="bi bi-person-badge me-2"></i> Investigators
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'suspect.php' ? 'active bg-secondary' : '' ?>"
          href="suspect.php">
          <i class="bi bi-person-badge me-2"></i> Criminals
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active bg-secondary' : '' ?>"
          href="settings.php">
          <i class="bi bi-gear me-2"></i>System Settings
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == '.php' ? 'active bg-secondary' : '' ?>"
          href="report.php">
          <i class="bi bi-folder2-open me-2"></i> Reports
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'active bg-secondary' : '' ?>"
          href="logs.php">
          <i class="bi bi-clock-history me-2"></i> Logs
        </a>
      </li>
    </ul>
  </div>
</nav>