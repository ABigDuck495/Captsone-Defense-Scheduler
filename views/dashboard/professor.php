<?php require __DIR__ . '/../layout/header.php'; ?>

<link rel="stylesheet" href="../../public/assets/css/custom.css">

<!-- Backend: set professorId from session, e.g. $_SESSION['user_id'] -->
<script>document.body.dataset.professorId = 'prof1';</script>

<header class="dash-header">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <h1 class="mb-0">Thesis Scheduling System</h1>
      <span class="badge bg-forest">Professor</span>
    </div>
    <a href="<?= $__tssBaseUrl ?>auth/logout.php" class="btn btn-sm btn-outline-light" style="color:#fff;border-color:rgba(255,255,255,.5);">Log out</a>
  </div>
</header>

<main class="container py-4">

  <!-- Backend: load professor name/dept from session -->
  <p class="text-body-secondary small mb-3" id="prof-name-label"></p>
  <div id="form-alert"></div>

  <!-- Booked defenses card -->
  <!-- Backend: load from defense_schedules where professor is on the panel -->
  <div class="card p-3 p-md-4 mb-4">
    <span class="eyebrow">Upcoming</span>
    <h2 class="h5 mb-3">My Booked Defenses</h2>
    <div id="booked-list">
      <div class="dash-empty">No booked defenses yet.</div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-4" id="prof-nav">
    <li class="nav-item">
      <a class="nav-link active" href="#" data-target="#tab-availability">My Availability</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#" data-target="#tab-requests">Requests</a>
    </li>
  </ul>

  <!-- Tab: My Availability — interactable calendar to add/remove slots -->
  <!-- Backend: load/save slots from professor_availability -->
  <div class="tab-pane" id="tab-availability">
    <?php $scheduleRole = 'professor'; require __DIR__ . '/../schedule/request.php'; ?>
  </div>

  <!-- Tab: Requests — accept or reject pending schedule requests -->
  <!-- Backend: load from schedule_requests + schedule_approvals for this professor -->
  <div class="tab-pane d-none" id="tab-requests">
    <span class="eyebrow">Defense panel requests</span>
    <h2 class="h5 mb-3">Requests needing your response</h2>
    <p class="text-body-secondary small mb-3">All 3 panel members must accept before the defense is booked. If you reject, select at least one reason.</p>
    <div id="requests-list"></div>
  </div>

</main>

<footer class="tt-footer" id="site-footer"></footer>

<script src="<?= $__tssBaseUrl ?>public/vendor/jquery/jquery.min.js"></script>
<script src="<?= $__tssBaseUrl ?>public/assets/js/app.js"></script>

<?php require __DIR__ . '/../layout/footer.php'; ?>