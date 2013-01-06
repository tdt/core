<?php

/**
 * The Map Vizualization vizualizes everything for development purposes
 *
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert <pieter.colpaert @ UGent.be>
 * @author Lieven Janssen <lieven.janssen@okfn.org>
 */

/**
 * This class inherits from the abstract Formatter. It will generate a Map
 */
class MapFormatter extends AFormatter {

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
    }

    public function printBody() {
        //When the objectToPrint has a property Ontology, it is an RDF Model
        //In this case, use the nice HTML formatting function
?>
<html> 
<head>
	<style type="text/css">
		body
		{
			margin: 0;
			padding: 0;
		}
		#map {
		width: 100%;
		height: 100%;
		}
		.olPopup p { margin:0px; }
	</style>

<!--	
	<title></title>
-->
	
	<?php
    
              $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $url = str_replace(".map",".kml",$url);

    $url = str_replace(":map",":kml",$url);


    // temporary but nice fix: redirect to google maps
    header("location: http://maps.google.com/?q=" . urlencode($url));
	?>
	
	<script src="http://openlayers.org/api/2.11/OpenLayers.js"></script>
	
	<script type="text/javascript">
		var markers = new OpenLayers.Layer.Markers( "Markers");
		var map;
		
		function init()
		{
			map = new OpenLayers.Map ("map", {
			controls:[
			new OpenLayers.Control.Navigation(),
			new OpenLayers.Control.PanZoomBar(),
			new OpenLayers.Control.LayerSwitcher(),
			new OpenLayers.Control.Attribution(),
			new OpenLayers.Control.Permalink(),
			new OpenLayers.Control.ScaleLine(),
			new OpenLayers.Control.OverviewMap(),
			new OpenLayers.Control.MousePosition()],
			maxResolution: 156543.0399,
			numZoomLevels: 19,
			units: 'm',
			projection: new OpenLayers.Projection("EPSG:900913"),
			displayProjection: new OpenLayers.Projection("EPSG:4326")
			} );

			map.addLayer(markers);

			
			map.addLayer(new OpenLayers.Layer.OSM());
			
			var center = new OpenLayers.LonLat(153.02775, -27.47558).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
			
			var zoom = 11
			map.setCenter(center, zoom);			
			
			var urlAddy = "<?php echo($url); ?>";
			if(urlAddy.length > 2)
			{
				vectorLayer = new OpenLayers.Layer.GML("KML", '<?php echo($url); ?>', 
				{
				projection: new OpenLayers.Projection("EPSG:4326"),
				eventListeners: { 'loadend': kmlLoaded },
				format: OpenLayers.Format.KML, 
				formatOptions: {
				style: {strokeColor: "green", strokeWidth: 5, strokeOpacity: 0.5},
				extractStyles: true, 
				maxDepth: 2,
				extractAttributes: true
				}
				})
				
				map.addLayer(vectorLayer);
		
				selectControl = new OpenLayers.Control.SelectFeature(map.layers[5],
				{onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});
				map.addControl(selectControl);
				selectControl.activate();
			}
			
			var click = new OpenLayers.Control.Click();
            map.addControl(click);
            click.activate();
		}
		
		function onPopupClose(evt) 
		{
			selectControl.unselect(selectedFeature);
		}
			
		function onFeatureSelect(feature) 
		{
			selectedFeature = feature;
			popup = new OpenLayers.Popup.FramedCloud("chicken", 
			feature.geometry.getBounds().getCenterLonLat(),
			new OpenLayers.Size(100,150),
			"<div style='font-size:.8em'><b>Name:</b>"+feature.attributes.name+"<br><b>Description:</b>"+feature.attributes.description+"</div>",
			null, true, onPopupClose);
			feature.popup = popup;
			map.addPopup(popup);
		}
		
		function onFeatureUnselect(feature) 
			{
			map.removePopup(feature.popup);
			feature.popup.destroy();
			feature.popup = null;
			}
		
		function kmlLoaded()
			{
			map.zoomToExtent(vectorLayer.getDataExtent());
			}
			
	</script>
</head>

<body onload="init()">
	<div id="map"></div>
</body>

<?php
    }
    
    public static function getDocumentation(){
        return "The Osm formatter is a formatter that generates a map visualisation.";
    }
    
}

;
?>