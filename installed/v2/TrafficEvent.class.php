<?php
/* Copyright (C) 2011-2014 by iRail vzw/asbl
 *
 * Author: Quentin Kaiser <contact@quentinkaiser.be>
 * License: AGPLv3
 *
 * This method of BeRoads TDT will get the traffic events of Belgian traffic jams, accidents and works
 */
include_once getcwd()."/../installed/Beroads.class.php";

class TrafficEvent extends BeRoads {

	public static function getParameters(){
		return array(

				'lang' => array(
					'required' => true, 
					'description' => 'Language in which the newsfeed should be returned'
					), 
				'region' => array(
					'required' => true,
					'description' => 'Region that you want data from'
					),
				'id' => array(
					'required' => false,
					'description' => 'ID of the event you want to retrieve'
					),
				'max' => array(
					'required' => false,
					'description' => 'Maximum of events that you want to retrieve'
					),
				'from' => array(
					'required' => false,
					'description' => 'Geographic coordinates you want data around (format : lat,lng)'
					),
				'area' => array(
						'required' => false,
						'description' => 'Area around <from> where you want to retrieve events'
						),
				'offset' => array(
						'required' => false,
						'description' => 'Offset let you request events with pagination'
						)
					);
	}

	public function setParameter($key,$val){
		if((!strcmp($key, 'region') || !strcmp($key, 'lang')) && !strcmp($val, 'all'))
			$this->$key = '%';
		else
			$this->$key = $val;
	}

	public function getData() {

		if(Input::has('id')){
			$query = "SELECT * FROM trafic WHERE id = ". Input::get('id'); 
			$rows = DB::select($query);
		}
		else{
			$query = "SELECT * FROM trafic WHERE language LIKE '" . (isset($this->lang) ? $this->lang : '%') . "' AND region LIKE '" . (isset($this->region) ? $this->region : '%') . "' AND active = 1"; 
			$rows = $this->filter(DB::select($query));
		}
		return $rows;
	}
}
?>
