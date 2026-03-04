<?php

include '../../models/database/connection.php';
include '../../models/meeting/meeting.php';
include '../../models/cycle/cycle.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$topic = $_POST['topic'];
$participant = $_POST['participant'];
$men = $_POST['men'];
$women =$_POST['women'];
$date = $_POST['date'];
$vslaId = $_POST['vslaId'];
$socialCase = $_POST['socialcase'];

if (isset($topic) && isset($participant) && isset($vslaId)) {
    $data = MagoConnection::connect()->query("SELECT MAX(cy.id) AS cycleId
    FROM cycle cy LEFT JOIN vsla vs ON cy.vslaId = vs.id
    WHERE vs.id = '{$vslaId}' AND cy.`status` = 'Encours'");
    if ($res = $data->fetch()) {
        $cycleId = $res['cycleId'];

        $dbMeeting = new Meeting();
        $dbMeeting->addMeeting($topic, $participant, $men, $women, $date, $socialCase, $cycleId);
    }
    
}