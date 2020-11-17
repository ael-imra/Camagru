<?php
if ($_SERVER['REQUEST_URI'] == '/outils/footer.php' )
{
    session_start();
    $_SESSION["failed"] = "Can't Access this page";
    header("location: /index.php");
    exit();
}?>
<footer style="position:fixed;color:white;bottom:0;right:0;transform:rotate(-90deg) translate(50%,100%);font-size:11px;">
    &copy; ael-imra
</footer>