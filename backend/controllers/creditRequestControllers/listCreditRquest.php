<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$credits = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT cq.*, mb.name, mb.phone, mb.operateur
FROM creditrequest cq LEFT JOIN member mb ON cq.memberId = mb.id
								LEFT JOIN vsla vs ON mb.vslaId = vs.id
WHERE vs.id = '{$vslaId}'
ORDER BY cq.id DESC");

while ($res = $data->fetch()) {
    $credits = array_merge($credits, array(['value'=>$res['id'], 'Amount'=>round($res['amount'], 2),
        'Date'=>$res['date'], 'Status'=>$res['status'], 'Use'=>$res['objective'], 'memberId'=>$res['memberId'],
        'Name'=>$res['name'], 'Phone'=>$res['phone'], 'Operator'=>$res['operateur'], 'Validateamount'=>round($res['validateAmount'], 2)]));
}

print(json_encode($credits));