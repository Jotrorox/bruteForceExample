<?php
session_start();
session_unset();
session_destroy();

// Send a response that will trigger a full page reload
echo '<script>
    setTimeout(function() {
        window.location.href = "/";
    }, 1000);
</script>
<div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
    Logged out successfully. Redirecting...
</div>';
?>
