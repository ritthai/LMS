<?php
class Pageview {
	/**
		Takes:	id - id of user who viewed the page or resource or zero for noop
				comment_id - id of the page or resource
	*/
	public static function Create($pvcfg) {
		if(!isset($pvcfg['id']) || !isset($pvcfg['comment_id']) || !$pvcfg['id']) return;
		db_query(	"REPLACE INTO pageviews (id, comment_id)
						VALUES ('%d', '%d')",
					$pvcfg['id'], $pvcfg['comment_id']
					);
	}
	public static function ListAllForUser($user_id) {
		$res=db_query("	SELECT id, comment_id, creation_timestamp
						FROM pageviews
						WHERE id='%d'
						ORDER BY creation_timestamp DESC",
					$user_id
				);
		return db_get_list_of_assoc($res);
	}
	public static function ListAllForPage($comment_id) {
		$res=db_query("	SELECT id, comment_id, creation_timestamp
						FROM pageviews
						WHERE comment_id='%d'
						ORDER BY creation_timestamp DESC",
					$comment_id
				);
		return db_get_list_of_assoc($res);
	}
}
