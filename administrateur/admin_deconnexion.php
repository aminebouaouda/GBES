<?php
// Start the session
session_start();

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect back to the login page
header("Location:../administrateur.php");
exit();
?>
