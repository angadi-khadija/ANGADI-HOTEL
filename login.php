<?php
session_start();

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errorMessage = 'Please enter your email address and password.';
    } else {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=hotel;charset=utf8mb4',
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
             
            $statement = $pdo->prepare('SELECT id, nom, prenom, email, password_hash FROM clients WHERE email = ?');
            $statement->execute([$email]);
            $client = $statement->fetch(PDO::FETCH_ASSOC);

            if ($client && password_verify($password, $client['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['client_id'] = $client['id'];
                $_SESSION['client_name'] = trim($client['prenom'] . ' ' . $client['nom']);
                $successMessage = 'Welcome, ' . htmlspecialchars($_SESSION['client_name'], ENT_QUOTES, 'UTF-8') . '!';
            } else {
                $errorMessage = 'Invalid email address or password.';
            }
        } catch (PDOException $exception) {
            $errorMessage = 'Unable to sign in right now. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#173b35">
	<title>Sign in | The Haven Hotel</title>
	<link rel="stylesheet" href="css/login.css">
</head>
<body>
	<main class="login-card">
		<section class="welcome-panel" aria-label="Welcome to ANGADI Hotel">
			<div class="brand">
				<span class="brand-mark" aria-hidden="true">AG</span>
				<span class="brand-name">ANGADI</span>
			</div>
			<div class="welcome-copy">
				<span class="eyebrow">A stay to remember</span>
				<h1>Welcome back<br>to your haven.</h1>
				<p>Sign in to manage your reservations and make your next stay exceptional.</p>
			</div>
			<div class="panel-footer">Thoughtful stays. Lasting memories.</div>
		</section>

		<section class="form-panel">
			<div class="form-wrap">
				<h2>Sign in</h2>
				<p class="subheading">Access your hotel account below.</p>
				<?php if ($errorMessage !== ''): ?>
					<p class="form-message form-error" role="alert"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
				<?php endif; ?>
				<?php if ($successMessage !== ''): ?>
					<p class="form-message form-success" role="status"><?php echo $successMessage; ?></p>
				<?php endif; ?>
				<form action="" method="post">
					<div class="field">
						<label for="email">Email address</label>
						<input id="email" name="email" type="email" placeholder="you@example.com" autocomplete="email" value="<?php echo htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
					</div>
					<div class="field">
						<label for="password">Password</label>
						<input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
					</div>
					<div class="form-options">
						<label class="remember"><input type="checkbox" name="remember"> Remember me</label>
						<a href="forgot-password.php">Forgot password?</a>
					</div>
					<button class="submit-button" type="submit">Sign in to your account</button>
				</form>
				<p class="signup">New to The ANGADI HOTEL? <a href="register.php">Create an account</a></p>
			</div>
		</section>
	</main>
</body>
</html>
