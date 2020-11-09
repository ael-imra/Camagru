<?php
session_start();
if ($_SERVER['REQUEST_URI'] == '/outils/check.php' )
{
    $_SESSION["failed"] = "Can't Access this page";
    header("location: /index.php");
    exit();
}
function Redirect($url)
{
    header("location: ".$url);
    exit();
}
function set_message_failed($msg,$url){
    $_SESSION["failed"] = $msg;
    Redirect($url);
}
function set_message_success($msg,$url){
    $_SESSION["success"] = $msg;
    Redirect($url);
}
function get_count($table,$pdo,$post_id = null)
{
    $count = 0;
    if ($post_id != null)
    {
        if($table == 'Like')
            $stmt = $pdo->prepare("SELECT COUNT(*) AS 'count' FROM `Like` WHERE PostId=:PostId AND `Likeactive`=1");
        else
            $stmt = $pdo->prepare("SELECT COUNT(*) AS 'count' FROM `".$table."` WHERE PostId=:PostId");
        $stmt->bindParam(":PostId",$post_id);
        $stmt->execute();
        $data = $stmt->fetchAll();
        if ($data)
            $count = $data[0]["count"];
    }
    else
    {
        if ($table == "Like")
            $stmt = $pdo->prepare("SELECT COUNT(*) AS 'count' FROM `Like` WHERE `UserIdOwner`=:Username AND `Likeactive`=1");
        else
            $stmt = $pdo->prepare("SELECT COUNT(*) AS 'count' FROM `".$table."` WHERE `UserIdOwner`=:Username");
        $stmt->bindParam(":Username",$_SESSION["User"]);
        $stmt->execute();
        $data = $stmt->fetchAll();
        if ($data)
            $count = $data[0]["count"];
    }
    return($count);
}
function send_mail($token,$Email,$Email_subject,$csrfToken = null)
{
    $url = "https://".$_SERVER['HTTP_HOST'];
    $Email_to = $Email;
    if ($Email_subject == "Camagru Activation")
        $Email_message = "<h1>You've successfully signed up</h1>".
                "<p>Please complete sign up by clicking the following link:".
                "<a href='".$url."/user/active.php?action=active&token=".$token."&Email=".$Email."'>Click here<a><br>".
                "Thanks, Camagru</p>";
    else if ($Email_subject == "Notification")
        $Email_message = "<h1>".$token."</h1><br>".
                    "Thanks, Camagru</p>";
    else
    {
        $Email_message = "<h1>Reset Password</h1>".
        "<p>Click on link to Reset Password:".
        "<a href='".$url."/user/reset_password.php?tokenpass=".$token."&Email=".$Email."'>Click here<a><br>".
        "Thanks, Camagru</p>";
    }
    $header = "Content-type: text/html";
    if(!mail($Email_to, $Email_subject, $Email_message, $header))
        set_message_failed("Faild to sent message to Email",$_SERVER["HTTP_REFERER"]);
}
require("validate.php");
?>