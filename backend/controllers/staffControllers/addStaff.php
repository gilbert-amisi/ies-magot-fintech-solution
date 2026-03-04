<?php

include '../../models/database/connection.php';
include '../../models/staff/staff.php';
include '../../models/user/user.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$username = $_POST['name'];
$phone = $_POST['phone'];

$email = $_POST['email'];
$position = $_POST['position'];
$ngoId = $_POST['idNGO'];
$userId = null;

if (isset($username) && isset($phone) && isset($email)) {
    $dbUser = new User();
    $dbUser->addUser($username, $email, $phone, $position);
    $data = MagoConnection::connect()->query("SELECT MAX(id) AS id FROM User");
    if ($res = $data->fetch()) {
        $userId = $res['id'];
        $dbStaff = new Staff();
        $dbStaff->Addstaff($username, $email, $position, $phone, $ngoId, $userId);
    }
}