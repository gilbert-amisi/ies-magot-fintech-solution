<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$members = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT mb.*
FROM member mb LEFT JOIN vsla vs ON mb.vslaId = vs.id
WHERE vs.id='{$vslaId}'");

while ($res = $data->fetch()) {
    $members = array_merge($members, array(['value'=>$res['id'], 'label'=>$res['name'], 'Gender'=>$res['gender'],
            'Age'=>$res['age'], 'Education'=>$res['educationLevel'], 'Activity'=>$res['mainactivity'], 'Position'=>$res['position'],
            'Phone'=>$res['phone'], 'Operator'=>$res['operateur'], 'Marginalization'=>$res['marginalization']]));
}

print(json_encode($members));