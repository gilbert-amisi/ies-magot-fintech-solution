<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donnees = array();
$entete = 0;

$vslaId = $_GET['vslaId'];

$data1 = MagoConnection::connect()->query("SELECT SUM(meet.socialCase) AS socialcase
FROM meeting meet LEFT JOIN  cycle cy on meet.cycleId = cy.id
					LEFT JOIN vsla vs ON cy.vslaId = vs.id
WHERE vs.id = '{$vslaId}'");

$data2 = MagoConnection::connect()->query("SELECT COUNT(sp.id) AS assistance, SUM(sp.amount) AS amount
FROM spending sp LEFT JOIN cycle cy on sp.cycleId = cy.id
					LEFT JOIN vsla vs ON cy.vslaId = vs.id
WHERE sp.objective = 'Assistance sociale' AND vs.id = '{$vslaId}'");

while (($res1 = $data1->fetch()) && ($res2 = $data2->fetch())) {
    $donnees = array_merge($donnees, array(round($res2['assistance']/$res1['socialcase']*100, 2)));
    $entete = round($res2['amount'], 0);
}

// while ($res = $data->fetch()) {
//     if ($res['nbmember']!=0) {
//         $donnees = array_merge($donnees, array(round($res['participant']/$res['nbmember']*100, 2)));
//     } else {
//         $donnees = [0];
//     }
// }

$dataS2 = array([
    'donnees'=>$donnees,
    'entete'=>$entete
]);

print(json_encode($dataS2));