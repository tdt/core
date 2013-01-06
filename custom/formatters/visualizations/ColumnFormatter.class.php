<?php

/**
 * The Html formatter formats everything for development purpose
 *
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Lieven Janssen <lieven.janssen@okfn.org>
 */

/**
 * This class inherits from the abstract Formatter. It will generate a column chart
 */
class ColumnFormatter extends AFormatter {

	private $data;
	private $category = "category";
	private $value = array();
	private $refresh = 0;
	private $sort = "";

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
?>
<head>
    <link rel="stylesheet" type="text/css" href="http://cdn.sencha.io/ext-4.0.7-gpl/resources/css/ext-all.css" />
    <script type="text/javascript" src="http://cdn.sencha.io/ext-4.0.7-gpl/ext-all.js"></script>
</head>	
<?php		
    }

    public function printBody() {
?>
<body>
<?php		
		$result = "";
		$array = get_object_vars($this->objectToPrint);
//echo (array_key_exists("data", $_GET));
		if(array_key_exists("category", $_GET)) {
			$this->category = $_GET["category"];
		}
		if(array_key_exists("value", $_GET)) {
			$this->value = explode(",",$_GET["value"]);
		} else {
			$this->getValues($array);
		}
		if(array_key_exists("refresh", $_GET)) {
			$this->refresh = $_GET["refresh"];
		}
		if(array_key_exists("sort", $_GET)) {
			$this->sort = $_GET["sort"];
		}


		$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                $url = str_replace(".column?",".chartdata?", $url);		
?>
		<script type="text/javascript">
			Ext.require('Ext.chart.*');

			Ext.onReady(function () {
			
			var store = Ext.create('Ext.data.Store', {
						fields: [{name: '<?php echo $this->category ?>',  type: 'string'},
						<?php
						$firstrow = true;
						foreach($this->value as $value) {
							if(!$firstrow) {
								echo ",";
							}
							echo "{name: '$value', type: 'int'}";
							$firstrow = false;
						}
						?>],	
						autoLoad: true,
						<?php if ($this->sort != "") { ?>
						sorters: <?php echo($this->sort); ?>,
						<?php } ?>						
						proxy: {
							type: 'ajax',
							url: '<?php echo $url ?>',
							reader: {
								type: 'json'
							}
						}
					});
			<?php if ($this->refresh > 0) { ?>
			var intr = setInterval(function() {
				store.load();
			}, <?php echo($this->refresh); ?> * 60 * 1000);			
			<?php } ?>
			var view = new Ext.Viewport({
				renderTo: Ext.getBody(),
				layout:'fit',
				items:[{
					xtype: 'chart',
					animate: true,
					store: store,
					theme: 'Category1',
					legend: {
						position: 'right'
					},
					axes: [{
						type: 'Numeric',
						position: 'left',
						fields: [<?php
						$firstrow = true;
						foreach($this->value as $value) {
							if(!$firstrow) {
								echo ",";
							}
							echo "'$value'";
							$firstrow = false;
						}
						?>],
						grid: true,
						minimum: 0
					},{
						type: 'Category',
						position: 'bottom',
						fields: ['<?php echo $this->category ?>'],
						label: {
							rotate: {
								degrees: 90
							}
						}
					}],
					series: [{
						type: 'column',
						axis: 'left',
						label: {
						  display: 'insideEnd',
						  'text-anchor': 'middle',
							field: [<?php
						$firstrow = true;
						foreach($this->value as $value) {
							if(!$firstrow) {
								echo ",";
							}
							echo "'$value'";
							$firstrow = false;
						}
						?>],
							renderer: Ext.util.Format.numberRenderer('0'),
							orientation: 'vertical',
							color: '#333'
						},
						xField: '<?php echo $this->category ?>',
						yField: [<?php
						$firstrow = true;
						foreach($this->value as $value) {
							if(!$firstrow) {
								echo ",";
							}
							echo "'$value'";
							$firstrow = false;
						}
						?>]
					}]
				}]
			  });
			});			
		</script>
</body>
</html>
<?php
	}
    
    public static function getDocumentation(){
        return "This formatter generates a column chart.";
    }
	
	private function getValues($array) {
		foreach($array as $key => $val){
			if(is_object($val)){
				$array = get_object_vars($val);
				$this->getValues($array);
				break;
			} else if(is_array($val)) {
				$array = $val;
				$this->getValues($array);
				break;
			} else {
				if($this->startsWith($key, "l")) {
					array_push($this->value,$key);
				}
			}
		}
	}
	
	private function startsWith($haystack, $needle) {
	   return (strpos($haystack, $needle) === 0);
	}

	private function getChartData($array) {
		$data = "";
		$childdata = "";
		$firstrow = true;
		foreach($array as $key => $val){
			if(is_object($val)){
				$array = get_object_vars($val);
				$childdata = $this->getChartData($array);
				if ($childdata != "") {
					if (!$firstrow) {
						$this->data .= ",";
					}
					$this->data .= '{' . $childdata . '}';
					$firstrow = false;
				}
			} else if(is_array($val)) {
				$array = $val;
				$childdata = $this->getChartData($array);
				if ($childdata != "") {
					if (!$firstrow) {
						$this->data .= ",";
					}
					$this->data .= '{' . $childdata . '}';
					$firstrow = false;
				}
			} else {
				if($key == $this->category || in_array($key, $this->value)) {
					if (!$firstrow) {
						$data .= ",";
					}
					$data .= "\"" . $key . "\":\"" . $val . "\"";
					$firstrow = false;
				}
			}
		}
		return $data;
	}	
}

;
?>
