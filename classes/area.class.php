<?php
class Area {
	public static function GetCountryID($id) {
		$ret = db_query("SELECT area.country
					FROM		areas			AS area
					WHERE		area.id = '%d'
					LIMIT		0, 1
					",
					$id);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in Area::GetCountryID');
            return array();
        }
        $ret = db_get_result($ret);
        return $ret;
	}
	public static function GetName($id) {
		$ret = db_query("SELECT area.name
					FROM		areas			AS area
					WHERE		area.id = '%d'
					LIMIT		0, 1
					",
					$id);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in Area::GetName');
            return array();
        }
        $ret = db_get_result($ret);
        return $ret;
	}
	public static function GetID($name, $country_id=false) {
		if($country_id) {
			$country_constraint =	"AND	area.country = '$country_id'";
		} else {
			$country_constraint = '';
		}
		$ret = db_query("SELECT area.id
					FROM		areas			AS area,
								countries		AS country
					WHERE		STRCMP(area.name,'%s')=0
								$country_constraint
					LIMIT		0, 1
					",
					$name);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in Area::GetID');
            return array();
        }
        $ret = db_get_result($ret);
        return $ret;
	}
	public static function Create($name, $country_id) {
		$res = db_query( 'INSERT INTO areas (name, country)
                            VALUES (\'%s\', \'%d\')',
                            $name, $country_id);
        if($res) {
            return mysql_insert_id();
        } else {
            Error::generate('notice', 'name already taken.');
            return false;
        }
	}
	public static function ListAll($country_id=false) {
		if($country_id) {
			$country_constraint = "AND country.id = '$country_id'";
		} else {
			$country_constraint = '';
		}
		$ret = db_query("SELECT area.id, area.name, country.name AS country_name
					FROM		areas			AS area,
								countries		AS country
					WHERE		area.country = country.id
								$country_constraint
					ORDER BY	area.name");
		if(!$ret) {
            Error::generate('debug', 'Could not query db in Area::ListAll');
            return array();
        }
        $ret = db_get_list_assoc($ret);
        return $ret;
	}
	public static function ListAllMatching($country_id=false, $val) {
		if($country_id) {
			$country = mysql_real_escape_string($country);
			$country_constraint =	"AND country.id = '$country_id'";
		} else {
			$country_constraint = '';
		}
		$ret = db_query("SELECT area.id, area.name, country_name
					FROM		areas			AS area,
								countries		AS country
					WHERE		area.name REGEXP '.*%s.*'
						AND		area.country = country.id
								$country_constraint
					ORDER BY	area.name",
						$val
						);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in Area::ListAll');
            return array();
        }
        $ret = db_get_list_assoc($ret);
        return $ret;
	}
}
