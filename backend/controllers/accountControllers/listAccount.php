<?php

include '../../models/database/connection.php';
$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$accounts = array();
$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT *
FROM account
WHERE vslaId = '{$vslaId}'");

while ($res = $data->fetch()) {
    $accounts = array_merge($accounts, array(['value'=>$res['id'], 'Operator'=>$res['type'], 'Phone'=>$res['number']]));
}

print(json_encode($accounts));