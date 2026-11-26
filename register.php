<?php
session_start();

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastName = trim($_POST['nom'] ?? '');
    $firstName = trim($_POST['prenom'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['telephone'] ?? '');
    $cni = trim($_POST['CNI'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($lastName === '' || $firstName === '' || $email === '' || $cni === '' || $password === '' || $confirmPassword === '') {
        $errorMessage = 'Please complete all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $errorMessage = 'Password must contain at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $errorMessage = 'Passwords do not match.';
    } else {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=hotel;charset=utf8mb4', // Database connection string
                'root', // Database username
                '', // Database password
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] // Set error mode to exception for better error handling
            );

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $statement = $pdo->prepare(
                'INSERT INTO clients (nom, prenom, email, telephone, CNI, password_hash)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $statement->execute([$lastName, $firstName, $email, $phone, $cni, $passwordHash]);

            $successMessage = 'Your account has been created. You can now sign in.';
        } catch (PDOException $exception) {
            if ((int) $exception->errorInfo[1] === 1062) {
                $errorMessage = 'An account with this email or CNI already exists.';
            } else {
                $errorMessage = 'Unable to create the account right now. Please try again later.';
            }
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
    <title>Create account | The Haven Hotel</title>
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
                <span class="eyebrow">Join The ANGADI HOTEL</span>
                <h1>Your next stay<br>starts here.</h1>
                <p>Create an account to reserve rooms and manage every part of your visit.</p>
            </div>
            <div class="panel-footer">Thoughtful stays. Lasting memories.</div>
        </section>

        <section class="form-panel">
            <div class="form-wrap">
                <h2>Create account</h2>
                <p class="subheading">Enter your details to get started.</p>

                <?php if ($errorMessage !== ''): ?>
                    <p class="form-message form-error" role="alert"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <?php if ($successMessage !== ''): ?>
                    <p class="form-message form-success" role="status"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="field-row">
                        <div class="field">
                            <label for="prenom">First name</label>
                            <input id="prenom" name="prenom" type="text" autocomplete="given-name" value="<?php echo htmlspecialchars(trim($_POST['prenom'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="field">
                            <label for="nom">Last name</label>
                            <input id="nom" name="nom" type="text" autocomplete="family-name" value="<?php echo htmlspecialchars(trim($_POST['nom'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                    </div>
                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" placeholder="you@example.com" autocomplete="email" value="<?php echo htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="telephone">Phone number</label>
                            <input id="telephone" name="telephone" type="tel" autocomplete="tel" value="<?php echo htmlspecialchars(trim($_POST['telephone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="field">
                            <label for="CNI">CNI</label>
                            <input id="CNI" name="CNI" type="text" value="<?php echo htmlspecialchars(trim($_POST['CNI'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="password">Password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
                        </div>
                        <div class="field">
                            <label for="confirm_password">Confirm password</label>
                            <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" minlength="8" required>
                        </div>
                    </div>
                    <button class="submit-button" type="submit">Create account</button>
                </form>
                <p class="signup">Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </section>
    </main>
</body>
</html>
