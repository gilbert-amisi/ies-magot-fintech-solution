<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$table = array();
$data = MagoConnection::connect()->prepare("SELECT *
FROM user
WHERE (phone = ? OR email = ?) AND password=?");
$data->execute([$_GET['username'], $_GET['username'], sha1($_GET['password'])]);

if ($res=$data->fetch()) {
    if ($res['type']=="NGO Staff") {
        $table = array_merge($table, array(['message'=>1, 'UserId'=>$res['id'], 'Username'=>$res['username'],
                            'Usertype'=>$res['type'], 'Useremail'=>$res['email'], 'Userphone'=>$res['phone']]));
    } else {
        $data = MagoConnection::connect()->prepare("SELECT DISTINCT us.*, sv.id AS vslaId, sv.name, meb.id AS memberId
        FROM vsla sv LEFT JOIN member meb ON sv.id = meb.vslaId
                            LEFT JOIN user us ON meb.userId = us.id
        WHERE us.phone = ? AND us.password = ?");
        $data->execute([$_GET['username'], sha1($_GET['password'])]);
        if ($res1=$data->fetch()) {
            $table = array_merge($table, array(['message'=>1, 'UserId'=>$res1['id'], 'Username'=>$res1['username'],
                            'Usertype'=>$res1['type'], 'Useremail'=>$res1['email'], 'Userphone'=>$res1['phone'],
                            'vslaId'=>$res1['vslaId'], 'vslaName'=>$res1['name'], 'memberId'=>$res1['memberId']]));
        }
    }
    
} else {
    $table = array_merge($table, array(['message'=>0]));
}
print(json_encode($table));