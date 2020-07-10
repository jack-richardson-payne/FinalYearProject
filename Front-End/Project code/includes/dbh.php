<?php
// configurations for local hosting 
// if you wish to host change to your selected username and password
$serverName = "localhost"; 
$username = "root";
$password = "";
$databaseName = "Compproj";


$conn = mysqli_connect($serverName, $username, $password, $databaseName);
if(!$conn){
    die("connection to database failed");
}