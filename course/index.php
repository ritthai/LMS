<?php
profiling_start('all');

@include("$ROOT/includes/tags.inc");
@include("dataacquisition/google.util.php");
@include("dataacquisition/youtube.util.php");
@include("dataacquisition/itunesu.util.php");
@include("dataacquisition/khanacad.util.php");
@include("$ROOT/includes/subjects.inc");
@include("$ROOT/includes/universities.inc");
@include("$ROOT/includes/geography.inc");

@session_start();
@db_connect();

controller_prefix();

Error::showSeparator();
Error::setBgColour('#B66');
Error::generate('debug', "Loading $_SERVER[REQUEST_URI]");
if($tmp1 = User::GetAuthenticatedID()) Error::generate('debug', "Logged in as ".User::GetAttrib($tmp1, 'name'));
Error::setBgColour('#555');
Error::showSeparator();

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

profiling_start('action');
if($action == 'countries') {
	$args['countries']		= Country::ListAll();
	$args['pagetitle']		= 'Choose a Country';
	foreach($args['countries'] as $k=>$v) {
		$args['countries'][$k] = array(	'name'	=> $v[1],
										'id'	=> $v[0]
										);
	}
	include("$ROOT/course/views/countries.view.php");
} else if($action == 'areas') {
	$args['pagetitle']		= 'Choose a Region';
	if(!is_numeric($params['country'])) {
		$args['country']	= array('id'	=> Country::GetID($params['country']),
									'name'	=> $params['country']);
	} else {
		$args['country']	= array('id'	=> $params['country'],
									'name'	=> Country::GetName($params['country']));
	}
	$args['areas']		= Area::ListAll($args['country']['id']);
	foreach($args['areas'] as $k=>$v) {
		$args['areas'][$k] = array(	'name'	=> $v[1],
									'id'	=> $v[0]
										);
	}
	include("$ROOT/course/views/areas.view.php");
} else if($action == 'universities') {
	$args['pagetitle']		= 'Choose a University';
	$args['area']			= array(	'id'	=> $params['area'],
										'name'	=> Area::GetName($params['area']) );
	$country				= Area::GetCountryID($params['area']);
	$args['country']		= array(	'name'	=> Country::GetName($country),
										'id'	=> $country );
	$args['universities']	= University::ListAll($args['area']['id']);
	foreach($args['universities'] as $k=>$v) {
		$args['universities'][$k] = array(	'name'	=> $v[1],
											'id'	=> $v[0]
											);
	}
	include("$ROOT/course/views/universities.view.php");
} else if($action == 'autocomplete') {
	switch($params['list']) {
	case 'countries':
		$arr = Country::ListAllMatching($params['val']);
		foreach($arr as $k=>$v) {
			$arr[$k] = array($v[0], $v[1]);
		}
		break;
	case 'areas':
		$arr = Area::ListAllMatching($params['country'], $params['val']);
		foreach($arr as $k=>$v) {
			$arr[$k] = array($v[0], $v[1], "$v[2]");
		}
		break;
	case 'universities':
		$arr = University::ListAllMatching($params['area'], $params['country'], $params['val']);
		foreach($arr as $k=>$v) {
			$arr[$k] = array($v[0], $v[1], "$v[2], $v[3]");
		}
		break;
	case 'courses':
		$arr = array();
		$lst = CourseDefn::ListAllStartingWithTitle($params['val']);
		foreach($lst as $elem) {
			// id, code, title, descr, university
			$arr[] = array($elem[0], $elem[2], $elem[1]);
		}
		$lst = CourseDefn::ListAllStartingWithCode($params['val']);
		foreach($lst as $elem) {
			// id, code, title, descr, university
			$arr[] = array($elem[0], $elem[1], $elem[2]);
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
	foreach($args['courses'] as $k=>$v) {
		// id,code,title,descr,university
		$args['courses'][$k] = array(	'id'			=> $v[0],
										'code'			=> $v[1],
										'title'			=> $v[2],
										'descr'			=> $v[3],
										'university'	=> $v[4] );
	}
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
} else if($action == 'post') { // post a comment
	if(!$params['owner'] || !User::IsAuthenticated()) {
        check_perms(false);
	} else if(!($cid = Comment::Create(
			array(	'subject'	=> $params['subject'],
					'body'		=> $params['body'],
					'owner'		=> User::GetAuthenticatedID(),
					'id'		=> $params['cid'] ) ) ) ) {
        Error::generate('warn', 'Could not create comment.', Error::$FLAGS['single']);
	}
} else if($action == 'show' || $action == 'search') {
	if($params['id']) $p = (int)$params['id'];
	else if($params['university']) $p = $params['terms'];
	$crs = new CourseDefn( $p );
	$succeeded = false;
	if(!$crs->load() && $action == 'search') {
		$crs->code = false;
		$crs->title = $params['terms'];
	} else {
		$succeeded = true;
	}
	if(!$succeeded && !$crs->load()) {
		Error::generate(Error::$PRIORITY['warn'], 'Course not found.');
		if(isset($_SESSION) && $_SESSION['last_rendered_page']) {
            redirect_raw($_SESSION['last_rendered_page']);
        } else {
            redirect();
        }
	} else { 
		profiling_start('process description and/or get topics from db');
		if(!($procd_descr = Comment::ListAll($crs->cid, 2))) {
			profiling_start('process description');
			$procd_descr = process_description($crs->descr);
			foreach($procd_descr as $key => $topic) {
				$id = Comment::Create(array('subject'	=> $topic,
											'type'		=> 2,
											'id'		=> $crs->cid));
				$procd_descr[$key] = array($id, $topic);
			}
			profiling_end('process description');
		} else {
			foreach($procd_descr as $key => $topic) {
				// cid, subject, timestamp
				$procd_descr[$key] = array($topic[0], $topic[1]);
			}
		}
		profiling_end('process description and/or get topics from db');

		profiling_start('deal with tags and procd_descr');
		$tags = split('[,]', get_tags($crs));

		foreach($procd_descr as $arr) {
			$descr = $arr[1];
			if($descr == ' ') continue;
			$descr = ereg_replace('[^A-Za-z0-9&; -]', '', $descr);
			array_push(	$search_results,
					array(	'subject'	=> ucfirst($descr),
							'google'	=> google_search($descr),
							'youtube'	=> youtube_search($descr, $tags, $crs),
							'itunesu'	=> itunesu_search($descr),
							'khanacad'	=> khanacad_search($descr),
							'comment_id'=> $arr[0],
							'comments'	=> array_map(	function($a) { return $a[0]; },
														Comment::ListAll($arr[0]) )
						));
		}
		profiling_end('deal with tags and procd_descr');

		profiling_start('wrap up processing and package data for the view');
		$args['pagetitle']		= "$crs->title ($crs->code)";
		$args['pageurl']		= $_SERVER['REQUEST_URI'];
		$args['course']			= array('id'	=> $crs->id,
										'title'	=> $crs->title,
										'code'	=> $crs->code,
										'descr' => $crs->descr);
		$args['searchresults']	= $search_results;
		$args['comment_id']		= $crs->cid;
		$args['comments']		= array_map(	function($a) { return $a[0]; },
										Comment::ListAll($crs->cid) );
		$args['actions']		= $ACTIONS;

		$_SESSION['lastargs'] = $args;
		preg_match('/^[a-zA-Z]+/', $crs->code, $matches);
		$args['code']		= $matches[0];
		$args['university']	= array('id'	=> $crs->university,
									'name'	=> University::GetName($crs->university));
		$args['area']		= array('id'	=> ($areaid = University::GetAreaID($args['university']['id'])),
									'name'	=> Area::GetName($areaid));
		$args['country']	= array('id'	=> ($countryid = Area::GetCountryID($args['area'])),
									'name'	=> Country::GetName($countryid));
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
profiling_end('view');
db_close();

profiling_end('all');

profiling_print_summary();

Error::showSeparator();
Error::setBgColour('#B66');
Error::generate('debug', "Finished rendering $_SERVER[REQUEST_URI] normally");
Error::setBgColour('#555');
Error::showSeparator();
