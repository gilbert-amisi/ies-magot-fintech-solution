<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donnees = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT COUNT(DISTINCT mb.id) AS nbmember, AVG(meet.participant) AS participant
FROM meeting meet LEFT JOIN cycle cy ON meet.cycleId = cy.id
						LEFT JOIN vsla vs ON cy.vslaId=vs.id
					LEFT JOIN member mb ON vs.id = mb.vslaId
WHERE vs.id = '{$vslaId}'");

while ($res = $data->fetch()) {
    if ($res['nbmember']!=0) {
        $donnees = array_merge($donnees, array(round($res['participant']/$res['nbmember']*100, 2)));
    } else {
        $donnees = [0];
    }
}

$dataS2 = array([
    'donnees'=>$donnees
]);

print(json_encode($dataS2));