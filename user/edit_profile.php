<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/";
require($Home_dir."config/setup.php");
require($Home_dir."outils/check.php");
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (!isset($_SESSION["User"]) || !check_user_exist("Username",$_SESSION["User"],$pdo))
  Redirect("./login.php");
function sentDatabase($pdo,$array_items)
{
    $array_key = array_keys($array_items);
    $i = 0;
    foreach($array_items as $item)
    {
        $query = "$array_key[$i]=:$array_key[$i]";
        $stmt = $pdo->prepare("UPDATE `Users` SET $query WHERE Username=:user");
        $stmt->bindParam(":$array_key[$i]",$item);
        $stmt->bindParam(":user",$_SESSION["User"]);
        $stmt->execute();
        $i++;
    }
}
$stmt = $pdo->prepare("SELECT * FROM `Users` WHERE Username=:Username");
$stmt->bindParam(":Username",$_SESSION["User"]);
$stmt->execute();
$data = $stmt->fetchAll();
$is_changed = false;
if ($_FILES && $_FILES["fileinput"] && $_FILES["fileinput"]["name"] != "")
{
  $type_image = array("image/png","image/jpg","image/jpeg","image/gif");
  if ($_FILES && !$_FILES["fileinput"]["error"] && array_search(strtolower($_FILES["fileinput"]["type"]),$type_image) > -1)
  {
      $image_id = time().".".explode("image/",$_FILES["fileinput"]["type"])[1];
      if(!move_uploaded_file($_FILES["fileinput"]["tmp_name"],"../img/$image_id"))
        set_message_failed("There was some error moving the file to upload directory",$url);
      if (file_exists("../".$data[0]["Image"]) && $data[0]["Image"] != "img/default.png")
        unlink("../".$data[0]["Image"]);
      sentDatabase($pdo,array("Image"=>"img/".$image_id));
  }
  else
      set_message_failed("Image must be PNG OR JPG OR JPEG OR GIF And Size lower than 10 MB",$url);
}
if (isset($_POST["save"]))
    sentDatabase($pdo,array("Notification"=>((isset($_POST["checkbox"])) && $_POST["checkbox"] == "on") ? 1 : 0));
if (isset($_POST["Username"],$_POST["Email"]) && $_POST["Username"] != "" && $_POST["Email"] != "")
{
    if ($_POST["Email"] == $data[0]["Email"] && $_POST["Username"] == $data[0]["Username"])
        $_SESSION["failed"] = "";
    else if (validator_Email($_POST["Email"]) && validator_Username($_POST["Username"]))
    {
      if (check_user_exist("Username",$_POST["Username"],$pdo) && check_user_exist("Email",$_POST["Email"],$pdo))
          set_message_failed("User Already exist",$url);
      else
      {
        sentDatabase($pdo,array("Email"=>$_POST["Email"],"Username"=>$_POST["Username"]));
        if ($_POST["Username"] != $data[0]["Username"])
        {
          $stmt = $pdo->prepare("UPDATE `Post` SET `UserIdOwner`=:Username WHERE `UserIdOwner`=:user");
          $stmt->bindParam(":user",$_SESSION["User"]);
          $stmt->bindParam(":Username",$_POST["Username"]);
          $stmt->execute();
          $_SESSION["User"] = $_POST["Username"];
        }
        $is_changed = true;
      }
    }
    else
      set_message_failed("Username or Email was wrong please try again.",$url);
}
if (isset($_POST["current-password"],$_POST["new-password"],$_POST["confirm-password"]) &&  $_POST["current-password"] != "" && $_POST["new-password"] != "" && $_POST["confirm-password"] != "")
{
    if (validator_Password($_POST["new-password"]) && $_POST["new-password"] == $_POST["confirm-password"] && $_POST["new-password"] != $_POST["current-password"])
    {
        $Password = hash("whirlpool",$_POST["current-password"]);
        $stmt = $pdo->prepare("SELECT * FROM `Users` WHERE Username=:Username AND Password=:Password");
        $stmt->bindParam(":Password",$Password);
        $stmt->bindParam(":Username",$_SESSION["User"]);
        $stmt->execute();
        $data = $stmt->fetchAll();
        if ($data)
        {
            $Password = hash("whirlpool",$_POST["new-password"]);
            $stmt = $pdo->prepare("UPDATE `Users` SET Password=:Password WHERE Username=:Username");
            $stmt->bindParam(":Password",$Password);
            $stmt->bindParam(":Username",$_SESSION["User"]);
            $stmt->execute();
            $is_changed = true;
        }
        else
            set_message_failed("old Password wasn't match",$url);
    }
    else
        set_message_failed("Something wrong with your Password please try again.",$url);
}
if ($is_changed)
  set_message_success("You Information has been Saved.",'./logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/all.css">
  <link rel="stylesheet" href="../css/style.css">
  <title>Edit Profile</title>
</head>

<body>
  <div class="d-flex flex-column m-0 p-0">
    <?php require("../outils/menu.php"); ?>
    <form class="profile_edit w-100 d-flex flex-row justify-content-center mt-5" method="post"
      enctype="multipart/form-data">
      <div class="d-flex flex-column"
        style="width: 90%;max-width: 600px;background-color: #252525;border-radius: 20px;">
        <div class="w-100 d-flex flex-column align-items-center justify-content-center"
          style="border-radius:20px 20px 0 0;background-color:#00bcd4;padding:10px 0">
          <div class="position-relative" style="width:100px;height:100px">
            <img class="rounded-pill" id="Profile_image" src="<?php echo '/'.$data[0]['Image'];?>"
              alt="<?php echo $data[0]['Username'];?>" style="width:100px;height:100px;" />
            <i class="fas fa-edit position-absolute" style="cursor:pointer;color:black;top:0;right:0;"
              onclick="document.querySelector('input[name=\'fileinput\']').click()"></i>
            <input class="position-absolute"
              style="width: 0;height: 0;margin: 0;overflow: hidden;padding: 0;top:0;right:0;" type="file"
              name="fileinput" onchange="change_image()" accept="image/png,image/jpg,image/jpeg,image/gif" />
          </div>
          <span class="d-block font-weight-bold" style="color:black"><?php echo $data[0]['Username'];?></span>
        </div>
        <div class="d-flex flex-column w-100 p-4">
          <div class="d-flex flex-column w-100 p-2">
            <label>Username :</label>
            <input type="text" name="Username" value="<?php echo $data[0]['Username'];?>" />
          </div>
          <div class="d-flex flex-column w-100 p-2">
            <label>Email :</label>
            <input type="text" name="Email" value="<?php echo $data[0]['Email'];?>" />
          </div>
          <div class="d-flex flex-flex w-100 p-2">
            <label>Active Notification :</label>
            <input style="margin-left:20px;margin-top:5px" type="checkbox" name="checkbox"
              <?php (($data[0]["Notification"] == 1) ? print("checked") : 0);?>>
          </div>
          <div class="d-flex flex-column w-100 p-2">
            <label>Old Password :</label>
            <input type="password" name="current-password" autocomplete="off" />
          </div>
          <div class="d-flex flex-column w-100 p-2">
            <label>New Password :</label>
            <input type="password" name="new-password" autocomplete="off" />
          </div>
          <div class="d-flex flex-column w-100 p-2">
            <label>Confirm Password :</label>
            <input type="password" name="confirm-password" autocomplete="off" />
          </div>
          <div class="d-flex flex-column align-items-center w-100 p-2">
            <button type="submit" name="save">Save</button>
          </div>
        </div>
      </div>
    </form>
    </section>
    <?php require("../outils/footer.php"); ?>
  </div>
  <script src="../js/script.js"></script>
</body>

</html>