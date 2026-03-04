<?php

include '../../models/database/connection.php';
include '../../models/ngo/ngo.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$name = $_POST['name'];

$website = $_POST['website'];

$urlPiece = $_FILES['logo']['tmp_name'];
$logo = $_FILES['logo']['name'];
$filedestination = '../files/'.$logo;

if (isset($_FILES['logo']['name'])){
    $dbNGO = new NGO();
    move_uploaded_file($urlPiece,$filedestination);
    $dbNGO->addNGO($name, $logo, $website);
} else {
    $dbNGO = new NGO();
    $dbNGO->addNGOWithoutLogo($name, $website);
}