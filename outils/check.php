<?php
function Redirect($url)
{
    echo("<script>location.href = '".$url."';</script>");
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
    $url = "https://192.168.99.102:8088/Camagru";
    $Email_to = $Email;
    if ($Email_subject == "Camagru Activation")
        $Email_message = "<h1>You've successfully signed up</h1>".
                "<p>Please complete sign up by clicking the following link:".
                "<a href='".$url."/user/active.php?action=active&token=".$token."'>Click here<a><br>".
                "Thanks, Camagru</p>";
    else if ($Email_subject == "Notification")
        $Email_message = "<h1>".$token."</h1><br>".
                    "Thanks, Camagru</p>";
    else
    {
        $Email_message = 
        '<form style="width:35%;text-align:center;" action="'.$url.'/user/active.php" method="POST" target="_blank">'.
        '<img style="width: 60px;height: 30px;" src="https://i.ibb.co/bPF20LX/CAMAGRU-LOGO.png">'.
        '<p style="font-size:16px;text-align: center;width: 100%;">Reset Password</p>'.
        '<input type="hidden" name="tokenpass" value="'.$token.'">'.
        '<input type="hidden" name="csrfToken" value="'.$csrfToken.'">'.
        '<div style="width:100%;display: flex;flex-flow: row;"><label style="width:50%; margin: 4px;">Old password</label><input style="width:50%;" type="password" name="old_password" required></div>'.
        '<div style="width:100%;display: flex;flex-flow: row;"><label style="width:50%; margin: 4px;">New password</label><input style="width:50%;" type="password" name="new_password" required></div>'.
        '<div style="width:100%;display: flex;flex-flow: row;"><label style="width:50%; margin: 4px;">Confirme password</label><input style="width:50%;" type="password" name="confirme_password" required></div>'.
        '<input style="margin: 5px;" type="submit" name="active" value="Reset">'.
        '<hr>'.
        '<p  style="width:100%;text-align:right;">&copy;Camagru</p>'.
        '</form>';
    }
    $header = "Content-type: text/html";
    if(!mail($Email_to, $Email_subject, $Email_message, $header))
        set_message_failed("Faild to sent message to Email",$_SERVER["HTTP_REFERER"]);
}
require("validate.php");
?>