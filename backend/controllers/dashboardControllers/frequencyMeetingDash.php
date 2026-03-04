<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$meetings = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT COUNT(meet.topic) AS frequency, meet.topic, cy.name AS annee
FROM meeting meet LEFT JOIN cycle cy ON meet.cycleId = cy.id
					LEFT JOIN  vsla vs ON cy.vslaId=vs.id
WHERE vs.id = '{$vslaId}'
GROUP BY cy.name, meet.topic");

while ($res = $data->fetch()) {
    $meetings = array_merge($meetings, array(['Theme'=>$res['topic'], 'Frequency'=>$res['frequency'], 'Year'=>$res['annee']]));
}

print(json_encode($meetings));