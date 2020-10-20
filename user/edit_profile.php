<?php
require("../config/database.php");
require("../config/setup.php");
require("../outils/check.php");
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$req_uri = (($_SERVER['REQUEST_URI'] == "/Camagru/" || strpos($_SERVER['REQUEST_URI'],"/Camagru/index.php") !== false ) ? "./" : "../");
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
if ($_FILES && $_FILES["fileinput"]["name"] != "")
{
    if (isset($_SERVER["CONTENT_LENGTH"]) && $_SERVER["CONTENT_LENGTH"] < 11000000)
    {
        if(($image = getimagesize($_FILES["fileinput"]["tmp_name"])))
        {
            $image_id = $_SESSION["User"].".".explode("image/",$image["mime"])[1];
            move_uploaded_file($_FILES["fileinput"]["tmp_name"],"../img/$image_id");
            sentDatabase($pdo,array("Image"=>"img/".$image_id));
        }
        else
            set_message_failed("Image must be PNG OR JPG OR JPEG OR GIF",$url);
    }
    else
        set_message_failed("Image size above 10MB",$url);
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
            set_message_success("User Already exist",$url);
        $tokenvalidate = hash_hmac('sha256',$_POST["Username"],time());
        $stmt = $pdo->prepare("UPDATE `Users` SET Email=:Email,Username=:Username WHERE Username=:user");
        $stmt->bindParam(":Email",$_POST["Email"]);
        $stmt->bindParam(":Username",$_POST["Username"]);
        $stmt->bindParam(":user",$_SESSION["User"]);
        $stmt->execute();
        $_SESSION["User"] = $_POST["Username"];
        set_message_success("You Information has been Saved.",$url);
    }
    else
        set_message_failed("Username or Email was wrong please try again.",$url);
}
if (isset($_POST["old_Password"]) && isset($_POST["new_Password"]) && isset($_POST["confirme_Password"]) &&  $_POST["old_Password"] != "" && $_POST["new_Password"] != "" && $_POST["confirme_Password"] != "")
{
    if (validator_Password($_POST["new_Password"]) && $_POST["new_Password"] == $_POST["confirme_Password"] && $_POST["new_Password"] != $_POST["old_Password"])
    {
        $Password = hash("whirlpool",$_POST["old_Password"]);
        $stmt = $pdo->prepare("SELECT * FROM `Users` WHERE Username=:Username AND Password=:Password");
        $stmt->bindParam(":Password",$Password);
        $stmt->bindParam(":Username",$_SESSION["User"]);
        $stmt->execute();
        $data = $stmt->fetchAll();
        if ($data)
        {
            $Password = hash("whirlpool",$_POST["new_Password"]);
            $stmt = $pdo->prepare("UPDATE `Users` SET Password=:Password WHERE Username=:Username");
            $stmt->bindParam(":Password",$Password);
            $stmt->bindParam(":Username",$_SESSION["User"]);
            $stmt->execute();
            set_message_success("You Information has been Saved.",$url);
        }
        else
            set_message_failed("old Password wasn't match",$url);
    }
    else
        set_message_failed("Something wrong with your Password please try again.",$url);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/all.css">
  <title>Edit Profile</title>
</head>

<body>
  <div class="d-flex flex-column m-0 p-0 h-100">
    <?php require("../outils/menu.php"); ?>
    <form class="profile_edit w-100 h-100 d-flex flex-row align-items-center justify-content-center" method="post"
      enctype="multipart/form-data">
      <div class="d-flex flex-column"
        style="width: 90%;max-width: 600px;background-color: #252525;border-radius: 20px;">
        <div class="w-100 d-flex flex-column align-items-center justify-content-center"
          style="border-radius:20px 20px 0 0;background-color:#00bcd4;padding:10px 0">
          <div class="position-relative" style="width:100px;height:100px">
            <img class="rounded-pill" id="Profile_image" src="<?php echo $req_uri.$data[0]['Image'];?>"
              alt="<?php echo $data[0]['Username'];?>" style="width:100px;height:100px;" />
            <i class="fas fa-edit position-absolute" style="cursor:pointer;color:black"
              onclick="document.querySelector('input[name=\'fileinput\']').click()"></i>
            <input class="position-absolute" style="width: 0;height: 0;margin: 0;overflow: hidden;padding: 0;"
              type="file" name="fileinput" onchange="change_image()" />
          </div>
          <span class="d-block font-weight-bold" style="color:black"><?php echo $data[0]['Username'];?></span>
        </div>
        <div class="d-flex flex-column w-100 p-4">
          <div class="d-flex flex-column w-100 p-2">
            <label>Username :</label>
            <input type="text" name="Username" placeholder="<?php echo $data[0]['Username'];?>" />
          </div>
          <div class="d-flex flex-column w-100 p-2">
            <label>Email :</label>
            <input type="text" name="Email" placeholder="<?php echo $data[0]['Email'];?>" />
          </div>
          <div class="d-flex flex-flex w-100 p-2">
            <label>Active Notification :</label>
            <input style="margin-left:20px;margin-top:5px" type="checkbox" name="checkbox"
              <?php (($data[0]["Notification"] == 1) ? print("checked") : 0);?>>
          </div>
          <div class="d-flex flex-column w-100 p-2">
            <label>Old Password :</label>
            <input type="password" name="old_Password" />
          </div>
          <div class="d-flex flex-column w-100 p-2">
            <label>New Password :</label>
            <input type="password" name="new_Password" />
          </div>
          <div class="d-flex flex-column w-100 p-2">
            <label>Confirme Password :</label>
            <input type="password" name="confirme_Password" />
          </div>
          <div class="d-flex flex-column align-items-center w-100 p-2">
            <button type="submit" name="save">Save</button>
          </div>
        </div>
      </div>
    </form>
    </section>
    <?php
            require("../outils/footer.php");
        ?>
  </div>
  <script src="../js/script.js"></script>
</body>

</html>