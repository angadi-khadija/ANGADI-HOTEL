<?php
declare(strict_types=1);

require_once __DIR__ . '/admin-auth.php';
start_admin_session();

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $submittedToken = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $submittedToken)) {
        $errorMessage = 'Your sign-in session expired. Please try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $errorMessage = 'Enter a valid email address and password.';
    } else {
        try {
            $statement = admin_database()->prepare(
                "SELECT admin_id, first_name, last_name, email, password_hash, role
                 FROM admins
                 WHERE email = :email AND account_status = 'active'
                 LIMIT 1"
            );
            $statement->execute(['email' => $email]);
            $admin = $statement->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int) $admin['admin_id'];
                $_SESSION['admin_name'] = trim($admin['first_name'] . ' ' . $admin['last_name']);
                $_SESSION['admin_role'] = $admin['role'];
                unset($_SESSION['csrf_token']);
                header('Location: dashboard.php');
                exit;
            }

            $errorMessage = 'Invalid email or password.';
        } catch (PDOException $exception) {
            error_log('Angadi admin sign-in database error: ' . $exception->getMessage());
            $errorMessage = 'Sign-in is unavailable right now. Please try again later.';
        }
    }
}

$csrfToken = admin_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#183c34">
  <title>Admin sign in | Angadi Hotel</title><link rel="stylesheet" href="css/site.css">
</head>
<body class="access-body">
  <main class="access-card">
    <a class="brand" href="index.html"><span class="brand-mark">A</span><span>ANGADI <small>HOTEL &amp; RETREAT</small></span></a>
    <p class="eyebrow">HOTEL TEAM</p>
    <h1>Welcome <em>back.</em></h1>
    <p>Sign in with your active administrator account to manage the hotel.</p>
    <?php if ($errorMessage !== ''): ?>
      <p class="form-message form-error" role="alert"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
    <?php endif; ?>
    <form action="login.php" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
      <label class="access-label" for="email">Email address</label>
      <input class="access-input" id="email" name="email" type="email" autocomplete="username" required value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
      <label class="access-label" for="password">Password</label>
      <input class="access-input" id="password" name="password" type="password" autocomplete="current-password" required>
      <button class="button button-dark button-wide" type="submit">Sign in to dashboard <span>→</span></button>
    </form>
    <a class="text-link" href="index.html">← Back to guest website</a>
  </main>
</body>
</html>
