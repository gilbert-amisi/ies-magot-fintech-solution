<?php

include '../../models/database/connection.php';
include '../../models/creditRequest/creditRequest.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$status =$_GET['status'];
$creditId = $_GET['requestId'];

if (isset($status) && isset($creditId)) {
    $dbCredit = new Creditrequest();
    $dbCredit->updateStatus($status, $creditId);
}