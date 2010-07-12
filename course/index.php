<?php
@include("$ROOT/includes/mysql.inc");
@include("$ROOT/includes/tags.inc");
@include("$ROOT/includes/prefix.inc");
@include("dataacquisition/google.util.php");
@include("dataacquisition/youtube.util.php");
@include("dataacquisition/itunesu.util.php");
@include("dataacquisition/khanacad.util.php");

session_start();
db_connect();

controller_prefix();

$CONTROLLER = 'course';
$PAGE_REL_URL = "$HTMLROOT/course";

$ACTIONS = array(	'search'	=> new HttpAction("$PAGE_REL_URL/search", 'get',
									array('terms')),
					'list'		=> new HttpAction("$PAGE_REL_URL/list", 'get',
									array()),
					'show'		=> new HttpAction("$PAGE_REL_URL/show", 'get',
									array('id')),
					'list2'		=> new HttpAction("$PAGE_REL_URL/show", 'get',
									array()),
					'post'		=> new HttpAction("$PAGE_REL_URL/post", 'post',
									array('subject', 'body', 'course')),
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
if($action == 'list2') $action = $list;

if($action == 'post') { // post a comment
	$crs = new CourseDefn($params['course']);
	$crs->load();
	$params['owner'] = User::GetAuthenticatedID();
    if(!$crs) {
        Error::generate('warn', 'Course not found.', Error::$FLAGS['single']);
		if($_SESSION['last_rendered_page']) {
			redirect_raw($_SESSION['last_rendered_page']);
		} else {
			redirect($CONTROLLER);
		}
	} else if(!$params['owner']) {
        check_perms(false);
	} else if(!Comment::Create(
			array(	'subject'	=> $params['subject'],
					'body'		=> $params['body'],
					'owner'		=> $params['owner'],
					'id'		=> $crs->cid ) ) ) {
        Error::generate('warn', 'Could not create comment.', Error::$FLAGS['single']);
		if($_SESSION['last_rendered_page']) {
			redirect_raw($_SESSION['last_rendered_page']);
		} else {
			redirect($CONTROLLER);
		}
    } else {
        Error::generate('success', 'Comment created.', Error::$FLAGS['single']);
		if($_SESSION['last_rendered_page']) {
			redirect_raw($_SESSION['last_rendered_page']);
		} else {
			redirect($CONTROLLER);
		}
    }
} else if($action == 'show' || $action == 'search') {
	$crs = new CourseDefn( $action == 'show' ? (int)$params['id'] : $params['terms']);
	if(!$crs->load()) {
		Error::generate(Error::$PRIORITY['warn'], 'Course not found.');
		redirect('search');
	} else {
		$procd_descr = process_description($crs->descr);
		$tags = split('[,]', get_tags($crs));

		foreach($procd_descr as $descr) {
			if($descr == ' ') continue;
			$descr = ereg_replace('[^A-Za-z0-9&; -]', '', $descr);
			array_push(	$search_results,
					array(	'subject' => ucfirst($descr),
							'google' => google_search($descr),
							'youtube' => youtube_search($descr, $tags, $crs),
							'itunesu' => itunesu_search($descr),
							'khanacad' => khanacad_search($descr)));
		}

		$args = array(	'pagetitle'		=> 'Viewing course: '.$crs->code,
						'pageurl'		=> $_SERVER['REQUEST_URI'],
						'course'		=> array(	'id'	=> $crs->id,
													'title'	=> $crs->title,
													'code'	=> $crs->code,
													'descr' => $crs->descr),
						'searchresults'	=> $search_results,
						'comments'		=>
							array_map(	function($a) { return $a['id']; },
										Comment::ListAll($crs->cid)),
						'actions'		=> $ACTIONS);
		$_SESSION['lastargs'] = $args;
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
			include("views/$action.view.php");
			break;
		default:
			Error::generate('suspicious', "Invalid action $action in /course/");
			header("Location: $PAGE_REL_URL");
			break;
	}
} else {
	$args = array(	'pagetitle'		=> 'Course Index',
					'pageurl'		=> $_SERVER['REQUEST_URI'],
					'courses'		=> CourseDefn::ListAll(),
					'actions'		=> $ACTIONS);
	include("views/index.view.php");
}

db_close();
?>
