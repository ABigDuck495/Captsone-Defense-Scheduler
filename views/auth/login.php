<?php
require __DIR__ . '/../layout/header.php';
?>

<header id="site-header"></header>

<main class="login-shell">
  <div class="login-card card p-4 p-md-5">
    <div class="text-center mb-4">
      <span class="brand-mark brand-mark-lg mb-3">TSS</span>
      <h1 class="h4 mb-1">Log in to Thesis Scheduler System</h1>
      <p class="text-body-secondary small mb-0">Students, use your email. Advisers, use your email.</p>
    </div>

    <div class="text-center mb-3">
      <a href="<?= $__tssBaseUrl ?>index.php" class="btn btn-outline-secondary btn-sm">Back to home</a>
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

<script>
const loginUrl = '<?= $__tssBaseUrl ?>views/ajax/login.php';

document.getElementById('login-form').addEventListener('submit', async function (e) {
  e.preventDefault();

  const alertBox = document.getElementById('form-alert');
  const btn = document.getElementById('login-btn');
  alertBox.innerHTML = '';
  btn.disabled = true;

  try {
    const res = await fetch(loginUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
      })
    });

    const data = await res.json();

    if (!res.ok || !data.success) {
      alertBox.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Login failed') + '</div>';
      btn.disabled = false;
      return;
    }

    window.location.href = data.redirect;
  } catch (err) {
    alertBox.innerHTML = '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
    btn.disabled = false;
  }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>