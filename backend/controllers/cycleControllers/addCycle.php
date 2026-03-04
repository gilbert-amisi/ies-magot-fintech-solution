<?php

include '../../models/database/connection.php';
include '../../models/cycle/cycle.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$name = $_POST['name'];
$start = $_POST['start'];
$end = $_POST['end'];
$vslaId = $_POST['vslaId'];

if (isset($vslaId) && isset($name)) {
    $dbCycle = new Cycle();
    
    $data = MagoConnection::connect()->query("SELECT c.id
    FROM cycle c LEFT JOIN vsla v ON c.vslaId = v.id
    WHERE c.`status` = 'Encours' AND v.id = '{$vslaId}'");

    if ($res = $data->fetch()) {
        $cycleId = $res['id'];
        $dbCycle->changeStatus('Clôturé', $cycleId);
        $dbCycle->addCycle($name, $start, $end, $vslaId);
    } else {
        $dbCycle->addCycle($name, $start, $end, $vslaId);
    }
    
}