<?php
if ($_SERVER['REQUEST_URI'] == '/outils/validate.php' )
{
    $_SESSION["failed"] = "Can't Access this page";
    header("location: /index.php");
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
    $has_one_spec_char = preg_match("/[\-\._]{0,1}/",$username);
    return(preg_match("/^[a-zA-Z][a-zA-Z\d\-\._]{6,25}$/",$username) && $has_one_spec_char);
}

function check_user_exist($row,$value,$pdo){
    $stmt = $pdo->prepare("SELECT * FROM `Users` WHERE `$row`=:value");
    $stmt->bindParam("value",$value);
    $stmt->execute();
    $data = $stmt->fetchAll();
    if ($data)
        return(1);
    return (0);
}
?>