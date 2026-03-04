<?php

include '../../models/database/connection.php';
include '../../models/vsla/vsla.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$VSLAs = array();

$userId = $_GET['userId'];

$data = MagoConnection::connect()->query("SELECT vs.*
FROM vsla vs LEFT JOIN ngo ong ON vs.ngoId = ong.id
					LEFT JOIN staff st ON ong.id = st.ngoId
					LEFT JOIN user us ON st.userId = us.id
WHERE us.id = '{$userId}'");

while ($res = $data->fetch()) {
    $VSLAs = array_merge($VSLAs, array(['value'=>$res['id'], 'label'=>$res['name'], 'Type'=>$res['type'],
                'Country'=>$res['country'], 'Location'=>$res['location'], 'Start'=>$res['creationDate'],
                'Nominalvalue'=>$res['nominalvalue']]));
}

print(json_encode($VSLAs));