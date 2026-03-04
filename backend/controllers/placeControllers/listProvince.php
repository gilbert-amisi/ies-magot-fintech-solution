<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$provinces = array();

$data = MagoConnection::connect()->query("SELECT * FROM province");
while($res=$data->fetch()) {
    $provinces=array_merge($provinces, array(['value'=>$res['id'], 'label'=>$res['name']]));
}
print(json_encode($provinces));