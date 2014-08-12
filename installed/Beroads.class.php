<?php
/* Copyright (C) 2011-2014 by iRail vzw/asbl
 *
 * Author: Quentin Kaiser <contact@quentinkaiser.be>
 * License: AGPLv3
 *
 * Generic class containing utility functions.
 */
include_once('Geocoder.php');

class BeRoads {

	protected function filter($items) {
		if(Input::has('from') && count($from = explode(',', Input::get('from')))==2){

			$area = (Input::has('area') ? Input::get('area') : 500);
			foreach($items as $item){       
				$distance = Geocoder::distance(
						array(
							"latitude"=>$from[0],
							"longitude"=>$from[1]
							),
						array(
							"latitude"=>$item->lat,
							"longitude"=>$item->lng
							)
						);
				if($distance < $area)
					$item->distance = $distance;               
				else
					unset($item);
			}
			usort($items, 'Geocoder::cmpDistances');
		} 
		return array_slice($items, (Input::has('offset') ? Input::get('offset') : 0), (Input::has('max') ? Input::get('max') : count($items))+1);
	}
}

?>
