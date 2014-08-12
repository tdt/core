<?php
/* Copyright (C) 2011-2014 by iRail vzw/asbl
 *
 * Author: Quentin Kaiser <contact@quentinkaiser.be>
 * License: AGPLv3
 *
 * This method of BeRoads TDT will get the live highway cameras.
 */
include_once getcwd()."/../installed/Beroads.class.php";

class Camera extends BeRoads {

	public static function getParameters(){
		return array(
				"max" => array(
					"required" => false,
					"description" => "Maximum of cameras you want to retrieve"
					),
				"from" => array(
					"required" => false,
					"description" => "Geographic coordinates that you want data around (format : latitude,longitude)"
					),
				"area" => array(
					"required" => false,
					"description" => "Area around <from> where you want to retrieve cameras"
					),
				"offset" => array(
					"required" => false,
					"description" => "Offset let you request events with pagination"
					)
				);
	}

	public function getData(){
		if(Input::has('id')){
			$query = "SELECT * FROM cameras WHERE id = ". Input::get('id'); 
			$rows = DB::select($query);
		}
		else{
			$query = "SELECT * FROM cameras"; 
			$query .= (Input::has('enabled') ? " WHERE enabled = " . Input::get('enabled') : "");
			$rows = $this->filter(DB::select($query));
		}
		$c = new stdClass();
		$c->Camera = new stdClass();
		$c->Camera->item = $rows;
		return $c;
	}
}
?>
