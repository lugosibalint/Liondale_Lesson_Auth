<?php
$serverAddress = "localhost";
$username = "root";
$password = "";
$databaseName = "lionsdale_app";

try {
    $connection =  mysqli_connect($serverAddress,$username,$password,$databaseName);
} catch (\Throwable $th) {
    $connection = '';
    echo "<script>alert('Something whent wrong... ');</script>";
}