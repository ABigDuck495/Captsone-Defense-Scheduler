<?php require __DIR__ . '/../layout/header.php'; ?>

<link rel="stylesheet" href="../../public/assets/css/custom.css">

<!-- Backend: set groupId from session, e.g. $_SESSION['group_id'] -->
<script>document.body.dataset.groupId = 'group1';</script>

<header class="dash-header">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <h1 class="mb-0">Thesis Scheduling System</h1>
      <span class="badge bg-forest">Student</span>
    </div>
    <a href="<?= $__tssBaseUrl ?>views/auth/logout.php" class="btn btn-sm btn-outline-light" style="color:#fff;border-color:rgba(255,255,255,.5);">Log out</a>
  </div>
</header>

<main class="container py-4">

  <div id="form-alert"></div>

  <!-- Booked defenses card -->
  <!-- Backend: load from defense_schedules where group_id matches -->
  <div class="card p-3 p-md-4 mb-4">
    <span class="eyebrow">Upcoming</span>
    <h2 class="h5 mb-3">My Booked Defenses</h2>
    <div id="booked-list">
      <div class="dash-empty">No booked defenses yet.</div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-4" id="student-nav">
    <li class="nav-item">
      <a class="nav-link active" href="#" data-target="#tab-book">Book Schedule</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#" data-target="#tab-requests">My Requests</a>
    </li>
  </ul>

  <!-- Tab: Book Schedule -->
  <div class="tab-pane" id="tab-book">

    <!-- Step 1: choose exactly 3 professors -->
    <div class="card p-3 p-md-4 mb-4">
      <span class="eyebrow">Step 1</span>
      <h2 class="h5 mb-2">Select exactly 3 advisers</h2>
      <p class="text-body-secondary small mb-3">You must pick exactly three professors. The calendar only shows dates where all three are available.</p>
      <!-- Backend: load professors list from users where role = professor -->
      <div class="prof-picker" id="prof-picker"></div>
      <div class="prof-counter" id="prof-counter"></div>
    </div>

    <!-- Step 2: calendar (shows available dates only, click to request a slot) -->
    <!-- Backend: load availability for the 3 selected professors -->
    <?php $scheduleRole = 'student'; require __DIR__ . '/../schedule/request.php'; ?>

    <!-- Reference: each professor's upcoming open slots -->
    <div class="mt-4">
      <span class="eyebrow">For reference</span>
      <h2 class="h5 mb-3">Adviser availability</h2>
      <div id="prof-availability-list"></div>
    </div>
  </div>

  <!-- Tab: My Requests — see accept/reject status of each panel member -->
  <!-- Backend: load schedule_requests + schedule_approvals for this group -->
  <!-- All 3 must accept before the defense is booked -->
  <div class="tab-pane d-none" id="tab-requests">
    <h2 class="h5 mb-3">My Requests</h2>
    <p class="text-body-secondary small mb-3">A defense is only booked when all 3 panel members accept.</p>
    <div id="requests-list"></div>
  </div>

</main>

<footer class="tt-footer" id="site-footer"></footer>

<script src="<?= $__tssBaseUrl ?>public/vendor/jquery/jquery.min.js"></script>
<script src="<?= $__tssBaseUrl ?>public/assets/js/app.js"></script>

<?php require __DIR__ . '/../layout/footer.php'; ?>