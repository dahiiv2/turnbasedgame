<?php
require_once __DIR__ . '/../exceptions/InvalidPasswordException.php';

function validatePassword($password) {
    if (strlen($password) < 6) {
        throw new InvalidPasswordException("Password must be at least 6 characters long.");
    }
    if (strlen($password) > 15) {
        throw new InvalidPasswordException("Password must be less than 16 characters long.");
    }
    if (!preg_match('/[A-Z]/', $password)) {
        throw new InvalidPasswordException("Password must contain at least one uppercase letter.");
    }
    if (!preg_match('/[a-z]/', $password)) {
        throw new InvalidPasswordException("Password must contain at least one lowercase letter.");
    }
    if (!preg_match('/[0-9]/', $password)) {
        throw new InvalidPasswordException("Password must contain at least one number.");
    }
}
