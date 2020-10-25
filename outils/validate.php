<?php
if ($_SERVER['REQUEST_URI'] == '/Camagru/outils/validate.php' )
{
    session_start();
    $_SESSION["failed"] = "Can't Access this page";
  echo("<script>location.href = '/Camagru/index.php';</script>");
  exit();
}
session_start();
function validator_email($email){
    return(preg_match("/^[a-zA-Z\d\.\-_]{4,30}@[a-zA-Z\d\._]{3,16}\.[a-zA-Z\d\._]{2,7}$/",$email));
}

function validator_password($password){
    return(preg_match("/^[a-zA-Z\d.]{8,25}$/",$password));
}

function validator_username($username){
    return(preg_match("/^[a-zA-Z][a-zA-Z\d\._\-]{7,25}$/",$username));
}

function check_user_exist($column,$value,$pdo){
    $stmt = $pdo->prepare("SELECT * FROM `Users` WHERE `$column`=:value");
    $stmt->bindParam("value",$value);
    $stmt->execute();
    $data = $stmt->fetchAll();
    if ($data)
        return(1);
    return (0);
}
$url = $_SERVER['REQUEST_URI'];
if (strpos($url,"/Camagru/user/active.php") !== false || strpos($url,"/Camagru/user/reset_password.php") !== false)
    $pos = 0;
else if(isset($pdo) && (!isset($_SESSION["User"]) || $_SESSION["User"] == "" || !($i = check_user_exist("Username",$_SESSION["User"],$pdo))) && $url !="/Camagru/user/login.php" && $url !="/Camagru/index.php")
{
    $_SESSION["User"] = "";
    $_SESSION["Redirect"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    Redirect("/Camagru/user/login.php");
}
else if (strpos($_SERVER['REQUEST_URI'],"/Camagru/user/login.php") !== false && isset($_SESSION["User"]) && isset($pdo) && check_user_exist("Username",$_SESSION["User"],$pdo))
    Redirect("../index.php");
?>