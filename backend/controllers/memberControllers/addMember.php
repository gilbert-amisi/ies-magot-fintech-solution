<?php

include '../../models/database/connection.php';
include '../../models/member/member.php';
include '../../models/user/user.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$name = $_POST['name'];
$gender = $_POST['gender'];
$age = $_POST['age'];
$education = $_POST['education'];
$activity = $_POST['activity'];
$position = $_POST['position'];
$phone = $_POST['phone'];
$userId = null;
$vslaId = $_POST['vslaId'];
$operateur = $_POST['operator'];
$marginalization = $_POST['marginalization'];


if (isset($name) && isset($activity) && isset($phone)) {

    $dbUser = new User();
    $dbUser->addUserWithoutEmail($name, $phone, $position);
    $data = MagoConnection::connect()->query("SELECT MAX(id) AS id FROM user");

    if ($res = $data->fetch()) {
        $userId = $res['id'];
        $dbMember = new Member();
        $dbMember->addMember($name, $gender, $age, $education, $activity, $position, $marginalization, $phone, $operateur, $userId, $vslaId);
    }
}