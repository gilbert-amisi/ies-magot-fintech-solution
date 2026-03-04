<?php

include '../../models/database/connection.php';
include '../../models/ngo/ngo.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$NGOs = array();

$data = MagoConnection::connect()->query("SELECT * FROM ngo");

while($res = $data->fetch()) {
    $NGOs = array_merge($NGOs, array(['value'=>$res['id'], 'label'=>$res['name'], 'Website'=>$res['website'], 
    'Evidence'=>'http://localhost/backendmago/controllers/files/'.$res['logo'], 'FileName'=>$res['logo']]));
}

print(json_encode($NGOs));