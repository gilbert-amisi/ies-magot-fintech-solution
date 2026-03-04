<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donnees = array();
$entete = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT mb.gender, COUNT(mb.id) AS nbMember
FROM member mb LEFT JOIN vsla vs ON mb.vslaId = vs.id
WHERE vs.id = '{$vslaId}' AND mb.`position` <> 'Ordinary member'
GROUP BY mb.gender");

while ($res = $data->fetch()) {
    $donnees = array_merge($donnees, array($res['nbMember']));
    $entete = array_merge($entete, array($res['gender']));
}

$dataS2 = array([
    'donnees'=>$donnees,
    'entete'=>$entete
]);

print(json_encode($dataS2));