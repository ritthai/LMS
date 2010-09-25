<?php
@session_start();
@db_connect();
$memcached = new Memcached();
$memcached->addServer('localhost', 11211);

controller_prefix();
if(isset($_SESSION)) Error::setPrepend($_SESSION);
Error::generate('debug', 'start controller');

@include("$ROOT/includes/tags.inc");
@include("dataacquisition/search.util.php");
@include("$ROOT/includes/subjects.inc");
@include("$ROOT/includes/universities.inc");
@include("$ROOT/includes/geography.inc");

$CONTROLLER = 'course';
$PAGE_REL_URL = "$HTMLROOT";

$ACTIONS = array(	'search'				=> new HttpAction("$PAGE_REL_URL/search", 'get',
												array('id')),
					'search2'				=> new HttpAction("$PAGE_REL_URL/search", 'get',
												array('terms')),
					'list'					=> new HttpAction("$PAGE_REL_URL/list", 'get',
												array()),
					'show'					=> new HttpAction("$PAGE_REL_URL/show", 'get',
												array('id')),
					'list2'					=> new HttpAction("$PAGE_REL_URL/show", 'get',
												array()),
					'post'					=> new HttpAction("$PAGE_REL_URL/post", 'post',
												array('subject', 'body', 'cid')),
					// contact page
					'contact'				=> new HttpAction("$PAGE_REL_URL/contact", 'get',
												array()),
					// ToU page
					'terms'					=> new HttpAction("$PAGE_REL_URL/terms", 'get',
												array()),
					// privacy page
					'privacy'				=> new HttpAction("$PAGE_REL_URL/privacy", 'get',
												array()),
					// about page
					'about'					=> new HttpAction("$PAGE_REL_URL/about", 'get',
												array()),
					// countries page
					'countries'				=> new HttpAction("$PAGE_REL_URL/countries", 'get',
												array()),
					// provinces page
					'areas'					=> new HttpAction("$PAGE_REL_URL/areas", 'get',
												array('country')),
					// universities page
					'universities'			=> new HttpAction("$PAGE_REL_URL/universities", 'get',
												array('area')),
					// subject.list
					'subjects'				=> new HttpAction("$PAGE_REL_URL/subjects", 'get',
												array('university')),
					// subject.show
					'subject'				=> new HttpAction("$PAGE_REL_URL/subject", 'get',
												array('university', 'code')),
					'index'					=> new HttpAction("$PAGE_REL_URL/", 'get',
												array()),
					'autocomplete'			=> new HttpAction("$PAGE_REL_URL/autocomplete", 'get',
												array('list', 'val')),
					'favs'					=> new HttpAction("$PAGE_REL_URL/favs", 'post',
												array('cid', 'owner', 'type')),
					'favsrm'				=> new HttpAction("$PAGE_REL_URL/favsrm", 'post',
												array('cid', 'owner', 'type')),
					'voteup'				=> new HttpAction("$PAGE_REL_URL/voteup", 'post',
												array('id', 'cid', 'owner', 'type')),
					'votedown'				=> new HttpAction("$PAGE_REL_URL/votedown", 'post',
												array('id', 'cid', 'owner', 'type')),
					'check_lock'			=> new HttpAction("$PAGE_REL_URL/check_lock", 'post',
												array('cid')),
					'invalidate'			=> new HttpAction("$PAGE_REL_URL/invalidate", 'get',
												array('id'), 'admin'),
				);

$search_results = array();

$action = false;
$params = array();
foreach($ACTIONS as $key => $val) {
	if($val->wasCalled()) {
		if(!$action) $action = $key;
		$params = array_merge($params, $ACTIONS[$action]->getParams());
		break;
	}
}
if($action == 'list2') $action = 'list';
if($action == 'search2') $action = 'search';

$args['actions'] = $ACTIONS;
$args['action'] = $action;
$args['favs'] = User::GetAttribs('fav');

function reset_loading_screen_counter() {
	if(isset($_SESSION)) {
		foreach($_SESSION as $k=>$v) {
			if(preg_match('/loading_screen_count.*/', $k)) {
				$_SESSION[$k] = 0;
			}
		}
	}
}
reset_loading_screen_counter();

profiling_start('action');
if($action == 'invalidate') {
	$p = (int)$params['id'];
	$crs = new CourseDefn( $p );
	$success = $crs->load();

	$memcached->delete($crs->cid);
	db_query("DELETE FROM comments WHERE id='%d' AND type='2'", $crs->cid);
	db_query("DELETE FROM comments WHERE parent='%d' AND type='2'", $crs->cid);
	db_query("UPDATE comments_lock SET locked='0' WHERE id='%d'", $crs->cid);
} else if($action == 'countries') {
	$args['countries'] = Country::ListAll();
	$args['pagetitle'] = 'Choose a Country';
	$args['countries'] = array_map(	function($country) { $country['id'] = intval($country['id']); return $country; },
									$args['countries']);
	include("$ROOT/course/views/countries.view.php");
} else if($action == 'areas') {
	$args['pagetitle'] = 'Choose a Region';
	if(!is_numeric($params['country'])) {
		$args['country'] = array('id'	=> Country::GetID($params['country']),
								 'name'	=> $params['country']);
	} else {
		$args['country'] = array('id'	=> $params['country'],
								 'name'	=> Country::GetName($params['country']));
	}
	$args['areas'] = Area::ListAll($args['country']['id']);
	$args['areas'] = array_map(	function($area) { $area['id'] = intval($area['id']); return $area; },
								$args['areas']);
	include("$ROOT/course/views/areas.view.php");
} else if($action == 'universities') {
	$args['pagetitle']		= 'Choose a University';
	$args['area']			= array(	'id'	=> $params['area'],
										'name'	=> Area::GetName($params['area']) );
	$country				= Area::GetCountryID($params['area']);
	$args['country']		= array(	'name'	=> Country::GetName($country),
										'id'	=> $country );
	$args['universities']	= University::ListAll($args['area']['id']);
	$args['universities']	= array_map(function($uni) { $uni['id'] = intval($uni['id']); return $uni; },
										$args['universities']);
	include("$ROOT/course/views/universities.view.php");
} else if($action == 'autocomplete') {
	switch($params['list']) {
	case 'countries':
		$arr = Country::ListAllMatching($params['val']);
		foreach($arr as $k=>$v) {
			$arr[$k] = array($v['id'], $v['name']);
		}
		break;
	case 'areas':
		$arr = Area::ListAllMatching(	isset($params['country'])?$params['country']:false,
										$params['val']);
		foreach($arr as $k=>$v) {
			$arr[$k] = array($v['id'], $v['name'], "$v[country_name]");
		}
		break;
	case 'universities':
		$arr = University::ListAllMatching(	isset($params['area'])?$params['area']:false,
											isset($params['country'])?$params['country']:false,
											$params['val']);
		foreach($arr as $k=>$v) {
			$arr[$k] = array($v['id'], $v['name'], "$v[area_name], $v[country_name]");
		}
		break;
	case 'courses':
		// TODO: This is not safe for multiple universities.
		$arr = array();
		//$lst = CourseDefn::ListAllStartingWithTitle($params['val']);
		$lst = CourseDefn::ListAllContainingTitle($params['val']);
		foreach($lst as $elem) {
			// id, code, title, descr, university
			$arr[] = array($elem['id'], $elem['title'], $elem['code']);
		}
		$lst = CourseDefn::ListAllStartingWithCode($params['val']);
		foreach($lst as $elem) {
			// id, code, title, descr, university
			$arr[] = array($elem['id'], $elem['code'], $elem['title']);
		}
		break;
	default:
		die('');
	}
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
	header("Content-Type: application/json");

	echo '{ results: [';
	foreach($arr as $key => $val) {
		if($key != 0) echo ',';
		if(is_array($val)) {
			$v0 = $val[0];
			$v1 = $val[1];
			$v2 = $val[2];
		} else {
			$v0 = $key;
			$v1 = $val;
			$v2 = '';
		}
		echo "{ id: \"$v0\", value: \"$v1\", info: \"$v2\" }";
	}
	echo '] }';
} else if($action == 'subjects') {
	$args['pagetitle']		= 'Choose a Subject';
	$university	= $params['university'];
	if(!is_numeric($university)) {
		$university = University::GetID($university);
	}
	$args['university']		= array(	'id'	=> $university,
										'name'	=> University::GetName($university) );
	$area					= University::GetAreaID($university);
	$args['area']			= array(	'id'	=> $area,
										'name'	=> Area::GetName($area) );
	$country				= Area::GetCountryID($area);
	$args['country']		= array(	'name'	=> Country::GetName($country),
										'id'	=> $country );
	//$subjects = CourseDefn::ListAllStartingWithCode($university, 'title');
	$subjects = $subjects[$args['country']['name']][$args['area']['name']][$args['university']['name']];
	if($subjects)
	foreach($subjects as $k=>$v) {
		$subjects[$k] = array(	'code'	=> $k,
								'title'	=> $v );
	}
	/*foreach($subjects as $k=>$v) {
		// id,code,title,descr,university
		$subjects[$k] = array(	'id'			=> $v[0],
								'code'			=> $v[1],
								'title'			=> $v[2],
								'descr'			=> $v[3],
								'university'	=> $v[4] );
	}*/
	$args['subjects'] = $subjects;
	include("views/subjects.view.php");
} else if($action == 'subject') {
	$code					= $params['code'];
	$university				= $params['university'];
	$args['university']		= array(	'id'	=> $university,
										'name'	=> University::GetName($university) );
	$area					= University::GetAreaID($university);
	$args['area']			= array(	'id'	=> $area,
										'name'	=> Area::GetName($area) );
	$country				= Area::GetCountryID($area);
	$args['country']		= array(	'name'	=> Country::GetName($country),
										'id'	=> $country );
	$args['subject'] = array(	'code'	=> $code,
								'title'	=> $subjects[$args['country']['name']][$args['area']['name']][$args['university']['name']][$code],
								'descr'	=> 'No subject area descriptions. I\'m not sure where I\'d get them.'
							);
	$args['pagetitle']		= $args['subject']['title'].' Courses';
	$args['courses'] = CourseDefn::ListAllWithCode($code);
	/*foreach($args['courses'] as $k=>$v) {
		// id,code,title,descr,university
		$args['courses'][$k] = array(	'id'			=> $v[0],
										'code'			=> $v[1],
										'title'			=> $v[2],
										'descr'			=> $v[3],
										'university'	=> $v[4] );
	}*/
	include("views/subject.view.php");
} else if($action == 'favsrm') { // remove a favourite
	switch($params['type']) {
		default:
			Error::generate('suspicious', 'Bad fav type in rm');
			$params['owner'] = false;
			break;
		case 'topic':
		case 'course':
	}
	if(!$params['owner'] || !User::IsAuthenticated()) {
        check_perms(false);
	} else if(!User::DeleteAttrib(	User::GetAuthenticatedID(),
									$params['type'].'fav', $params['cid'])) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/html");
		echo "Could not remove from favs.";
		Error::generate('warn', 'Could not remove from favourites.');
	} else {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/html");
		echo "Removed from favs.";
	}
} else if($action == 'favs') { // add a favourite
	switch($params['type']) {
		default:
			Error::generate('suspicious', 'Bad fav type');
			$params['owner'] = false;
			break;
		case 'topic':
		case 'course':
	}
	if(!$params['owner'] || !User::IsAuthenticated()) {
        check_perms(false);
	} else if(!User::SetAttrib(User::GetAuthenticatedID(), $params['type'].'fav', $params['cid'])) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/html");
		header("Connection:");
		header("Content-length:");
		echo "Could not add to favs.";
	} else {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/html");
		header("Connection:");
		header("Content-length:");
		echo "Added to favs.";
	}
} else if($action == 'voteup' || $action == 'votedown') {
	switch($params['type']) {
		default:
			Error::generate('suspicious', 'Bad vote type in voteup');
			$params['owner'] = false;
			break;
		case 'result':
	}
	$val = $action == 'voteup' ? 1 : -1;
	if(!$params['owner'] || !User::IsAuthenticated()) {
        check_perms(false);
	} else if(!Comment::Vote(User::GetAuthenticatedID(), $params['cid'], $val)) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/html");
		header("Connection:");
		header("Content-length:");
		echo "Could not vote.";
	} else {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/html");
		header("Connection:");
		header("Content-length:");
		echo "Vote successful.";
		Error::generate('debug', 'memcached delete '.$params['cid']);

		$p = (int)$params['id'];
		$crs = new CourseDefn( $p );
		$success = $crs->load();
		$memcached->delete($crs->cid);
	}
} else if($action == 'post') { // post a comment
	if(!$params['owner'] || !User::IsAuthenticated()) {
        //check_perms(false);
        Error::generate('warn', 'Must be logged in to post a comment.', Error::$FLAGS['single']);
        die('Must be logged in to post a comment.');
	} else if(!($cid = Comment::Create(
			array(	'subject'	=> $params['subject'],
					'body'		=> $params['body'],
					'owner'		=> User::GetAuthenticatedID(),
					'id'		=> $params['cid'] ) ) ) ) {
        Error::generate('warn', 'Could not create comment.', Error::$FLAGS['single']);
	}
} else if($action == 'check_lock') { // check the status of a comment lock
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-Type: text/html");
	$status = db_check_transaction('comments', $params['cid']) == 0;
	die($status ? '0' : '1');
} else if($action == 'show' || $action == 'search') {
	$success = false;
	if(isset($params['id']) && $params['id']) {
		$p = (int)$params['id'];
		$icrs = new CourseDefn( $p );
		$success = $icrs->load();
	} else if(isset($params['terms'])) {
		$p = $params['terms'];
		$uni = 1;
		if(isset($params['university']) && $params['university']) $uni = $params['university'];
		else if(isset($_SESSION['university']) && $_SESSION['university']) $uni = $_SESSION['university']['id'];
		$res = CourseDefn::ListAllStartingWithCode($p, 'code', $uni);
		if($res && count($res) > 0) {
			$icrs = new CourseDefn( intval($res[0]['id']) );
			$success = $icrs->load();
		}
	}
	if(!$success) {
		Error::generate(Error::$PRIORITY['warn'], 'Course not found.');
		if(isset($_SESSION) && $_SESSION['last_rendered_page']) {
            redirect_raw($_SESSION['last_rendered_page']);
        } else {
            redirect();
        }
	} else { 
		profiling_start('process description and/or get topics from db');
		$exists = ($procd_descr = Comment::ListAll($icrs->cid, 2));
		$locked = db_check_transaction("comments", $icrs->cid);
		if(!$exists || $locked) {
			// display loading page
			$page = "$ROOT/includes/template/loading.php";
			$args['pagetitle']		= "$icrs->title ($icrs->code)";
			$args['pageurl']		= $_SERVER['REQUEST_URI'];
			$args['course']			= array('id'	=> $icrs->id,
											'title'	=> $icrs->title,
											'code'	=> $icrs->code,
											'descr' => $icrs->descr);
			$args['searchresults']	= $search_results;
			$args['private']		= true; // do not track as a pageview
			$args['comment_id']		= $icrs->cid;
			$args['comments']		= array_map(function($a) { return $a['id']; },
												Comment::ListAll($icrs->cid) );
			$args['actions']		= $ACTIONS;

			$_SESSION['lastargs'] = $args;
			preg_match('/^[a-zA-Z]+/', $icrs->code, $matches);
			$args['code']		= $matches[0];
			$args['university']	= array('id'	=> $icrs->university,
										'name'	=> University::GetName($icrs->university));
			$args['area']		= array('id'	=> ($areaid = University::GetAreaID($args['university']['id'])),
										'name'	=> Area::GetName($areaid));
			$args['country']	= array('id'	=> ($countryid = Area::GetCountryID($args['area'])),
										'name'	=> Country::GetName($countryid));
			if(!isset($_SESSION['loading_screen_count'.$icrs->cid])) {
				$_SESSION['loading_screen_count'.$icrs->cid] = 1;
			} else if($_SESSION['loading_screen_count'.$icrs->cid]++ > 3) {
				// we're stuck in a loading screen loop. uh oh.
				$page = "$ROOT/includes/template/error.php";
				Error::generate('prod_debug', 'Stuck in a loading loop!');
				Error::generate('prod_debug', $procd_descr);
				Error::generate('prod_debug', $args);
			} else {
				$_SESSION['loading_screen_count'.$icrs->cid]++;
			}
			ob_start();
			include($page);
			$contents = ob_get_contents();
			ob_end_clean();
			header("Connection: close");
			header("Content-Length: ".strlen($contents));
			echo $contents;
			flush();
			session_write_close();
			// this will spinlock if the course is locked
			$job_taken = db_start_transaction("comments", $icrs->cid);
			$lock_acquired = true;
		} else {
			$lock_acquired = false;
		}
		if(!$exists && $job_taken) { // course not cached
			async_cache_connect(1);
			async_cache_connect(2);
			async_cache_connect(3);
			//Comment::enableInitMode();
			$procd_descr = process_description($icrs->descr);
			if(count($procd_descr) == 0) $procd_descr[] = 'N/A';
			$ids = $desicrs = $tagss = array();
			foreach($procd_descr as $key => $topic) {
				// Cache topic
				$id = Comment::Create(array('subject'	=> $topic,
											'type'		=> 2, // topic
											'id'		=> $icrs->cid));
				if($topic == 'N/A') { // placeholder to indicate that there are no topics
					continue;
				}
				$procd_descr[$key] = array($id, $topic);

				// Cache topic result
				$tags = split('[,]', get_tags($icrs));
				$descr = $topic;
				if($descr === '') continue;
				if($descr === ' ') continue;
				$descr = ereg_replace('[^A-Za-z0-9&; -]', '', $descr);

				$ids[$key] = $id;
				$desicrs[$key] = $descr;
				$tagss[$key] = $tags;

				prefetch_search('youtube', $descr, $tags, $icrs);
				prefetch_search('google', $descr, $tags, $icrs);
				prefetch_search('khanacad', $descr, $tags, $icrs);
				prefetch_search('wikipedia', $descr, $tags, $icrs);
				prefetch_search('itunesu', $descr, $tags, $icrs);
			}
			foreach($procd_descr as $key => $topic) {
				$id = $ids[$key];
				$descr = $desicrs[$key];
				$tags = $tagss[$key];
				$results = array_merge(
						perform_search('khanacad', $descr, $tags, $icrs),
						perform_search('youtube', $descr, $tags, $icrs),
						perform_search('google', $descr, $tags, $icrs),
						perform_search('wikipedia', $descr, $tags, $icrs),
						perform_search('itunesu', $descr, $tags, $icrs));
				Error::generate('debug', $results);
				foreach($results as $res) {
					$res['subject'] = $res['title'];
					$res['type'] = 2;
					$res['id'] = $id;
					$res['rating'] = isset($res['rating']) ? $res['rating'] : 0 ;
					Comment::Create($res);
				}
				//Comment::disableInitMode();
			}
			async_cache_disconnect(1);
			async_cache_disconnect(2);
			async_cache_disconnect(3);
			$memcached->delete(''.$icrs->cid);
		} else {
			foreach($procd_descr as $key => $topic) {
				// cid, subject, timestamp
				$procd_descr[$key] = array($topic['id'], $topic['subject']);
			}
		}
		if($lock_acquired) {
			db_end_transaction('comments', $icrs->cid);
			goto end;
		}
		profiling_end('process description and/or get topics from db');

		profiling_start('deal with tags and procd_descr');
		$_SESSION['loading_screen_count'.$icrs->cid] = 0;
		Error::generate('debug', 'starting deal with tags and procd_descr');
		$search_results = $memcached->get(''.$icrs->cid);
		if(!$search_results) {
			$search_results = array();
			$topics = Comment::ListAll($icrs->cid, 2); // topic
			Error::generate('debug', $topics);
			foreach($topics as $topic) {
				$topicid = $topic['id']; $topicsubject = $topic['subject'];
				if($topicsubject == 'N/A') { // placeholder to indicate that there are no topics
					continue;
				}
				Error::generate('debug', "starting deal with tags and procd_descr, topic $topicid/$topicsubject");
				$results = Comment::ListAll($topicid, 2);
				Error::generate('debug',$results);
				foreach($results as $k=>$v) {
					$attr = Comment::GetAttribs($v['id']);
					$results[$k] = $v;
					foreach($attr as $vi) {
						$results[$k][strtolower($vi[0])] = $vi[1];
					}
				}
				Error::generate('debug',$results);
				array_push(	$search_results, array(
							'subject'	=> ucfirst($topicsubject),
							'youtube'	=> array_filter( $results, function ($elem) { return filter_result($elem, array('source'=>'youtube', 'rating'=>0)); } ),
							'google'	=> array_filter( $results, function ($elem) { return filter_result($elem, array('source'=>'google', 'rating'=>0)); } ),
							'wikipedia'	=> array_filter( $results, function ($elem) { return filter_result($elem, array('source'=>'wikipedia', 'rating'=>0)); } ),
							'khanacad'	=> array_filter( $results, function ($elem) { return filter_result($elem, array('source'=>'khanacad', 'rating'=>1.1)); } ),
							'itunesu'	=> array_filter( $results, function ($elem) { return filter_result($elem, array('source'=>'itunesu', 'rating'=>0)); } ),
							'comment_id'=> $topicid,
							'comments'	=> array_map( function($a) { return $a['id']; }, Comment::ListAll($topicid) )
							));
				Error::generate('debug', "finishing deal with tags and procd_descr, topic $topicid/$topicsubject, result:");
				Error::generate('debug', $search_results);
			}
			$memcached->set(''.$icrs->cid, $search_results);
		}
		profiling_end('deal with tags and procd_descr');

		profiling_start('wrap up processing and package data for the view');
		$args['pagetitle']		= "$icrs->title ($icrs->code)";
		$args['pageurl']		= $_SERVER['REQUEST_URI'];
		$args['course']			= array('id'	=> $icrs->id,
										'title'	=> $icrs->title,
										'code'	=> $icrs->code,
										'descr' => $icrs->descr);
		$args['searchresults']	= $search_results;
		$args['comment_id']		= $icrs->cid;
		$args['comments']		= array_map(	function($a) { return $a['id']; },
												Comment::ListAll($icrs->cid) );
		$args['actions']		= $ACTIONS;

		$_SESSION['lastargs'] = $args;
		preg_match('/^[a-zA-Z]+/', $icrs->code, $matches);
		$args['code']		= $matches[0];
		$args['university']	= array('id'	=> $icrs->university,
									'name'	=> University::GetName($icrs->university));
		$args['area']		= array('id'	=> ($areaid = University::GetAreaID($args['university']['id'])),
									'name'	=> Area::GetName($areaid));
		$args['country']	= array('id'	=> ($countryid = Area::GetCountryID($args['area'])),
									'name'	=> Country::GetName($countryid));
		reset_loading_screen_counter();
		profiling_end('wrap up processing and package data for the view');
		include("views/search.view.php");
	}
} else if($action == 'list') {
	$args = array(	'pagetitle'		=> 'List',
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'courses'		=> CourseDefn::ListAll(),
					'actions'		=> $ACTIONS);
	$_SESSION['lastargs'] = $args;
	include("views/list.view.php");
} else if(isset($_GET['action']) && $_GET['action'] != '') { // Action with no params
	$action = $_GET['action'];
	$args = array(	'pagetitle'		=> ucfirst($action),
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'actions'		=> $ACTIONS);
	$_SESSION['lastargs'] = $args;
	switch($action) {
		case 'search':
		case 'list':
		case 'contact':
		case 'privacy':
		case 'terms':
		case 'about':
			include("views/$action.view.php");
			break;
		default:
			Error::generate('suspicious', "Invalid action $action in /course/ : $_SERVER[REQUEST_URI]");
			redirect_raw($PAGE_REL_URL);
			break;
	}
} else {
	$args = array(	'pagetitle'		=> 'Welcome',
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'courses'		=> CourseDefn::ListAll(),
					'actions'		=> $ACTIONS);
	include("views/index.view.php");
}
end:
if(isset($args['country']) && $c = $args['country']) {
	$_SESSION['country'] = $c;
}
if(isset($args['area']) && $a = $args['area']) {
	$_SESSION['area'] = $a;
}
if(isset($args['university']) && $u = $args['university']) {
	$_SESSION['university'] = $u;
}
if(isset($args['code']) && $o = $args['code']) {
	$_SESSION['code'] = $o;
}
profiling_end('view');
db_close();

profiling_end('all');

profiling_print_summary();

Error::showSeparator();
Error::setBgColour('#B66');
Error::generate('debug', "Finished rendering $_SERVER[REQUEST_URI] normally");
Error::setBgColour('#555');
Error::showSeparator();
