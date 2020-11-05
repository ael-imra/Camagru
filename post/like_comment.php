<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/Camagru/";
require($Home_dir."config/setup.php");
require($Home_dir."outils/check.php");
function getUserData($pdo,$owner){
    $stmt = $pdo->prepare("SELECT * FROM `Users` WHERE `Username`=:username AND `Notification` = 1");
    $stmt->bindParam(":username",$owner);
    $stmt->execute();
    return ($stmt->fetchAll());
}
if (isset($_SESSION["User"]) && !isset($_POST["all_comment"],$_POST["postid"]))
{
    if (isset($_POST["submit"]) && $_POST["submit"] == "like")
    {
        if (isset($_POST["postid"],$_POST["owner"]))
        {
            $stmt = $pdo->prepare("SELECT * FROM `Like` WHERE `PostId`=:postid AND `UserAction`=:UserAction AND `UserIdOwner`=:useridowner");
            $stmt->bindParam(":postid",$_POST["postid"]);
            $stmt->bindParam(":useridowner",$_POST["owner"]);
            $stmt->bindParam(":UserAction",$_SESSION["User"]);
            $stmt->execute();
            $data = $stmt->fetchAll();
            if ($data)
            {
                if ($data[0]["Likeactive"] == 1)
                {
                    $stmt = $pdo->prepare("UPDATE `Like` SET `Likeactive` = 0 WHERE `PostId`=:postid AND `UserAction`=:UserAction AND `UserIdOwner`=:useridowner");
                    $stmt->bindParam(":postid",$_POST["postid"]);
                    $stmt->bindParam(":useridowner",$_POST["owner"]);
                    $stmt->bindParam(":UserAction",$_SESSION["User"]);
                    $stmt->execute();
                    echo "Like:-".get_count("Like",$pdo,$_POST["postid"]);
                }
                else
                {
                    $stmt = $pdo->prepare("UPDATE `Like` SET `Likeactive` = 1 WHERE `PostId`=:postid AND `UserAction`=:UserAction AND `UserIdOwner`=:useridowner");
                    $stmt->bindParam(":postid",$_POST["postid"]);
                    $stmt->bindParam(":useridowner",$_POST["owner"]);
                    $stmt->bindParam(":UserAction",$_SESSION["User"]);
                    $stmt->execute();
                    echo "Like:-".get_count("Like",$pdo,$_POST["postid"]);
                }
            }
            else if (count(explode('post_',$_POST["postid"])) == 2)
            {
                $postid = explode('post_',$_POST["postid"])[1];
                $stmt = $pdo->prepare("SELECT * FROM `Post` WHERE `PostId`=:postid AND `UserIdOwner`=:useridowner");
                $stmt->bindParam(":postid",$postid);
                $stmt->bindParam(":useridowner",$_POST["owner"]);
                $stmt->execute();
                $data = $stmt->fetchAll();
                if ($data)
                {
                    echo "<script>alert('".$value."')</script>";
                    $stmt = $pdo->prepare("INSERT INTO `Like`(`UserIdOwner`, `PostId`, `UserAction`) VALUES (:useridowner,:postid,:UserAction)");
                    $stmt->bindParam(":postid",$_POST["postid"]);
                    $stmt->bindParam(":useridowner",$_POST["owner"]);
                    $stmt->bindParam(":UserAction",$_SESSION["User"]);
                    $stmt->execute();
                    $data_user = getUserData($pdo,$_POST["owner"]);
                    $message = $_SESSION["User"]." like your post";
                    echo "Like:-".get_count("Like",$pdo,$_POST["postid"]);
                    // if ($data_user)
                    //     send_mail($message,$data_user[0]["Email"],"Notification");
                }
                else 
                    set_message_failed("Something Wroong!",$url);
            }
        }
        else
            set_message_failed("Something Wroong!",$url);
    }
    else if (isset($_POST["submit"]) && $_POST["submit"] == "comment")
    {
        if (isset($_POST["postid"]) && isset($_POST["owner"]) && isset($_POST["content"]) && count(explode('post_',$_POST["postid"])) == 2 && str_replace(' ','',$_POST["content"]))
        {
            $postid = explode('post_',$_POST["postid"])[1];
            $stmt = $pdo->prepare("SELECT * FROM `Post` WHERE `PostId`=:postid AND `UserIdOwner`=:useridowner");
            $stmt->bindParam(":postid",$postid);
            $stmt->bindParam(":useridowner",$_POST["owner"]);
            $stmt->execute();
            $data = $stmt->fetchAll();
            if ($data)
            {
                $stmt = $pdo->prepare("INSERT INTO `Comment`(`UserIdOwner`, `PostId`, `UserAction`, `Content`) VALUES (:useridowner,:postid,:UserAction,:content)");
                $stmt->bindParam(":postid",$_POST["postid"]);
                $stmt->bindParam(":useridowner",$_POST["owner"]);
                $stmt->bindParam(":UserAction",$_SESSION["User"]);
                $stmt->bindParam(":content",htmlspecialchars($_POST["content"]));
                $stmt->execute();
                $data_user = getUserData($pdo,$_POST["owner"]);
                $message = $_POST["owner"]." comment your post";
                echo "Comment:-".get_count("Comment",$pdo,$_POST["postid"]);
                // if ($data_user)
                //     send_mail($message,$data_user[0]["Email"],"Notification");
            }
            else 
                set_message_failed("Something Wroong!",$url);
        }
        else 
            set_message_failed("Something Wroong!",$url);
    }
    else if (isset($_POST["submit"]) && $_POST["submit"] == "delete")
    {
        if (isset($_POST["commentid"]) && $_POST["commentid"] != "")
        {
            $commentid = $_POST["commentid"];
            $stmt = $pdo->prepare("SELECT * FROM `Comment` WHERE `CommentId`=:CommentId AND `UserAction`=:UserAction");
            $stmt->bindParam(":CommentId",$commentid);
            $stmt->bindParam(":UserAction",$_SESSION["User"]);
            $stmt->execute();
            $data = $stmt->fetchAll();
            if ($data)
            {
                $stmt = $pdo->prepare("DELETE FROM `Comment` WHERE `CommentId`=:CommentId");
                $stmt->bindParam(":CommentId",$commentid);
                $stmt->execute();
            }
            else 
                set_message_failed("Something Wroong!",$url);
        }
        else 
            set_message_failed("Something Wroong!",$url);
    }
    else if (isset($_GET["not"]))
    {
        $stmt = $pdo->prepare("UPDATE `Like` SET `Notification` = 1 WHERE 1");
        $stmt->execute();
        $stmt = $pdo->prepare("UPDATE `Comment` SET `Notification` = 1 WHERE 1");
        $stmt->execute();
    }
    else
        Redirect("/Camagru/index.php");
}
else if (isset($_POST["all_comment"],$_POST["postid"]) && $_POST["all_comment"] == "true" && $_POST["postid"]!="")
{
    $stmt = $pdo->prepare("SELECT * FROM `Comment` WHERE `PostId`=:postid ORDER by `CommentId` DESC");
    $stmt->bindParam(":postid",$_POST["postid"]);
    $stmt->execute();
    $data = $stmt->fetchAll();
    $div = "";
    if ($data)
    {
        foreach($data as $value)
        {
            $stmt = $pdo->prepare("SELECT * FROM `Users` WHERE `Username`=:Username");
            $stmt->bindParam(":Username",$value["UserAction"]);
            $stmt->execute();
            $data_user = $stmt->fetchAll();
            $div .= '<div id="Comment_'.$value["CommentId"].'" class="d-flex flex-row w-100 mb-1" style="padding: 7px;border: 1px solid #5f5d5d;">
                    <div class="delete_comment_box w-100 d-none flex-column text-center">
                        <div>Are you sure?</div>
                        <div class="w-100 d-flex flex-row">
                            <button class="w-50 text-dark" onclick="deleteComment(\''.$value["CommentId"].'\',\''.$_POST["postid"].'\')">Delete</button>
                            <button class="w-50 text-dark" onclick="displayDeleteComment(\'Comment_'.$value["CommentId"].'\')">Cancel</button>
                        </div>
                    </div>
                    <div>
                        <img src="/Camagru/'.$data_user[0]["Image"].'" style="width: 45px;height: 45px;border-radius: 50%;">
                    </div>
                    <div class="d-flex flex-column w-100">
                        <div class="d-flex flex-row justify-content-between">
                            <div style="font-weight: bold;margin-left: 5px;">'.$value["UserAction"].'</div>
                            '.((isset($_SESSION["User"]) && $_SESSION["User"] == $value["UserAction"])?"<div><a style='font-size:12px;cursor:pointer;' onclick=\"displayDeleteComment('Comment_".$value["CommentId"]."')\">Delete</a></div>":"").'
                        </div>
                        <div class="d-flex flex-row justify-content-between">
                            <div class=w-100 text-center ml-3 mr-3" style="line-break: anywhere;text-align:center;">'.$value["Content"].'</div>
                        </div>
                    </div>
                </div>';
        }
        echo $div;
    }
    else 
        echo "error";
}
else
    echo "error";