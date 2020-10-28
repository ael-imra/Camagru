<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/Camagru/";
require($Home_dir."config/setup.php");
require($Home_dir."outils/check.php");
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (!isset($_SESSION["User"]) || !check_user_exist("Username",$_SESSION["User"],$pdo))
  Redirect("../user/login.php");
function deleteFromTable($pdo,$table,$postid)
{
    $stmt = $pdo->prepare("DELETE FROM `$table` WHERE `PostId` = :postid");
    $stmt->bindParam(":postid",$postid);
    $stmt->execute();
}
if (isset($_POST["postid"]) && $_POST["postid"] != "")
{
    if (explode('post_',$_POST["postid"])[1])
    {
        $postid = explode('post_',$_POST["postid"])[1];
        $stmt = $pdo->prepare("SELECT * FROM `Post` WHERE `PostId` = :postid AND `UserIdOwner`=:UserIdOwner");
        $stmt->bindParam(":postid",$postid);
        $stmt->bindParam(":UserIdOwner",$_SESSION["User"]);
        $stmt->execute();
        $data = $stmt->fetchAll();
        if ($data)
        {
            if (file_exists("../".$data[0]["Image"]))
                unlink("../".$data[0]["Image"]);
            deleteFromTable($pdo,"Post",$postid);
            deleteFromTable($pdo,"Comment","post_".$postid);
            deleteFromTable($pdo,"Like","post_".$postid);
        }
        else
            set_message_failed("You don't have permission to delete this post!",$url);
    }
    else
        set_message_failed("You don't have permission to delete this post!",$url);
}
else
    set_message_failed("Can't Access this page","/Camagru/index.php");

?>