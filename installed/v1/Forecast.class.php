<?php
/* Copyright (C) 2011-2014 by iRail vzw/asbl
 *
 * Author: Quentin Kaiser <contact@quentinkaiser.be>
 * License: AGPLv3
 *
 * This method of BeRoads TDT will get forecast about trafic jams and travel times in belgium
 */

include_once getcwd()."/../installed/simple_html_dom.php";

class Forecast {


	public static function getParameters(){
		return array(
				'type' => array(
					'required' => true,
					'description' => 'The type of forecast that you want to retrieve. Available : traveltime, traficjam.'
					)  
				);
	}

	public function setParameter($key,$val){
		$this->$key = $val;
	}


	public function getData(){

		$results = array();
		if(!strcmp($this->type, "traficjam")){
			$url = "http://www.rtbf.be/services/mobilinfo/previsions-trafic";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
			$html = str_get_html($response);

			$times = $html->find('div[class=head] span');
			$lengths = $html->find('span[class=indicationPannel]');

			for($i=0; $i<count($times); $i++){
				$element = new stdClass();
				preg_match("/(\d\d)-(\d\d)-(\d\d\d\d)?/",$times[$i]->innertext,$match);
				$element->time = mktime(0, 0, 0, $match[2], $match[1], $match[3]);
				preg_match("/(\w+)<br \/>(\d+)-(\d+) km?/", $lengths[$i]->innertext, $match);
				$element->min = $match[2];
				$element->max = $match[3];              
			}
			array_push($results, $element);
		}
		else if(!strcmp($this->type, "traveltime")){
			$url = "http://www.rtbf.be/services/mobilinfo/temps-parcours";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);

			$html = str_get_html($response);
			$forecasts = $html->find('div[id=mobilTabs-2] table tr');
			for($i=1; $i < count($forecasts); $i++){

				$element = new stdClass();
				$tds = $forecasts[$i]->find('td');
				for($j=0; $j<count($tds); $j++){
					if($j==0)
						$element->from = $tds[$j]->innertext;
					if($j==1)
						$element->to = $tds[$j]->innertext;
					if($j==2){
						preg_match("/(\d+) mins (\+(\d+))?/", $tds[$j], $match);
						$element->current_time = $match[1];
					}
					if($j==3){
						preg_match("/(\d+) mins?/", $tds[$j], $match);
						$element->normal_time = $match[1];
					}
				}
				array_push($results, $element);
			}		
		}		
		$f = new stdClass();
		$f->Forecast = new stdClass();
		$f->Forecast->item = $results;
		return $f;
	}

}
?>
