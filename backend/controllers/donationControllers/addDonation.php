<?php

include '../../models/database/connection.php';
include '../../models/donation/donation.php';
include '../../models/cycle/cycle.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donor = $_POST['donor'];
$amount = $_POST['amount'];
$use = $_POST['use'];
$date = $_POST['date'];
$vslaId = $_POST['vslaId'];

if (isset($donor) && isset($amount) && isset($vslaId)) {
    $data = MagoConnection::connect()->query("SELECT MAX(cy.id) AS cycleId
    FROM cycle cy LEFT JOIN vsla vs ON cy.vslaId = vs.id
    WHERE vs.id = '{$vslaId}' AND cy.`status` = 'Encours'");
    if ($res = $data->fetch()) {
        $cycleId = $res['cycleId'];

        $dbDonor = new Donation();
        $dbDonor->addDonation($donor, $amount, $use, $date, $cycleId);
    }
    
}