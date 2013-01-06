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
 * This class inherits from the abstract Formatter. It will generate a pie chart
 */
class PieFormatter extends AFormatter {

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
		if(array_key_exists("category", $_GET)) {
			$this->category = $_GET["category"];
		}
		if(array_key_exists("value", $_GET)) {
			$this->value = explode(",",$_GET["value"]);
		} else {
			array_push($this->value,"value");
		}	
		if(array_key_exists("refresh", $_GET)) {
			$this->refresh = $_GET["refresh"];
		}
		if(array_key_exists("sort", $_GET)) {
			$this->sort = $_GET["sort"];
		}

              $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
              $url = str_replace(".pie?",".chartdata?", $url);
?>
		<script type="text/javascript">
			Ext.require('Ext.chart.*');

			Ext.onReady(function () {
			
			var store = Ext.create('Ext.data.Store', {
						fields: [{name: '<?php echo $this->category ?>',  type: 'string'},
						{name: '<?php echo $this->value[0] ?>', type: 'int'}],	
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
					legend: {
						position: 'right'
					},
					series: [{
						type: 'pie',
						field: '<?php echo $this->value[0] ?>',
						donut: false,
						highlight: {
						  segment: {
							margin: 20
						  }
						},
						label: {
							field: '<?php echo $this->category ?>',
							display: 'rotate',
							contrast: true,
							font: '18px Arial'
						}
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
        return "This formatter generates a pie chart.";
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
