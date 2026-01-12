<?php
session_start();
session_destroy(); // On efface tout
header("Location: index.php");
exit();
?>