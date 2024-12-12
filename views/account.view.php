<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/navigation.css">
    <link rel="stylesheet" href="styles/footer.css">   
    <link rel="stylesheet" href="styles/background.css">
    <link rel="stylesheet" href="styles/account.css">
    <title>Strike - Account</title>
</head>
<body>
    <?php include 'partials/nav-menu.php'; ?>

    <div class="glass-container">
        <h1>Account Settings</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>Profile Information</h2>
            <div class="profile-section">
                <div class="profile-picture">
                    <?php if (!empty($user['imagen'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['imagen']); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <div class="no-picture">No Picture</div>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <div class="info-row">
                        <span class="label">Username:</span>
                        <span class="value"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                </div>
            </div>
            <form method="POST" action="account.php" enctype="multipart/form-data" class="picture-form">
                <div class="form-group">
                    <label for="profile_picture">Change Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                </div>
                <button type="submit" name="upload_picture">Update Picture</button>
            </form>
        </div>

        <div class="section">
            <h2>Change Password</h2>
            <form method="POST" action="account.php">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" required minlength="6">
                </div>

                <button type="submit" name="change_password" class="button">Update Password</button>
            </form>
        </div>

        <div class="section">
            <h2>Account Management</h2>
            <form method="POST" action="account.php" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                <button type="submit" name="delete_account" class="button delete">Delete Account</button>
            </form>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
</body>
</html>