<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/Camagru/";
require($Home_dir."config/setup.php");
require($Home_dir."outils/check.php");
if (isset($_SESSION["User"]))
{
  $_SESSION["Redirect"] = $_SERVER["HTTP_REFERER"];
  unset($_SESSION['User']);
}
Redirect("../user/login.php");
?>