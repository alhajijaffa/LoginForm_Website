<?php
session_start();

if (isset($_SESSION['email']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } elseif ($_SESSION['role'] === 'teacher') {
        header("Location: teacher/dashboard.php");
    } else {
        header("Location: student/dashboard.php");
    }
    exit();
}

$login_error = $_SESSION['login_error'] ?? '';
session_unset();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management Suite - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-box active" id="login-form">
            <form action="login.php" method="post">
                <h2>Student Management Suite</h2>
                <?php if (!empty($login_error)): ?>
                    <p class="error-message"><?= htmlspecialchars($login_error) ?></p>
                <?php endif; ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
