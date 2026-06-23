<?php
session_start();
session_unset();
session_destroy();

header("Location: ../admin-login-logout/admin-login.php");
exit();
?>