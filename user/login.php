<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT'] . "/Camagru/";
require ($Home_dir . "config/setup.php");
require ($Home_dir . "outils/check.php");
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (isset($_POST["signup"]) && $_POST["signup"] != "")
{
    $_SESSION["script"] = "<script>display_signup();</script>";
    if (isset($_SESSION["csrfToken"]) && $_SESSION["csrfToken"] == $_POST["csrfToken"])
    {
        if (isset($_POST["Username"], $_POST["Email"], $_POST["Password"], $_POST["Confirm_Password"]) && $_POST["Username"] != "" && $_POST["Email"] != "" && $_POST["Password"] != "" && $_POST["Confirm_Password"] == $_POST["Password"])
        {
            if (!check_user_exist("Username", $_POST["Username"], $pdo) && !check_user_exist("Email", $_POST["Email"], $pdo))
            {
                if (!validator_Username($_POST["Username"])) set_message_failed("Username must be :<br/>-between 7 and 25 character", $url);
                else if (!validator_Email($_POST["Email"])) set_message_failed("Email wrong.", $url);
                else if (!validator_Password($_POST["Password"])) set_message_failed("Password wrong.", $url);
                else
                {
                    $Password = hash("whirlpool", $_POST["Password"]);
                    $Tokenlogin = hash('whirlpool', $_POST["Username"] + time());
                    send_mail($Tokenlogin, trim($_POST["Email"]) , "Camagru Activation");
                    $stmt = $pdo->prepare("INSERT INTO `Users`(`Email`, `Username`, `Password`, `Tokenlogin`) VALUES (:Email,:Username,:Password,:Tokenlogin)");
                    $stmt->bindParam(":Email", $_POST["Email"]);
                    $stmt->bindParam(":Username", $_POST["Username"]);
                    $stmt->bindParam(":Password", $Password);
                    $stmt->bindParam(":Tokenlogin", $Tokenlogin);
                    $stmt->execute();
                    set_message_success("To Activate your account Please check your Email", $url);
                }
            }
            else set_message_failed("This user already exist", $url);
        }
        else set_message_failed("Sonthing wrong!", $url);
    }
    else set_message_failed("Wrong CSRF TOKEN", $url);
}
else if (isset($_POST["signin"]) && $_POST["signin"] != "")
{
    $_SESSION["script"] = "<script>display_signin();</script>";
    if (isset($_SESSION["csrfToken"]) && $_SESSION["csrfToken"] == $_POST["csrfToken"])
    {
        if (isset($_POST["Username"], $_POST["Password"]) && $_POST["Username"] != "" && $_POST["Password"] != "")
        {
            $Password = hash("whirlpool", $_POST["Password"]);
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE `Username`=:Username AND `Password`=:Password");
            $stmt->bindParam(":Username", $_POST["Username"]);
            $stmt->bindParam(":Password", $Password);
            $stmt->execute();
            $data = $stmt->fetchAll();
            if ($data)
            {
                if ($data[0]["Tokenlogin"] == "1")
                {
                    $_SESSION["User"] = $data[0]["Username"];
                    if (isset($_SESSION["Redirect"])) Redirect($_SESSION["Redirect"]);
                    else Redirect("../index.php");
                }
                else set_message_failed("Your account is not active, please check your Email.", $url);
            }
            else
                set_message_failed("Username or Password is wrong",$url);
        }
        else
            set_message_failed("Username or Password is empty",$url);
    }
    else set_message_failed("Wrong CSRF TOKEN", $url);
}
if (isset($_POST["reset_Password"]) && $_POST["reset_Password"] != "")
{
    $_SESSION["script"] = "<script>display_reset_Password();</script>";
    if (isset($_SESSION["csrfToken"]) && $_SESSION["csrfToken"] == $_POST["csrfToken"])
    {
        if ($_POST["Email"] != "")
        {
            $Tokenpassword = hash("whirlpool", $_POST["Email"] + time());
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE Email=:Email");
            $stmt->bindParam(":Email", $_POST["Email"]);
            $stmt->execute();
            $data = $stmt->fetchAll();
            if ($data)
            {
                $stmt = $pdo->prepare("UPDATE Users SET Tokenpassword=:Tokenpassword WHERE Email=:Email");
                $stmt->bindParam("Email", $_POST["Email"]);
                $stmt->bindParam("Tokenpassword", $Tokenpassword);
                $stmt->execute();
                send_mail($Tokenpassword, $_POST["Email"], "reset Password");
                set_message_success("To Reset Your Password Please check your Email", $url);
            }
            else set_message_failed("This Email does not exist.", $url);
        }
    }
    else set_message_failed("Wrong CSRF TOKEN", $url);
}
else
{
    $csrfToken = hash('whirlpool', time() + time());
    $_SESSION["csrfToken"] = $csrfToken;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/all.css">
  <link rel="stylesheet" href="../css/style.css">
  <title>Login</title>
</head>

<body style="height:100vh;">
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
    <div id="failed" class="message position-fixed text-center d-flex flex-column" style="--color-message:#FF8788;">
      <h4 class=" w-100" style="--color-message:#FF8788;">
        <i class="fas fa-exclamation-triangle" style="--color-message:#FF8788;"></i>Failed!
      </h4>
      <span style="--color-message:#FF8788;"><?php echo $_SESSION["failed"] ?></span>
    </div>
    <?php
    unset($_SESSION["failed"]);
}
?>
    <?php
if (isset($_SESSION["success"]) && $_SESSION["success"] != "")
{
?>
    <div id="success" class="message position-fixed text-center d-flex flex-column">
      <h4 class=" w-100">
        <i class="fas fa-chevron-circle-down"></i>Success!
      </h4>
      <span><?php echo $_SESSION["success"] ?></span>
    </div>
    <?php
    unset($_SESSION["success"]);
}
?>
    <section class="d-flex flex-row position-relative h-100">
      <div class="login-register d-flex flex-row w-75 br-box border-dark overflow-hidden mt-4 mx-auto position-relative">
        <div id="login-img" class="w-50 d-flex flex-column align-items-center bg-linear">
          <div class="d-flex flex-column justify-content-center w-100 h-100">
            <img class="w-100" src="../img/banner.png">
            <h1 class="w-100 text-center text-dark">Join us</h1>
            <p class="w-100 text-center text-dark">You choose the correct site</p>
          </div>
        </div>
        <div class="w-100 d-flex flex-column justify-content-around mx-auto" style="max-width: 520px">
          <div class="position-absolute switch border rounded-pill border-white mt-4 mr-2" style="right:0;top:0">
            <span class="rounded-pill click_form" onclick="display_signin()">Sign
              In</span>
            <span id="active" class="rounded-pill click_form"
              onclick="display_signup()">Sign Up</span>
          </div>
          <div class="signup d-flex align-items-center h-100">
            <div class="d-flex flex-column align-items-center w-100">
              <h2>Regitration</h2>
              <form class="w-100" action="login.php" method="post">
                <input type="hidden" name="csrfToken" value="<?php echo $csrfToken ?>">
                <div class="mx-auto mt-4 w-75">
                  <input class="w-100" type="Email" name="Email" placeholder="Email" required>
                </div>
                <div class="mx-auto mt-4 w-75">
                  <input class="w-100" type="text" name="Username" placeholder="Username" required>
                </div>
                <div class="mx-auto mt-4 w-75">
                  <input class="w-100" type="Password" name="Password" placeholder="Password" required>
                </div>
                <div class="mx-auto mt-4 w-75">
                  <input class="w-100" type="Password" name="Confirm_Password" placeholder="Confirm Password" required>
                </div>
                <div class="mx-auto mt-5 w-75">
                  <input class="w-100" id="btn-signup" type="submit" name="signup" value="Sign up">
                </div>
                <div class="mx-auto mt-5 w-100 text-center">
                  <span class="text-white">Already have an account? Click <span style="color:#F97042;cursor:pointer;"
                      class="click_form" onclick="switch_l_r(document.querySelectorAll('.click_form')[2])">here</span>
                    to login</span>
                </div>
              </form>
            </div>
          </div>
          <div class="d-flex signin align-items-center  h-100">
            <div class="d-flex flex-column align-items-center w-100">
              <h2>Login</h2>
              <form class="w-100" action="login.php" method="post">
                <input type="hidden" name="csrfToken" value="<?php echo $csrfToken ?>">
                <div class="mx-auto mt-4 w-75">
                  <input class="w-100" type="text" name="Username" placeholder="Username" required>
                </div>
                <div class="mx-auto mt-4 w-75">
                  <input class="w-100" type="Password" name="Password" placeholder="Password" required>
                </div>
                <div class="mx-auto mt-1 w-100 text-right mr-2">
                  <a class="text-decoration-none text-white mr-5" style="cursor: pointer"
                    onclick="display_reset_Password()">Forgot Password?</a>
                </div>
                <div class="mx-auto mt-3 w-75">
                  <input class="w-100" id="btn-signin" type="submit" name="signin" value="Sign in">
                </div>
              </form>
            </div>
          </div>
          <div class="d-flex reset_password align-items-center h-100">
            <div class="d-flex flex-column align-items-center w-100">
              <h2>Reset Password</h2>
              <form class="w-100" action="login.php" method="post">
                <input type="hidden" name="csrfToken" value="<?php echo $csrfToken ?>">
                <div class="mx-auto mt-4 mb-4 w-75">
                  <input class="w-100" type="Email" name="Email" placeholder="Email" required>
                </div>
                <div class="mx-auto mt-5 w-75">
                  <input class="w-100" id="btn-reset_Password" type="submit" name="reset_Password"
                    value="Reset Password">
                </div>
                <div class="mx-auto mt-3 w-75">
                  <p class="text-center w-100" style="cursor: pointer" id="btn-back"
                    onclick="display_signin()">← Back</p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
require ("../outils/footer.php");
?>
  </div>

  <script src="../js/script.js"></script>
  <?php
if (isset($_SESSION["script"])) echo $_SESSION["script"];
else echo "<script>display_signin();</script>"
?>
</body>

</html>
