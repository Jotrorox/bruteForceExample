<?php
session_start();
session_unset();
session_destroy();

echo '<div class="alert alert-success">Logged out successfully. Redirecting...</div>';
?>
