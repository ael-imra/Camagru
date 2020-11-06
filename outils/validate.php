<?php
if ($_SERVER['REQUEST_URI'] == '/Camagru/outils/validate.php' )
{
    $_SESSION["failed"] = "Can't Access this page";
    header("location: /Camagru/index.php");
    exit();
}
function validator_email($email){
    return(filter_var($email, FILTER_VALIDATE_EMAIL));
}
function validator_password($password){
    $has_lowerCase = preg_match("/[a-z]+/",$password);
    $has_upperCase = preg_match("/[A-Z]+/",$password);
    $has_number = preg_match("/[0-9]+/",$password);
    return((preg_match("/^[ -~]{8,25}$/",$password) && $has_lowerCase && $has_upperCase && $has_number));
}

function validator_username($username){
    return(preg_match("/^[a-zA-Z][a-zA-Z][a-zA-Z\d\._\-]{7,25}$/",$username));
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
?>