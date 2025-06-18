<!-- navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm" style="z-index: 1000; font-size: 20px;">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="../police.png" width="40" alt="Logo" class="me-2">
      <span class="fw-bold fs-4">CRMS</span>
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav Links -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
      <ul class="navbar-nav mb-2 mb-lg-0">
        <!-- Logout -->
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center gap-1" href="../logout.php">
            <i class="fa fa-sign-out"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
