<?php
class ResourceReport {
	public static function Create($rrcfg) {
		if(!isset($rrcfg['user_id'])
			|| !isset($rrcfg['comment_id'])
			|| !$rrcfg['user_id'])
		{
			return false;
		}
		db_query(	"REPLACE INTO resourcereports (comment_id, user_id, comments)
						VALUES ('%d', '%d', '%s')",
					$rrcfg['comment_id'], $rrcfg['user_id'], $rrcfg['comments']
					);
		return true;
	}
	public static function ListAll() {
		$res=db_query("	SELECT id, comment_id, user_id, comments, creation_timestamp
						FROM resourcereports
						ORDER BY status, creation_timestamp DESC",
					$comment_id
				);
		return db_get_list_of_assoc($res);
	}
	public static function Get($id) {
		$res=db_query("	SELECT *
						FROM resourcereports
						LIMIT 1",
					$comment_id
				);
		return db_get_assoc($res);
	}
}
