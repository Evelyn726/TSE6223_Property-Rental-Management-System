<?php
session_start();
session_unset();
session_destroy();

header("Location: ../admin-landlord-module/admin-login.php");
exit();
?>