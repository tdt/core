<?php
/* Copyright (C) 2011-2014 by iRail vzw/asbl
 *
 * Author: Quentin Kaiser <contact@quentinkaiser.be>
 * License: AGPLv3
 *
 * This method of BeRoads TDT will get all the radars of belgium territory
 */
include_once getcwd()."/../installed/Beroads.class.php";
include_once getcwd()."/../installed/simple_html_dom.php";

class Radar extends BeRoads {

	public static function getParameters(){
		return array(
				"max" => array(
					"required" => false,
					"description" => "Maximum of radars you want to retrieve"
					),
				"from" => array(
					"required" => false,
					"description" => "Geographic coordinates that you want data around (format : latitude,longitude)"
					),
				"offset" => array(
					"required" => false,
					"description" => "Offset let you request radars with pagination"
					)
				);
	}

	public function getData(){

		//we get fixed radars and set the date to today's date
		$radars = DB::select("SELECT * FROM radars");
		foreach( $radars as $radar){
			$radar->date = date('d-m-Y');
			$radar->type = "fixed";
		}

		//mobile radars scraping from fedpol
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"www.polfed-fedpol.be/verkeer/verkeer_radar_fr.php");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$html = str_get_html($response);

		$today = date('d');
		$found = false;
		$i = 0;
		foreach($html->find('TABLE[width=600]') as $table){
			foreach($table->find('span[class=textehome]') as $span){

				foreach($span->find('a') as $a){
					$date = $a->name."-".date('m-Y');
					if($a->name == $today){
						$found = true;          
					}
				}
			}
			if($found){
				foreach($table->find('TR') as $tr){
					$name = "";
					foreach($tr->find('TD[class=textehome]') as $td){
						if($td->width == "143"){
							$name = $td->plaintext;
						}
						else if($td->width!=25 && $td->width!=40 && $td->bgcolor == "#F5F5FC" && !strstr($td, 'center')){
							$name .= " ".$td->plaintext;
						}
					}
					if($name != ""){
						$radar = new stdClass();            
						$radar->date = $date;
						$radar->address = utf8_encode($name);
						$radar->type = "mobile";
						$radar->speedLimit = 0;
						$radar->name = utf8_encode($name);
						$coordinates = Geocoder::geocode("Belgium, ".$radar->name);
						$radar->lat = $coordinates["latitude"];
						$radar->lng = $coordinates["longitude"];
						array_push($radars, $radar);
					}
				}
			}
		}
		for($i=0; $i < count($radars); $i++){
			$radars[$i]->id = $i; 
		}
		$r = new stdClass();
		$r->Radar = new stdClass();
		$r->Radar->item = $this->filter($radars);
		return $r; 
	}
}
?>
