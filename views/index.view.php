<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strike - Log In</title>
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/background.css">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/navigation.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <img src="img/logo.png" alt="Strike Logo" class="logo">
    </header>
    <main class="contenedor">
        <div class="form glass-container">
            <h2>Log in</h2>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Log in</button>
                <a href="createAccount.php" class="createAccount">Create Account</a>
            </form>
        </div>
    </main>
    <?php include 'partials/footer.php'; ?>
</body>
</html>
