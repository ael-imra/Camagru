<?php
require("../config/database.php");
require("../config/setup.php");
require("../outils/check.php");
$url = "../index.php";
if (isset($_GET["action"],$_GET["token"]) && $_GET["action"] == "active" && $_GET["token"] != "")
{
    $stmt = $pdo->prepare("SELECT * FROM `Users` WHERE `Tokenlogin`=:Tokenlogin");
    $stmt->bindParam(":Tokenlogin",$_GET["token"]);
    $stmt->execute();
    $data = $stmt->fetchAll();
    if ($data)
    {
        $stmt = $pdo->prepare("UPDATE Users SET Tokenlogin='1' WHERE Email=:Email");
        $stmt->bindParam("Email",$data[0]["Email"]);
        $stmt->execute();
        $_SESSION["User"] = $data[0]["Username"];
        set_message_success("Seccuss to active account",$url);
    }
    else
        set_message_failed("This Token does not exist.",$url);
}
else if (isset($_POST["active"],$_POST["tokenpass"],$_POST["old_password"],$_POST["new_password"],$_POST["confirme_password"]) && 
        $_POST["active"] == "Reset" && $_POST["tokenpass"] != "" && $_POST["old_password"] != "" && $_POST["new_password"] != "" && $_POST["confirme_password"] != "")
{
    $Password = hash("whirlpool",$_POST["old_password"]);
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Tokenpassword=:Tokenpassword AND Password=:Password");
    $stmt->bindParam(":Tokenpassword",$_POST["tokenpass"]);
    $stmt->bindParam(":Password",$Password);
    $stmt->execute();
    $data = $stmt->fetchAll();
    if ($data)
    {
        if (validator_Password($_POST["new_password"]) && $_POST["new_password"] == $_POST["confirme_password"])
        {
            $Tokenpassword = hash_hmac("sha256",$data[0]["Email"],time());
            $Password = hash("whirlpool",$_POST["new_password"]);
            $stmt = $pdo->prepare("UPDATE Users SET Tokenpassword=:Tokenpassword,Password=:Password WHERE Email=:Email");
            $stmt->bindParam("Email",$data[0]["Email"]);
            $stmt->bindParam("Tokenpassword",$Tokenpassword);
            $stmt->bindParam("Password",$Password);
            $stmt->execute();
            set_message_success("Seccuss to reset password account",$url);
        }
        else
            set_message_failed("New password format wroong OR new password nor eqaul confirme password",$url);
    }
    else
        set_message_failed("Password OR Token does not exist.",$url);
}


?>