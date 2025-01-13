<?php
// session_config.php

session_set_cookie_params([
    'secure' => true,       // Send cookie over HTTPS only
    'httponly' => true,     // Prevent JavaScript access
    'samesite' => 'Strict', // Mitigate CSRF attacks
]);
?>
