<?php 

class GroupDataReader{
	
	
	private static function readGroupData(){
		$content = file_get_contents(WAI_JSON);
		return json_decode($content, true);
	}
	
	private static function writeGroupData($groupdatas){
		file_put_contents(WAI_JSON, json_encode($groupdatas));
	}
	
	public static function getAllGroups(){
		$groupdatas = self::readGroupData();
		if(	isset($groupdatas) ){
			$groups = array();
			foreach($groupdatas as $key => $value){
				$groups[] = $key;
			}
			return $groups;
		}
		return array();
	}
	
	public static function getClassIdByGroup($chat_id){
		
		$groupdatas = self::readGroupData();
		
		if(	isset($groupdatas) 
			&& array_key_exists($chat_id, $groupdatas) 
			&& array_key_exists('noClass', $groupdatas[$chat_id])){
			return $groupdatas[$chat_id]['noClass'];
		}
		return false;
	}
	
	public static function setClassIdByGroup($chat_id, $noClass){
		$groupdatas = self::readGroupData();
		$groupdatas[$chat_id]['noClass'] = $noClass;
		self::writeGroupData($groupdatas);
	}
}