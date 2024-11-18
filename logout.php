<?php
session_start();
session_destroy();
header("Location: opros.php"); 
exit;
?>
