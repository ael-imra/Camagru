<?php
if ($_SERVER['REQUEST_URI'] == '/Camagru/outils/validate.php' )
{
    $_SESSION["failed"] = "Can't Access this page";
    header("location: /Camagru/index.php");
    exit();
}
function validator_email($email){
    return(preg_match("/^[a-zA-Z\d][a-zA-Z\d\.\-_]{4,30}@[a-zA-Z\d\._]{3,16}\.[a-zA-Z\d\._]{2,7}$/",$email));
}

function validator_password($password){
    return(preg_match("/^[ -~]{8,25}$/",$password));
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