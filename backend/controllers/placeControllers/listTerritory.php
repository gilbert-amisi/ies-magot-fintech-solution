<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$territories = array();
$provinceId = $_GET['provinceId'];

$data = MagoConnection::connect()->query("SELECT terr.* FROM territory terr, province pro WHERE terr.provinceId=pro.id AND pro.id='{$provinceId}' ORDER BY terr.name ASC");
while ($res=$data->fetch()) {
    $territories = array_merge($territories, array(['value'=>$res['name'], 'label'=>$res['name']]));
}
print(json_encode($territories));