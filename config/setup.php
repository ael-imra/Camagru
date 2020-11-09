<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/";
require($Home_dir."config/database.php");
if (!isset($pdo))
{
    try{
        $pdo = new PDO("mysql:host=".$DB_HOST.";",$DB_USER,$DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        echo "Error to connect with database";
        exit();
    }
    if (isset($pdo))
    {
        try {
            $stmt = $pdo->prepare("CREATE DATABASE IF NOT EXISTS `".$DB_NAME."`;");
            $stmt->execute();
        } catch (PDOException $e) {
            echo "ERROR ON CREATE DATABASE";
            exit();
        }
        try{
            $pdo = new PDO($DB_DSN,$DB_USER,$DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e)
        {
            echo "Error to connect with database";
            exit();
        }
        try {
            $stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS `Users`
            (
                `Username` VARCHAR(30) NOT NULL,
                `Email` VARCHAR(100) NOT NULL,
                `Password` VARCHAR(255) NOT NULL,
                `Image` VARCHAR(255) DEFAULT 'img/default.png',
                `Tokenlogin` VARCHAR(255),
                `Notification` BOOLEAN DEFAULT 1,
                `Tokenpassword` VARCHAR(255),
                UNIQUE KEY(`Username`,`Email`)
            );");
            $stmt->execute();
        } catch (PDOException $e) {
            echo "ERROR ON TABLE USERS";
            exit();
        }

        try {
            $stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS `Post`
            (
                `PostId` INT PRIMARY KEY AUTO_INCREMENT,
                `UserIdOwner` VARCHAR(55) NOT NULL,
                `Image` VARCHAR(255) NOT NULL,
                `Date_create` DATETIME NOT NULL
            );");
            $stmt->execute();
        } catch (PDOException $e) {
            echo "ERROR ON TABLE POST";
            exit();
        }

        try {
            $stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS `Like`
            (
                `LikeId` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `UserIdOwner` VARCHAR(55) NOT NULL,
                `PostId` VARCHAR(55) NOT NULL,
                `UserAction` VARCHAR(55) NOT NULL,
                `Likeactive` BOOLEAN DEFAULT 1,
                `Notification` BOOLEAN DEFAULT 0
            );");
            $stmt->execute();
        } catch (PDOException $e) {
            echo "ERROR ON TABLE LIKE";
            exit();
        }

        try {
            $stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS `Comment`
            (
                `CommentId` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `UserIdOwner` VARCHAR(55) NOT NULL,
                `PostId` VARCHAR(55) NOT NULL,
                `UserAction` VARCHAR(55) NOT NULL,
                `Content` VARCHAR(255) NOT NULL,
                `Notification` BOOLEAN DEFAULT 0
            );");
            $stmt->execute();
        } catch (PDOException $e) {
            echo "ERROR ON TABLE COMMENT";
            exit();
        }
    }
}
?>