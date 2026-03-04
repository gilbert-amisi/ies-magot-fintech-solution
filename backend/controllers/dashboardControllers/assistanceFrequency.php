<?php

include '../../models/database/connection.php';

$fd = json_decode(file_get_contents("php://input"));
header('Access-Control-Allow-Header: *');
header('Access-Control-Allow-Origin:*');

$donnees = array();
$entete = array();

$vslaId = $_GET['vslaId'];

$data1 = MagoConnection::connect()->query("SELECT DISTINCT SUM(meet.socialCase) AS socialcase, cy.name AS anneeSocial
FROM meeting meet LEFT JOIN cycle cy ON meet.cycleId = cy.id
						LEFT JOIN vsla vs ON cy.vslaId = vs.id
WHERE vs.id = '{$vslaId}'
GROUP BY cy.name
ORDER BY cy.name");

$data2 = MagoConnection::connect()->query("SELECT COUNT(sp.id) AS assistance, cy.name AS anneeSpending
FROM spending sp LEFT JOIN cycle cy ON sp.cycleId = cy.id
						LEFT JOIN vsla vs ON cy.vslaId = vs.id
WHERE vs.id = '{$vslaId}'
GROUP BY cy.name
ORDER BY cy.name;");

while (($res1 = $data1->fetch()) && ($res2 = $data2->fetch())) {
    if ($res1['anneeSocial'] == $res2['anneeSpending']) {
        $donnees = array_merge($donnees, array(round($res2['assistance']/$res1['socialcase']*100, 2)));
        $entete = array_merge($entete, array($res1['anneeSocial']));
    }
    
}

$series = array(
    [
      'name' => 'Assistance (%)',
    'type' => 'column',
    'data' => $donnees
    ]
  );
  $chartOptions = array(
    'colors' => array('#2d4d1f', '#752975cc', '#ba1616', '#1091b5'),
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
      'type' => 'bar',
      'stacked' => true
    //   'stackType' => '100%'
    ),
    'title' => array(
        'text' => 'Social cases assistance per cycle'
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
        'text'=> 'Pourcentage',
        'style'=> array(
          'color'=> '#000043'
        )
    ),
    'max'=>100,
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
  