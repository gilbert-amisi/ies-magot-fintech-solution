<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$stats = array();

$vslaId = $_GET['vslaId'];

$data = MagoConnection::connect()->query("SELECT (
	SELECT COUNT(mb.id)
	FROM member mb LEFT JOIN vsla vs ON mb.vslaId = vs.id) AS nbMember,
	(SELECT COUNT(ac.id)
	FROM account ac LEFT JOIN vsla vs ON ac.vslaId = vs.id) AS nbAccount,
	(SELECT COUNT(cq.id) 
	FROM creditrequest cq LEFT JOIN member mb ON cq.memberId = mb.id
									LEFT JOIN vsla vs ON mb.vslaId = vs.id
	WHERE cq.`status` = 'En attente') AS nbCreditRequest,
	(SELECT COUNT(lo.id)
	FROM loan lo LEFT JOIN creditrequest cq ON lo.creditId = cq.id
						LEFT JOIN member mb ON cq.memberId = mb.id
						LEFT JOIN vsla vs ON mb.vslaId = vs.id
	WHERE lo.`status`='Outstanding') AS Outstanding,
	(SELECT COUNT(don.id)
	FROM donation don LEFT JOIN cycle cy ON don.cycleId = cy.id
								LEFT JOIN vsla vs ON cy.vslaId = vs.id
	WHERE cy.`status` = 'Encours') AS nbDonation,
	(SELECT COUNT(mit.id)
	FROM meeting mit LEFT JOIN cycle cy ON mit.cycleId = cy.id
							LEFT JOIN vsla vs ON cy.vslaId = vs.id
	WHERE cy.`status` = 'Encours') AS nbMeeting
FROM vsla
WHERE id = '{$vslaId}'");

while ($res = $data->fetch()) {
    $stats = array_merge($stats, array(['nbMember'=>$res['nbMember'], 'nbAccount'=>$res['nbAccount'],
            'nbCreditRequest'=>$res['nbCreditRequest'], 'Outstanding'=>$res['Outstanding'], 'nbDonation'=>$res['nbDonation'],
            'nbMeeting'=>$res['nbMeeting']]));
}

print(json_encode($stats));