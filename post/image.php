<?php
require("../config/database.php");
require("../config/setup.php");
require("../outils/check.php");
if(isset($_POST["image_data"]) && $_POST["image_data"] != "")
{
    $type_image = "";
    if (explode('data:image/',$_POST["image_data"])[1] && explode(';',explode('data:image/',$_POST["image_data"])[1])[0])
        $type_image = explode(';',explode('data:image/',$_POST["image_data"])[1])[0];
    if ($_SERVER["CONTENT_LENGTH"] < 11000000 && ($type_image == "png" || $type_image == "jpg" || $type_image == "jpeg" || $type_image == "gif"))
    {
        $img_base64 = str_replace(' ', '+', $_POST["image_data"]);
        $size = getimagesize('data://application/octet-stream;base64,'.str_replace('data:image/'.$type_image.';base64,', '', $img_base64));
        if ($size)
        {
            $path = 'img/post_'.time().'.'.$type_image;
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
        echo "Failed";
}
else
    echo "Failed";
?>