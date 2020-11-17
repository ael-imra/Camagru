<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/";
require($Home_dir."config/setup.php");
require($Home_dir."outils/check.php");
$url = "./login.php";
if (isset($_GET["tokenpass"],$_GET["Email"]) && $_GET["tokenpass"]!="" && $_GET["Email"]!="")
{
  $stmt = $pdo->prepare("SELECT * FROM Users WHERE Tokenpassword=:Tokenpassword");
  $stmt->bindParam(":Tokenpassword",$_GET["tokenpass"]);
  $stmt->execute();
  $data = $stmt->fetchAll();
  if(!$data)
    set_message_failed("Invalide Token !",$url);
}
else
  set_message_failed("You can't access this page!",$url);
if (isset($_SESSION["csrfToken"]))
    unset($_SESSION["csrfToken"]);
$csrfToken = hash('whirlpool', time().time());
$_SESSION["csrfToken"] = $csrfToken;
  ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/all.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <div class="d-flex flex-column m-0 p-0 h-100">
    <header id="first-menu">
      <div class="mx-auto">
        <a class="Homelink" href="../index.php"><img src="../img/logo.png"></a>
      </div>
    </header>
    <?php
      if (isset($_SESSION["failed"]) && $_SESSION["failed"] != "")
      {
    ?>
    <div id="failed" class="message position-fixed text-center d-flex flex-column" style="background-color:#FF8788;">
      <h4 class=" w-100" style="color:#ff8788;position: relative;">
        <i class="fas fa-chevron-circle-down" style="color:#ff8788"></i>
        <span style="color: #ff8788;">Failed!</span>
        <span style="color: #ff8788;position: absolute;right: 15px;font-size: 25px;cursor:pointer;"
          onclick="document.getElementById('failed').className = document.getElementById('failed').className.replace('d-flex','d-none');">X</span>
      </h4>
      <span style="color:white;"><?php echo $_SESSION["failed"] ?></span>
    </div>
    <?php
        unset($_SESSION["failed"]);
      }
    ?>
    <?php
      if (isset($_SESSION["success"]) && $_SESSION["success"] != "")
      {
    ?>
    <div id="success" class="message position-fixed text-center d-flex flex-column" style="background-color:#b1e17e">
      <h4 class=" w-100" style="color:#b1e17e;position: relative;">
        <i class="fas fa-chevron-circle-down" style="color:#b1e17e"></i>
        <span style="color: #b1e17e;">Success!</span>
        <span style="color: #b1e17e;position: absolute;right: 15px;font-size: 25px;cursor:pointer;"
          onclick="document.getElementById('success').className = document.getElementById('success').className.replace('d-flex','d-none');">X</span>
      </h4>
      <span><?php echo $_SESSION["success"] ?></span>
    </div>
    <?php
      unset($_SESSION["success"]);
      }
    ?>
    <form class="mt-5 p-3 d-flex flex-column align-items-center justify-content-center mx-auto"
      style="width:90%;max-width:500px;height:300px;background-color:#252525;border-radius:8px"
      action="<?php echo './active.php'?>" method="POST">
      <input type="hidden" name="csrfToken" value="<?php echo $csrfToken ?>">
      <p style="font-size:20px;text-align: center;width: 100%;color:white;margin-bottom:55px">Reset Password</p>
      <input type="hidden" name="tokenpass" value="<?php echo $_GET["tokenpass"];?>">
      <input type="hidden" name="Email" value="<?php echo $_GET["Email"];?>">
      <div style="width:100%;display: flex;flex-flow: row;margin:5px 0;"><label
          style="width:40%; text-align:center;color:white;font-size:14px;margin:auto 0;">New password</label><input
          style="width:50%;border-radius:10px" type="password" name="new_password" required></div>
      <div style="width:100%;display: flex;flex-flow: row;margin:5px 0;"><label
          style="width:40%; text-align:center;color:white;font-size:14px;margin:auto 0;">Confirme password</label><input
          style="width:50%;border-radius:10px" type="password" name="confirm_pass" required></div>
      <input style="margin: 25px 0;width:150px;height:35px;" id="btn-reset_Password" type="submit" name="active"
        value="Reset">
    </form>
    <?php require("../outils/footer.php");?>
    <script src="../js/script.js"></script>
  </div>
</body>

</html>