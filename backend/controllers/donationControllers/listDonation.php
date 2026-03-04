<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donations = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT don.*
FROM donation don LEFT JOIN cycle cy ON don.cycleId = cy.id
							LEFT JOIN vsla vs ON cy.vslaId = vs.id
WHERE vs.id = '{$vslaId}' AND cy.`status` = 'Encours'");

while ($res = $data->fetch()) {
    $donations = array_merge($donations, array(['value'=>$res['id'], 'label'=>$res['donor'], 'Amount'=>$res['amount'],
        'Use'=>$res['objective'], 'Date'=>$res['date']]));
}

print(json_encode($donations));