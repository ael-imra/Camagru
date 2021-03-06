<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/";
require($Home_dir."config/setup.php");
require($Home_dir."outils/check.php");
if(isset($_POST["image_data"]) && $_POST["image_data"] != "" && isset($_SESSION["csrfToken"],$_POST["csrfToken"]) && $_SESSION["csrfToken"] == $_POST["csrfToken"])
{
    if (isset($_SESSION["csrfToken"]))
        unset($_SESSION["csrfToken"]);
    $type_image = array("image/png","image/jpg","image/jpeg","image/gif");
    $img_base64 = str_replace(' ', '+', $_POST["image_data"]);
    $imagesize = getimagesize($img_base64);
    if($imagesize && array_search(strtolower($imagesize["mime"]),$type_image) > -1)
    {
        if (!file_exists($Home_dir.'/post/img/'))
        {
            chmod($Home_dir,0777);
            chmod($Home_dir.'/post/',0777);
            mkdir($Home_dir.'/post/img');
        }
        chmod($Home_dir.'/post/img',0777);
        $path = 'img/post_'.time().'.'.explode("image/",$imagesize["mime"])[1];
        $file = file_get_contents($img_base64);
        file_put_contents($path, $file);
        $path = "post/".$path;
        $date = date("Y-m-d H:i:s");
        $stmt = $pdo->prepare("INSERT INTO `Post`(`UserIdOwner`, `Image`, `Date_create`) VALUES (:user,:image,:date)");
        $stmt->bindParam(":user",$_SESSION["User"]);
        $stmt->bindParam(":image",$path);
        $stmt->bindParam(":date",$date);
        $stmt->execute();
    }
    else
        echo "Failed";
}
else
    set_message_failed("Can't Access this page","/index.php");
?>