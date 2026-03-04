<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donnees = array();
$entete = array();

$vslaId = $_GET['vslaId'];
$data = MagoConnection::connect()->query("SELECT COUNT(meet.id) AS meetings, cy.name AS annee
FROM meeting meet LEFT JOIN cycle cy ON meet.cycleId = cy.id
						LEFT JOIN vsla vs ON cy.vslaId=vs.id
WHERE vs.id = '{$vslaId}'
GROUP BY cy.name
ORDER BY cy.name ASC");

while ($res = $data->fetch()) {
    $entete = array_merge($entete, array($res['annee']));
    $donnees = array_merge($donnees, array($res['meetings']));
}

$series = array(
    [
      'name' => 'Meetings',
    'type' => 'column',
    'data' => $donnees
    ]
  );
  $chartOptions = array(
    'colors' => array('#51a1ad', '#780778', '#bcb4e0'),
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
      'enabledOnSeries'=> [0]
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
        'text'=> 'Number of meetings per year',
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
  