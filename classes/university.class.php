<?php
class University {
	public static function GetAreaID($id) {
		$ret = db_query("SELECT uni.area
					FROM		universities	AS uni,
								areas			AS area
					WHERE		area.id = uni.area
						AND		uni.id = '%d'
					LIMIT		0, 1
					",
					$id);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in University::GetAreaID');
            return array();
        }
        $ret = db_get_result($ret);
        return $ret;
	}
	public static function GetName($id) {
		$ret = db_query("SELECT uni.name
					FROM		universities	AS uni
					WHERE		uni.id = '%d'
					LIMIT		0, 1
					",
					$id);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in University::GetName');
            return array();
        }
        $ret = db_get_result($ret);
        return $ret;
	}
	public static function GetID($name, $area_id=false, $country_id=false) {
		if($area_id) {
			$area_constraint = "AND area.id = '$area_id'";
		} else {
			$area_constraint = '';
		}
		if($country_id) {
			$country_constraint = "AND country.id = '$country_id'";
		} else {
			$country_constraint = '';
		}
		$ret = db_query("SELECT uni.id
					FROM		universities	AS uni,
								areas			AS area,
								countries		AS country
					WHERE		STRCMP(uni.name,'%s')=0
						AND		uni.area = area.id
						AND		area.country = country.id
								$area_constraint
								$country_constraint
					LIMIT		0, 1
					",
					$name);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in University::GetID');
            return array();
        }
        $ret = db_get_result($ret);
        return $ret;
	}
	public static function Create($name, $area_id) {
		$res = db_query( 'INSERT INTO universities (name, area)
                            VALUES (\'%s\', \'%d\')',
                            $name, $area_id);
        if($res) {
            return mysql_insert_id();
        } else {
            Error::generate('notice', 'name already taken.');
            return false;
        }
	}
	public static function ListAll($area_id=false, $country_id=false) {
		if($area_id) {
			$area_constraint =	"AND area.id = '$area_id'";
		} else {
			$area_constraint = '';
		}
		if($country_id) {
			$country_constraint =	"AND area.country = country.id \
									 AND country.id = '$country_id'";
		} else {
			$country_constraint = '';
		}
		$ret = db_query("SELECT uni.id, uni.name, area.name, country.name
					FROM		universities	AS uni,
								areas			AS area,
								countries		AS country
					WHERE		uni.area = area.id
						AND		area.country = country.id
								$area_constraint
								$country_constraint
					ORDER BY	uni.name");
		if(!$ret) {
            Error::generate('debug', 'Could not query db in University::ListAll');
            return array();
        }
        $ret = db_get_list_result($ret);
        return $ret;
	}
	public static function ListAllMatching($area_id=false, $country_id=false, $val) {
		if($area_id) {
			$area_constraint =	"AND area.id = '$area_id'";
		} else {
			$area_constraint = '';
		}
		if($country_id) {
			$country_constraint =	"AND country.id = '$country_id'";
		} else {
			$country_constraint = '';
		}
		$ret = db_query("SELECT uni.id, uni.name, area.name, country.name
					FROM		universities	AS uni,
								areas			AS area,
								countries		AS country
					WHERE		uni.name REGEXP '.*%s.*'
						AND		uni.area = area.id
						AND		area.country = country.id
								$area_constraint
								$country_constraint
					ORDER BY	uni.name",
						$val
						);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in University::ListAll');
            return array();
        }
        $ret = db_get_list_result($ret);
        return $ret;
	}
}
