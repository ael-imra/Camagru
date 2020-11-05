<?php
  if ($_SERVER['REQUEST_URI'] == '/Camagru/outils/menu.php' )
  {
    session_start();
    $_SESSION["failed"] = "Can't Access this page";
    header("location: /Camagru/index.php");
    exit();
  }
  function getNotification($pdo,$table)
  {
      $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE `UserIdOwner`=:UserIdOwner AND `Notification`=0");
      $stmt->bindParam(":UserIdOwner",$_SESSION["User"]);
      $stmt->execute();
      $data = $stmt->fetchAll();
      $notification = "";
      if ($data)
      {
          foreach($data as $value)
              $notification .= "<div>".$value["UserAction"]." ".$table." Your Post</div>".PHP_EOL;
      }
      return $notification;
  }
  if (isset($_SESSION["User"]))
  {
    $stmt = $pdo->prepare("SELECT * FROM `Users` WHERE Username=:Username");
    $stmt->bindParam(":Username",$_SESSION["User"]);
    $stmt->execute();
    $data = $stmt->fetchAll();
    $notification = getNotification($pdo,"Like").getNotification($pdo,"Comment");
    if ($notification != "")
        $notification = '<div class="notification-box d-none position-absolute">'.$notification.'</div>';
  }
?>
<header class="position-fixed w-100 d-flex flex-row align-items-center" style="z-index:99;" id="first-menu">
  <?php  
    if (isset($_SESSION["User"]) && check_user_exist("Username",$_SESSION["User"],$pdo)){
      echo 
      '<div class="mr-auto">
        <button class="border-0 bg-transparent" onclick="menu_click();"><i id="menu_icon" class="fas fa-bars"></i></button>
      </div>';
    }
  ?>
  <div
    class="w-75 <?php (isset($_SESSION["User"]) && check_user_exist("Username",$_SESSION["User"],$pdo)) ? print('text-center'): print('text-left ml-3')?>">
    <img style="width:90px;cursor:pointer;" src="/Camagru/img/logo.png" onclick="location.href='/Camagru/index.php';">
  </div>
  <?php  
    if (isset($_SESSION["User"]) && check_user_exist("Username",$_SESSION["User"],$pdo)){
      echo 
        '<div class="ml-auto d-flex flex-row align-items-center position-relative">
          <i class="fas fa-bell mr-2" style="cursor: pointer;" onclick="notificatioClick()"></i>
          <i class="fas fa-sign-out-alt ml-2 mr-2" onclick="location.href = \'/Camagru/user/logout.php\'"></i>
          <span class="position-absolute '.($notification!=""? "nt-active":"").'"></span>
          '.$notification.'
        </div>';
      }
      else {
        echo "<div class='w-25 text-right mr-3'><a href='/Camagru/user/login.php' id='btn-login'>Login</a></div>";
      }
  ?>
</header>
<?php
  if(isset($_SESSION["failed"]) && $_SESSION["failed"] != ""){
    echo 
    '<div id="failed" class="message position-fixed text-center d-flex flex-column" style="background-color:#ff8788">
      <h4 class=" w-100" style="color:#ff8788">
        <i class="fas fa-exclamation-triangle" style="color:#ff8788"></i>Failed!
      </h4>
      <span>'.$_SESSION["failed"].'</span>
    </div>';
    unset($_SESSION["failed"]);
  }
?>
<?php
  if(isset($_SESSION["success"]) && $_SESSION["success"] != ""){
    echo 
    '<div id="success" class="message position-fixed text-center d-flex flex-column" style="background-color:#b1e17e">
      <h4 class=" w-100" style="color:#b1e17e">
        <i class="fas fa-chevron-circle-down" style="color:#b1e17e"></i>Success!
      </h4>
      <span>'.$_SESSION["success"].'</span>
    </div>';
    unset($_SESSION["success"]);
  }
?>
<section class="d-flex flex-row position-relative h-100">
  <?php  if (isset($_SESSION["User"]) && check_user_exist("Username",$_SESSION["User"],$pdo)){
      echo 
      '<ul class="position-fixed pl-0 pt-2" id="second-menu">
        <form class="w-100 ml-4 mb-2" action="/Camagru/index.php" method="GET">
          <div class="w-100">
            <input class="w-75 pl-3 rounded-pill mx-auto" type="search" name="search" placeholder="Search">
            <button class="border-0 position-relative" id="btn-search" type="submit"><i class="fa fa-search"
                style="color: white;"></i></button>
          </div>
        </form>
        <li class="text-center"><img src="/Camagru/'.$data[0]["Image"].'" alt="Profile"></li>
        <li class="text-center"><span class="text-white">'.$_SESSION["User"].'</span></li>
        <li class="text-center "><i class="fas fa-circle text-success"></i><span class="text-white p-1">Online</span></li>
        <li class="d-flex flex-row justify-content-around ml-0 mt-2 pb-2 pt-2 border-top border-bottom">
          <div class="text-center"><span
              class="text-white d-block">'.get_count("Post",$pdo).'</span><span class="text-white">Post</span>
          </div>
          <div class="text-center"><span
              class="text-white d-block">'.get_count("Like",$pdo).'</span><span class="text-white">Like</span>
          </div>
          <div class="text-center"><span
              class="text-white d-block">'.get_count("Comment",$pdo).'</span><span
              class="text-white">Comment</span></div>
        </li>
        <div class="d-flex flex-column justify-content-center h-50">
          <li class="mt-5 p-2 w-100 menu-item" onclick="location.href=\'/Camagru/index.php\'">
            <i class="fas fa-home w-25 text-center text-light"></i>
            <span class="w-75 text-center text-light">HOME</span>
          </li>
          <li class="mt-2 p-2 w-100 menu-item"
            onclick="location.href=\'/Camagru/index.php?search='.$_SESSION["User"].'\'">
            <i class="fas fa-user-circle w-25 text-center text-light"></i>
            <span class="w-75 text-center text-light">My Post</span>
          </li>
          <li class="mt-2 p-2 w-100 menu-item"
            onclick="location.href = \'/Camagru/post/create_post.php\';">
            <i class="fas fa-plus w-25 text-center text-light"></i>
            <span class="w-75 text-center text-light">New Post</span>
          </li>
          <li class="mt-2 p-2 w-100 menu-item"
            onclick="location.href = \'/Camagru/user/edit_profile.php\';">
            <i class="fas fa-user-edit w-25 text-center text-light"></i>
            <span class="w-75 text-center text-light">Edit Profile</span>
          </li>
        </div>
      </ul>';
    }
  ?>
  <div class="full_post d-none flex-column align-items-center p-3 mt-3.com.com">
    <div class="w-100 d-flex flex-row align-items-center mb-2">
      <div class="w-25 d-flex flex-row" style="margin: 3px;">
        <i class="fas fa-arrow-left fa-2x mr-2" id="full_previous_post"></i>
        <i class="fas fa-arrow-right fa-2x" id="full_next_post"></i>
      </div>
      <div class="w-50 d-flex flex-row justify-content-center align-items-center">
        <div class="mr-1">
          <img id="full_post_img_owner" alt="Profile">
        </div>
        <div id="full_post_owner"></div>
      </div>
      <div class="w-25 text-right" style="margin: 3px;">
        <i class="fas fa-times fa-2x"
          onclick="document.getElementsByClassName('full_post')[0].setAttribute('style','display:none!important;')"></i>
      </div>
    </div>
    <div class="w-100 mb-2" style="max-height:300px">
      <img id="full_img_post" class="w-100 h-100">
    </div>
    <div class="w-100 d-flex flex-row mb-2 align-items-center">
      <div class="w-50 text-center like_box" style="padding: 5px;border: 1px solid white;cursor: pointer;">
        <i class="fas fa-thumbs-up"></i>
        <span class="w-50 like_txt">Like</span>
      </div>
      <div class="w-50 text-right">
        <span class="w-50" style="font-size: 12px;" id="full_like_count">0 Likes</span>
        <span class="w-50" style="font-size: 12px;" id="full_comment_count">0 Comments</span>
      </div>
    </div>
    <div class="w-100 text-center mb-2">
      <input type="text" name="comment" class="mr-1" style="width: 65%;">
      <input type="submit" value="Comment" style="font-size:14px">
    </div>
    <div class="comment_box w-100 d-flex flex-column" style="overflow: auto;">
    </div>
  </div>