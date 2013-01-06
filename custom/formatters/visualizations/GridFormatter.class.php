<?php

/**
 * The Html formatter formats everything for development purpose
 *
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Miel Vander Sande 
 */

include_once("custom/formatters/HtmltableFormatter.class.php");
 
 /**
 * This class inherits from the HtmlFormatter. It will generate a extjs grid
 */
class GridFormatter extends HtmlTableFormatter {

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="http://cdn.sencha.io/ext-4.0.7-gpl/resources/css/ext-all.css" />
    <style type="text/css">
        #the-table { 
            border:1px solid #bbb;
            border-collapse:collapse; 
        }
        #the-table td,#the-table th { 
            border:1px solid #ccc;
            border-collapse:collapse;
            padding:5px; 
        }
    </style>
    <script type="text/javascript" src="http://cdn.sencha.io/ext-4.0.7-gpl/ext-all.js"></script>
	<script type="text/javascript">
		Ext.Loader.setConfig({
			enabled: true
		});
		Ext.Loader.setPath('Ext.ux', 'http://cdn.sencha.io/ext-4.0.7-gpl/examples/ux');

		Ext.require([
			'Ext.data.*',
			'Ext.grid.*',
			'Ext.ux.grid.TransformGrid'
		]);

		Ext.onReady(function(){
			//btn.dom.disabled = true;

			// create the grid
			var grid = Ext.create('Ext.ux.grid.TransformGrid', "the-table", {
				stripeRows: true
			});
			grid.render();
		});
	</script>	
	
</head>	
<?php		
    }

    public static function getDocumentation(){
        return "The Html formatter is a formatter for developing purpose. It prints everything in the internal object.";
    }
    
}
?>