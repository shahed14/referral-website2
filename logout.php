<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['register']) && $_SESSION['register'] === true) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();
}

// Redirect the user to the login page after logout
header("Location: reflogin.php");
exit();
?>
