<?php
/* Copyright (C) 2011 by iRail vzw/asbl */
/* 
   This file is part of iWay.

   iWay is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   iWay is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with iWay.  If not, see <http://www.gnu.org/licenses/>.

   http://www.beroads.com

   Source available at http://github.com/QKaiser/IWay
 */

/**
 * All functionnalities about geolocation (get coordinates from API like 
 * GMap, Bing or OSM; compute distance between coordinates).
 */

class Geocoder {

	/*  
		static vars so when we are asking coordinates for a place that have been geocoded previously, we return coordinates
		that we have stored before
	 */

	public static $max_over_query_retry = 5;
	public static $over_query_retry = 0;
	public static $keywords = array(
			array("fr" => "à", "en" => "in", "nl" => "in", "de" => "der"),
			array("fr" => "vers", "en" => "to", "nl" => "naar", "de" => "nach"),
			array("fr" => "la", "en" => "the", "nl" => "de", "de" => "der"),
			array("fr" => "à hauteur de", "en" => "ter hoogte van", "nl" => "ter hoogte van", "de" => "ter hoogte van"),
			array("fr"=>"en direction de", "en" => "richting", "nl" => "richting", "de" => "richting")
			);

	public static function distance($from, $to){

		$earth_radius = 6371.00; // km
		$delta_lat = $to["latitude"]-$from["latitude"];
		$delta_lon = $to["longitude"]-$from["longitude"];

		$alpha    = $delta_lat/2;
		$beta     = $delta_lon/2;
		$a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($from["latitude"])) * cos(deg2rad($to["latitude"])) * sin(deg2rad($beta)) * sin(deg2rad($beta));
		$c        = asin(min(1, sqrt($a)));
		$distance = 2*$earth_radius * $c;
		return round($distance);
	}


	public static function cmpDistances($a, $b){
		if($a == $b)
			return 0;
		else
			return ($a->distance < $b->distance) ? -1 : 1;
	}


	public static function sortByDistance($array){
		usort($array, Geocoder::cmpDistances);
	}

	/**
	 * Geocode an address with online tools such as Google Maps, OpenStreetMap (Nominatim) or Bing Maps
	 * @param $address : the address to be geocoded
	 * @param $tool : the online tool used to geocode (eg. gmap, osm, bing)
	 * @return an array of decimal coordinates ("lat"=>0, "lng"=>0)
	 */
	public static function geocode($address, $tool = "osm") {

		$coordinates = array("longitude" => 0, "latitude" =>0);
		if(!isset($address) || $address=="" || $address == null)
			return $coordinates;

		if(Cache::has($address)){
			return Cache::get($address);
		}else{
			$key = $address;

			//gmap api geocoding tool
			if($tool=="gmap") {
				$address = $address . ", Belgium";
				$request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false";
				$response = Geocoder::request($request_url);
				sleep(1);
				$json = json_decode($response);
				$status = $json->status;
				//successful geocode
				if (strcmp($status, "OK") == 0) {
					Geocoder::$over_query_retry = 0;
					$coordinates = array("longitude"=> $json->results[0]->geometry->location->lng, "latitude" => $json->results[0]->geometry->location->lat);
				}
				//too much requests, gmap server can't handle it
				else if (strcmp($status, "OVER_QUERY_LIMIT") == 0) {
					if(Geocoder::$over_query_retry < Geocoder::$max_over_query_retry){
						Geocoder::$over_query_retry++;
						$coordinates = Geocoder::geocode($address, "osm");
					}
				}
			}
			//openstreetmap geocoding tool (Nominatim)
			else if($tool=="osm") {

				$base_url = "http://nominatim.openstreetmap.org/search/be/".urlencode($address)."/?format=json&addressdetails=0&limit=1&countrycodes=be";
				$json = Geocoder::request($base_url);
				$data = json_decode($json);
				if(count($data)==0) {
					if(Geocoder::$over_query_retry < Geocoder::$max_over_query_retry){
						Geocoder::$over_query_retry++;
						$coordinates = Geocoder::geocode($address, "gmap");
					}
				}
				else {
					Geocoder::$over_query_retry=0;
					$place = $data[0];
					$coordinates = array("longitude" => (string)$place->lon, "latitude" => (string)$place->lat);
				}
			}
			else {
				throw new Exception("Wrong tool parameter, please retry.");
			}
			Cache::add($key, $coordinates, 86400);
			return $coordinates;
		}
	}

	public static function request($url) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}   

	/**
	 * Extract relevant information from a string depending on its source. Relevant information 
	 * is mainly town, street, area or highways to be geocoded later.
	 * @param data : a string to be analyzed
	 * @param source : the source of $data
	 * @param language : the language in which $data is written
	 * @return an array of decimal coordinates ("lat"=>0, "lng"=>0)
	 */
	public static function geocodeData($data, $source, $language) {

		if($source=="federal" || $source == "flanders"){
			preg_match("/[\s\S]* " . Geocoder::$keywords[0][$language] . " ([\s\S]*)/", $data, $match);
			if(count($match)==2){
				$data = $match[1];
			}
			else{
				preg_match("/[\s\S]* ([\s\S]*) -> [\s\S]*/", $data, $match);
				if(count($match)==2){
					$data = $match[1];
				}else{
					preg_match("/[\s\S]* ".Geocoder::$keywords[1][$language] . " ([\s\S]*)/", $data, $match); 
					$data = (count($match)==2?$match[1]:null);
				}
			}
		}
		else{
			throw new Exception("Wrong source parameter, please retry.");
		}
		return Geocoder::geocode($data);


	}
};
?>
