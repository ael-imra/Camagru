<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/Camagru/";
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
        set_message_success("Seccuss to active account",$url);
    }
    else
        set_message_failed("This Token does not exist.",$url);
}
else if (isset($_POST["active"],$_POST["Email"],$_POST["tokenpass"],$_POST["new_password"],$_POST["confirm_pass"]) && 
        $_POST["active"] == "Reset" && $_POST["tokenpass"] != "" && $_POST["new_password"] != "" && $_POST["confirm_pass"] != "" && $_POST["Email"] != "")
{
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Tokenpassword=:Tokenpassword");
    $stmt->bindParam(":Tokenpassword",$_POST["tokenpass"]);
    $stmt->execute();
    $data = $stmt->fetchAll();
    if ($data)
    {
        if (validator_Password($_POST["new_password"]) && $_POST["new_password"] == $_POST["confirm_pass"])
        {
            $Tokenpassword = hash("whirlpool",$data[0]["Email"]+time());
            $Password = hash("whirlpool",$_POST["new_password"]);
            $stmt = $pdo->prepare("UPDATE Users SET Tokenpassword=:Tokenpassword,Password=:Password WHERE Email=:Email");
            $stmt->bindParam("Email",$data[0]["Email"]);
            $stmt->bindParam("Tokenpassword",$Tokenpassword);
            $stmt->bindParam("Password",$Password);
            $stmt->execute();
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
    Redirect("/Camagru/user/login.php");


?>