<?php
function validation($data) {
    $errors = array();

    // Regular expression patterns
    $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    $passwordPattern = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!])([a-zA-Z0-9@#$%^&+=!]){8,}$/';

    // Validate email
    if (empty($data['email'])) {
        $errors['email'] = 'Email is required';
    } else if (!preg_match($emailPattern, $data['email'])) {
        $errors['email'] = 'Invalid email address';
    }

    // Validate password
    if (empty($data['password'])) {
        $errors['password'] = 'Password is required';
    } else if (!preg_match($passwordPattern, $data['password'])) {
        $errors['password'] = 'Password must be at least 8 characters long and contain at least one number, one lowercase letter, one uppercase letter, and one special character';
    }

    return $errors;
}
?>
