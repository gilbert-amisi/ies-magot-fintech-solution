<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$meetings = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT meet.*
FROM meeting meet LEFT JOIN cycle cy ON meet.cycleId = cy.id
							LEFT JOIN vsla vs ON cy.vslaId = vs.id
WHERE vs.id = '{$vslaId}' AND cy.`status` = 'Encours'
ORDER BY id DESC");

while ($res = $data->fetch()) {
    $meetings = array_merge($meetings, array(['value'=>$res['id'], 'label'=>$res['topic'], 
        'Participant'=>$res['participant'], 'Men'=>$res['men'], 'Women'=>$res['women'], 'Date'=>$res['date'],
        'Socialcase'=>$res['socialCase']]));
}

print(json_encode($meetings));