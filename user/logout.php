<?php
require("../config/database.php");
require("../config/setup.php");
require("../outils/check.php");
$_SESSION["Redirect"] = $_SERVER["HTTP_REFERER"];
unset($_SESSION['User']);
Redirect("../user/login.php");
?>