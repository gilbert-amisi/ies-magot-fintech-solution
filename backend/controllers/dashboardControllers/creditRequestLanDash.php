<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donnees1 = array();
$donnees2 = array();
$entete = array();

$vslaId = $_GET['vslaId'];
$data = MagoConnection::connect()->query("SELECT SUM(c.amount) AS demande, SUM(l.amount) AS credit, cy.name
FROM loan l LEFT JOIN creditrequest c ON l.creditId = c.id
				LEFT JOIN cycle cy ON c.cycleId = cy.id
				LEFT JOIN vsla v ON cy.vslaId = v.id
WHERE v.id = '{$vslaId}'
GROUP BY cy.name
ORDER BY cy.name ASC");

while ($res = $data->fetch()) {
    $entete = array_merge($entete, array($res['name']));
    $donnees1 = array_merge($donnees1, array($res['demande']));
    $donnees2 = array_merge($donnees2, array($res['credit']));
}

$series = array(
    [
      'name' => 'Credits request',
    'type' => 'column',
    'data' => $donnees1
    ],
    [
        'name'=>'Loans',
        'type'=>'column',
        'data'=>$donnees2
    ]
  );
  
  $chartOptions = array(
    'colors' => array('#04121fcc', '#612222'),
    'animations' => array(
      'enabled' => true,
      'easing' => 'easeinout',
      'speed' => 1000
    ),
    'grid' => array(
      'show' => true,
      'strokeDashArray' => 0,
      'xaxis' => array(
        'lines' => array(
          'show' => true
        )
      )
    ),
    'chart' => array(
      'type' => 'area'
    ),
    'title' => array(
        'text' => 'Credits requested vs loans granted'
    ),
    'fill' => array(
      'type' => 'gradient',
      'gradient' => array(
        'shade'=> 'dark',
        'type'=> 'vertical',
        'shadeIntensity'=> 0.5,
        'inverseColors'=> false,
        'opacityFrom'=> 1,
        'opacityTo'=> 0.8,
        'stops'=> array (0, 100)
      )
    ),
    'dataLabels'=> array(
      'enabled'=> true,
      'enabledOnSeries'=> [0, 1]
    ),
    'stroke'=> array (
      'width'=> [0, 4]
  ),
    'xaxis'=> array (
      'categories'=>$entete,
      'type'=>'date',
      'labels'=> array (
        'style'=> array (
          'colors'=> '#000043'
        )
      )
    ),
    'yaxis'=>array (
      'title'=> array(
        'style'=> array(
          'color'=> '#000043'
      )
    ),
      'labels'=> array (
        'style'=> array (
          'colors'=> '#000043'
        )
      )
    )
        );
  $dataS2 = array([
    'chartOptions'=>$chartOptions,
    'series' => $series
  ]);
  print(json_encode($dataS2));
  