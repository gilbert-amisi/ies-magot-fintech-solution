<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$spendings = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT sp.*
FROM spending sp LEFT JOIN cycle cy ON sp.cycleId = cy.id
						LEFT JOIN vsla vs ON cy.vslaId = vs.id
WHERE vs.id = '{$vslaId}' AND cy.`status`='Encours'");

while ($res = $data->fetch()) {
    $spendings = array_merge($spendings, array(['value'=>$res['id'], 'Date'=>$res['date'], 'Amount'=>$res['amount'],
                    'Use'=>$res['objective']]));
}

print(json_encode($spendings));