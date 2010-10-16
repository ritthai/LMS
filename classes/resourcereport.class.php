<?php
class ResourceReport {
	public static function Create($rrcfg) {
		if(!isset($rrcfg['user_id'])
			|| !isset($rrcfg['resource_id'])
			|| !$rrcfg['user_id'])
		{
			return false;
		}
		db_query(	"REPLACE INTO resourcereports (resource_id, user_id, comments, type)
						VALUES ('%d', '%d', '%s', '%d')",
					$rrcfg['resource_id'], $rrcfg['user_id'], $rrcfg['comments'], $rrcfg['type']
					);
		return true;
	}
	public static function ListAll() {
		$res=db_query("	SELECT id, resource_id, user_id, comments, creation_timestamp, type
						FROM resourcereports
						ORDER BY status, creation_timestamp DESC",
					$comment_id
				);
		return db_get_list_of_assoc($res);
	}
	public static function Get($id) {
		$res=db_query("	SELECT *
						FROM resourcereports
						WHERE id='%d'
						LIMIT 1",
					$id
				);
		return db_get_assoc($res);
	}
}
