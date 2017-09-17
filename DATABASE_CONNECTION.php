<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nud_prep_test";
$conn = null;
global $conn;
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("set names utf8");
    //echo "Connected successfully"; 
} catch (PDOException $e) {
    //echo "Connection failed: " . $e->getMessage();
}
?>