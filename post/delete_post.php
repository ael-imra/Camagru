<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/";
require($Home_dir."config/setup.php");
require($Home_dir."outils/check.php");
$url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if (!isset($_SESSION["User"]) || !check_user_exist("Username",$_SESSION["User"],$pdo))
  Redirect("../user/login.php");
if (!isset($_SESSION["csrfToken"],$_POST["csrfToken"]) || $_SESSION["csrfToken"] != $_POST["csrfToken"])
{
    set_message_failed("Can't Access this page","/index.php");
    exit();
}
function deleteFromTable($pdo,$table,$postid)
{
    $stmt = $pdo->prepare("DELETE FROM `$table` WHERE `PostId` = :postid");
    $stmt->bindParam(":postid",$postid);
    $stmt->execute();
}
if (isset($_POST["postid"]) && $_POST["postid"] != "")
{
    if (count(explode('post_',$_POST["postid"]))>=2)
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
    set_message_failed("Can't Access this page","/index.php");

?>