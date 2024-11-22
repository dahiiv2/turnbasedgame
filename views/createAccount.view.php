<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strike - Create Account</title>
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <img src="img/logo.png" alt="Strike Logo" class="logo">
    </header>
    <main class="contenedor">
        <div class="form">
            <h2>Create Account</h2>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form method="POST" action="createAccount.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required minlength="3" maxlength="50">

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required minlength="6">

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">

                <button type="submit">Create Account</button>
                <a href="index.php" class="back-to-login">Back to Login</a>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Strike. All rights reserved.</p>
    </footer>
</body>
</html>
