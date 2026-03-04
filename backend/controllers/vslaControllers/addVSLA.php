<?php

include '../../models/database/connection.php';
include '../../models/vsla/vsla.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$name = $_POST['name'];
$type = $_POST['type'];
$country = $_POST['country'];
$location = $_POST['location'];
$nominalvalue = $_POST['nominalvalue'];
$userId = $_POST['userId'];
$date = $_POST['start'];

if (isset($name) && isset($type) && isset($location) && isset($date)) {
    $dbVSLA = new VSLA();
    $data = MagoConnection::connect()->query("SELECT ong.id AS ngoId
    FROM staff st LEFT JOIN ngo ong ON st.ngoId = ong.id
    WHERE st.userId = '{$userId}'");

    if ($res = $data->fetch()) {
        $ngoId = $res['ngoId'];
        $dbVSLA->addVSLA($name, $type, $country, $location, $nominalvalue, $date, $ngoId);
    }
}