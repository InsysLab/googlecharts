<?php

include('gchart.php');
$chart = new GChart( array('Health' => 4.5, 
						   'Infrastructure' => 3,
						   'Agriculture' => 2,
						   'Education' => 4,
						   'Defense' => 6) );

$chart->headScript();
$chart->render('pie');