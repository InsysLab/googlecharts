<?php
class GChart{

	private $data;
	public $config;

	public function __construct($data = null){
		$this->data = $data;
		$this->chartCount = 0;
	}
	
	public function headScript(){
		echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
		echo '<script type="text/javascript">
				google.load("visualization", "1", {packages:["corechart", "gauge", "table"]}) 
			 </script>';		
	}
	
	public function render($type, $config = array(), $element = null){
		$element = $this->checkElement($element);
		$this->config = $config;
			
		echo '<script type="text/javascript">google.setOnLoadCallback(function() { ';
		echo $this->buildScript($type, $config, $element) . '})</script>';
	}
	
	public function buildScript($type, $config, $element){
		$defaultConfig = array('width' => 600, 'height' => 300,
								'vAxis' => array('color' => 'DC3912'),
								'hAxis' => array('color' => '3366CC'),
								'legend' => 'right',
								'is3D' => false);
					
		$types = array("pie"     => "google.visualization.PieChart",
					   "line"    => "google.visualization.LineChart",
					   "bar"     => "google.visualization.BarChart",
					   "column"  => "google.visualization.ColumnChart",
					   "scatter" => "google.visualization.ScatterChart",
					   "area"    => "google.visualization.AreaChart",
					   "gauge"   => "google.visualization.Gauge",
					   "table"   => "google.visualization.Table"
					   );
		
		$chart = 'var chart = new '.$types[$type].'(document.getElementById("' . $element . '"));';
		$data = $this->generateData($type != 'scatter');
		
		$config = array_merge($defaultConfig, $config);
		$execute = 'chart.draw(data, ' . json_encode($config) . ')';
		
		return $data . $chart . $execute;
	}

	private function checkElement($element){
		if (!$element){
			$element = 'chart-' . $this->chartCount++ . '-' . time() . '-' . rand();
			echo '<div id="' . $element . '"></div>';
		}
		
		return $element;		
	}
	
	public function generateData($hasLabel){
		$chart_data   = array();
 		$cols         = array();
 		$chart_values = array();
 		
 		foreach($this->data as $label => $values){			
			$cdata = $hasLabel ? array("'".addslashes($label)."'") : array();
			
			if(is_array($values)){
				foreach($values as $key => $value){
					if (is_array($value)){
						$cols[] = $value[0];
						$cdata[] = $value[1];	
					}else{
						$cols[] = $key;
						$cdata[] = $value;
					}
 				}
 			}else{		
				$cols[] = "";
				$cdata[] = $values;
			}
			
			$chart_values[] = '['.implode(',', $cdata).']';			
		}		
		
		$chart_values = implode(',', $chart_values);
				
		$col_text = '';
		if ($hasLabel)
			$col_text = "data.addColumn('string','');";
		$cols = array_unique($cols);
		
		if( ! empty($cols) ){
			foreach($cols as $data)
				$col_text .= "data.addColumn('number', '".addslashes($data)."');";		
		}
		else $col_text = "data.addColumn('number','');";
			
		$rows   = "data.addRows([". $chart_values ."]);";			
		return "var data = new google.visualization.DataTable();" . $col_text . $rows;
	}
}
