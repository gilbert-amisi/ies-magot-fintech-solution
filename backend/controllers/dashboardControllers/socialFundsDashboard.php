<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donnees1 = array();
$donnees2 = array();
$entete = array();

$vslaId = $_GET['vslaId'];
// $vslaId = 1;

$data = MagoConnection::connect()->query("SELECT DISTINCT
cy.name, 
SUM(IFNULL(sp.amount, 0)) AS depense,
SUM(IFNULL(dp.amount, 0))/(COUNT(IFNULL(dp.id, 0))) AS depot
FROM 
cycle AS cy
LEFT OUTER JOIN 
spending AS sp ON cy.id = sp.cycleId AND sp.objective = 'Assistance sociale'
LEFT OUTER JOIN 
deposit AS dp ON cy.id = dp.cycleId AND dp.`type` = 'Social found'
WHERE
 cy.vslaId = '{$vslaId}'
GROUP BY 
cy.name");

while ($res = $data->fetch()) {
    $entete = array_merge($entete, array($res['name']));
    $donnees1 = array_merge($donnees1, array(round($res['depense'], 2)));
    $donnees2 = array_merge($donnees2, array(round($res['depot'], 2)));
}

$series = array(
    [
      'name' => 'Deposit',
    // 'type' => 'column',
    'data' => $donnees2
    ],
    [
        'name' => 'Spending',
        // 'type' => 'column',
        'data' => $donnees1
    ]
  );
  
  $chartOptions = array(
    'chart' => array(
      'height' => 270,
      'type' => 'line',
      'dropShadow' => array(
        'enabled' => true,
        'color' => '#000',
        'top' => 18,
        'left' => 7,
        'blur' => 10,
        'opacity' => 0.2
      ),
      'toolbar' => array(
        'show' => false
      )
      ),
      'colors'  => ['#77B6EA', '#545454'],
      'dataLabels' => array(
        'enabled' => true,
      ),
      'stroke' => array(
        'curve' => 'smooth'
      ),
      'title' => array(
        'text' => 'Use of social fund',
        'align' => 'left'
      ),
      'grid' => array(
        'borderColor' => '#e7e7e7',
        'row' => array(
          'colors' => ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
          'opacity' => 0.5
        ),
      ),
      'markers' => array(
        'size' => 1
      ),
      'xaxis' => array(
        'categories' => $entete,
        'title' => array(
          'text' => 'Cycle'
        )
      ),
      'yaxis'=> array(
        'title'=> array(
          'text'=> 'Amount'
        )
      ),
      'legend'=> array(
        'position'=> 'top',
        'horizontalAlign'=> 'right',
        'floating'=> true,
        'offsetY'=> -25,
        'offsetX'=> -5
      )
    );
    
  $dataS2 = array([
    'chartOptions'=>$chartOptions,
    'series' => $series
  ]);
  print(json_encode($dataS2));
  