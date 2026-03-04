<?php

include '../../models/database/connection.php';
include '../../models/creditRequest/creditRequest.php';
include '../../models/cycle/cycle.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$amount =$_POST['amount'];
$date = date('Y-m-d');
$memberId = $_POST['memberId'];
$use = $_POST['use'];
$vslaId = $_POST['vslaId'];

if (isset($amount) && isset($memberId)) {
    $data = MagoConnection::connect()->query("SELECT MAX(cy.id) AS cycleId
    FROM cycle cy LEFT JOIN vsla vs ON cy.vslaId = vs.id
    WHERE vs.id = '{$vslaId}' AND cy.`status` = 'Encours'");
    if ($res = $data->fetch()) {
        $cycleId = $res['cycleId'];

        $dbCredit = new Creditrequest();
        $dbCredit->addCreditrequest($amount, $date, $use, $memberId, $cycleId);
    }
}