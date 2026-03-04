<?php

include '../../models/database/connection.php';
include '../../models/spending/spending.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$date = $_POST['date'];
$amount = $_POST['amount'];
$use = $_POST['use'];
$vslaId = $_POST['vslaId'];

if (isset($vslaId) && isset($amount)) {
    $data = MagoConnection::connect()->query("SELECT MAX(cy.id) AS cycleId
    FROM cycle cy LEFT JOIN vsla vs ON cy.vslaId = vs.id
    WHERE vs.id = '{$vslaId}' AND cy.`status` = 'Encours'");
    if ($res = $data->fetch()) {
        $cycleId = $res['cycleId'];

        $dbSpending = new Spending();
        $dbSpending->addSpending($date, $amount, $use, $cycleId);
    }
    
}