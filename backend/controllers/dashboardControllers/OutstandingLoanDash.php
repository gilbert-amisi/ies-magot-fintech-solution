<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$totalLoan = 0;
$nbrTotalLoan = 0;
$ontime = 0;
$nbrOntime = 0;
$overdue = 0;
$nbrOverdue = 0;

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT (
	SELECT IFNULL(SUM(l.amount), 0)
	FROM  loan l, creditrequest c, member m
	WHERE l.`status` = 'Encours' AND l.creditId = c.id AND c.memberId = m.id
			AND m.vslaId = v.id
	) AS creditencours,
	(SELECT COUNT(l.id)
	FROM  loan l, creditrequest c, member m
	WHERE l.`status` = 'Encours' AND l.creditId = c.id AND c.memberId = m.id
			AND m.vslaId = v.id
	) AS nbrtotalcredit,
	(SELECT IFNULL(SUM(l.amount), 0)
	FROM loan l, creditrequest c, member m
	WHERE l.`status` = 'Encours' AND DATEDIFF(l.dateend, CURRENT_DATE)>=0
			AND l.creditId = c.id AND c.memberId = m.id
			AND m.vslaId = v.id
	) AS ontime,
	(SELECT COUNT(l.id)
	FROM loan l, creditrequest c, member m
	WHERE l.`status` = 'Encours' AND DATEDIFF(l.dateend, CURRENT_DATE)>=0
			AND l.creditId = c.id AND c.memberId = m.id
			AND m.vslaId = v.id
	) AS nbrontime,
	(SELECT IFNULL(SUM(l.amount),0)
	FROM loan l, creditrequest c, member m
	WHERE l.`status` = 'Encours' AND DATEDIFF(l.dateend, CURRENT_DATE)<0
			AND l.creditId = c.id AND c.memberId = m.id
			AND m.vslaId = v.id
	) AS overdue,
	(SELECT COUNT(l.id)
	FROM loan l, creditrequest c, member m
	WHERE l.`status` = 'Encours' AND DATEDIFF(l.dateend, CURRENT_DATE)<0
			AND l.creditId = c.id AND c.memberId = m.id
			AND m.vslaId = v.id
	) AS nbroverdue
FROM vsla v
WHERE v.id = '{$vslaId}';");

while ($res = $data->fetch()) {
    $totalLoan = round($res['creditencours'], 2);
    $ontime = round($res['ontime'], 2);
    $overdue = round($res['overdue'], 2);
    $nbrTotalLoan = $res['nbrtotalcredit'];
    $nbrOntime = $res['nbrontime'];
    $nbrOverdue = $res['nbroverdue'];
}

$dataS2 = array([
    'totalLoan'=>$totalLoan,
    'ontime'=>$ontime,
    'overdue'=>$overdue,
    'nbrTotalLoan'=>$nbrTotalLoan,
    'nbrOntime'=>$nbrOntime,
    'nbrOverdue'=>$nbrOverdue
]);

print(json_encode($dataS2));