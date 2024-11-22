<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strike - Log In</title>
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
            <h2>Log in</h2>
            <form method="POST" action="">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Enter</button>
                <button class="createAccount">Create Account</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Strike. All rights reserved.</p>
    </footer>
</body>
</html>
