<?php

use Sunra\PhpSimple\HtmlDomParser;

define('OUT_DATE_FORMAT', 'd-m-Y');
define('HES_DATE_FORMAT', 'd.m.Y');

class HES_Schedule{

	/**
	 * Return the list of the available classes
	 */
	public static function getClassList(){
		$url = 'http://mobileapps.hevs.ch/HoraireBellevue/listeclasse.aspx?Filtre=FIG';

		$str = file_get_contents($url);

		$dom = HtmlDomParser::str_get_html( $str );

		$elems = $dom->find('p');

		$classes = array();
		foreach($elems as $p){
			$links = $p->find('a');
			foreach($links as $a){
				if(0 !== strpos($a->href, 'javascript:')){
					$classes[] = array(
						'id' => self::getQueryStringValue($a->href, 'NoClasse'),
						'name' => $a->plaintext
					);
				}
			}
		}
		return $classes;
	}

	/**
	 * Return the schedule of the given class id
	 * @param int $classId The id of the class
	 * @param bool $currentWeekOnly True : return the schedule for the current week only
	 *								False: return all the available schedule
	 */
	public static function getSchedule($classId, $currentWeekOnly = true){
		$url = 'http://mobileapps.hevs.ch/HoraireBellevue/Planning.aspx?NoClasse='.$classId;

		$str = file_get_contents($url);

		$dom = HtmlDomParser::str_get_html( $str );

		$elems = $dom->find('p');

		$schedule = array();
		$week = null;
		$weekDate;
		foreach($elems as $p){
			// get week date
			if(0 === strpos($p->plaintext, 'Semaine du ')){
				
				// if next week
				if(isset($week)){
					// if current week only, return the week schedule
					if($currentWeekOnly) 
						return  self::groupByDay($week);
					
					// else add week schedule in array and continue
					$week['schedule'] = self::groupByDay($week['schedule']);
					$schedule[] = $week;
				}
				
				// parse date
				$dateStr = str_replace('Semaine du ', '', $p->plaintext);
				$date = DateTime::createFromFormat(HES_DATE_FORMAT, $dateStr);
				$week = array();
				
				// format
				$weekDate = $date->format(OUT_DATE_FORMAT);
				if(!$currentWeekOnly)
					$week['date'] = $weekDate ;
			}
			else{
				$row = array();
				$titles = $p->find('b');
				foreach($titles as $b){
					if(!empty($b->plaintext)) {
						// parse start time and end time
						$text = str_replace("&nbsp;", "", $b->plaintext);
						$timeStart = substr($text, 5, 5);
						$timeEnd = substr($text, 13, 5);

						// parse date
						$addDays = self::getDayOfWeek($b->plaintext);
						$date = DateTime::createFromFormat(OUT_DATE_FORMAT, $weekDate);
						$date->add(new DateInterval('P'.$addDays.'D'));

						// parse room
						$room = substr($text, strpos($text, 'Bellevue '), 12);

						$row['date'] = $date->format(OUT_DATE_FORMAT);
						$row['time-start'] = $timeStart;
						$row['time-end'] = $timeEnd;
						$row['room'] = $room;

					}
				}
				$cours = $p->find('i');
				foreach($cours as $i){
					if(!empty($i->plaintext)){


						// parse prof and module label
						$info = explode(' - ', $i->plaintext);

						$row['prof'] = $info[0];
						$row['module'] = $info[3];
					}
				}
				if(sizeof($row) > 0)
					if($currentWeekOnly)
						$week[] = $row;
					else
						$week['schedule'][] = $row;
			}
		}
		return $schedule;
	}

	private static function groupByDay($week){
		$weekByDay = array();
		$currentDay = null;
		foreach($week as $item){
			if($currentDay != $item['date']){
				$currentDay = $item['date'];
			}
			//unset($item['date']);
			$weekByDay[$currentDay][] = $item;
		}
		return $weekByDay;
	}

	private static function getDayOfWeek($value){
		// transform : Lu/Mo  12 h 45 - 14 h 15 -  Bellevue 302
		//		  to : 0
		// transform : Ma/Di  12 h 45 - 14 h 15 -  Bellevue 302
		//		  to : 1
		// ...
		$dayofweekStr = substr($value, 0, 2);

		$match = array(
			'Lu' => 0,
			'Ma' => 1,
			'Me' => 2,
			'Je' => 3,
			'Ve' => 4,
			'Sa' => 5,
			'Di' => 6
		);
		if(!array_key_exists($dayofweekStr, $match)){
			return 0;
		}
		return $match[$dayofweekStr];

	}

	/**
	 *	Parse an url and return the query string value by the given key
	 *	@param string The url
	 *	@param string The key
	 */
	private static function getQueryStringValue($url, $key){
		$array = array();
		parse_str( parse_url( $url, PHP_URL_QUERY), $array );
		if(array_key_exists($key, $array)){
			return $array[$key];
		}
		return null;
	}
}
