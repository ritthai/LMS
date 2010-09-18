<?php
class File extends EavAdjList {
	/*
		Known parents:
			1: Course notes
			2: Avatar
	*/
	protected static function subGetClass() {
		return 'file';
	}
	protected static function subGetAttribs() {
		return array(	1=>	array( 'NAME',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'PATH',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'OWNER',		static::ATTRIB_TYPE_INT ,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'ROLES',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'TYPE',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'COMMENT',	static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE )
					);
	}
	/**
		Public Functions
	*/
	public static function Init() {
		if($__inited) return;
		else $__inited = true;
	}
	public static function Create($userCfg, $uploadPath) {
		global $ROOT;
		($owner = $userCfg['owner']) or $owner = 0;
		($context = $userCfg['context']) or $context = 1;
		$id = static::eav_create($userCfg['name'], $owner, $context);
		if($id < 1) { // pretty sure this shouldn't happen with current schema
			Error::generate('notice', 'Filename already taken.', Error::$FLAGS['override']);
			return false;
		}
		// Copy the file from the uploadPath to the given userCfg['path']
		if(!isset($userCfg['path']) || !move_uploaded_file($uploadPath, "$ROOT/".$userCfg['path'])) {
			Error::generate('debug', 'Could not move file');
			return false;
		}
		foreach($userCfg as $attrib => $val) {
			switch(strtoupper($attrib)) {
			default:
				$storeval = $val;
			}
			static::store_attrib($id, $attrib, $storeval);
		}
		return $id;
	}
	public static function ListAll($id=1, $type=1) {
		$ret = static::eav_list($id, $type) or array();
		return array_reverse($ret, true);
	}
	public static function GetAttrib($id, $attrib) {
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
		}
		if($attribid && !(static::get_attrib_props($attribid) & static::ATTRIB_PROP_NODISPLAY)) {
			$ret = static::get_attrib($id, $attribid);
		} else {
			$ret = false;
		}
		return $ret;
	}
	public static function SetAttrib($id, $attrib, $val) {
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
		}
		if($attribid && !(static::get_attrib_props($attribid) & static::ATTRIB_PROP_READONLY)) {
			switch(strtoupper(static::get_attrib_str($attribid))) {
			default:
				$storeval = $val;
			}
			$ret = static::store_attrib($id, $attribid, $storeval);
		} else {
			$ret = false;
		}
		return $ret;
	}
	public static function GetAttribs($id) {
		return static::get_all_attribs($id);
	}
}
