<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donnees1 = array();
$donnees2 = array();
$donnees3 = array();
$entete = array();

$vslaId = $_GET['vslaId'];
$data = MagoConnection::connect()->query("SELECT ROUND(AVG(meet.participant)) AS participant, ROUND(AVG(meet.men)) AS men, 
ROUND(AVG(meet.women)) AS women, cy.name AS annee
FROM meeting meet LEFT JOIN cycle cy ON meet.cycleId = cy.id
						LEFT JOIN vsla vs ON cy.vslaId = vs.id
WHERE vs.id = '{$vslaId}'
GROUP BY cy.name
ORDER BY cy.name ASC");

while ($res = $data->fetch()) {
    $entete = array_merge($entete, array($res['annee']));
    $donnees1 = array_merge($donnees1, array($res['participant']));
    $donnees2 = array_merge($donnees2, array($res['men']));
    $donnees3 = array_merge($donnees3, array($res['women']));
}

$series = array(
    [
      'name' => 'Participants',
    'type' => 'column',
    'data' => $donnees1
    ],
    [
        'name' => 'Men',
        'type' => 'column',
        'data' => $donnees2
    ],
    [
        'name' => 'Women',
        'type' => 'column',
        'data' => $donnees3
    ]
  );
  $chartOptions = array(
    'colors' => array('#911111', '#d1cc64', '#0a2b52'),
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
      'type' => 'bar'
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
      'enabledOnSeries'=> [0, 1, 2]
    ),
    'stroke'=> array (
      'width'=> [0, 4]
  ),
    'xaxis'=> array (
      'categories'=>$entete,
      'type'=>'year',
      'labels'=> array (
        'style'=> array (
          'colors'=> '#000043'
        )
      )
    ),
    'yaxis'=>array (
      'title'=> array(
        'text'=> 'Annual average attendance at meetings',
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
  