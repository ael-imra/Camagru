<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/";
require($Home_dir."config/setup.php");
require($Home_dir."outils/check.php");
$url = "./login.php";

if (isset($_GET["action"],$_GET["token"],$_GET["Email"]) && $_GET["action"] == "active" && $_GET["Email"] != "" && $_GET["token"] != "" && $_GET["token"] != '1')
{
    $stmt = $pdo->prepare("SELECT * FROM `Users` WHERE `Tokenlogin`=:Tokenlogin AND `Email`=:Email");
    $stmt->bindParam(":Tokenlogin",$_GET["token"]);
    $stmt->bindParam(":Email",$_GET["Email"]);
    $stmt->execute();
    $data = $stmt->fetchAll();
    if ($data)
    {
        $stmt = $pdo->prepare("UPDATE Users SET Tokenlogin='1' WHERE Email=:Email");
        $stmt->bindParam("Email",$data[0]["Email"]);
        $stmt->execute();
        $_SESSION["script"] = "<script>display_signin();</script>";
        set_message_success("Seccuss to active account",$url);
    }
    else
        set_message_failed("This Token does not exist.",$url);
}
else if (isset($_POST["active"],$_POST["Email"],$_POST["tokenpass"],$_POST["new_password"],$_POST["confirm_pass"]) && 
        $_POST["active"] == "Reset" && $_POST["tokenpass"] != "" && $_POST["new_password"] != "" && $_POST["confirm_pass"] != "" && $_POST["Email"] != "")
{
    if (isset($_SESSION["csrfToken"]) && $_SESSION["csrfToken"] == $_POST["csrfToken"])
    {
        if (isset($_SESSION["csrfToken"]))
            unset($_SESSION["csrfToken"]);
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE Tokenpassword=:Tokenpassword AND `Email`=:Email");
        $stmt->bindParam(":Tokenpassword",$_POST["tokenpass"]);
        $stmt->bindParam(":Email",$_POST["Email"]);
        $stmt->execute();
        $data = $stmt->fetchAll();
        if ($data)
        {
            if (validator_Password($_POST["new_password"]) && $_POST["new_password"] == $_POST["confirm_pass"])
            {
                $Tokenpassword = hash("whirlpool",$data[0]["Email"].time());
                $Password = hash("whirlpool",$_POST["new_password"]);
                $stmt = $pdo->prepare("UPDATE Users SET Tokenpassword=:Tokenpassword,Password=:Password WHERE Email=:Email");
                $stmt->bindParam("Email",$data[0]["Email"]);
                $stmt->bindParam("Tokenpassword",$Tokenpassword);
                $stmt->bindParam("Password",$Password);
                $stmt->execute();
                $_SESSION["script"] = "<script>display_signin();</script>";
                set_message_success("Seccuss to reset password account",$url);
            }
            else
            {
                $_SESSION["failed"] = "New password format wroong OR new password nor eqaul confirme password";
                echo "<script>history.back();</script>";
                exit();
            }
        }
        else
            set_message_failed("Password OR Token does not exist.",$url);
    }
    else
        Redirect("/user/login.php");
}
else
    set_message_failed("Wrong CSRF TOKEN", $url);

?>