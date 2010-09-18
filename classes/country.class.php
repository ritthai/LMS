<?php
class Country {
	public static function GetName($id) {
		$ret = db_query("SELECT country.name
					FROM		countries		AS country
					WHERE		country.id = '%d'
					LIMIT		0, 1
					",
					$id);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in Country::GetName');
            return array();
        }
        $ret = db_get_result($ret);
        return $ret;
	}
	public static function GetID($name) {
		$ret = db_query("SELECT country.id
					FROM		countries		AS country
					WHERE		STRCMP(country.name,'%s')=0
					LIMIT		0, 1
					",
					$name);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in Country::GetID');
            return array();
        }
        $ret = db_get_result($ret);
        return $ret;
	}
	public static function Create($name) {
		$res = db_query( 'INSERT INTO countries (name)
                            VALUES (\'%s\')',
                            $name);
        if($res) {
            return mysql_insert_id();
        } else {
            Error::generate('notice', 'name already taken.');
            return false;
        }
	}
	public static function ListAll() {
		$ret = db_query("SELECT country.id, country.name
					FROM		countries		AS country
					ORDER BY	country.name");
		if(!$ret) {
            Error::generate('debug', 'Could not query db in Country::ListAll');
            return array();
        }
        $ret = db_get_list_assoc($ret);
        return $ret;
	}
	public static function ListAllMatching($val) {
		$ret = db_query("SELECT country.id, country.name
					FROM		countries		AS country
					WHERE		country.name REGEXP '.*%s.*'
					ORDER BY	country.name",
						$val
						);
		if(!$ret) {
            Error::generate('debug', 'Could not query db in Country::ListAll');
            return array();
        }
        $ret = db_get_list_assoc($ret);
        return $ret;
	}
}
