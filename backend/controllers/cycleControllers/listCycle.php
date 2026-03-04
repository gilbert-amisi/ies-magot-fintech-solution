<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$credits = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT c.*
FROM cycle c LEFT JOIN vsla v ON c.vslaId = v.id
WHERE v.id = '{$vslaId}'
ORDER BY c.id DESC 
LIMIT 1");

while ($res = $data->fetch()) {
    $credits = array_merge($credits, array(['value'=>$res['id'], 'label'=>$res['name'],
        'Start'=>$res['start'], 'End'=>$res['end'], 'Status'=>$res['status']]));
}

print(json_encode($credits));