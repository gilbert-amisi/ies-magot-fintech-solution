<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donnees = array();
$entete = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT COUNT(l.id) AS credit, c.objective
FROM loan l LEFT JOIN creditrequest c ON l.creditId = c.id
				LEFT JOIN member m ON c.memberId = m.id
				LEFT JOIN vsla v ON m.vslaId = v.id
WHERE v.id = '{$vslaId}'
GROUP BY c.objective");

while ($res = $data->fetch()) {
    $donnees = array_merge($donnees, array($res['credit']));
    $entete = array_merge($entete, array($res['objective']));
}

$dataS2 = array([
    'donnees'=>$donnees,
    'entete'=>$entete
]);

print(json_encode($dataS2));