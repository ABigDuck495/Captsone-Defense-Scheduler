<?php require __DIR__ . '/../views/layout/header.php' ?>

<link rel="stylesheet" href="vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/custom.css">

<header id="site-header" class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-5">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Thesis Scheduling System</span>
    <a class="btn btn-primary" href="/auth/login.php">Log in</a>
  </div>
</header>

<main class="container py-5">
  <div class="row g-5 align-items-center mb-5">
    <div class="col-lg-7">
      <span class="eyebrow">Capstone &amp; Thesis Scheduling</span>
      <h1 class="display-5 fw-semibold mb-3">Get your defense on the calendar without the email back-and-forth.</h1>
      <p class="lead" style="max-width:46ch;">
        ThesisTrack is where thesis groups and advisers coordinate defense
        schedules in one place — propose a group, check adviser availability,
        request a slot, and track approval, all without a single reply-all thread.
      </p>

      <div class="row row-cols-3 g-2">
        <div class="col"><div class="stat-card"><div class="num" id="stat-groups">—</div><div class="label">Thesis groups</div></div></div>
        <div class="col"><div class="stat-card"><div class="num" id="stat-scheduled">—</div><div class="label">Defenses scheduled</div></div></div>
        <div class="col"><div class="stat-card"><div class="num" id="stat-professors">—</div><div class="label">Advisers on board</div></div></div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card p-4">
        <span class="eyebrow">How it works</span>
        <div class="mt-2">
          <div class="trail-step is-done">
            <div class="trail-dot">1</div>
            <div class="trail-body"><h4>Form your group</h4><p>One student sets up the group profile and adds the other two members.</p></div>
          </div>
          <div class="trail-step is-active">
            <div class="trail-dot">2</div>
            <div class="trail-body"><h4>Request a defense slot</h4><p>Pick from your adviser's open availability and submit a request.</p></div>
          </div>
          <div class="trail-step">
            <div class="trail-dot">3</div>
            <div class="trail-body"><h4>Adviser reviews &amp; approves</h4><p>The professor confirms the panel and locks in the schedule.</p></div>
          </div>
          <div class="trail-step">
            <div class="trail-dot">4</div>
            <div class="trail-body"><h4>Defend</h4><p>Everyone shows up already knowing the time, room, and panel.</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section>
    <span class="eyebrow">Two roles, one calendar</span>
    <h2 class="mb-3">Built around how a defense actually gets scheduled</h2>
    <p style="max-width:64ch">
      Students work as a group of three under a single account, submit
      requests against real adviser availability, and professors approve or
      decline with a panel attached — no spreadsheet required.
    </p>
  </section>
</main>

<footer class="tt-footer" id="site-footer"></footer>

<?php require __DIR__ . '/../views/layout/footer.php' ?>