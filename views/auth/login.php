<?php require __DIR__ . '/../layout/header.php'; ?>

<header id="site-header"></header>

<main class="login-shell">
  <div class="login-card card p-4 p-md-5">
    <div class="text-center mb-4">
      <span class="brand-mark brand-mark-lg mb-3">TSS</span>
      <h1 class="h4 mb-1">Log in to Thesis Scheduler System</h1>
      <p class="text-body-secondary small mb-0">Students, use your email. Advisers, use your email.</p>
    </div>

    <div id="form-alert"></div>

    <form id="login-form" class="text-center" novalidate>
      <div class="mb-3">
        <input type="text" class="form-control text-center" id="email" name="email" placeholder="Email" autocomplete="email" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control text-center" id="password" name="password" placeholder="Password" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100" id="login-btn">Log in</button>
    </form>
  </div>
</main>

<footer class="tt-footer" id="site-footer"></footer>

<?php require __DIR__ . '/../layout/footer.php'; ?>