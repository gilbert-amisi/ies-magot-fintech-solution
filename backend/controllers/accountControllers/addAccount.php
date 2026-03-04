<?php

include '../../models/database/connection.php';
include '../../models/account/account.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$number = $_POST['phone'];
$type = $_POST['operator'];
$vslaId = $_POST['vslaId'];

if (isset($number) && isset($type) && isset($vslaId)) {
    $dbAccount = new Account();
    $dbAccount->addAccount($number, $type, $vslaId);
}