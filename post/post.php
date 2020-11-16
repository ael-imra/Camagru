<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/";
require($Home_dir."config/setup.php");
require($Home_dir."outils/check.php");
if ($_SERVER['REQUEST_URI'] == '/post/post.php' )
    set_message_failed("Can't Access this page","/index.php");
function getDateTime($Interval)
{
    if ($Interval->y > 0)
        return ($Interval->y." year".(($Interval->y > 1)?"s":"")." ago");
    else if ($Interval->m > 0)
        return ($Interval->m." month".(($Interval->m > 1)?"s":"")." ago");
    else if ($Interval->d > 0)
        return ($Interval->d." day".(($Interval->d > 1)?"s":"")." ago");
    else if ($Interval->h > 0)
        return ($Interval->h." hour".(($Interval->h > 1)?"s":"")." ago");
    else if ($Interval->i > 0)
        return ($Interval->i." minute".(($Interval->i > 1)?"s":"")." ago");
    else if ($Interval->s > 0)
        return ($Interval->s." second".(($Interval->s > 1)?"s":"")." ago");
}
function getDataUser($pdo,$owner){
    $stmt = $pdo->prepare("SELECT `Image` FROM `Users` WHERE `Username`=:username");
    $stmt->bindParam(":username",$owner);
    $stmt->execute();
    return ($stmt->fetchAll());
}
function getDataLike($pdo,$user,$postid){
    $stmt = $pdo->prepare("SELECT * FROM `Like` WHERE `UserAction`=:UserAction AND `PostId` = :PostId AND `Likeactive`=1");
    $stmt->bindParam(":UserAction",$user);
    $stmt->bindParam("PostId",$postid);
    $stmt->execute();
    return($stmt->fetchAll());
}
function getAllPost($pdo){
    $start = 0;
    $end = 16;
    if (isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0)
    {
        $start = (16 * (int)($_GET["page"] - 1));
        $end = (16 * (int)$_GET["page"]);
    }
    if (isset($_GET["search"]))
    {
        $search = "%".$_GET["search"]."%";
        $stmt = $pdo->prepare("SELECT * FROM `Post` WHERE `Date_create` LIKE :search OR  `UserIdOwner` LIKE :search ORDER BY `Date_create` DESC LIMIT $start,$end");
        $stmt->bindParam(":search",$search);
    }
    else
        $stmt = $pdo->prepare("SELECT * FROM `Post` ORDER BY `Date_create` DESC LIMIT $start,$end");
    $stmt->execute();
    $data_post = $stmt->fetchAll();
    $div = "";
    foreach($data_post as $value)
    {
        $postid = "post_".$value["PostId"];
        $data_user = getDataUser($pdo,$value["UserIdOwner"]);
        $like_count = get_count("Like",$pdo,$postid);
        $comment_count = get_count("Comment",$pdo,$postid);
        $stmt = $pdo->prepare("SELECT * FROM `Like` WHERE `UserAction`=:UserAction AND `PostId`=:PostId AND `Likeactive` = '1'");
        $stmt->bindParam(":UserAction",$_SESSION["User"]);
        $stmt->bindParam(":PostId",$postid);
        $stmt->execute();
        $is_like = ($stmt->fetchAll()) ? "color:#167db9!important" : "";
        $date_post = new DateTime($value["Date_create"]);
        $date_now = new DateTime(date("Y-m-d H:i:s"));
        $date_post = getDateTime($date_post->diff($date_now));
        $div .= 
                '<div id="'.$postid.'" class="post">'.PHP_EOL.
                    '<div class="delete_box d-none flex-column justify-content-between w-100">'.PHP_EOL.
                        '<div style="margin: 40px;text-align: center;">Are you sure?</div>'.PHP_EOL.
                        '<div class="w-100">'.PHP_EOL.
                            '<button id="delete_post" onclick="deletePost(\''.$postid.'\')">Yes</button>'.PHP_EOL.
                            '<button onclick="displayDeleteBox(\''.$postid.'\')">No</button>'.PHP_EOL.
                        '</div>'.PHP_EOL.
                    '</div>'.PHP_EOL.
                    '<div class="post_box">'.PHP_EOL.
                        '<div class="w-100 d-flex flex-row ml-auto align-items-center mb-1">'.PHP_EOL.
                            '<img class="mr-1" src="/'.$data_user[0]["Image"].'" alt="Profile">'.PHP_EOL.
                            '<span id="owner" class="d-block" style="font-size: 12px;">'.$value["UserIdOwner"].'</span>'.PHP_EOL.
                            '<span class="d-block ml-auto" style="font-size: 10px;">'.$date_post.'</span>'.PHP_EOL.
                            (($value["UserIdOwner"] == $_SESSION["User"])?'<i class="far fa-trash-alt ml-2" style="color: red!important;" title="delete" onclick="displayDeleteBox(\''.$postid.'\')"></i>'.PHP_EOL : '').
                        '</div>'.PHP_EOL.
                        '<img class="w-100 mb-2" style="height:193px;" src="/'.$value["Image"].'" onclick="getPost(\''.$postid.'\')">'.PHP_EOL.
                        '<div class="d-flex flex-row align-items-center">'.PHP_EOL.
                            '<div class="text-left m-1 post_btn" onclick="like_click(\''.$postid.'\')">'.PHP_EOL.
                                '<i class="fas fa-thumbs-up" style="'.$is_like.'"></i>'.PHP_EOL.
                                '<span class="w-50 like_txt" style="'.$is_like.'">Like</span>'.PHP_EOL.
                            '</div>'.PHP_EOL.
                            '<div class="text-left m-1 post_btn" onclick="hide_show_comment(\''.$postid.'\')">'.PHP_EOL.
                                '<i class="fas fa-comment"></i>'.PHP_EOL.
                                '<span class="w-50">Comment</span>'.PHP_EOL.
                            '</div>'.PHP_EOL.
                            '<div class="flex-grow-1 text-right" style="font-size: 12px;">'.PHP_EOL.
                                '<span id="like_txt">'.$like_count.' like'.(($like_count > 1) ? "s":"").'</span>'.PHP_EOL.
                                '<span id="comment_txt">'.$comment_count.' Comment'.(($comment_count > 1) ? "s":"").'</span>'.PHP_EOL.
                            '</div>'.PHP_EOL.
                        '</div>'.PHP_EOL.
                        '<div class="comment_text d-none w-100 mt-1 mb-1">'.PHP_EOL.
                            '<input class="comment_txt" style="box-shadow:none" type="text" name="comment_text">'.PHP_EOL.
                            '<button class="comment_btn" onclick="Comment_click(\''.$postid.'\')">Comment</button>'.PHP_EOL.
                        '</div>'.PHP_EOL.
                    '</div>'.PHP_EOL.
                '</div>'.PHP_EOL;
    }
    return($div);
}
function getPagination($pdo)
{
    $div = "";
    if (isset($_GET["search"]) && $_GET["search"] != "")
    {
        $search = "%".$_GET["search"]."%";
        $stmt = $pdo->prepare("SELECT COUNT(*) AS 'Count' FROM `Post` WHERE `Date_create` LIKE :search OR  `UserIdOwner` LIKE :search");
        $stmt->bindParam(":search",$search);
    }
    else
        $stmt = $pdo->prepare("SELECT COUNT(*) AS 'Count' FROM `Post`");
    $stmt->execute();
    $data = $stmt->fetchAll();
    if ($data[0]["Count"] >= 1)
    {
        $i = 1;
        $count = $data[0]["Count"] / 16;
        $count = (int)($count + (((int)$count < $count) ? 1 : 0));
        if (isset($_GET["search"]) && $_GET["search"] != "")
            $div .= "<div><input type='hidden' name='search' value='".htmlspecialchars($_GET["search"])."'></div>";
        while($i <= $count)
        {
            $style = "";
            if (isset($_GET["page"]) && (int)$_GET["page"] == $i)
                $style = "background-color: #353535";
            $div .= "<div><input type='submit' style='$style' name='page' value='$i'></div>";
            $i++;
        }
    }
    return($div);
}

?>