<?php
if ($_SERVER['REQUEST_URI'] == '/Camagru/outils/footer.php' )
{
    session_start();
    $_SESSION["failed"] = "Can't Access this page";
  echo("<script>location.href = '/Camagru/index.php';</script>");
  exit();
}?>
<footer style="position:fixed;color:white;bottom:0;right:0;transform:rotate(-90deg) translate(50%,100%);font-size:11px;">
    &copy; ael-imra
</footer>