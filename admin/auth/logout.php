<?php
/**
 * This file is responsible for logging out the user.
 * @author Fabe
 */
session_start();
session_unset();
session_destroy();
header("Location: ../auth/login.php");
exit();
?>